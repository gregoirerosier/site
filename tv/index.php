<?php
declare(strict_types=1);
require_once __DIR__ . '/../includes/ecosystem.php';
require_once __DIR__ . '/../beyond-tv/includes/classic-schedule.php';
require_once __DIR__ . '/../beyond-tv/includes/beyond-cartoons-schedule.php';
require_once __DIR__ . '/../beyond-tv/includes/eight-channel-guide.php';

if (!empty($_SESSION['user_id'])) {
    beyond_track_app('Beyond TV');
}

$signedIn = !empty($_SESSION['user_id']);
$channels = json_decode((string)file_get_contents(__DIR__ . '/../beyond-tv/data/channels.json'), true) ?: [];
$classic = null;
$memberChannels = [];
$publicChannels = [];
foreach ($channels as $channel) {
    if (($channel['slug'] ?? '') === 'classic-cartoon-theater') {
        $classic = $channel;
    }
    if (($channel['access'] ?? 'member') === 'guest' && ($channel['release_status'] ?? '') === 'released' && ($channel['slug'] ?? '') !== 'classic-cartoon-theater') {
        $publicChannels[] = $channel;
    }
    if (($channel['access'] ?? 'member') === 'member' && ($channel['release_status'] ?? '') === 'released_member') {
        $memberChannels[] = $channel;
    }
}
if (is_array($classic)) {
    $classic['stream_endpoint'] = '/api/classic-live.php';
}
$classic ??= [
    'slug' => 'classic-cartoon-theater',
    'name' => 'Classic Cartoon Theater',
    'stream_endpoint' => '/api/classic-live.php',
    'icon' => '📺',
];
$schedule = beyond_classic_schedule_state();
$current = $schedule['current'];
$next = $schedule['next'];
$cartoonSchedule = beyond_cartoons_schedule_state();
$cartoonCurrent = $cartoonSchedule['current'];
$cartoonNext = $cartoonSchedule['next'];
$guideChannels = beyond_tv_eight_channel_guide($schedule, $cartoonSchedule);
$currentHour = (int)(new DateTimeImmutable('now', new DateTimeZone('America/Vancouver')))->format('G');
?>
<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1,viewport-fit=cover">
<meta name="theme-color" content="#32133f"><script>(function(){try{const t=localStorage.getItem('beyond-tv-theme');document.documentElement.dataset.tvTheme=['dark','light','sunset'].includes(t)?t:'sunset';}catch(e){document.documentElement.dataset.tvTheme='sunset';}})();</script>
<title>Beyond TV | 8 Channels</title>
<meta name="description" content="Explore eight Beyond TV channels across cartoons, space, ancient history, movies, French and health.">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
<link rel="stylesheet" href="/assets/css/app.css?v=2.2.0">
<style>
:root{color-scheme:dark}.classic-home{min-height:100vh;background:radial-gradient(circle at 50% -10%,#42234e 0,#15111d 35%,#080812 70%);color:#fff;font-family:Inter,system-ui,sans-serif}.classic-nav{position:sticky;top:0;z-index:20;display:flex;align-items:center;justify-content:space-between;gap:16px;padding:14px clamp(16px,4vw,44px);background:rgba(8,8,18,.88);backdrop-filter:blur(18px);border-bottom:1px solid rgba(255,255,255,.09)}.classic-brand{font-weight:900;letter-spacing:-.04em;font-size:1.15rem}.classic-nav-actions{display:flex;gap:9px;align-items:center}.classic-btn{display:inline-flex;align-items:center;justify-content:center;min-height:42px;padding:0 15px;border-radius:12px;text-decoration:none;font-weight:850;border:1px solid rgba(255,255,255,.14);color:#fff;background:rgba(255,255,255,.08)}.classic-btn.primary{background:#7659ef;border-color:#7659ef}.classic-shell{width:min(1180px,calc(100% - 28px));margin:auto}.classic-hero{padding:28px 0 18px}.classic-player-grid{display:grid;grid-template-columns:minmax(0,1.75fr) minmax(280px,.75fr);gap:18px;align-items:stretch}.classic-player{position:relative;aspect-ratio:16/9;overflow:hidden;border-radius:22px;background:#000;box-shadow:0 26px 70px rgba(0,0,0,.42);border:1px solid rgba(255,255,255,.1)}.classic-player video{width:100%;height:100%;object-fit:contain;background:#000}.classic-player .player-loading,.classic-player .player-fallback{position:absolute;inset:0;display:grid;place-items:center;background:#090912;color:#fff;text-align:center;padding:22px}.classic-player .player-fallback[hidden],.classic-player .player-loading[hidden]{display:none}.classic-player iframe{position:absolute;inset:0;width:100%;height:100%;border:0}.classic-player iframe[hidden]{display:none}.unmute-hint{position:absolute;left:14px;bottom:14px;z-index:3;border:0;border-radius:999px;padding:10px 13px;font-weight:800;background:rgba(0,0,0,.72);color:#fff}.classic-info{border-radius:22px;padding:24px;background:linear-gradient(145deg,rgba(84,42,91,.92),rgba(29,20,38,.96));border:1px solid rgba(255,255,255,.12);display:flex;flex-direction:column;justify-content:space-between}.classic-live{display:inline-flex;align-items:center;gap:7px;font-size:.75rem;font-weight:900;letter-spacing:.12em;color:#ffb1b1}.classic-live:before{content:"";width:8px;height:8px;border-radius:50%;background:#ff4141;box-shadow:0 0 0 5px rgba(255,65,65,.14)}.classic-info h1{font-size:clamp(2rem,4.5vw,4.2rem);letter-spacing:-.065em;line-height:.96;margin:14px 0}.classic-info p{color:#d8cfe0;line-height:1.55}.now-card{margin-top:18px;padding:16px;border-radius:16px;background:rgba(0,0,0,.22);border:1px solid rgba(255,255,255,.09)}.now-card small{display:block;color:#baaeca;font-weight:800;letter-spacing:.09em}.now-card strong{display:block;font-size:1.2rem;margin:6px 0}.clock-line{font-size:.85rem;color:#c9bdd3}.guide-section,.channels-section{padding:24px 0 38px}.section-head{display:flex;justify-content:space-between;gap:18px;align-items:end;margin-bottom:14px}.section-head h2{font-size:clamp(1.55rem,3vw,2.4rem);letter-spacing:-.045em;margin:4px 0}.kicker{font-size:.72rem;font-weight:900;letter-spacing:.13em;color:#bfa8ff}.guide-grid{display:grid;grid-template-columns:repeat(7,minmax(145px,1fr));gap:10px;overflow-x:auto;padding-bottom:6px}.guide-block{min-height:150px;padding:15px;border-radius:16px;background:rgba(255,255,255,.055);border:1px solid rgba(255,255,255,.09)}.guide-block.current{background:linear-gradient(145deg,#7557ec,#4b2f91);border-color:#a58dff}.guide-time{font-size:.75rem;color:#bdb5c8;font-weight:800}.guide-block.current .guide-time{color:#eee8ff}.guide-icon{font-size:1.45rem;margin:14px 0 9px}.guide-block strong{display:block}.guide-block p{font-size:.8rem;line-height:1.35;color:#bdb5c8;margin:7px 0 0}.guide-block.current p{color:#eee8ff}.channel-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(230px,1fr));gap:14px}.channel-card{display:block;text-decoration:none;color:#fff;border-radius:19px;padding:20px;min-height:170px;background:linear-gradient(145deg,#161522,#25213a);border:1px solid rgba(255,255,255,.09)}.channel-card.locked{position:relative;opacity:.94}.channel-card .icon{font-size:2rem}.channel-card h3{margin:25px 0 7px;font-size:1.25rem}.channel-card p{margin:0;color:#bdb5c8;font-size:.9rem}.lock-label{position:absolute;top:14px;right:14px;font-size:.7rem;font-weight:900;letter-spacing:.08em;background:rgba(0,0,0,.4);padding:7px 9px;border-radius:999px}.release-note{padding:20px;border-radius:18px;background:rgba(255,255,255,.05);border:1px solid rgba(255,255,255,.09);color:#c9c0d2}.classic-footer{padding:24px 0 44px;color:#92899d;font-size:.85rem}.classic-footer a{color:#cbbdff}.provider-status{margin-top:10px;color:#bfb5c8;font-size:.8rem}@media(max-width:850px){.classic-player-grid{grid-template-columns:1fr}.classic-info{min-height:unset}.guide-grid{grid-template-columns:repeat(7,180px)}.classic-nav-actions .secondary-label{display:none}}@media(max-width:520px){.classic-nav{padding:11px 14px}.classic-btn{min-height:39px;padding:0 12px;font-size:.82rem}.classic-hero{padding-top:16px}.classic-player{border-radius:15px}.classic-info{border-radius:17px;padding:19px}.classic-info h1{font-size:2.35rem}.section-head{align-items:start;flex-direction:column}.guide-section,.channels-section{padding-top:18px}}
.channel-switcher{display:grid;grid-template-columns:repeat(4,minmax(0,1fr));gap:12px;margin:18px 0 0}.channel-switch{display:flex;width:100%;text-align:left;cursor:pointer;font:inherit;align-items:center;justify-content:space-between;gap:14px;padding:15px 17px;border-radius:16px;text-decoration:none;color:#fff;border:1px solid rgba(255,255,255,.12);background:rgba(255,255,255,.055)}.channel-switch.active{border-color:#a58dff;box-shadow:0 0 0 1px rgba(165,141,255,.25) inset}.channel-switch strong{display:block}.channel-switch small{display:block;margin-top:4px;color:#c8bfd0}.live-dot{font-size:.72rem;font-weight:900;letter-spacing:.08em;color:#ffb1b1;white-space:nowrap}.guide-channel-head{display:flex;align-items:center;justify-content:space-between;gap:16px;margin:28px 0 12px}.guide-channel-head h3{margin:0;font-size:1.2rem}.guide-channel-head a{color:#d7caff;text-decoration:none;font-weight:800;font-size:.86rem}@media(max-width:900px){.channel-switcher{grid-template-columns:repeat(2,minmax(0,1fr))}}@media(max-width:620px){.channel-switcher{grid-template-columns:1fr}.channel-switch{padding:14px}}

/* Mobile-first Beyond TV UX pass */
.channel-switcher{scrollbar-width:none}
.channel-switcher::-webkit-scrollbar,.guide-grid::-webkit-scrollbar{display:none}
.channel-switch:focus-visible,.guide-block:focus-visible,.classic-btn:focus-visible{outline:3px solid #d7caff;outline-offset:3px}
.channel-switch{min-height:76px;touch-action:manipulation;-webkit-tap-highlight-color:transparent}
.guide-grid{grid-template-columns:repeat(8,minmax(145px,1fr));scroll-snap-type:x proximity;overscroll-behavior-inline:contain}
.guide-block{scroll-snap-align:start}

@media(max-width:620px){
  .classic-shell{width:min(100% - 24px,1180px)}
  .classic-nav{padding:10px 12px;gap:8px}
  .classic-brand{font-size:1.05rem;white-space:nowrap}
  .classic-nav-actions{gap:6px}
  .classic-btn{min-height:44px;padding:0 11px;border-radius:11px;font-size:.78rem}
  .channel-switcher{
    display:flex;
    gap:10px;
    overflow-x:auto;
    margin:12px -12px 0;
    padding:2px 12px 12px;
    scroll-snap-type:x mandatory;
    overscroll-behavior-inline:contain;
    -webkit-overflow-scrolling:touch;
  }
  .channel-switch{
    flex:0 0 min(78vw,286px);
    min-height:68px;
    padding:12px 13px;
    border-radius:14px;
    scroll-snap-align:start;
  }
  .channel-switch strong{font-size:.91rem;line-height:1.2}
  .channel-switch small{font-size:.76rem;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;max-width:190px}
  .live-dot{font-size:.62rem}
  .classic-hero{padding:10px 0 14px}
  .classic-player-grid{gap:10px}
  .classic-player{border-radius:14px;box-shadow:0 14px 38px rgba(0,0,0,.36)}
  .classic-info{padding:15px;border-radius:15px}
  .classic-info h1{font-size:1.72rem;line-height:1.02;margin:9px 0 7px}
  .classic-info>div:first-child p{font-size:.86rem;line-height:1.42;margin:0}
  .classic-live{font-size:.66rem}
  .now-card{margin-top:12px;padding:12px;border-radius:13px}
  .now-card strong{font-size:1.02rem;margin:5px 0}
  .now-card p{margin:7px 0;font-size:.84rem}
  .provider-status{display:none}
  .guide-section{padding:16px 0 24px}
  .section-head{gap:3px;margin-bottom:8px}
  .section-head h2{font-size:1.45rem;margin:2px 0}
  .guide-channel-head{align-items:flex-start;margin:22px 0 9px;gap:8px}
  .guide-channel-head h3{font-size:1rem;line-height:1.3}
  .guide-channel-head a{font-size:.76rem;white-space:nowrap;padding-top:2px}
  .guide-grid{
    display:flex;
    gap:9px;
    overflow-x:auto;
    margin:0 -12px;
    padding:0 12px 10px;
    scroll-snap-type:x mandatory;
    -webkit-overflow-scrolling:touch;
  }
  .guide-block{flex:0 0 72vw;min-height:126px;padding:13px;border-radius:14px;scroll-snap-align:start}
  .guide-icon{font-size:1.25rem;margin:10px 0 7px}
  .guide-block strong{font-size:.93rem}
  .guide-block p{font-size:.76rem}
  .channels-section{padding-top:10px}
  .channel-grid{grid-template-columns:1fr 1fr;gap:10px}
  .channel-card{min-height:132px;padding:14px;border-radius:15px}
  .channel-card .icon{font-size:1.6rem}
  .channel-card h3{font-size:.98rem;margin:18px 0 5px}
  .channel-card p{font-size:.76rem}
  .release-note{padding:14px;border-radius:14px;font-size:.82rem;line-height:1.45}
  .classic-footer{padding:18px 0 32px;text-align:center;line-height:1.6}
}
@media(max-width:380px){
  .classic-nav .classic-btn:not(.primary){display:none}
  .channel-switch{flex-basis:84vw}
  .guide-block{flex-basis:80vw}
  .channel-grid{grid-template-columns:1fr}
}

/* 2.2 player containment + mobile overlap fix */
*,*::before,*::after{box-sizing:border-box}
html,body{max-width:100%;overflow-x:hidden}
.classic-home{overflow-x:clip}
.classic-shell,.classic-player-grid,.classic-player,.classic-info,.guide-section,.channels-section{min-width:0;max-width:100%}
.classic-player-grid{position:relative;isolation:isolate}
.classic-player{
  width:100%;
  height:auto;
  min-height:0;
  aspect-ratio:16/9;
  contain:layout paint;
  isolation:isolate;
  z-index:1;
}
.classic-player iframe,.classic-player video{
  display:block;
  position:absolute;
  inset:0;
  width:100%!important;
  max-width:100%!important;
  height:100%!important;
  max-height:100%!important;
  margin:0;
  border:0;
  object-fit:contain;
}
.classic-info{position:relative;z-index:0;overflow:hidden}
.guide-section{position:relative;z-index:0;clear:both}
.channel-switcher,.guide-grid{max-width:100%}

@media(max-width:850px){
  .classic-player-grid{display:flex;flex-direction:column;align-items:stretch}
  .classic-player{flex:0 0 auto}
  .classic-info{flex:0 0 auto}
  .guide-section{margin-top:8px}
}

@media(max-width:620px){
  .classic-shell{width:calc(100% - 20px)}
  .classic-hero{overflow:visible}
  .classic-player-grid{gap:12px}
  .classic-player{border-radius:12px}
  .classic-info{padding:14px}
  .guide-section{margin-top:10px;padding-top:18px;border-top:1px solid rgba(255,255,255,.08)}
  .unmute-hint{left:9px;bottom:9px;max-width:calc(100% - 18px);padding:8px 11px;font-size:.76rem}
}

.tv-theme-toggle{cursor:pointer;font:inherit}html[data-tv-theme="light"]{color-scheme:light}html[data-tv-theme="light"] .classic-home{background:radial-gradient(circle at 50% -10%,#e9ddff 0,#f5f7fc 36%,#eef2f8 72%);color:#171a2e}html[data-tv-theme="light"] .classic-nav{background:rgba(255,255,255,.9);border-color:rgba(20,28,55,.12)}html[data-tv-theme="light"] .classic-brand,html[data-tv-theme="light"] .classic-btn{color:#171a2e!important}html[data-tv-theme="light"] .classic-btn{background:#fff;border-color:rgba(20,28,55,.16)}html[data-tv-theme="light"] .classic-btn.primary{color:#fff!important;background:#7659ef}html[data-tv-theme="light"] .channel-switch,html[data-tv-theme="light"] .classic-info,html[data-tv-theme="light"] .guide-block,html[data-tv-theme="light"] .channel-card,html[data-tv-theme="light"] .release-note{background:#fff;color:#171a2e;border-color:rgba(20,28,55,.14);box-shadow:0 12px 30px rgba(35,43,75,.09)}html[data-tv-theme="light"] .channel-switch small,html[data-tv-theme="light"] .classic-info p,html[data-tv-theme="light"] .clock-line,html[data-tv-theme="light"] .guide-block p,html[data-tv-theme="light"] .channel-card p,html[data-tv-theme="light"] .provider-status{color:#657087}html[data-tv-theme="light"] .now-card{background:#f2f4fa;border-color:rgba(20,28,55,.12)}@media(max-width:620px){.tv-theme-toggle span{display:none}}


html[data-tv-theme="sunset"]{color-scheme:dark}html[data-tv-theme="sunset"] .btv,html[data-tv-theme="sunset"] .classic-home,html[data-tv-theme="sunset"] body.tv-app{color:#fff7f2;background:radial-gradient(circle at 50% -10%,#7b2d58 0,#32133f 34%,#151326 72%)}html[data-tv-theme="sunset"] .btv:before{background:linear-gradient(180deg,rgba(78,25,70,.38),rgba(28,15,42,.88) 45%,#111325 80%)}html[data-tv-theme="sunset"] .btv-nav,html[data-tv-theme="sunset"] .classic-nav{background:rgba(40,17,43,.90);border-color:rgba(255,198,166,.18)}html[data-tv-theme="sunset"] .btv-btn,html[data-tv-theme="sunset"] .btv-theme-toggle,html[data-tv-theme="sunset"] .classic-btn,html[data-tv-theme="sunset"] .channel-theme-toggle{background:rgba(106,43,76,.45);border-color:rgba(255,205,176,.25);color:#fff7f2}html[data-tv-theme="sunset"] .now-panel,html[data-tv-theme="sunset"] .next-panel,html[data-tv-theme="sunset"] .live-chip,html[data-tv-theme="sunset"] .channel-switch,html[data-tv-theme="sunset"] .classic-info,html[data-tv-theme="sunset"] .guide-block,html[data-tv-theme="sunset"] .channel-card,html[data-tv-theme="sunset"] .release-note,html[data-tv-theme="sunset"] .channel-detail,html[data-tv-theme="sunset"] .schedule-mini>div{background:rgba(54,23,52,.90)!important;border-color:rgba(255,195,160,.20)!important;color:#fff7f2}html[data-tv-theme="sunset"] .epg{background:#2a1733;border-color:rgba(255,194,158,.20)}html[data-tv-theme="sunset"] .epg-cell{background:#321b3e;border-color:#5d3556;color:#fff7f2}html[data-tv-theme="sunset"] .epg-time,html[data-tv-theme="sunset"] .epg-channel,html[data-tv-theme="sunset"] .epg-corner{background:#402047;color:#ffd9c6}html[data-tv-theme="sunset"] .epg-program.current{background:#6e345e;box-shadow:inset 0 0 0 3px #ffb36b}html[data-tv-theme="sunset"] .channel-detail p,html[data-tv-theme="sunset"] .schedule-mini small,html[data-tv-theme="sunset"] .provider-status,html[data-tv-theme="sunset"] .clock-line{color:#e5bdb5}
</style>
</head>
<body class="classic-home">
<header class="classic-nav">
  <a class="classic-brand" href="https://tv.beyondimagination.co.technology/" style="color:#fff;text-decoration:none">Beyond TV</a>
  <nav class="classic-nav-actions" aria-label="Beyond TV navigation">
    <button class="classic-btn tv-theme-toggle" type="button" data-tv-theme-toggle aria-label="Switch Beyond TV to light theme" aria-pressed="false">☀️ <span>Light</span></button>
    <?php if ($signedIn): ?>
      <a class="classic-btn" href="https://beyondimagination.co.technology/beyond-tv/browse.php">Browse</a>
      <a class="classic-btn primary" href="https://beyondimagination.co.technology/dashboard/">Beyond ID</a>
    <?php else: ?>
      <a class="classic-btn" href="https://beyondimagination.co.technology/">Beyond OS</a>
      <a class="classic-btn primary" href="https://beyondimagination.co.technology/beyond-id/auth/login.php?return=https%3A%2F%2Ftv.beyondimagination.co.technology%2F"><span class="secondary-label">Sign in with </span>Beyond ID</a>
    <?php endif; ?>
  </nav>
</header>

<main>
<section class="classic-shell" aria-label="Choose a live channel">
  <div class="channel-switcher">
    <button type="button" class="channel-switch active" data-tv-channel="classic" data-endpoint="/api/classic-live.php" data-title="Classic Cartoon Theater" data-open="https://beyondimagination.co.technology/beyond-tv/channel.php?slug=classic-cartoon-theater"><span><strong>🎞️ Channel 1 · Classic Cartoon Theater</strong><small><?= htmlspecialchars((string)$current['title']) ?> · Cartoons</small></span><span class="live-dot">● LIVE</span></button>
    <button type="button" class="channel-switch" data-tv-channel="cartoons" data-endpoint="/api/beyond-cartoons-live.php" data-title="Beyond Cartoons" data-open="https://beyondimagination.co.technology/beyond-tv/channel.php?slug=beyond-cartoons"><span><strong>📺 Channel 2 · Beyond Cartoons</strong><small><?= htmlspecialchars((string)$cartoonCurrent['title']) ?> · Cartoons</small></span><span class="live-dot">● LIVE</span></button>
    <button type="button" class="channel-switch" data-tv-channel="preschool" data-player-url="https://archive.org/embed/bluey-iso-archive" data-title="Preschool TV" data-current="Bluey &amp; Nick Jr. Live Library" data-lineup="Bluey · Blue's Clues · Allegra's Window · Gullah Gullah Island" data-next="Next curated preschool episode" data-open="https://beyondimagination.co.technology/beyond-tv/channel.php?slug=bubble-guppies"><span><strong>🐾 Channel 3 · Preschool TV</strong><small>Bluey · Blue's Clues · Nick Jr. classics</small></span><span class="live-dot">● LIVE</span></button>
    <button type="button" class="channel-switch" data-tv-channel="space" data-endpoint="/api/space-live.php" data-title="Beyond Space" data-open="https://beyondimagination.co.technology/beyond-tv/channel.php?slug=space-tv"><span><strong>🛰️ Channel 4 · Beyond Space</strong><small>The Sun &amp; The Milky Way</small></span><span class="live-dot">● LIVE</span></button>
    <button type="button" class="channel-switch" data-tv-channel="ancient" data-player-url="https://www.youtube-nocookie.com/embed/BR2ZMj3o5EU?autoplay=1&amp;mute=1&amp;playsinline=1&amp;enablejsapi=1" data-title="Beyond Ancient" data-current="Ancient Egypt Documentary" data-lineup="Egypt · Archaeology · Civilizations" data-next="Pyramids, pharaohs and archaeology" data-open="https://beyondimagination.co.technology/beyond-tv/channel.php?slug=beyond-ancient"><span><strong>𓂀 Channel 5 · Beyond Ancient</strong><small>Egypt · Mythology · Civilizations</small></span><span class="live-dot">EXPLORE</span></button>
    <button type="button" class="channel-switch" data-tv-channel="cinema" data-player-url="https://www.youtube-nocookie.com/embed/videoseries?list=PLdk1SI29-q9yrN9GFMnOAYmC_tcw5v59L&amp;autoplay=1&amp;mute=1&amp;playsinline=1&amp;rel=0&amp;enablejsapi=1" data-title="Beyond Movies" data-current="Beyond Movies Playlist" data-lineup="Movies · Features · Curated playlist" data-next="Next movie in the playlist" data-open="https://beyondimagination.co.technology/beyond-tv/channel.php?slug=classic-cinema"><span><strong>🎬 Channel 6 · Beyond Movies</strong><small>Curated movie playlist</small></span><span class="live-dot">● PLAY</span></button>
    <button type="button" class="channel-switch" data-tv-channel="french" data-player-url="https://www.youtube-nocookie.com/embed/hd0_GZHHWeE?autoplay=1&amp;mute=1&amp;playsinline=1&amp;enablejsapi=1" data-title="Beyond French" data-current="Français du jour" data-lineup="Lessons · Conversation · Review" data-next="Daily French challenge" data-open="https://beyondimagination.co.technology/beyond-french/"><span><strong>🇫🇷 Channel 7 · Beyond French</strong><small>French lessons · Conversation</small></span><span class="live-dot">LEARN</span></button>
    <button type="button" class="channel-switch" data-tv-channel="health" data-player-url="https://www.youtube-nocookie.com/embed/7_chERnJ0gE?autoplay=1&amp;mute=1&amp;playsinline=1&amp;enablejsapi=1" data-title="Beyond Health" data-current="Featured Health Presentation" data-lineup="Health · Wellness · Education" data-next="Replay" data-open="https://beyondimagination.co.technology/beyond-tv/channel.php?slug=beyond-health"><span><strong>💚 Channel 8 · Beyond Health</strong><small>Featured health presentation</small></span><span class="live-dot">● PLAY</span></button>
  </div>
</section>

<section class="classic-hero classic-shell">
  <div class="classic-player-grid">
    <div class="classic-player" id="tvLivePlayerWrap">
      <iframe id="tvLivePlayer" src="https://archive.org/embed/SnowWhiteWithBettyBoop1933?autoplay=1" title="Classic Cartoon Theater live" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share; fullscreen" referrerpolicy="strict-origin-when-cross-origin" allowfullscreen></iframe>
      <div class="player-loading" id="tvTuneLoading" role="status" hidden>Tuning channel…</div>
    </div>
    <article class="classic-info">
      <div>
        <span class="classic-live">LIVE · FREE</span>
        <h1 id="tvStageTitle">Classic Cartoon Theater</h1>
        <p>Eight Beyond TV destinations across cartoons, space, history, movies, French and health. Channels 1–3 are the cartoon lineup.</p>
      </div>
      <div class="now-card">
        <small>NOW PLAYING</small>
        <strong id="tvStageCurrent"><?= htmlspecialchars((string)$current['icon'].' '.$current['title']) ?></strong>
        <span id="tvStageLineup"><?= htmlspecialchars((string)$current['lineup']) ?></span>
        <p><b>Up next:</b> <span id="tvStageNext"><?= htmlspecialchars((string)$next['title']) ?></span></p>
        <div class="clock-line"><span id="tvStageClock"><?= htmlspecialchars((string)$schedule['time_label']) ?></span> <?= htmlspecialchars((string)$schedule['timezone_label']) ?> · Vancouver</div>
        <div class="provider-status">Joining the same live position as every viewer.</div>
      </div>
    </article>
  </div>
</section>



<section class="guide-section classic-shell">
  <div class="section-head"><div><span class="kicker">LIVE GUIDE</span><h2>Today on Beyond TV</h2></div><span class="clock-line"><?= htmlspecialchars((string)$schedule['date_label']) ?> · PT</span></div>
  <?php foreach($guideChannels as $number=>$guideChannel): ?>
  <div class="guide-channel-head"><h3><?=htmlspecialchars((string)$guideChannel['icon'])?> Channel <?=($number+1)?> · <?=htmlspecialchars((string)$guideChannel['name'])?></h3><a href="https://beyondimagination.co.technology/beyond-tv/channel.php?slug=<?=urlencode((string)$guideChannel['slug'])?>">Watch Channel <?=($number+1)?> →</a></div>
  <div class="guide-grid">
  <?php foreach($guideChannel['rows'] as $block): $isCurrent=$currentHour >= (int)$block['start'] && $currentHour < (int)$block['end']; $startLabel=(new DateTimeImmutable('today',new DateTimeZone('America/Vancouver')))->setTime((int)$block['start'],0)->format('g:i A'); $endLabel=((int)$block['end']===24)?'12:00 AM':(new DateTimeImmutable('today',new DateTimeZone('America/Vancouver')))->setTime((int)$block['end'],0)->format('g:i A'); ?>
    <a class="guide-block<?=$isCurrent?' current':''?>" href="https://beyondimagination.co.technology/beyond-tv/channel.php?slug=<?=urlencode((string)$guideChannel['slug'])?>" style="text-decoration:none;color:inherit"><span class="guide-time"><?=htmlspecialchars($startLabel.'–'.$endLabel)?></span><div class="guide-icon"><?=htmlspecialchars((string)$block['icon'])?></div><strong><?=htmlspecialchars((string)$block['title'])?></strong><p><?=htmlspecialchars((string)$block['lineup'])?></p></a>
  <?php endforeach;?>
  </div>
  <?php endforeach;?>
</section>

<section class="channels-section classic-shell">
  <div class="section-head"><div><span class="kicker">BEYOND TV CHANNELS</span><h2>Released one channel at a time</h2></div></div>
  <div class="channel-grid">
    <a class="channel-card" href="https://beyondimagination.co.technology/beyond-tv/channel.php?slug=classic-cartoon-theater" style="background:<?= htmlspecialchars((string)($classic['gradient'] ?? 'linear-gradient(145deg,#4b2552,#17101e)')) ?>">
      <span class="icon"><?= htmlspecialchars((string)($classic['icon'] ?? '📺')) ?></span>
      <h3>Classic Cartoon Theater</h3>
      <p>Public · Live now</p>
    </a>
    <?php foreach ($publicChannels as $channel): ?>
      <a class="channel-card" href="https://beyondimagination.co.technology/beyond-tv/channel.php?slug=<?= urlencode((string)$channel['slug']) ?>" style="background:<?= htmlspecialchars((string)($channel['gradient'] ?? 'linear-gradient(145deg,#151522,#25213a)')) ?>">
        <span class="icon"><?= htmlspecialchars((string)($channel['icon'] ?? '📺')) ?></span>
        <h3><?= htmlspecialchars((string)$channel['name']) ?></h3>
        <p>Public · Live now</p>
      </a>
    <?php endforeach; ?>
    <?php foreach ($memberChannels as $channel): ?>
      <a class="channel-card locked" href="https://beyondimagination.co.technology/beyond-tv/channel.php?slug=<?= urlencode((string)$channel['slug']) ?>" style="background:<?= htmlspecialchars((string)($channel['gradient'] ?? 'linear-gradient(145deg,#151522,#25213a)')) ?>">
        <span class="lock-label">🔒 BEYOND ID</span>
        <span class="icon"><?= htmlspecialchars((string)($channel['icon'] ?? '📺')) ?></span>
        <h3><?= htmlspecialchars((string)$channel['name']) ?></h3>
        <p><?= $signedIn ? 'Member channel · Watch now' : 'Sign in to unlock' ?></p>
      </a>
    <?php endforeach; ?>
  </div>
  <p class="release-note">All eight featured channels are now present in the public guide. Channels 4, 5, 7 and 8 use YouTube-hosted programming for space, ancient history, French learning and health; playback remains hosted by YouTube.</p>
</section>
</main>
<footer class="classic-footer classic-shell">Beyond TV 2.2 · Scheduled in <strong>America/Vancouver</strong> · <a href="https://beyondimagination.co.technology/legal/terms.php">Terms</a> · <a href="https://beyondimagination.co.technology/legal/privacy.php">Privacy</a></footer>
<script src="/assets/js/app.js?v=2.2.0"></script>
<script>
(function(){
  const player=document.getElementById('tvLivePlayer');
  const loading=document.getElementById('tvTuneLoading');
  const buttons=[...document.querySelectorAll('[data-tv-channel]')];
  const title=document.getElementById('tvStageTitle');
  const current=document.getElementById('tvStageCurrent');
  const lineup=document.getElementById('tvStageLineup');
  const next=document.getElementById('tvStageNext');
  const clock=document.getElementById('tvStageClock');
  async function tune(button){
    buttons.forEach(b=>b.classList.toggle('active',b===button));
    loading.hidden=false;
    try{
      if(button.dataset.endpoint){
        const response=await fetch(button.dataset.endpoint,{cache:'no-store',headers:{Accept:'application/json'}});
        if(!response.ok)throw new Error('Channel unavailable');
        const payload=await response.json();
        const state=payload.state||payload;
        if(state.embed_url) player.src=state.embed_url;
        current.textContent=`${state.current?.icon||''} ${state.current?.title||'Live now'}`.trim();
        lineup.textContent=state.current?.lineup||'';
        next.textContent=state.next?.title||'';
        clock.textContent=state.time_label||'';
      }else{
        player.src=button.dataset.playerUrl||button.dataset.open||'';
        current.textContent=button.dataset.current||'Now available';
        lineup.textContent=button.dataset.lineup||'';
        next.textContent=button.dataset.next||'';
        clock.textContent='Beyond TV';
      }
      title.textContent=button.dataset.title||'Beyond TV';
      player.title=`${button.dataset.title||'Beyond TV'} channel`;
      const channelNumber=buttons.indexOf(button)+1;
      history.replaceState(null,'',`#channel-${channelNumber}`);
    }catch(error){
      console.warn(error);
    }finally{loading.hidden=true;}
  }
  buttons.forEach(button=>button.addEventListener('click',()=>tune(button)));
  const initialMatch=location.hash.match(/^#channel-(\d+)$/);
  if(initialMatch){const initial=buttons[Number(initialMatch[1])-1];if(initial)tune(initial);}
  setInterval(()=>{const active=buttons.find(b=>b.classList.contains('active'));if(active)tune(active);},60000);
})();
</script>
<script>(function(){const root=document.documentElement,btn=document.querySelector('[data-tv-theme-toggle]'),themes=['dark','light','sunset'];if(!btn)return;function apply(t){if(!themes.includes(t))t='dark';root.dataset.tvTheme=t;const next=themes[(themes.indexOf(t)+1)%themes.length],icons={dark:'🌙',light:'☀️',sunset:'🌅'},labels={dark:'Dark',light:'Light',sunset:'Sunset'};btn.innerHTML=icons[t]+' <span>'+labels[t]+'</span>';btn.setAttribute('aria-label','Current theme '+labels[t]+'. Switch to '+labels[next]+' theme');btn.title='Switch to '+labels[next]+' theme';document.querySelector('meta[name=theme-color]')?.setAttribute('content',t==='light'?'#f4f6fb':t==='sunset'?'#32133f':'#080812');}apply(root.dataset.tvTheme||'sunset');btn.addEventListener('click',()=>{const current=themes.includes(root.dataset.tvTheme)?root.dataset.tvTheme:'dark',next=themes[(themes.indexOf(current)+1)%themes.length];try{localStorage.setItem('beyond-tv-theme',next)}catch(e){}apply(next)});})();</script></body>
</html>
