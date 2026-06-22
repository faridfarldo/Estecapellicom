/**
 * Hair Analysis widget — multi-step wizard state machine + rendering.
 *
 * Stages: intro → capture(×4) → analyzing → result → form → done.
 * Vanilla JS, no framework, so it ports into the WP hero with no build step.
 */
import { CONFIG } from './config.js';
import { openCamera, stopStream, captureFromVideo, compressFile } from './camera.js';
import { startFaceGate } from './face-detect.js';
import { analyzePhotos } from './analyze.js';
import { submitLead } from './submit.js';

const STEPS = CONFIG.steps;

export class HairAnalysisWidget {
  constructor(root) {
    this.root = root;
    this.root.classList.add('hair-widget');

    // Captured photos keyed by step id → { blob, url }.
    this.photos = {};
    this.analysis = null;

    // Camera/runtime state for the active capture step.
    this.stream = null;
    this.gate = null;
    this.captureState = 'live'; // 'live' | 'preview' | 'denied'

    this.stage = 'intro';
    this.stepIndex = 0;
    this.error = '';

    this.render();
  }

  // --- helpers -------------------------------------------------------------

  get step() {
    return STEPS[this.stepIndex];
  }

  /** Front + sides use the selfie camera; the donor (back) uses the rear one. */
  get facingMode() {
    return this.step?.pose === 'donor' ? 'environment' : 'user';
  }

  get mirror() {
    return this.facingMode === 'user';
  }

  setStage(stage) {
    this.teardownCamera();
    this.stage = stage;
    this.error = '';
    this.render();
  }

  teardownCamera() {
    this.gate?.stop();
    this.gate = null;
    stopStream(this.stream);
    this.stream = null;
  }

  // --- camera step lifecycle ----------------------------------------------

  async enterCaptureStep(index) {
    this.teardownCamera();
    this.stepIndex = index;
    this.stage = 'capture';
    this.captureState = 'live';
    this.error = '';
    this.render();
    await this.startLive();
  }

  async startLive() {
    this.captureState = 'live';
    this.render();
    try {
      this.stream = await openCamera({ facingMode: this.facingMode });
    } catch {
      this.captureState = 'denied';
      this.render();
      return;
    }

    const video = this.root.querySelector('.hw-video');
    if (!video) return;
    video.srcObject = this.stream;
    await video.play().catch(() => {});

    if (this.step.auto) this.beginAutoCapture(video);
  }

  beginAutoCapture(video) {
    const hintEl = this.root.querySelector('.hw-live-hint');
    const ring = this.root.querySelector('.hw-ring-fill');

    this.gate = startFaceGate(video, {
      onUpdate: ({ ok, reasons, holdProgress }) => {
        if (hintEl) hintEl.textContent = ok ? 'Hold still…' : reasons[0] || '';
        this.root.querySelector('.hw-stage')?.classList.toggle('is-aligned', ok);
        if (ring) ring.style.setProperty('--p', String(holdProgress));
      },
      onCapture: () => this.capture(),
      onError: () => {
        // MediaPipe failed to load → fall back to manual capture for this step.
        const btn = this.root.querySelector('.hw-shutter');
        if (btn) btn.hidden = false;
        if (hintEl) hintEl.textContent = 'Tap to capture when you’re ready.';
      },
    });
  }

  async capture() {
    const video = this.root.querySelector('.hw-video');
    if (!video) return;
    this.gate?.stop();
    this.gate = null;

    try {
      const { blob, url } = await captureFromVideo(video, { mirror: this.mirror });
      this.setPhoto(this.step.id, blob, url);
    } catch {
      this.error = 'Could not capture the photo. Please try again.';
    }
    this.captureState = 'preview';
    stopStream(this.stream);
    this.stream = null;
    this.render();
  }

  async onFileFallback(file) {
    if (!file) return;
    try {
      const { blob, url } = await compressFile(file);
      this.setPhoto(this.step.id, blob, url);
      this.captureState = 'preview';
      this.render();
    } catch {
      this.error = 'That image could not be read. Please try another.';
      this.render();
    }
  }

  setPhoto(id, blob, url) {
    if (this.photos[id]?.url) URL.revokeObjectURL(this.photos[id].url);
    this.photos[id] = { blob, url };
  }

  retake() {
    this.startLive();
  }

  confirmStep() {
    if (this.stepIndex < STEPS.length - 1) {
      this.enterCaptureStep(this.stepIndex + 1);
    } else {
      this.runAnalysis();
    }
  }

  // --- analysis ------------------------------------------------------------

  async runAnalysis() {
    this.setStage('analyzing');
    try {
      const blobs = Object.fromEntries(
        Object.entries(this.photos).map(([id, p]) => [id, p.blob])
      );
      this.analysis = await analyzePhotos(blobs);
      this.setStage('result');
    } catch {
      this.error = 'Analysis failed. Please try again.';
      this.stage = 'result';
      this.render();
    }
  }

  // --- submission ----------------------------------------------------------

  async onSubmit(form) {
    const name = form.name.value.trim();
    const phone = form.phone.value.trim();
    const email = form.email.value.trim();
    const consent = form.consent.checked;

    if (!name || !phone || !email) {
      this.error = 'Please enter your name, phone and email.';
      this.render();
      return;
    }
    if (!consent) {
      this.error = 'Please accept the consent to continue.';
      this.render();
      return;
    }

    this.stage = 'submitting';
    this.error = '';
    this.render();

    try {
      const blobs = Object.fromEntries(
        Object.entries(this.photos).map(([id, p]) => [id, p.blob])
      );
      await submitLead({ photos: blobs, analysis: this.analysis, contact: { name, phone, email, consent } });
      this.setStage('done');
    } catch {
      this.error = 'Submission failed. Please try again.';
      this.stage = 'form';
      this.render();
    }
  }

  // --- rendering -----------------------------------------------------------

  render() {
    this.root.innerHTML = this[`view_${this.stage}`]?.() ?? '';
    this.bind();
  }

  errorBanner() {
    return this.error ? `<p class="hw-error" role="alert">${esc(this.error)}</p>` : '';
  }

  /** Reference illustration ("match this pose") shown inside the camera stage. */
  guideInset(step = this.step) {
    if (!step?.guide) return '';
    return `
      <figure class="hw-guide">
        <img src="${esc(step.guide)}" alt="${esc(step.title)} example pose" />
        <figcaption>Match this</figcaption>
      </figure>`;
  }

  progressDots() {
    return `<div class="hw-dots" aria-hidden="true">${STEPS.map(
      (s, i) =>
        `<span class="hw-dot ${i < this.stepIndex ? 'is-done' : ''} ${
          i === this.stepIndex ? 'is-active' : ''
        }"></span>`
    ).join('')}</div>`;
  }

  view_intro() {
    return `
      <div class="hw-card hw-intro">
        <span class="hw-eyebrow">AI hair check</span>
        <h2 class="hw-title">Free preliminary hair analysis</h2>
        <p class="hw-lead">Take four quick photos and get an instant estimate of your
          hairline stage and graft range. Takes about a minute.</p>
        <button class="hw-btn hw-btn--accent" data-action="start">Start free analysis</button>
        <p class="hw-fineprint">Your photos are used only for this assessment.</p>
      </div>`;
  }

  view_capture() {
    const step = this.step;
    const stepNo = this.stepIndex + 1;

    let body;
    if (this.captureState === 'preview') {
      body = `
        <div class="hw-preview">
          <img class="hw-preview-img" src="${this.photos[step.id]?.url ?? ''}" alt="Captured ${esc(step.title)}" />
          ${this.guideInset(step)}
        </div>
        <div class="hw-actions">
          <button class="hw-btn hw-btn--ghost" data-action="retake">Retake</button>
          <button class="hw-btn hw-btn--accent" data-action="confirm">
            ${this.stepIndex < STEPS.length - 1 ? 'Looks good →' : 'Analyze my photos'}
          </button>
        </div>`;
    } else if (this.captureState === 'denied') {
      body = `
        <div class="hw-denied">
          ${step.guide ? `<img class="hw-guide-static" src="${esc(step.guide)}" alt="${esc(step.title)} example pose" />` : ''}
          <p>We couldn’t access your camera. Match the example above and upload a photo instead.</p>
          <label class="hw-btn hw-btn--accent">
            Choose photo
            <input class="hw-file" type="file" accept="image/*" capture="${step.pose === 'donor' ? 'environment' : 'user'}" hidden />
          </label>
        </div>`;
    } else {
      // live
      body = `
        <div class="hw-stage ${step.pose === 'front' ? 'hw-stage--oval' : 'hw-stage--' + step.pose}">
          <video class="hw-video ${this.mirror ? 'is-mirrored' : ''}" playsinline muted></video>
          <div class="hw-overlay hw-overlay--${step.pose}" aria-hidden="true"></div>
          ${this.guideInset(step)}
          ${
            step.auto
              ? `<svg class="hw-ring" viewBox="0 0 100 100" aria-hidden="true">
                   <circle class="hw-ring-track" cx="50" cy="50" r="46"></circle>
                   <circle class="hw-ring-fill" cx="50" cy="50" r="46" style="--p:0"></circle>
                 </svg>`
              : ''
          }
          <p class="hw-live-hint">${esc(step.hint)}</p>
        </div>
        <div class="hw-actions">
          <button class="hw-shutter" data-action="shutter" ${step.auto ? 'hidden' : ''} aria-label="Capture"></button>
          <label class="hw-btn hw-btn--ghost hw-btn--sm">
            Upload instead
            <input class="hw-file" type="file" accept="image/*" capture="${step.pose === 'donor' ? 'environment' : 'user'}" hidden />
          </label>
        </div>`;
    }

    return `
      <div class="hw-card hw-capture">
        <header class="hw-head">
          <span class="hw-step-no">Step ${stepNo} of ${STEPS.length}</span>
          <h2 class="hw-title hw-title--sm">${esc(step.title)}</h2>
          ${this.progressDots()}
        </header>
        ${this.errorBanner()}
        ${body}
      </div>`;
  }

  view_analyzing() {
    return `
      <div class="hw-card hw-analyzing">
        <div class="hw-spinner" aria-hidden="true"></div>
        <h2 class="hw-title hw-title--sm">Analyzing your photos…</h2>
        <p class="hw-lead">Our model is reviewing your hairline and donor area. This takes a few seconds.</p>
      </div>`;
  }

  view_result() {
    const a = this.analysis;
    if (!a) {
      return `
        <div class="hw-card hw-result">
          ${this.errorBanner()}
          <button class="hw-btn hw-btn--accent" data-action="restart">Try again</button>
        </div>`;
    }
    return `
      <div class="hw-card hw-result">
        <span class="hw-eyebrow">Preliminary estimate</span>
        <div class="hw-result-headline">
          <div class="hw-stat">
            <span class="hw-stat-label">Hairline stage</span>
            <span class="hw-stat-value">Norwood ${esc(a.norwood_stage)}</span>
          </div>
          <div class="hw-stat">
            <span class="hw-stat-label">Estimated grafts</span>
            <span class="hw-stat-value">~${num(a.graft_range?.min)}–${num(a.graft_range?.max)}</span>
          </div>
        </div>
        ${a.summary ? `<p class="hw-lead">${esc(a.summary)}</p>` : ''}
        <p class="hw-cta-text">This is an automatic estimate. If you'd like a doctor to review your
          photos, send them along with your contact details and our team will get back to you.</p>
        <button class="hw-btn hw-btn--accent" data-action="to-form">Send my photos to a doctor</button>
        <p class="hw-disclaimer">This is a preliminary estimate generated automatically and is
          <strong>not a medical diagnosis</strong>. A specialist will review your photos.</p>
      </div>`;
  }

  view_form() {
    const a = this.analysis;
    return `
      <div class="hw-card hw-form-card">
        <h2 class="hw-title hw-title--sm">Get your accurate assessment</h2>
        ${
          a
            ? `<p class="hw-form-note">Your estimate: <strong>Norwood ${esc(a.norwood_stage)}, ~${num(
                a.graft_range?.min
              )}–${num(a.graft_range?.max)} grafts</strong></p>`
            : ''
        }
        ${this.errorBanner()}
        <form class="hw-form" novalidate>
          <label class="hw-field">
            <span>Full name</span>
            <input name="name" type="text" autocomplete="name" required />
          </label>
          <label class="hw-field">
            <span>Phone / WhatsApp</span>
            <input name="phone" type="tel" autocomplete="tel" inputmode="tel" required />
          </label>
          <label class="hw-field">
            <span>Email</span>
            <input name="email" type="email" autocomplete="email" inputmode="email" required />
          </label>
          <label class="hw-consent">
            <input name="consent" type="checkbox" required />
            <span>I consent to Estecapelli processing my photos and contact details for the
              purpose of this assessment (KVKK / GDPR).</span>
          </label>
          <button class="hw-btn hw-btn--accent" type="submit">Submit</button>
        </form>
      </div>`;
  }

  view_submitting() {
    return `
      <div class="hw-card hw-analyzing">
        <div class="hw-spinner" aria-hidden="true"></div>
        <h2 class="hw-title hw-title--sm">Sending your request…</h2>
      </div>`;
  }

  view_done() {
    return `
      <div class="hw-card hw-done">
        <div class="hw-check" aria-hidden="true">✓</div>
        <h2 class="hw-title hw-title--sm">Thank you!</h2>
        <p class="hw-lead">Our team has received your photos and will contact you shortly with a
          personalized assessment.</p>
      </div>`;
  }

  // --- event binding -------------------------------------------------------

  bind() {
    this.root.querySelectorAll('[data-action]').forEach((el) => {
      el.addEventListener('click', (e) => {
        const action = e.currentTarget.dataset.action;
        if (action === 'start') this.enterCaptureStep(0);
        else if (action === 'shutter') this.capture();
        else if (action === 'retake') this.retake();
        else if (action === 'confirm') this.confirmStep();
        else if (action === 'to-form') this.setStage('form');
        else if (action === 'restart') this.runAnalysis();
      });
    });

    const file = this.root.querySelector('.hw-file');
    if (file) file.addEventListener('change', (e) => this.onFileFallback(e.target.files?.[0]));

    const form = this.root.querySelector('.hw-form');
    if (form)
      form.addEventListener('submit', (e) => {
        e.preventDefault();
        this.onSubmit(e.currentTarget);
      });
  }
}

// --- tiny utilities --------------------------------------------------------

function esc(s) {
  return String(s ?? '').replace(/[&<>"']/g, (c) => ({
    '&': '&amp;',
    '<': '&lt;',
    '>': '&gt;',
    '"': '&quot;',
    "'": '&#39;',
  })[c]);
}

function num(n) {
  return typeof n === 'number' ? n.toLocaleString('en-US') : '—';
}
