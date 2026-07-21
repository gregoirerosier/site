<?php
declare(strict_types=1);
if (PHP_SAPI !== 'cli') { http_response_code(404); exit; }
require_once __DIR__ . '/../beyond-tattoo/includes/config.php';

$legacyDir = __DIR__ . '/../beyond-tattoo/data';
$file = $legacyDir . '/beta-signups.json';
$records = is_file($file) ? json_decode((string)file_get_contents($file), true) : [];
$imported = 0;
if (is_array($records)) {
    foreach ($records as $record) {
        $name = mb_substr(trim((string)($record['name'] ?? '')), 0, 200);
        $email = strtolower(trim((string)($record['email'] ?? '')));
        $interest = mb_substr(trim((string)($record['interest'] ?? 'all')), 0, 100);
        if ($name !== '' && filter_var($email, FILTER_VALIDATE_EMAIL) && bt_add_beta_signup($name, $email, $interest)) $imported++;
    }
}

$users = json_read($legacyDir . '/users.json');
$tattoos = json_read($legacyDir . '/tattoos.json');
if ($users || $tattoos) {
    fwrite(STDERR, "Legacy users or tattoos were found. Import them through Beyond ID identity review before removing the JSON files.\n");
    exit(2);
}
fwrite(STDOUT, "Imported {$imported} legacy beta signup(s). Tattoo profiles, tattoos, jobs, and invitations now use the shared database.\n");

