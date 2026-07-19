<?php
declare(strict_types=1);
require __DIR__ . '/../../includes/admin-check.php';
require_once __DIR__ . '/../../../config/bootstrap.php';
header('Content-Type: application/json; charset=utf-8');
function out(int $status,array $data): void {http_response_code($status);echo json_encode($data,JSON_UNESCAPED_SLASHES);exit;}
if($_SERVER['REQUEST_METHOD']!=='POST')out(405,['ok'=>false,'error'=>'POST required.']);
$csrf=(string)($_SERVER['HTTP_X_CSRF_TOKEN']??'');
if(empty($_SESSION['verse_generator_csrf'])||!hash_equals((string)$_SESSION['verse_generator_csrf'],$csrf))out(419,['ok'=>false,'error'=>'Reload the page and try again.']);
$key=trim((string)beyond_config('narration.elevenlabs.api_key',''));
if($key==='')out(422,['ok'=>false,'error'=>'ElevenLabs API key is not configured.']);
if(!function_exists('curl_init'))out(500,['ok'=>false,'error'=>'PHP cURL is not enabled on this server.']);
$ch=curl_init('https://api.elevenlabs.io/v2/voices?page_size=50');
curl_setopt_array($ch,[CURLOPT_RETURNTRANSFER=>true,CURLOPT_CONNECTTIMEOUT=>10,CURLOPT_TIMEOUT=>25,CURLOPT_HTTPHEADER=>['xi-api-key: '.$key,'Accept: application/json']]);
$body=curl_exec($ch);$status=(int)curl_getinfo($ch,CURLINFO_HTTP_CODE);$err=curl_error($ch);curl_close($ch);
if($body===false)out(503,['ok'=>false,'error'=>'Could not connect to ElevenLabs: '.$err]);
$data=json_decode((string)$body,true);
if($status<200||$status>=300){$detail=is_array($data)?($data['detail']['message']??$data['detail']['status']??$data['message']??''):'';out($status,['ok'=>false,'error'=>'ElevenLabs connection failed'.($detail?' — '.$detail:'')]);}
$voices=[];foreach((array)($data['voices']??[]) as $voice){$id=trim((string)($voice['voice_id']??''));if($id==='')continue;$voices[]=['id'=>$id,'name'=>(string)($voice['name']??'Unnamed voice')];}
out(200,['ok'=>true,'message'=>'ElevenLabs connected. '.count($voices).' voice(s) available.','voices'=>$voices]);
