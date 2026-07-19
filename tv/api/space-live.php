<?php
declare(strict_types=1);
header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store');
require_once dirname(__DIR__, 2) . '/beyond-tv/includes/space-schedule.php';
echo json_encode(['ok'=>true,'state'=>beyond_space_schedule_state()], JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE);
