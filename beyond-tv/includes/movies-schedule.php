<?php
declare(strict_types=1);
function beyond_movies_schedule_state(?DateTimeImmutable $now=null): array {
    $tz=new DateTimeZone('America/Vancouver'); $now=$now?->setTimezone($tz)??new DateTimeImmutable('now',$tz);
    $movies=[
      1=>['title'=>'Cats','id'=>'QrnXZgFYMbk','genre'=>'Family · Adventure','runtime'=>'1 hr 30 min'],
      2=>['title'=>'Mud (2012)','id'=>'qbhV8m2wrZM','genre'=>'Drama','runtime'=>'2 hr 10 min'],
      3=>['title'=>'Never Back Down','id'=>'y886zL1bwQU','genre'=>'Action · Martial Arts','runtime'=>'Full movie'],
      4=>['title'=>'Big Stan','id'=>'sx8pViXxZQg','genre'=>'Comedy','runtime'=>'Full movie'],
      5=>['title'=>'In the Mix','id'=>'zhue70cwb7Y','genre'=>'Romantic Comedy','runtime'=>'Full movie'],
      6=>['title'=>"Don't Look Away",'id'=>'eDrk1ifu0g8','genre'=>'Horror','runtime'=>'Full movie'],
      7=>['title'=>'Zatch Bell! Movie 1: 101st Devil','type'=>'archive','url'=>'https://archive.org/download/zatch-bell-collection/Zatch%20Bell/Movies/Zatch%20Bell%20Movie%2001%20-%20%20Unlisted%20Demon%20101%20%28101st%20Devil%29.mp4','genre'=>'Anime · Adventure','runtime'=>'Feature film'],
      8=>['title'=>'Zatch Bell! Movie 2: Attack of the Mechavulcan','type'=>'archive','url'=>'https://archive.org/download/zatch-bell-collection/Zatch%20Bell/Movies/Zatch%20Bell%20Movie%2002%20-%20Attack%20of%20The%20Mechavulcan.mp4','genre'=>'Anime · Action','runtime'=>'Feature film'],
    ];
    $specials=['2026-07-17'=>['title'=>'Jeepers Creepers','id'=>'ROY1YDlYUNc','genre'=>'Horror · Special Presentation','runtime'=>'Full movie']];
    $day=(int)$now->format('N'); $date=$now->format('Y-m-d');
    if(isset($specials[$date])){$current=$specials[$date];$label="TODAY'S SPECIAL";$next=$movies[6];}
    elseif($day===7){
      $movieCount=count($movies); $slot=(int)floor(((int)$now->format('G')*60+(int)$now->format('i'))/(24*60/$movieCount));
      $slot=max(0,min($movieCount-1,$slot)); $current=$movies[$slot+1]; $label='SUNDAY MARATHON'; $next=$movies[(($slot+1)%$movieCount)+1];
    } else {$current=$movies[$day];$label='FEATURE OF THE DAY';$next=$day===6?$movies[1]:$movies[$day+1];}
    $embed=($current['type']??'youtube')==='archive' ? (string)$current['url'] : 'https://www.youtube-nocookie.com/embed/'.$current['id'].'?autoplay=1&mute=1&playsinline=1&rel=0&modestbranding=1';
    return ['current'=>$current,'next'=>$next,'label'=>$label,'embed_url'=>$embed,'movies'=>$movies,'is_marathon'=>$day===7,'date'=>$date];
}
