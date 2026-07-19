<?php
declare(strict_types=1);
require_once __DIR__.'/../includes/ecosystem.php';
$wallet=beyond_app_bootstrap('DailyBreath');$pdo=beyond_db();require_once __DIR__.'/includes/academy-stripe.php';
if($_SERVER['REQUEST_METHOD']!=='POST'||!verify_csrf_token($_POST['csrf']??null)){http_response_code(403);exit('Invalid request.');}
$s=$pdo->prepare('SELECT provider_customer_id FROM academy_subscriptions WHERE user_id=? LIMIT 1');$s->execute([(int)$_SESSION['user_id']]);$customer=(string)$s->fetchColumn();if($customer==='')exit('No Stripe subscription was found.');
try{$portal=academy_stripe_request('POST','billing_portal/sessions',['customer'=>$customer,'return_url'=>academy_absolute_url('dailybreath/academy.php')]);header('Location: '.$portal['url']);exit;}catch(Throwable $e){http_response_code(503);exit(e($e->getMessage()));}
