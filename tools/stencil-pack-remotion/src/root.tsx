import React from 'react';
import {AbsoluteFill, Composition, Easing, Img, interpolate, staticFile, useCurrentFrame} from 'remotion';

type Props = {packImage: string};

const MotionPack: React.FC<Props> = ({packImage}) => {
  const frame = useCurrentFrame();
  return (
    <AbsoluteFill style={{backgroundColor:'#030106',overflow:'hidden'}}>
      <div style={{position:'absolute',inset:-160,background:'radial-gradient(circle at 50% 58%, #5a1479 0%, #13031d 45%, #020104 78%)'}} />
      {Array.from({length:18}).map((_,i)=>{
        const drift=interpolate(frame,[0,180],[0,120+(i%4)*28],{extrapolateRight:'clamp',easing:Easing.bezier(.16,1,.3,1)});
        return <div key={i} style={{position:'absolute',left:(i%2===0?-80:820)+(i%5)*30,top:80+(i*127)%1300,width:260+(i%4)*90,height:260+(i%4)*90,borderRadius:'50%',background:'radial-gradient(circle, rgba(185,80,255,.18), rgba(70,10,110,0) 70%)',filter:'blur(22px)',opacity:.75,translate:`${i%2===0?drift:-drift}px ${Math.sin((frame+i*8)/26)*16}px`}}/>;
      })}
      <Img src={staticFile(packImage)} style={{position:'absolute',width:940,height:'auto',left:70,top:40,filter:'drop-shadow(0 36px 44px rgba(138,40,220,.52))',scale:interpolate(frame,[0,24,180],[.94,1,1.018],{extrapolateRight:'clamp',easing:Easing.bezier(.16,1,.3,1)}),rotate:`${interpolate(frame,[0,180],[-.7,.7])}deg`}} />
      <div style={{position:'absolute',top:-100,left:interpolate(frame,[0,180],[-420,1160]),width:260,height:1700,rotate:'18deg',background:'linear-gradient(90deg, transparent, rgba(255,255,255,.16), transparent)',filter:'blur(10px)',mixBlendMode:'screen'}} />
    </AbsoluteFill>
  );
};

export const RemotionRoot: React.FC = () => (
  <Composition
    id="BeyondStencilPack"
    component={MotionPack}
    durationInFrames={180}
    fps={30}
    width={1080}
    height={1350}
    defaultProps={{packImage:'stencil-pack.png'}}
  />
);
