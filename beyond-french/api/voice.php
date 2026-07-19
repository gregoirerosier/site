<?php
declare(strict_types=1);
require_once __DIR__ . '/../includes/functions.php';
header('Content-Type: application/json; charset=utf-8');
if ($_SERVER['REQUEST_METHOD'] !== 'POST') { http_response_code(405); echo json_encode(['error'=>'Method not allowed']); exit; }
$payload=json_decode((string)file_get_contents('php://input'), true);
$text=trim((string)($payload['text']??'')); $locale=(string)($payload['locale']??'');
$allowed=['fr-FR','es-ES','ht-HT','en-JM'];
if ($text==='' || mb_strlen($text)>300 || !in_array($locale,$allowed,true)) { http_response_code(422); echo json_encode(['error'=>'Invalid request']); exit; }
$apiKey=(string)beyond_config('voice.api_key',''); $voiceId=(string)beyond_config('voice.voices.'.$locale,'');
if ($apiKey==='' || $voiceId==='') { http_response_code(503); echo json_encode(['error'=>'API voice is not configured for this language']); exit; }
$cacheDir=beyond_private_root().'/cache/voices'; if(!is_dir($cacheDir)) mkdir($cacheDir,0700,true);
$key=hash('sha256',$locale.'|'.$voiceId.'|'.$text); $cache=$cacheDir.'/'.$key.'.mp3';
if(!is_file($cache)) {
    $body=['text'=>$text,'model_id'=>(string)beyond_config('voice.model_id','eleven_v3')];
    if(in_array($locale,['fr-FR','es-ES'],true)) $body['language_code']=substr($locale,0,2);
    $ch=curl_init('https://api.elevenlabs.io/v1/text-to-speech/'.rawurlencode($voiceId));
    curl_setopt_array($ch,[CURLOPT_POST=>true,CURLOPT_RETURNTRANSFER=>true,CURLOPT_TIMEOUT=>25,CURLOPT_HTTPHEADER=>['Content-Type: application/json','xi-api-key: '.$apiKey,'Accept: audio/mpeg'],CURLOPT_POSTFIELDS=>json_encode($body)]);
    $audio=curl_exec($ch); $status=(int)curl_getinfo($ch,CURLINFO_HTTP_CODE); $err=curl_error($ch); curl_close($ch);
    if($status<200 || $status>=300 || !is_string($audio) || $audio==='') { error_log('Voice API failed: '.$status.' '.$err); http_response_code(502); echo json_encode(['error'=>'Voice service unavailable']); exit; }
    file_put_contents($cache,$audio,LOCK_EX);
}
header('Content-Type: audio/mpeg'); header('Cache-Control: public, max-age=86400'); readfile($cache);
