/**
 * Camera helpers: open the stream, grab a still frame, and compress it
 * client-side before upload. Also a graceful file-input fallback for when
 * camera permission is denied or getUserMedia is unavailable.
 */
import { CONFIG } from './config.js?v=3';

/**
 * Request the camera. Returns the MediaStream, or throws so the caller can
 * fall back to the file input.
 */
export async function openCamera({ facingMode = 'user' } = {}) {
  if (!navigator.mediaDevices?.getUserMedia) {
    throw new Error('getUserMedia not supported');
  }
  return navigator.mediaDevices.getUserMedia({
    audio: false,
    video: {
      facingMode,
      width: { ideal: 1280 },
      height: { ideal: 1280 },
    },
  });
}

export function stopStream(stream) {
  if (!stream) return;
  for (const track of stream.getTracks()) track.stop();
}

/**
 * Draw the current video frame to an offscreen canvas, scaled so the longest
 * edge is at most CONFIG.image.maxDimension, then return a compressed JPEG Blob
 * plus an object URL for preview.
 *
 * The front camera ("user") is mirrored on screen; we un-mirror on capture so
 * the saved photo reads naturally (matches what the analyst expects).
 */
export async function captureFromVideo(video, { mirror = false } = {}) {
  const vw = video.videoWidth;
  const vh = video.videoHeight;
  if (!vw || !vh) throw new Error('Video not ready');

  const { maxDimension, quality, mimeType } = CONFIG.image;
  const scale = Math.min(1, maxDimension / Math.max(vw, vh));
  const w = Math.round(vw * scale);
  const h = Math.round(vh * scale);

  const canvas = document.createElement('canvas');
  canvas.width = w;
  canvas.height = h;
  const ctx = canvas.getContext('2d');

  if (mirror) {
    ctx.translate(w, 0);
    ctx.scale(-1, 1);
  }
  ctx.drawImage(video, 0, 0, w, h);

  return canvasToResult(canvas, mimeType, quality);
}

/**
 * Compress an arbitrary image File (from the <input type=file> fallback) the
 * same way, so uploads are consistent regardless of source.
 */
export async function compressFile(file) {
  const bitmap = await createImageBitmap(file);
  const { maxDimension, quality, mimeType } = CONFIG.image;
  const scale = Math.min(1, maxDimension / Math.max(bitmap.width, bitmap.height));
  const w = Math.round(bitmap.width * scale);
  const h = Math.round(bitmap.height * scale);

  const canvas = document.createElement('canvas');
  canvas.width = w;
  canvas.height = h;
  canvas.getContext('2d').drawImage(bitmap, 0, 0, w, h);
  bitmap.close?.();

  return canvasToResult(canvas, mimeType, quality);
}

function canvasToResult(canvas, mimeType, quality) {
  return new Promise((resolve, reject) => {
    canvas.toBlob(
      (blob) => {
        if (!blob) return reject(new Error('Compression failed'));
        resolve({ blob, url: URL.createObjectURL(blob), width: canvas.width, height: canvas.height });
      },
      mimeType,
      quality
    );
  });
}
