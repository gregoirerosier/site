<?php
require __DIR__ . '/../includes/admin-check.php';
require_once __DIR__ . '/../includes/functions.php';
require __DIR__ . '/../includes/db.php';
$title = 'Logs';
require __DIR__ . '/../includes/admin-header.php';
require __DIR__ . '/../includes/admin-sidebar.php';
?>
<section class="content">

<h1>Audit Logs</h1>
<?php try{$logs=$pdo->query("SELECT * FROM activity_logs ORDER BY id DESC LIMIT 100")->fetchAll(PDO::FETCH_ASSOC);}catch(Throwable $e){$logs=[];} ?>
<div class="card"><table><tr><th>ID</th><th>User</th><th>Action</th><th>IP</th><th>Date</th></tr><?php foreach($logs as $l): ?><tr><td><?= e($l['id']??'') ?></td><td><?= e($l['user_id']??'') ?></td><td><?= e($l['action']??'') ?></td><td><?= e($l['ip_address']??'') ?></td><td><?= e($l['created_at']??'') ?></td></tr><?php endforeach; ?></table></div>
</section><?php require __DIR__ . '/../includes/admin-footer.php'; ?>
