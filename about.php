<?php
declare(strict_types=1);
require_once __DIR__ . '/includes/ecosystem.php';
beyond_nav_bootstrap('About Beyond');
?>
<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1,viewport-fit=cover">
<meta name="theme-color" content="#050817">
<link rel="icon" type="image/webp" href="assets/icons/beyond-os-192.webp">
<title>About Us | Beyond Imagination</title>
<meta name="description" content="From Ottawa to Nanaimo and beyond, discover Beyond Imagination's forever-evolving headquarters expansion plan.">
<style>
:root{--bg:#030611;--panel:#09101f;--line:rgba(255,255,255,.13);--text:#f7f8ff;--muted:#b8bed2;--pink:#f2469d;--violet:#7057ff;--green:#51db78;--gold:#ffbf32;--blue:#448cff}
*{box-sizing:border-box}html{background:var(--bg)}body{margin:0;color:var(--text);font-family:Inter,ui-sans-serif,system-ui,-apple-system,BlinkMacSystemFont,"Segoe UI",sans-serif;background:radial-gradient(circle at 78% 10%,rgba(73,54,204,.26),transparent 30%),linear-gradient(180deg,#050817,#02040d 72%);min-height:100vh}a{color:inherit}.wrap{width:min(1080px,calc(100% - 32px));margin-inline:auto}.top{min-height:84px;display:flex;align-items:center;justify-content:space-between;gap:20px}.brand{text-decoration:none;font-weight:1000;font-size:23px;letter-spacing:-.045em}.brand span{background:linear-gradient(100deg,#7667ff,#ec4caa);background-clip:text;color:transparent}.brand small{display:block;margin-top:5px;font-size:9px;letter-spacing:.13em;color:#c7c9d5;font-weight:700}.back{display:inline-flex;align-items:center;min-height:44px;padding:0 18px;border:1px solid rgba(255,255,255,.2);border-radius:10px;text-decoration:none;font-size:13px;font-weight:800;background:rgba(255,255,255,.035)}.hero{padding:92px 0 72px;max-width:900px}.eyebrow{display:inline-flex;align-items:center;gap:9px;padding:9px 13px;border:1px solid rgba(81,219,120,.35);border-radius:999px;color:#8debab;background:rgba(81,219,120,.08);font-size:12px;font-weight:850;letter-spacing:.08em}.hero h1{font-size:clamp(54px,9vw,104px);line-height:.88;letter-spacing:-.07em;margin:25px 0}.hero h1 span{display:block;background:linear-gradient(100deg,#7667ff,#ec4caa);background-clip:text;color:transparent}.lead{max-width:720px;margin:0;color:#d2d7e7;font-size:clamp(19px,3vw,27px);line-height:1.45;font-weight:650}.story{display:grid;grid-template-columns:1fr 1fr;gap:18px;padding-bottom:74px}.card{border:1px solid var(--line);border-radius:24px;padding:32px;background:linear-gradient(145deg,rgba(255,255,255,.075),rgba(255,255,255,.025));box-shadow:0 18px 55px rgba(0,0,0,.22)}.card:first-child{grid-column:1/-1;background:radial-gradient(circle at 92% 15%,rgba(255,58,84,.18),transparent 28%),linear-gradient(145deg,rgba(255,255,255,.075),rgba(255,255,255,.025))}.card-icon{font-size:32px}.card h2{margin:18px 0 10px;font-size:clamp(25px,4vw,37px);letter-spacing:-.045em}.card p{margin:0;color:var(--muted);font-size:16px;line-height:1.7}.values{display:flex;gap:9px;flex-wrap:wrap;margin-top:23px}.values span{padding:9px 12px;border:1px solid rgba(255,255,255,.13);border-radius:999px;color:#dce0ed;font-size:12px;font-weight:750}.cta{margin-bottom:74px;padding:38px;border:1px solid rgba(112,87,255,.35);border-radius:25px;background:linear-gradient(110deg,rgba(86,67,204,.75),rgba(190,38,124,.65));display:flex;align-items:center;justify-content:space-between;gap:25px}.cta h2{margin:0 0 8px;font-size:28px}.cta p{margin:0;color:#e1daed}.button{display:inline-flex;align-items:center;justify-content:center;min-height:48px;padding:0 20px;border-radius:10px;background:#fff;color:#151426;text-decoration:none;font-size:13px;font-weight:900;white-space:nowrap}.footer{padding:30px 0 45px;border-top:1px solid rgba(255,255,255,.09);color:#8f96aa;font-size:12px;display:flex;justify-content:space-between;gap:20px}.footer a{text-decoration:none;color:#d7dbe8}@media(max-width:680px){.top{min-height:72px}.brand{font-size:19px}.brand small{display:none}.hero{padding:68px 0 52px}.story{grid-template-columns:1fr}.card:first-child{grid-column:auto}.card{padding:25px}.cta{padding:28px;align-items:flex-start;flex-direction:column}.button{width:100%}.footer{flex-direction:column}}
.expansion{padding:8px 0 74px}.section-label{color:#a99cff;font-size:12px;font-weight:900;letter-spacing:.12em}.expansion h2{max-width:750px;margin:14px 0 12px;font-size:clamp(34px,6vw,58px);line-height:1;letter-spacing:-.055em}.expansion-intro{max-width:720px;margin:0 0 28px;color:var(--muted);font-size:17px;line-height:1.65}.locations{display:grid;grid-template-columns:repeat(3,1fr);gap:14px}.location{position:relative;min-height:230px;padding:25px;border:1px solid var(--line);border-radius:20px;background:linear-gradient(150deg,rgba(68,140,255,.13),rgba(255,255,255,.025));display:flex;flex-direction:column;justify-content:flex-end}.location.active{border-color:rgba(81,219,120,.4);background:linear-gradient(150deg,rgba(81,219,120,.16),rgba(255,255,255,.025))}.status{position:absolute;top:20px;left:20px;padding:7px 9px;border-radius:999px;background:rgba(255,255,255,.08);font-size:10px;font-weight:900;letter-spacing:.08em}.active .status{color:#8debab;background:rgba(81,219,120,.1)}.location h3{margin:0 0 7px;font-size:25px;letter-spacing:-.04em}.location p{margin:0;color:var(--muted);line-height:1.5;font-size:14px}@media(max-width:760px){.locations{grid-template-columns:1fr}}
</style>
</head>
<body>
<header class="top wrap">
    <a class="brand" href="./"><img src="assets/icons/beyond-os-192.webp" alt="" style="width:34px;height:34px;border-radius:10px;vertical-align:middle;margin-right:9px">BEYOND <span>OS</span><small>THE CONNECTED IMAGINATION ECOSYSTEM</small></a>
    <a class="back" href="./">Back to Beyond OS</a>
</header>
<main class="wrap">
    <section class="hero">
        <span class="eyebrow">&#127809; ABOUT US</span>
        <h1>From Ottawa <span>to Nanaimo.</span></h1>
        <p class="lead">We’re forever evolving—growing from Canadian roots into a connected company built to reach people everywhere.</p>
    </section>
    <section class="story" aria-label="Our story">
        <article class="card">
            <span class="card-icon" aria-hidden="true">&#127464;&#127462;</span>
            <h2>Connected across Canada.</h2>
            <p>Our story stretches from Ottawa, Ontario, to Nanaimo, British Columbia. Those roots shape the way we build: open to possibility, grounded in community and always evolving. Beyond OS brings health, education and finance together around one secure identity.</p>
            <div class="values"><span>Forever evolving</span><span>Community first</span><span>Human creativity</span><span>Connected opportunity</span></div>
        </article>
        <article class="card">
            <span class="card-icon" aria-hidden="true">&#10024;</span>
            <h2>Why “Beyond”?</h2>
            <p>Because imagination is where every new future starts. We create tools that help people move beyond barriers, discover what they can do and turn ideas into real opportunities.</p>
        </article>
        <article class="card">
            <span class="card-icon" aria-hidden="true">&#8734;</span>
            <h2>Forever evolving</h2>
            <p>For us, evolution means never standing still. We listen, learn and expand—bringing new people, places and perspectives into a shared vision for what technology can become.</p>
        </article>
    </section>
    <section class="expansion" aria-labelledby="expansion-title">
        <span class="section-label">HQ EXPANSION PLAN</span>
        <h2 id="expansion-title">From one coast to the next.</h2>
        <p class="expansion-intro">Our headquarters plan connects creativity, technology and opportunity across key North American communities. Nanaimo anchors our next chapter, with future expansion planned for South Florida and Los Angeles.</p>
        <div class="locations">
            <article class="location active"><span class="status">CANADIAN HQ</span><h3>Nanaimo</h3><p>Our West Coast home and the foundation for the next stage of Beyond Imagination.</p></article>
            <article class="location"><span class="status">PLANNED EXPANSION</span><h3>Palm Beach &amp;<br>West Palm Beach</h3><p>A future South Florida presence connecting business, culture and new opportunity.</p></article>
            <article class="location"><span class="status">PLANNED EXPANSION</span><h3>Los Angeles</h3><p>A future creative and technology hub connecting Beyond with global media and ideas.</p></article>
        </div>
    </section>
    <section class="cta">
        <div><h2>Come imagine what is next.</h2><p>Explore the apps and ideas growing inside Beyond OS.</p></div>
        <a class="button" href="./">Explore the ecosystem &rarr;</a>
    </section>
</main>
<footer class="footer wrap"><span>&copy; 2026 Beyond Imagination Corp.</span><a href="./">Beyond OS</a></footer>
</body>
</html>
