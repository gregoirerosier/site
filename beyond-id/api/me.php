<?php
declare(strict_types=1);
require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ . '/../includes/db.php';
header('Content-Type: application/json; charset=utf-8');
if (empty($_SESSION['user_id'])) { http_response_code(401); echo json_encode(['ok'=>false,'authenticated'=>false]); exit; }
$stmt=$pdo->prepare('SELECT u.id,u.name,u.first_name,u.last_name,u.email,u.role,u.preferred_locale,u.timezone,p.display_name,p.avatar,p.country,p.city,p.bio,p.interests,p.goals FROM users u LEFT JOIN profiles p ON p.user_id=u.id WHERE u.id=? LIMIT 1');
$stmt->execute([(int)$_SESSION['user_id']]); $user=$stmt->fetch(PDO::FETCH_ASSOC);
$wallet=['balance'=>0,'currency'=>'BITS'];
try{$w=$pdo->prepare('SELECT balance,currency,status FROM beyond_wallets WHERE user_id=? LIMIT 1');$w->execute([(int)$_SESSION['user_id']]);$wallet=$w->fetch(PDO::FETCH_ASSOC)?:$wallet;}catch(Throwable $e){}
echo json_encode(['ok'=>true,'authenticated'=>true,'user'=>$user,'wallet'=>$wallet]);
