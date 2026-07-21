<?php
declare(strict_types=1);
header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: public, max-age=300');

function media_fetch_json(string $url): ?array {
    $body = null;
    if (function_exists('curl_init')) {
        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_CONNECTTIMEOUT => 4,
            CURLOPT_TIMEOUT => 10,
            CURLOPT_USERAGENT => 'BeyondMedia/2.2.1',
        ]);
        $result = curl_exec($ch);
        $status = (int)curl_getinfo($ch, CURLINFO_RESPONSE_CODE);
        curl_close($ch);
        if (is_string($result) && $status >= 200 && $status < 300) $body = $result;
    }
    if ($body === null && filter_var(ini_get('allow_url_fopen'), FILTER_VALIDATE_BOOLEAN)) {
        $result = @file_get_contents($url, false, stream_context_create(['http' => ['timeout' => 10, 'user_agent' => 'BeyondMedia/2.2.1']]));
        if (is_string($result)) $body = $result;
    }
    $decoded = $body !== null ? json_decode($body, true) : null;
    return is_array($decoded) ? $decoded : null;
}

$query = trim((string)($_GET['q'] ?? ''));
$type = (string)($_GET['type'] ?? 'audio');
if (!in_array($type, ['audio', 'movies'], true)) $type = 'audio';
if ($query === '' || strlen($query) > 300) {
    http_response_code(400);
    echo json_encode(['ok' => false, 'error' => 'Enter a search up to 100 characters.']);
    exit;
}

$safeWords = preg_replace('/[^\pL\pN\s\-\'".]/u', ' ', $query);
$safeWords = trim((string)preg_replace('/\s+/', ' ', (string)$safeWords));
$licenseQuery = '(licenseurl:*creativecommons.org* OR licenseurl:*publicdomain*)';
$search = 'mediatype:' . $type . ' AND ' . $licenseQuery . ' AND (' . $safeWords . ')';
$params = 'q=' . rawurlencode($search)
    . '&fl%5B%5D=identifier&fl%5B%5D=title&fl%5B%5D=creator&fl%5B%5D=date'
    . '&fl%5B%5D=mediatype&fl%5B%5D=licenseurl&fl%5B%5D=description'
    . '&sort%5B%5D=downloads%20desc&rows=18&page=1&output=json';
$payload = media_fetch_json('https://archive.org/advancedsearch.php?' . $params);
if (!$payload) {
    http_response_code(502);
    echo json_encode(['ok' => false, 'error' => 'The open-media search is temporarily unavailable.']);
    exit;
}

$docs = $payload['response']['docs'] ?? [];
$results = [];
foreach (is_array($docs) ? $docs : [] as $doc) {
    if (!is_array($doc) || empty($doc['identifier'])) continue;
    $id = preg_replace('/[^A-Za-z0-9_.-]/', '', (string)$doc['identifier']);
    if ($id === '') continue;
    $creator = $doc['creator'] ?? 'Unknown creator';
    if (is_array($creator)) $creator = implode(', ', array_map('strval', $creator));
    $description = strip_tags((string)($doc['description'] ?? ''));
    $results[] = [
        'id' => $id,
        'title' => (string)($doc['title'] ?? $id),
        'creator' => (string)$creator,
        'year' => substr((string)($doc['date'] ?? ''), 0, 4),
        'type' => (string)($doc['mediatype'] ?? $type),
        'license_url' => (string)($doc['licenseurl'] ?? ''),
        'description' => substr($description, 0, 220),
        'thumbnail' => 'https://archive.org/services/img/' . rawurlencode($id),
        'source_url' => 'https://archive.org/details/' . rawurlencode($id),
    ];
}

echo json_encode(['ok' => true, 'query' => $query, 'results' => $results], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
