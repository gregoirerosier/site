<?php
require __DIR__ . '/includes/config.php';

$allowedRoles = ['client', 'artist', 'owner'];
$selectedRole = (string)($_GET['role'] ?? $_POST['role'] ?? 'client');
if (!in_array($selectedRole, $allowedRoles, true)) $selectedRole = 'client';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    bt_require_csrf();
    $name = trim((string)($_POST['name'] ?? ''));
    $email = strtolower(trim((string)($_POST['email'] ?? '')));
    $password = (string)($_POST['password'] ?? '');
    $role = (string)($_POST['role'] ?? 'client');

    if ($name === '' || !filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($password) < 6 || !in_array($role, $allowedRoles, true)) {
        $error = 'Enter a name, valid email, role, and password of at least 6 characters.';
    } else {
        $file = DATA_DIR . '/users.json';
        $users = json_read($file);
        foreach ($users as $user) {
            if (($user['email'] ?? '') === $email) {
                $error = 'An account already exists for this email.';
                break;
            }
        }
        if (!isset($error)) {
            $users[] = [
                'id' => 'usr_' . bin2hex(random_bytes(5)),
                'name' => $name,
                'email' => $email,
                'role' => $role,
                'onboarding_complete' => false,
                'password_hash' => password_hash($password, PASSWORD_DEFAULT),
                'created_at' => date(DATE_ATOM)
            ];
            json_write($file, $users);
            $_SESSION['user_email'] = $email;
            $_SESSION['user_name'] = $name;
            $_SESSION['user_role'] = $role;
            redirect('onboarding.php');
        }
    }
}
$pageTitle = 'Choose your path — Beyond Tattoo';
require __DIR__ . '/includes/header.php';
?>
<div class="auth-wrap role-auth">
  <div class="auth-card role-card-wide">
    <a class="brand" href="index.php"><img class="brand-icon" src="../assets/icons/beyond-tattoo-192.webp" alt=""><span>BEYOND TATTOO</span></a>
    <span class="eyebrow">Step 1 of 3</span>
    <h1>How will you use Beyond Tattoo?</h1>
    <p class="small">Choose your main account type. You can connect with the other sides of the marketplace later.</p>
    <?php if (isset($error)): ?><div class="notice error-notice"><?= e($error) ?></div><?php endif; ?>
    <form class="form-grid" method="post">
      <input type="hidden" name="_csrf" value="<?= e(bt_csrf_token()) ?>">
      <div class="role-picker">
        <label class="role-option <?= $selectedRole === 'client' ? 'selected' : '' ?>">
          <input type="radio" name="role" value="client" <?= $selectedRole === 'client' ? 'checked' : '' ?>>
          <span class="role-symbol">🖼️</span><strong>Canvas</strong><small>Discover artists, collect artwork, request consultations, book tattoos and track healing.</small>
        </label>
        <label class="role-option <?= $selectedRole === 'artist' ? 'selected' : '' ?>">
          <input type="radio" name="role" value="artist" <?= $selectedRole === 'artist' ? 'checked' : '' ?>>
          <span class="role-symbol">🎨</span><strong>Artist</strong><small>Build a portfolio, receive opportunities and manage appointments.</small>
        </label>
        <label class="role-option <?= $selectedRole === 'owner' ? 'selected' : '' ?>">
          <input type="radio" name="role" value="owner" <?= $selectedRole === 'owner' ? 'checked' : '' ?>>
          <span class="role-symbol">🏪</span><strong>Studio owner</strong><small>Manage your studio, team and hire artists through the app.</small>
        </label>
      </div>
      <div class="form-grid two">
        <input class="input" name="name" placeholder="Full name" required value="<?= e($_POST['name'] ?? '') ?>">
        <input class="input" type="email" name="email" placeholder="Email address" required value="<?= e($_POST['email'] ?? '') ?>">
      </div>
      <input class="input" type="password" name="password" placeholder="Create password" minlength="6" required>
      <button class="btn btn-primary" type="submit">Continue setup →</button>
    </form>
    <p class="small">Already registered? <a href="login.php">Log in</a></p>
  </div>
</div>
<?php require __DIR__ . '/includes/footer.php'; ?>
