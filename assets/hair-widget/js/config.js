/**
 * Widget configuration.
 *
 * In the WordPress theme, `window.HAIR_WIDGET_CFG` (printed by the section
 * template) supplies the asset base URL, the REST endpoints and the `mock`
 * flag. While `mock` is true the widget runs fully on the front end with a
 * placeholder analysis and a no-op submit — wire the real endpoints later.
 */
import { getHairWidgetCopy, normalizeLocale } from './i18n.js?v=6';

const OV = (typeof window !== 'undefined' && window.HAIR_WIDGET_CFG) || {};
const IMG = OV.imageBase || 'image/';
const LOCALE = normalizeLocale(OV.locale);
const COPY = getHairWidgetCopy(LOCALE);

export const CONFIG = {
  // Endpoints (used only when mock === false).
  analyzeUrl: OV.analyzeUrl || '/api/analyze',
  submitUrl: OV.submitUrl || '/api/submit',

  // WordPress nonce for the theme REST routes (estecapelli_hair action).
  // NOTE: the baked-in `nonce` below is a fallback only — it lives in the page
  // HTML, so a full-page cache (plugin / Nginx / Cloudflare) can freeze it and
  // it expires after ~12–24h, causing 403s for every visitor. Always prefer a
  // fresh one from `nonceUrl` (uncached REST route) via freshNonce().
  nonce: OV.nonce || '',
  nonceUrl: OV.nonceUrl || '',

  // Front-end-only mode: placeholder analysis, no network calls.
  mock: OV.mock !== undefined ? OV.mock : false,

  // Client-side copy follows the active WPML language because it bypasses
  // PHP gettext entirely.
  locale: LOCALE,
  copy: COPY,

  // Image compression before upload.
  image: {
    maxDimension: 1100, // longest edge, px — enough for a surface-level screen, ~half the image tokens of 1500
    quality: 0.8, // JPEG quality
    mimeType: 'image/jpeg',
  },

  // MediaPipe assets (loaded from CDN; runs fully in-browser).
  mediapipe: {
    visionWasm:
      'https://cdn.jsdelivr.net/npm/@mediapipe/tasks-vision@0.10.14/wasm',
    faceLandmarkerModel:
      'https://storage.googleapis.com/mediapipe-models/face_landmarker/face_landmarker/float16/1/face_landmarker.task',
  },

  // Front-step auto-capture thresholds. Tuned conservatively; loosen if capture
  // feels too fussy on real devices.
  autoCapture: {
    holdMs: 1000, // conditions must hold this long before firing
    maxYawDeg: 12, // |left/right head turn|
    maxPitchDeg: 14, // |up/down head tilt|
    maxRollDeg: 12, // |in-plane tilt|
    minFaceFrac: 0.32, // face bbox height as fraction of frame height
    maxFaceFrac: 0.78,
    centerTolFrac: 0.16, // allowed offset of face center from frame center
  },

  // The four capture steps, aligned 1:1 with the guide illustrations.
  steps: [
    {
      id: 'front',
      title: COPY.steps[0].title,
      hint: COPY.steps[0].hint,
      pose: 'front',
      auto: true,
      guide: IMG + 'Front.webp',
    },
    {
      id: 'side',
      title: COPY.steps[1].title,
      hint: COPY.steps[1].hint,
      pose: 'side',
      auto: false,
      guide: IMG + 'SIde.webp',
    },
    {
      id: 'top',
      title: COPY.steps[2].title,
      hint: COPY.steps[2].hint,
      pose: 'top',
      auto: false,
      guide: IMG + 'Top.webp',
    },
    {
      id: 'donor',
      title: COPY.steps[3].title,
      hint: COPY.steps[3].hint,
      pose: 'donor',
      auto: false,
      guide: IMG + 'Back.webp',
    },
  ],
};

/**
 * Fetch a FRESH WordPress nonce right before a request, so a cached page's
 * stale/expired nonce can never cause a 403. Cache-busted and `no-store` so no
 * caching layer can freeze it. Falls back to the baked-in nonce on any failure.
 * @returns {Promise<string>}
 */
export async function freshNonce() {
  if (!CONFIG.nonceUrl) return CONFIG.nonce;
  try {
    const res = await fetch(`${CONFIG.nonceUrl}?_=${Date.now()}`, { cache: 'no-store' });
    if (res.ok) {
      const data = await res.json();
      if (data && data.nonce) {
        CONFIG.nonce = data.nonce;
        return data.nonce;
      }
    }
  } catch {
    /* network hiccup — fall through to the baked-in nonce */
  }
  return CONFIG.nonce;
}
