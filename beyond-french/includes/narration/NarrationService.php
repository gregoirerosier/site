<?php
declare(strict_types=1);

require_once __DIR__ . '/NarrationProvider.php';

final class NarrationService
{
    /** @var array<string,NarrationProvider> */
    /** @var array */
    private $providers = [];

    /** @param array<string,NarrationProvider> $providers */
    public function __construct(array $providers)
    {
        foreach ($providers as $key => $provider) {
            if (!$provider instanceof NarrationProvider) {
                throw new InvalidArgumentException('Every narration provider must implement NarrationProvider.');
            }
            $this->providers[strtolower((string)$key)] = $provider;
        }
    }

    public function provider(string $name): NarrationProvider
    {
        $key = strtolower(trim($name));
        if (!isset($this->providers[$key])) {
            throw new NarrationProviderException('Narration provider is not allowed.', 'provider_not_allowed');
        }
        return $this->providers[$key];
    }

    public function generate(string $requestedProvider, array $request, array $fallbackProviders = []): array
    {
        $queue = array_values(array_unique(array_filter(array_merge(
            [strtolower(trim($requestedProvider))],
            array_map(static function ($name) { return strtolower(trim((string)$name)); }, $fallbackProviders)
        ))));

        $lastTemporaryFailure = null;
        $primaryProvider = $queue[0] ?? '';
        foreach ($queue as $providerName) {
            $provider = $this->provider($providerName);
            try {
                $providerRequest = $request;
                $providerRequest['_is_fallback'] = $providerName !== $primaryProvider;
                $result = $provider->generate($providerRequest);
                if (empty($result['success']) || !isset($result['audio_content']) || !is_string($result['audio_content'])) {
                    throw new NarrationProviderException('Provider returned an invalid response.', 'invalid_provider_response', true);
                }
                return $result;
            } catch (NarrationProviderException $error) {
                if (!$error->allowsFallback()) {
                    throw $error;
                }
                $lastTemporaryFailure = $error;
            }
        }

        if ($lastTemporaryFailure instanceof NarrationProviderException) {
            throw $lastTemporaryFailure;
        }
        throw new NarrationProviderException('No narration provider is available.', 'provider_unavailable', true);
    }
}
