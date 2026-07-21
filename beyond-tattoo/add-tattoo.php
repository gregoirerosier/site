<?php
declare(strict_types=1);
require __DIR__ . '/includes/config.php';
require_login();
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try { bt_create_tattoo(bt_current_user_id(), $_POST); flash('success','Tattoo added to your journey.'); redirect('my-tattoos.php'); }
    catch (Throwable $e) { $error = $e instanceof InvalidArgumentException ? $e->getMessage() : 'The tattoo could not be saved.'; }
}
$pageTitle='Add Tattoo — Beyond Tattoo'; require __DIR__ . '/includes/header.php';
?>
<div class="auth-wrap"><div class="auth-card"><a class="brand" href="dashboard.php"><span class="brand-badge">B</span><span>Add Tattoo</span></a><h1>Start a new journey</h1><?php if(isset($error)):?><div class="notice error-notice"><?=e($error)?></div><?php endif;?><form class="form-grid" method="post"><input type="hidden" name="_csrf" value="<?=e(bt_csrf_token())?>"><input class="input" name="name" placeholder="Tattoo name" required><input class="input" name="artist" placeholder="Artist"><input class="input" name="studio" placeholder="Studio"><input class="input" name="placement" placeholder="Placement"><input class="input" name="style" placeholder="Style"><input class="input" type="date" name="start_date" value="<?=date('Y-m-d')?>"><textarea class="input" name="notes" placeholder="Notes"></textarea><button class="btn btn-primary" type="submit">Add tattoo</button></form></div></div>
<?php require __DIR__ . '/includes/footer.php'; ?>

