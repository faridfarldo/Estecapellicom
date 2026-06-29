/**
 * Hair Analysis widget — multi-step wizard state machine + rendering.
 *
 * Stages: intro → capture(×4) → analyzing → result → form → done.
 * Vanilla JS, no framework, so it ports into the WP hero with no build step.
 */
// NOTE: the ?v=N query on these relative imports cache-busts the whole module
// graph. Bump N (here AND in analyze.js / submit.js / camera.js / face-detect.js)
// whenever you change any widget JS, so browsers never run a stale mix.
import { CONFIG } from './config.js?v=2';
import { openCamera, stopStream, captureFromVideo, compressFile } from './camera.js?v=2';
import { startFaceGate } from './face-detect.js?v=2';
import { analyzePhotos } from './analyze.js?v=2';
import { submitLead } from './submit.js?v=2';

const STEPS = CONFIG.steps;

export class HairAnalysisWidget {
  constructor(root) {
    this.root = root;
    this.root.classList.add('hair-widget');

    // Captured photos keyed by step id → { blob, url }.
    this.photos = {};
    this.analysis = null;

    // Contact is collected UP FRONT (intro), before any photo is taken.
    this.contact = null; // { name, phone, email, consent }
    this.method = ''; // preferred contact channel: 'whatsapp' | 'call' | 'email'
    this.submitted = false; // guard so the lead is sent only once

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

  // --- contact (collected up front, on the intro screen) -------------------

  onIntroSubmit(form) {
    const name = form.name.value.trim();
    // Prepend the selected country dial code (intl-tel-input keeps it separate),
    // matching how phone-intl.js handles the site's other forms.
    let phone = form.phone.value.trim();
    if (this.iti && phone && phone[0] !== '+') {
      const cc = this.iti.getSelectedCountryData();
      if (cc && cc.dialCode) phone = '+' + cc.dialCode + ' ' + phone;
    }
    const email = form.email.value.trim();
    const consent = form.consent.checked;

    // Validate WITHOUT re-rendering, so the visitor doesn't lose what they typed.
    if (!this.method) return this.showError('Please choose how we should send your result.');
    if (!name || !phone || !email) return this.showError('Please enter your name, phone and email.');
    if (!consent) return this.showError('Please accept the consent to continue.');

    this.contact = { name, phone, email, consent };
    this.enterCaptureStep(0);
  }

  /** Update the inline error in place (no re-render → form input is preserved). */
  showError(msg) {
    this.error = msg;
    const el = this.root.querySelector('[data-error]');
    if (el) {
      el.textContent = msg;
      el.hidden = false;
    }
  }

  photoBlobs() {
    return Object.fromEntries(
      Object.entries(this.photos).map(([id, p]) => [id, p.blob])
    );
  }

  methodLabel() {
    return { whatsapp: 'WhatsApp', call: 'a direct call', email: 'email' }[this.method] || '';
  }

  // --- analysis + submission ----------------------------------------------

  // After the last photo: run the AI estimate, then send the lead (photos +
  // contact + estimate) once, then show the result. The lead is sent even if
  // the AI estimate fails, so no enquiry is ever lost.
  async runAnalysis() {
    this.setStage('analyzing');

    try {
      this.analysis = await analyzePhotos(this.photoBlobs());
    } catch {
      this.analysis = null;
    }

    if (!this.submitted && this.contact) {
      try {
        await submitLead({
          photos: this.photoBlobs(),
          analysis: this.analysis,
          contact: this.contact,
          method: this.method,
        });
        this.submitted = true;
      } catch {
        // The estimate is still shown; the team can be reached via the page's
        // other contact routes if this single POST failed.
      }
    }

    this.setStage('result');
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
    const methods = [
      { id: 'whatsapp', label: 'WhatsApp' },
      { id: 'call', label: 'Direct call' },
      { id: 'email', label: 'Email' },
    ];
    return `
      <div class="hw-card hw-intro">
        <span class="hw-eyebrow">Estecapelli AI</span>
        <h2 class="hw-title hw-title--sm">Your analysis has two stages</h2>

        <ol class="hw-stages">
          <li class="hw-stage-item">
            <span class="hw-stage-num">1</span>
            <div class="hw-stage-text">
              <strong>Instant AI pre-check — right here</strong>
              <p>Our AI reviews your photos and gives a first, surface-level estimate in seconds.</p>
            </div>
          </li>
          <li class="hw-stage-item">
            <span class="hw-stage-num">2</span>
            <div class="hw-stage-text">
              <strong>Specialist review — our lab &amp; doctors</strong>
              <p>Our medical team completes the estimate, corrects any error and prepares your personalised plan.</p>
            </div>
          </li>
        </ol>

        <div class="hw-method">
          <span class="hw-method-label">How should we send you the result?</span>
          <div class="hw-method-options" role="radiogroup" aria-label="Preferred contact method">
            ${methods
              .map(
                (m) => `
              <button type="button" class="hw-chip ${this.method === m.id ? 'is-active' : ''}"
                data-method="${m.id}" role="radio" aria-checked="${this.method === m.id ? 'true' : 'false'}">
                ${esc(m.label)}
              </button>`
              )
              .join('')}
          </div>
        </div>

        <div class="hw-reveal ${this.method ? 'is-open' : ''}" data-reveal>
          <div class="hw-reveal-inner">
            <p class="hw-error" data-error role="alert" ${this.error ? '' : 'hidden'}>${esc(this.error)}</p>

            <form class="hw-form hw-intro-form" novalidate>
              <label class="hw-field">
                <span>Full name</span>
                <input name="name" type="text" autocomplete="name" required />
              </label>
              <label class="hw-field">
                <span>Phone / WhatsApp</span>
                <input name="phone" class="js-intl-phone" type="tel" autocomplete="tel" inputmode="tel" required />
              </label>
              <label class="hw-field">
                <span>Email</span>
                <input name="email" type="email" autocomplete="email" inputmode="email" required />
              </label>
              <label class="hw-consent">
                <input name="consent" type="checkbox" required />
                <span>I agree Estecapelli may process my photos and contact details for this
                  assessment (KVKK / GDPR).</span>
              </label>
              <button class="hw-btn hw-btn--accent" type="submit">Submit my contacts &amp; start AI analysis</button>
            </form>

            <p class="hw-fineprint">Next, you'll take four quick photos. They are used only for this assessment.</p>
          </div>
        </div>
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
    const via = this.methodLabel();
    const viaSuffix = via ? ` via ${esc(via)}` : '';

    // The AI estimate couldn't be produced, but the photos + contact were sent.
    if (!a) {
      return `
        <div class="hw-card hw-result hw-result--plain">
          <div class="hw-check" aria-hidden="true">✓</div>
          <h2 class="hw-title hw-title--sm">Your photos have reached us</h2>
          <p class="hw-lead">We couldn't generate the instant estimate this time, but your photos
            and details reached our team. Our specialists will review them and contact you${viaSuffix}.</p>
        </div>`;
    }

    // No identifiable hair/scalp in the photos.
    if (a.status === 'no_hair_detected') {
      return `
        <div class="hw-card hw-result">
          <span class="hw-eyebrow hw-eyebrow--warn">Couldn't read your photos</span>
          <h2 class="hw-title hw-title--sm">We didn't detect your hair clearly</h2>
          <p class="hw-lead">${
            a.summary
              ? esc(a.summary)
              : "The photos didn't clearly show your hair and scalp. Please retake them in good light, filling the frame with your head."
          }</p>
          <button class="hw-btn hw-btn--accent" data-action="retake-all">Retake my photos</button>
          <p class="hw-disclaimer">Your details were still sent to our team — they'll also reach out${viaSuffix}.</p>
        </div>`;
    }

    // Minimal loss (≈ Norwood 1–2) → no transplant needed; hide the graft stat.
    const noTransplant = a.transplant_recommended === false;
    return `
      <div class="hw-card hw-result">
        <span class="hw-eyebrow">Preliminary AI estimate</span>
        <div class="hw-result-headline">
          <div class="hw-stat">
            <span class="hw-stat-label">Hairline stage</span>
            <span class="hw-stat-value">Norwood ${esc(a.norwood_stage)}</span>
          </div>
          ${
            noTransplant
              ? ''
              : `<div class="hw-stat">
            <span class="hw-stat-label">Estimated grafts</span>
            <span class="hw-stat-value">~${num(a.graft_range?.min)}–${num(a.graft_range?.max)}</span>
          </div>`
          }
        </div>
        ${a.summary ? `<p class="hw-lead">${esc(a.summary)}</p>` : ''}
        <p class="hw-cta-text">A more precise analysis will be sent to you by our specialists${viaSuffix}.</p>
        <p class="hw-disclaimer">This is an automatic, surface-level estimate and is
          <strong>not a medical diagnosis</strong>.</p>
      </div>`;
  }

  // --- event binding -------------------------------------------------------

  bind() {
    this.root.querySelectorAll('[data-action]').forEach((el) => {
      el.addEventListener('click', (e) => {
        const action = e.currentTarget.dataset.action;
        if (action === 'shutter') this.capture();
        else if (action === 'retake') this.retake();
        else if (action === 'confirm') this.confirmStep();
        else if (action === 'retake-all') this.enterCaptureStep(0);
      });
    });

    // Contact-method chips (intro). Toggle in place — re-rendering here would
    // wipe whatever the visitor has already typed into the form below.
    this.root.querySelectorAll('[data-method]').forEach((el) => {
      el.addEventListener('click', () => {
        this.method = el.dataset.method;
        this.root.querySelectorAll('[data-method]').forEach((c) => {
          const on = c === el;
          c.classList.toggle('is-active', on);
          c.setAttribute('aria-checked', on ? 'true' : 'false');
        });
        // Reveal the contact form on first pick (stays open if they switch).
        const reveal = this.root.querySelector('[data-reveal]');
        if (reveal && !reveal.classList.contains('is-open')) {
          reveal.classList.add('is-open');
          // Focus the first field once the open transition has settled.
          setTimeout(() => reveal.querySelector('input[name="name"]')?.focus(), 420);
        }
      });
    });

    const file = this.root.querySelector('.hw-file');
    if (file) file.addEventListener('change', (e) => this.onFileFallback(e.target.files?.[0]));

    const form = this.root.querySelector('.hw-intro-form');
    if (form) {
      form.addEventListener('submit', (e) => {
        e.preventDefault();
        this.onIntroSubmit(e.currentTarget);
      });
      this.initPhone(form);
    }
  }

  // Upgrade the phone field with the international dial-code selector. The
  // widget form is rendered dynamically after page load, so phone-intl.js
  // (which runs once at load) never sees it — we init it here on each render.
  initPhone(form) {
    const input = form.querySelector('input[name="phone"]');
    if (!input || typeof window.intlTelInput !== 'function') return;
    if (this.iti) {
      try { this.iti.destroy(); } catch (e) {}
      this.iti = null;
    }
    this.iti = window.intlTelInput(input, {
      initialCountry: 'auto',
      separateDialCode: true,
      // Append the country list to <body> so the collapsible reveal's
      // overflow:hidden never clips it.
      dropdownContainer: document.body,
      geoIpLookup: (cb) => {
        let cached = null;
        try { cached = sessionStorage.getItem('ec_country'); } catch (e) {}
        if (cached) { cb(cached); return; }
        fetch('https://ipwho.is/')
          .then((r) => r.json())
          .then((d) => {
            const cc = (d && d.success && d.country_code ? d.country_code : 'us').toLowerCase();
            try { sessionStorage.setItem('ec_country', cc); } catch (e) {}
            cb(cc);
          })
          .catch(() => cb('us'));
      },
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
