<?php
require_once __DIR__.'/bootstrap.php'; Auth::requireLogin();
$error=''; $success='';
if ($_SERVER['REQUEST_METHOD']==='POST') {
    if (!Auth::verifyCsrf($_POST['csrf'] ?? '')) $error='Security token expired.';
    else {
        $user = Auth::user();
        $stmt=Database::conn()->prepare("SELECT password_hash FROM users WHERE id=?"); $stmt->execute([$user['id']]); $row=$stmt->fetch();
        if (!$row || !password_verify($_POST['current_password'] ?? '', $row['password_hash'])) $error='Current password is incorrect.';
        elseif (strlen($_POST['new_password'] ?? '') < 10) $error='New password must be at least 10 characters.';
        elseif (($_POST['new_password'] ?? '') !== ($_POST['confirm_password'] ?? '')) $error='Passwords do not match.';
        else { $hash=password_hash($_POST['new_password'], PASSWORD_DEFAULT); Database::conn()->prepare("UPDATE users SET password_hash=? WHERE id=?")->execute([$hash,$user['id']]); Auth::log($user['id'],'password_changed','admin'); $success='Password updated.'; }
    }
}
require __DIR__.'/_header.php'; ?>
<h1>Change Password</h1><div class="card"><?php if($error):?><div class="alert alert-error"><?=htmlspecialchars($error)?></div><?php endif;?><?php if($success):?><div class="alert" style="background:#0f3d2a;color:#aaffc8"><?=htmlspecialchars($success)?></div><?php endif;?><form method="post"><input type="hidden" name="csrf" value="<?=htmlspecialchars(Auth::csrf())?>"><div class="field"><label>Current Password</label><input class="input" type="password" name="current_password" required></div><div class="field"><label>New Password</label><input class="input" type="password" name="new_password" required></div><div class="field"><label>Confirm New Password</label><input class="input" type="password" name="confirm_password" required></div><button class="btn">Update Password</button></form></div><?php require __DIR__.'/_footer.php'; ?>
