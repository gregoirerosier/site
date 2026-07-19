import { writeFile } from 'node:fs/promises';

const archiveId = 'bluey-iso-archive';
const folder = 'Bluey DVD ISO Archive';
const output = new URL('../beyond-tv/data/bluey-library.json', import.meta.url);

const response = await fetch('https://api.tvmaze.com/singlesearch/shows?q=Bluey&embed=episodes');
if (!response.ok) throw new Error(`TVMaze returned ${response.status}`);
const show = await response.json();
const episodes = show._embedded.episodes.filter((episode) => episode.season >= 1 && episode.season <= 3);

// TVMaze currently omits The Sign from its standard numbered list.
const season3 = episodes.filter((episode) => episode.season === 3);
const ghostbasket = season3.find((episode) => episode.name === 'Ghostbasket');
const surprise = season3.find((episode) => episode.name === 'Surprise!');
if (ghostbasket && surprise) {
  surprise.number = 50;
  episodes.push({season: 3, number: 49, name: 'The Sign', airdate: '2024-04-14', runtime: 28});
}

function archiveFile(season, episode) {
  const half = episode <= 26 ? 'First Half' : 'Second Half';
  const discPosition = episode <= 26 ? episode : episode - 26;
  // Individual episode tracks start at DVD track 4; tracks 1–3 are menus/artifacts.
  const track = discPosition + 3;
  return `${folder}/Bluey - Season ${season} - ${half}${track}.mp4`;
}

const library = episodes
  .sort((a, b) => a.season - b.season || a.number - b.number)
  .map((episode) => {
    const file = archiveFile(episode.season, episode.number);
    return {
      season: episode.season,
      episode: episode.number,
      title: episode.name,
      air_date: episode.airdate || '',
      runtime_seconds: Math.max(420, Number(episode.runtime || 7) * 60),
      archive_file: file,
      video_url: `https://archive.org/download/${archiveId}/${file.split('/').map(encodeURIComponent).join('/')}`,
    };
  });

await writeFile(output, `${JSON.stringify(library, null, 2)}\n`, 'utf8');
console.log(`Wrote ${library.length} Bluey episodes to ${output.pathname}`);
