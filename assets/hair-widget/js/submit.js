/**
 * Lead submission seam.
 *
 * Posts the four photos + contact fields as a single multipart FormData
 * request. In production this maps onto the theme's existing lead pipeline
 * (inc/leads.php): a WP REST route that stores a private `lead` and notifies
 * the team. Field names are kept close to that handler's expectations.
 *
 * @param {object} args
 * @param {Record<string, Blob>} args.photos
 * @param {object} args.analysis        the preliminary result, sent along for context
 * @param {{name:string, phone:string, email:string, consent:boolean}} args.contact
 * @param {string} args.method          preferred contact channel (whatsapp|call|email)
 * @returns {Promise<object>}
 */
import { CONFIG, freshNonce } from './config.js?v=3';

export async function submitLead({ photos, analysis, contact, method }) {
  // Front-end-only mode: pretend the lead was sent (no backend yet).
  if (CONFIG.mock) {
    await new Promise((r) => setTimeout(r, 1200));
    return { ok: true };
  }

  const form = new FormData();
  form.append('lead_name', contact.name);
  form.append('lead_phone', contact.phone);
  form.append('lead_email', contact.email || '');
  form.append('lead_consent', contact.consent ? '1' : '0');
  form.append('lead_method', method || '');
  form.append('lead_source', 'hero-hair-analysis');
  form.append('analysis_json', JSON.stringify(analysis ?? {}));
  // Fresh, uncached nonce — the baked-in page nonce may be stale behind a cache.
  form.append('nonce', await freshNonce());

  for (const [id, blob] of Object.entries(photos)) {
    form.append(`photo_${id}`, blob, `${id}.jpg`);
  }

  const res = await fetch(CONFIG.submitUrl, { method: 'POST', body: form });
  if (!res.ok) throw new Error(`Submit failed (${res.status})`);

  const data = await res.json();
  if (!data.ok) throw new Error('Submission was rejected');
  return data;
}
