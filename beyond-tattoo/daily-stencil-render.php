<?php
declare(strict_types=1);
require_once __DIR__ . '/../includes/ecosystem.php';
require_once __DIR__ . '/includes/stencil-content.php';
header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store, max-age=0');
$stencil=bt_stencil_content();
$origin='https://'.($_SERVER['HTTP_HOST']??'beyondimagination.co.technology');
$absolute=static fn(string $path):string=>preg_match('~^https?://~i',$path)?$path:$origin.beyond_url('beyond-tattoo/'.ltrim($path,'/'));
echo json_encode([
  'mainArtwork'=>$absolute((string)$stencil['preview_url']),
  'studioTransfer'=>$absolute((string)$stencil['transfer_png_url']),
  'collectionName'=>$stencil['collection'],
  'stencilTitle'=>$stencil['title'],
  'date'=>$stencil['display_date'],
  'suggestedPlacement'=>$stencil['placement'],
  'downloadUrl'=>$stencil['public_url'],
  'caption'=>$stencil['description'],
  'audioFile'=>(string)($stencil['audio_file']??''),
  'showQrCode'=>true,
],JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES);

