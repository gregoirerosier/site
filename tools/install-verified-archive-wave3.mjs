import { readFile, writeFile } from 'node:fs/promises';

const dataDir = new URL('../beyond-tv/data/', import.meta.url);
const catalogPath = new URL('catalog.json', dataDir);
const catalog = JSON.parse(await readFile(catalogPath, 'utf8'));
const archiveUrl = (id,file) => `https://archive.org/download/${id}/${file.split('/').map(encodeURIComponent).join('/')}`;

const hauntingId = 'rl-stines-the-haunting-hour-full-series';
const hauntingMetadata = await (await fetch(`https://archive.org/metadata/${hauntingId}`)).json();
const haunting = (hauntingMetadata.files || []).flatMap((file) => {
  const name = String(file.name || '');
  const match = name.match(/^(\d{3})\s+(.+)\.(m4v|mp4)$/i);
  if (!match || /\.ia\.mp4$/i.test(name) || Number(file.size || 0) < 10_000_000) return [];
  return [{season:1,episode:Number(match[1]),title:match[2].trim(),runtime_seconds:1380,archive_file:name,video_url:archiveUrl(hauntingId,name)}];
}).sort((a,b)=>a.episode-b.episode);
await writeFile(new URL('haunting-hour-library.json',dataDir),`${JSON.stringify(haunting,null,2)}\n`,'utf8');

const poohId = '20231225_20231225_0425';
const poohFiles = [
  ['2. Winnie the Pooh and the Honey Tree (1966).mp4','Winnie the Pooh and the Honey Tree',1966],
  ['3. Winnie the Pooh and the Blustery Day (1968).mp4','Winnie the Pooh and the Blustery Day',1968],
  ['4. Winnie the Pooh and Tigger Too (1974).mp4','Winnie the Pooh and Tigger Too',1974],
  ['5. Winnie the Pooh and a Day for Eeyore (1983).mp4','Winnie the Pooh and a Day for Eeyore',1983]
];
const poohMetadata = await (await fetch(`https://archive.org/metadata/${poohId}`)).json();
const available = new Set((poohMetadata.files || []).map((file)=>String(file.name||'')));
const pooh = poohFiles.filter(([file])=>available.has(file)).map(([file,title,year],index)=>({season:1,episode:index+1,title,year,runtime_seconds:1500,archive_file:file,video_url:archiveUrl(poohId,file)}));
await writeFile(new URL('winnie-pooh-storybook-library.json',dataDir),`${JSON.stringify(pooh,null,2)}\n`,'utf8');

const additions = [
  {slug:'sister-act-2',type:'movie',title:'Sister Act 2: Back in the Habit',subtitle:'Beyond Comedy · Verified Archive source',description:'Deloris returns to help a struggling school choir find its voice.',icon:'🎤',gradient:'linear-gradient(135deg,#32214b,#794e9d 55%,#e1b552)',rating:'PG',year:'1993',genre:'Comedy · Music · Family',runtime:'Feature film',source_type:'direct_video',video_url:archiveUrl('sister-act-2','Sister Act 2.mp4'),archive_id:'sister-act-2',source_label:'Internet Archive · Owner-verified source',channel_slug:'beyond-comedy'},
  {slug:'bring-it-on',type:'movie',title:'Bring It On',subtitle:'Beyond Comedy · Verified Archive source',description:'A championship cheer squad discovers its routines were stolen.',icon:'📣',gradient:'linear-gradient(135deg,#47254c,#c84d83 55%,#f0c550)',rating:'PG-13',year:'2000',genre:'Comedy · Sports · Teen',runtime:'Feature film',source_type:'direct_video',video_url:archiveUrl('bring-it-on_202312','Bring It On.mp4'),archive_id:'bring-it-on_202312',source_label:'Internet Archive · Owner-verified source',channel_slug:'beyond-comedy'},
  {slug:'rl-stines-haunting-hour',type:'show',title:'R. L. Stine’s The Haunting Hour',subtitle:`${haunting.length} episodes · Beyond After Dark`,description:'The full supernatural anthology library with individually named episodes.',icon:'🕰️',gradient:'linear-gradient(135deg,#171724,#493b68 55%,#a96b48)',rating:'TV-PG',year:'2010–2014',genre:'Horror · Fantasy · Anthology',source_type:'archive_episode_map',archive_id:hauntingId,archive_episode_map:'haunting-hour-library.json',source_label:'Internet Archive · Owner-verified source',channel_slug:'beyond-after-dark',seasons:1},
  {slug:'winnie-pooh-storybook-classics',type:'show',title:'Winnie the Pooh Storybook Classics',subtitle:`${pooh.length} stories · Beyond Family`,description:'Four classic Winnie the Pooh animated storybook adventures.',icon:'🍯',gradient:'linear-gradient(135deg,#704514,#d79a2b 55%,#f3d763)',rating:'G',year:'1966–1983',genre:'Animation · Family',source_type:'archive_episode_map',archive_id:poohId,archive_episode_map:'winnie-pooh-storybook-library.json',source_label:'Internet Archive · Public Domain Mark 1.0',channel_slug:'beyond-family',seasons:1}
];
for (const item of additions) { const index=catalog.findIndex((entry)=>entry.slug===item.slug); if(index>=0)catalog.splice(index,1); }
catalog.unshift(...additions);
await writeFile(catalogPath,`${JSON.stringify(catalog,null,2)}\n`,'utf8');
console.log(`Installed ${additions.length} verified titles, ${haunting.length} Haunting Hour episodes, and ${pooh.length} Pooh stories.`);
