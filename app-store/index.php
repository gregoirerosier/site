<?php
declare(strict_types=1);
require_once __DIR__ . '/../includes/app-layout.php';
$wallet = beyond_nav_bootstrap('Beyond App Store');
?>
<!doctype html><html lang="en"><head><script>(function(){try{var t=localStorage.getItem('beyond-theme');document.documentElement.dataset.theme=['dark','light','sunset'].includes(t)?t:'sunset';}catch(e){document.documentElement.dataset.theme='sunset';}})();</script><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1,viewport-fit=cover"><meta name="theme-color" content="#32113d"><title>Beyond App Store | Beyond OS 2.2.1</title><meta name="description" content="Find and try every app in the Beyond ecosystem."><link rel="manifest" href="<?=e(beyond_url('manifest.webmanifest'))?>"><link rel="stylesheet" href="<?=e(beyond_url('assets/css/bos-21.css'))?>"></head><body class="bos-page">
<main class="bos-main app-store-main">
  <section class="bos-hero app-store-hero">
    <span class="bos-kicker">Patch 2.2.1</span>
    <h1>Every Beyond app.<br>One store.</h1>
    <p>Browse the connected tools for living, learning, earning and exploring. Apps open directly—there is nothing extra to install.</p>
    <div class="bos-actions"><a class="bos-btn" href="<?=e(beyond_url('beyond-finance/'))?>">Open Wallet</a><a class="bos-btn secondary" href="<?=e(beyond_url('beyond-market/'))?>">Explore Marketplace</a><a class="bos-btn secondary" href="#featured">Browse all apps</a></div>
  </section>

  <section class="bos-section" id="featured">
    <span class="bos-kicker">Live</span><h2>Health & daily life</h2><p>Daily guidance, wellness and creative self-expression.</p>
    <div class="bos-grid">
      <?=bos_app_card('Daily Breath','Daily verse, reflection and breathing practice.','dailybreath/bible.php?preview=1','DB','Live demo','@atom')?>
      <?=bos_app_card('Beyond Health','Health tools for mind, body and everyday care.','app-store/demo.php?app=health','BH','Try demo','@atom')?>
      <?=bos_app_card('Beyond Tattoo','AI-assisted tattoo ideas and daily stencils.','app-store/demo.php?app=tattoo','BT','Try demo','beyond-tattoo/assets/img/beyond-tattoo-logo.webp')?>
      <?=bos_app_card('Baby Names','Explore names, origins and meanings.','beyond-baby-names/','BN','Open app','@atom')?>
    </div>
  </section>

  <section class="bos-section">
    <span class="bos-kicker">Learn</span><h2>Education & discovery</h2><p>Languages, numbers, history and the universe.</p>
    <div class="bos-grid">
      <?=bos_app_card('Beyond French','French phrase of the day and language practice.','beyond-french/','BF','Live demo','beyond-french/assets/images/beyond-french-logo.webp')?>
      <?=bos_app_card('Beyond Math','Practice and explore mathematics.','app-store/demo.php?app=math','BM','Try demo','beyond-math/assets/img/beyond-math-logo.webp')?>
      <?=bos_app_card('Beyond Ancient','Explore ancient worlds and civilizations.','app-store/demo.php?app=ancient','BA','Try demo','@atom')?>
      <?=bos_app_card('Beyond Space','Launch into astronomy and space discovery.','app-store/demo.php?app=space','BS','Try demo','beyond-space/beyond-space-v1/assets/img/beyond-space-logo.webp')?>
      <?=bos_app_card('Beyond Preschool','Early learning activities for young minds.','app-store/demo.php?app=preschool','BP','Try demo','@atom')?>
    </div>
  </section>

  <section class="bos-section">
    <span class="bos-kicker">Earn</span><h2>Wallet, market & work</h2><p>Manage bit$, shop assets, follow markets and build income.</p>
    <div class="bos-grid">
      <?=bos_app_card('Beyond Wallet','Spend bit$, review activity and manage your Beyond finances.','beyond-finance/','BW','Open Wallet','@atom')?>
      <?=bos_app_card('Beyond Marketplace','Discover and buy creator assets with bit$.','beyond-market/','MP','Open Marketplace','@atom')?>
      <?=bos_app_card('Beyond Investing','Live Bitcoin prices in CAD and USD with bit$ context.','app-store/demo.php?app=investing','BI','Try demo','@atom')?>
      <?=bos_app_card('Beyond Sell','List digital and physical products.','app-store/demo.php?app=sell','SELL','Try demo','@atom')?>
      <?=bos_app_card('Beyond Careers','Find opportunities across the ecosystem.','app-store/demo.php?app=careers','BC','Try demo','@atom')?>
    </div>
  </section>

  <section class="bos-section">
    <span class="bos-kicker">Explore</span><h2>Entertainment & creation</h2><p>Watch, listen, create and play across Beyond.</p>
    <div class="bos-grid">
      <?=bos_app_card('Beyond TV','Eight live channels and an on-demand catalogue.','beyond-tv/','TV','Live demo','beyond-tv/assets/img/beyond-tv-logo.webp')?>
      <?=bos_app_card('Beyond Audio','Listen across the Beyond universe.','app-store/demo.php?app=audio','BA','Try demo','@atom')?>
      <?=bos_app_card('Beyond Canvas','Create visual work and share ideas.','app-store/demo.php?app=canvas','CAN','Try demo','@atom')?>
      <?=bos_app_card('Beyond Skate','Skate culture, media and community.','app-store/demo.php?app=skate','SK8','Try demo','@atom')?>
      <?=bos_app_card('Beyond Casino — Social Play','Demo bit$ games for entertainment only. No purchase necessary and no cash value.','beyond-casino/','BC','Play demo','@atom')?>
    </div>
  </section>
</main>
<style>
.app-store-main{width:min(1320px,calc(100% - 28px))}.app-store-hero{background:radial-gradient(circle at 90% 8%,rgba(68,140,255,.35),transparent 28%),linear-gradient(135deg,#10163b,#261150 58%,#3c1036)}.app-store-hero h1{max-width:900px}.app-store-main .bos-section{scroll-margin-top:88px}.app-store-main .bos-section>p{max-width:680px}.app-store-main .bos-card{min-height:154px}.app-store-main .bos-card-icon{width:58px;height:58px;font-size:19px;font-weight:950}
@media(max-width:560px){.app-store-main{width:min(100% - 18px,1320px)}.app-store-hero{padding:30px 18px}.app-store-hero h1{font-size:clamp(2.45rem,13vw,4rem)}.app-store-main .bos-card{min-height:128px}.app-store-main .bos-actions{display:grid;grid-template-columns:1fr}.app-store-main .bos-btn{width:100%}}html[data-theme="sunset"]{--bos-bg:#1a0d24;--bos-panel:#32133f;--bos-line:rgba(255,204,176,.2);--bos-text:#fff7f2;--bos-muted:#e5bdb5;--bos-purple:#ff8a62;--bos-pink:#c44c88}html[data-theme="sunset"] .bos-page{background:radial-gradient(circle at 80% 0,rgba(255,111,97,.3),transparent 30%),radial-gradient(circle at 12% 35%,rgba(255,179,71,.16),transparent 34%),linear-gradient(180deg,#32113d,#1d102b 48%,#0d1021)}html[data-theme="sunset"] .app-store-hero{background:radial-gradient(circle at 88% 8%,rgba(255,179,71,.28),transparent 30%),linear-gradient(135deg,#5f214e,#3a183f 58%,#27162f)}html[data-theme="sunset"] .bos-card{background:rgba(75,29,64,.76);border-color:rgba(255,204,176,.18)}html[data-theme="sunset"] .bos-btn.secondary{background:rgba(103,40,72,.56)}</style>
<?php bos_page_end(); ?>
