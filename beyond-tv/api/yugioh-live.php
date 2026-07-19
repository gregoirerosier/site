<?php
declare(strict_types=1);
header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store, max-age=0');
require_once __DIR__ . '/../includes/yugioh-live.php';
echo json_encode(['ok' => true, 'channel' => 'yugioh-tv', 'state' => beyond_yugioh_live_state()], JSON_UNESCAPED_SLASHES);
