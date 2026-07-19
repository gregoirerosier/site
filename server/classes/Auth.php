<?php
class Auth {
    public static function check(): bool { return !empty($_SESSION['user_id']); }
    public static function user(): ?array {
        if (!self::check()) return null;
        $stmt = Database::conn()->prepare("SELECT id,name,email,role,status,last_login FROM users WHERE id=? LIMIT 1");
        $stmt->execute([$_SESSION['user_id']]);
        return $stmt->fetch() ?: null;
    }
    public static function login(string $email, string $password): array {
        $stmt = Database::conn()->prepare("SELECT id,name,email,password_hash,role,status FROM users WHERE email=? LIMIT 1");
        $stmt->execute([$email]);
        $user = $stmt->fetch();
        if (!$user || !password_verify($password, $user['password_hash'])) {
            self::log(null, 'login_failed', $email);
            return ['ok'=>false, 'message'=>'Invalid email or password.'];
        }
        if (($user['status'] ?? 'active') !== 'active') return ['ok'=>false, 'message'=>'Account inactive.'];
        session_regenerate_id(true);
        $_SESSION['user_id'] = (int)$user['id'];
        $_SESSION['user_email'] = $user['email'];
        $_SESSION['user_name'] = $user['name'];
        $_SESSION['user_role'] = $user['role'];
        Database::conn()->prepare("UPDATE users SET last_login=NOW() WHERE id=?")->execute([$user['id']]);
        self::log((int)$user['id'], 'login_success', $user['email']);
        return ['ok'=>true];
    }
    public static function logout(): void {
        if (self::check()) self::log((int)$_SESSION['user_id'], 'logout', $_SESSION['user_email'] ?? '');
        $_SESSION = [];
        if (ini_get('session.use_cookies')) {
            $p = session_get_cookie_params();
            setcookie(session_name(), '', time()-42000, $p['path'], $p['domain'], $p['secure'], $p['httponly']);
        }
        session_destroy();
    }
    public static function requireLogin(): void { if (!self::check()) { header('Location: /server/admin/login.php'); exit; } }
    public static function csrf(): string {
        if (empty($_SESSION['csrf'])) $_SESSION['csrf'] = bin2hex(random_bytes(32));
        return $_SESSION['csrf'];
    }
    public static function verifyCsrf(?string $token): bool { return is_string($token) && hash_equals($_SESSION['csrf'] ?? '', $token); }
    public static function log(?int $userId, string $action, string $detail=''): void {
        try {
            $stmt = Database::conn()->prepare("INSERT INTO activity_logs (user_id, action, detail, ip_address, created_at) VALUES (?,?,?,?,NOW())");
            $stmt->execute([$userId, $action, $detail, $_SERVER['REMOTE_ADDR'] ?? '']);
        } catch (Throwable $e) { error_log($e->getMessage()); }
    }
}
