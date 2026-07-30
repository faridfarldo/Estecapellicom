/**
 * Preliminary analysis seam.
 *
 * Sends the four captured photos (base64 JPEG) to the analysis endpoint as
 * JSON and returns the structured result. In dev this hits server.mjs, which
 * proxies to Claude vision server-side (the API key never reaches the browser).
 * In production the same request goes to a WP REST route that does the same.
 * The result shape is identical either way.
 *
 * @param {Record<string, Blob>} photos  keyed by step id (front/left/right/donor)
 * @returns {Promise<object>} analysis result
 */
import { CONFIG, freshNonce } from './config.js?v=7';

export async function analyzePhotos(photos, session = '') {
  // Front-end-only mode: return a placeholder estimate, no network call.
  if (CONFIG.mock) {
    await new Promise((r) => setTimeout(r, 1600));
    return {
      status: 'ok',
      norwood_stage: 3,
      graft_range: { min: 2000, max: 2800 },
      summary: CONFIG.copy.mockSummary,
    };
  }

  const entries = await Promise.all(
    Object.entries(photos).map(async ([id, blob]) => [id, await blobToBase64(blob)])
  );
  // Fresh, uncached nonce — the baked-in page nonce may be stale behind a cache.
  const nonce = await freshNonce();
  const payload = { photos: Object.fromEntries(entries), nonce, locale: CONFIG.locale, session };

  const res = await fetch(CONFIG.analyzeUrl, {
    method: 'POST',
    headers: { 'content-type': 'application/json' },
    body: JSON.stringify(payload),
  });
  if (!res.ok) throw new Error(`Analysis failed (${res.status})`);

  const data = await res.json();
  if (!data.ok || !data.analysis) throw new Error('Malformed analysis response');
  return data.analysis;
}

/** Blob → base64 string (no data: prefix), chunked to avoid call-stack limits. */
async function blobToBase64(blob) {
  const bytes = new Uint8Array(await blob.arrayBuffer());
  let binary = '';
  const chunk = 0x8000;
  for (let i = 0; i < bytes.length; i += chunk) {
    binary += String.fromCharCode.apply(null, bytes.subarray(i, i + chunk));
  }
  return btoa(binary);
}
