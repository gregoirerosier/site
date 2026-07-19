<?php
declare(strict_types=1);
require_once __DIR__ . '/../../includes/stencil-tracking.php';
require_once __DIR__ . '/../includes/stencil-content.php';
$stencil = bt_stencil_content();
$type = strtolower((string)($_GET['type'] ?? 'package'));
$map = [
  'package' => [$stencil['package_url'], 'application/zip', 'beyond-tattoo-stencil-of-the-day.zip'],
  'preview' => [$stencil['preview_url'], 'image/png', 'beyond-tattoo-stencil-preview.png'],
  'png' => [$stencil['transfer_png_url'] ?? $stencil['preview_url'], 'image/png', 'beyond-tattoo-studio-transfer.png'],
  'editable' => [$stencil['editable_url'] ?? $stencil['preview_url'], 'image/svg+xml; charset=UTF-8', 'beyond-tattoo-editable-master.svg'],
  'ig' => [$stencil['ig_post_url'], 'image/png', 'beyond-tattoo-instagram-post.png'],
];
[$relative,$mime,$name] = $map[$type] ?? $map['package'];
$file = dirname(__DIR__) . '/' . ltrim($relative, '/');
if (!is_file($file)) { http_response_code(404); exit('Generated stencil asset unavailable. Publish the current design again in Beyond Studio.'); }
track_stencil_download('stencil-of-day.' . $type);
header('Content-Type: ' . $mime);
header('Content-Disposition: attachment; filename="' . $name . '"');
header('Content-Length: ' . filesize($file));
header('Cache-Control: private, no-store, max-age=0');
header('X-Content-Type-Options: nosniff');
readfile($file);
