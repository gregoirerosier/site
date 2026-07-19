<?php
require_once dirname(__DIR__, 2) . '/config/security.php';
spl_autoload_register(function($class){
    $file = __DIR__ . '/../classes/' . $class . '.php';
    if (file_exists($file)) require_once $file;
});


// Bridge Beyond ID session if available
$root = dirname(__DIR__,2);
$bidBootstrap = $root . '/beyond-id/includes/bootstrap.php';
$bidAuth = $root . '/beyond-id/includes/auth.php';
if (file_exists($bidBootstrap)) require_once $bidBootstrap;
elseif (file_exists($bidAuth)) require_once $bidAuth;
