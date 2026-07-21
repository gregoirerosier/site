<?php
declare(strict_types=1);
require_once __DIR__ . '/../includes/app-layout.php';
$method = (string) ($_GET['method'] ?? 'stripe');
if ($method !== 'free') require_beyond_id();
$id = max(0, (int) ($_GET['id'] ?? 0));
$listing = null;
$asset = null;
try {
    $pdo = beyond_db();
    $statement = $pdo->prepare("SELECT id,title,price_cash,price_bits,currency,status FROM listings WHERE id=? AND status='active' LIMIT 1");
    $statement->execute([$id]);
    $listing = $statement->fetch() ?: null;
    if ($listing) {
        $assetStatement = $pdo->prepare('SELECT file_name,file_path FROM digital_assets WHERE listing_id=? ORDER BY id LIMIT 1');
        $assetStatement->execute([$id]);
        $asset = $assetStatement->fetch() ?: null;
    }
} catch (Throwable $exception) {
    $listing = null;
}
$isFree = $listing && (float) ($listing['price_cash'] ?? 0) <= 0 && (int) ($listing['price_bits'] ?? 0) <= 0;
$wallet = bos_page_start('Beyond Market', 'Checkout', 'Secure marketplace checkout.');
?>
<main class="bos-main checkout-main">
<?php if (!$listing): ?>
  <section class="bos-hero checkout-hero"><span class="bos-kicker">Beyond Market</span><h1>Item unavailable.</h1><p>This listing cannot be checked out right now.</p><a class="bos-btn" href="<?=e(beyond_url('beyond-market/'))?>">Return to market</a></section>
<?php elseif ($method === 'free' && $isFree): ?>
  <section class="bos-hero checkout-hero free"><span class="bos-kicker">Free creator download</span><h1><?=e((string)$listing['title'])?></h1><p>No payment is required for this community listing.</p><?php if($asset):?><div class="bos-actions"><a class="bos-btn" href="<?=e((string)$asset['file_path'])?>" target="_blank" rel="noopener">Download <?=e((string)$asset['file_name'])?></a><a class="bos-btn secondary" href="<?=e(beyond_url('beyond-market/'))?>">Keep browsing</a></div><?php else:?><div class="bos-notice">The seller published the listing and is still attaching its download. Check back shortly.</div><?php endif;?></section>
<?php else: ?>
  <section class="bos-hero checkout-hero"><span class="bos-kicker">Secure checkout</span><h1><?=e($method==='bits'?'Pay with bit$':'Stripe Checkout')?></h1><p><?=e((string)$listing['title'])?> · <?php if($method==='bits'):?><?=number_format((int)$listing['price_bits'])?> bit$<?php else:?>$<?=number_format((float)$listing['price_cash'],2)?> <?=e((string)$listing['currency'])?><?php endif;?></p><div class="bos-notice">Beta safety: the order is fulfilled only after a successful wallet transaction or verified Stripe webhook. Payment activation requires the production checkout service.</div><div class="bos-actions"><a class="bos-btn secondary" href="<?=e(beyond_url('beyond-sell/listing.php?id='.$id))?>">Return to listing</a></div></section>
<?php endif; ?>
</main>
<style>.checkout-main{width:min(980px,calc(100% - 28px));padding-bottom:60px}.checkout-hero{background:radial-gradient(circle at 85% 15%,rgba(114,87,228,.3),transparent 28%),linear-gradient(135deg,#121638,#30204f 58%,#421e42)}.checkout-hero.free{background:radial-gradient(circle at 85% 15%,rgba(65,224,169,.28),transparent 28%),linear-gradient(135deg,#101c34,#17374a 58%,#194436)}.checkout-hero .bos-notice{margin:22px 0}</style>
<?php bos_page_end(); ?>
