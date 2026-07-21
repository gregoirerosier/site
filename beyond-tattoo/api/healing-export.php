<?php
declare(strict_types=1);
require __DIR__ . '/../includes/config.php'; require_login();
$entries=bt_list_healing_entries(bt_current_user_id());header('Content-Type: text/csv; charset=UTF-8');header('Content-Disposition: attachment; filename="beyond-tattoo-healing-journal.csv"');header('Cache-Control: private, no-store, max-age=0');$out=fopen('php://output','wb');fputcsv($out,['date','tattoo','notes']);foreach($entries as $entry)fputcsv($out,[$entry['created_at'],$entry['tattoo_name'],$entry['notes']]);fclose($out);

