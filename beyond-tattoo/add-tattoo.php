<?php
require __DIR__ . '/includes/config.php';
require_login();
if ($_SERVER['REQUEST_METHOD']==='POST') {
  bt_require_csrf();
  $file=DATA_DIR.'/tattoos.json';
  $tattoos=json_read($file);
  $tattoos[]=[
    'id'=>count($tattoos)+1,
    'user_email'=>current_user_email(),
    'name'=>trim((string)($_POST['name']??'Untitled Tattoo')),
    'artist'=>trim((string)($_POST['artist']??'')),
    'studio'=>trim((string)($_POST['studio']??'')),
    'placement'=>trim((string)($_POST['placement']??'')),
    'style'=>trim((string)($_POST['style']??'')),
    'start_date'=>trim((string)($_POST['start_date']??date('Y-m-d'))),
    'healing_days'=>28,'status'=>'active','stage'=>'Fresh','progress'=>5,
    'notes'=>trim((string)($_POST['notes']??''))
  ];
  json_write($file,$tattoos);
  redirect('my-tattoos.php');
}
$pageTitle='Add Tattoo — Beyond Tattoo';
require __DIR__ . '/includes/header.php';
?>
<div class="auth-wrap"><div class="auth-card"><a class="brand" href="dashboard.php"><span class="brand-badge">B</span><span>Add Tattoo</span></a><h1>Start a new journey</h1>
<form class="form-grid" method="post">
<input type="hidden" name="_csrf" value="<?= e(bt_csrf_token()) ?>">
<input class="input" name="name" placeholder="Tattoo name" required>
<input class="input" name="artist" placeholder="Artist">
<input class="input" name="studio" placeholder="Studio">
<input class="input" name="placement" placeholder="Placement">
<input class="input" name="style" placeholder="Style">
<input class="input" type="date" name="start_date" value="<?= date('Y-m-d') ?>">
<textarea class="input" name="notes" placeholder="Notes"></textarea>
<button class="btn btn-primary" type="submit">Add tattoo</button>
</form></div></div>
<?php require __DIR__ . '/includes/footer.php'; ?>
