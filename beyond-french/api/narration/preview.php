<?php
declare(strict_types=1);

require_once dirname(__DIR__, 2) . '/includes/narration/NarrationApi.php';

try {
    $payload = narration_read_json_request();
    $adminId = narration_require_admin();
    narration_verify_request_csrf($payload);
    $request = narration_validate_payload($payload);
    narration_enforce_rate_limit(sqlite_db(), $adminId, 'preview');

    $fallbacks = array_values(array_filter(
        (array)narration_config()['fallback_providers'],
        static fn($provider): bool => (string)$provider !== $request['provider']
    ));
    $result = narration_service()->generate($request['provider'], $request, $fallbacks);
    $audio = (string)$result['audio_content'];
    if (!narration_valid_mp3($audio)) {
        throw new NarrationProviderException('Preview audio failed validation.', 'invalid_audio_file', true);
    }

    header('Content-Type: audio/mpeg');
    header('Content-Length: ' . strlen($audio));
    header('Cache-Control: no-store');
    header('X-Content-Type-Options: nosniff');
    header('X-Narration-Provider: ' . preg_replace('/[^a-z0-9_-]/i', '', (string)$result['provider']));
    header('X-Narration-Voice: ' . preg_replace('/[^a-z0-9._-]/i', '', (string)$result['voice']));
    echo $audio;
    exit;
} catch (Throwable $error) {
    narration_public_error($error);
}
