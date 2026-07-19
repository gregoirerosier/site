<?php
require __DIR__ . '/../config/database.php';
session_start();
if (empty($_SESSION['dailybreath_admin'])) { http_response_code(403); exit('Forbidden'); }
header('Content-Type: text/csv');
header('Content-Disposition: attachment; filename="dailybreath-subscribers.csv"');
$out = fopen('php://output', 'w');
fputcsv($out, ['id','name','email','status','created_at']);
foreach (db()->query("SELECT id,name,email,status,created_at FROM dailybreath_subscribers ORDER BY created_at DESC") as $row) fputcsv($out, $row);
