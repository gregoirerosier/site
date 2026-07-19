import { readFile, writeFile } from 'node:fs/promises';

const catalogPath = new URL('../beyond-tv/data/catalog.json', import.meta.url);
const catalog = JSON.parse(await readFile(catalogPath, 'utf8'));
const removedSlugs = new Set(['woman-on-the-run-1950', 'the-buccaneers-blackbeard']);
const updated = catalog.filter((entry) => !removedSlugs.has(String(entry.slug || '')));

await writeFile(catalogPath, `${JSON.stringify(updated, null, 2)}\n`, 'utf8');
console.log(`Removed ${catalog.length - updated.length} old catalogue entries.`);
