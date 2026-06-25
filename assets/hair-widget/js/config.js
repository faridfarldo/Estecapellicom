/**
 * Widget configuration.
 *
 * In the WordPress theme, `window.HAIR_WIDGET_CFG` (printed by the section
 * template) supplies the asset base URL, the REST endpoints and the `mock`
 * flag. While `mock` is true the widget runs fully on the front end with a
 * placeholder analysis and a no-op submit — wire the real endpoints later.
 */
const OV = (typeof window !== 'undefined' && window.HAIR_WIDGET_CFG) || {};
const IMG = OV.imageBase || 'image/';

export const CONFIG = {
  // Endpoints (used only when mock === false).
  analyzeUrl: OV.analyzeUrl || '/api/analyze',
  submitUrl: OV.submitUrl || '/api/submit',

  // WordPress nonce for the theme REST routes (estecapelli_hair action).
  nonce: OV.nonce || '',

  // Front-end-only mode: placeholder analysis, no network calls.
  mock: OV.mock !== undefined ? OV.mock : false,

  // Image compression before upload.
  image: {
    maxDimension: 1500, // longest edge, px
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
      title: 'Front of head',
      hint: 'Look straight at the camera and fit your face inside the oval.',
      pose: 'front',
      auto: true,
      guide: IMG + 'Front.webp',
    },
    {
      id: 'side',
      title: 'Side profile',
      hint: 'Turn your head to the side, matching the example, then tap capture.',
      pose: 'side',
      auto: false,
      guide: IMG + 'SIde.webp',
    },
    {
      id: 'top',
      title: 'Top (crown)',
      hint: 'Tilt your head down so the camera sees the top of your scalp.',
      pose: 'top',
      auto: false,
      guide: IMG + 'Top.webp',
    },
    {
      id: 'donor',
      title: 'Back (donor area)',
      hint: 'Show the back of your head, matching the example, then tap capture.',
      pose: 'donor',
      auto: false,
      guide: IMG + 'Back.webp',
    },
  ],
};
