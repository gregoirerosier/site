<?php
declare(strict_types=1);
require_once __DIR__ . '/../includes/app-layout.php';
$wallet = beyond_nav_bootstrap('Beyond Marketplace');
?>
<!doctype html><html lang="en"><head><script>(function(){try{var t=localStorage.getItem('beyond-theme');document.documentElement.dataset.theme=['dark','light','sunset'].includes(t)?t:'sunset';}catch(e){}})();</script><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1,viewport-fit=cover"><meta name="theme-color" content="#11152b"><title>Beyond Marketplace | Beyond OS</title><meta name="description" content="Discover creator tools, tattoo artwork, digital products and selling experiences across Beyond OS."><link rel="stylesheet" href="<?=e(beyond_url('assets/css/bos-21.css'))?>"></head><body class="bos-page">
<main class="bos-main market-main">
  <section class="bos-hero market-hero"><span class="bos-kicker">Beyond Marketplace</span><h1>Discover. Create.<br>Sell beyond.</h1><p>A working directory of creator products and commerce experiences already available across Beyond OS.</p><div class="bos-actions"><a class="bos-btn" href="<?=e(beyond_url('beyond-sell/'))?>">Start selling</a><a class="bos-btn secondary" href="<?=e(beyond_url('beyond-id/dashboard/wallet.php'))?>">Open Wallet</a></div></section>
  <section class="bos-section"><span class="bos-kicker">Shop & discover</span><h2>Creator marketplace</h2><p>Browse artwork, daily releases and creator services.</p><div class="bos-grid">
    <?=bos_app_card('Tattoo Stencil Library','Browse editable artwork, transfer files and studio-ready designs.','beyond-tattoo/stencils.php','INK','Browse library','assets/icons/app-store/beyond-tattoo.jpg')?>
    <?=bos_app_card('Stencil of the Day','Discover today’s featured tattoo artwork and downloadable formats.','beyond-tattoo/stencil-of-day.php','DAY','View release','assets/icons/app-store/beyond-tattoo.jpg')?>
    <?=bos_app_card('Find Tattoo Artists','Explore artists, studios and collaboration opportunities.','beyond-tattoo/studios.php','ART','Find artists','assets/icons/app-store/beyond-tattoo.jpg')?>
  </div></section>
  <section class="bos-section"><span class="bos-kicker">Create & earn</span><h2>Seller tools</h2><p>Turn skills and ideas into products for the Beyond ecosystem.</p><div class="bos-grid">
    <?=bos_app_card('Beyond Sell','List digital and physical products and manage your storefront.','beyond-sell/','SELL','Open seller tools','@atom')?>
    <?=bos_app_card('Graphic Design & SVG','Build scalable artwork, icons and brand systems.','coding-school/?age=graphic-design-svg','SVG','Start pathway','@atom')?>
    <?=bos_app_card('Beyond Wallet','Review bit$ rewards and marketplace activity.','beyond-id/dashboard/wallet.php','BW','Open Wallet','@atom')?>
  </div></section>
</main>
<style>.market-main{width:min(1240px,calc(100% - 28px))}.market-hero{background:radial-gradient(circle at 85% 15%,rgba(255,192,83,.25),transparent 28%),linear-gradient(135deg,#151a38,#30204e 55%,#48213f)}.market-main .bos-card-icon{width:62px;height:62px}</style>
<?php bos_page_end(); ?>
