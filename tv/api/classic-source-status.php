<?php
declare(strict_types=1);
header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store');
require_once __DIR__ . '/../includes/classic-schedule.php';
$input=json_decode((string)file_get_contents('php://input'),true); if(!is_array($input))$input=$_POST;
$library=preg_replace('/[^a-z0-9-]/','',strtolower((string)($input['library']??'')));
$source=max(0,(int)($input['source_index']??0)); $embed=(string)($input['embed_url']??'');
$libs=beyond_classic_libraries(); if(!isset($libs[$library])){http_response_code(400);echo json_encode(['ok'=>false]);exit;}
$progress=beyond_classic_load_progress(); $entry=array_merge(['source'=>0,'episode'=>0,'previous_valid'=>null],(array)($progress['libraries'][$library]??[]));
if(($input['status']??'')==='valid'){
  $entry['source']=$source; $entry['previous_valid']=['embed_url'=>$embed,'saved_at'=>gmdate(DATE_ATOM)];
}else{
  $entry['source']=min($source+1,max(0,count($libs[$library]['sources'])-1));
}
$progress['libraries'][$library]=$entry; beyond_classic_save_progress($progress);
echo json_encode(['ok'=>true,'source_index'=>$entry['source']]);
