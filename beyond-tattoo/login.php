<?php
declare(strict_types=1);
require __DIR__ . '/includes/config.php';

if (is_logged_in()) redirect('dashboard.php');
$returnTo = beyond_url('beyond-tattoo/dashboard.php');
$_SESSION['beyond_return_to'] = $returnTo;
redirect(beyond_url('beyond-id/auth/login.php?required=1&app=beyond-tattoo&return=' . rawurlencode($returnTo)));

