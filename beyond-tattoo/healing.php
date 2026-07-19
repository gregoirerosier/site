<?php
declare(strict_types=1);
require __DIR__ . '/includes/config.php';
require_login();

$message = null;
$error = null;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $photo = $_FILES['photo'] ?? null;
    if (!is_array($photo) || ($photo['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) {
        $error = 'Choose an image to upload.';
    } elseif ((int)($photo['size'] ?? 0) < 1 || (int)$photo['size'] > 10 * 1024 * 1024) {
        $error = 'The image must be smaller than 10 MB.';
    } elseif (!is_uploaded_file((string)$photo['tmp_name'])) {
        $error = 'The upload could not be verified.';
    } else {
        $finfo = new finfo(FILEINFO_MIME_TYPE);
        $mime = (string)$finfo->file((string)$photo['tmp_name']);
        $allowed = ['image/jpeg'=>'jpg', 'image/png'=>'png', 'image/webp'=>'webp'];
        $dimensions = @getimagesize((string)$photo['tmp_name']);
        if (!isset($allowed[$mime]) || $dimensions === false || ($dimensions['mime'] ?? '') !== $mime) {
            $error = 'Only valid JPEG, PNG, or WebP images are accepted.';
        } elseif ((int)$dimensions[0] > 12000 || (int)$dimensions[1] > 12000) {
            $error = 'The image dimensions are too large.';
        } else {
            $owner = current_user_email() !== '' ? strtolower(current_user_email()) : 'user:'.(string)($_SESSION['user_id'] ?? 'unknown');
            $ownerKey = hash('sha256', $owner);
            $ownerDir = UPLOAD_DIR . '/' . $ownerKey;
            if (!is_dir($ownerDir) && !mkdir($ownerDir, 0750, true) && !is_dir($ownerDir)) {
                $error = 'Private upload storage is unavailable.';
            } else {
                $name = gmdate('Ymd-His') . '-' . bin2hex(random_bytes(12)) . '.' . $allowed[$mime];
                if (!move_uploaded_file((string)$photo['tmp_name'], $ownerDir . '/' . $name)) {
                    $error = 'The image could not be stored.';
                } else {
                    $entries = json_read(HEALING_DATA_FILE);
                    $entries[] = [
                        'id' => 'heal_' . bin2hex(random_bytes(8)),
                        'owner_key' => $ownerKey,
                        'file' => $ownerKey . '/' . $name,
                        'mime' => $mime,
                        'bytes' => (int)$photo['size'],
                        'width' => (int)$dimensions[0],
                        'height' => (int)$dimensions[1],
                        'notes' => mb_substr(trim((string)($_POST['notes'] ?? '')), 0, 2000),
                        'created_at' => gmdate(DATE_ATOM),
                    ];
                    if (!is_dir(dirname(HEALING_DATA_FILE))) mkdir(dirname(HEALING_DATA_FILE), 0750, true);
                    if (!json_write(HEALING_DATA_FILE, $entries)) {
                        @unlink($ownerDir . '/' . $name);
                        $error = 'The healing entry could not be saved.';
                    } else {
                        $message = 'Healing entry saved privately.';
                    }
                }
            }
        }
    }
}
$pageTitle = 'Healing Journal — Beyond Tattoo';
require __DIR__ . '/includes/header.php';
?>
<div class="app-shell"><header class="app-header"><div class="container app-header-inner"><a class="brand" href="dashboard.php"><span class="brand-badge">B</span><span>Healing Journal</span></a></div></header>
<main class="container dashboard"><div class="panel"><h2>Upload today's photo</h2><p class="meta">JPEG, PNG or WebP up to 10 MB. Photos are stored in private account storage.</p><?php if($message): ?><div class="notice"><?= e($message) ?></div><?php endif; ?><?php if($error): ?><div class="notice error-notice"><?= e($error) ?></div><?php endif; ?><form class="form-grid" method="post" enctype="multipart/form-data"><input type="hidden" name="_csrf" value="<?= e(bt_csrf_token()) ?>"><input class="input" type="file" name="photo" accept="image/jpeg,image/png,image/webp" required><textarea class="input" name="notes" maxlength="2000" placeholder="How does it feel today?"></textarea><button class="btn btn-primary" type="submit">Save healing entry</button></form></div></main></div>
<?php require __DIR__ . '/includes/footer.php'; ?>
