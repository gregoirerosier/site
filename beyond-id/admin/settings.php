<?php
require __DIR__ . '/../includes/admin-check.php';
require_once __DIR__ . '/../includes/functions.php';
require __DIR__ . '/../includes/db.php';
$title = 'Appearance';
require __DIR__ . '/../includes/admin-header.php';
require __DIR__ . '/../includes/admin-sidebar.php';
?>
<section class="content">
  <div class="page-heading">
    <div>
      <p class="eyebrow">Personalize your console</p>
      <h1>Themes and Appearance</h1>
      <p class="muted">Choose a palette for this browser. Your selection is saved automatically.</p>
    </div>
  </div>
  <div class="card">
    <div class="card-heading"><h2>Choose theme</h2></div>
    <div class="themes">
      <button type="button" class="theme-card" data-theme-choice="midnight" onclick="setBeyondTheme('midnight')">Midnight</button>
      <button type="button" class="theme-card" data-theme-choice="light" onclick="setBeyondTheme('light')">Light</button>
      <button type="button" class="theme-card" data-theme-choice="ocean" onclick="setBeyondTheme('ocean')">Ocean</button>
      <button type="button" class="theme-card" data-theme-choice="forest" onclick="setBeyondTheme('forest')">Forest</button>
      <button type="button" class="theme-card" data-theme-choice="sunset" onclick="setBeyondTheme('sunset')">Sunset</button>
    </div>
  </div>
</section>
<?php require __DIR__ . '/../includes/admin-footer.php'; ?>
