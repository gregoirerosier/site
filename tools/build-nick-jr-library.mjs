import { writeFile } from 'node:fs/promises';

const archiveId = 'img-4382';
const response = await fetch(`https://archive.org/metadata/${archiveId}`);
if (!response.ok) throw new Error(`Internet Archive returned ${response.status}`);
const metadata = await response.json();
const names = metadata.files
  .map((file) => String(file.name || ''))
  .filter((name) => name.endsWith('.mp4') && !name.endsWith('.ia.mp4'));

const urlFor = (file) => `https://archive.org/download/${archiveId}/${encodeURIComponent(file)}`;
const cleanTitle = (title) => title.replace(/\s+/g, ' ').replace(/\s+-\s*$/, '').trim();

const bluesRaw = names.flatMap((file) => {
  const match = file.match(/^Blue's Clues S(\d{2})E(\d{2,3})\s+(.+)\.mp4$/);
  return match ? [{sourceSeason: Number(match[1]), sourceEpisode: Number(match[2]), title: cleanTitle(match[3]), file}] : [];
}).sort((a, b) => a.sourceSeason - b.sourceSeason || a.sourceEpisode - b.sourceEpisode);
const seasonCounters = new Map();
const blues = bluesRaw.map((item) => {
  const episode = (seasonCounters.get(item.sourceSeason) || 0) + 1;
  seasonCounters.set(item.sourceSeason, episode);
  return {season: item.sourceSeason, episode, title: item.title, runtime_seconds: 1440, archive_file: item.file, video_url: urlFor(item.file)};
});

const allegra = names.flatMap((file) => {
  const match = file.match(/^Allegra's Window - (\d+)x(\d+) - (.+)\.mp4$/);
  return match ? [{season: Number(match[1]), episode: Number(match[2]), title: cleanTitle(match[3]), runtime_seconds: 1440, archive_file: file, video_url: urlFor(file)}] : [];
}).sort((a, b) => a.season - b.season || a.episode - b.episode);

const gullah = names.flatMap((file) => {
  const match = file.match(/^Gullah Gullah Island - (.+)\.mp4$/);
  return match ? [{title: cleanTitle(match[1].replace(/\s*\(Noggin Airing\)$/i, '')), file}] : [];
}).map((item, index) => ({season: 1, episode: index + 1, title: item.title, runtime_seconds: 1440, archive_file: item.file, video_url: urlFor(item.file)}));

for (const [file, library] of [['blues-clues-library.json', blues], ['allegras-window-library.json', allegra], ['gullah-gullah-library.json', gullah]]) {
  await writeFile(new URL(`../beyond-tv/data/${file}`, import.meta.url), `${JSON.stringify(library, null, 2)}\n`, 'utf8');
  console.log(`Wrote ${library.length} items to ${file}`);
}
