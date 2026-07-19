<?php
declare(strict_types=1);
require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/remember-me.php';
try { beyondRememberForget($pdo); } catch (Throwable $e) {}
if (!empty($_SESSION['user_id'])) {
    try {
        $hash=hash('sha256',session_id());
        $pdo->prepare('UPDATE user_sessions SET revoked_at=? WHERE user_id=? AND session_token_hash=?')->execute([date('Y-m-d H:i:s'),(int)$_SESSION['user_id'],$hash]);
    } catch (Throwable $e) {}
}
$_SESSION=[];
if (ini_get('session.use_cookies')) {$p=session_get_cookie_params();setcookie(session_name(),'',time()-42000,$p['path'],$p['domain'],$p['secure'],$p['httponly']);}
session_destroy();
header('Location: login.php');
exit;
