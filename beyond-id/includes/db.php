<?php
declare(strict_types=1);

$db = require __DIR__ . '/../config/database.php';
require_once __DIR__ . '/migration-runner.php';

try {
    if (($db['driver'] ?? 'mysql') === 'sqlite') {
        if (!extension_loaded('pdo_sqlite')) {
            throw new RuntimeException('The pdo_sqlite PHP extension is required.');
        }

        $sqlitePath = (string)$db['sqlite_path'];
        $sqliteDirectory = dirname($sqlitePath);
        if (!is_dir($sqliteDirectory) && !mkdir($sqliteDirectory, 0750, true) && !is_dir($sqliteDirectory)) {
            throw new RuntimeException('Unable to create the SQLite data directory.');
        }

        $isNewDatabase = !is_file($sqlitePath) || filesize($sqlitePath) === 0;
        $pdo = new PDO('sqlite:' . $sqlitePath);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        $pdo->exec('PRAGMA foreign_keys = ON');
        $pdo->exec('PRAGMA busy_timeout = 5000');
        $pdo->exec('PRAGMA journal_mode = WAL');

        if ($isNewDatabase && getenv('BEYOND_SQLITE_AUTO_MIGRATE') !== 'false') {
            $schema = file_get_contents(__DIR__ . '/../database/sqlite-schema.sql');
            if ($schema === false) {
                throw new RuntimeException('Unable to load the SQLite schema.');
            }
            $pdo->exec($schema);
        }
    } elseif (($db['driver'] ?? 'mysql') === 'mysql') {
        $dsn = "mysql:host={$db['host']};dbname={$db['database']};charset={$db['charset']}";
        $pdo = new PDO($dsn, $db['username'], $db['password'], [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]);
    } else {
        throw new RuntimeException('Unsupported database driver: ' . ($db['driver'] ?? ''));
    }
} catch (Throwable $exception) {
    error_log('Database connection failed: ' . $exception->getMessage());
    http_response_code(500);
    exit('Database connection failed [DB-CONNECT].');
}

try {
    beyond_run_migrations($pdo, (string)($db['driver'] ?? 'sqlite'));
} catch (Throwable $exception) {
    error_log('Database migration failed: ' . $exception->getMessage());
    http_response_code(500);
    exit('Database setup failed [DB-MIGRATION].');
}
