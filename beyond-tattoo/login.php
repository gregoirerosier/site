<?php
require __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/stencil-content.php';
$stencilDay = bt_stencil_content();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    bt_require_csrf();
    $email = strtolower(trim((string)($_POST['email'] ?? '')));
    $password = (string)($_POST['password'] ?? '');

    $users = json_read(DATA_DIR . '/users.json');
    foreach ($users as $user) {
        if (($user['email'] ?? '') === $email && password_verify($password, (string)($user['password_hash'] ?? ''))) {
            $_SESSION['user_email'] = $email;
            $_SESSION['user_name'] = (string)($user['name'] ?? 'Tattoo Lover');
            $_SESSION['user_role'] = (string)($user['role'] ?? 'client');
            redirect('dashboard.php');
        }
    }
    $error = 'Email or password was not recognized.';
}
$pageTitle = 'Log in — Beyond Tattoo';
$bodyClass = 'tattoo-login-page';
require __DIR__ . '/includes/header.php';
?>
<main class="tattoo-login-shell">
  <div class="tattoo-login-topline">
    <a href="/" aria-label="Back to Beyond OS">← Beyond OS</a>
    <span>✦ Free stencil drops</span>
  </div>

  <section class="tattoo-login-card" aria-labelledby="tattoo-login-title">
    <div class="tattoo-login-form-panel">
      <a class="tattoo-login-brand" href="index.php">
        <img src="assets/img/beyond-tattoo-logo.webp" alt="Beyond Tattoo">
        <span><b>BEYOND</b> TATTOO</span>
      </a>

      <div class="tattoo-login-copy">
        <span class="tattoo-login-kicker">ARTIST ACCESS</span>
        <h1 id="tattoo-login-title">Welcome back.</h1>
        <p>Sign in to manage stencils, healing tools, collections and your tattoo studio workspace.</p>
      </div>

      <?php if (isset($error)): ?>
        <div class="notice error-notice"><?= e($error) ?></div>
      <?php endif; ?>

      <form class="tattoo-login-form" method="post">
        <input type="hidden" name="_csrf" value="<?= e(bt_csrf_token()) ?>">
        <label>
          <span>Email</span>
          <input class="input" type="email" name="email" placeholder="you@example.com" autocomplete="email" required>
        </label>
        <label>
          <span>Password</span>
          <input class="input" type="password" name="password" placeholder="Your password" autocomplete="current-password" required>
        </label>
        <button class="btn btn-primary btn-block tattoo-login-submit" type="submit">Log in to Beyond Tattoo</button>
      </form>

      <p class="tattoo-login-register">New here? <a href="register.php">Create an account</a></p>
    </div>

    <aside class="tattoo-drop-panel" aria-label="Stencil of the Day download">
      <div class="tattoo-drop-image-wrap">
        <img src="<?= e($stencilDay['preview_url']) ?>?v=<?= e((string)($stencilDay['updated_at'] ?: $stencilDay['iso_date'])) ?>" alt="<?= e($stencilDay['title']) ?> — Stencil of the Day">
        <span class="tattoo-drop-badge">STENCIL<br>OF THE DAY</span>
      </div>

      <div class="tattoo-drop-content">
        <span class="tattoo-drop-label">TODAY'S FREE DROP</span>
        <h2>Download <?= e($stencilDay['title']) ?></h2>
        <p>Printer-ready PNG, editable SVG, studio PDF and preview files in one free pack.</p>

        <div class="tattoo-drop-features" aria-label="Package features">
          <span>✓ Clean linework</span>
          <span>✓ Transfer ready</span>
          <span>✓ Artist format</span>
        </div>

        <a class="tattoo-download-cta" href="<?= e($stencilDay['package_url']) ?>" download>
          <span aria-hidden="true">↓</span>
          <b>DOWNLOAD FREE STENCIL PACK</b>
        </a>
        <a class="tattoo-view-link" href="stencil-of-day.php">Preview today's stencil →</a>
        <small>No login required for the daily download.</small>
      </div>
    </aside>
  </section>
</main>
<?php require __DIR__ . '/includes/footer.php'; ?>
