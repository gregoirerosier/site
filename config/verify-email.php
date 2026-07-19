<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . '/../config/db.php';

$token = $_GET['token'] ?? '';

if (!$token) {
    die("Invalid verification link.");
}

$stmt = $pdo->prepare("
    SELECT *
    FROM email_verifications
    WHERE token = ?
    AND used_at IS NULL
    AND expires_at > NOW()
    LIMIT 1
");
$stmt->execute([$token]);
$verification = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$verification) {
    die("Verification link is invalid or expired.");
}

$pdo->beginTransaction();

try {
    $updateUser = $pdo->prepare("
        UPDATE users
        SET email_verified = 1
        WHERE id = ?
    ");
    $updateUser->execute([$verification['user_id']]);

    $markUsed = $pdo->prepare("
        UPDATE email_verifications
        SET used_at = NOW()
        WHERE id = ?
    ");
    $markUsed->execute([$verification['id']]);

    $pdo->commit();

    echo "
    <div style='font-family:Arial;background:#0b0b0f;color:white;min-height:100vh;display:flex;align-items:center;justify-content:center;text-align:center;'>
        <div>
            <h1>Email Verified ✅</h1>
            <p>Your account is now active.</p>
            <a href='login.php' style='color:#ff8a1d;'>Continue to login</a>
        </div>
    </div>
    ";

} catch (Exception $e) {
    $pdo->rollBack();
    die("Verification failed.");
}