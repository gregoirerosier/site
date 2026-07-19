<?php

declare(strict_types=1);

header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: public, max-age=300, stale-while-revalidate=3600');

$archiveId = 'SnowWhiteWithBettyBoop1933';
$cacheFile = dirname(__DIR__) . '/cache/betty-provider.json';
$cacheTtl = 21600;

/** @return array<string,mixed>|null */
function fetch_json(string $url): ?array
{
    $body = null;

    if (function_exists('curl_init')) {
        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_CONNECTTIMEOUT => 3,
            CURLOPT_TIMEOUT => 6,
            CURLOPT_USERAGENT => 'BeyondTV/2.1 (+https://beyondimagination.co.technology)',
            CURLOPT_SSL_VERIFYPEER => true,
        ]);
        $result = curl_exec($ch);
        $status = (int) curl_getinfo($ch, CURLINFO_RESPONSE_CODE);
        curl_close($ch);
        if (is_string($result) && $status >= 200 && $status < 300) {
            $body = $result;
        }
    }

    if ($body === null && filter_var(ini_get('allow_url_fopen'), FILTER_VALIDATE_BOOLEAN)) {
        $context = stream_context_create([
            'http' => [
                'timeout' => 6,
                'follow_location' => 1,
                'user_agent' => 'BeyondTV/2.1 (+https://beyondimagination.co.technology)',
            ],
        ]);
        $result = @file_get_contents($url, false, $context);
        if (is_string($result)) {
            $body = $result;
        }
    }

    if ($body === null) {
        return null;
    }

    $decoded = json_decode($body, true);
    return is_array($decoded) ? $decoded : null;
}

function archive_file_url(string $identifier, string $filename): string
{
    $parts = array_map('rawurlencode', explode('/', $filename));
    return 'https://archive.org/download/' . rawurlencode($identifier) . '/' . implode('/', $parts);
}

/** @return array<int,array<string,string>> */
function fallback_sources(): array
{
    return [
        [
            'provider' => 'Wikimedia Commons',
            'title' => "Betty Boop's Rise to Fame (1934)",
            'url' => "https://commons.wikimedia.org/wiki/Special:Redirect/file/Betty_Boop%27s_Rise_to_Fame_%281934%29.webm",
            'type' => 'video/webm',
            'rights' => 'Public domain in the United States; verify availability by territory.',
        ],
        [
            'provider' => 'Wikimedia Commons',
            'title' => 'Poor Cinderella (1934)',
            'url' => 'https://commons.wikimedia.org/wiki/Special:Redirect/file/Betty_Boop_-_Poor_Cinderella_%281934%29_-_HD.webm',
            'type' => 'video/webm',
            'rights' => 'Public domain in the United States; verify availability by territory.',
        ],
    ];
}

$sources = [];
$usedCache = false;

if (is_file($cacheFile) && (time() - (int) filemtime($cacheFile)) < $cacheTtl) {
    $cached = json_decode((string) file_get_contents($cacheFile), true);
    if (is_array($cached) && isset($cached['sources']) && is_array($cached['sources'])) {
        $sources = $cached['sources'];
        $usedCache = true;
    }
}

if (!$sources) {
    $metadata = fetch_json('https://archive.org/metadata/' . rawurlencode($archiveId));
    if ($metadata && isset($metadata['files']) && is_array($metadata['files'])) {
        $candidates = [];
        foreach ($metadata['files'] as $file) {
            if (!is_array($file)) {
                continue;
            }
            $name = (string) ($file['name'] ?? '');
            $format = strtolower((string) ($file['format'] ?? ''));
            if ($name === '' || !preg_match('/\.(mp4|m4v)$/i', $name)) {
                continue;
            }
            if (str_contains($format, 'thumbnail') || str_contains(strtolower($name), 'thumb')) {
                continue;
            }
            $size = (int) ($file['size'] ?? 0);
            $candidates[] = ['name' => $name, 'size' => $size];
        }
        usort($candidates, static fn(array $a, array $b): int => $b['size'] <=> $a['size']);
        if ($candidates) {
            $sources[] = [
                'provider' => 'Internet Archive',
                'title' => 'Snow White (1933)',
                'url' => archive_file_url($archiveId, (string) $candidates[0]['name']),
                'type' => 'video/mp4',
                'rights' => 'Source item identifies the film as public domain in the United States.',
            ];
        }
    }

    $sources = array_merge($sources, fallback_sources());
    @file_put_contents($cacheFile, json_encode([
        'created_at' => gmdate(DATE_ATOM),
        'sources' => $sources,
    ], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
}

if (!$sources) {
    $sources = fallback_sources();
}

echo json_encode([
    'ok' => true,
    'channel' => [
        'slug' => 'vintage-cartoon-theater',
        'name' => 'Classic Cartoon Theater',
        'programme' => 'Betty Boop Classics',
        'mode' => 'provider-fallback',
    ],
    'sources' => $sources,
    'embed_fallback' => 'https://archive.org/embed/SnowWhiteWithBettyBoop1933',
    'cache' => $usedCache ? 'hit' : 'refreshed',
], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
