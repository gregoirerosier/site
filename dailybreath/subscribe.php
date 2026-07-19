<?php
require __DIR__ . '/config/database.php';

$returnToLogin = ($_POST['return_to'] ?? '') === 'dailybreath_login';
$redirect = static function (string $status, string $message = '') use ($returnToLogin): never {
    if ($returnToLogin) {
        header('Location: ../beyond-id/auth/login.php?newsletter=' . $status);
    } else {
        header('Location: index.php?' . ($status === 'success' ? 'success=1' : 'error=' . urlencode($message)));
    }
    exit;
};

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit;
}
beyond_require_csrf();

if (!empty($_POST['website'] ?? '')) {
    $redirect('success');
}

$name = trim($_POST['name'] ?? '');
$email = strtolower(trim($_POST['email'] ?? ''));

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $redirect('error', 'Please enter a valid email address.');
}

try {
    $stmt = db()->prepare("INSERT INTO dailybreath_subscribers (name, email, ip_address, user_agent) VALUES (?, ?, ?, ?) ON DUPLICATE KEY UPDATE name=VALUES(name), status='active'");
    $stmt->execute([
        $name ?: null,
        $email,
        $_SERVER['REMOTE_ADDR'] ?? null,
        substr($_SERVER['HTTP_USER_AGENT'] ?? '', 0, 255)
    ]);

    $subject = 'Welcome to DailyBreath';
    $message = "Welcome to DailyBreath!\n\nThanks for joining our faith-centered wellness newsletter.\n\nBreathe. Pray. Reflect.\n\nLaunching October 2026.";
    $headers = "From: DailyBreath <no-reply@" . ($_SERVER['HTTP_HOST'] ?? 'dailybreath.app') . ">\r\n";
    @mail($email, $subject, $message, $headers);

    $redirect('success');
} catch (Throwable $e) {
    $redirect('error', 'Signup is temporarily unavailable. Please try again soon.');
}
