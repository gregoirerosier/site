<?php
declare(strict_types=1);

function beyond_split_sql(string $sql): array
{
    $statements = [];
    $buffer = '';
    $quote = null;
    $lineComment = false;
    $blockComment = false;
    $length = strlen($sql);

    for ($i = 0; $i < $length; $i++) {
        $char = $sql[$i];
        $next = $i + 1 < $length ? $sql[$i + 1] : '';

        if ($lineComment) {
            if ($char === "\n") { $lineComment = false; $buffer .= $char; }
            continue;
        }
        if ($blockComment) {
            if ($char === '*' && $next === '/') { $blockComment = false; $i++; }
            continue;
        }
        if ($quote === null && $char === '-' && $next === '-') { $lineComment = true; $i++; continue; }
        if ($quote === null && $char === '/' && $next === '*') { $blockComment = true; $i++; continue; }

        if ($quote !== null) {
            $buffer .= $char;
            if ($char === '\\' && $next !== '') { $buffer .= $next; $i++; continue; }
            if ($char === $quote) {
                if ($next === $quote) { $buffer .= $next; $i++; }
                else $quote = null;
            }
            continue;
        }

        if ($char === "'" || $char === '"' || $char === '`') { $quote = $char; $buffer .= $char; continue; }
        if ($char === ';') {
            if (trim($buffer) !== '') $statements[] = trim($buffer);
            $buffer = '';
            continue;
        }
        $buffer .= $char;
    }

    if (trim($buffer) !== '') $statements[] = trim($buffer);
    return $statements;
}

function beyond_run_migrations(PDO $pdo, string $driver): void
{
    if (getenv('BEYOND_AUTO_MIGRATE') === 'false') return;

    if ($driver === 'mysql') {
        $pdo->exec('CREATE TABLE IF NOT EXISTS schema_migrations (migration VARCHAR(255) PRIMARY KEY, applied_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4');
    } else {
        $pdo->exec('CREATE TABLE IF NOT EXISTS schema_migrations (migration TEXT PRIMARY KEY, applied_at TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP)');
    }

    $directory = __DIR__ . '/../database/migrations';
    $files = glob($directory . '/*_' . $driver . '.sql') ?: [];
    sort($files, SORT_NATURAL);
    $check = $pdo->prepare('SELECT 1 FROM schema_migrations WHERE migration=?');
    $record = $pdo->prepare('INSERT INTO schema_migrations (migration) VALUES (?)');

    foreach ($files as $file) {
        $name = basename($file);
        $check->execute([$name]);
        if ($check->fetchColumn()) continue;
        $sql = file_get_contents($file);
        if ($sql === false) throw new RuntimeException('Unable to read migration ' . $name);
        try {
            foreach (beyond_split_sql($sql) as $statement) {
                $pdo->exec($statement);
            }
        } catch (Throwable $exception) {
            throw new RuntimeException(
                'Migration ' . $name . ' failed: ' . $exception->getMessage(),
                0,
                $exception
            );
        }
        $record->execute([$name]);
    }
}
