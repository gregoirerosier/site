import React from 'react';
import {Audio} from '@remotion/media';
import {
  AbsoluteFill,
  Easing,
  Img,
  interpolate,
  staticFile,
  useCurrentFrame,
  useVideoConfig,
} from 'remotion';

export type SpaceHoroscopeProps = {
  sign: string;
  symbol: string;
  season: string;
  date: string;
  headline: string;
  paragraphs: string[];
  mood: string;
  backgroundImage: string;
  audioFile?: string;
  source?: string;
};

export const defaultSpaceHoroscopeProps: SpaceHoroscopeProps = {
  sign: 'Aries',
  symbol: '♈',
  season: 'Mar 21 - Apr 19',
  date: 'Wednesday, August 5, 2026',
  headline: 'COSMIC RESET',
  paragraphs: [
    'Lead with curiosity today. A bold question may open a surprising path.',
    'Protect your focus and let the right people meet you halfway.',
    'What settles inside you now becomes tomorrow\'s confidence.',
  ],
  mood: 'Open & Grounded',
  backgroundImage: 'space/aries.jpg',
  audioFile: '',
  source: 'Beyond Space',
};

const mediaSource = (source = '') =>
  /^(blob:|data:|https?:\/\/|\/)/i.test(source) ? source : staticFile(source);

const clamp = {
  extrapolateLeft: 'clamp' as const,
  extrapolateRight: 'clamp' as const,
};

const rise = (frame: number, start: number, distance = 32) => ({
  opacity: interpolate(frame, [start, start + 24], [0, 1], clamp),
  translate: `0 ${interpolate(frame, [start, start + 32], [distance, 0], {
    ...clamp,
    easing: Easing.bezier(0.16, 1, 0.3, 1),
  })}px`,
});

export const SpaceHoroscopeVideo: React.FC<SpaceHoroscopeProps> = (props) => {
  const frame = useCurrentFrame();
  const {durationInFrames} = useVideoConfig();
  const progress = interpolate(frame, [0, durationInFrames - 1], [0, 100], clamp);
  const paragraphs = (props.paragraphs || []).filter(Boolean).slice(0, 3);

  return (
    <AbsoluteFill
      style={{
        overflow: 'hidden',
        color: '#FFF7DF',
        background: '#030302',
        fontFamily: 'Arial, Helvetica, sans-serif',
      }}
    >
      {props.backgroundImage ? (
        <Img
          src={mediaSource(props.backgroundImage)}
          style={{
            position: 'absolute',
            inset: 0,
            width: '100%',
            height: '100%',
            objectFit: 'cover',
            scale: interpolate(frame, [0, durationInFrames], [1.08, 1.02], clamp),
          }}
        />
      ) : null}
      <AbsoluteFill
        style={{
          background:
            'linear-gradient(180deg, rgba(0,0,0,.24), rgba(0,0,0,.08) 35%, rgba(0,0,0,.18) 56%, rgba(0,0,0,.78))',
        }}
      />
      <AbsoluteFill
        style={{
          border: '36px solid rgba(3,3,2,.18)',
          boxShadow: 'inset 0 0 0 2px rgba(233,198,117,.88), inset 0 0 0 16px rgba(233,198,117,.18)',
        }}
      />
      <div
        style={{
          position: 'absolute',
          left: 82,
          right: 82,
          top: 76,
          textAlign: 'center',
          ...rise(frame, 4, 18),
        }}
      >
        <div
          style={{
            color: '#E9C675',
            fontSize: 27,
            fontWeight: 950,
            letterSpacing: 7,
            textTransform: 'uppercase',
            textShadow: '0 6px 22px rgba(0,0,0,.72)',
          }}
        >
          Beyond Space • {props.sign}
        </div>
      </div>

      <div
        style={{
          position: 'absolute',
          left: 108,
          right: 108,
          bottom: 250,
          display: 'flex',
          flexDirection: 'column',
          gap: 20,
        }}
      >
        <div
          style={{
            ...rise(frame, 34, 34),
            color: '#E9C675',
            fontSize: 24,
            fontWeight: 950,
            letterSpacing: 5,
            textAlign: 'center',
            textTransform: 'uppercase',
          }}
        >
          {props.headline || 'Cosmic Weather'}
        </div>
        {paragraphs.map((paragraph, index) => (
          <div
            key={paragraph}
            style={{
              ...rise(frame, 58 + index * 24, 36),
              color: '#FFF9EA',
              fontFamily: 'Georgia, Times New Roman, serif',
              fontSize: 35,
              fontWeight: 700,
              lineHeight: 1.16,
              textAlign: 'center',
              textShadow: '0 8px 24px rgba(0,0,0,.72)',
            }}
          >
            {paragraph}
          </div>
        ))}
      </div>

      <div
        style={{
          position: 'absolute',
          left: 88,
          right: 88,
          bottom: 82,
          display: 'grid',
          gridTemplateColumns: '1fr',
          gap: 12,
          textAlign: 'center',
          ...rise(frame, 132, 30),
        }}
      >
        <div
          style={{
            justifySelf: 'center',
            minWidth: 0,
            padding: 0,
            border: '0',
            borderRadius: 0,
            background: 'transparent',
            color: '#FFE9A4',
            fontSize: 25,
            fontWeight: 950,
            letterSpacing: 2,
            textTransform: 'uppercase',
            textShadow: '0 7px 20px rgba(0,0,0,.82)',
          }}
        >
          Mood: {props.mood || 'Open & Grounded'}
        </div>
        <div style={{color: '#F8E5AA', fontSize: 24, fontWeight: 900, letterSpacing: 2}}>
          {props.date}
        </div>
        {props.source ? (
          <div style={{color: '#B7A783', fontSize: 15, fontWeight: 800}}>
            Source seed: {props.source}
          </div>
        ) : null}
      </div>
      <div
        style={{
          position: 'absolute',
          left: 0,
          right: 0,
          bottom: 0,
          height: 8,
          background: 'rgba(255,255,255,.12)',
        }}
      >
        <div style={{width: `${progress}%`, height: '100%', background: '#E9C675'}} />
      </div>
      {props.audioFile ? <Audio src={mediaSource(props.audioFile)} volume={0.96} /> : null}
    </AbsoluteFill>
  );
};
