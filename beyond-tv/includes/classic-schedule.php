<?php
declare(strict_types=1);

/**
 * Channel 1 library-driven linear schedule.
 * Schedule chooses a franchise; library manager chooses the episode/source.
 * Progress is advanced once per Vancouver programme block, never once per viewer.
 */
function beyond_classic_libraries(): array
{
    return [
        'popeye' => [
            'name' => 'Popeye', 'icon' => '💪', 'episode_count' => 1,
            'sources' => [
                ['type'=>'video','id'=>'nSdz5ln2rME','label'=>'Popeye classics','start'=>0],
            ],
        ],
        'lucky-luke' => [
            'name' => 'Lucky Luke', 'icon' => '🤠', 'episode_count' => 10,
            'sources' => [
                ['type'=>'playlist','id'=>'PLQGZSV9PRCFmc11X9ec_FaoKdC3u23Cua','label'=>'Lucky Luke playlist'],
            ],
        ],
        'spider-man' => [
            'name' => 'Spider-Man', 'icon' => '🕷️', 'episode_count' => 10,
            'sources' => [
                ['type'=>'playlist','id'=>'PL_S5Mwou0NdrXq3z7PBxgPR4Q25YnKL9r','label'=>'Spider-Man playlist'],
            ],
        ],
        'batman' => [
            'name' => 'Batman', 'icon' => '🦇', 'episode_count' => 10,
            'sources' => [
                ['type'=>'playlist','id'=>'PLBJAZVFKjDq2vW8BovPYNyYNQTaJFhHn1','label'=>'Batman playlist'],
            ],
        ],
        'superman' => [
            'name' => 'Superman', 'icon' => '🦸', 'episode_count' => 10,
            'sources' => [
                ['type'=>'playlist','id'=>'PLhGipfv0juZWw5lM_NyhY1n32UXVSn37Q','label'=>'Superman playlist'],
                ['type'=>'video','id'=>'u92t2pNOoqM','label'=>'Superman feature / special','start'=>0],
            ],
        ],
        'mario' => [
            'name' => 'Channel 1 Classics', 'icon' => '🎞️', 'episode_count' => 1,
            'sources' => [
                ['type'=>'video','id'=>'nSdz5ln2rME','label'=>'Channel 1 classic fallback','start'=>0],
            ],
        ],
    ];
}

function beyond_classic_blocks(): array
{
    return [
        ['start'=>0,  'end'=>3,  'library'=>'batman',      'title'=>'Batman After Dark'],
        ['start'=>3,  'end'=>6,  'library'=>'popeye',      'title'=>'Popeye Classics'],
        ['start'=>6,  'end'=>9,  'library'=>'popeye',      'title'=>'Morning Cartoon Classics'],
        ['start'=>9,  'end'=>12, 'library'=>'spider-man',  'title'=>'Spider-Man Classics'],
        ['start'=>12, 'end'=>15, 'library'=>'lucky-luke',  'title'=>'Lucky Luke'],
        ['start'=>15, 'end'=>18, 'library'=>'mario',       'title'=>'Channel 1 Classics'],
        ['start'=>18, 'end'=>21, 'library'=>'superman',    'title'=>'Superman Prime Time'],
        ['start'=>21, 'end'=>24, 'library'=>'mario',       'title'=>'Channel 1 Classics'],
    ];
}

function beyond_classic_state_file(): string
{
    $root = dirname(__DIR__, 3);
    $dir = $root . '/var/tv';
    if (!is_dir($dir)) @mkdir($dir, 0775, true);
    return $dir . '/channel-1-library-state.json';
}

function beyond_classic_load_progress(): array
{
    $file = beyond_classic_state_file();
    if (!is_file($file)) return ['libraries'=>[], 'processed_blocks'=>[]];
    $decoded = json_decode((string)@file_get_contents($file), true);
    return is_array($decoded) ? array_merge(['libraries'=>[], 'processed_blocks'=>[]], $decoded) : ['libraries'=>[], 'processed_blocks'=>[]];
}

function beyond_classic_save_progress(array $state): void
{
    $file = beyond_classic_state_file();
    $state['updated_at'] = gmdate(DATE_ATOM);
    $state['processed_blocks'] = array_slice((array)($state['processed_blocks'] ?? []), -180, null, true);
    $json = json_encode($state, JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE);
    if ($json === false) return;
    $tmp = $file . '.tmp';
    if (@file_put_contents($tmp, $json, LOCK_EX) !== false) @rename($tmp, $file);
}

function beyond_classic_embed(array $source, int $episodeIndex): string
{
    $base = 'https://www.youtube-nocookie.com/embed/';
    $params = ['autoplay'=>1,'mute'=>1,'controls'=>1,'rel'=>0,'playsinline'=>1,'modestbranding'=>1,'enablejsapi'=>1];
    if (($source['type'] ?? '') === 'playlist') {
        $params['list'] = preg_replace('/[^A-Za-z0-9_-]/', '', (string)$source['id']);
        $params['index'] = max(0, $episodeIndex);
        return $base . 'videoseries?' . http_build_query($params);
    }
    $id = preg_replace('/[^A-Za-z0-9_-]/', '', (string)($source['id'] ?? ''));
    if (!empty($source['start'])) $params['start'] = max(0, (int)$source['start']);
    return $base . $id . '?' . http_build_query($params);
}

function beyond_classic_schedule_state(?DateTimeImmutable $now = null): array
{
    $tz = new DateTimeZone('America/Vancouver');
    $now = ($now ?? new DateTimeImmutable('now', $tz))->setTimezone($tz);
    $hour = (int)$now->format('G');
    $blocks = beyond_classic_blocks();
    $libraries = beyond_classic_libraries();
    $currentIndex = 0;
    foreach ($blocks as $i=>$block) if ($hour >= $block['start'] && $hour < $block['end']) { $currentIndex=$i; break; }
    $current = $blocks[$currentIndex];
    $next = $blocks[($currentIndex+1)%count($blocks)];
    $libraryKey = $current['library'];
    $library = $libraries[$libraryKey];
    $start = $now->setTime((int)$current['start'],0,0);
    $end = $current['end']===24 ? $now->modify('+1 day')->setTime(0,0,0) : $now->setTime((int)$current['end'],0,0);
    $blockKey = $start->format('Y-m-d-H') . '-' . $libraryKey;

    $progress = beyond_classic_load_progress();
    $entry = array_merge(['source'=>0,'episode'=>-1,'previous_valid'=>null], (array)($progress['libraries'][$libraryKey] ?? []));
    if (empty($progress['processed_blocks'][$blockKey])) {
        $count = max(1, (int)($library['episode_count'] ?? 1));
        $entry['episode'] = ((int)$entry['episode'] + 1) % $count;
        $progress['libraries'][$libraryKey] = $entry;
        $progress['processed_blocks'][$blockKey] = $now->format(DATE_ATOM);
        beyond_classic_save_progress($progress);
    }
    $sourceIndex = min(max(0,(int)$entry['source']), count($library['sources'])-1);
    $fallbacks = [];
    foreach ($library['sources'] as $i=>$source) {
        $fallbacks[] = [
            'source_index'=>$i,
            'label'=>(string)$source['label'],
            'embed_url'=>beyond_classic_embed($source, (int)$entry['episode']),
        ];
    }
    if (is_array($entry['previous_valid']) && !empty($entry['previous_valid']['embed_url'])) {
        $fallbacks[] = ['source_index'=>'previous','label'=>'Previous valid episode','embed_url'=>(string)$entry['previous_valid']['embed_url']];
    }
    $fallbacks[] = ['source_index'=>'intermission','label'=>'Beyond TV intermission','embed_url'=>'/beyond-tv/intermission.php?channel=1'];
    $active = $fallbacks[$sourceIndex] ?? $fallbacks[0];

    $current['icon']=$library['icon'];
    $current['lineup']=$library['name'].' · Episode '.((int)$entry['episode']+1);
    $current['library_name']=$library['name'];
    $current['episode_number']=(int)$entry['episode']+1;
    $nextLib=$libraries[$next['library']];
    $next['icon']=$nextLib['icon']; $next['lineup']=$nextLib['name'];

    return [
        'timezone'=>'America/Vancouver','timezone_label'=>$now->format('T'),'local_time'=>$now->format(DATE_ATOM),
        'date_label'=>$now->format('l, F j'),'time_label'=>$now->format('g:i A'),
        'current'=>$current,'next'=>$next,'block_key'=>$blockKey,'block_started_at'=>$start->format(DATE_ATOM),
        'block_ends_at'=>$end->format(DATE_ATOM),'seconds_into_block'=>max(0,$now->getTimestamp()-$start->getTimestamp()),
        'seconds_remaining'=>max(0,$end->getTimestamp()-$now->getTimestamp()),
        'library_key'=>$libraryKey,'episode_index'=>(int)$entry['episode'],'episode_number'=>(int)$entry['episode']+1,
        'source_index'=>$sourceIndex,'source_label'=>$active['label'],'embed_url'=>$active['embed_url'],
        'fallbacks'=>$fallbacks,'blocks'=>array_map(static function(array $block) use ($libraries): array { $lib=$libraries[$block['library']]; $block['icon']=$lib['icon']; $block['lineup']=$lib['name'].' · library rotation'; return $block; }, $blocks),
    ];
}
