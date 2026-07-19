<?php
declare(strict_types=1);
require_once __DIR__.'/bootstrap.php';
$message='';
if($_SERVER['REQUEST_METHOD']==='POST'){
    if(!Security::verifyCsrf($_POST['csrf']??null)){http_response_code(403);exit('Invalid security token.');}
    $email=trim((string)($_POST['email']??''));$token=bin2hex(random_bytes(24));
    try{$stmt=Database::conn()->prepare('SELECT id FROM users WHERE email=? LIMIT 1');$stmt->execute([$email]);if($user=$stmt->fetch()){Database::conn()->prepare('INSERT INTO password_resets (user_id,token,expires_at,created_at) VALUES (?,?,DATE_ADD(NOW(),INTERVAL 1 HOUR),NOW())')->execute([$user['id'],$token]);}$message='If the email exists, a reset request was created.';}catch(Throwable $exception){error_log($exception->getMessage());$message='Reset system unavailable.';}
}
?><!doctype html><html><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><link rel="stylesheet" href="/server/admin/assets/css/app.css"><title>Forgot password</title></head><body class="login-page"><div class="login-card"><h1>Forgot Password</h1><?php if($message):?><div class="alert"><?=htmlspecialchars($message,ENT_QUOTES,'UTF-8')?></div><?php endif;?><form method="post"><input type="hidden" name="csrf" value="<?=htmlspecialchars(Security::csrf(),ENT_QUOTES,'UTF-8')?>"><div class="field"><label>Email</label><input class="input" type="email" name="email" required></div><button class="btn">Create Reset Request</button></form><p><a href="/server/admin/login.php">Back to login</a></p></div></body></html>
