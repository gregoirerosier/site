<?php
declare(strict_types=1);

/** Shared Yu-Gi-Oh! TV clock. All viewers resolve against server time. */
function beyond_yugioh_live_state(?int $serverNow = null): array
{
    $playlistId = 'PLXBcsPKqNstB10447aKbDnkPEJdTV9sj-';
    $episodeCount = 49;
    $episodeDuration = 24 * 60;
    $timezone = new DateTimeZone('America/Vancouver');
    $start = new DateTimeImmutable('2026-07-16 00:00:00', $timezone);
    $now = $serverNow ?? time();
    $cycleDuration = $episodeCount * $episodeDuration;
    $elapsed = max(0, $now - $start->getTimestamp());
    $cyclePosition = $cycleDuration > 0 ? $elapsed % $cycleDuration : 0;
    $index = intdiv($cyclePosition, $episodeDuration);
    $offset = $cyclePosition % $episodeDuration;
    $remaining = $episodeDuration - $offset;

    return [
        'playlist_id' => $playlistId,
        'episode_count' => $episodeCount,
        'episode_duration' => $episodeDuration,
        'episode_index' => $index,
        'episode_number' => $index + 1,
        'start_offset' => $offset,
        'remaining' => $remaining,
        'next_episode_number' => (($index + 1) % $episodeCount) + 1,
        'server_time' => $now,
        'timezone' => 'America/Vancouver',
        'channel_start' => $start->format(DATE_ATOM),
        'embed_url' => 'https://www.youtube-nocookie.com/embed/videoseries?list=' . rawurlencode($playlistId)
            . '&index=' . $index . '&start=' . $offset
            . '&autoplay=1&mute=1&controls=1&rel=0&playsinline=1',
    ];
}
