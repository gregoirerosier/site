<?php
declare(strict_types=1);
header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store');
require_once __DIR__.'/../includes/beyond-cartoons-schedule.php';
echo json_encode(['ok'=>true,'state'=>beyond_cartoons_schedule_state()],JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE);
