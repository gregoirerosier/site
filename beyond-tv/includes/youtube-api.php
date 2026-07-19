<?php
declare(strict_types=1);

function beyond_youtube_config(): array
{
    static $config;
    if (is_array($config)) return $config;
    $file = dirname(__DIR__, 2) . '/config/youtube.php';
    $loaded = is_file($file) ? require $file : [];
    $config = is_array($loaded) ? $loaded : [];
    return $config;
}

function beyond_youtube_keys(): array
{
    $keys = beyond_youtube_config()['api_keys'] ?? [];
    return array_values(array_filter(array_map('strval', is_array($keys) ? $keys : [])));
}

function beyond_youtube_extract_id(string $input): string
{
    $input = trim($input);
    if (preg_match('/^[A-Za-z0-9_-]{11}$/', $input)) return $input;
    $parts = parse_url($input);
    if (!is_array($parts)) return '';
    $host = strtolower((string)($parts['host'] ?? ''));
    $path = trim((string)($parts['path'] ?? ''), '/');
    if (in_array($host, ['youtu.be', 'www.youtu.be'], true)) {
        $candidate = explode('/', $path)[0] ?? '';
        return preg_match('/^[A-Za-z0-9_-]{11}$/', $candidate) ? $candidate : '';
    }
    if (str_contains($host, 'youtube.com')) {
        parse_str((string)($parts['query'] ?? ''), $query);
        $candidate = (string)($query['v'] ?? '');
        if (preg_match('/^[A-Za-z0-9_-]{11}$/', $candidate)) return $candidate;
        if (preg_match('#^(?:embed|shorts|live)/([A-Za-z0-9_-]{11})#', $path, $match)) return $match[1];
    }
    return '';
}

function beyond_youtube_http_get(string $url, int $timeout): array
{
    if (function_exists('curl_init')) {
        $curl = curl_init($url);
        curl_setopt_array($curl, [CURLOPT_RETURNTRANSFER=>true,CURLOPT_FOLLOWLOCATION=>true,CURLOPT_CONNECTTIMEOUT=>$timeout,CURLOPT_TIMEOUT=>$timeout,CURLOPT_HTTPHEADER=>['Accept: application/json'],CURLOPT_USERAGENT=>'BeyondTV/2.1']);
        $body = curl_exec($curl);
        $status = (int)curl_getinfo($curl, CURLINFO_HTTP_CODE);
        $error = curl_error($curl);
        curl_close($curl);
        return ['status'=>$status,'body'=>is_string($body)?$body:'','error'=>$error];
    }
    $context = stream_context_create(['http'=>['method'=>'GET','timeout'=>$timeout,'ignore_errors'=>true,'header'=>"Accept: application/json\r\nUser-Agent: BeyondTV/2.1\r\n"]]);
    $body = @file_get_contents($url, false, $context);
    $status = 0;
    foreach (($http_response_header ?? []) as $header) if (preg_match('#^HTTP/\S+\s+(\d+)#', $header, $match)) { $status=(int)$match[1]; break; }
    return ['status'=>$status,'body'=>is_string($body)?$body:'','error'=>$body===false?'HTTP request failed.':''];
}

function beyond_youtube_video(string $videoId): array
{
    if (!preg_match('/^[A-Za-z0-9_-]{11}$/', $videoId)) return ['ok'=>false,'error'=>'Enter a valid YouTube URL or 11-character video ID.'];
    $keys = beyond_youtube_keys();
    if (!$keys) return ['ok'=>false,'error'=>'No YouTube Data API key is configured.'];
    $timeout = max(2, (int)(beyond_youtube_config()['timeout_seconds'] ?? 8));
    $lastError = 'YouTube validation failed.';
    $start = abs(crc32($videoId . date('Y-m-d-H'))) % count($keys);
    for ($offset=0; $offset<count($keys); $offset++) {
        $index = ($start+$offset)%count($keys);
        $url = 'https://www.googleapis.com/youtube/v3/videos?' . http_build_query(['part'=>'snippet,status,contentDetails,liveStreamingDetails','id'=>$videoId,'key'=>$keys[$index]]);
        $response = beyond_youtube_http_get($url,$timeout);
        $payload = json_decode((string)$response['body'],true);
        if (($response['status']??0)===200 && is_array($payload)) {
            $item = $payload['items'][0] ?? null;
            if (!is_array($item)) return ['ok'=>false,'error'=>'That video is unavailable, private, deleted, or region-restricted.'];
            $status = $item['status'] ?? [];
            if (($status['embeddable'] ?? true)!==true) return ['ok'=>false,'error'=>'The rights holder has disabled embedding for this video.'];
            return ['ok'=>true,'key_index'=>$index,'video'=>[
                'id'=>$videoId,
                'title'=>(string)($item['snippet']['title']??''),
                'channel_title'=>(string)($item['snippet']['channelTitle']??''),
                'channel_id'=>(string)($item['snippet']['channelId']??''),
                'published_at'=>(string)($item['snippet']['publishedAt']??''),
                'thumbnail'=>(string)($item['snippet']['thumbnails']['high']['url']??$item['snippet']['thumbnails']['medium']['url']??''),
                'duration'=>(string)($item['contentDetails']['duration']??''),
                'live_status'=>(string)($item['snippet']['liveBroadcastContent']??'none'),
                'embeddable'=>(bool)($status['embeddable']??true),
                'privacy_status'=>(string)($status['privacyStatus']??''),
            ]];
        }
        $reason=(string)($payload['error']['errors'][0]['reason']??'');
        $message=(string)($payload['error']['message']??$response['error']??'');
        $lastError=$message!==''?$message:$lastError;
        if (!in_array($reason,['quotaExceeded','dailyLimitExceeded','keyInvalid','accessNotConfigured'],true)) break;
    }
    return ['ok'=>false,'error'=>$lastError];
}
