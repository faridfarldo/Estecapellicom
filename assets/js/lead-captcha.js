/*
 * Lead captcha — load the security code into every [data-lead-captcha] block.
 *
 * The code comes from an uncached REST route (see inc/lead-captcha.php), so a
 * cached contact page still gets a fresh one per visitor. Each code is
 * single-use on the server: after any submit the page reloads with a new one.
 *
 * The contact form posts and redirects, so a wrong code used to mean retyping
 * everything. The visitor's details are kept in sessionStorage across that one
 * redirect and put back when the page returns with ?lead_error=…; they are
 * dropped as soon as the page returns with ?sent=1.
 */
(function () {
	'use strict';

	var cfg = window.EstecapelliCaptcha || {};
	var i18n = cfg.i18n || {};
	if (!cfg.endpoint) return;

	var DRAFT_KEY = 'ec_lead_draft';
	var DRAFT_FIELDS = ['lead_name', 'lead_phone', 'lead_email', 'lead_treatment', 'lead_message'];
	var ttlMs = (parseInt(cfg.ttl, 10) || 7200) * 1000;

	function store(action, value) {
		try {
			if (action === 'get') return window.sessionStorage.getItem(DRAFT_KEY);
			if (action === 'set') window.sessionStorage.setItem(DRAFT_KEY, value);
			if (action === 'remove') window.sessionStorage.removeItem(DRAFT_KEY);
		} catch (e) { /* Storage disabled — the form still works, it just isn't refilled. */ }
		return null;
	}

	function setup(block) {
		var form = block.closest('form');
		var img = block.querySelector('[data-lead-captcha-image]');
		var status = block.querySelector('[data-lead-captcha-status]');
		var refresh = block.querySelector('[data-lead-captcha-refresh]');
		var input = block.querySelector('input[name="lead_captcha"]');
		var token = block.querySelector('input[name="lead_captcha_token"]');
		if (!form || !img || !input || !token) return;

		var loadedAt = 0;
		var inFlight = false;

		function showStatus(text) {
			if (!status) return;
			status.textContent = text;
			status.hidden = !text;
		}

		function load() {
			if (inFlight) return;
			inFlight = true;
			token.value = '';
			input.value = '';
			img.hidden = true;
			showStatus(i18n.loading || 'Loading the security code…');

			fetch(cfg.endpoint, {
				credentials: 'same-origin',
				cache: 'no-store',
				headers: { 'X-Requested-With': 'XMLHttpRequest' }
			})
				.then(function (response) { return response.json(); })
				.then(function (data) {
					if (!data || !data.image || !data.token) throw new Error('empty');
					img.src = data.image;
					img.hidden = false;
					token.value = data.token;
					loadedAt = Date.now();
					showStatus('');
				})
				.catch(function () {
					showStatus(i18n.failed || 'The security code could not be loaded.');
				})
				.then(function () { inFlight = false; });
		}

		if (refresh) {
			refresh.addEventListener('click', function () { load(); input.focus(); });
		}

		// A tab left open past the code's lifetime gets a new one on return,
		// rather than an "expired" error after the visitor has typed it.
		document.addEventListener('visibilitychange', function () {
			if (document.visibilityState === 'visible' && loadedAt && Date.now() - loadedAt > ttlMs - 5 * 60 * 1000) {
				load();
			}
		});

		input.addEventListener('input', function () {
			var digits = input.value.replace(/\D+/g, '');
			if (digits !== input.value) input.value = digits;
			input.setCustomValidity('');
		});

		form.addEventListener('submit', function (e) {
			if (!token.value) {
				e.preventDefault();
				e.stopImmediatePropagation();
				showStatus(i18n.failed || 'The security code could not be loaded.');
				load();
				return;
			}
			if (!input.value.trim()) {
				e.preventDefault();
				e.stopImmediatePropagation();
				input.setCustomValidity(i18n.missing || 'Please type the security code shown in the image.');
				input.reportValidity();
				return;
			}
			var draft = {};
			DRAFT_FIELDS.forEach(function (name) {
				var field = form.querySelector('[name="' + name + '"]');
				if (field) draft[name] = field.value;
			});
			store('set', JSON.stringify(draft));
		});

		restoreDraft(form);
		load();
	}

	function restoreDraft(form) {
		var params = new URLSearchParams(window.location.search);
		var footerResult = params.get('lead_form') === 'footer';
		if (params.get('sent') && !footerResult) { store('remove'); return; }
		if (!params.get('lead_error') || footerResult) return;

		var raw = store('get');
		store('remove');
		if (!raw) return;
		var draft;
		try { draft = JSON.parse(raw); } catch (e) { return; }

		function apply() {
			DRAFT_FIELDS.forEach(function (name) {
				var field = form.querySelector('[name="' + name + '"]');
				if (!field || typeof draft[name] !== 'string' || !draft[name]) return;
				var iti = name === 'lead_phone' && window.intlTelInput && window.intlTelInput.getInstance
					? window.intlTelInput.getInstance(field)
					: null;
				if (iti && typeof iti.setNumber === 'function') {
					iti.setNumber(draft[name]);
				} else {
					field.value = draft[name];
				}
			});
		}
		// After every other script has run, so the phone field is already the
		// intl-tel-input control and takes the number with its country code.
		if (document.readyState === 'complete') { apply(); } else { window.addEventListener('load', apply, { once: true }); }
	}

	function init() {
		document.querySelectorAll('[data-lead-captcha]').forEach(setup);
	}

	if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', init, { once: true });
	} else {
		init();
	}
})();
