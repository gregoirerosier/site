<?php
declare(strict_types=1);

require_once __DIR__ . '/ecosystem.php';

function stencil_download_table(PDO $pdo): void
{
    $driver = $pdo->getAttribute(PDO::ATTR_DRIVER_NAME);
    if ($driver === 'sqlite') {
        $pdo->exec("CREATE TABLE IF NOT EXISTS stencil_downloads (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            user_id INTEGER NULL,
            session_hash TEXT NOT NULL,
            ip_hash TEXT NOT NULL,
            user_agent TEXT NULL,
            stencil_date TEXT NOT NULL,
            file_name TEXT NOT NULL,
            source TEXT NOT NULL DEFAULT 'stencil-of-day',
            downloaded_at TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP
        )");
        $pdo->exec('CREATE INDEX IF NOT EXISTS idx_stencil_downloaded_at ON stencil_downloads(downloaded_at)');
        $pdo->exec('CREATE INDEX IF NOT EXISTS idx_stencil_user_id ON stencil_downloads(user_id)');
    } else {
        $pdo->exec("CREATE TABLE IF NOT EXISTS stencil_downloads (
            id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
            user_id BIGINT UNSIGNED NULL,
            session_hash CHAR(64) NOT NULL,
            ip_hash CHAR(64) NOT NULL,
            user_agent VARCHAR(500) NULL,
            stencil_date DATE NOT NULL,
            file_name VARCHAR(190) NOT NULL,
            source VARCHAR(80) NOT NULL DEFAULT 'stencil-of-day',
            downloaded_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
            INDEX idx_stencil_downloaded_at (downloaded_at),
            INDEX idx_stencil_user_id (user_id),
            INDEX idx_stencil_date (stencil_date)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
    }
}

function stencil_client_ip(): string
{
    $forwarded = trim((string)($_SERVER['HTTP_X_FORWARDED_FOR'] ?? ''));
    if ($forwarded !== '') return trim(explode(',', $forwarded)[0]);
    return trim((string)($_SERVER['REMOTE_ADDR'] ?? 'unknown'));
}

function track_stencil_download(string $fileName, string $source = 'stencil-of-day'): void
{
    $record = [
        'user_id' => !empty($_SESSION['user_id']) ? (int)$_SESSION['user_id'] : null,
        'session_hash' => hash('sha256', session_id() ?: ('anonymous|' . stencil_client_ip() . '|' . ($_SERVER['HTTP_USER_AGENT'] ?? ''))),
        'ip_hash' => hash('sha256', stencil_client_ip() . '|beyond-stencil-v1'),
        'user_agent' => mb_substr((string)($_SERVER['HTTP_USER_AGENT'] ?? ''), 0, 500),
        'stencil_date' => date('Y-m-d'),
        'file_name' => $fileName,
        'source' => $source,
        'downloaded_at' => date('Y-m-d H:i:s'),
    ];

    try {
        $pdo = beyond_db();
        stencil_download_table($pdo);
        $stmt = $pdo->prepare('INSERT INTO stencil_downloads (user_id,session_hash,ip_hash,user_agent,stencil_date,file_name,source,downloaded_at) VALUES (?,?,?,?,?,?,?,?)');
        $stmt->execute([$record['user_id'],$record['session_hash'],$record['ip_hash'],$record['user_agent'],$record['stencil_date'],$record['file_name'],$record['source'],$record['downloaded_at']]);
    } catch (Throwable $e) {
        error_log('Stencil download database tracking failed: ' . $e->getMessage());
        $dir = __DIR__ . '/../storage/logs';
        if (!is_dir($dir)) @mkdir($dir, 0775, true);
        @file_put_contents($dir . '/stencil-downloads.jsonl', json_encode($record, JSON_UNESCAPED_SLASHES) . PHP_EOL, FILE_APPEND | LOCK_EX);
    }
}
