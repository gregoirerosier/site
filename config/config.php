<?php
declare(strict_types=1);
require_once __DIR__ . '/bootstrap.php';

$config = beyond_live_config();
date_default_timezone_set((string)beyond_config('app.timezone', 'America/Vancouver'));

define('APP_NAME', (string)beyond_config('app.name', 'Beyond Imagination Technology'));
define('APP_ENV', (string)beyond_config('app.env', 'production'));
define('APP_URL', (string)beyond_config('app.url', ''));
define('GOOGLE_MAPS_API_KEY', (string)beyond_config('app.google_maps_api_key', ''));
define('DB_HOST', (string)beyond_config('database.host', ''));
define('DB_NAME', (string)beyond_config('database.name', ''));
define('DB_USER', (string)beyond_config('database.user', ''));
define('DB_PASS', (string)beyond_config('database.pass', ''));
define('STRIPE_PUBLIC_KEY', (string)beyond_config('stripe.public_key', ''));
define('STRIPE_SECRET_KEY', (string)beyond_config('stripe.secret_key', ''));
define('STRIPE_WEBHOOK_SECRET', (string)beyond_config('stripe.webhook_secret', ''));
define('JWT_SECRET', (string)beyond_config('security.jwt_secret', ''));

try {
    $pdo = new PDO(
        'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4',
        DB_USER,
        DB_PASS,
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC]
    );
} catch (PDOException $e) {
    error_log('Database connection failed: ' . $e->getMessage());
    http_response_code(503);
    exit('The service is temporarily unavailable.');
}
