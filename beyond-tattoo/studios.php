<?php
require __DIR__ . '/includes/config.php';
require_login();
$pageTitle='Studios — Beyond Tattoo';
require __DIR__ . '/includes/header.php';
?>
<div class="app-shell"><header class="app-header"><div class="container app-header-inner"><a class="brand" href="dashboard.php"><span class="brand-badge">B</span><span>Studios Near You</span></a></div></header>
<main class="container dashboard"><div class="panel"><input class="input" placeholder="Search city, postal code or studio"><div id="studio-results" class="plan" style="margin-top:14px"></div></div></main></div>
<script>
fetch('api/studios/nearby.php?lat=49.1659&lng=-123.9401&radius=25').then(r=>r.json()).then(data=>{
 document.getElementById('studio-results').innerHTML=data.studios.map(s=>`<div class="task"><span><strong>${s.name}</strong><br><small class="meta">⭐ ${s.rating} • ${s.distance_km} km • ${s.open_now?'Open':'Closed'}</small></span><a href="${s.website}" target="_blank">›</a></div>`).join('');
});
</script>
<?php require __DIR__ . '/includes/footer.php'; ?>