<?php
declare(strict_types=1);

function bt_stencil_asset_path(string $relative): string
{
    $relative = str_replace('\\', '/', trim($relative));
    if ($relative === '' || str_contains($relative, '..') || preg_match('~^(?:[a-z]+:|/)~i', $relative)) throw new RuntimeException('Unsafe stencil asset path.');
    $root = realpath(dirname(__DIR__));
    $file = realpath(dirname(__DIR__) . '/' . $relative);
    if (!is_string($root) || !is_string($file) || !str_starts_with(str_replace('\\','/',$file), str_replace('\\','/',$root) . '/')) throw new RuntimeException('Stencil asset is missing: ' . $relative);
    return $file;
}

function bt_stencil_dos_time(int $timestamp): array
{
    $parts = getdate($timestamp);
    $time = (($parts['hours'] & 0x1f) << 11) | (($parts['minutes'] & 0x3f) << 5) | (($parts['seconds'] >> 1) & 0x1f);
    $date = ((max(1980, $parts['year']) - 1980) << 9) | (($parts['mon'] & 0x0f) << 5) | ($parts['mday'] & 0x1f);
    return [$time, $date];
}

function bt_write_store_zip(string $destination, array $files): void
{
    $handle = fopen($destination, 'w+b');
    if ($handle === false) throw new RuntimeException('Could not create stencil package.');
    $central = ''; $offset = 0; $count = 0;
    try {
        foreach ($files as $source => $archiveName) {
            $data = file_get_contents($source);
            if ($data === false) throw new RuntimeException('Could not read stencil asset.');
            $archiveName = ltrim(str_replace('\\','/',(string)$archiveName), '/');
            $size = strlen($data); $crc = (int)sprintf('%u', crc32($data));
            [$dosTime,$dosDate] = bt_stencil_dos_time((int)filemtime($source));
            $nameLength = strlen($archiveName);
            $local = pack('VvvvvvVVVvv',0x04034b50,20,0,0,$dosTime,$dosDate,$crc,$size,$size,$nameLength,0) . $archiveName . $data;
            if (fwrite($handle, $local) !== strlen($local)) throw new RuntimeException('Could not write stencil package.');
            $central .= pack('VvvvvvvVVVvvvvvVV',0x02014b50,20,20,0,0,$dosTime,$dosDate,$crc,$size,$size,$nameLength,0,0,0,0,0,$offset) . $archiveName;
            $offset += strlen($local); $count++;
        }
        if (fwrite($handle, $central) !== strlen($central)) throw new RuntimeException('Could not finalize stencil package.');
        $end = pack('VvvvvVVv',0x06054b50,0,0,$count,$count,strlen($central),$offset,0);
        if (fwrite($handle, $end) !== strlen($end)) throw new RuntimeException('Could not finalize stencil package.');
    } finally { fclose($handle); }
}

function bt_stencil_package(array $stencil, bool $force = false): string
{
    $packageFiles = $stencil['package_files'] ?? [];
    if (!is_array($packageFiles) || !$packageFiles) throw new RuntimeException('No stencil package assets are configured.');
    $resolved = []; $latest = 0;
    foreach ($packageFiles as $relative => $archiveName) {
        $file = bt_stencil_asset_path((string)$relative);
        $resolved[$file] = (string)$archiveName;
        $latest = max($latest, (int)filemtime($file));
    }
    $cacheDir = beyond_private_root() . '/cache/beyond-tattoo';
    if (!is_dir($cacheDir) && !mkdir($cacheDir, 0750, true) && !is_dir($cacheDir)) throw new RuntimeException('Could not create the stencil package cache.');
    $slug = preg_replace('/[^a-z0-9-]+/i', '-', (string)($stencil['slug'] ?? 'stencil-of-the-day'));
    $destination = $cacheDir . '/' . trim((string)$slug, '-') . '.zip';
    if ($force || !is_file($destination) || (int)filemtime($destination) < $latest) {
        $temporary = $destination . '.' . bin2hex(random_bytes(5)) . '.tmp';
        try { bt_write_store_zip($temporary, $resolved); if (is_file($destination) && !unlink($destination)) throw new RuntimeException('Could not replace the previous stencil package.'); if (!rename($temporary, $destination)) throw new RuntimeException('Could not publish stencil package.'); }
        finally { if (is_file($temporary)) @unlink($temporary); }
    }
    return $destination;
}
