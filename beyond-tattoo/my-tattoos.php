<?php
require __DIR__ . '/includes/config.php';
require_login();
$tattoos = array_values(array_filter(json_read(DATA_DIR . '/tattoos.json'), fn($t) => ($t['user_email'] ?? '') === current_user_email()));
$pageTitle = 'My Tattoos — Beyond Tattoo';
require __DIR__ . '/includes/header.php';
?>
<div class="app-shell"><header class="app-header"><div class="container app-header-inner"><a class="brand" href="dashboard.php"><span class="brand-badge">B</span><span>My Tattoos</span></a><a class="btn btn-primary" href="add-tattoo.php">+ Add</a></div></header>
<main class="container dashboard"><div class="tattoo-grid">
<?php foreach($tattoos as $tattoo): ?><article class="tattoo-mini"><span class="status"><?= e(($tattoo['status']??'')==='active'?'● Active healing':'✓ Healed') ?></span><h3><?= e($tattoo['name']??'') ?></h3><p class="meta"><?= e($tattoo['artist']??'') ?> • <?= e($tattoo['studio']??'') ?></p><div class="progress"><span style="width:<?= (int)($tattoo['progress']??0) ?>%"></span></div></article><?php endforeach; ?>
</div></main></div>
<?php require __DIR__ . '/includes/footer.php'; ?>