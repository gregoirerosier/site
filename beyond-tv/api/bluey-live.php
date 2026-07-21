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
$showLibraries = [];
foreach ($librarySources as $source) {
    $episodes = json_decode((string) @file_get_contents(__DIR__ . '/../data/' . $source['file']), true);
    if (!is_array($episodes)) { continue; }
    $showLibrary = [];
    foreach ($episodes as $episode) { $episode['show'] = $source['show']; $showLibrary[] = $episode; }
    if ($showLibrary) $showLibraries[] = $showLibrary;
}
$bubbleGuppies = [];
for ($index = 1; $index <= 24; $index++) {
    $bubbleGuppies[] = [
        'show' => 'Bubble Guppies',
        'season' => 0,
        'episode' => $index,
        'title' => 'Official preschool collection',
        'runtime_seconds' => 1500,
        'source_type' => 'youtube',
        'video_url' => 'https://www.youtube-nocookie.com/embed/videoseries?list=PLuzvx7jZbDrEgD0q3Ohi_p7ITdGVhy3JI&index=' . $index . '&autoplay=1&mute=1&controls=1&rel=0&playsinline=1',
    ];
}
$showLibraries[] = $bubbleGuppies;
// Interleave each preschool series instead of running the entire Bluey
// library before Blue's Clues and the other shows get airtime.
$library = [];
$largestLibrary = max(array_map('count', $showLibraries) ?: [0]);
for ($episodeIndex = 0; $episodeIndex < $largestLibrary; $episodeIndex++) {
    foreach ($showLibraries as $showLibrary) {
        if (isset($showLibrary[$episodeIndex])) $library[] = $showLibrary[$episodeIndex];
    }
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
    'provider' => ($episode['source_type'] ?? '') === 'youtube' ? 'Official Bubble Guppies YouTube' : 'Internet Archive',
    'title' => sprintf('%s · S%02dE%02d · %s', (string) $episode['show'], (int) $episode['season'], (int) $episode['episode'], (string) $episode['title']),
    'url' => (string) $episode['video_url'],
    'duration' => max(60, (int) ($episode['runtime_seconds'] ?? 420)),
    'type' => ($episode['source_type'] ?? '') === 'youtube' ? 'youtube' : 'video/mp4',
], $ordered);

$current = $library[$currentIndex];
$next = $library[($currentIndex + 1) % count($library)];
$archiveId = 'bluey-iso-archive';
if (preg_match('#archive\.org/download/([^/]+)#i', (string)($current['video_url'] ?? ''), $archiveMatch)) {
    $archiveId = rawurlencode(rawurldecode($archiveMatch[1]));
}
echo json_encode([
    'ok' => true,
    'channel' => [
        'slug' => 'bubble-guppies',
        'name' => 'Preschool English',
        'mode' => 'pseudo-live',
        'programme' => sprintf('%s · S%02dE%02d · %s', $current['show'], $current['season'], $current['episode'], $current['title']),
        'up_next' => sprintf('%s · S%02dE%02d · %s', $next['show'], $next['season'], $next['episode'], $next['title']),
    ],
    'sources' => $sources,
    'start_offset' => $offset,
    'playlist_duration' => $total,
    'server_time' => time(),
    'embed_fallback' => 'https://archive.org/embed/' . $archiveId,
], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
