/*
 * Estecapelli — international phone input + validation.
 *
 * Upgrades every `input.js-intl-phone` with intl-tel-input, auto-selecting the
 * country dial code from the visitor's IP (geolocation) while letting them
 * change it manually. Loads the library `utils` so we can:
 *   - block invalid characters as the visitor types (strictMode — no letters),
 *   - validate the number against the *selected country's* real format on submit
 *     (too short / too long / wrong for that country → the form won't send),
 *   - store the number in canonical E.164 form (+<dialcode><national>) so the
 *     CRM always receives a clean, country-prefixed phone.
 *
 * Runs its submit check in the CAPTURE phase so it fires *before* the popup's
 * AJAX handler (main.js) and can cancel a bad submit for every form.
 *
 * Used by: footer quick-form, contact page form, the ACF "form" section, and
 * the site-wide lead popup.
 */

(function () {
	'use strict';

	if (typeof window.intlTelInput !== 'function') return;

	var inputs = document.querySelectorAll('input.js-intl-phone');
	if (!inputs.length) return;

	var UTILS_URL = 'https://cdn.jsdelivr.net/npm/intl-tel-input@24.6.0/build/js/utils.js';

	// Friendly messages keyed by intl-tel-input's getValidationError() codes.
	var ERRORS = {
		0: 'Please enter a valid phone number.',            // IS_POSSIBLE (shouldn't reach here)
		1: 'Please select a valid country code.',           // INVALID_COUNTRY_CODE
		2: 'This number is too short for the selected country.',
		3: 'This number is too long for the selected country.',
		4: 'Please enter the full phone number, including the area code.', // LOCAL_ONLY
		default: 'Please enter a valid phone number for the selected country.'
	};

	// Resolve the visitor's country once, cached for the session.
	function geoLookup(callback) {
		var cached = null;
		try { cached = sessionStorage.getItem('ec_country'); } catch (e) {}
		if (cached) { callback(cached); return; }

		function done(cc) {
			cc = (cc || '').toString().toLowerCase();
			if (cc) {
				try { sessionStorage.setItem('ec_country', cc); } catch (e) {}
				callback(cc);
			} else {
				callback('us');
			}
		}

		fetch('https://ipwho.is/')
			.then(function (r) { return r.json(); })
			.then(function (d) {
				if (d && d.success && d.country_code) { done(d.country_code); }
				else { throw new Error('no country'); }
			})
			.catch(function () {
				fetch('https://ipapi.co/json/')
					.then(function (r) { return r.json(); })
					.then(function (d) { done(d && d.country_code ? d.country_code : ''); })
					.catch(function () { done(''); });
			});
	}

	// Show / clear an inline error message beneath a field.
	function setError(input, message) {
		var field = input.closest('.contact-form__field') || input.parentNode;
		if (!field) return;
		var el = field.querySelector('.contact-form__error');
		if (message) {
			if (!el) {
				el = document.createElement('span');
				el.className = 'contact-form__error';
				el.setAttribute('role', 'alert');
				field.appendChild(el);
			}
			el.textContent = message;
			input.setAttribute('aria-invalid', 'true');
		} else if (el) {
			el.parentNode.removeChild(el);
			input.removeAttribute('aria-invalid');
		}
	}

	// A cheap fallback filter for when utils hasn't loaded yet: strip anything
	// that could never belong in a phone number (letters especially).
	function stripLetters(value) {
		return value.replace(/[^0-9+\-\s().]/g, '');
	}

	Array.prototype.forEach.call(inputs, function (input) {
		var iti = window.intlTelInput(input, {
			initialCountry: 'auto',
			separateDialCode: true,
			strictMode: true,        // needs utils: blocks invalid keystrokes (no letters)
			geoIpLookup: geoLookup,
			loadUtils: function () { return import(UTILS_URL); }
		});

		// Fallback while utils loads (and if the CDN import ever fails): never let
		// letters sit in a phone field.
		input.addEventListener('input', function () {
			if (typeof iti.isValidNumber === 'function' && iti.isValidNumber() !== null) return; // utils ready → strictMode handles it
			var cleaned = stripLetters(input.value);
			if (cleaned !== input.value) { input.value = cleaned; }
		});

		// Clear a shown error as soon as the visitor edits the field.
		input.addEventListener('input', function () { setError(input, ''); });

		var form = input.closest('form');
		if (!form) return;

		// CAPTURE phase → runs before main.js's AJAX submit handler, so a bad
		// number cancels the whole submit (AJAX or classic POST) for every form.
		form.addEventListener('submit', function (e) {
			var val = (input.value || '').trim();

			// Required phone fields must not be empty.
			if (!val) {
				if (input.hasAttribute('required')) {
					e.preventDefault();
					e.stopImmediatePropagation();
					setError(input, 'Please enter your phone number.');
					input.focus();
				}
				return;
			}

			// isValidNumber() returns null until utils finishes loading; only
			// enforce strict per-country validation once it's available.
			var valid = (typeof iti.isValidNumber === 'function') ? iti.isValidNumber() : null;
			if (valid === false) {
				e.preventDefault();
				e.stopImmediatePropagation();
				var code = (typeof iti.getValidationError === 'function') ? iti.getValidationError() : -1;
				setError(input, ERRORS[code] || ERRORS.default);
				input.focus();
				return;
			}

			// Valid (or utils not loaded): store canonical E.164 for the CRM.
			setError(input, '');
			if (valid === true && typeof iti.getNumber === 'function') {
				var e164 = iti.getNumber(); // e.g. +905321234567
				if (e164) { input.value = e164; return; }
			}
			// Fallback prefix if utils never loaded.
			if (val.charAt(0) !== '+') {
				var data = iti.getSelectedCountryData();
				if (data && data.dialCode) { input.value = '+' + data.dialCode + ' ' + val; }
			}
		}, true);
	});
})();
