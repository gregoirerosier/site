<?php
declare(strict_types=1);
require __DIR__ . '/includes/config.php';
$query = trim((string)($_GET['q'] ?? ''));
$studios = bt_list_studios($query);
$pageTitle = 'Studios — Beyond Tattoo';
require __DIR__ . '/includes/header.php';
?>
<div class="app-shell">
  <header class="app-header"><div class="container app-header-inner"><a class="brand" href="index.php"><span class="brand-badge">B</span><span>Studios</span></a><?php if(is_logged_in()): ?><a class="btn btn-secondary" href="dashboard.php">Dashboard</a><?php endif; ?></div></header>
  <main class="container dashboard">
    <section class="panel"><span class="eyebrow">Real studios • Direct contact</span><h1>Find a studio</h1><p class="section-copy">Browse studios that have been added to Beyond Tattoo. Contact and portfolio links go directly to the studio.</p>
      <form class="filter-row" method="get"><label class="sr-only" for="studio-search">Search studios</label><input id="studio-search" class="input" name="q" value="<?= e($query) ?>" placeholder="Search city, studio, or service"><button class="btn btn-primary" type="submit">Search</button></form>
    </section>
    <section class="artist-grid" aria-live="polite">
      <?php foreach ($studios as $studio): ?>
      <article class="artist-card">
        <div class="artist-avatar"><?= e(strtoupper(substr((string)$studio['name'], 0, 1))) ?></div>
        <div class="artist-card-body"><div class="artist-heading"><div><h2><?= e($studio['name']) ?></h2><p class="meta"><?= e($studio['city']) ?><?= $studio['province'] ? ', ' . e($studio['province']) : '' ?></p></div><?php if((int)$studio['walk_ins']===1): ?><span class="verified">Walk-ins</span><?php endif; ?></div>
          <p><?= e($studio['description']) ?></p><div class="chip-row"><span><?= (int)$studio['artist_count'] ?> listed artists</span><span><?= e($studio['services']) ?></span></div>
          <div class="artist-actions"><a class="btn btn-primary" href="studio-profile.php?slug=<?= urlencode($studio['slug']) ?>">View studio</a><a class="btn btn-secondary" href="<?= e($studio['instagram_url']) ?>" target="_blank" rel="noopener">Instagram ↗</a></div>
        </div>
      </article>
      <?php endforeach; ?>
      <?php if (!$studios): ?><div class="panel"><h2>No studios matched.</h2><p class="meta">Try a city or service such as Ottawa, tattooing, or piercing.</p></div><?php endif; ?>
    </section>
  </main>
</div>
<?php require __DIR__ . '/includes/footer.php'; ?>

