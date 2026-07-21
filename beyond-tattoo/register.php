<?php
declare(strict_types=1);
require __DIR__ . '/includes/config.php';

if (is_logged_in()) redirect('onboarding.php');
$_SESSION['beyond_return_to'] = beyond_url('beyond-tattoo/onboarding.php');
redirect(beyond_url('beyond-id/auth/register.php'));

