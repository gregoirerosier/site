<?php
declare(strict_types=1);

require_once dirname(__DIR__, 2) . '/includes/narration/NarrationApi.php';

try {
    $payload = narration_read_json_request();
    $adminId = narration_require_admin();
    narration_verify_request_csrf($payload);
    $config = narration_config();
    $providerName = strtolower(trim((string)($payload['provider'] ?? '')));
    $language = trim((string)($payload['language'] ?? ''));

    if (!in_array($providerName, (array)$config['allowed_providers'], true)) {
        throw new NarrationApiException('The narration provider is not allowed.', 'invalid_provider', 422);
    }
    if ($language !== '' && !in_array($language, (array)$config['allowed_languages'], true)) {
        throw new NarrationApiException('The language is not allowed.', 'invalid_language', 422);
    }

    narration_enforce_rate_limit(sqlite_db(), $adminId, 'voices');
    $provider = narration_service()->provider($providerName);
    $providerConfig = (array)($config['providers'][$providerName] ?? []);
    $configured = trim((string)($providerConfig['api_key'] ?? '')) !== '';
    if ($providerName === 'azure') $configured = $configured && trim((string)($providerConfig['region'] ?? '')) !== '';

    narration_json([
        'success' => true,
        'provider' => $providerName,
        'configured' => $configured,
        'voices' => $provider->voices($language),
    ]);
} catch (Throwable $error) {
    narration_public_error($error);
}
