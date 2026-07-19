<?php
declare(strict_types=1);

require_once __DIR__ . '/mail.php';

/**
 * Notify the operating team when a new Beyond ID is created.
 * Delivery failures are logged and never block account creation.
 */
function send_beyond_id_admin_signup_alert(array $user, string $source = 'Beyond ID'): bool
{
    $recipients = [
        'admin@beyondimagination.co.technology',
        'rosiergreg@gmail.com',
    ];

    $name = trim((string)($user['name'] ?? trim(((string)($user['first_name'] ?? '')) . ' ' . ((string)($user['last_name'] ?? '')))));
    $email = strtolower(trim((string)($user['email'] ?? '')));
    $userId = isset($user['id']) ? (int)$user['id'] : 0;
    $createdAt = (string)($user['created_at'] ?? date('Y-m-d H:i:s'));
    $safeName = htmlspecialchars($name ?: 'New member', ENT_QUOTES, 'UTF-8');
    $safeEmail = htmlspecialchars($email ?: 'Unavailable', ENT_QUOTES, 'UTF-8');
    $safeSource = htmlspecialchars($source, ENT_QUOTES, 'UTF-8');
    $safeCreated = htmlspecialchars($createdAt, ENT_QUOTES, 'UTF-8');
    $safeId = $userId > 0 ? (string)$userId : 'Pending';
    $adminUrl = 'https://beyondimagination.co.technology/admin/users.php';

    $subject = 'New Beyond ID signup: ' . ($name ?: $email ?: 'New member');
    $html = "<html><body style='margin:0;background:#08080d;color:#fff;font-family:Arial,sans-serif;padding:28px'>
      <div style='max-width:620px;margin:auto;background:#15151f;border:1px solid #34344a;border-radius:24px;padding:30px'>
        <p style='margin:0 0 10px;color:#a78bfa;font-weight:800;letter-spacing:.08em'>BEYOND ID ADMIN ALERT</p>
        <h1 style='margin:0 0 18px;font-size:28px'>A new member joined Beyond OS</h1>
        <table role='presentation' style='width:100%;border-collapse:collapse;color:#fff'>
          <tr><td style='padding:10px 0;color:#9ca3af'>Name</td><td style='padding:10px 0;text-align:right;font-weight:700'>{$safeName}</td></tr>
          <tr><td style='padding:10px 0;color:#9ca3af'>Email</td><td style='padding:10px 0;text-align:right;font-weight:700'>{$safeEmail}</td></tr>
          <tr><td style='padding:10px 0;color:#9ca3af'>User ID</td><td style='padding:10px 0;text-align:right;font-weight:700'>{$safeId}</td></tr>
          <tr><td style='padding:10px 0;color:#9ca3af'>Signup source</td><td style='padding:10px 0;text-align:right;font-weight:700'>{$safeSource}</td></tr>
          <tr><td style='padding:10px 0;color:#9ca3af'>Created</td><td style='padding:10px 0;text-align:right;font-weight:700'>{$safeCreated}</td></tr>
        </table>
        <p style='margin:24px 0 0'><a href='{$adminUrl}' style='display:inline-block;background:linear-gradient(100deg,#5b8cff,#a044f2,#e9449f);color:#fff;text-decoration:none;font-weight:800;padding:13px 20px;border-radius:999px'>Open user dashboard</a></p>
      </div>
    </body></html>";

    $allSent = true;
    foreach ($recipients as $recipient) {
        if (!smtp_send_html($recipient, $subject, $html, 'Beyond ID Alerts')) {
            $allSent = false;
            error_log('Beyond ID admin signup alert failed for ' . $recipient);
        }
    }
    return $allSent;
}
