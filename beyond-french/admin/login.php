<?php
require __DIR__ . '/../includes/functions.php';
$error = '';
$ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!french_verify_csrf()) { $error = 'Session expired. Refresh and try again.'; }
    elseif (login_blocked($ip)) { $error = 'Too many attempts. Try again in 15 minutes.'; }
    else {
        $username = trim($_POST['username'] ?? ''); $password = $_POST['password'] ?? '';
        if (hash_equals(ADMIN_USERNAME, $username) && ADMIN_PASSWORD_HASH !== '' && password_verify($password, ADMIN_PASSWORD_HASH)) {
            clear_login_failures($ip); session_regenerate_id(true); $_SESSION['admin_authenticated'] = true; $_SESSION['admin_role'] = 'admin'; $_SESSION['french_admin_id'] = 1;
            header('Location: index.php'); exit;
        }
        record_login_failure($ip); $error = 'Incorrect login.';
    }
}
?><!doctype html><html lang="en"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><link rel="stylesheet" href="../assets/css/style.css"><title>Admin Login</title></head>
<body class="admin-body"><form class="admin-login" method="post"><a href="/" class="button" style="display:inline-block;margin-bottom:16px">← Back to Home</a><h1>Beyond French Admin</h1>
<input type="hidden" name="csrf_token" value="<?= h(french_csrf_token()) ?>">
<?php if ($error): ?><p class="error"><?= h($error) ?></p><?php endif; ?>
<input name="username" placeholder="Username" autocomplete="username" required>
<input name="password" type="password" placeholder="Password" autocomplete="current-password" required>
<button class="button primary">Sign in</button></form></body></html>
