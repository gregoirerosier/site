<?php
declare(strict_types=1);
header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: public, max-age=300, stale-while-revalidate=3600');

$slug = preg_replace('/[^a-z0-9-]/', '', strtolower((string)($_GET['slug'] ?? '')));
$channels = [
    'vintage-cartoon-theater' => [
        'name' => 'Classic Cartoon Theater',
        'items' => [
            ['archive' => 'SnowWhiteWithBettyBoop1933', 'title' => 'Snow White (1933)', 'duration' => 397],
            ['url' => "https://commons.wikimedia.org/wiki/Special:Redirect/file/Betty_Boop%27s_Rise_to_Fame_%281934%29.webm", 'title' => "Betty Boop's Rise to Fame (1934)", 'duration' => 530],
            ['url' => 'https://commons.wikimedia.org/wiki/Special:Redirect/file/Betty_Boop_-_Poor_Cinderella_%281934%29_-_HD.webm', 'title' => 'Poor Cinderella (1934)', 'duration' => 620],
        ],
        'embed' => 'https://archive.org/embed/SnowWhiteWithBettyBoop1933',
    ],
    'saturday-morning-cartoons' => [
        'name' => 'Saturday Morning Cartoons',
        'items' => [
            ['url' => 'https://commons.wikimedia.org/wiki/Special:Redirect/file/Betty_Boop_-_Poor_Cinderella_%281934%29_-_HD.webm', 'title' => 'Poor Cinderella (1934)', 'duration' => 620],
            ['url' => "https://commons.wikimedia.org/wiki/Special:Redirect/file/Betty_Boop%27s_Rise_to_Fame_%281934%29.webm", 'title' => "Betty Boop's Rise to Fame (1934)", 'duration' => 530],
        ],
        'embed' => 'https://archive.org/embed/SnowWhiteWithBettyBoop1933',
    ],
    'classic-cinema' => [
        'name' => 'Beyond Movies',
        'items' => [
            ['archive' => 'HisGirlFriday1940_201505', 'title' => 'His Girl Friday (1940)', 'duration' => 5520],
            ['url' => 'https://commons.wikimedia.org/wiki/Special:Redirect/file/His_Girl_Friday.webm', 'title' => 'His Girl Friday — Commons edition', 'duration' => 5520],
        ],
        'embed' => 'https://www.youtube-nocookie.com/embed/videoseries?list=PLdk1SI29-q9yrN9GFMnOAYmC_tcw5v59L',
    ],
    'midnight-movies' => [
        'name' => 'Midnight Movies',
        'items' => [
            ['url' => 'https://commons.wikimedia.org/wiki/Special:Redirect/file/Plan_9_from_Outer_Space_%281959%29.webm', 'title' => 'Plan 9 from Outer Space (1959)', 'duration' => 4740],
            ['archive' => 'TheLittleShopOfHorrors1960_765', 'title' => 'The Little Shop of Horrors (1960)', 'duration' => 4289],
        ],
        'embed' => 'https://archive.org/embed/TheLittleShopOfHorrors1960_765',
    ],
    'silent-film-theater' => [
        'name' => 'Silent Film Theater',
        'items' => [
            ['url' => 'https://commons.wikimedia.org/wiki/Special:Redirect/file/The_General_%281926%29.webm', 'title' => 'The General (1926)', 'duration' => 4552],
            ['archive' => 'The_General_Buster_Keaton', 'title' => 'The General — archive edition', 'duration' => 4552],
        ],
        'embed' => 'https://archive.org/embed/The_General_Buster_Keaton',
    ],
    'family-movie-matinee' => [
        'name' => 'Family Movie Matinee',
        'items' => [
            ['archive' => 'SnowWhiteWithBettyBoop1933', 'title' => 'Vintage Animation Matinee', 'duration' => 397],
            ['url' => 'https://commons.wikimedia.org/wiki/Special:Redirect/file/The_General_%281926%29.webm', 'title' => 'The General (1926)', 'duration' => 4552],
        ],
        'embed' => 'https://archive.org/embed/The_General_Buster_Keaton',
    ],

    'kreyol-lakay' => [
        'name' => 'Kreyòl Lakay',
        'items' => [
            [
                'url' => 'https://commons.wikimedia.org/wiki/Special:Redirect/file/WIKITONGUES-_Castelline_speaking_Haitian_Creole.webm',
                'title' => 'Castelline Speaking Haitian Creole',
                'duration' => 238,
                'creator' => 'Wikitongues and Casteline Titus',
                'license' => 'CC BY-SA 4.0',
                'rights_url' => 'https://commons.wikimedia.org/wiki/File:WIKITONGUES-_Castelline_speaking_Haitian_Creole.webm',
            ],
            [
                'url' => 'https://commons.wikimedia.org/wiki/Special:Redirect/file/VOA_Creole_Chief_Reacts_to_Kenyan_Police_Moving_to_Haiti.webm',
                'title' => 'VOA Creole: Haitian Voices',
                'duration' => 89,
                'creator' => 'Voice of America',
                'license' => 'U.S. public domain (VOA)',
                'rights_url' => 'https://commons.wikimedia.org/wiki/File:VOA_Creole_Chief_Reacts_to_Kenyan_Police_Moving_to_Haiti.webm',
            ],
        ],
        'embed' => 'https://commons.wikimedia.org/wiki/File:WIKITONGUES-_Castelline_speaking_Haitian_Creole.webm',
    ],
    'ayiti-caribbean' => [
        'name' => 'Ayiti & Caribbean Culture',
        'items' => [
            [
                'url' => 'https://commons.wikimedia.org/wiki/Special:Redirect/file/Merengue_Haitian,_Havana,_2012.webm',
                'title' => 'Haitian Méringue, Havana (2012)',
                'duration' => 167,
                'creator' => 'Nastya Yagushchenko',
                'license' => 'CC BY 3.0',
                'rights_url' => 'https://commons.wikimedia.org/wiki/File:Merengue_Haitian,_Havana,_2012.webm',
            ],
            [
                'url' => 'https://commons.wikimedia.org/wiki/Special:Redirect/file/WIKITONGUES-_Castelline_speaking_Haitian_Creole.webm',
                'title' => 'Haitian Creole Language & Culture',
                'duration' => 238,
                'creator' => 'Wikitongues and Casteline Titus',
                'license' => 'CC BY-SA 4.0',
                'rights_url' => 'https://commons.wikimedia.org/wiki/File:WIKITONGUES-_Castelline_speaking_Haitian_Creole.webm',
            ],
        ],
        'embed' => 'https://commons.wikimedia.org/wiki/File:Merengue_Haitian,_Havana,_2012.webm',
    ],
    'cinema-francais-classique' => [
        'name' => 'Cinéma Français Classique',
        'items' => [
            [
                'url' => 'https://commons.wikimedia.org/wiki/Special:Redirect/file/Cinderella_(1899_film).webm',
                'title' => 'Cendrillon / Cinderella (1899)',
                'duration' => 341,
                'creator' => 'Georges Méliès',
                'license' => 'Public domain',
                'rights_url' => 'https://commons.wikimedia.org/wiki/File:Cinderella_(1899_film).webm',
            ],
            [
                'url' => 'https://commons.wikimedia.org/wiki/Special:Redirect/file/1896-melies-une-partie-de-cartes.webm',
                'title' => 'Une partie de cartes (1896)',
                'duration' => 69,
                'creator' => 'Georges Méliès',
                'license' => 'Public domain',
                'rights_url' => 'https://commons.wikimedia.org/wiki/File:1896-melies-une-partie-de-cartes.webm',
            ],
            [
                'url' => 'https://commons.wikimedia.org/wiki/Special:Redirect/file/Le_Cauchemar,_1896,_Méliès.webm',
                'title' => 'Le Cauchemar (1896)',
                'duration' => 65,
                'creator' => 'Georges Méliès',
                'license' => 'Public domain',
                'rights_url' => 'https://commons.wikimedia.org/wiki/File:Le_Cauchemar,_1896,_Méliès.webm',
            ],
        ],
        'embed' => 'https://commons.wikimedia.org/wiki/File:Cinderella_(1899_film).webm',
    ],

];
if (!isset($channels[$slug])) { http_response_code(404); echo json_encode(['ok'=>false,'error'=>'Unknown channel']); exit; }

function fetch_json(string $url): ?array {
    $body = null;
    if (function_exists('curl_init')) {
        $ch = curl_init($url);
        curl_setopt_array($ch,[CURLOPT_RETURNTRANSFER=>true,CURLOPT_FOLLOWLOCATION=>true,CURLOPT_CONNECTTIMEOUT=>3,CURLOPT_TIMEOUT=>7,CURLOPT_USERAGENT=>'BeyondTV/2.1']);
        $result=curl_exec($ch); $status=(int)curl_getinfo($ch,CURLINFO_RESPONSE_CODE); curl_close($ch);
        if(is_string($result)&&$status>=200&&$status<300)$body=$result;
    }
    if($body===null&&filter_var(ini_get('allow_url_fopen'),FILTER_VALIDATE_BOOLEAN)){
        $result=@file_get_contents($url,false,stream_context_create(['http'=>['timeout'=>7,'user_agent'=>'BeyondTV/2.1']]));
        if(is_string($result))$body=$result;
    }
    $decoded=$body!==null?json_decode($body,true):null; return is_array($decoded)?$decoded:null;
}
function archive_url(string $id,string $file): string { return 'https://archive.org/download/'.rawurlencode($id).'/'.implode('/',array_map('rawurlencode',explode('/',$file))); }
function resolve_archive(string $id): ?string {
    $metadata=fetch_json('https://archive.org/metadata/'.rawurlencode($id));
    if(!$metadata||!is_array($metadata['files']??null))return null;
    $c=[]; foreach($metadata['files'] as $f){ if(!is_array($f))continue; $n=(string)($f['name']??''); $fmt=strtolower((string)($f['format']??''));
        if(!preg_match('/\.(mp4|m4v|webm)$/i',$n)||str_contains($fmt,'thumbnail')||str_contains(strtolower($n),'thumb'))continue;
        $score=(int)($f['size']??0); if(preg_match('/\.mp4$/i',$n))$score+=2000000000; $c[]=['n'=>$n,'s'=>$score]; }
    usort($c,fn($a,$b)=>$b['s']<=>$a['s']); return $c?archive_url($id,$c[0]['n']):null;
}
$config=$channels[$slug]; $resolved=[];
foreach($config['items'] as $item){ $url=$item['url']??null; if(!$url&&!empty($item['archive']))$url=resolve_archive($item['archive']); if(!$url)continue;
    $resolved[]=['provider'=>!empty($item['archive'])?'Internet Archive':'Wikimedia Commons','title'=>$item['title'],'url'=>$url,'duration'=>(int)$item['duration'],'type'=>str_contains($url,'.webm')?'video/webm':'video/mp4','creator'=>(string)($item['creator']??''),'license'=>(string)($item['license']??''),'rights_url'=>(string)($item['rights_url']??'')]; }
$total=array_sum(array_column($resolved,'duration')); $position=$total>0?time()%$total:0; $current=0; $offset=0;
foreach($resolved as $i=>$item){ if($position<$item['duration']){$current=$i;$offset=$position;break;} $position-=$item['duration']; }
$ordered=$resolved; if($resolved){$ordered=array_merge(array_slice($resolved,$current),array_slice($resolved,0,$current));}
echo json_encode(['ok'=>true,'channel'=>['slug'=>$slug,'name'=>$config['name'],'mode'=>'pseudo-live','programme'=>$resolved[$current]['title']??'Unavailable'],'sources'=>$ordered,'start_offset'=>$offset,'playlist_duration'=>$total,'server_time'=>time(),'embed_fallback'=>$config['embed']],JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE);
