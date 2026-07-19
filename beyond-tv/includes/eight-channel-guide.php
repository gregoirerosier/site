<?php
declare(strict_types=1);

/** Kept under the original function name for backwards compatibility. */
function beyond_tv_eight_channel_guide(array $classicState, array $cartoonState): array {
    $featured = json_decode((string) @file_get_contents(__DIR__ . '/../data/featured-channels.json'), true) ?: [];
    $schedules = json_decode((string) @file_get_contents(__DIR__ . '/../data/channel-schedules.json'), true) ?: [];
    $guide = [];
    foreach ($featured as $channel) {
        $slug = (string)($channel['slug'] ?? '');
        $rows = $schedules[$slug] ?? [];
        if ($slug === 'classic-cartoon-theater' && !empty($classicState['blocks'])) { $rows = $classicState['blocks']; }
        if ($slug === 'beyond-cartoons' && !empty($cartoonState['blocks'])) { $rows = $cartoonState['blocks']; }
        if (!$rows) { continue; }
        $guide[] = [
            'slug' => $slug,
            'name' => (string)($channel['name'] ?? $slug),
            'icon' => (string)($channel['icon'] ?? '📺'),
            'access' => 'Free · Live library',
            'rows' => $rows,
        ];
    }
    return $guide;
}

function beyond_tv_guide_block(array $rows, int $hour): array {
    foreach ($rows as $row) {
        if ($hour >= (int)($row['start'] ?? 0) && $hour < (int)($row['end'] ?? 0)) { return $row; }
    }
    return $rows[0] ?? ['start'=>0,'end'=>24,'icon'=>'▶','title'=>'Beyond TV','lineup'=>'Curated library'];
}
