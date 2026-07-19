<?php
declare(strict_types=1);
require_once __DIR__ . '/../includes/app-layout.php';
$wallet = beyond_nav_bootstrap('Beyond App Store', ['balance'=>0,'currency'=>'BITS','status'=>'guest']);
?>
<!doctype html><html lang="en"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1,viewport-fit=cover"><meta name="theme-color" content="#050817"><title>Beyond App Store | Beyond OS Beta Build 2.1.1</title><meta name="description" content="Find and try every app in the Beyond ecosystem."><link rel="manifest" href="<?=e(beyond_url('manifest.webmanifest'))?>"><link rel="stylesheet" href="<?=e(beyond_url('assets/css/bos-21.css'))?>"></head><body class="bos-page">
<main class="bos-main app-store-main">
  <section class="bos-hero app-store-hero">
    <span class="bos-kicker">Beta Build 2.1.1</span>
    <h1>Every Beyond app.<br>One store.</h1>
    <p>Browse the connected tools for living, learning, earning and exploring. Apps open directly—there is nothing extra to install.</p>
    <div class="bos-actions"><a class="bos-btn" href="#featured">Browse featured apps</a><a class="bos-btn secondary" href="<?=e(beyond_url('beyond-id/dashboard/'))?>">My Beyond ID</a></div>
  </section>

  <section class="bos-section" id="featured">
    <span class="bos-kicker">Live</span><h2>Health & daily life</h2><p>Daily guidance, wellness and creative self-expression.</p>
    <div class="bos-grid">
      <?=bos_app_card('Daily Breath','Daily verse, reflection and breathing practice.','dailybreath/bible.php?preview=1','☀','Live demo')?>
      <?=bos_app_card('Beyond Health','Health tools for mind, body and everyday care.','app-store/demo.php?app=health','♥','Try demo')?>
      <?=bos_app_card('Beyond Tattoo','AI-assisted tattoo ideas and daily stencils.','app-store/demo.php?app=tattoo','✦','Try demo')?>
      <?=bos_app_card('Baby Names','Explore names, origins and meanings.','app-store/demo.php?app=baby-names','◉','Try demo')?>
    </div>
  </section>

  <section class="bos-section">
    <span class="bos-kicker">Learn</span><h2>Education & discovery</h2><p>Languages, numbers, history and the universe.</p>
    <div class="bos-grid">
      <?=bos_app_card('Beyond French','French phrase of the day and language practice.','beyond-french/','FR','Live demo')?>
      <?=bos_app_card('Beyond Math','Practice and explore mathematics.','app-store/demo.php?app=math','∑','Try demo')?>
      <?=bos_app_card('Beyond Ancient','Explore ancient worlds and civilizations.','app-store/demo.php?app=ancient','𓂀','Try demo')?>
      <?=bos_app_card('Beyond Space','Launch into astronomy and space discovery.','app-store/demo.php?app=space','★','Try demo')?>
      <?=bos_app_card('Beyond Preschool','Early learning activities for young minds.','app-store/demo.php?app=preschool','ABC','Try demo')?>
    </div>
  </section>

  <section class="bos-section">
    <span class="bos-kicker">Earn</span><h2>Wallet, market & work</h2><p>Manage bit$, shop assets, follow markets and build income.</p>
    <div class="bos-grid">
      <?=bos_app_card('Beyond Wallet','Spend bit$, review activity and manage payouts.','app-store/demo.php?app=wallet','¤','Try demo')?>
      <?=bos_app_card('Beyond Market','Preview and buy creator assets with bit$.','beyond-market/','▦','Live demo')?>
      <?=bos_app_card('Beyond Investing','Live Bitcoin prices in CAD and USD with bit$ context.','app-store/demo.php?app=investing','₿','Try demo')?>
      <?=bos_app_card('Beyond Sell','List digital and physical products.','app-store/demo.php?app=sell','$','Try demo')?>
      <?=bos_app_card('Beyond Careers','Find opportunities across the ecosystem.','app-store/demo.php?app=careers','↗','Try demo')?>
    </div>
  </section>

  <section class="bos-section">
    <span class="bos-kicker">Explore</span><h2>Entertainment & creation</h2><p>Watch, listen, create and play across Beyond.</p>
    <div class="bos-grid">
      <?=bos_app_card('Beyond TV','Eight live channels and an on-demand catalogue.','beyond-tv/','▶','Live demo')?>
      <?=bos_app_card('Beyond Audio','Listen across the Beyond universe.','app-store/demo.php?app=audio','♫','Try demo')?>
      <?=bos_app_card('Beyond Canvas','Create visual work and share ideas.','app-store/demo.php?app=canvas','◫','Try demo')?>
      <?=bos_app_card('Beyond Skate','Skate culture, media and community.','app-store/demo.php?app=skate','◢','Try demo')?>
    </div>
  </section>
</main>
<style>
.app-store-main{width:min(1320px,calc(100% - 28px))}.app-store-hero{background:radial-gradient(circle at 90% 8%,rgba(68,140,255,.35),transparent 28%),linear-gradient(135deg,#10163b,#261150 58%,#3c1036)}.app-store-hero h1{max-width:900px}.app-store-main .bos-section{scroll-margin-top:88px}.app-store-main .bos-section>p{max-width:680px}.app-store-main .bos-card{min-height:154px}.app-store-main .bos-card-icon{font-size:19px;font-weight:950}
</style>
<?php bos_page_end(); ?>
