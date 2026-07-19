<?php
declare(strict_types=1);

if (!function_exists('studio_safe_json_encode')) {
    function studio_safe_json_encode(array $payload)
    {
        $json = json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        if (!is_string($json)) {
            throw new RuntimeException('Could not encode narration request.');
        }
        return $json;
    }
}

require_once __DIR__ . '/NarrationProvider.php';

final class OpenAIProvider implements NarrationProvider
{
    private const BUILT_IN_VOICES = [
        'alloy' => 'Alloy',
        'ash' => 'Ash',
        'ballad' => 'Ballad',
        'coral' => 'Coral',
        'echo' => 'Echo',
        'fable' => 'Fable',
        'onyx' => 'Onyx',
        'nova' => 'Nova',
        'sage' => 'Sage',
        'shimmer' => 'Shimmer',
        'verse' => 'Verse',
        'marin' => 'Marin',
        'cedar' => 'Cedar',
    ];

    /** @var array */
    private $config;

    public function __construct(array $config)
    {
        $this->config = $config;
    }

    public function name(): string
    {
        return 'openai';
    }

    public function voices(string $language = ''): array
    {
        $voices = [];
        foreach (self::BUILT_IN_VOICES as $id => $label) {
            $voices[] = ['id' => $id, 'label' => $label, 'language' => $language, 'provider' => $this->name()];
        }
        return $voices;
    }

    public function generate(array $request): array
    {
        $apiKey = trim((string)($this->config['api_key'] ?? ''));
        if ($apiKey === '') {
            throw new NarrationProviderException('OpenAI narration is not configured.', 'provider_not_configured');
        }
        if (!function_exists('curl_init')) {
            throw new NarrationProviderException('The cURL extension is required.', 'server_http_client_unavailable');
        }

        $voice = strtolower(trim((string)($request['voice'] ?? 'coral')));
        if (!empty($request['_is_fallback']) && !isset(self::BUILT_IN_VOICES[$voice])) {
            $voice = 'coral';
        }
        if (!isset(self::BUILT_IN_VOICES[$voice])) {
            throw new NarrationProviderException('The selected OpenAI voice is invalid.', 'invalid_voice');
        }

        $model = trim((string)($this->config['model'] ?? 'gpt-4o-mini-tts'));
        $format = (string)($request['format'] ?? 'mp3');
        $body = [
            'model' => $model,
            'input' => (string)$request['text'],
            'voice' => $voice,
            'response_format' => $format,
            'speed' => (float)($request['speed'] ?? 1.0),
        ];

        $instructions = trim((string)($request['instructions'] ?? ''));
        $language = trim((string)($request['language'] ?? ''));
        if ($language !== '') {
            $instructions = trim($instructions . ' Pronounce the text naturally for the ' . $language . ' locale.');
        }
        if ($instructions !== '' && !in_array($model, ['tts-1', 'tts-1-hd'], true)) {
            $body['instructions'] = $instructions;
        }

        $endpoint = (string)($this->config['endpoint'] ?? 'https://api.openai.com/v1/audio/speech');
        $ch = curl_init($endpoint);
        curl_setopt_array($ch, [
            CURLOPT_POST => true,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CONNECTTIMEOUT => 10,
            CURLOPT_TIMEOUT => (int)($this->config['timeout'] ?? 45),
            CURLOPT_HTTPHEADER => [
                'Authorization: Bearer ' . $apiKey,
                'Content-Type: application/json',
                'Accept: audio/mpeg',
            ],
            CURLOPT_POSTFIELDS => studio_safe_json_encode($body),
        ]);

        $audio = curl_exec($ch);
        $status = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlError = curl_error($ch);
        $curlNumber = curl_errno($ch);
        curl_close($ch);

        if ($curlNumber !== 0) {
            error_log('OpenAI narration network failure: ' . $curlNumber . ' ' . $curlError);
            throw new NarrationProviderException('OpenAI narration is temporarily unavailable.', 'provider_network_error', true);
        }
        if ($status < 200 || $status >= 300) {
            error_log('OpenAI narration HTTP failure: ' . $status);
            $temporary = $status === 408 || $status === 409 || $status === 425 || $status === 429 || $status >= 500;
            $code = $status === 429 ? 'provider_rate_limited' : ($temporary ? 'provider_temporary_failure' : 'provider_request_rejected');
            throw new NarrationProviderException('OpenAI narration request failed.', $code, $temporary, $status);
        }
        if (!is_string($audio) || strlen($audio) < 128) {
            throw new NarrationProviderException('OpenAI returned invalid audio.', 'invalid_provider_response', true);
        }

        return [
            'success' => true,
            'provider' => $this->name(),
            'voice' => $voice,
            'format' => $format,
            'audio_content' => $audio,
            'duration' => null,
        ];
    }
}
