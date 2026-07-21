<?php
declare(strict_types=1);
header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: public, max-age=600');

function media_metadata(string $id): ?array {
    $url = 'https://archive.org/metadata/' . rawurlencode($id);
    $body = null;
    if (function_exists('curl_init')) {
        $ch = curl_init($url);
        curl_setopt_array($ch, [CURLOPT_RETURNTRANSFER=>true, CURLOPT_FOLLOWLOCATION=>true, CURLOPT_CONNECTTIMEOUT=>4, CURLOPT_TIMEOUT=>12, CURLOPT_USERAGENT=>'BeyondMedia/2.2.1']);
        $result = curl_exec($ch); $status = (int)curl_getinfo($ch, CURLINFO_RESPONSE_CODE); curl_close($ch);
        if (is_string($result) && $status >= 200 && $status < 300) $body = $result;
    }
    if ($body === null && filter_var(ini_get('allow_url_fopen'), FILTER_VALIDATE_BOOLEAN)) {
        $result = @file_get_contents($url, false, stream_context_create(['http'=>['timeout'=>12,'user_agent'=>'BeyondMedia/2.2.1']]));
        if (is_string($result)) $body = $result;
    }
    $decoded = $body !== null ? json_decode($body, true) : null;
    return is_array($decoded) ? $decoded : null;
}

$id = preg_replace('/[^A-Za-z0-9_.-]/', '', (string)($_GET['id'] ?? ''));
if ($id === '') { http_response_code(400); echo json_encode(['ok'=>false,'error'=>'Invalid item.']); exit; }
$metadata = media_metadata($id);
if (!$metadata) { http_response_code(502); echo json_encode(['ok'=>false,'error'=>'Source metadata is unavailable.']); exit; }

$meta = is_array($metadata['metadata'] ?? null) ? $metadata['metadata'] : [];
$licenseUrl = (string)($meta['licenseurl'] ?? '');
$rights = (string)($meta['rights'] ?? '');
$licenseText = strtolower($licenseUrl . ' ' . $rights);
$authorized = strpos($licenseText, 'creativecommons.org') !== false || strpos($licenseText, 'public domain') !== false || strpos($licenseText, 'publicdomain') !== false;
$allowedExt = ['mp3','m4a','ogg','flac','wav','mp4','m4v','webm','ogv'];
$files = [];
if ($authorized) {
    foreach (is_array($metadata['files'] ?? null) ? $metadata['files'] : [] as $file) {
        if (!is_array($file) || empty($file['name'])) continue;
        $name = (string)$file['name']; $ext = strtolower(pathinfo($name, PATHINFO_EXTENSION));
        if (!in_array($ext, $allowedExt, true) || strpos(strtolower($name), '_thumb') !== false) continue;
        $files[] = [
            'name' => $name,
            'format' => (string)($file['format'] ?? strtoupper($ext)),
            'size' => isset($file['size']) ? (int)$file['size'] : null,
            'kind' => in_array($ext, ['mp3','m4a','ogg','flac','wav'], true) ? 'audio' : 'video',
            'url' => 'https://archive.org/download/' . rawurlencode($id) . '/' . implode('/', array_map('rawurlencode', explode('/', $name))),
        ];
        if (count($files) >= 30) break;
    }
}
echo json_encode([
    'ok'=>true,
    'authorized'=>$authorized,
    'license_url'=>$licenseUrl,
    'rights'=>$rights,
    'source_url'=>'https://archive.org/details/'.rawurlencode($id),
    'files'=>$files,
], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
