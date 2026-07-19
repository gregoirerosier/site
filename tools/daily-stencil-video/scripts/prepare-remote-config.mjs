import fs from 'node:fs/promises';
import path from 'node:path';

const configUrl = process.env.DAILY_STENCIL_CONFIG_URL || process.argv[2] || '';
const outPath = path.resolve('public/daily-stencil.render.json');
const assetDir = path.resolve('public/queued');
await fs.mkdir(assetDir, {recursive: true});

const isRemote = (value) => /^https?:\/\//i.test(String(value || ''));
const safeExt = (url, fallback) => {
  try {
    const ext = path.extname(new URL(url).pathname).toLowerCase();
    return ext && ext.length <= 8 ? ext : fallback;
  } catch { return fallback; }
};

async function download(url, filename) {
  const response = await fetch(url);
  if (!response.ok) throw new Error(`Download failed (${response.status}): ${url}`);
  const bytes = new Uint8Array(await response.arrayBuffer());
  await fs.writeFile(path.join(assetDir, filename), bytes);
  return `queued/${filename}`;
}

let config;
if (configUrl) {
  const response = await fetch(configUrl, {headers: {'User-Agent': 'Beyond-Tattoo-Renderer/1.0'}});
  if (!response.ok) throw new Error(`Config request failed (${response.status})`);
  config = await response.json();
} else {
  config = JSON.parse(await fs.readFile(path.resolve('public/daily-stencil.json'), 'utf8'));
}

if (isRemote(config.mainArtwork)) {
  config.mainArtwork = await download(config.mainArtwork, `main-artwork${safeExt(config.mainArtwork, '.png')}`);
}
if (isRemote(config.studioTransfer)) {
  config.studioTransfer = await download(config.studioTransfer, `studio-transfer${safeExt(config.studioTransfer, '.png')}`);
}
if (isRemote(config.audioFile)) {
  config.audioFile = await download(config.audioFile, `audio${safeExt(config.audioFile, '.mp3')}`);
}

await fs.writeFile(outPath, `${JSON.stringify(config, null, 2)}\n`);
console.log(`Prepared ${outPath}`);
