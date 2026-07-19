<?php
require_once __DIR__ . '/bootstrap.php';
define('SMTP_HOST', (string)beyond_config('smtp.host', ''));
define('SMTP_PORT', (int)beyond_config('smtp.port', 465));
define('SMTP_SECURE', (string)beyond_config('smtp.secure', 'ssl'));
define('SMTP_USER', (string)beyond_config('smtp.user', ''));
define('SMTP_PASS', (string)beyond_config('smtp.pass', ''));
define('SMTP_FROM', (string)beyond_config('smtp.from', ''));
define('SMTP_FROM_NAME', (string)beyond_config('smtp.from_name', 'Beyond Imagination'));
define('SMTP_REPLY_TO', (string)beyond_config('smtp.reply_to', ''));
