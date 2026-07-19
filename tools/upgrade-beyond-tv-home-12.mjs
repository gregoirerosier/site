import { readFile, writeFile } from 'node:fs/promises';
const path=new URL('../beyond-tv/index.php',import.meta.url);let html=await readFile(path,'utf8');
html=html.replace('<title>Beyond TV | 8 Live Channels</title>','<title>Beyond TV | 12 Live Channels</title>')
  .replace('Explore eight Beyond TV channels','Explore twelve Beyond TV channels')
  .replace(/<section class="channel-picker btv-shell">[\s\S]*?<\/div><\/section>\s*<section class="guide-wrap/,`<section class="channel-picker btv-shell"><div class="section-head"><div><span class="eyebrow">CHOOSE A CHANNEL</span><h2>12 channels across the ecosystem</h2></div></div><div class="channel-cards">
<?php $featuredChannels=json_decode((string)@file_get_contents(__DIR__.'/data/featured-channels.json'),true)?:[]; foreach($featuredChannels as $featuredChannel): ?>
<a class="channel-card-new channel-featured-<?=intval($featuredChannel['number'])?>" href="/beyond-tv/channel.php?slug=<?=urlencode((string)$featuredChannel['slug'])?>"><span class="channel-num">CHANNEL <?=intval($featuredChannel['number'])?></span><span class="channel-live">● LIVE LIBRARY</span><div><h3><?=htmlspecialchars((string)$featuredChannel['icon'].' '.$featuredChannel['name'])?></h3><p><?=htmlspecialchars((string)$featuredChannel['description'])?></p></div></a>
<?php endforeach; ?>
</div></section>
<section class="guide-wrap`);
html=html.replace('</style></head>','.channel-featured-9{background:linear-gradient(135deg,#67295f,#c14c77)}.channel-featured-10{background:linear-gradient(135deg,#172d49,#47799b)}.channel-featured-11{background:linear-gradient(135deg,#171522,#563960)}.channel-featured-12{background:linear-gradient(135deg,#514019,#c08a35)}\n</style></head>');
await writeFile(path,html,'utf8');console.log('Upgraded Beyond TV home to a dynamic 12-channel grid.');
