<?php
require __DIR__ . '/includes/config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('index.php#beta');
}
bt_require_csrf();

$name = trim((string)($_POST['name'] ?? ''));
$email = strtolower(trim((string)($_POST['email'] ?? '')));
$interest = trim((string)($_POST['interest'] ?? 'all'));

if ($name === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    flash('success', 'Please enter a valid name and email address.');
    redirect('index.php#beta');
}

$file = DATA_DIR . '/beta-signups.json';
$signups = json_read($file);

foreach ($signups as $signup) {
    if (($signup['email'] ?? '') === $email) {
        flash('success', 'You are already on the beta list. We will keep you posted.');
        redirect('index.php#beta');
    }
}

$signups[] = [
    'name' => $name,
    'email' => $email,
    'interest' => $interest,
    'created_at' => date(DATE_ATOM)
];

json_write($file, $signups);
flash('success', 'You are on the list — welcome to the Beyond Tattoo beta.');
redirect('index.php#beta');
