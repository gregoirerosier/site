<?php
require_once __DIR__ . '/../config/security.php';
if ($_SERVER['REQUEST_METHOD'] !== 'POST') { header('Location: index.php'); exit; }
beyond_require_csrf();
$name = trim($_POST['name'] ?? ''); $email = trim($_POST['email'] ?? '');
if ($name === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) { header('Location: index.php?joined=0'); exit; }
$file = __DIR__ . '/data/beta-signups.json';
$rows = file_exists($file) ? json_decode(file_get_contents($file), true) : [];
if (!is_array($rows)) $rows = [];
$rows[] = ['name'=>$name,'email'=>$email,'created_at'=>date(DATE_ATOM),'ip_hash'=>hash('sha256', $_SERVER['REMOTE_ADDR'] ?? '')];
file_put_contents($file, json_encode($rows, JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES), LOCK_EX);
header('Location: index.php?joined=1');
