<?php
declare(strict_types=1);
if (PHP_SAPI !== 'cli') { http_response_code(404); exit; }
require_once __DIR__ . '/../config/bootstrap.php';
require_once __DIR__ . '/../beyond-tattoo/includes/stencil-content.php';
require_once __DIR__ . '/../beyond-tattoo/includes/stencil-package.php';

$sourceFile = $argv[1] ?? (__DIR__ . '/../beyond-tattoo/config/stencil-day.php');
if (!is_file($sourceFile)) { fwrite(STDERR, "Stencil source not found.\n"); exit(1); }
$stencil = require $sourceFile;
if (!is_array($stencil)) { fwrite(STDERR, "Stencil source is invalid.\n"); exit(1); }
foreach (['slug','title','collection','display_date','iso_date','preview_url','editable_url','transfer_png_url','transfer_pdf_url','placement_guide_url','package_files'] as $field) {
    if (!isset($stencil[$field]) || $stencil[$field] === '' || $stencil[$field] === []) { fwrite(STDERR, "Missing required field: {$field}\n"); exit(1); }
}
try {
    $package = bt_stencil_package($stencil, true);
    $videoPublic = __DIR__ . '/daily-stencil-video/public';
    $videoStencilDir = $videoPublic . '/stencils';
    if (!is_dir($videoStencilDir) && !mkdir($videoStencilDir, 0775, true) && !is_dir($videoStencilDir)) throw new RuntimeException('Could not create the video stencil directory.');
    if (!copy(bt_stencil_asset_path((string)$stencil['preview_url']), $videoStencilDir . '/main-stencil.webp')) throw new RuntimeException('Could not publish the video preview artwork.');
    if (!copy(bt_stencil_asset_path((string)$stencil['transfer_png_url']), $videoStencilDir . '/studio-transfer.png')) throw new RuntimeException('Could not publish the video transfer artwork.');
    $videoManifest = [
        'mainArtwork'=>'stencils/main-stencil.webp','studioTransfer'=>'stencils/studio-transfer.png',
        'collectionName'=>$stencil['collection'],'stencilTitle'=>$stencil['title'],'date'=>$stencil['display_date'],
        'suggestedPlacement'=>$stencil['placement'],'downloadUrl'=>$stencil['public_url'],'caption'=>$stencil['description'],
        'audioFile'=>(string)($stencil['audio_file']??''),'showQrCode'=>true,
    ];
    $videoJson=json_encode($videoManifest,JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES);
    if($videoJson===false||file_put_contents($videoPublic.'/daily-stencil.json',$videoJson."\n",LOCK_EX)===false)throw new RuntimeException('Could not publish the video manifest.');
    bt_stencil_save($stencil);
    fwrite(STDOUT, "Published {$stencil['title']} ({$stencil['iso_date']})\nPackage: {$package}\nManifest: " . bt_stencil_data_file() . "\n");
} catch (Throwable $e) { fwrite(STDERR, $e->getMessage() . "\n"); exit(1); }
