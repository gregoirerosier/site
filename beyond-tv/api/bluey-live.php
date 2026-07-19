<?php
declare(strict_types=1);
header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: public, max-age=30');

$librarySources = [
    ['file' => 'bluey-library.json', 'show' => 'Bluey'],
    ['file' => 'blues-clues-library.json', 'show' => "Blue's Clues"],
    ['file' => 'allegras-window-library.json', 'show' => "Allegra's Window"],
    ['file' => 'gullah-gullah-library.json', 'show' => 'Gullah Gullah Island'],
];
$library = [];
foreach ($librarySources as $source) {
    $episodes = json_decode((string) @file_get_contents(__DIR__ . '/../data/' . $source['file']), true);
    if (!is_array($episodes)) { continue; }
    foreach ($episodes as $episode) { $episode['show'] = $source['show']; $library[] = $episode; }
}
if (!is_array($library) || $library === []) {
    http_response_code(503);
    echo json_encode(['ok' => false, 'error' => 'Bluey library is unavailable.']);
    exit;
}

$durations = array_map(static fn(array $episode): int => max(60, (int) ($episode['runtime_seconds'] ?? 420)), $library);
$total = array_sum($durations);
$position = $total > 0 ? time() % $total : 0;
$currentIndex = 0;
$offset = 0;
foreach ($durations as $index => $duration) {
    if ($position < $duration) {
        $currentIndex = $index;
        $offset = $position;
        break;
    }
    $position -= $duration;
}

$ordered = array_merge(array_slice($library, $currentIndex), array_slice($library, 0, $currentIndex));
$sources = array_map(static fn(array $episode): array => [
    'provider' => 'Internet Archive',
    'title' => sprintf('%s · S%02dE%02d · %s', (string) $episode['show'], (int) $episode['season'], (int) $episode['episode'], (string) $episode['title']),
    'url' => (string) $episode['video_url'],
    'duration' => max(60, (int) ($episode['runtime_seconds'] ?? 420)),
    'type' => 'video/mp4',
], $ordered);

$current = $library[$currentIndex];
$next = $library[($currentIndex + 1) % count($library)];
echo json_encode([
    'ok' => true,
    'channel' => [
        'slug' => 'bubble-guppies',
        'name' => 'Preschool TV',
        'mode' => 'pseudo-live',
        'programme' => sprintf('%s · S%02dE%02d · %s', $current['show'], $current['season'], $current['episode'], $current['title']),
        'up_next' => sprintf('%s · S%02dE%02d · %s', $next['show'], $next['season'], $next['episode'], $next['title']),
    ],
    'sources' => $sources,
    'start_offset' => $offset,
    'playlist_duration' => $total,
    'server_time' => time(),
    'embed_fallback' => 'https://archive.org/embed/bluey-iso-archive',
], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
