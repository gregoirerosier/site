<?php
declare(strict_types=1);

function bt_stencil_defaults(): array
{
    return [
        'title' => 'Eye of Horus Anubis',
        'collection' => 'Beyond Ancient Collection',
        'display_date' => 'Saturday, July 18, 2026',
        'iso_date' => '2026-07-18',
        'description' => 'Premium realism stencil with clean, transfer-ready line work.',
        'preview_url' => 'assets/img/storefront/featured-anubis.webp',
        'package_url' => 'downloads/Beyond_Tattoo_Stencil_of_the_Day_Pack.zip',
        'ig_post_url' => 'assets/stencils/eye-of-horus-anubis-preview.webp',
        'editable_url' => 'assets/stencils/eye-of-horus-anubis-editable.svg',
        'transfer_png_url' => 'assets/stencils/eye-of-horus-anubis-transfer.png',
        'placement' => 'Outer forearm · 6.5–8.5 inches tall',
        'updated_at' => '',
    ];
}

function bt_stencil_data_file(): string
{
    return dirname(__DIR__, 3) . '/var/data/beyond-tattoo-stencil-day.json';
}

function bt_stencil_content(): array
{
    $data = bt_stencil_defaults();
    $file = bt_stencil_data_file();
    if (is_file($file)) {
        $decoded = json_decode((string) file_get_contents($file), true);
        if (is_array($decoded)) {
            foreach ($data as $key => $value) {
                if (isset($decoded[$key]) && is_string($decoded[$key])) {
                    $data[$key] = $decoded[$key];
                }
            }
        }
    }
    return $data;
}

function bt_stencil_save(array $data): void
{
    $file = bt_stencil_data_file();
    $dir = dirname($file);
    if (!is_dir($dir) && !mkdir($dir, 0775, true) && !is_dir($dir)) {
        throw new RuntimeException('Could not create the stencil data directory.');
    }
    $payload = array_merge(bt_stencil_defaults(), $data);
    $payload['updated_at'] = gmdate('c');
    $json = json_encode($payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
    if ($json === false || file_put_contents($file, $json, LOCK_EX) === false) {
        throw new RuntimeException('Could not save stencil settings. Check var/data permissions.');
    }
}
