<?php
require_once __DIR__ . '/bootstrap.php';
Auth::logout();
header('Location: /server/admin/login.php'); exit;
