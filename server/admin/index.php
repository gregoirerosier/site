<?php
require_once __DIR__ . '/bootstrap.php';
if (Auth::check()) { header('Location: /server/admin/dashboard.php'); exit; }
header('Location: /server/admin/login.php'); exit;
