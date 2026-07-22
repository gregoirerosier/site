(() => {
  'use strict';
  if (window.__beyondVisitorAnalyticsLoaded) return;
  window.__beyondVisitorAnalyticsLoaded = true;
  if (navigator.doNotTrack === '1' || window.doNotTrack === '1') return;

  const path = window.location.pathname || '/';
  const blocked = /^\/(?:api|server\/admin|beyond-id\/admin|beyond-french\/admin|dailybreath\/admin|admin|assets|sql|tools|docs)(?:\/|$)/i;
  if (blocked.test(path) || /\.(?:css|js|json|xml|txt|zip|pdf|png|jpe?g|gif|webp|svg|ico|mp3|mp4|webm)$/i.test(path)) return;

  const payload = JSON.stringify({
    path,
    title: document.title || '',
    referrer: document.referrer || '',
    screen_width: window.screen?.width || window.innerWidth || 0,
  });

  const send = () => {
    fetch('/api/analytics/track.php', {
      method: 'POST',
      credentials: 'same-origin',
      keepalive: true,
      headers: {'Content-Type': 'application/json'},
      body: payload,
    }).catch(() => {});
  };

  if ('requestIdleCallback' in window) window.requestIdleCallback(send, {timeout: 1800});
  else window.setTimeout(send, 450);
})();
