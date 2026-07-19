<?php
declare(strict_types=1);
require_once __DIR__ . '/security.php';

function beyond_private_root(): string
{
    $configured = getenv('BEYOND_VAR_PATH');
    if (is_string($configured) && $configured !== '') {
        return rtrim($configured, DIRECTORY_SEPARATOR);
    }
    return dirname(__DIR__, 2) . DIRECTORY_SEPARATOR . 'var';
}

function beyond_live_config(): array
{
    static $config;
    if (is_array($config)) {
        return $config;
    }
    $file = beyond_private_root() . '/config/live.php';
    if (!is_file($file)) {
        throw new RuntimeException('Protected configuration is unavailable.');
    }
    $loaded = require $file;
    if (!is_array($loaded)) {
        throw new RuntimeException('Protected configuration is invalid.');
    }
    $config = $loaded;
    return $config;
}

function beyond_config(string $path, $default = null)
{
    $value = beyond_live_config();
    foreach (explode('.', $path) as $part) {
        if (!is_array($value) || !array_key_exists($part, $value)) {
            return $default;
        }
        $value = $value[$part];
    }
    return $value;
}
