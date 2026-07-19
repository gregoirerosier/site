import { readFile, writeFile } from 'node:fs/promises';
const dataDir=new URL('../beyond-tv/data/',import.meta.url);
const channelsPath=new URL('channels.json',dataDir);const channels=JSON.parse(await readFile(channelsPath,'utf8'));
const featured=[
 ['classic-cartoon-theater','Classic Cartoon Theater','🎞️','Vintage animation and weekend cartoon blocks.','Cartoons'],
 ['beyond-cartoons','Beyond Cartoons','📺','Anime, action cartoons, and animated comedy.','Cartoons'],
 ['bubble-guppies','Preschool TV','🐾','Bluey and curated Nick Jr. libraries.','Preschool'],
 ['space-tv','Beyond Space','🛰️','Space science, NASA, and cosmic ambience.','Science'],
 ['beyond-ancient','Beyond Ancient','🏺','Archaeology, civilizations, and ancient history.','History'],
 ['classic-cinema','Beyond Movies','🎬','Features, double bills, and weekend marathons.','Movies'],
 ['beyond-french','Beyond French','🇫🇷','Lessons, conversation, and French culture.','Education'],
 ['beyond-health','Beyond Health','💚','Fitness, nutrition, meditation, and wellness.','Wellness'],
 ['beyond-comedy','Beyond Comedy','😂','Sitcoms, teen comedy, and feel-good movies.','Comedy'],
 ['beyond-mystery','Beyond Mystery','🔎','Detectives, puzzles, thrillers, and mysteries.','Mystery'],
 ['beyond-after-dark','Beyond After Dark','🌙','Goosebumps, Haunting Hour, and supernatural anthologies.','Horror'],
 ['beyond-family','Beyond Family','✨','Fantasy, storybook classics, and family adventures.','Family']
].map(([slug,name,icon,description,category],index)=>({number:index+1,slug,name,icon,description,category}));
const newChannels={
 'beyond-comedy':{now:'Sister Act 2',up_next:'Bring It On',gradient:'linear-gradient(135deg,#52245f,#d05683)',access:'guest'},
 'beyond-mystery':{now:'Mystery watchlist',up_next:'Glass Onion',gradient:'linear-gradient(135deg,#14243b,#456b8b)',access:'guest'},
 'beyond-after-dark':{now:'R. L. Stine’s The Haunting Hour',up_next:'Next supernatural story',gradient:'linear-gradient(135deg,#11131d,#50345c)',access:'guest'},
 'beyond-family':{now:'Winnie the Pooh Storybook Classics',up_next:'The Spiderwick Chronicles',gradient:'linear-gradient(135deg,#3f3518,#b38332)',access:'guest'}
};
for(const item of featured){let channel=channels.find(x=>x.slug===item.slug);if(!channel){channel={slug:item.slug};channels.push(channel)}Object.assign(channel,item,newChannels[item.slug]||{},{featured:true,release_status:'released',live:true,source_label:channel.source_label||'Curated Beyond TV library'});}
for(const channel of channels){if(!featured.some(x=>x.slug===channel.slug))channel.featured=false;}
const blocks=(titles)=>titles.map((title,index)=>({start:index*3,end:(index+1)*3,icon:'▶',title,lineup:title}));
const schedules={
 'classic-cartoon-theater':blocks(['Midnight Toons','Golden-Age Cartoons','Breakfast Cartoons','Hero Hour','Comedy Cartoons','Afternoon Adventures','Prime-Time Classics','Late-Night Animation']),
 'beyond-cartoons':blocks(['Anime Overnight','Zatch Bell','Yu-Gi-Oh!','Mr. Bean & Friends','Adventure Animation','Cartoon Mix','Anime Prime Time','Courage After Dark']),
 'bubble-guppies':blocks(['Sleepy-Time Preschool','Bluey Breakfast','Blue’s Clues','Allegra’s Window','Gullah Gullah Island','Preschool Playtime','Family Favorites','Bedtime Stories']),
 'space-tv':blocks(['Milky Way Overnight','The Sun in 4K','NASA Morning','Solar System','Deep Space','Astronomy Lab','Space Documentary','Cosmic Ambience']),
 'beyond-ancient':blocks(['Ancient Egypt After Dark','Dynasties of the Nile','Dawn of Civilization','Pyramid Engineering','Pharaohs & Queens','Tombs & Afterlife','Prime Documentary','Archaeology Night']),
 'classic-cinema':blocks(['Midnight Movie','Early Feature','Family Matinee','Comedy Feature','Adventure Feature','Double Bill','Movie Premiere','Late Feature']),
 'beyond-french':blocks(['French Listening','Vocabulary Loop','Bonjour Morning','French Foundations','Conversation Practice','Grammar Workshop','Français du Jour','Review & Quiz']),
 'beyond-health':blocks(['Sleep & Calm','Morning Mobility','Fitness Basics','Healthy Cooking','Mindful Break','Strength & Balance','Wellness Feature','Evening Meditation']),
 'beyond-comedy':blocks(['Sitcom Overnight','3rd Rock','Malcolm in the Middle','Workplace Comedy','Teen Comedy','Comedy Movies','Prime-Time Sitcoms','Late-Night Comedy']),
 'beyond-mystery':blocks(['Midnight Mystery','Classic Detectives','Family Mysteries','Puzzle Box','Mystery Matinee','Benoit Blanc Watchlist','Prime Mystery','Unsolved After Hours']),
 'beyond-after-dark':blocks(['Tales from the Darkside','Haunting Hour','Goosebumps','Eerie Adventures','Supernatural Stories','Are You Afraid?','After Dark Premiere','Midnight Anthology']),
 'beyond-family':blocks(['Storybook Overnight','Winnie the Pooh','Peter Rabbit','Family Fantasy','Narnia Adventures','Zathura & Spiderwick','Family Movie Night','Bedtime Classics'])
};
await writeFile(channelsPath,`${JSON.stringify(channels,null,2)}\n`,'utf8');
await writeFile(new URL('featured-channels.json',dataDir),`${JSON.stringify(featured,null,2)}\n`,'utf8');
await writeFile(new URL('channel-schedules.json',dataDir),`${JSON.stringify(schedules,null,2)}\n`,'utf8');
console.log('Installed 12 featured channels and 96 three-hour schedule blocks.');
