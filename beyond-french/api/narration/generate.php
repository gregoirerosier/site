<?php
declare(strict_types=1);

require_once dirname(__DIR__, 2) . '/includes/narration/NarrationApi.php';

$audioId = 0;
$savedFile = '';
try {
    $payload = narration_read_json_request();
    $adminId = narration_require_admin();
    narration_verify_request_csrf($payload);
    $request = narration_validate_payload($payload);
    if (!$request['save']) {
        throw new NarrationApiException('Use the preview endpoint for unsaved narration.', 'preview_endpoint_required', 422);
    }

    $pdo = sqlite_db();
    $contentHash = narration_content_hash($request);
    $cached = narration_cached_audio($pdo, $request['lesson_id'], $contentHash);
    if ($cached) {
        narration_json([
            'success' => true,
            'audio_id' => (int)$cached['id'],
            'lesson_id' => (int)$cached['lesson_id'],
            'provider' => (string)$cached['provider'],
            'voice' => (string)$cached['voice'],
            'audio_url' => (string)$cached['audio_path'],
            'cached' => true,
        ]);
    }

    narration_enforce_rate_limit($pdo, $adminId, 'generate');
    $audioId = narration_processing_record($pdo, $request, $contentHash, $adminId);

    $fallbacks = array_values(array_filter(
        (array)narration_config()['fallback_providers'],
        static fn($provider): bool => (string)$provider !== $request['provider']
    ));
    $result = narration_service()->generate($request['provider'], $request, $fallbacks);
    if ($request['format'] !== 'mp3' || !narration_valid_mp3((string)$result['audio_content'])) {
        throw new NarrationProviderException('Generated audio failed MP3 validation.', 'invalid_audio_file', true);
    }

    $stored = narration_store_mp3((string)$result['audio_content'], $request['lesson_id'], $audioId);
    $savedFile = (string)$stored['file'];
    $update = $pdo->prepare("UPDATE french_lesson_audio SET provider=?, voice=?, format='mp3', audio_path=?, generation_status='ready', error_code=NULL WHERE id=?");
    $update->execute([(string)$result['provider'], (string)$result['voice'], (string)$stored['url'], $audioId]);

    narration_json([
        'success' => true,
        'audio_id' => $audioId,
        'lesson_id' => $request['lesson_id'],
        'provider' => (string)$result['provider'],
        'voice' => (string)$result['voice'],
        'audio_url' => (string)$stored['url'],
        'cached' => false,
    ], 201);
} catch (Throwable $error) {
    if ($audioId > 0) {
        try {
            $code = $error instanceof NarrationProviderException ? $error->errorCode() : 'generation_failed';
            $failed = sqlite_db()->prepare("UPDATE french_lesson_audio SET generation_status='failed', error_code=? WHERE id=?");
            $failed->execute([$code, $audioId]);
        } catch (Throwable $ignored) {
        }
    }
    if ($savedFile !== '' && is_file($savedFile)) @unlink($savedFile);
    narration_public_error($error);
}
