<?php
require __DIR__ . '/includes/config.php';
require_login();
$pageTitle='Profile — Beyond Tattoo';
require __DIR__ . '/includes/header.php';
?>
<div class="app-shell"><header class="app-header"><div class="container app-header-inner"><a class="brand" href="dashboard.php"><span class="brand-badge">B</span><span>Profile</span></a></div></header>
<main class="container dashboard"><div class="panel"><h2><?= e($_SESSION['user_name'] ?? 'Alex') ?></h2><p class="meta"><?= e(current_user_email()) ?></p><div class="cards"><div class="card"><h3>2 Tattoos</h3><p>Your personal tattoo vault.</p></div><div class="card"><h3>9 Healing Days</h3><p>Keep the streak going.</p></div><div class="card"><h3>3 Saved Studios</h3><p>Ready for your next piece.</p></div></div></div></main></div>
<?php require __DIR__ . '/includes/footer.php'; ?>