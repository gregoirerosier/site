<?php
declare(strict_types=1);
require __DIR__ . '/includes/config.php';
$artist = bt_get_artist(trim((string)($_GET['slug'] ?? '')));
if (!$artist) { http_response_code(404); $pageTitle='Artist not found — Beyond Tattoo'; require __DIR__.'/includes/header.php'; echo '<main class="container dashboard"><div class="panel"><h1>Artist not found</h1><a href="studios.php">Browse studios</a></div></main>'; require __DIR__.'/includes/footer.php'; exit; }
$pageTitle = $artist['display_name'] . ' — Beyond Tattoo';
require __DIR__ . '/includes/header.php';
?>
<div class="app-shell"><header class="app-header"><div class="container app-header-inner"><a class="btn btn-secondary" href="studio-profile.php?slug=<?= urlencode((string)$artist['studio_slug']) ?>">← <?= e($artist['studio_name']) ?></a><span class="brand">Artist profile</span></div></header>
<main class="container dashboard"><section class="artist-profile-hero panel"><div class="artist-avatar large"><?= e(strtoupper(substr($artist['display_name'],0,1))) ?></div><div><span class="eyebrow">Artist at <?= e($artist['studio_name']) ?></span><h1><?= e($artist['display_name']) ?></h1><p class="section-copy"><?= e($artist['bio']) ?></p><p><strong><?= e($artist['styles']) ?></strong></p><?php if($artist['languages']): ?><p class="meta">Languages: <?= e($artist['languages']) ?></p><?php endif; ?></div></section>
<div class="dashboard-grid" style="margin-top:18px"><section class="panel"><h2>Portfolio</h2><p>This listing links to the artist’s current public portfolio so their latest work remains the source of truth.</p><a class="btn btn-primary" href="<?= e($artist['instagram_url']) ?>" target="_blank" rel="noopener">Open <?= e($artist['instagram_handle']) ?> ↗</a></section><aside class="panel"><h2>Availability</h2><p><?= e($artist['availability'] ?: 'Contact the artist for current availability.') ?></p><p class="meta"><?= e($artist['city']) ?></p></aside></div>
</main></div>
<?php require __DIR__ . '/includes/footer.php'; ?>

