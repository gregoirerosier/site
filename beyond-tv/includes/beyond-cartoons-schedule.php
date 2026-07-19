<?php
declare(strict_types=1);

/** Channel 2 confirmed-source scheduler.
 * Rotates the starting episode/source daily and keeps a fallback chain.
 */
function beyond_cartoons_schedule_state(?DateTimeImmutable $now = null): array
{
    $tz = new DateTimeZone('America/Vancouver');
    $now = ($now ?? new DateTimeImmutable('now', $tz))->setTimezone($tz);
    $hour = (int)$now->format('G');
    $dayIndex = (int)$now->format('z');

    $libraries = [
        'mr-bean' => ['title'=>'Mr. Bean: The Animated Series','id'=>'PLUJhKGzDNqVVdzmYhoY9xZyQGoNLwukQq','duration'=>660,'count'=>18],
        'courage' => ['title'=>'Courage the Cowardly Dog','id'=>'PLToQtMHGzmM0TTzBSVOfxrU-pgAZm6nlK','duration'=>1320,'count'=>13],
        'adventure-time' => ['title'=>'Adventure Time','id'=>'PLjW1ObFDddxNu8bA8-J_FYh-_S6v270PW','duration'=>660,'count'=>26],
        'yugioh' => ['title'=>'Yu-Gi-Oh! Season 1','id'=>'PLXBcsPKqNstD_1Km3OzyynMJdcJSQEmiz','duration'=>1440,'count'=>49],
        'zatch-bell' => ['title'=>'Zatch Bell! Season 1','type'=>'archive','id'=>'zatch-bell-collection','duration'=>1320,'count'=>50,'template'=>'Zatch Bell/Season 1/Zatch Bell S01E%02d.mp4'],
    ];

    $blocks = [
        ['start'=>9,'end'=>12,'title'=>'Mamodo Morning','lineup'=>'Zatch Bell! Season 1','icon'=>'⚡','library'=>'zatch-bell'],
        ['start'=>18,'end'=>21,'title'=>'After-School Anime','lineup'=>'Zatch Bell! Season 1','icon'=>'⚡','library'=>'zatch-bell'],
        ['start'=>0,'end'=>3,'title'=>'Cartoon Comedy','lineup'=>'Mr. Bean: The Animated Series','icon'=>'🧸','library'=>'mr-bean'],
        ['start'=>3,'end'=>6,'title'=>'Courage Overnight','lineup'=>'Courage the Cowardly Dog','icon'=>'🐶','library'=>'courage'],
        ['start'=>6,'end'=>9,'title'=>'Morning Adventure','lineup'=>'Adventure Time','icon'=>'🗡️','library'=>'adventure-time'],
        ['start'=>9,'end'=>12,'title'=>'Duel Monsters','lineup'=>'Yu-Gi-Oh! Season 1','icon'=>'🃏','library'=>'yugioh'],
        ['start'=>12,'end'=>15,'title'=>'Lunch Laughs','lineup'=>'Mr. Bean: The Animated Series','icon'=>'🥪','library'=>'mr-bean'],
        ['start'=>15,'end'=>18,'title'=>'Adventure Hour','lineup'=>'Adventure Time','icon'=>'🌈','library'=>'adventure-time'],
        ['start'=>18,'end'=>21,'title'=>'After-School Anime','lineup'=>'Yu-Gi-Oh! Season 1','icon'=>'🎴','library'=>'yugioh'],
        ['start'=>21,'end'=>24,'title'=>'Spooky Prime Time','lineup'=>'Courage the Cowardly Dog','icon'=>'🌙','library'=>'courage'],
    ];

    $uniqueBlocks = [];
    foreach ($blocks as $block) { $uniqueBlocks[(int)$block['start']] ??= $block; }
    ksort($uniqueBlocks);
    $blocks = array_values($uniqueBlocks);

    $index=0;
    foreach($blocks as $i=>$block){ if($hour >= $block['start'] && $hour < $block['end']){$index=$i;break;} }
    $current=$blocks[$index]; $next=$blocks[($index+1)%count($blocks)];
    $source=$libraries[$current['library']];
    $start=$now->setTime((int)$current['start'],0,0);
    $elapsed=max(0,$now->getTimestamp()-$start->getTimestamp());
    $dailyStart=($dayIndex + ($index * 3)) % max(1,(int)$source['count']);
    $episodeOffset=(int)floor($elapsed/max(1,(int)$source['duration']));
    $episodeIndex=($dailyStart+$episodeOffset)%max(1,(int)$source['count']);
    $startOffset=$elapsed%max(1,(int)$source['duration']);

    if (($source['type'] ?? 'youtube') === 'archive') {
        $archiveFile = sprintf((string)$source['template'], $episodeIndex + 1);
        $embed = 'https://archive.org/download/' . rawurlencode((string)$source['id']) . '/' . str_replace('%2F', '/', rawurlencode($archiveFile));
    } else {
        $query=['list'=>$source['id'],'index'=>$episodeIndex,'start'=>$startOffset,'autoplay'=>1,'mute'=>1,'controls'=>1,'rel'=>0,'playsinline'=>1];
        $embed='https://www.youtube-nocookie.com/embed/videoseries?'.http_build_query($query);
    }

    return [
        'timezone'=>'America/Vancouver','timezone_label'=>$now->format('T'),'time_label'=>$now->format('g:i A'),'date_label'=>$now->format('l, F j'),
        'current'=>$current,'next'=>$next,'blocks'=>$blocks,
        'playing'=>['type'=>(($source['type'] ?? 'youtube') === 'archive' ? 'archive_video' : 'playlist'),'id'=>$source['id'],'title'=>$source['title'],'index'=>$episodeIndex],
        'fallbacks'=>[
            ['type'=>'playlist','id'=>$source['id'],'title'=>$source['title']],
            ['type'=>'last_successful','library'=>$current['library']],
            ['type'=>'intermission','title'=>'Beyond TV intermission'],
        ],
        'start_offset'=>$startOffset,'embed_url'=>$embed,'server_time'=>$now->getTimestamp(),
    ];
}
