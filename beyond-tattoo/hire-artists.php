<?php
declare(strict_types=1);
require __DIR__ . '/includes/config.php';
require_login();
$current = bt_current_user();
$role = (string)($current['account_type'] ?? 'client');
if ($role !== 'owner') { flash('error', 'Studio owner access is required.'); redirect('dashboard.php'); }
$artists = bt_list_artists();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = (string)($_POST['action'] ?? '');
    if ($action === 'post_job') {
        $title = mb_substr(trim((string)($_POST['title'] ?? '')), 0, 220);
        $type = mb_substr(trim((string)($_POST['type'] ?? '')), 0, 120);
        $details = mb_substr(trim((string)($_POST['details'] ?? '')), 0, 5000);
        if ($title === '' || $type === '') {
            flash('error', 'Add a title and opportunity type.');
        } else {
            bt_create_job(bt_current_user_id(), (string)($current['studio_name'] ?: 'My Studio'), $title, $type, $details);
            flash('success', 'Opportunity posted for artists.');
        }
        redirect('hire-artists.php');
    }
    if ($action === 'invite') {
        $artistId = filter_input(INPUT_POST, 'artist_id', FILTER_VALIDATE_INT) ?: null;
        $target = mb_substr(trim((string)($_POST['target_label'] ?? 'Artist')), 0, 200);
        $message = mb_substr(trim((string)($_POST['message'] ?? '')), 0, 5000);
        if ($message === '') flash('error', 'Add a message before sending the invitation.');
        else { bt_create_invite(bt_current_user_id(), $artistId, $target, $message); flash('success', 'Invitation saved.'); }
        redirect('hire-artists.php');
    }
}
$pageTitle = 'Hire artists — Beyond Tattoo';
require __DIR__ . '/includes/header.php';
$success = flash('success'); $error = flash('error');
?>
<div class="app-shell"><header class="app-header"><div class="container app-header-inner"><a class="brand" href="dashboard.php"><span class="brand-badge">B</span><span>Studio hiring</span></a><a class="btn btn-secondary" href="dashboard.php">Done</a></div></header>
<main class="container dashboard">
  <?php if($success): ?><div class="notice"><?= e($success) ?></div><?php endif; ?><?php if($error): ?><div class="notice error-notice"><?= e($error) ?></div><?php endif; ?>
  <section class="hire-hero panel"><div><span class="eyebrow">Owner tools</span><h1>Build your artist roster.</h1><p class="section-copy">Post an opening and contact listed artists through their current portfolios.</p></div><button class="btn btn-primary" type="button" data-open-modal="job-modal">+ Post opportunity</button></section>
  <div class="filter-row"><input class="input" type="search" placeholder="Search style, city, studio, or artist" data-artist-search><select class="input" data-artist-filter><option value="">All availability</option><option>open</option><option>available</option></select></div>
  <section class="artist-grid" data-artist-grid><?php foreach($artists as $artist): ?><article class="artist-card" data-artist-text="<?= e(strtolower($artist['display_name'].' '.$artist['city'].' '.$artist['styles'].' '.$artist['availability'].' '.$artist['studio_name'])) ?>"><div class="artist-avatar"><?= e(strtoupper(substr($artist['display_name'],0,1))) ?></div><div class="artist-card-body"><div class="artist-heading"><div><h3><?= e($artist['display_name']) ?></h3><p class="meta"><?= e($artist['city']) ?> • <?= e($artist['studio_name']) ?></p></div></div><p class="artist-styles"><?= e($artist['styles']) ?></p><?php if($artist['availability']): ?><div class="chip-row"><span><?= e($artist['availability']) ?></span></div><?php endif; ?><div class="artist-actions"><a class="btn btn-secondary" href="artist-profile.php?slug=<?= urlencode($artist['slug']) ?>">View profile</a><a class="btn btn-secondary" href="<?= e($artist['instagram_url']) ?>" target="_blank" rel="noopener">Portfolio ↗</a><button class="btn btn-primary" type="button" data-invite-id="<?= (int)$artist['id'] ?>" data-invite-name="<?= e($artist['display_name']) ?>">Invite</button></div></div></article><?php endforeach; ?></section>
</main></div>
<div class="modal" id="job-modal" hidden><div class="modal-card"><button class="modal-close" type="button" data-close-modal>×</button><span class="eyebrow">New opportunity</span><h2>What kind of artist do you need?</h2><form class="form-grid" method="post"><input type="hidden" name="_csrf" value="<?= e(bt_csrf_token()) ?>"><input type="hidden" name="action" value="post_job"><input class="input" name="title" placeholder="e.g. Resident realism artist" required><select class="input" name="type" required><option value="">Opportunity type</option><option>Full-time resident</option><option>Part-time resident</option><option>Guest spot</option><option>Apprenticeship</option></select><textarea class="input" name="details" rows="5" placeholder="Style, schedule, compensation model, and studio culture"></textarea><button class="btn btn-primary">Publish opportunity</button></form></div></div>
<div class="modal" id="invite-modal" hidden><div class="modal-card"><button class="modal-close" type="button" data-close-modal>×</button><span class="eyebrow">Direct invitation</span><h2>Invite <span data-invite-target>artist</span></h2><form class="form-grid" method="post"><input type="hidden" name="_csrf" value="<?= e(bt_csrf_token()) ?>"><input type="hidden" name="action" value="invite"><input type="hidden" name="artist_id" data-invite-input><input type="hidden" name="target_label" data-invite-label><textarea class="input" name="message" rows="5" placeholder="Introduce your studio and the opportunity" required></textarea><button class="btn btn-primary">Save invitation</button></form></div></div>
<?php require __DIR__ . '/includes/footer.php'; ?>

