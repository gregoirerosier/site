<?php
declare(strict_types=1);

if (!function_exists('e')) {
    function e($value): string {
        return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
    }
}

function log_activity(PDO $pdo, ?int $user_id, string $action): void {
    try {
        $stmt = $pdo->prepare("INSERT INTO activity_logs (user_id, action, ip_address) VALUES (?, ?, ?)");
        $stmt->execute([$user_id, $action, $_SERVER['REMOTE_ADDR'] ?? null]);
    } catch (Throwable $e) {
        error_log('Activity log failed: ' . $e->getMessage());
    }
}

function csrf_token(): string {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return (string)$_SESSION['csrf_token'];
}

function verify_csrf_token(?string $token): bool {
    return is_string($token) && hash_equals(csrf_token(), $token);
}

function safe_return_path(?string $path, string $fallback = '../dashboard/'): string {
    if (!$path || !str_starts_with($path, '/') || str_starts_with($path, '//')) return $fallback;
    return $path;
}

function register_session(PDO $pdo, int $userId): void {
    try {
        $token = hash('sha256', session_id());
        $now = date('Y-m-d H:i:s');
        $expiresAt = date('Y-m-d H:i:s', time() + 30 * 86400);
        if ($pdo->getAttribute(PDO::ATTR_DRIVER_NAME) === 'sqlite') {
            $sql = "INSERT INTO user_sessions
                (user_id,session_token_hash,ip_address,user_agent,last_seen_at,expires_at)
                VALUES (?,?,?,?,?,?)
                ON CONFLICT(user_id,session_token_hash) DO UPDATE SET
                ip_address=excluded.ip_address,user_agent=excluded.user_agent,last_seen_at=excluded.last_seen_at,expires_at=excluded.expires_at,revoked_at=NULL";
        } else {
            $sql = "INSERT INTO user_sessions
                (user_id,session_token_hash,ip_address,user_agent,last_seen_at,expires_at)
                VALUES (?,?,?,?,?,?)
                ON DUPLICATE KEY UPDATE ip_address=VALUES(ip_address),user_agent=VALUES(user_agent),last_seen_at=VALUES(last_seen_at),expires_at=VALUES(expires_at),revoked_at=NULL";
        }
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$userId, $token, $_SERVER['REMOTE_ADDR'] ?? null, substr($_SERVER['HTTP_USER_AGENT'] ?? 'Browser', 0, 500), $now, $expiresAt]);
    } catch (Throwable $e) {
        error_log('Session registry unavailable: ' . $e->getMessage());
    }
}

function touch_session(PDO $pdo, int $userId): void {
    try {
        $token = hash('sha256', session_id());
        $stmt = $pdo->prepare("UPDATE user_sessions SET last_seen_at=? WHERE user_id=? AND session_token_hash=? AND revoked_at IS NULL");
        $stmt->execute([date('Y-m-d H:i:s'), $userId, $token]);
    } catch (Throwable $e) {}
}

function create_notification(PDO $pdo, int $userId, string $title, string $body, ?string $url = null, string $type = 'system'): void {
    try {
        $stmt = $pdo->prepare("INSERT INTO user_notifications (user_id,type,title,body,action_url) VALUES (?,?,?,?,?)");
        $stmt->execute([$userId, $type, $title, $body, $url]);
    } catch (Throwable $e) {
        error_log('Notification creation failed: ' . $e->getMessage());
    }
}

function unread_notification_count(PDO $pdo, int $userId): int {
    try {
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM user_notifications WHERE user_id=? AND read_at IS NULL");
        $stmt->execute([$userId]);
        return (int)$stmt->fetchColumn();
    } catch (Throwable $e) { return 0; }
}
