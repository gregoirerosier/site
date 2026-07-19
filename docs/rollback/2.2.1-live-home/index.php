<?php
declare(strict_types=1);
require_once __DIR__ . '/includes/ecosystem.php';
require_once __DIR__ . '/beyond-tv/includes/classic-schedule.php';
require_once __DIR__ . '/beyond-tv/includes/beyond-cartoons-schedule.php';
beyond_nav_bootstrap('Beyond OS');
$signedIn = isset($_SESSION['user_id']);

$sections = [
    [
        'id' => 'health',
        'label' => 'HEALTH',
        'icon' => '♥',
        'headline' => 'Live your best life.',
        'copy' => 'Mind, body and soul. Everything you need to feel better every day.',
        'apps' => [
            ['Daily Breath', 'dailybreath/', 'assets/icons/daily-breath-192.webp'],
            ['Beyond Health', 'beyond-health/', 'assets/icons/beyond-health-192.webp'],
            ['Beyond TV', 'beyond-tv/', 'assets/icons/beyond-tv-192.webp'],
            ['Beyond Tattoo', 'beyond-tattoo/', 'assets/icons/beyond-tattoo-v2-192.webp?v=20260717-2'],
            ['Beyond Audio', 'beyond-audio/', '🎧'],
        ],
    ],
    [
        'id' => 'education',
        'label' => 'EDUCATION',
        'icon' => '🏫',
        'headline' => 'Knowledge without limits.',
        'copy' => 'Learn anything. Anywhere. Unlock your potential across every subject.',
        'apps' => [
            ['Beyond Academy', 'academy/', '🎓'],
            ['Beyond French', 'beyond-french/', 'assets/icons/beyond-french-192.webp'],
            ['Beyond Math', 'beyond-math/', '🧮'],
            ['Beyond Ancient', 'beyond-ancient/Egypt/', 'assets/icons/beyond-ancient-192.webp'],
            ['Beyond Space', 'beyond-space/beyond-space-v1/', 'assets/icons/beyond-space-192.webp'],
            ['Coding School', 'coding-school/', '💻'],
            ['Beyond Baby Names', 'beyond-baby-names/', 'assets/icons/beyond-baby-names-v2-192.webp?v=20260717-2'],
        ],
    ],
    [
        'id' => 'wallet',
        'label' => 'WALLET',
        'icon' => '🏦',
        'headline' => 'Spend, earn and cash out.',
        'copy' => 'Your bit$, purchases and verified creator earnings in one customer-friendly wallet.',
        'apps' => [
            ['Beyond Wallet', 'beyond-id/dashboard/wallet.php', '👛'],
            ['Beyond Sell', 'beyond-sell/', '🛍️'],
            ['Beyond Canvas', 'beyond-canvas/', '🖼️'],
            ['Beyond Market', 'beyond-market/', '🌐'],
            ['Beyond Investing', 'beyond-investing/', '₿'],
            ['Beyond Careers', 'beyond-careers/', '💼'],
            ['Beyond Catering', 'beyond-catering/', '🍽️'],
            ['Earnings', 'beyond-finance/', '📈'],
        ],
    ],
    [
        'id' => 'entertainment',
        'label' => 'ENTERTAINMENT',
        'icon' => '▶',
        'headline' => 'Explore what moves you.',
        'copy' => 'Watch, listen, create and discover something new across the Beyond universe.',
        'apps' => [
            ['Beyond TV', 'beyond-tv/', 'assets/icons/beyond-tv-192.webp'],
            ['Beyond Audio', 'beyond-audio/', '🎧'],
            ['Beyond Skate', 'beyond-skate/', 'assets/icons/beyond-skate-192.webp'],
            ['Beyond Tattoo', 'beyond-tattoo/', 'assets/icons/beyond-tattoo-v2-192.webp?v=20260717-2'],
            ['Beyond Canvas', 'beyond-canvas/', '🖼️'],
            ['Beyond Radio', 'beyond-radio/', '📻'],
        ],
    ],
];
?>
<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1,viewport-fit=cover">
<script>(function(){try{document.documentElement.dataset.theme=localStorage.getItem('beyond-theme')==='light'?'light':'dark';}catch(e){document.documentElement.dataset.theme='dark';}})();</script>
<meta name="theme-color" content="#050817">
<link rel="icon" type="image/webp" href="assets/icons/beyond-os-192.webp">
<link rel="apple-touch-icon" href="assets/icons/beyond-os.webp">
<title>Beyond OS Beta Build 2.1.1 | Live. Learn. Earn. Explore.</title>
<meta name="description" content="Health, education, wallet and entertainment connected through one secure Beyond ID and one shared bit$ balance.">
<style>
:root{--bg:#030611;--panel:#09101f;--line:rgba(255,255,255,.13);--text:#f7f8ff;--muted:#b8bed2;--pink:#f2469d;--violet:#7057ff;--green:#51db78;--gold:#ffbf32;--blue:#448cff}
*{box-sizing:border-box}html{scroll-behavior:smooth;background:var(--bg)}body{margin:0;color:var(--text);font-family:Inter,ui-sans-serif,system-ui,-apple-system,BlinkMacSystemFont,"Segoe UI",sans-serif;background:radial-gradient(circle at 75% 10%,rgba(73,54,204,.18),transparent 28%),linear-gradient(180deg,#050817,#02040d 72%);overflow-x:hidden}a{color:inherit}.wrap{width:min(1180px,calc(100% - 32px));margin-inline:auto}.top{min-height:84px;display:flex;align-items:center;justify-content:space-between;gap:20px}.brand{text-decoration:none;font-weight:1000;font-size:23px;letter-spacing:-.045em}.brand span{background:linear-gradient(100deg,#7667ff,#ec4caa);background-clip:text;color:transparent}.brand small{display:block;margin-top:5px;font-size:9px;letter-spacing:.13em;color:#c7c9d5;font-weight:700}.nav{display:flex;align-items:center;gap:29px}.nav>a:not(.primary){text-decoration:none;font-size:13px;color:#f6f7ff;padding:12px 0;border-bottom:2px solid transparent}.nav a[href="#health"]{border-color:var(--green)}.nav a[href="#education"]{border-color:var(--gold)}.nav a[href="#finance"]{border-color:var(--blue)}.primary{display:inline-flex;align-items:center;justify-content:center;min-height:47px;padding:0 23px;border-radius:9px;text-decoration:none;font-weight:850;font-size:13px;background:linear-gradient(100deg,#586cff,#ef4897);box-shadow:0 12px 34px rgba(106,74,255,.28)}.menu{display:none;background:none;border:0;color:#fff;font-size:29px}.hero{min-height:600px;display:grid;grid-template-columns:.88fr 1.12fr;align-items:center;gap:45px;padding:46px 0 55px}.hero h1{font-size:clamp(58px,7.3vw,102px);line-height:.83;letter-spacing:-.075em;margin:0 0 26px}.hero h1 span{display:block}.hero .h{color:var(--green)}.hero .e{color:var(--gold)}.hero .f{color:var(--blue)}.hero .x{color:var(--pink)}.tagline{font-size:25px;font-weight:850;margin:0 0 20px}.intro{max-width:430px;color:var(--muted);line-height:1.65;font-size:16px}.hero-actions{display:flex;gap:14px;flex-wrap:wrap;margin-top:30px}.ghost{display:inline-flex;align-items:center;justify-content:center;min-height:50px;padding:0 23px;border:1px solid rgba(255,255,255,.24);border-radius:12px;text-decoration:none;font-weight:800;background:rgba(255,255,255,.025)}.benefits{display:flex;gap:28px;flex-wrap:wrap;margin-top:26px;color:#c9cedd;font-size:12px}.benefits span{display:flex;align-items:center;gap:8px}.benefits b{font-size:16px;color:#fff}.orbit{position:relative;aspect-ratio:1.18/1;display:grid;place-items:center;isolation:isolate}.orbit:before{content:"";position:absolute;inset:5%;background:radial-gradient(circle,rgba(99,78,255,.22),transparent 50%);filter:blur(15px);z-index:-1}.ring{position:absolute;border:1px solid rgba(117,158,255,.42);border-radius:50%;width:70%;aspect-ratio:1}.ring.r2{width:91%;border-color:rgba(255,188,86,.34)}.core{width:170px;aspect-ratio:1;border-radius:50%;display:grid;place-items:center;font-size:74px;font-weight:1000;background:radial-gradient(circle at 40% 35%,#251b55,#090919 58%);border:2px solid #9a52ff;box-shadow:0 0 0 12px rgba(92,88,255,.09),0 0 55px #6c51ff88,inset 0 0 45px #2f225f}.core span{background:linear-gradient(145deg,#516fff,#e745a3);background-clip:text;color:transparent}.planet{position:absolute;width:112px;aspect-ratio:1;border-radius:50%;display:grid;place-items:center;text-align:center;font-weight:900;font-size:12px;border:1px solid currentColor;background:rgba(6,11,25,.9);box-shadow:0 0 33px currentColor}.planet i{font-style:normal;font-size:38px;display:block;line-height:1.1}.ph{left:13%;top:14%;color:var(--green)}.pe{right:8%;top:18%;color:var(--gold)}.pf{bottom:3%;left:24%;color:var(--blue)}.px{bottom:3%;right:8%;color:var(--pink)}.welcome{margin-bottom:18px;padding:20px 26px;border:1px solid rgba(255,255,255,.12);border-radius:17px;background:linear-gradient(100deg,rgba(82,52,217,.9),rgba(201,36,125,.86));display:flex;align-items:center;justify-content:space-between;gap:18px}.welcome-copy{display:flex;align-items:center;gap:17px}.gift{font-size:38px}.welcome strong{display:block;font-size:16px}.welcome p{margin:5px 0 0;color:#e6def4;font-size:13px}.welcome .primary{background:rgba(255,255,255,.08);border:1px solid rgba(255,255,255,.22);box-shadow:none}.world{--accent:var(--green);position:relative;margin:0 auto 18px;min-height:380px;border:1px solid color-mix(in srgb,var(--accent) 55%,transparent);border-radius:20px;overflow:hidden;background:#08121b}.world:before{content:"";position:absolute;inset:0;background:radial-gradient(circle at 72% 40%,color-mix(in srgb,var(--accent) 22%,transparent),transparent 38%),linear-gradient(90deg,rgba(2,7,14,.97) 0%,rgba(3,8,17,.78) 38%,rgba(3,8,17,.18) 75%,rgba(3,8,17,.65) 100%)}.world.health{--accent:var(--green);background:radial-gradient(ellipse at 72% 26%,#2bb6a055 0,transparent 28%),linear-gradient(130deg,#06170f,#052b30 55%,#06141d)}.world.education{--accent:var(--gold);background:radial-gradient(circle at 62% 32%,#7f3ad466 0,transparent 30%),linear-gradient(130deg,#1b0f08,#1d1030 56%,#2c1608)}.world.finance{--accent:var(--blue);background:radial-gradient(ellipse at 75% 30%,#2f62c555 0,transparent 34%),linear-gradient(130deg,#061328,#081d45 58%,#190c37)}.world-inner{position:relative;z-index:1;min-height:380px;padding:31px 31px 26px;display:grid;grid-template-columns:320px 1fr;align-items:end;gap:26px}.world-copy{align-self:start}.world-title{display:flex;align-items:center;gap:15px;color:var(--accent)}.world-icon{width:56px;height:56px;border-radius:15px;display:grid;place-items:center;font-size:28px;background:color-mix(in srgb,var(--accent) 18%,rgba(255,255,255,.04));border:1px solid color-mix(in srgb,var(--accent) 48%,transparent)}.world h2{font-size:32px;margin:0;letter-spacing:-.035em}.world h3{font-size:17px;margin:22px 0 9px}.world p{max-width:290px;color:#c0c6d4;line-height:1.5;font-size:14px}.explore{display:inline-flex;margin-top:8px;min-height:42px;padding:0 16px;border:1px solid color-mix(in srgb,var(--accent) 72%,transparent);border-radius:12px;align-items:center;text-decoration:none;color:var(--accent);font-weight:850;font-size:13px}.apps{display:grid;grid-template-columns:repeat(auto-fit,minmax(92px,1fr));gap:10px;align-self:end}.app{position:relative;min-height:104px;border:1px solid rgba(255,255,255,.14);border-radius:14px;background:rgba(5,10,22,.75);display:flex;flex-direction:column;align-items:center;justify-content:center;gap:9px;text-align:center;text-decoration:none;font-size:11px;font-weight:780;padding:8px;backdrop-filter:blur(10px);transition:.2s}.app:hover{transform:translateY(-4px);border-color:var(--accent)}.app b{font-size:25px;color:var(--accent)}.app-icon{width:48px;height:48px;border-radius:13px;object-fit:cover;border:1px solid rgba(255,255,255,.18);box-shadow:0 8px 22px rgba(0,0,0,.34)}.soon{position:absolute;right:6px;top:-8px;padding:3px 6px;border-radius:8px;background:#dedfe8;color:#202333;font-size:8px}.all{border-style:dashed}.identity{margin:18px auto 0;padding:24px 30px;border:1px solid rgba(207,107,255,.32);border-radius:18px;background:linear-gradient(100deg,rgba(61,30,151,.76),rgba(192,35,123,.72));display:grid;grid-template-columns:auto 1fr auto;align-items:center;gap:24px}.shield{width:74px;height:74px;border-radius:22px;display:grid;place-items:center;font-size:38px;overflow:hidden;background:linear-gradient(145deg,#6a57ff,#ed47a2);box-shadow:0 0 35px #8a50ff66}.shield img{width:100%;height:100%;object-fit:cover}.identity h2{font-size:24px;line-height:1.05;margin:0 0 8px}.identity p{margin:0;color:#d7d2e6;font-size:13px;line-height:1.5}.id-action{text-align:center}.id-action small{display:block;margin-top:10px;color:#ece8f4}.footer{margin-top:25px;padding:36px 0 50px;border-top:1px solid rgba(255,255,255,.09);display:grid;grid-template-columns:1.4fr repeat(4,1fr);gap:28px;color:#8f96aa;font-size:12px}.footer h4{margin:0 0 12px;color:#d7dbe8;font-size:11px;letter-spacing:.08em}.footer a{display:block;text-decoration:none;margin:7px 0}.footer .brand{color:#fff;font-size:18px}.copyright{margin-top:15px}.mobile-links{display:none}
@media(max-width:850px){.nav>a:not(.primary){display:none}.hero{grid-template-columns:1fr;padding-top:34px}.orbit{max-width:590px;width:100%;margin:auto}.hero h1{font-size:clamp(58px,15vw,88px)}.world-inner{grid-template-columns:1fr;padding:25px 20px}.apps{grid-template-columns:repeat(3,1fr)}.identity{grid-template-columns:auto 1fr}.id-action{grid-column:1/-1}.id-action .primary{width:100%}.footer{grid-template-columns:1fr 1fr 1fr}.footer>div:first-child{grid-column:1/-1}}@media(max-width:560px){.wrap{width:min(100% - 22px,1180px)}.top{min-height:72px}.brand{font-size:19px}.brand small{display:none}.nav .primary{padding:0 14px;min-height:42px}.hero{gap:20px;min-height:auto;padding:35px 0}.tagline{font-size:21px}.intro{font-size:15px}.hero-actions>*{width:100%}.benefits{gap:12px 18px}.orbit{aspect-ratio:1;transform:scale(.96)}.core{width:116px;font-size:50px}.planet{width:78px;font-size:9px}.planet i{font-size:27px}.ph{left:3%}.pe{right:1%}.pf{bottom:0;left:10%}.px{bottom:0;right:1%}.welcome{padding:17px;align-items:flex-start}.welcome .primary{display:none}.world{min-height:500px}.world-inner{min-height:500px;display:flex;flex-direction:column;align-items:stretch}.world-copy{width:100%}.apps{margin-top:auto;grid-template-columns:repeat(3,1fr)}.app{min-height:94px;font-size:10px}.identity{padding:22px;grid-template-columns:1fr;text-align:left}.shield{width:60px;height:60px}.footer{grid-template-columns:1fr 1fr}.footer>div:first-child{grid-column:1/-1}}
@media(prefers-reduced-motion:reduce){*{scroll-behavior:auto!important;transition:none!important}}
@media(max-width:560px){.planet{overflow:hidden}.planet .label{display:flex;flex-direction:column;align-items:center;justify-content:center;line-height:1.05;font-size:8px}.planet .label i{margin-bottom:2px}.planet{width:82px;height:82px}.planet .label br{display:block}}


/* Beyond OS 2.1 vector hero: additive overrides preserve the existing page CSS. */
.ecosystem-svg{position:absolute;inset:0;width:100%;height:100%;overflow:visible;z-index:0;pointer-events:none}.orbit>.planet,.orbit>.orbit-copy{z-index:2}.svg-orbits use{vector-effect:non-scaling-stroke}.svg-gateway{transform-box:fill-box;transform-origin:center;animation:gateway-breathe 6.8s ease-in-out infinite}.svg-atom{transform-box:fill-box;transform-origin:center;animation:atom-drift 14s linear infinite}.svg-sheen{animation:sheen-pulse 5.8s ease-in-out infinite}.svg-connections path{stroke-dasharray:7 13;animation:connection-flow 13s linear infinite}.svg-connections path:nth-child(2){animation-delay:-4s}.svg-connections path:nth-child(3){animation-delay:-8s}@keyframes gateway-breathe{0%,100%{opacity:.92}50%{opacity:1}}@keyframes atom-drift{to{transform:rotate(360deg)}}@keyframes sheen-pulse{0%,65%,100%{opacity:.13}78%{opacity:.34}}@keyframes connection-flow{to{stroke-dashoffset:-120}}html[data-theme="light"] .ecosystem-svg #gatewaySurface stop:first-child{stop-color:#ffffff}html[data-theme="light"] .ecosystem-svg #gatewaySurface stop:nth-child(2){stop-color:#eceefe}html[data-theme="light"] .ecosystem-svg #gatewaySurface stop:last-child{stop-color:#dfe3f7}html[data-theme="light"] .svg-ambient circle{opacity:.05}html[data-theme="light"] .svg-nucleus{animation:nucleusPulse 4s ease-in-out infinite;transform-origin:center}.svg-nucleus circle{filter:drop-shadow(0 0 10px rgba(168,85,247,.55))}html[data-theme="light"] .svg-nucleus circle{filter:drop-shadow(0 0 6px rgba(124,58,237,.35))}@keyframes nucleusPulse{0%,100%{transform:scale(1)}50%{transform:scale(1.08)}}@media(prefers-reduced-motion:reduce){.svg-gateway,.svg-atom,.svg-sheen,.svg-connections path{animation:none!important}.svg-particles{display:none}}@media(max-width:560px){.ecosystem-svg{inset:2% -2% -2%;width:104%;height:100%}.svg-connections{opacity:.72}}
.root-live-guide{display:grid;grid-template-columns:repeat(2,minmax(0,1fr)) auto;gap:10px;align-items:stretch;margin:10px 0 4px}.root-live-guide>div,.root-live-guide>a{display:flex;flex-direction:column;justify-content:center;min-height:82px;padding:13px 15px;border-radius:14px;border:1px solid rgba(255,255,255,.12);background:rgba(255,255,255,.055);text-decoration:none;color:#fff}.root-live-guide b{font-size:12px}.root-live-guide span{margin-top:5px;font-weight:850}.root-live-guide small{margin-top:3px;color:#aeb4c7}.root-live-guide>a{font-weight:850;color:#d7caff}@media(max-width:760px){.root-live-guide{grid-template-columns:1fr}.root-live-guide>a{min-height:52px}}

/* 2.2 hero scale refinement */
@media(min-width:851px){.hero{min-height:720px;grid-template-columns:.82fr 1.18fr;gap:64px;padding:64px 0 72px}.orbit{width:108%;margin-left:-4%;}.hero h1{font-size:clamp(66px,7.8vw,112px)}.core{width:190px}.planet{width:124px}}


html[data-theme="sunset"]{background:#1a0d24}html[data-theme="sunset"] body{color:#fff7f2;background:radial-gradient(circle at 76% 8%,rgba(255,111,97,.30),transparent 30%),radial-gradient(circle at 20% 34%,rgba(255,179,71,.18),transparent 35%),linear-gradient(180deg,#32113d 0%,#1d102b 46%,#0d1021 100%)}html[data-theme="sunset"] .brand small,html[data-theme="sunset"] .intro,html[data-theme="sunset"] .world p,html[data-theme="sunset"] .identity p{color:#f2c9c1}html[data-theme="sunset"] .login-btn,html[data-theme="sunset"] .ghost,html[data-theme="sunset"] .theme-toggle{border-color:rgba(255,220,190,.28);background:rgba(83,34,66,.48)}html[data-theme="sunset"] .core{background:radial-gradient(circle at 40% 35%,#fff0c6,#ff8a72 58%,#8f3b73);box-shadow:0 0 0 12px rgba(255,151,98,.09),0 0 52px rgba(255,96,108,.55),inset 0 0 35px rgba(255,222,170,.55)}html[data-theme="sunset"] .planet{background:rgba(70,25,60,.92);border-color:rgba(255,190,160,.30)}html[data-theme="sunset"] .world:before{background:linear-gradient(90deg,rgba(39,12,35,.95),rgba(54,17,46,.72) 42%,rgba(255,126,84,.12) 78%,rgba(36,14,44,.68))}html[data-theme="sunset"] .app{background:rgba(45,18,47,.78);border-color:rgba(255,207,176,.18)}html[data-theme="sunset"] .footer{border-color:rgba(255,210,183,.14);color:#d5aeb0}html[data-theme="sunset"] .footer h4{color:#ffe6dc}
</style>
<style>
.login-btn{display:inline-flex!important;align-items:center;justify-content:center;min-height:47px;padding:0 21px!important;border:1px solid rgba(255,255,255,.28)!important;border-radius:9px;text-decoration:none!important;font-weight:850!important;font-size:13px!important;background:rgba(255,255,255,.04);transition:.2s}
.login-btn:hover{border-color:#a99cff!important;background:rgba(112,87,255,.14)}
.core{font-size:0}.core .atom{position:relative;display:block;width:104px;height:104px;background:none;color:inherit}.core .atom i{position:absolute;display:block;inset:28px 4px;border:5px solid #7f6dff;border-radius:50%;transform:rotate(0deg)}.core .atom i:nth-child(2){transform:rotate(60deg);border-color:#3f91ff}.core .atom i:nth-child(3){transform:rotate(120deg);border-color:#35d69b}.core .atom b{position:absolute;left:50%;top:50%;width:62px;height:62px;border-radius:18px;transform:translate(-50%,-50%);background:#090519 url('assets/img/keyhole-hero.webp') center 72%/180% auto no-repeat;border:2px solid rgba(212,141,255,.82);box-shadow:0 0 0 5px rgba(90,70,255,.12),0 0 28px #8a61ff;z-index:2}
@media(max-width:560px){.nav{gap:8px}.nav .login-btn,.nav .primary{min-height:42px;padding:0 12px!important}.nav .login-btn{display:inline-flex!important}}
</style>
<style>
.theme-toggle{width:43px;height:43px;flex:0 0 43px;border:1px solid rgba(255,255,255,.22);border-radius:50%;display:grid;place-items:center;background:rgba(255,255,255,.05);color:inherit;font-size:18px;cursor:pointer}.theme-toggle:hover{background:rgba(112,87,255,.15)}
.core .atom b{top:43%;width:46px;height:46px;border-radius:50%;background:#0b0620 url('assets/img/keyhole-hero.webp') center 67%/235% auto no-repeat;border:2px solid rgba(230,186,255,.9);box-shadow:0 0 0 6px rgba(90,70,255,.12),0 0 30px #8a61ff}.core .atom b:after{content:"";position:absolute;left:50%;top:29px;width:31px;height:40px;transform:translateX(-50%);clip-path:polygon(33% 0,67% 0,100% 100%,0 100%);background:#0b0620 url('assets/img/keyhole-hero.webp') center 79%/300% auto no-repeat;border-bottom:2px solid rgba(230,186,255,.85);filter:drop-shadow(0 8px 8px rgba(91,70,255,.42));z-index:-1}.core{overflow:visible}.core .atom{filter:drop-shadow(0 0 10px rgba(104,81,255,.35))}
.benefits{display:none}.division-icon{display:block;width:40px;height:40px;margin:0 auto 4px}.division-icon svg,.world-icon svg{display:block;width:100%;height:100%;fill:none;stroke:currentColor;stroke-width:2.35;stroke-linecap:round;stroke-linejoin:round}.world-icon svg{width:34px;height:34px}.orbit-copy{position:absolute;top:2%;left:50%;transform:translateX(-50%);width:90%;text-align:center;z-index:3;pointer-events:none}.orbit-copy strong{display:block;font-size:12px;letter-spacing:.08em;color:#dce1f2}.orbit-copy span{display:block;margin-top:5px;color:#a99cff;font-size:11px;font-weight:900;letter-spacing:.13em;text-transform:uppercase}.ring{opacity:.7;aspect-ratio:1.68/1;animation:ring-orbit 46s linear infinite}.ring.r2{opacity:.66;animation-duration:62s;animation-direction:reverse}.planet{transition:background .25s ease,box-shadow .25s ease}.planet:hover,.planet:focus-visible{background:rgba(13,18,35,.97);box-shadow:0 0 46px currentColor;z-index:5}.planet em{display:block;max-height:0;opacity:0;overflow:hidden;font-size:10px;font-style:normal;letter-spacing:.08em;transition:max-height .25s ease,opacity .25s ease,margin .25s ease}.planet:hover em,.planet:focus-visible em{max-height:18px;opacity:1;margin-top:3px}.welcome{box-shadow:0 18px 50px rgba(42,21,105,.32),inset 0 1px rgba(255,255,255,.12)}.welcome-bits,.id-action small{color:#ffe17a!important;font-weight:900}.core .atom{animation:atom-float 7s ease-in-out infinite}.core .atom b{animation:keyhole-pulse 5.8s ease-in-out infinite}@keyframes ring-orbit{to{transform:rotate(360deg)}}@keyframes atom-float{0%,100%{transform:translateY(0)}50%{transform:translateY(-5px)}}@keyframes removed-keyhole-pulse{0%,72%,100%{box-shadow:0 0 0 6px rgba(90,70,255,.12),0 0 30px #8a61ff}84%{box-shadow:0 0 0 9px rgba(90,70,255,.16),0 0 48px #b174ff}}@media(prefers-reduced-motion:reduce){.ring,.core .atom,.core .atom b{animation:none!important}}@media(max-width:560px){.orbit-copy{top:0}.orbit-copy strong{font-size:10px}.orbit-copy span{font-size:9px}.division-icon{width:28px;height:28px}.planet em{display:none}}
html[data-theme="light"]{background:#f4f6fc}html[data-theme="light"] body{color:#15182a;background:radial-gradient(circle at 75% 10%,rgba(112,87,255,.13),transparent 30%),linear-gradient(180deg,#fbfcff,#eef2fb 72%)}html[data-theme="light"] .brand small,html[data-theme="light"] .intro,html[data-theme="light"] .world p,html[data-theme="light"] .identity p{color:#596077}html[data-theme="light"] .nav>a:not(.primary),html[data-theme="light"] .login-btn{color:#171a2e}html[data-theme="light"] .login-btn,html[data-theme="light"] .ghost,html[data-theme="light"] .theme-toggle{border-color:rgba(23,26,46,.2);background:rgba(255,255,255,.62)}html[data-theme="light"] .benefits{color:#4d546a}html[data-theme="light"] .benefits b{color:#20243a}html[data-theme="light"] .orbit:before{background:radial-gradient(circle,rgba(99,78,255,.18),transparent 54%)}html[data-theme="light"] .core{background:radial-gradient(circle at 40% 35%,#fff,#e8eafd 62%);box-shadow:0 0 0 12px rgba(92,88,255,.07),0 0 45px #6c51ff55,inset 0 0 35px #d8d9f2}html[data-theme="light"] .planet{background:rgba(255,255,255,.92)}html[data-theme="light"] .world:before{background:linear-gradient(90deg,rgba(255,255,255,.94),rgba(255,255,255,.74) 42%,rgba(255,255,255,.18) 78%,rgba(255,255,255,.58))}html[data-theme="light"] .world.health{background:linear-gradient(130deg,#ecfff4,#dff8f6 55%,#eaf5fb)}html[data-theme="light"] .world.education{background:linear-gradient(130deg,#fff8e8,#f2eaff 56%,#fff2dc)}html[data-theme="light"] .world.finance{background:linear-gradient(130deg,#eef5ff,#e2ecff 58%,#f2e9ff)}html[data-theme="light"] .world p{color:#565e73}html[data-theme="light"] .app{color:#22263a;background:rgba(255,255,255,.78);border-color:rgba(35,40,65,.14)}html[data-theme="light"] .identity{color:#fff}html[data-theme="light"] .identity p{color:#eee8f7}html[data-theme="light"] .footer{border-color:rgba(23,26,46,.12);color:#626a80}html[data-theme="light"] .footer h4{color:#30364b}html[data-theme="light"] .footer .brand{color:#171a2e}@media(max-width:560px){.theme-toggle{width:40px;height:40px;flex-basis:40px}.nav{gap:6px}}
</style>
<style>
.nav a[href="#entertainment"]{border-color:var(--pink)}.nav-tools{display:flex;align-items:center;gap:8px}.visually-hidden{position:absolute!important;width:1px!important;height:1px!important;overflow:hidden!important;clip:rect(0,0,0,0)!important;white-space:nowrap!important}.locale-picker{position:relative;width:43px;height:43px;flex:0 0 43px}.locale-picker:before{content:"🌐";position:absolute;inset:0;display:grid;place-items:center;font-size:18px;pointer-events:none}.locale-picker select{width:100%;height:100%;border:1px solid rgba(255,255,255,.22);border-radius:50%;background:rgba(255,255,255,.05);color:transparent;cursor:pointer;appearance:none}.locale-picker select:focus-visible{outline:2px solid #a99cff;outline-offset:2px}.locale-picker option{color:#111;background:#fff}.world.entertainment{--accent:var(--pink);background:radial-gradient(circle at 72% 28%,#f2469d55 0,transparent 31%),linear-gradient(130deg,#19071b,#2a0b2f 54%,#101438)}.daily-demos{display:grid;gap:14px;margin:0 auto 24px}.daily-demo{position:relative;overflow:hidden;display:grid;grid-template-columns:auto 1fr auto;align-items:center;gap:20px;padding:24px 26px;border:1px solid rgba(255,255,255,.14);border-radius:21px;text-decoration:none;background:linear-gradient(135deg,rgba(18,22,48,.94),rgba(54,28,79,.86));box-shadow:0 16px 44px rgba(0,0,0,.25)}.daily-demo.french{border-color:rgba(78,137,255,.42);background:linear-gradient(135deg,rgba(8,35,75,.95),rgba(52,24,91,.9))}.daily-demo.verse{border-color:rgba(81,219,120,.36);background:linear-gradient(135deg,rgba(10,50,35,.96),rgba(26,68,49,.88))}.daily-demo-icon{font-size:34px}.daily-demo-kicker{display:block;margin-bottom:6px;color:#aaa0ff;font-size:10px;font-weight:950;letter-spacing:.15em;text-transform:uppercase}.daily-demo.verse .daily-demo-kicker{color:#8ce5a8}.daily-demo h2{margin:0 0 5px;font-size:clamp(21px,3vw,29px)}.daily-demo p{margin:0;color:#c5cada;line-height:1.5}.daily-demo-action{font-size:13px;font-weight:900;white-space:nowrap}.demo-badge{position:absolute;right:13px;top:10px;color:#b8bed2;font-size:8px;font-weight:900;letter-spacing:.12em;text-transform:uppercase}html[data-theme="light"] .locale-picker select{border-color:rgba(23,26,46,.2);background:rgba(255,255,255,.62)}html[data-theme="light"] .daily-demo{color:#fff}@media(max-width:650px){.daily-demo{grid-template-columns:auto 1fr;padding:21px 18px}.daily-demo-action{grid-column:2}.nav-tools{gap:5px}.locale-picker,.theme-toggle{width:40px;height:40px;flex-basis:40px}}
</style>
<style>
.nav a[href="#wallet"]{border-color:var(--blue)}
.world.wallet{--accent:var(--blue);background:radial-gradient(ellipse at 75% 30%,#2f62c555 0,transparent 34%),linear-gradient(130deg,#061328,#081d45 58%,#190c37)}
html[data-theme="light"] .world.wallet{background:linear-gradient(130deg,#eef5ff,#e2ecff 58%,#f2e9ff)}
#beyond-os-shell .locale-picker,#beyond-os-shell .theme-toggle{width:38px;height:38px;flex:0 0 38px}
@media(max-width:650px){#beyond-os-shell .bos-actions{gap:6px}}
</style>
</head>
<body>
<header class="top wrap">
    <a class="brand" href="./"><img src="assets/icons/beyond-os-192.webp" alt="" style="width:34px;height:34px;border-radius:10px;vertical-align:middle;margin-right:9px">BEYOND <span>OS</span><small>THE CONNECTED IMAGINATION ECOSYSTEM</small></a>
    <nav class="nav" aria-label="Primary navigation">
        <a href="app-store/">App Store</a><a href="beyond-finance/">Wallet</a><a href="beyond-investing/">Investing</a><a href="beyond-tv/">TV</a>
    </nav>
</header>
<main>
<section class="hero wrap">
    <div>
        <h1><span class="h">Health.</span><span class="e">Education.</span><span class="f">Wallet.</span><span class="x">Entertainment.</span></h1>
        <p class="tagline">Live. Learn. Earn. Explore.</p>
        <p class="intro">Everything you need to grow, create and discover—connected in one ecosystem.</p>
        <div class="hero-actions">
            <a class="ghost" href="app-store/">Open the App Store &nbsp;▶</a>
        </div>
        <div class="benefits"><span><b>∞</b> Every possibility, connected</span></div>
    </div>
    <div class="orbit" aria-label="Health, education, wallet and entertainment orbit Beyond OS">
        <svg class="ecosystem-svg" viewBox="0 0 720 610" role="img" aria-labelledby="ecosystemTitle ecosystemDesc">
            <title id="ecosystemTitle">The Beyond OS connected ecosystem</title>
            <desc id="ecosystemDesc">Health, education, wallet and entertainment connect in one ecosystem.</desc>
            <defs>
                <radialGradient id="gatewaySurface" cx="38%" cy="30%" r="76%">
                    <stop offset="0" stop-color="#342466"/><stop offset=".58" stop-color="#0b0b1d"/><stop offset="1" stop-color="#050713"/>
                </radialGradient>
                <linearGradient id="atomStroke" x1="0" y1="0" x2="1" y2="1">
                    <stop offset="0" stop-color="#4f8cff"/><stop offset=".5" stop-color="#8d58ff"/><stop offset="1" stop-color="#4ee097"/>
                </linearGradient>
                <linearGradient id="keyholeFill" x1="0" y1="0" x2="0" y2="1">
                    <stop offset="0" stop-color="#e853c1"/><stop offset=".46" stop-color="#755cff"/><stop offset="1" stop-color="#080a18"/>
                </linearGradient>
                <filter id="gatewayGlow" x="-80%" y="-80%" width="260%" height="260%">
                    <feGaussianBlur stdDeviation="13" result="blur"/><feFlood flood-color="#7657ff" flood-opacity=".78"/><feComposite in2="blur" operator="in"/><feMerge><feMergeNode/><feMergeNode in="SourceGraphic"/></feMerge>
                </filter>
                <filter id="particleGlow" x="-300%" y="-300%" width="700%" height="700%"><feGaussianBlur stdDeviation="4" result="b"/><feMerge><feMergeNode in="b"/><feMergeNode in="SourceGraphic"/></feMerge></filter>
                <path id="orbitA" d="M86 304 C92 126 628 110 638 304 C648 496 99 487 86 304Z"/>
                <path id="orbitB" d="M151 128 C324 56 594 179 566 345 C541 500 252 573 121 416 C18 292 42 174 151 128Z"/>
                <path id="orbitC" d="M122 418 C58 273 237 109 430 122 C608 134 681 309 564 435 C451 558 188 567 122 418Z"/>
                <path id="orbitD" d="M105 228 C214 76 518 70 625 226 C729 378 557 548 357 544 C160 541 3 374 105 228Z"/>
            </defs>
            <g class="svg-ambient" aria-hidden="true">
                <circle cx="360" cy="306" r="150" fill="#775cff" opacity=".08"/>
                <circle cx="360" cy="306" r="112" fill="#4b8cff" opacity=".06"/>
            </g>
            <g class="svg-orbits" fill="none" aria-hidden="true">
                <use href="#orbitA" stroke="#5792ff" stroke-opacity=".36"/>
                <use href="#orbitB" stroke="#ffbd49" stroke-opacity=".27"/>
                <use href="#orbitC" stroke="#54df83" stroke-opacity=".26"/>
                <use href="#orbitD" stroke="#f2469d" stroke-opacity=".30"/>
            </g>
            <g class="svg-particles" aria-hidden="true" filter="url(#particleGlow)">
                <circle r="5" fill="#70a7ff"><animateMotion dur="12s" repeatCount="indefinite"><mpath href="#orbitA"/></animateMotion></circle>
                <circle r="4" fill="#ffd16b"><animateMotion dur="16s" begin="-6s" repeatCount="indefinite"><mpath href="#orbitB"/></animateMotion></circle>
                <circle r="4.5" fill="#70e99a"><animateMotion dur="18s" begin="-11s" repeatCount="indefinite"><mpath href="#orbitC"/></animateMotion></circle>
                <circle r="4.5" fill="#ff6fba"><animateMotion dur="21s" begin="-9s" repeatCount="indefinite"><mpath href="#orbitD"/></animateMotion></circle>
            </g>
            <g class="svg-connections" fill="none" stroke-width="2" stroke-linecap="round" aria-hidden="true">
                <path d="M313 267 C262 228 205 210 164 192" stroke="#51db78" stroke-opacity=".32"/>
                <path d="M407 267 C469 229 519 219 570 208" stroke="#ffbf32" stroke-opacity=".30"/>
                <path d="M335 372 C301 421 251 463 210 505" stroke="#448cff" stroke-opacity=".34"/>
                <path d="M389 370 C432 418 490 462 544 505" stroke="#f2469d" stroke-opacity=".34"/>
            </g>
            <g class="svg-gateway" transform="translate(360 306)" filter="url(#gatewayGlow)">
                <circle r="101" fill="none" stroke="#9259ff" stroke-width="3" opacity=".92"/>
                <circle r="84" fill="url(#gatewaySurface)" stroke="#6d69ff" stroke-width="2"/>
                <g class="svg-atom" fill="none" stroke="url(#atomStroke)" stroke-width="8" stroke-linecap="round">
                    <ellipse rx="72" ry="31" transform="rotate(0)"/>
                    <ellipse rx="72" ry="31" transform="rotate(60)"/>
                    <ellipse rx="72" ry="31" transform="rotate(120)"/>
                </g>
                <g class="svg-nucleus">
                    <path d="M0-25A17 17 0 0 0-9.5 6L-17 32H17L9.5 6A17 17 0 0 0 0-25Z" fill="#090b18" stroke="#f2eaff" stroke-width="5" stroke-linejoin="round" style="filter:drop-shadow(0 0 10px rgba(168,85,247,.85))"/>
                    <path d="M0-17A9 9 0 0 0-4.5-.2L-9 24H9L4.5-.2A9 9 0 0 0 0-17Z" fill="url(#keyholeFill)"/>
                </g>
                <circle class="svg-sheen" cx="-22" cy="-36" r="10" fill="#fff" opacity=".18"/>
            </g>
        </svg>
        <a class="planet ph" href="app-store/#featured"><span><i>♥</i>HEALTH</span></a>
        <a class="planet pe" href="app-store/"><span><i>🏫</i>EDUCATION</span></a>
        <a class="planet pf" href="beyond-finance/"><span><i>👛</i>WALLET</span></a>
        <a class="planet px" href="beyond-tv/"><span class="label"><i>▶</i>ENTERTAIN<br>MENT</span></a>
    </div>
</section>
<?php
$classicSchedule = beyond_classic_schedule_state();
$classicCurrent = $classicSchedule['current'];
$classicNext = $classicSchedule['next'];
$cartoonSchedule = beyond_cartoons_schedule_state();
$cartoonCurrent = $cartoonSchedule['current'];
$cartoonNext = $cartoonSchedule['next'];
$tvCataloguePath = __DIR__ . '/beyond-tv/data/catalog.json';
$tvFeatured = [[
    'slug' => 'classic-cartoon-theater',
    'title' => 'Classic Cartoon Theater',
    'type' => 'Live Channel',
    'year' => 'LIVE',
    'rating' => 'FREE',
    'description' => $classicCurrent['title'] . ' now · Up next: ' . $classicNext['title'] . ' · Vancouver time',
    'thumbnail' => 'https://archive.org/services/img/SnowWhiteWithBettyBoop1933',
    'gradient' => 'linear-gradient(135deg,#230a2d,#891b59 58%,#e58c2c)',
    'icon' => '🎞️',
    'source_type' => 'live_channel',
], [
    'slug' => 'beyond-cartoons',
    'title' => 'Beyond Cartoons',
    'type' => 'Live Channel',
    'year' => 'LIVE',
    'rating' => 'FREE',
    'description' => $cartoonCurrent['title'] . ' now · Up next: ' . $cartoonNext['title'] . ' · Vancouver time',
    'thumbnail' => 'https://i.ytimg.com/vi/vX2g-VnhbU8/maxresdefault.jpg',
    'gradient' => 'linear-gradient(135deg,#06355a,#7353ff 52%,#ff4f9a)',
    'icon' => '📺',
    'source_type' => 'live_channel',
]];
if (is_file($tvCataloguePath)) {
    $decodedTvCatalogue = json_decode((string) file_get_contents($tvCataloguePath), true);
    if (is_array($decodedTvCatalogue)) {
        $preferredSlugs = ['betty-boop-collection', 'cats-1998', 'bubble-guppies', 'fairly-oddparents', 'teen-titans'];
        foreach ($preferredSlugs as $preferredSlug) {
            foreach ($decodedTvCatalogue as $tvItem) {
                if (($tvItem['slug'] ?? '') === $preferredSlug) {
                    $tvFeatured[] = $tvItem;
                    break;
                }
            }
        }
        if (!$tvFeatured) {
            $tvFeatured = array_merge($tvFeatured, array_slice($decodedTvCatalogue, 0, 5));
        }
    }
}
?>
<style>
.tv-discovery{margin:0 auto 22px;padding:28px;border:1px solid rgba(143,92,255,.38);border-radius:24px;background:radial-gradient(circle at 95% 0,rgba(109,73,255,.25),transparent 34%),linear-gradient(145deg,#070914,#111329 60%,#090b18);box-shadow:0 22px 65px rgba(0,0,0,.35);overflow:hidden}.tv-discovery-head{display:flex;align-items:end;justify-content:space-between;gap:24px;margin-bottom:20px}.tv-discovery-kicker{display:block;color:#9e8cff;font-size:10px;font-weight:950;letter-spacing:.16em;text-transform:uppercase;margin-bottom:8px}.tv-discovery h2{margin:0;font-size:clamp(30px,5vw,52px);letter-spacing:-.055em}.tv-discovery-head p{max-width:500px;margin:9px 0 0;color:#bfc3d2;line-height:1.55}.tv-carousel-actions{display:flex;gap:9px}.tv-carousel-btn{width:43px;height:43px;border-radius:50%;border:1px solid rgba(255,255,255,.22);color:#fff;background:rgba(255,255,255,.06);font-size:21px;cursor:pointer}.tv-carousel-btn:hover{background:rgba(255,255,255,.13)}.tv-carousel{display:grid;grid-auto-flow:column;grid-auto-columns:minmax(230px,29%);gap:15px;overflow-x:auto;scroll-snap-type:x mandatory;scrollbar-width:none;padding:2px 2px 12px}.tv-carousel::-webkit-scrollbar{display:none}.tv-card{position:relative;min-height:310px;border-radius:19px;overflow:hidden;scroll-snap-align:start;text-decoration:none;border:1px solid rgba(255,255,255,.14);background:#101329;isolation:isolate;box-shadow:0 13px 34px rgba(0,0,0,.25);transition:transform .2s ease,border-color .2s ease}.tv-card:hover{transform:translateY(-4px);border-color:rgba(177,145,255,.72)}.tv-card-art{position:absolute;inset:0;background:var(--tv-gradient,linear-gradient(135deg,#14183c,#6f49ce));background-size:cover;background-position:center}.tv-card-art:after{content:"";position:absolute;inset:0;background:linear-gradient(180deg,rgba(4,6,14,.04) 20%,rgba(4,6,14,.42) 54%,rgba(4,6,14,.98) 100%)}.tv-card-copy{position:absolute;inset:auto 0 0;padding:20px;z-index:2}.tv-card-top{position:absolute;top:14px;left:14px;right:14px;display:flex;justify-content:space-between;align-items:flex-start;z-index:2}.tv-badge{display:inline-flex;padding:6px 8px;border-radius:999px;background:rgba(4,6,14,.74);backdrop-filter:blur(10px);font-size:9px;font-weight:950;letter-spacing:.08em;text-transform:uppercase}.tv-badge.pending{background:#f4b63a;color:#14100a}.tv-card-icon{font-size:28px;filter:drop-shadow(0 5px 12px rgba(0,0,0,.4))}.tv-card h3{margin:0 0 7px;font-size:23px;letter-spacing:-.035em}.tv-card-meta{margin:0 0 8px;color:#d9dbea;font-size:11px;font-weight:800}.tv-card-copy p{margin:0;color:#afb5c8;font-size:12px;line-height:1.45;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden}.tv-card-cta{display:inline-block;margin-top:13px;font-size:11px;font-weight:950;color:#fff}.tv-discovery-footer{display:flex;align-items:center;justify-content:space-between;gap:20px;margin-top:12px;color:#9fa5ba;font-size:11px}.tv-discovery-footer .primary{min-height:43px}.tv-dots{display:flex;gap:6px}.tv-dot{width:6px;height:6px;border:0;border-radius:50%;background:rgba(255,255,255,.25);padding:0}.tv-dot.active{width:19px;border-radius:999px;background:#8b72ff}@media(max-width:800px){.tv-discovery{padding:21px 16px}.tv-discovery-head{align-items:flex-start}.tv-carousel-actions{display:none}.tv-carousel{grid-auto-columns:82%}.tv-discovery-footer{align-items:flex-start;flex-direction:column}.tv-discovery-footer .primary{width:100%}}
</style>
<section class="home-live-stage wrap" data-channel-theme="cartoons" aria-label="Beyond TV live player">
  <div class="home-live-stage__top">
    <div>
      <span class="tv-discovery-kicker" id="homeLiveKicker">Beyond TV · Channel 2 live</span>
      <h2 id="homeLiveHeading">Beyond Cartoons is playing now.</h2>
      <p id="homeLiveDescription"><strong><?= htmlspecialchars((string)$cartoonCurrent['icon'].' '.$cartoonCurrent['title']) ?></strong> · <?= htmlspecialchars((string)$cartoonCurrent['lineup']) ?> · Up next: <?= htmlspecialchars((string)$cartoonNext['title']) ?> · Vancouver time</p>
    </div>
    <div class="home-live-switch" role="group" aria-label="Choose from eight Beyond TV channels">
      <button type="button" data-home-channel="classic" data-channel-number="1" data-channel-name="Classic Cartoon Theater" data-endpoint="/beyond-tv/api/classic-live.php" data-open="/beyond-tv/channel.php?slug=classic-cartoon-theater">🎞️ <span>Ch 1 · Classic</span></button>
      <button type="button" class="active" data-home-channel="cartoons" data-channel-number="2" data-channel-name="Beyond Cartoons" data-endpoint="/beyond-tv/api/beyond-cartoons-live.php" data-open="/beyond-tv/channel.php?slug=beyond-cartoons">📺 <span>Ch 2 · Toons</span></button>
      <button type="button" data-home-channel="preschool" data-channel-number="3" data-channel-name="Preschool TV" data-embed="https://www.youtube-nocookie.com/embed/61fSXCbzF1M?autoplay=1&amp;mute=1&amp;playsinline=1&amp;rel=0&amp;enablejsapi=1" data-now="Bluey Full Episodes" data-next="Bubble Guppies" data-icon="🐾" data-open="/beyond-tv/channel.php?slug=bubble-guppies">🐾 <span>Ch 3 · Kids</span></button>
      <button type="button" data-home-channel="space" data-channel-number="4" data-channel-name="Beyond Space" data-endpoint="/beyond-tv/api/space-live.php" data-now="The Sun & The Milky Way" data-next="Weekly space rotation" data-icon="🛰️" data-open="/beyond-tv/channel.php?slug=space-tv">🛰️ <span>Ch 4 · Space</span></button>
      <button type="button" data-home-channel="ancient" data-channel-number="5" data-channel-name="Beyond Ancient" data-embed="https://www.youtube-nocookie.com/embed/BR2ZMj3o5EU?autoplay=1&amp;mute=1&amp;playsinline=1&amp;rel=0&amp;enablejsapi=1" data-now="Ancient Egypt Documentary" data-next="Pyramids, pharaohs and archaeology" data-icon="𓂀" data-open="/beyond-ancient/">𓂀 <span>Ch 5 · Ancient</span></button>
      <button type="button" data-home-channel="cinema" data-channel-number="6" data-channel-name="Beyond Movies" data-embed="https://www.youtube-nocookie.com/embed/videoseries?list=PLdk1SI29-q9yrN9GFMnOAYmC_tcw5v59L&amp;autoplay=1&amp;mute=1&amp;playsinline=1&amp;rel=0&amp;enablejsapi=1" data-now="Beyond Movies Playlist" data-next="Next movie in the playlist" data-icon="🎬" data-open="/beyond-tv/channel.php?slug=classic-cinema">🎬 <span>Ch 6 · Movies</span></button>
      <button type="button" data-home-channel="french" data-channel-number="7" data-channel-name="Beyond French" data-embed="https://www.youtube-nocookie.com/embed/hd0_GZHHWeE?autoplay=1&amp;mute=1&amp;playsinline=1&amp;rel=0&amp;enablejsapi=1" data-now="Français du jour" data-next="Daily French challenge" data-icon="🇫🇷" data-open="/beyond-french/">🇫🇷 <span>Ch 7 · French</span></button>
      <button type="button" data-home-channel="health" data-channel-number="8" data-channel-name="Beyond Health" data-embed="https://www.youtube-nocookie.com/embed/7_chERnJ0gE?autoplay=1&amp;mute=1&amp;playsinline=1&amp;rel=0&amp;enablejsapi=1" data-now="Featured Health Presentation" data-next="Replay" data-icon="💚" data-open="/beyond-health/">💚 <span>Ch 8 · Health</span></button>
    </div>
  </div>
  <div class="home-live-player">
    <iframe id="homeBeyondTvPlayer" src="<?= htmlspecialchars((string)$cartoonSchedule['embed_url']) ?>" title="Beyond Cartoons live on Beyond TV" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" referrerpolicy="strict-origin-when-cross-origin" allowfullscreen></iframe>
  </div>
  <div class="home-live-meta">
    <div><span class="live-dot"></span><b id="homeLiveChannelName">Beyond Cartoons</b><span id="homeLiveNow"><?= htmlspecialchars((string)$cartoonCurrent['title']) ?></span></div>
    <a href="/beyond-tv/channel.php?slug=beyond-cartoons" id="homeLiveOpen">Open full channel →</a>
  </div>
</section>
<style>
.home-live-stage{margin:0 auto 22px;padding:22px;border:1px solid rgba(143,92,255,.38);border-radius:26px;background:linear-gradient(145deg,#070914,#111329 60%,#090b18);box-shadow:0 24px 70px rgba(0,0,0,.38)}.home-live-stage__top{display:flex;justify-content:space-between;gap:20px;align-items:end;margin-bottom:16px}.home-live-stage h2{margin:0;font-size:clamp(30px,4.8vw,52px);letter-spacing:-.05em}.home-live-stage p{margin:8px 0 0;color:#bfc3d2}.home-live-switch{display:grid;grid-template-columns:repeat(4,minmax(112px,1fr));gap:8px;min-width:min(100%,520px)}.home-live-switch button{display:flex;align-items:center;justify-content:center;gap:7px;border:1px solid rgba(255,255,255,.16);background:rgba(255,255,255,.06);color:#fff;padding:11px 12px;border-radius:14px;font-weight:900;cursor:pointer;white-space:nowrap}.home-live-switch button:hover{background:rgba(255,255,255,.11)}.home-live-switch button.active{background:#7f5cff;border-color:#9c83ff;box-shadow:0 8px 24px rgba(127,92,255,.28)}.home-live-player{aspect-ratio:16/9;border-radius:22px;overflow:hidden;background:#000;box-shadow:0 18px 55px rgba(0,0,0,.45)}.home-live-player iframe{display:block;width:100%;height:100%;border:0}.home-live-meta{display:flex;justify-content:space-between;gap:16px;align-items:center;padding-top:14px}.home-live-meta>div{display:flex;gap:10px;align-items:center;flex-wrap:wrap}.home-live-meta span{color:#b8bed0}.home-live-meta a{color:#fff;font-weight:900;text-decoration:none}.live-dot{width:9px;height:9px;border-radius:50%;background:#ff365f;box-shadow:0 0 0 5px rgba(255,54,95,.14)}@media(max-width:800px){.home-live-stage{padding:16px;border-radius:20px}.home-live-stage__top{display:block}.home-live-switch{grid-template-columns:repeat(2,minmax(0,1fr));margin-top:14px;min-width:0}.home-live-switch button{width:100%}.home-live-player{border-radius:14px}.home-live-meta{align-items:flex-start;flex-direction:column}}
</style>
<style>
.home-live-stage{position:relative;isolation:isolate;overflow:hidden}.home-live-stage:before{content:"";position:absolute;inset:0;z-index:-2;background-image:linear-gradient(145deg,rgba(7,9,20,.74),rgba(9,11,24,.9)),url('/beyond-tv/assets/img/channel-backgrounds-sprite.png');background-repeat:no-repeat;background-size:400% 200%;background-position:var(--channel-bg,33.333% 0);opacity:.82;transition:background-position .35s ease}.home-live-stage:after{content:"";position:absolute;inset:0;z-index:-1;background:linear-gradient(180deg,rgba(4,5,12,.28),rgba(4,5,12,.72));pointer-events:none}.home-live-stage[data-channel-theme="classic"]{--channel-bg:0 0}.home-live-stage[data-channel-theme="cartoons"]{--channel-bg:33.333% 0}.home-live-stage[data-channel-theme="preschool"]{--channel-bg:66.666% 0}.home-live-stage[data-channel-theme="space"]{--channel-bg:100% 0}.home-live-stage[data-channel-theme="ancient"]{--channel-bg:0 100%}.home-live-stage[data-channel-theme="cinema"]{--channel-bg:33.333% 100%}.home-live-stage[data-channel-theme="french"]{--channel-bg:66.666% 100%}.home-live-stage[data-channel-theme="health"]{--channel-bg:100% 100%}
.home-live-stage.wrap{width:min(1600px,calc(100vw - 24px));max-width:none;padding:30px}.home-live-stage__top{align-items:start;flex-direction:column}.home-live-switch{width:100%;min-width:0;grid-template-columns:repeat(8,minmax(0,1fr))}.home-live-switch button{padding:12px 8px}.home-live-player{width:100%;aspect-ratio:16/8.25;min-height:520px}.home-live-stage:before{opacity:.96}.home-live-stage:after{background:linear-gradient(180deg,rgba(4,5,12,.17),rgba(4,5,12,.67))}@media(max-width:1100px){.home-live-switch{grid-template-columns:repeat(4,minmax(0,1fr))}.home-live-player{min-height:0;aspect-ratio:16/9}}@media(max-width:650px){.home-live-stage.wrap{width:calc(100vw - 12px);padding:14px}.home-live-switch{grid-template-columns:repeat(2,minmax(0,1fr))}}
</style>
<script>
(function(){
 const frame=document.getElementById('homeBeyondTvPlayer'); if(!frame)return;
 const stage=document.querySelector('.home-live-stage');
 const buttons=[...document.querySelectorAll('[data-home-channel]')];
 const name=document.getElementById('homeLiveChannelName');
 const now=document.getElementById('homeLiveNow');
 const open=document.getElementById('homeLiveOpen');
 const kicker=document.getElementById('homeLiveKicker');
 const heading=document.getElementById('homeLiveHeading');
 const description=document.getElementById('homeLiveDescription');
 const clean=value=>String(value||'').replace(/[&<>"']/g,char=>({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#039;'}[char]));
 function render(button,state={}){
   if(stage)stage.dataset.channelTheme=button.dataset.homeChannel||'cartoons';
   const channelName=button.dataset.channelName||'Beyond TV';
   const channelNumber=button.dataset.channelNumber||'';
   const current=state.current||state.playing||{};
   const next=state.next||{};
   const block=current.title||state.episode_title||button.dataset.now||state.programme||'Live now';
   const shows=current.lineup||button.dataset.now||state.episode_title||'';
   const upNext=next.title||button.dataset.next||'';
   const icon=current.icon||button.dataset.icon||button.textContent.trim().split(' ')[0]||'📺';
   const embed=state.embed_url||state.embed_fallback||button.dataset.embed||'';
   if(embed) frame.src=embed.includes('enablejsapi=1')?embed:(embed+(embed.includes('?')?'&':'?')+'enablejsapi=1');
   name.textContent=channelName;
   now.textContent=block;
   kicker.textContent=`Beyond TV · Channel ${channelNumber} live`;
   heading.textContent=`${channelName} is playing now.`;
   description.innerHTML=`<strong>${clean(icon)} ${clean(block)}</strong>${shows&&shows!==block?` · ${clean(shows)}`:''}${upNext?` · Up next: ${clean(upNext)}`:''} · Vancouver time`;
   open.href=button.dataset.open||'/beyond-tv/';
 }
 async function tune(button){
   buttons.forEach(b=>b.classList.toggle('active',b===button));
   render(button);
   if(!button.dataset.endpoint)return;
   try{
     const res=await fetch(button.dataset.endpoint,{cache:'no-store'});
     if(!res.ok)throw new Error(`HTTP ${res.status}`);
     const data=await res.json();
     render(button,data.state||data);
   }catch(e){ console.warn('Could not tune channel',e); }
 }
 buttons.forEach(button=>button.addEventListener('click',()=>tune(button)));
 setInterval(()=>{const active=document.querySelector('[data-home-channel].active');if(active&&active.dataset.endpoint)tune(active);},60000);
})();
</script>
<div class="wrap" style="display:flex;justify-content:center;margin:22px auto 38px;">
  <a class="primary" href="https://beyondimagination.co.technology/beyond-tv/">Watch Beyond TV &nbsp;→</a>
</div>
<section class="daily-demos wrap" aria-label="Daily featured content">
    <a class="daily-demo french" href="beyond-french/" data-content-slot="francais-du-jour" data-admin-source="beyond-french-daily">
        <span class="demo-badge">Demo · Admin sync ready</span><span class="daily-demo-icon" aria-hidden="true">🇫🇷</span>
        <div><span class="daily-demo-kicker">Beyond French · Français du jour</span><h2>Bonjour — Hello</h2><p>Start the day with one useful French phrase, pronunciation practice and a quick challenge.</p></div><span class="daily-demo-action">Practice now →</span>
    </a>
    <a class="daily-demo verse" href="dailybreath/bible.php?preview=1" data-content-slot="daily-bible-verse" data-admin-source="daily-studio">
        <span class="demo-badge">Demo · Admin sync ready</span><span class="daily-demo-icon" aria-hidden="true">☀️</span>
        <div><span class="daily-demo-kicker">Daily Breath · Bible verse of the day</span><h2>“Be still, and know that I am God.”</h2><p>Psalm 46:10 · A daily moment to read, listen, reflect and breathe.</p></div><span class="daily-demo-action">Read &amp; listen →</span>
    </a>
</section>
<style>
.daily-demos.wrap{width:min(1600px,calc(100vw - 24px));max-width:none;grid-template-columns:repeat(2,minmax(0,1fr));gap:18px;margin-bottom:44px}.daily-demo{min-height:390px;grid-template-columns:1fr;align-content:end;padding:36px;isolation:isolate}.daily-demo:before{content:"";position:absolute;inset:0;z-index:-2;opacity:.8}.daily-demo:after{content:"";position:absolute;inset:0;z-index:-1;background:linear-gradient(180deg,rgba(3,7,18,.08),rgba(3,7,18,.9) 78%)}.daily-demo.french:before{background:radial-gradient(circle at 76% 18%,rgba(255,255,255,.22),transparent 22%),linear-gradient(135deg,#061e51 0%,#163b9d 52%,#d82348 100%)}.daily-demo.verse:before{background:radial-gradient(circle at 75% 16%,rgba(244,217,143,.26),transparent 23%),linear-gradient(135deg,#071d14 0%,#164b31 55%,#6b7b2b 100%)}.daily-demo-icon{position:absolute;right:8%;top:18%;font-size:clamp(70px,11vw,150px);opacity:.22}.daily-demo h2{font-size:clamp(34px,5vw,62px);line-height:.98;max-width:680px}.daily-demo p{max-width:650px;font-size:16px}.daily-demo-action{display:inline-flex;width:max-content;margin-top:18px;padding:12px 16px;border:1px solid rgba(255,255,255,.28);border-radius:999px;background:rgba(255,255,255,.09)}@media(max-width:850px){.daily-demos.wrap{grid-template-columns:1fr}.daily-demo{min-height:330px;padding:26px}}@media(max-width:560px){.daily-demos.wrap{width:calc(100vw - 12px)}.daily-demo{min-height:300px;padding:23px}.daily-demo-action{grid-column:auto}}
</style>
<script src="/beyond-tv/assets/js/app.js?v=2.1.1"></script>
</main>
<footer class="footer wrap">
    <div><a class="brand" href="./">BEYOND <span>OS</span></a><p>The connected imagination ecosystem.</p><p class="copyright">© 2026 Beyond Imagination Corp.</p></div>
    <div><h4>DISCOVER</h4><a href="app-store/">App Store</a><a href="beyond-finance/">Wallet</a><a href="beyond-investing/">Investing</a><a href="beyond-tv/">Beyond TV</a></div>
    <div><h4>COMPANY</h4><a href="about.php">About</a><a href="blog.php">Blog</a><a href="beyond-careers/">Careers</a><a href="contact.php">Contact</a></div>
    <div><h4>SUPPORT</h4><a href="help-center.php">Help Center</a><a href="beyond-id/auth/privacy.php">Privacy Policy</a><a href="beyond-id/auth/terms.php">Terms of Service</a></div>
    <div><h4>FOLLOW US</h4><a href="https://www.instagram.com/beyondimaginationtech/" target="_blank" rel="noopener noreferrer">Instagram @beyondimaginationtech</a></div>
</footer>
<script>
(function(){
const icons={
health:'<svg viewBox="0 0 48 48" aria-hidden="true"><path d="M10 42V15a4 4 0 0 1 4-4h20a4 4 0 0 1 4 4v27M7 42h34M18 42V31h12v11M20 20h8M24 16v8"/></svg>',
education:'<svg viewBox="0 0 48 48" aria-hidden="true"><path d="M7 42V18l17-10 17 10v24M4 42h40M14 24h5v5h-5zM29 24h5v5h-5zM20 42V33h8v9M13 16h22"/></svg>',
wallet:'<svg viewBox="0 0 48 48" aria-hidden="true"><path d="M7 14h31a4 4 0 0 1 4 4v22H7a4 4 0 0 1-4-4V12a4 4 0 0 1 4-4h27v6M31 25h11v9H31a4 4 0 0 1 0-9Z"/></svg>',
entertainment:'<svg viewBox="0 0 48 48" aria-hidden="true"><rect x="6" y="10" width="36" height="28" rx="5"/><path d="m20 18 11 6-11 6V18ZM16 43h16"/></svg>'
};
const names={health:'HEALTH',education:'EDUCATION',wallet:'WALLET',entertainment:'ENTERTAINMENT'},actions={health:'LIVE',education:'LEARN',wallet:'EARN',entertainment:'EXPLORE'},planetClasses={health:'.ph',education:'.pe',wallet:'.pf',entertainment:'.px'};
Object.keys(icons).forEach(function(id){
const planet=document.querySelector(planetClasses[id]);
if(planet)planet.innerHTML='<span><i class="division-icon">'+icons[id]+'</i>'+names[id]+'<em>'+actions[id]+'</em></span>';
const panel=document.querySelector('.world.'+id+' .world-icon');if(panel)panel.innerHTML=icons[id];
});
const orbit=document.querySelector('.orbit');if(orbit){const copy=document.createElement('div');copy.className='orbit-copy';copy.innerHTML='<strong>Live &bull; Learn &bull; Earn &bull; Explore</strong><span>Every Possibility</span>';orbit.appendChild(copy);}
})();
</script>
<script src="assets/js/pwa-install.js" defer></script></body>
</html>
