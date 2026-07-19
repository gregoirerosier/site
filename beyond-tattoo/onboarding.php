<?php
require __DIR__ . '/includes/config.php';
require_login();
$usersFile = DATA_DIR . '/users.json';
$users = json_read($usersFile);
$userIndex = null;
$user = null;
foreach ($users as $i => $candidate) {
    if (($candidate['email'] ?? '') === current_user_email()) { $userIndex = $i; $user = $candidate; break; }
}
$role = (string)($user['role'] ?? $_SESSION['user_role'] ?? 'client');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $userIndex !== null) {
    bt_require_csrf();
    $profile = [
        'city' => trim((string)($_POST['city'] ?? '')),
        'bio' => trim((string)($_POST['bio'] ?? '')),
        'styles' => trim((string)($_POST['styles'] ?? '')),
        'experience' => trim((string)($_POST['experience'] ?? '')),
        'studio_name' => trim((string)($_POST['studio_name'] ?? '')),
        'budget' => trim((string)($_POST['budget'] ?? '')),
        'availability' => trim((string)($_POST['availability'] ?? '')),
    ];
    $users[$userIndex]['profile'] = $profile;
    $users[$userIndex]['onboarding_complete'] = true;
    json_write($usersFile, $users);
    flash('success', 'Your Beyond Tattoo profile is ready.');
    redirect('dashboard.php');
}
$pageTitle = 'Set up profile — Beyond Tattoo';
require __DIR__ . '/includes/header.php';
?>
<div class="auth-wrap onboarding-wrap">
  <div class="auth-card role-card-wide">
    <span class="eyebrow">Step 2 of 3 • <?= e(($role === 'client' ? 'Canvas' : ucfirst($role))) ?> setup</span>
    <h1>Make your profile useful from day one.</h1>
    <p class="small">These details power recommendations, studio matches and hiring.</p>
    <form class="form-grid" method="post">
      <input type="hidden" name="_csrf" value="<?= e(bt_csrf_token()) ?>">
      <input class="input" name="city" placeholder="City or service area" required>
      <?php if ($role === 'artist'): ?>
        <input class="input" name="styles" placeholder="Styles — realism, fine line, blackwork…" required>
        <input class="input" name="experience" placeholder="Years of experience">
        <input class="input" name="availability" placeholder="Availability — guest spots, full-time, weekends…">
        <textarea class="input" name="bio" rows="4" placeholder="Artist bio and what makes your work distinct"></textarea>
      <?php elseif ($role === 'owner'): ?>
        <input class="input" name="studio_name" placeholder="Studio name" required>
        <input class="input" name="styles" placeholder="Styles your studio is known for">
        <input class="input" name="availability" placeholder="Hiring needs — resident, guest, apprentice…">
        <textarea class="input" name="bio" rows="4" placeholder="Tell artists about the studio, culture and opportunities"></textarea>
      <?php else: ?>
        <input class="input" name="styles" placeholder="Tattoo styles you like">
        <input class="input" name="budget" placeholder="Typical project budget (optional)">
        <textarea class="input" name="bio" rows="4" placeholder="Ideas, placements or goals for your next tattoo"></textarea>
      <?php endif; ?>
      <button class="btn btn-primary" type="submit">Finish setup →</button>
    </form>
  </div>
</div>
<?php require __DIR__ . '/includes/footer.php'; ?>
