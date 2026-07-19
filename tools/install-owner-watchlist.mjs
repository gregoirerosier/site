import { readFile, writeFile } from 'node:fs/promises';

const catalogPath = new URL('../beyond-tv/data/catalog.json', import.meta.url);
const catalog = JSON.parse(await readFile(catalogPath, 'utf8'));
const watchlist = [
  {slug:'the-spiderwick-chronicles',type:'movie',title:'The Spiderwick Chronicles',subtitle:'Owner watchlist · Source review required',description:'The Grace family discovers a hidden world of magical creatures after moving into the Spiderwick Estate.',icon:'🧚',gradient:'linear-gradient(135deg,#172419,#31583c 55%,#b28b4b)',rating:'PG',year:'2008',genre:'Fantasy · Family · Adventure',runtime:'Feature film',candidate_url:'https://archive.org/details/the.-spiderwick.-chronicles.-2008.-multi-5-0-w-3-n_202607'},
  {slug:'2-broke-girls',type:'show',title:'2 Broke Girls',subtitle:'Owner watchlist · Source review required',description:'Two waitresses build an unlikely friendship while trying to launch a cupcake business.',icon:'🧁',gradient:'linear-gradient(135deg,#37214b,#a23679 55%,#efb04b)',rating:'TV-14',year:'2011–2017',genre:'Comedy · Sitcom',candidate_url:'https://archive.org/details/2-broke-girls-complete-series'},
  {slug:'wonderful-world-brothers-grimm',type:'movie',title:'The Wonderful World of the Brothers Grimm',subtitle:'Owner pick · Source review required',description:'A fantasy adventure inspired by the lives and stories of the Brothers Grimm.',icon:'📖',gradient:'linear-gradient(135deg,#23314b,#6a477b 55%,#d6ae57)',rating:'G',year:'1962',genre:'Fantasy · Family · Musical',runtime:'Feature film',candidate_url:'https://archive.org/details/the-wonderful-world-of-the-brothers-grimm-1962'},
  {slug:'glass-onion-knives-out-mystery',type:'movie',title:'Glass Onion: A Knives Out Mystery',subtitle:'Owner watchlist · Source review required',description:'Detective Benoit Blanc investigates a new mystery surrounding a technology billionaire and his friends.',icon:'🔎',gradient:'linear-gradient(135deg,#182238,#3f668b 55%,#d6a64c)',rating:'PG-13',year:'2022',genre:'Mystery · Comedy · Crime',runtime:'Feature film',candidate_url:'https://archive.org/details/glass-onion-a-knives-out-mystery-2022_202607'}
].map((item)=>({...item,source_type:'watchlist',source_label:'Interested · Playback source not yet approved',source_bookmark:'kareneliot'}));

for (const item of watchlist) {
  const index = catalog.findIndex((entry) => entry.slug === item.slug);
  if (index >= 0) catalog.splice(index, 1);
}
catalog.unshift(...watchlist);
await writeFile(catalogPath, `${JSON.stringify(catalog, null, 2)}\n`, 'utf8');
console.log(`Installed ${watchlist.length} owner-watchlist titles.`);
