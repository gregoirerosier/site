<?php
require __DIR__ . '/includes/functions.php';
if ($_SERVER['REQUEST_METHOD'] !== 'POST') { header('Location: index.php'); exit; }
if (!french_verify_csrf()) { http_response_code(419); exit('Session expired. Please return and try again.'); }
if (trim($_POST['website'] ?? '') !== '') { header('Location: thank-you.php?status=new'); exit; }
$name = trim($_POST['name'] ?? ''); $email = trim($_POST['email'] ?? ''); $preferred = trim($_POST['preferred_language'] ?? 'French');
$allowed = ['French', 'Kreyol', 'Patois', 'Spanish'];
if ($name === '' || mb_strlen($name) > 100 || !filter_var($email, FILTER_VALIDATE_EMAIL) || !in_array($preferred, $allowed, true)) { http_response_code(422); exit('Please provide valid signup information.'); }
try {
    $stmt = sqlite_db()->prepare('INSERT INTO french_subscribers(id,name,email,preferred_language,consent_at,created_at) VALUES(?,?,?,?,?,?)');
    $now = date(DATE_ATOM); $stmt->execute([bin2hex(random_bytes(8)), $name, strtolower($email), $preferred, $now, $now]);
    header('Location: thank-you.php?status=new'); exit;
} catch (PDOException $e) {
    if ((string)$e->getCode() === '23000' || str_contains($e->getMessage(), 'UNIQUE')) { header('Location: thank-you.php?status=existing'); exit; }
    error_log('Subscriber save failed: ' . $e->getMessage()); http_response_code(500); exit('Unable to save your signup right now.');
}
