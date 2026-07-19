<?php
require __DIR__ . '/../includes/admin-check.php';
require_once __DIR__ . '/../includes/functions.php';
require __DIR__ . '/../includes/db.php';
$title = 'Users';
require __DIR__ . '/../includes/admin-header.php';
require __DIR__ . '/../includes/admin-sidebar.php';
?>
<section class="content">

<h1>User Manager</h1>
<?php $users=$pdo->query("SELECT id, first_name, last_name, email, role, status, created_at FROM users ORDER BY id DESC LIMIT 100")->fetchAll(PDO::FETCH_ASSOC); ?>
<div class="card"><table><tr><th>ID</th><th>Name</th><th>Email</th><th>Role</th><th>Status</th><th>Created</th></tr><?php foreach($users as $u): ?><tr><td><?= e($u['id']) ?></td><td><?= e(($u['first_name']??'').' '.($u['last_name']??'')) ?></td><td><?= e($u['email']) ?></td><td><?= e($u['role']) ?></td><td><?= e($u['status']) ?></td><td><?= e($u['created_at']) ?></td></tr><?php endforeach; ?></table></div>
</section><?php require __DIR__ . '/../includes/admin-footer.php'; ?>
