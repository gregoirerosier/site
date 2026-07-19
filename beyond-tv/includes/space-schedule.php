<?php
declare(strict_types=1);

function beyond_space_schedule_state(?DateTimeImmutable $now = null): array
{
    $tz = new DateTimeZone('America/Vancouver');
    $now = ($now ?? new DateTimeImmutable('now', $tz))->setTimezone($tz);
    $hour = (int)$now->format('G');

    $blocks = [
        ['start'=>0,'end'=>3,'title'=>'Milky Way Overnight','lineup'=>'Sleep-time Milky Way ambience and calming galaxy visuals','icon'=>'🌌','sources'=>[
            ['type'=>'video','id'=>'iaYWxdS578A','title'=>'Milky Way Sleep Time','duration'=>10800],
        ]],
        ['start'=>3,'end'=>6,'title'=>'The Sun in 4K','lineup'=>'The deepest views into the Sun · SDO 4K','icon'=>'☀️','sources'=>[
            ['type'=>'video','id'=>'6EbuAEagQj4','title'=>'The Deepest We Have Ever Seen Into the Sun','duration'=>10800],
        ]],
        ['start'=>6,'end'=>9,'title'=>'Sun 101','lineup'=>'A clear introduction to our nearest star','icon'=>'🌅','sources'=>[
            ['type'=>'video','id'=>'2HoTK_Gqi2Q','title'=>'Sun 101','duration'=>10800],
        ]],
        ['start'=>9,'end'=>12,'title'=>'Milky Way Explorer','lineup'=>'Stars, structure and the galaxy we call home','icon'=>'✨','sources'=>[
            ['type'=>'video','id'=>'Yla5i5tzXKE','title'=>'Milky Way Explorer','duration'=>10800],
        ]],
        ['start'=>12,'end'=>15,'title'=>'Solar Activity','lineup'=>'How activity on the Sun shapes our world','icon'=>'🌞','sources'=>[
            ['type'=>'video','id'=>'TTn3kmLUFrY','title'=>'How Solar Activity Shapes Our World','duration'=>10800],
        ]],
        ['start'=>15,'end'=>18,'title'=>'Journey Through the Milky Way','lineup'=>'Galactic structure, stars and cosmic discovery','icon'=>'🌠','sources'=>[
            ['type'=>'video','id'=>'Yla5i5tzXKE','title'=>'Journey Through the Milky Way','duration'=>10800],
        ]],
        ['start'=>18,'end'=>21,'title'=>'Sun Prime Time','lineup'=>'Solar Activity · Sun 101 · SDO 4K','icon'=>'⭐','sources'=>[
            ['type'=>'video','id'=>'TTn3kmLUFrY','title'=>'How Solar Activity Shapes Our World','duration'=>3600],
            ['type'=>'video','id'=>'2HoTK_Gqi2Q','title'=>'Sun 101','duration'=>3600],
            ['type'=>'video','id'=>'6EbuAEagQj4','title'=>'The Sun in SDO 4K','duration'=>3600],
        ]],
        ['start'=>21,'end'=>24,'title'=>'Galactic Night','lineup'=>'Sleep-time Milky Way ambience for winding down','icon'=>'🔭','sources'=>[
            ['type'=>'video','id'=>'iaYWxdS578A','title'=>'Milky Way Sleep Time','duration'=>10800],
        ]],
    ];

    $index = 0;
    foreach ($blocks as $i => $block) {
        if ($hour >= $block['start'] && $hour < $block['end']) { $index = $i; break; }
    }
    $current = $blocks[$index];
    $next = $blocks[($index + 1) % count($blocks)];
    $start = $now->setTime((int)$current['start'], 0, 0);
    $elapsed = max(0, $now->getTimestamp() - $start->getTimestamp());

    $timeline = $current['sources'];
    $total = 0;
    foreach ($timeline as $item) { $total += (int)$item['duration']; }
    $position = $total > 0 ? $elapsed % $total : 0;
    $playing = $timeline[0];
    $offset = 0;
    foreach ($timeline as $item) {
        $duration = (int)$item['duration'];
        if ($position < $duration) { $playing = $item; $offset = $position; break; }
        $position -= $duration;
    }

    $embed = 'https://www.youtube-nocookie.com/embed/' . rawurlencode((string)$playing['id']) . '?' . http_build_query([
        'start'=>$offset,'autoplay'=>1,'mute'=>1,'controls'=>1,'rel'=>0,'playsinline'=>1,'enablejsapi'=>1
    ]);

    return [
        'timezone'=>'America/Vancouver','timezone_label'=>$now->format('T'),
        'time_label'=>$now->format('g:i A'),'date_label'=>$now->format('l, F j'),
        'current'=>$current,'next'=>$next,'blocks'=>$blocks,'playing'=>$playing,
        'start_offset'=>$offset,'embed_url'=>$embed,'server_time'=>$now->getTimestamp(),
    ];
}
