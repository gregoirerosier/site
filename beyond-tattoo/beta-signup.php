<?php
declare(strict_types=1);
require __DIR__ . '/includes/config.php';
if ($_SERVER['REQUEST_METHOD'] !== 'POST') redirect('index.php#beta');
bt_require_csrf();
$name=mb_substr(trim((string)($_POST['name']??'')),0,200); $email=strtolower(trim((string)($_POST['email']??''))); $interest=mb_substr(trim((string)($_POST['interest']??'all')),0,100);
if($name===''||!filter_var($email,FILTER_VALIDATE_EMAIL)){flash('success','Please enter a valid name and email address.');redirect('index.php#beta');}
$added=bt_add_beta_signup($name,$email,$interest);
flash('success',$added?'You are on the list — welcome to the Beyond Tattoo beta.':'You are already on the beta list. We will keep you posted.');
redirect('index.php#beta');
