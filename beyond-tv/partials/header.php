<?php
require_once __DIR__ . '/../../includes/ecosystem.php';
render_beyond_bar('Beyond TV');
$signedIn = !empty($_SESSION['user_id']);
?>
<header class="site-header tv-header">
  <div class="shell nav-wrap">
    <a class="brand" href="/beyond-tv/">
      <img src="/beyond-tv/assets/img/beyond-tv-logo.webp" alt="Beyond TV">
      <span><b>BEYOND <em>TV</em></b><small>LIVE • LEARN • RELAX</small></span>
    </a>
    <nav class="desktop-nav" aria-label="Beyond TV">
      <a href="/beyond-tv/">Watch</a><a href="/beyond-tv/live-tv.php">Guide</a><a href="/beyond-tv/browse.php">Browse</a>
      <?php if ($signedIn): ?><a href="/beyond-tv/browse.php?list=mine">My List</a><?php endif; ?>
    </nav>
    <div class="nav-actions">
      <button class="icon-btn btv-theme-toggle" type="button" data-tv-theme-toggle aria-label="Change theme" title="Change theme">🌙</button>
      <a class="icon-btn" href="/beyond-tv/browse.php" aria-label="Search channels">⌕</a>
      <?php if (!$signedIn): ?><a class="tv-signin" href="/beyond-id/auth/login.php?return=/beyond-tv/">Profile</a><?php endif; ?>
      <button class="menu-btn" type="button" aria-label="Toggle menu" aria-expanded="false">☰</button>
    </div>
  </div>
  <nav class="mobile-nav" aria-label="Mobile" hidden><a href="/beyond-tv/">Watch now</a><a href="/beyond-tv/live-tv.php">Guide</a><a href="/beyond-tv/browse.php">Browse</a><?php if (!$signedIn): ?><a href="/beyond-id/auth/login.php?return=/beyond-tv/">Profile channels</a><?php endif; ?></nav>
</header>
