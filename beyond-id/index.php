<?php
require_once __DIR__ . '/includes/session.php';
if (isset($_SESSION['user_id'])) {
    header('Location: dashboard/');
    exit;
}
header('Location: auth/login.php');
exit;
