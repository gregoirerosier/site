<?php
declare(strict_types=1);
require_once __DIR__ . '/../../includes/ecosystem.php';
header('Content-Type: application/json; charset=utf-8');
if (empty($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['ok'=>false,'message'=>'Sign in to earn 25 bit$. The stencil remains free.']);
    exit;
}
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['ok'=>false,'message'=>'POST required']);
    exit;
}
$token = (string)($_POST['csrf'] ?? '');
if (empty($_SESSION['stencil_csrf']) || !hash_equals((string)$_SESSION['stencil_csrf'], $token)) {
    http_response_code(403);
    echo json_encode(['ok'=>false,'message'=>'Security token expired. Refresh and try again.']);
    exit;
}
$userId = (int)$_SESSION['user_id'];
$dateKey = date('Y-m-d');
$idempotency = 'stencil-of-day:' . $dateKey . ':user:' . $userId;
try {
    $pdo = beyond_db();
    $pdo->beginTransaction();
    $insertWallet = $pdo->getAttribute(PDO::ATTR_DRIVER_NAME) === 'sqlite'
        ? "INSERT OR IGNORE INTO beyond_wallets(user_id,balance,currency,status) VALUES(?,0,'BITS','active')"
        : "INSERT IGNORE INTO beyond_wallets(user_id,balance,currency,status) VALUES(?,0,'BITS','active')";
    $pdo->prepare($insertWallet)->execute([$userId]);
    $stmt = $pdo->prepare('SELECT id,balance FROM beyond_wallets WHERE user_id=? LIMIT 1');
    $stmt->execute([$userId]);
    $wallet = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$wallet) throw new RuntimeException('Wallet unavailable.');
    $check = $pdo->prepare('SELECT id FROM beyond_wallet_transactions WHERE idempotency_key=? LIMIT 1');
    $check->execute([$idempotency]);
    if ($check->fetchColumn()) {
        $pdo->commit();
        echo json_encode(['ok'=>true,'awarded'=>false,'balance'=>(float)$wallet['balance'],'message'=>'Today’s 25 bit$ were already added.']);
        exit;
    }
    $tx = $pdo->prepare("INSERT INTO beyond_wallet_transactions(wallet_id,amount,type,app_slug,description,idempotency_key) VALUES(?,25,'credit','beyond-tattoo',?,?)");
    $tx->execute([(int)$wallet['id'], 'Stencil of the Day reward — ' . $dateKey, $idempotency]);
    $pdo->prepare('UPDATE beyond_wallets SET balance=balance+25 WHERE id=?')->execute([(int)$wallet['id']]);
    $pdo->commit();
    echo json_encode(['ok'=>true,'awarded'=>true,'balance'=>(float)$wallet['balance']+25,'message'=>'+25 bit$ added to your Beyond Wallet.']);
} catch (Throwable $e) {
    if (isset($pdo) && $pdo instanceof PDO && $pdo->inTransaction()) $pdo->rollBack();
    error_log('Stencil reward failed: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode(['ok'=>false,'message'=>'The stencil downloaded, but the reward could not be added right now.']);
}
