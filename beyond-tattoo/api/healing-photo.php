<?php
declare(strict_types=1);
require __DIR__ . '/../includes/config.php'; require_login();
$id=filter_input(INPUT_GET,'id',FILTER_VALIDATE_INT)?:0;$stmt=bt_db()->prepare('SELECT file_path,mime FROM tattoo_healing_entries WHERE id=? AND user_id=? LIMIT 1');$stmt->execute([$id,bt_current_user_id()]);$entry=$stmt->fetch(PDO::FETCH_ASSOC);
if(!$entry){http_response_code(404);exit('Healing photo not found.');}
$base=realpath(UPLOAD_DIR);$file=realpath(UPLOAD_DIR.'/'.$entry['file_path']);if(!is_string($base)||!is_string($file)||!str_starts_with(str_replace('\\','/',$file),str_replace('\\','/',$base).'/')){http_response_code(404);exit('Healing photo not found.');}
header('Content-Type: '.$entry['mime']);header('Content-Length: '.filesize($file));header('Cache-Control: private, no-store, max-age=0');header('Content-Disposition: inline');header('X-Content-Type-Options: nosniff');readfile($file);

