<?php
$pageTitle = 'Welcome to the Beta | Beyond French';
require __DIR__ . '/includes/header.php';
$status = $_GET['status'] ?? 'new';
?>
<section class="section page-top center">
    <span class="big-icon">🎓</span>
    <span class="eyebrow">BETA ACCESS</span>
    <h1><?= $status === 'existing' ? 'You’re already on the list.' : 'Welcome to Beyond French.' ?></h1>
    <p><?= $status === 'existing' ? 'We already have that email saved.' : 'Your beta signup has been saved successfully.' ?></p>
    <a class="button primary" href="index.php">Return home</a>
</section>
<?php require __DIR__ . '/includes/footer.php'; ?>
