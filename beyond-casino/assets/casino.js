(()=>{
  'use strict';
  let balance=1000;
  const money=value=>Number.isInteger(value)?value.toLocaleString():value.toLocaleString(undefined,{maximumFractionDigits:1});
  const balanceEls=[...document.querySelectorAll('[data-balance]')];
  const setBalance=value=>{balance=value;balanceEls.forEach(el=>el.textContent=money(balance));};
  const randomInt=max=>{if(max<1)return 0;const limit=0x100000000-(0x100000000%max);const values=new Uint32Array(1);do{crypto.getRandomValues(values)}while(values[0]>=limit);return values[0]%max};
  const shuffle=items=>{for(let i=items.length-1;i>0;i--){const j=randomInt(i+1);[items[i],items[j]]=[items[j],items[i]]}return items};

  document.querySelectorAll('[data-game-tab]').forEach(tab=>tab.addEventListener('click',()=>{
    const game=tab.dataset.gameTab;
    document.querySelectorAll('[data-game-tab]').forEach(item=>{const active=item===tab;item.classList.toggle('is-active',active);item.setAttribute('aria-selected',String(active))});
    document.querySelectorAll('[data-game-panel]').forEach(panel=>{const active=panel.dataset.gamePanel===game;panel.classList.toggle('is-active',active);panel.hidden=!active});
  }));

  const symbols=['🍒','⭐','7','♦️','🍋'];
  const reels=['r1','r2','r3'].map(id=>document.getElementById(id));
  const slotsStatus=document.getElementById('slots-status');
  const slotsSpin=document.getElementById('slots-spin');
  const drawSymbol=()=>symbols[randomInt(symbols.length)];
  slotsSpin.addEventListener('click',()=>{
    if(balance<10){slotsStatus.textContent='Not enough demo bit$. Reload to reset the lounge.';return}
    setBalance(balance-10);slotsSpin.disabled=true;slotsStatus.textContent='Spinning…';let ticks=0;
    const timer=setInterval(()=>{reels.forEach(reel=>reel.textContent=drawSymbol());if(++ticks<10)return;clearInterval(timer);const result=reels.map(reel=>reel.textContent);let prize=0;if(result.every(value=>value==='7'))prize=250;else if(result.every(value=>value==='⭐'))prize=100;else if(result.every(value=>value===result[0]))prize=50;setBalance(balance+prize);slotsStatus.textContent=prize?`You won ${prize} demo bit$!`:'No match — try another social spin.';slotsSpin.disabled=false},70);
  });

  const redNumbers=new Set([1,3,5,7,9,12,14,16,18,19,21,23,25,27,30,32,34,36]);
  const rouletteResult=document.getElementById('roulette-result');
  const rouletteStatus=document.getElementById('roulette-status');
  const rouletteSpin=document.getElementById('roulette-spin');
  const rouletteBet=document.getElementById('roulette-bet');
  const rouletteColor=number=>number===0?'green':redNumbers.has(number)?'red':'black';
  const winsRoulette=(bet,number)=>number!==0&&({red:rouletteColor(number)==='red',black:rouletteColor(number)==='black',odd:number%2===1,even:number%2===0,low:number<=18,high:number>=19}[bet]||false);
  rouletteSpin.addEventListener('click',()=>{
    if(balance<10){rouletteStatus.textContent='Not enough demo bit$. Reload to reset the lounge.';return}
    setBalance(balance-10);rouletteSpin.disabled=true;rouletteBet.disabled=true;rouletteStatus.textContent='The wheel is spinning…';let ticks=0;
    const timer=setInterval(()=>{const number=randomInt(37);rouletteResult.textContent=String(number);rouletteResult.className=`roulette-result is-${rouletteColor(number)}`;if(++ticks<18)return;clearInterval(timer);const finalNumber=Number(rouletteResult.textContent);const won=winsRoulette(rouletteBet.value,finalNumber);if(won)setBalance(balance+20);rouletteStatus.textContent=`${finalNumber} ${rouletteColor(finalNumber)} — ${won?'you won 20 demo bit$!':'the table wins this spin.'}`;rouletteSpin.disabled=false;rouletteBet.disabled=false},75);
  });

  const suits=[{key:'s',symbol:'♠',red:false},{key:'h',symbol:'♥',red:true},{key:'d',symbol:'♦',red:true},{key:'c',symbol:'♣',red:false}];
  const rankLabel=rank=>({11:'J',12:'Q',13:'K',14:'A'}[rank]||String(rank));
  const freshDeck=()=>shuffle(suits.flatMap(suit=>Array.from({length:13},(_,index)=>({rank:index+2,suit:suit.key,symbol:suit.symbol,red:suit.red}))));
  const cardMarkup=(card,hidden=false)=>hidden?'<span class="playing-card back" aria-label="Hidden card">B</span>':`<span class="playing-card${card.red?' red':''}" aria-label="${rankLabel(card.rank)} of ${card.symbol}"><b>${rankLabel(card.rank)}</b><small>${card.symbol}</small></span>`;

  let blackjackDeck=[],playerHand=[],dealerHand=[],blackjackActive=false;
  const dealerCards=document.getElementById('dealer-cards'),playerCards=document.getElementById('player-cards'),dealerScore=document.getElementById('dealer-score'),playerScore=document.getElementById('player-score');
  const blackjackStatus=document.getElementById('blackjack-status'),blackjackDeal=document.getElementById('blackjack-deal'),blackjackHit=document.getElementById('blackjack-hit'),blackjackStand=document.getElementById('blackjack-stand');
  const blackjackValue=hand=>{let total=hand.reduce((sum,card)=>sum+(card.rank===14?1:card.rank>10?10:card.rank),0),aces=hand.filter(card=>card.rank===14).length;while(aces&&total+10<=21){total+=10;aces--}return total};
  const renderBlackjack=(reveal=false)=>{playerCards.innerHTML=playerHand.map(card=>cardMarkup(card)).join('');dealerCards.innerHTML=dealerHand.map((card,index)=>cardMarkup(card,!reveal&&index===1)).join('');playerScore.textContent=String(blackjackValue(playerHand));dealerScore.textContent=reveal?String(blackjackValue(dealerHand)):'?'};
  const finishBlackjack=(message,payout=0)=>{blackjackActive=false;if(payout)setBalance(balance+payout);renderBlackjack(true);blackjackStatus.textContent=message;blackjackHit.disabled=true;blackjackStand.disabled=true;blackjackDeal.disabled=false;blackjackDeal.textContent='Deal another 20 demo bit$'};
  const dealerPlay=()=>{while(blackjackValue(dealerHand)<17)dealerHand.push(blackjackDeck.pop());const player=blackjackValue(playerHand),dealer=blackjackValue(dealerHand);if(dealer>21||player>dealer)finishBlackjack(`You win ${dealer>21?'— dealer busts.':'with '+player+' against '+dealer+'.'} +40 demo bit$.`,40);else if(player===dealer)finishBlackjack(`Push at ${player}. Your 20 demo bit$ wager is returned.`,20);else finishBlackjack(`Dealer wins ${dealer} to ${player}.`)};
  blackjackDeal.addEventListener('click',()=>{if(balance<20){blackjackStatus.textContent='Not enough demo bit$. Reload to reset the lounge.';return}setBalance(balance-20);blackjackDeck=freshDeck();playerHand=[blackjackDeck.pop(),blackjackDeck.pop()];dealerHand=[blackjackDeck.pop(),blackjackDeck.pop()];blackjackActive=true;blackjackDeal.disabled=true;blackjackHit.disabled=false;blackjackStand.disabled=false;renderBlackjack(false);const player=blackjackValue(playerHand),dealer=blackjackValue(dealerHand);if(player===21&&dealer===21)finishBlackjack('Both hands have blackjack. Your wager is returned.',20);else if(player===21)finishBlackjack('Natural blackjack! You receive 50 demo bit$.',50);else if(dealer===21)finishBlackjack('Dealer has blackjack.');else blackjackStatus.textContent='Hit or stand.'});
  blackjackHit.addEventListener('click',()=>{if(!blackjackActive)return;playerHand.push(blackjackDeck.pop());renderBlackjack(false);const total=blackjackValue(playerHand);if(total>21)finishBlackjack(`Bust at ${total}.`);else if(total===21)dealerPlay();else blackjackStatus.textContent=`You have ${total}. Hit or stand.`});
  blackjackStand.addEventListener('click',()=>{if(blackjackActive)dealerPlay()});

  const categoryNames=['High card','One pair','Two pair','Three of a kind','Straight','Flush','Full house','Four of a kind','Straight flush'];
  const compareScores=(a,b)=>{for(let i=0;i<Math.max(a.length,b.length);i++){const diff=(a[i]||0)-(b[i]||0);if(diff)return diff}return 0};
  const scoreFive=cards=>{const ranks=cards.map(card=>card.rank).sort((a,b)=>b-a);const groups=[...new Set(ranks)].map(rank=>({rank,count:ranks.filter(value=>value===rank).length})).sort((a,b)=>b.count-a.count||b.rank-a.rank);const flush=cards.every(card=>card.suit===cards[0].suit);let unique=[...new Set(ranks)];if(unique[0]===14)unique=[...unique,1];let straight=0;for(let i=0;i<=unique.length-5;i++)if(unique[i]-unique[i+4]===4){straight=unique[i];break}if(flush&&straight)return[8,straight];if(groups[0].count===4)return[7,groups[0].rank,groups[1].rank];if(groups[0].count===3&&groups[1].count===2)return[6,groups[0].rank,groups[1].rank];if(flush)return[5,...ranks];if(straight)return[4,straight];if(groups[0].count===3)return[3,groups[0].rank,...groups.slice(1).map(group=>group.rank).sort((a,b)=>b-a)];if(groups[0].count===2&&groups[1].count===2){const pairs=[groups[0].rank,groups[1].rank].sort((a,b)=>b-a);return[2,...pairs,groups[2].rank]}if(groups[0].count===2)return[1,groups[0].rank,...groups.slice(1).map(group=>group.rank).sort((a,b)=>b-a)];return[0,...ranks]};
  const bestSeven=cards=>{let best=null;for(let a=0;a<cards.length-4;a++)for(let b=a+1;b<cards.length-3;b++)for(let c=b+1;c<cards.length-2;c++)for(let d=c+1;d<cards.length-1;d++)for(let e=d+1;e<cards.length;e++){const score=scoreFive([cards[a],cards[b],cards[c],cards[d],cards[e]]);if(!best||compareScores(score,best)>0)best=score}return best};
  let holdemDeck=[],holdemPlayer=[],holdemHouse=[],community=[],holdemStage=0;
  const holdemHouseEl=document.getElementById('holdem-house'),holdemPlayerEl=document.getElementById('holdem-player'),communityEl=document.getElementById('holdem-community'),holdemHouseLabel=document.getElementById('holdem-house-label'),holdemPlayerLabel=document.getElementById('holdem-player-label'),holdemStatus=document.getElementById('holdem-status'),holdemAction=document.getElementById('holdem-action');
  const renderHoldem=(revealHouse=false)=>{holdemPlayerEl.innerHTML=holdemPlayer.map(card=>cardMarkup(card)).join('');holdemHouseEl.innerHTML=holdemHouse.map(card=>cardMarkup(card,!revealHouse)).join('');communityEl.innerHTML=community.map(card=>cardMarkup(card)).join('')||'<span class="empty-hand">Community cards are waiting.</span>'};
  const finishHoldem=()=>{const playerScore=bestSeven([...holdemPlayer,...community]),houseScore=bestSeven([...holdemHouse,...community]),comparison=compareScores(playerScore,houseScore);holdemPlayerLabel.textContent=categoryNames[playerScore[0]];holdemHouseLabel.textContent=categoryNames[houseScore[0]];renderHoldem(true);if(comparison>0){setBalance(balance+50);holdemStatus.textContent=`${categoryNames[playerScore[0]]} wins. You receive 50 demo bit$.`}else if(comparison===0){setBalance(balance+25);holdemStatus.textContent=`Both hands make ${categoryNames[playerScore[0]]}. Your 25 demo bit$ wager is returned.`}else holdemStatus.textContent=`House wins with ${categoryNames[houseScore[0]]}.`;holdemStage=0;holdemAction.textContent='Deal another 25 demo bit$'};
  holdemAction.addEventListener('click',()=>{if(holdemStage===0){if(balance<25){holdemStatus.textContent='Not enough demo bit$. Reload to reset the lounge.';return}setBalance(balance-25);holdemDeck=freshDeck();holdemPlayer=[holdemDeck.pop(),holdemDeck.pop()];holdemHouse=[holdemDeck.pop(),holdemDeck.pop()];community=[];holdemStage=1;holdemHouseLabel.textContent='Hidden';holdemPlayerLabel.textContent='In play';holdemStatus.textContent='Pocket cards dealt. Reveal the flop.';holdemAction.textContent='Reveal the flop';renderHoldem(false);return}if(holdemStage===1){community.push(holdemDeck.pop(),holdemDeck.pop(),holdemDeck.pop());holdemStage=2;holdemStatus.textContent='Flop revealed. Continue to the turn.';holdemAction.textContent='Reveal the turn';renderHoldem(false);return}if(holdemStage===2){community.push(holdemDeck.pop());holdemStage=3;holdemStatus.textContent='Turn revealed. Continue to the river.';holdemAction.textContent='Reveal the river';renderHoldem(false);return}community.push(holdemDeck.pop());finishHoldem()});
})();
