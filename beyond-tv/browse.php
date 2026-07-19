<?php
declare(strict_types=1);
require_once __DIR__ . '/require-member.php';

$catalog = json_decode((string) file_get_contents(__DIR__ . '/data/catalog.json'), true) ?: [];
$view = strtolower((string)($_GET['view'] ?? 'all'));
if (!in_array($view, ['all','shows','movies'], true)) { $view = 'all'; }
$filtered = array_values(array_filter($catalog, static function(array $item) use ($view): bool {
    return $view === 'all' || ($view === 'shows' && ($item['type'] ?? '') === 'show') || ($view === 'movies' && ($item['type'] ?? '') === 'movie');
}));
$heading = $view === 'shows' ? 'TV Shows' : ($view === 'movies' ? 'Free Movies' : 'Browse');
?>
<!doctype html><html lang="en"><head><script>(function(){try{const t=localStorage.getItem("beyond-tv-theme");document.documentElement.dataset.tvTheme=["dark","light","sunset"].includes(t)?t:"sunset"}catch(e){document.documentElement.dataset.tvTheme="sunset"}})();</script><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title><?=htmlspecialchars($heading)?> | Beyond TV</title><link rel="stylesheet" href="/beyond-tv/assets/css/app.css?v=2.2.0"></head><body class="tv-app"><?php include __DIR__.'/partials/header.php'; ?>
<main class="page shell catalog-page">
<span class="kicker">BEYOND TV LIBRARY</span><h1><?=htmlspecialchars($heading)?></h1><p class="lead">Browse every Season 1 collection and select episodes directly from a clean on-page episode list.</p>
<div class="library-search"><label for="library-filter">Search library</label><input id="library-filter" type="search" placeholder="Search Yu-Gi-Oh!, Mr. Bean, Courage…" autocomplete="off"></div><nav class="catalog-tabs" aria-label="Browse library"><a class="<?=$view==='all'?'is-active':''?>" href="/beyond-tv/browse.php">All</a><a class="<?=$view==='shows'?'is-active':''?>" href="/beyond-tv/browse.php?view=shows">TV Shows</a><a class="<?=$view==='movies'?'is-active':''?>" href="/beyond-tv/browse.php?view=movies">Free Movies</a><a href="/beyond-tv/live-tv.php">Live Guide</a></nav>
<div class="catalog-grid"><?php foreach($filtered as $item): ?>
<article class="catalog-card" data-library-card data-library-title="<?=htmlspecialchars(strtolower((string)$item['title']))?>"><a href="/beyond-tv/title.php?slug=<?=urlencode((string)$item['slug'])?>"><div class="catalog-art" style="background:<?=htmlspecialchars((string)$item['gradient'])?><?php if(!empty($item['thumbnail'])): ?> url(<?=htmlspecialchars((string)$item['thumbnail'])?>) center/cover no-repeat<?php endif; ?>"><span class="catalog-art-shade"></span><span class="catalog-icon"><?=htmlspecialchars((string)$item['icon'])?></span><span class="catalog-type"><?=($item['type']??'')==='movie'?'MOVIE':'SERIES'?></span><span class="catalog-play">▶</span></div><div class="catalog-copy"><small><?=htmlspecialchars((string)($item['subtitle']??''))?></small><h2><?=htmlspecialchars((string)$item['title'])?></h2><p><?=htmlspecialchars((string)($item['year']??''))?> · <?=htmlspecialchars((string)($item['rating']??'NR'))?><?php if(!empty($item['runtime'])):?> · <?=htmlspecialchars((string)$item['runtime'])?><?php endif;?></p><span><?=htmlspecialchars((string)($item['genre']??''))?></span></div></a></article>
<?php endforeach; ?></div>
<p class="library-empty" data-library-empty hidden>No matching titles found.</p></main><?php include __DIR__.'/partials/footer.php'; ?><script src="/beyond-tv/assets/js/app.js?v=2.2.0"></script><script>(function(){const input=document.getElementById("library-filter");if(!input)return;const cards=[...document.querySelectorAll("[data-library-card]")],empty=document.querySelector("[data-library-empty]");input.addEventListener("input",()=>{const q=input.value.trim().toLowerCase();let shown=0;cards.forEach(card=>{const match=!q||card.dataset.libraryTitle.includes(q)||card.textContent.toLowerCase().includes(q);card.hidden=!match;if(match)shown++;});if(empty)empty.hidden=shown!==0;});})();</script></body></html>
