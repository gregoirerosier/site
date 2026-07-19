import { readFile, writeFile } from 'node:fs/promises';

const root = new URL('../beyond-tv/data/', import.meta.url);
const catalogPath = new URL('catalog.json', root);
const channelsPath = new URL('channels.json', root);
const catalog = JSON.parse(await readFile(catalogPath, 'utf8'));
const channels = JSON.parse(await readFile(channelsPath, 'utf8'));

const bluey = {
  slug: 'bluey', type: 'show', title: 'Bluey', subtitle: '154 episodes · Preschool TV',
  description: 'Browse all available episodes across three seasons with corrected episode titles, streamed from the Internet Archive collection.',
  icon: '🐾', gradient: 'linear-gradient(135deg,#0a4c9a,#28a9de 54%,#f6c94d)', rating: 'TV-Y',
  year: '2018–2024', genre: 'Kids · Family · Animation', source_type: 'archive_episode_map',
  archive_id: 'bluey-iso-archive', archive_episode_map: 'bluey-library.json',
  source_label: 'Internet Archive item bluey-iso-archive', channel_slug: 'bubble-guppies',
  thumbnail: 'https://archive.org/services/img/bluey-iso-archive', seasons: 3,
  network: 'Ludo Studio', episode_catalog: 'archive_episode_map'
};
const nickJrTitles = [
  bluey,
  {slug:"blues-clues",type:"show",title:"Blue's Clues",subtitle:"141 episodes · Preschool TV",description:"Six seasons of clearly named Blue's Clues episodes from the Nick Jr. archive.",icon:"🐶",gradient:"linear-gradient(135deg,#1769d2,#42b9ff 58%,#77db75)",rating:"TV-Y",year:"1996–2006",genre:"Kids · Educational",source_type:"archive_episode_map",archive_id:"img-4382",archive_episode_map:"blues-clues-library.json",source_label:"Internet Archive item img-4382",channel_slug:"bubble-guppies",thumbnail:"https://archive.org/services/img/img-4382",seasons:6,network:"Nick Jr.",episode_catalog:"archive_episode_map"},
  {slug:"allegras-window",type:"show",title:"Allegra's Window",subtitle:"50 episodes · Preschool TV",description:"Three seasons with cleaned season, episode, and title labels from the Nick Jr. archive.",icon:"🪟",gradient:"linear-gradient(135deg,#6b42b8,#e664a4 58%,#f4c95d)",rating:"TV-Y",year:"1994–1996",genre:"Kids · Musical · Educational",source_type:"archive_episode_map",archive_id:"img-4382",archive_episode_map:"allegras-window-library.json",source_label:"Internet Archive item img-4382",channel_slug:"bubble-guppies",thumbnail:"https://archive.org/services/img/img-4382",seasons:3,network:"Nick Jr.",episode_catalog:"archive_episode_map"},
  {slug:"gullah-gullah-island",type:"show",title:"Gullah Gullah Island",subtitle:"Curated episodes · Preschool TV",description:"Clearly labelled Gullah Gullah Island episodes from the Nick Jr. archive.",icon:"🏝️",gradient:"linear-gradient(135deg,#087f8c,#5ecb8b 58%,#ffd166)",rating:"TV-Y",year:"1994–1998",genre:"Kids · Musical · Educational",source_type:"archive_episode_map",archive_id:"img-4382",archive_episode_map:"gullah-gullah-library.json",source_label:"Internet Archive item img-4382",channel_slug:"bubble-guppies",thumbnail:"https://archive.org/services/img/img-4382",seasons:1,network:"Nick Jr.",episode_catalog:"archive_episode_map"}
];
for (const title of nickJrTitles) {
  const oldCatalogIndex = catalog.findIndex((item) => item.slug === title.slug);
  if (oldCatalogIndex >= 0) catalog.splice(oldCatalogIndex, 1);
}
catalog.unshift(...nickJrTitles);

const channel = channels.find((item) => item.slug === 'bubble-guppies');
if (!channel) throw new Error('Preschool TV channel was not found.');
Object.assign(channel, {
  now: 'Bluey & Nick Jr. live library', up_next: 'Next curated preschool episode',
  description: 'A continuous live-library rotation of Bluey, Blue’s Clues, Allegra’s Window, and Gullah Gullah Island with clean show and episode titles.',
  source_type: 'archive_library_live', stream_endpoint: '/beyond-tv/api/bluey-live.php',
  official_url: 'https://archive.org/details/bluey-iso-archive/Bluey+DVD+ISO+Archive/',
  rights_status: 'archive_item', rights_label: 'Playback hosted by Internet Archive',
  thumbnail: 'https://archive.org/services/img/bluey-iso-archive',
  source_label: 'Internet Archive · Curated Bluey and Nick Jr. live library'
});
for (const key of ['youtube_id','youtube_start','youtube_autoplay','youtube_muted','youtube_title','youtube_live_status','youtube_playlist_id','youtube_playlist_video_id','youtube_playlist_title','official_channel']) delete channel[key];

await writeFile(catalogPath, `${JSON.stringify(catalog, null, 2)}\n`, 'utf8');
await writeFile(channelsPath, `${JSON.stringify(channels, null, 2)}\n`, 'utf8');
console.log('Installed Bluey catalogue and Preschool TV archive source.');
