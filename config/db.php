<?php
declare(strict_types=1);
require_once __DIR__ . '/bootstrap.php';

if (!defined('DB_HOST')) define('DB_HOST', (string)beyond_config('database.host', ''));
if (!defined('DB_NAME')) define('DB_NAME', (string)beyond_config('database.name', ''));
if (!defined('DB_USER')) define('DB_USER', (string)beyond_config('database.user', ''));
if (!defined('DB_PASS')) define('DB_PASS', (string)beyond_config('database.pass', ''));

function db(): PDO
{
    static $pdo = null;
    if ($pdo instanceof PDO) return $pdo;
    try {
        $pdo = new PDO(
            'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4',
            DB_USER,
            DB_PASS,
            [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC, PDO::ATTR_EMULATE_PREPARES => false]
        );
        return $pdo;
    } catch (PDOException $e) {
        error_log('Database connection failed: ' . $e->getMessage());
        throw new RuntimeException('Database service unavailable.');
    }
}
