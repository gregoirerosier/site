<?php
declare(strict_types=1);
require __DIR__ . '/bootstrap.php';
require_once dirname(__DIR__, 3) . '/beyond-tattoo/includes/stencil-content.php';
header('Content-Type: application/json; charset=UTF-8');
try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') throw new RuntimeException('POST required.');
    if (!Auth::check()) { http_response_code(403); throw new RuntimeException('Administrator access required.'); }
    $csrf = is_string($_SERVER['HTTP_X_CSRF_TOKEN'] ?? null) ? $_SERVER['HTTP_X_CSRF_TOKEN'] : null;
    if (!Auth::verifyCsrf($csrf)) { http_response_code(403); throw new RuntimeException('Invalid security token.'); }
    $input = json_decode((string)file_get_contents('php://input'), true, 512, JSON_THROW_ON_ERROR);
    $title = trim((string)($input['title'] ?? 'Untitled Stencil'));
    $svg = (string)($input['svg'] ?? '');
    $pngData = (string)($input['png'] ?? '');
    if ($svg === '' || !str_contains($svg, '<svg')) throw new RuntimeException('Generated SVG is missing.');
    if (strlen($svg) > 2 * 1024 * 1024 || preg_match('/<(?:script|foreignObject)\b|\son[a-z]+\s*=|(?:href|src)\s*=\s*["\']\s*(?:javascript:|https?:|\/\/)/i', $svg)) throw new RuntimeException('Generated SVG contains unsupported or unsafe content.');
    if (!preg_match('#^data:image/png;base64,(.+)$#s', $pngData, $m)) throw new RuntimeException('Generated PNG is missing.');
    $png = base64_decode($m[1], true); if ($png === false) throw new RuntimeException('PNG could not be decoded.');
    $pngInfo = @getimagesizefromstring($png);
    if (strlen($png) > 15 * 1024 * 1024 || $pngInfo === false || ($pngInfo['mime'] ?? '') !== 'image/png') throw new RuntimeException('Generated PNG is invalid or too large.');
    $dir = dirname(__DIR__, 3) . '/beyond-tattoo/uploads/stencil-day';
    if (!is_dir($dir) && !mkdir($dir, 0775, true) && !is_dir($dir)) throw new RuntimeException('Could not create stencil upload folder.');
    $slug = strtolower(trim(preg_replace('/[^a-z0-9]+/i', '-', $title), '-')) ?: 'generated-stencil';
    $svgFile = $dir . '/generated-stencil-of-the-day.svg';
    $pngFile = $dir . '/generated-stencil-of-the-day.png';
    $igFile = $dir . '/generated-instagram-post.png';
    $metaFile = $dir . '/generated-stencil-metadata.json';
    $readmeFile = $dir . '/STUDIO-TRANSFER-NOTES.txt';
    file_put_contents($svgFile, $svg, LOCK_EX); file_put_contents($pngFile, $png, LOCK_EX); file_put_contents($igFile, $png, LOCK_EX);
    $meta = ['title'=>$title,'slug'=>$slug,'motif'=>(string)($input['motif']??''),'style'=>(string)($input['style']??''),'collection'=>(string)($input['collection']??'Beyond Ancient Collection'),'placement'=>(string)($input['placement']??'Artist choice'),'seed'=>(string)($input['seed']??''),'published_at'=>gmdate('c')];
    file_put_contents($metaFile, json_encode($meta, JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES), LOCK_EX);
    file_put_contents($readmeFile, "BEYOND TATTOO — STUDIO TRANSFER FILE\n\nDesign: {$title}\nStyle: {$meta['style']}\nSuggested placement: {$meta['placement']}\n\nFiles\n- generated-stencil-of-the-day.svg: editable vector master\n- generated-stencil-of-the-day.png: high-resolution transfer image\n- generated-instagram-post.png: 1080 x 1350 social asset\n- generated-stencil-metadata.json: design settings\n\nBefore tattooing, the artist must verify scale, line spacing, transfer orientation, anatomy and skin suitability.\n", LOCK_EX);
    $zipPath = $dir . '/generated-stencil-of-the-day.zip';
    if (!class_exists('ZipArchive')) throw new RuntimeException('PHP ZipArchive is required to rebuild the package.');
    $zip = new ZipArchive(); if ($zip->open($zipPath, ZipArchive::CREATE|ZipArchive::OVERWRITE) !== true) throw new RuntimeException('Could not create package ZIP.');
    foreach ([$svgFile,$pngFile,$igFile,$metaFile,$readmeFile] as $f) $zip->addFile($f, basename($f)); $zip->close();
    bt_stencil_save(['title'=>$title,'collection'=>$meta['collection'],'display_date'=>date('l, F j, Y'),'iso_date'=>date('Y-m-d'),'description'=>$meta['style'].' · '.$meta['motif'].' · Generated in Beyond Studio','placement'=>$meta['placement'],'preview_url'=>'uploads/stencil-day/generated-stencil-of-the-day.png','package_url'=>'uploads/stencil-day/generated-stencil-of-the-day.zip','ig_post_url'=>'uploads/stencil-day/generated-instagram-post.png','editable_url'=>'uploads/stencil-day/generated-stencil-of-the-day.svg','transfer_png_url'=>'uploads/stencil-day/generated-stencil-of-the-day.png']);
    echo json_encode(['ok'=>true,'package_url'=>'/beyond-tattoo/uploads/stencil-day/generated-stencil-of-the-day.zip']);
} catch (Throwable $e) { if (http_response_code() < 400) http_response_code(400); echo json_encode(['ok'=>false,'error'=>$e->getMessage()]); }
