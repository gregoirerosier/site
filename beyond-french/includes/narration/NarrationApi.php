<?php
declare(strict_types=1);

require_once dirname(__DIR__) . '/functions.php';
require_once __DIR__ . '/NarrationProvider.php';
require_once __DIR__ . '/NarrationService.php';
require_once __DIR__ . '/OpenAIProvider.php';
require_once __DIR__ . '/ElevenLabsProvider.php';
require_once __DIR__ . '/AzureSpeechProvider.php';

final class NarrationApiException extends RuntimeException
{
    public function __construct(
        string $message,
        private readonly string $errorCode,
        private readonly int $httpStatus
    ) {
        parent::__construct($message);
    }

    public function errorCode(): string { return $this->errorCode; }
    public function httpStatus(): int { return $this->httpStatus; }
}

function narration_config(): array
{
    static $config;
    if (!is_array($config)) $config = require dirname(__DIR__, 2) . '/config/narration.php';
    return $config;
}

function narration_service(): NarrationService
{
    static $service;
    if ($service instanceof NarrationService) return $service;
    $config = narration_config();
    $providers = (array)($config['providers'] ?? []);
    $service = new NarrationService([
        'openai' => new OpenAIProvider((array)($providers['openai'] ?? [])),
        'elevenlabs' => new ElevenLabsProvider((array)($providers['elevenlabs'] ?? [])),
        'azure' => new AzureSpeechProvider((array)($providers['azure'] ?? [])),
    ]);
    return $service;
}

function narration_json(array $payload, int $status = 200): never
{
    http_response_code($status);
    header('Content-Type: application/json; charset=utf-8');
    header('Cache-Control: no-store');
    header('X-Content-Type-Options: nosniff');
    echo json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    exit;
}

function narration_read_json_request(): array
{
    if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
        throw new NarrationApiException('POST is required.', 'method_not_allowed', 405);
    }
    $contentType = strtolower(trim((string)($_SERVER['CONTENT_TYPE'] ?? '')));
    if (!str_starts_with($contentType, 'application/json')) {
        throw new NarrationApiException('A JSON request is required.', 'json_required', 415);
    }
    $raw = file_get_contents('php://input');
    if (!is_string($raw) || $raw === '' || strlen($raw) > 16384) {
        throw new NarrationApiException('The request body is invalid.', 'invalid_request', 400);
    }
    try {
        $payload = json_decode($raw, true, 32, JSON_THROW_ON_ERROR);
    } catch (JsonException) {
        throw new NarrationApiException('The JSON body is invalid.', 'invalid_json', 400);
    }
    if (!is_array($payload)) {
        throw new NarrationApiException('The JSON body is invalid.', 'invalid_json', 400);
    }
    return $payload;
}

function narration_require_admin(): int
{
    if (!is_admin()) {
        throw new NarrationApiException('Administrator access is required.', 'admin_required', 403);
    }

    $role = strtolower(trim((string)($_SESSION['role'] ?? $_SESSION['admin_role'] ?? 'admin')));
    if (!in_array($role, ['admin', 'super_admin'], true)) {
        throw new NarrationApiException('Administrator access is required.', 'admin_required', 403);
    }

    $adminId = (int)($_SESSION['user_id'] ?? $_SESSION['french_admin_id'] ?? 1);
    return max(1, $adminId);
}

function narration_verify_request_csrf(array $payload): void
{
    $headerToken = (string)($_SERVER['HTTP_X_CSRF_TOKEN'] ?? '');
    $payloadToken = (string)($payload['csrf_token'] ?? '');
    $token = $headerToken !== '' ? $headerToken : $payloadToken;
    if (!french_verify_csrf($token)) {
        throw new NarrationApiException('The session expired. Refresh and try again.', 'csrf_failed', 419);
    }
}

function narration_validate_payload(array $payload): array
{
    $config = narration_config();
    $provider = strtolower(trim((string)($payload['provider'] ?? '')));
    $voice = trim((string)($payload['voice'] ?? ''));
    $language = trim((string)($payload['language'] ?? ''));
    $format = strtolower(trim((string)($payload['format'] ?? 'mp3')));
    $text = trim((string)($payload['text'] ?? ''));
    $instructions = trim((string)($payload['instructions'] ?? ''));
    $lessonId = filter_var($payload['lesson_id'] ?? null, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
    $speed = is_numeric($payload['speed'] ?? null) ? (float)$payload['speed'] : 1.0;

    if (!in_array($provider, (array)$config['allowed_providers'], true)) {
        throw new NarrationApiException('The narration provider is not allowed.', 'invalid_provider', 422);
    }
    if (!in_array($format, (array)$config['allowed_formats'], true)) {
        throw new NarrationApiException('The audio format is not allowed.', 'invalid_format', 422);
    }
    if (!in_array($language, (array)$config['allowed_languages'], true)) {
        throw new NarrationApiException('The language is not allowed.', 'invalid_language', 422);
    }
    if ($lessonId === false || lesson_by_id((int)$lessonId) === null) {
        throw new NarrationApiException('The selected lesson does not exist.', 'invalid_lesson', 422);
    }
    $textLength = function_exists('mb_strlen') ? mb_strlen($text, 'UTF-8') : strlen($text);
    if ($text === '' || $textLength > (int)$config['max_text_length']) {
        throw new NarrationApiException('The narration text is empty or too long.', 'invalid_text', 422);
    }
    $instructionLength = function_exists('mb_strlen') ? mb_strlen($instructions, 'UTF-8') : strlen($instructions);
    if ($instructionLength > (int)$config['max_instructions_length']) {
        throw new NarrationApiException('The narration instructions are too long.', 'invalid_instructions', 422);
    }
    if ($speed < 0.25 || $speed > 4.0) {
        throw new NarrationApiException('The narration speed is outside the allowed range.', 'invalid_speed', 422);
    }
    if ($voice === '' || !preg_match('/^[A-Za-z0-9._-]{1,80}$/', $voice)) {
        throw new NarrationApiException('The selected voice is invalid.', 'invalid_voice', 422);
    }

    return [
        'lesson_id' => (int)$lessonId,
        'provider' => $provider,
        'voice' => $voice,
        'language' => $language,
        'text' => $text,
        'instructions' => $instructions,
        'speed' => round($speed, 2),
        'format' => $format,
        'save' => !array_key_exists('save', $payload) || (bool)$payload['save'],
    ];
}

function narration_content_hash(array $request): string
{
    $canonical = [
        'provider' => $request['provider'],
        'voice' => $request['voice'],
        'language' => $request['language'],
        'text' => preg_replace('/\s+/u', ' ', trim((string)$request['text'])),
        'instructions' => preg_replace('/\s+/u', ' ', trim((string)$request['instructions'])),
        'speed' => number_format((float)$request['speed'], 2, '.', ''),
        'format' => $request['format'],
    ];
    return hash('sha256', json_encode($canonical, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR));
}

function narration_enforce_rate_limit(PDO $pdo, int $adminId, string $action): void
{
    $limitConfig = (array)(narration_config()['rate_limits'][$action] ?? ['requests' => 10, 'window_seconds' => 3600]);
    $limit = max(1, (int)($limitConfig['requests'] ?? 10));
    $window = max(60, (int)($limitConfig['window_seconds'] ?? 3600));
    $now = time();

    try {
        $pdo->beginTransaction();
        $stmt = $pdo->prepare('SELECT window_started_at, request_count FROM french_narration_rate_limits WHERE admin_id=? AND action=?');
        $stmt->execute([$adminId, $action]);
        $row = $stmt->fetch();

        if (!$row) {
            $insert = $pdo->prepare('INSERT INTO french_narration_rate_limits(admin_id,action,window_started_at,request_count) VALUES(?,?,?,1)');
            $insert->execute([$adminId, $action, $now]);
        } elseif ($now - (int)$row['window_started_at'] >= $window) {
            $reset = $pdo->prepare('UPDATE french_narration_rate_limits SET window_started_at=?, request_count=1 WHERE admin_id=? AND action=?');
            $reset->execute([$now, $adminId, $action]);
        } elseif ((int)$row['request_count'] >= $limit) {
            $pdo->rollBack();
            throw new NarrationApiException('Too many narration requests. Try again later.', 'rate_limited', 429);
        } else {
            $update = $pdo->prepare('UPDATE french_narration_rate_limits SET request_count=request_count+1 WHERE admin_id=? AND action=?');
            $update->execute([$adminId, $action]);
        }
        $pdo->commit();
    } catch (NarrationApiException $error) {
        throw $error;
    } catch (Throwable $error) {
        if ($pdo->inTransaction()) $pdo->rollBack();
        error_log('Narration rate limiter failed: ' . $error->getMessage());
        throw new NarrationApiException('Narration protection is temporarily unavailable.', 'rate_limit_unavailable', 503);
    }
}

function narration_valid_mp3(string $audio): bool
{
    $length = strlen($audio);
    if ($length < 128) return false;
    if (substr($audio, 0, 3) === 'ID3') return true;
    return ord($audio[0]) === 0xFF && (ord($audio[1]) & 0xE0) === 0xE0;
}

function narration_cached_audio(PDO $pdo, int $lessonId, string $contentHash): ?array
{
    $stmt = $pdo->prepare("SELECT * FROM french_lesson_audio WHERE lesson_id=? AND content_hash=? AND generation_status='ready' LIMIT 1");
    $stmt->execute([$lessonId, $contentHash]);
    $row = $stmt->fetch();
    if (!$row || empty($row['audio_path'])) return null;

    $config = narration_config();
    $baseUrl = rtrim((string)$config['public_storage_url'], '/');
    $url = (string)$row['audio_path'];
    if (!str_starts_with($url, $baseUrl . '/')) return null;
    $relative = ltrim(substr($url, strlen($baseUrl)), '/');
    $file = rtrim((string)$config['public_storage_path'], DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $relative);
    return is_file($file) && filesize($file) > 128 ? $row : null;
}

function narration_processing_record(PDO $pdo, array $request, string $contentHash, int $adminId): int
{
    $driver = (string)$pdo->getAttribute(PDO::ATTR_DRIVER_NAME);
    $verb = $driver === 'mysql' ? 'INSERT IGNORE' : 'INSERT OR IGNORE';
    $sql = $verb . " INTO french_lesson_audio(lesson_id,provider,voice,language,format,audio_path,content_hash,generation_status,error_code,created_by) VALUES(?,?,?,?,?,'',?,'processing',NULL,?)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$request['lesson_id'], $request['provider'], $request['voice'], $request['language'], $request['format'], $contentHash, $adminId]);

    $find = $pdo->prepare('SELECT id FROM french_lesson_audio WHERE lesson_id=? AND content_hash=? LIMIT 1');
    $find->execute([$request['lesson_id'], $contentHash]);
    $id = (int)$find->fetchColumn();
    if ($id < 1) throw new RuntimeException('Narration record could not be created.');

    $reset = $pdo->prepare("UPDATE french_lesson_audio SET provider=?, voice=?, language=?, format=?, audio_path='', generation_status='processing', error_code=NULL, created_by=? WHERE id=?");
    $reset->execute([$request['provider'], $request['voice'], $request['language'], $request['format'], $adminId, $id]);
    return $id;
}

function narration_store_mp3(string $audio, int $lessonId, int $audioId): array
{
    if (!narration_valid_mp3($audio)) {
        throw new NarrationProviderException('Generated MP3 failed validation.', 'invalid_audio_file', true);
    }

    $config = narration_config();
    $year = date('Y');
    $month = date('m');
    $directory = rtrim((string)$config['public_storage_path'], DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . $year . DIRECTORY_SEPARATOR . $month;
    if (!is_dir($directory) && !mkdir($directory, 0755, true) && !is_dir($directory)) {
        throw new RuntimeException('Narration storage could not be created.');
    }

    $random = bin2hex(random_bytes(8));
    $filename = 'lesson-' . $lessonId . '-' . $audioId . '-' . $random . '.mp3';
    $finalPath = $directory . DIRECTORY_SEPARATOR . $filename;
    $tempPath = $directory . DIRECTORY_SEPARATOR . '.' . $filename . '.tmp';
    $bytes = file_put_contents($tempPath, $audio, LOCK_EX);
    if ($bytes === false || $bytes !== strlen($audio) || !rename($tempPath, $finalPath)) {
        if (is_file($tempPath)) @unlink($tempPath);
        throw new RuntimeException('Narration audio could not be saved.');
    }
    @chmod($finalPath, 0644);

    $url = rtrim((string)$config['public_storage_url'], '/') . '/' . $year . '/' . $month . '/' . $filename;
    return ['file' => $finalPath, 'url' => $url];
}

function narration_public_error(Throwable $error): never
{
    if ($error instanceof NarrationApiException) {
        narration_json(['success' => false, 'error' => $error->errorCode(), 'message' => $error->getMessage()], $error->httpStatus());
    }
    if ($error instanceof NarrationProviderException) {
        error_log('Narration provider failure [' . $error->errorCode() . ']: ' . $error->getMessage());
        $status = $error->errorCode() === 'invalid_voice' ? 422 : 503;
        $message = $error->errorCode() === 'provider_not_configured'
            ? 'The selected narration provider is not configured.'
            : ($error->errorCode() === 'invalid_voice' ? 'The selected voice is unavailable.' : 'Narration is temporarily unavailable.');
        narration_json(['success' => false, 'error' => $error->errorCode(), 'message' => $message], $status);
    }
    error_log('Narration endpoint failure: ' . $error->getMessage());
    narration_json(['success' => false, 'error' => 'narration_failed', 'message' => 'Narration could not be completed.'], 500);
}
