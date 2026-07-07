/**
 * Front-step auto-capture, powered by MediaPipe FaceLandmarker (WASM, in-browser).
 *
 * We watch the live video and fire `onCapture` once the face has been frontal
 * and well-framed for ~1 second. "Frontal & well-framed" means:
 *   - a face is detected, not clipped at the top (forehead visible)
 *   - head yaw / pitch / roll are all near zero (looking at the camera)
 *   - the face fills the right amount of the frame (not too close / far)
 *   - the face is roughly centered (inside the oval guide)
 *
 * Side and donor steps don't use this — Face Mesh doesn't track profile/back
 * views, so those are manual captures.
 */
import { CONFIG } from './config.js?v=3';

const RAD2DEG = 180 / Math.PI;

let _landmarker = null;

/** Load the FaceLandmarker once and cache it. */
async function getLandmarker() {
  if (_landmarker) return _landmarker;
  const { FilesetResolver, FaceLandmarker } = await import(
    'https://cdn.jsdelivr.net/npm/@mediapipe/tasks-vision@0.10.14'
  );
  const fileset = await FilesetResolver.forVisionTasks(CONFIG.mediapipe.visionWasm);
  _landmarker = await FaceLandmarker.createFromOptions(fileset, {
    baseOptions: {
      modelAssetPath: CONFIG.mediapipe.faceLandmarkerModel,
      delegate: 'GPU',
    },
    runningMode: 'VIDEO',
    numFaces: 1,
    outputFacialTransformationMatrixes: true,
  });
  return _landmarker;
}

/**
 * Decode head yaw/pitch/roll (degrees) from MediaPipe's 4x4 facial
 * transformation matrix (column-major). At identity (facing the camera) all
 * three are ~0, which is exactly the gate we want.
 */
function eulerFromMatrix(data) {
  // m[row][col] = data[col*4 + row]
  const m00 = data[0], m10 = data[1], m20 = data[2];
  const m21 = data[6], m22 = data[10];

  const sy = Math.hypot(m00, m10);
  let x, y, z;
  if (sy > 1e-6) {
    x = Math.atan2(m21, m22);
    y = Math.atan2(-m20, sy);
    z = Math.atan2(m10, m00);
  } else {
    x = Math.atan2(-data[9], data[5]);
    y = Math.atan2(-m20, sy);
    z = 0;
  }
  return { pitch: x * RAD2DEG, yaw: y * RAD2DEG, roll: z * RAD2DEG };
}

/** Axis-aligned bbox (normalized 0..1) from the landmark cloud. */
function boundingBox(landmarks) {
  let minX = 1, minY = 1, maxX = 0, maxY = 0;
  for (const p of landmarks) {
    if (p.x < minX) minX = p.x;
    if (p.x > maxX) maxX = p.x;
    if (p.y < minY) minY = p.y;
    if (p.y > maxY) maxY = p.y;
  }
  return { minX, minY, maxX, maxY };
}

/**
 * Evaluate one detection result against the thresholds.
 * Returns { ok, reasons } — reasons lists what's currently wrong (for UI hints).
 */
function evaluate(result) {
  const cfg = CONFIG.autoCapture;
  const reasons = [];

  if (!result.faceLandmarks?.length) {
    return { ok: false, reasons: ['No face detected'] };
  }

  const landmarks = result.faceLandmarks[0];
  const box = boundingBox(landmarks);

  if (box.minY < 0.02) reasons.push('Move back — forehead is cut off');

  const faceFrac = box.maxY - box.minY;
  if (faceFrac < cfg.minFaceFrac) reasons.push('Move closer');
  else if (faceFrac > cfg.maxFaceFrac) reasons.push('Move back a little');

  const cx = (box.minX + box.maxX) / 2;
  const cy = (box.minY + box.maxY) / 2;
  if (Math.abs(cx - 0.5) > cfg.centerTolFrac || Math.abs(cy - 0.5) > cfg.centerTolFrac) {
    reasons.push('Center your face in the oval');
  }

  const matrix = result.facialTransformationMatrixes?.[0]?.data;
  if (matrix) {
    const { yaw, pitch, roll } = eulerFromMatrix(matrix);
    if (Math.abs(yaw) > cfg.maxYawDeg) reasons.push('Face the camera straight on');
    else if (Math.abs(pitch) > cfg.maxPitchDeg) reasons.push("Don't tilt up or down");
    else if (Math.abs(roll) > cfg.maxRollDeg) reasons.push('Keep your head level');
  }

  return { ok: reasons.length === 0, reasons };
}

/**
 * Start the auto-capture gate on a playing <video>.
 *
 * @param {HTMLVideoElement} video
 * @param {object} cb
 * @param {(state:{ok:boolean,reasons:string[],holdProgress:number})=>void} cb.onUpdate
 * @param {()=>void} cb.onCapture   fired once when conditions hold long enough
 * @param {(err:Error)=>void} [cb.onError]
 * @returns {{stop:()=>void}}
 */
export function startFaceGate(video, { onUpdate, onCapture, onError }) {
  let running = true;
  let okSince = 0;
  let fired = false;
  let rafId = 0;
  let lastVideoTime = -1;

  (async () => {
    let landmarker;
    try {
      landmarker = await getLandmarker();
    } catch (err) {
      onError?.(err);
      return;
    }

    const tick = () => {
      if (!running) return;
      rafId = requestAnimationFrame(tick);

      if (video.readyState < 2 || video.currentTime === lastVideoTime) return;
      lastVideoTime = video.currentTime;

      let result;
      try {
        result = landmarker.detectForVideo(video, performance.now());
      } catch {
        return; // transient; try next frame
      }

      const { ok, reasons } = evaluate(result);
      const now = performance.now();
      if (ok) {
        if (!okSince) okSince = now;
      } else {
        okSince = 0;
      }
      const holdProgress = okSince ? Math.min(1, (now - okSince) / CONFIG.autoCapture.holdMs) : 0;

      onUpdate?.({ ok, reasons, holdProgress });

      if (holdProgress >= 1 && !fired) {
        fired = true;
        running = false;
        cancelAnimationFrame(rafId);
        onCapture?.();
      }
    };

    rafId = requestAnimationFrame(tick);
  })();

  return {
    stop() {
      running = false;
      cancelAnimationFrame(rafId);
    },
  };
}
