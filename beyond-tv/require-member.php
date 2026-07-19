<?php
declare(strict_types=1);
require_once __DIR__ . '/../includes/ecosystem.php';
if (empty($_SESSION['user_id'])) {
    $_SESSION['beyond_return_to'] = $_SERVER['REQUEST_URI'] ?? '/beyond-tv/';
    header('Location: /beyond-tv/');
    exit;
}
beyond_track_app('Beyond TV');
