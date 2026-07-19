<?php
require __DIR__ . '/includes/config.php';
$email = strtolower(trim((string)($_GET['email'] ?? '')));
$artist = null;
foreach (json_read(DATA_DIR . '/users.json') as $u) if (($u['email'] ?? '') === $email && ($u['role'] ?? '') === 'artist') { $artist = $u; break; }
if (!$artist) $artist = ['name'=>'Featured artist','email'=>$email,'profile'=>['city'=>'British Columbia','styles'=>'Fine line • Realism • Illustrative','experience'=>'Portfolio available','availability'=>'Open to opportunities']];
$p = $artist['profile'] ?? [];
$pageTitle = e($artist['name'] ?? 'Artist') . ' — Beyond Tattoo';
require __DIR__ . '/includes/header.php';
?>
<div class="app-shell"><header class="app-header"><div class="container app-header-inner"><a class="btn btn-secondary" href="hire-artists.php">← Back</a><span class="brand">Artist profile</span></div></header>
<main class="container dashboard"><section class="artist-profile-hero panel"><div class="artist-avatar large"><?= e(strtoupper(substr((string)($artist['name'] ?? 'A'),0,1))) ?></div><div><span class="verified">✓ Verified artist</span><h1><?= e($artist['name'] ?? 'Artist') ?></h1><p class="section-copy"><?= e($p['city'] ?? '') ?> • <?= e($p['experience'] ?? '') ?></p><p><?= e($p['styles'] ?? '') ?></p></div></section>
<section class="panel"><h2>Portfolio preview</h2><div class="portfolio-grid"><div class="portfolio-tile">Fine line</div><div class="portfolio-tile">Blackwork</div><div class="portfolio-tile">Custom work</div></div></section>
<section class="panel" style="margin-top:18px"><h2>Availability</h2><p><?= e($p['availability'] ?? 'Contact artist for current availability.') ?></p><a class="btn btn-primary" href="hire-artists.php">Invite through Beyond Tattoo</a></section></main></div>
<?php require __DIR__ . '/includes/footer.php'; ?>
