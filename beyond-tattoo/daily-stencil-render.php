<?php
declare(strict_types=1);
header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store, max-age=0');
$path = __DIR__ . '/../tools/daily-stencil-video/public/daily-stencil.json';
if (!is_file($path)) {
    http_response_code(404);
    echo json_encode(['error' => 'Daily stencil manifest not found.']);
    exit;
}
$data = json_decode((string) file_get_contents($path), true);
if (!is_array($data)) {
    http_response_code(500);
    echo json_encode(['error' => 'Daily stencil manifest is invalid.']);
    exit;
}
$origin = 'https://' . ($_SERVER['HTTP_HOST'] ?? 'beyondimagination.co.technology');
foreach (['mainArtwork', 'studioTransfer', 'audioFile'] as $field) {
    $value = trim((string)($data[$field] ?? ''));
    if ($value !== '' && !preg_match('~^https?://~i', $value)) {
        $data[$field] = $origin . '/tools/daily-stencil-video/public/' . ltrim($value, '/');
    }
}
echo json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
