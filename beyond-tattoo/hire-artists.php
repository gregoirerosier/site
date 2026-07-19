<?php
require __DIR__ . '/includes/config.php';
require_login();

$users = json_read(DATA_DIR . '/users.json');
$current = null;
foreach ($users as $candidate) if (($candidate['email'] ?? '') === current_user_email()) { $current = $candidate; break; }
$role = (string)($current['role'] ?? $_SESSION['user_role'] ?? 'client');
if ($role !== 'owner') { flash('error', 'Studio owner access is required.'); redirect('dashboard.php'); }

$artists = array_values(array_filter($users, fn($u) => ($u['role'] ?? '') === 'artist'));
if (!$artists) {
    $artists = [
      ['id'=>'artist_maya','name'=>'Maya Chen','email'=>'maya@example.test','role'=>'artist','profile'=>['city'=>'Vancouver, BC','styles'=>'Fine line • Botanical • Blackwork','experience'=>'7 years','availability'=>'Open to resident position']],
      ['id'=>'artist_dre','name'=>'Andre Lewis','email'=>'andre@example.test','role'=>'artist','profile'=>['city'=>'Victoria, BC','styles'=>'Realism • Portraits • Colour','experience'=>'9 years','availability'=>'Guest spots available']],
      ['id'=>'artist_nova','name'=>'Nova Reyes','email'=>'nova@example.test','role'=>'artist','profile'=>['city'=>'Nanaimo, BC','styles'=>'Neo-traditional • Anime • Illustrative','experience'=>'5 years','availability'=>'Seeking full-time studio']],
    ];
}
$jobsFile = DATA_DIR . '/artist-opportunities.json';
$jobs = json_read($jobsFile);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    bt_require_csrf();
    $action = (string)($_POST['action'] ?? '');
    if ($action === 'post_job') {
        $title = trim((string)($_POST['title'] ?? ''));
        $type = trim((string)($_POST['type'] ?? ''));
        $details = trim((string)($_POST['details'] ?? ''));
        if ($title !== '' && $type !== '') {
            $jobs[] = ['id'=>'job_'.bin2hex(random_bytes(4)),'owner_email'=>current_user_email(),'studio_name'=>$current['profile']['studio_name'] ?? 'My Studio','title'=>$title,'type'=>$type,'details'=>$details,'status'=>'open','created_at'=>date(DATE_ATOM)];
            json_write($jobsFile, $jobs);
            flash('success', 'Opportunity posted for artists.');
            redirect('hire-artists.php');
        }
    }
    if ($action === 'invite') {
        $artistEmail = trim((string)($_POST['artist_email'] ?? ''));
        $invitesFile = DATA_DIR . '/artist-invites.json';
        $invites = json_read($invitesFile);
        $invites[] = ['id'=>'inv_'.bin2hex(random_bytes(4)),'owner_email'=>current_user_email(),'artist_email'=>$artistEmail,'message'=>trim((string)($_POST['message'] ?? '')),'status'=>'sent','created_at'=>date(DATE_ATOM)];
        json_write($invitesFile, $invites);
        flash('success', 'Artist invitation sent.');
        redirect('hire-artists.php');
    }
}
$pageTitle = 'Hire artists — Beyond Tattoo';
require __DIR__ . '/includes/header.php';
$success = flash('success');
?>
<div class="app-shell">
<header class="app-header"><div class="container app-header-inner"><a class="brand" href="dashboard.php"><img class="brand-icon" src="../assets/icons/beyond-tattoo-192.webp" alt=""><span>Studio hiring</span></a><a class="btn btn-secondary" href="dashboard.php">Done</a></div></header>
<main class="container dashboard">
  <?php if ($success): ?><div class="notice"><?= e($success) ?></div><?php endif; ?>
  <section class="hire-hero panel">
    <div><span class="eyebrow">Owner tools</span><h1>Build your artist roster.</h1><p class="section-copy">Post an opening, review talent and invite the right artist directly into your studio workflow.</p></div>
    <button class="btn btn-primary" type="button" data-open-modal="job-modal">+ Post opportunity</button>
  </section>
  <div class="filter-row"><input class="input" type="search" placeholder="Search style, city or experience" data-artist-search><select class="input" data-artist-filter><option value="">All availability</option><option>full-time</option><option>guest</option><option>resident</option></select></div>
  <section class="artist-grid" data-artist-grid>
    <?php foreach ($artists as $artist): $p = $artist['profile'] ?? []; ?>
    <article class="artist-card" data-artist-text="<?= e(strtolower(($artist['name'] ?? '').' '.($p['city'] ?? '').' '.($p['styles'] ?? '').' '.($p['availability'] ?? ''))) ?>">
      <div class="artist-avatar"><?= e(strtoupper(substr((string)($artist['name'] ?? 'A'),0,1))) ?></div>
      <div class="artist-card-body"><div class="artist-heading"><div><h3><?= e($artist['name'] ?? 'Artist') ?></h3><p class="meta"><?= e($p['city'] ?? 'Location flexible') ?></p></div><span class="verified">✓ Pro</span></div>
      <p class="artist-styles"><?= e($p['styles'] ?? 'Multi-style tattoo artist') ?></p><div class="chip-row"><span><?= e($p['experience'] ?? 'Portfolio ready') ?></span><span><?= e($p['availability'] ?? 'Available') ?></span></div>
      <div class="artist-actions"><a class="btn btn-secondary" href="artist-profile.php?email=<?= urlencode((string)($artist['email'] ?? '')) ?>">View portfolio</a><button class="btn btn-primary" type="button" data-invite-email="<?= e($artist['email'] ?? '') ?>" data-invite-name="<?= e($artist['name'] ?? 'Artist') ?>">Invite</button></div></div>
    </article>
    <?php endforeach; ?>
  </section>
</main>
<nav class="bottom-nav"><a href="dashboard.php"><span>⌂</span>Studio</a><a class="active" href="hire-artists.php"><span>🤝</span>Hire</a><a href="studios.php"><span>📍</span>Profile</a><a href="profile.php"><span>👤</span>Account</a></nav>
</div>
<div class="modal" id="job-modal" hidden><div class="modal-card"><button class="modal-close" type="button" data-close-modal>×</button><span class="eyebrow">New opportunity</span><h2>What kind of artist do you need?</h2><form class="form-grid" method="post"><input type="hidden" name="action" value="post_job"><input class="input" name="title" placeholder="e.g. Resident realism artist" required><select class="input" name="type" required><option value="">Opportunity type</option><option>Full-time resident</option><option>Part-time resident</option><option>Guest spot</option><option>Apprenticeship</option></select><textarea class="input" name="details" rows="5" placeholder="Style, schedule, compensation model and studio culture"></textarea><button class="btn btn-primary">Publish opportunity</button></form></div></div>
<div class="modal" id="invite-modal" hidden><div class="modal-card"><button class="modal-close" type="button" data-close-modal>×</button><span class="eyebrow">Direct invitation</span><h2>Invite <span data-invite-target>artist</span></h2><form class="form-grid" method="post"><input type="hidden" name="action" value="invite"><input type="hidden" name="artist_email" data-invite-input><textarea class="input" name="message" rows="5" placeholder="Introduce your studio and the opportunity" required></textarea><button class="btn btn-primary">Send invitation</button></form></div></div>
<?php require __DIR__ . '/includes/footer.php'; ?>
