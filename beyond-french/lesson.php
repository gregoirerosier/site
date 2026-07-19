<?php
require __DIR__ . '/includes/functions.php';
$id = (int)($_GET['id'] ?? 0);
$lesson = lesson_by_id($id);
if (!$lesson) {
    http_response_code(404);
    exit('Lesson not found.');
}
$userId=(int)($_SESSION['user_id']??0);french_mark_started($userId,$id);$position=lesson_position($id);
$pageTitle = $lesson['english'] . ' | Beyond French';
require __DIR__ . '/includes/header.php';
?>
<section class="section page-top">
    <a class="back-link" href="archive.php">← Back to lessons</a>
    <div class="lesson-progress-label" style="display:inline-flex;margin-bottom:12px;padding:8px 11px;border-radius:999px;color:#fff;background:#1768ff;font-size:12px;font-weight:900">Module <?= (int)$position['module'] ?> · <?= h($position['module_title']) ?> · Lesson <?= (int)$position['lesson'] ?></div>
    <span class="eyebrow"><?= h($lesson['category']) ?></span>
    <h1><?= h($lesson['english']) ?></h1>
    <article class="lesson-card">
        <div class="translation-grid">
            <div class="translation"><span class="flag">🇫🇷</span><small>Français</small><strong><?= h($lesson['french']) ?></strong><em><?= h($lesson['french_pronunciation']) ?></em></div>
            <div class="translation"><span class="flag">🇯🇲</span><small>Patois</small><strong><?= h($lesson['patois']) ?></strong></div>
            <div class="translation"><span class="flag">🇭🇹</span><small>Kreyòl</small><strong><?= h($lesson['kreyol']) ?></strong></div>
            <div class="translation"><span class="flag">🇪🇸</span><small>Español</small><strong><?= h($lesson['spanish']) ?></strong></div>
        </div>
        <div class="culture-note"><strong>💡 Culture note:</strong> <?= h($lesson['culture_note']) ?></div>
        <a class="button primary" href="challenge.php?id=<?= (int)$lesson['id'] ?>">Take this challenge</a>
    </article>
</section>
<?php require __DIR__ . '/includes/footer.php'; ?>
