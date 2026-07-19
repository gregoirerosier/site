import { readFile, writeFile } from 'node:fs/promises';
const path=new URL('../beyond-tv/data/catalog.json',import.meta.url);const catalog=JSON.parse(await readFile(path,'utf8'));
let approved=0,pending=0;
for(const item of catalog){if(item.source_type!=='watchlist')continue;const match=String(item.candidate_url||'').match(/^https:\/\/archive\.org\/details\/([^/?#]+)/);if(!match){pending++;continue;}item.source_type='archive_embed';item.archive_id=decodeURIComponent(match[1]);item.source_label='Internet Archive · Owner-verified source';item.approved_by='owner';item.approved_at='2026-07-19';approved++;}
await writeFile(path,`${JSON.stringify(catalog,null,2)}\n`,'utf8');console.log(`Approved ${approved} Archive embeds; ${pending} item remains pending.`);
