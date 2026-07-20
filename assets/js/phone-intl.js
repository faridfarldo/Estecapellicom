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
	var config = window.EstecapelliPhone || {};
	var i18n = config.i18n || {};

	// Friendly messages keyed by intl-tel-input's getValidationError() codes.
	var ERRORS = {
		0: i18n.invalid || 'Please enter a valid phone number.',            // IS_POSSIBLE (shouldn't reach here)
		1: i18n.countryCode || 'Please select a valid country code.',        // INVALID_COUNTRY_CODE
		2: i18n.tooShort || 'This number is too short for the selected country.',
		3: i18n.tooLong || 'This number is too long for the selected country.',
		4: i18n.areaCode || 'Please enter the full phone number, including the area code.', // LOCAL_ONLY
		default: i18n.invalidCountry || 'Please enter a valid phone number for the selected country.'
	};
	var REQUIRED_ERROR = i18n.required || 'Please enter your phone number.';

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

		var touched = false; // don't nag before the visitor has finished the field

		// Returns: true = valid, false = invalid, null = can't tell yet (no utils).
		function checkValidity() {
			if (typeof iti.isValidNumber !== 'function') return null;
			return iti.isValidNumber();
		}

		// Show/clear the red error for the current value. Only shows once the
		// field has been "touched" (blurred or a submit was attempted).
		function reflectValidity() {
			var val = (input.value || '').trim();
			if (!val) {
				setError(input, ( touched && input.hasAttribute('required') ) ? REQUIRED_ERROR : '');
				return true; // empty handled at submit; not an "invalid number"
			}
			var valid = checkValidity();
			if (valid === false) {
				if (touched) {
					var code = (typeof iti.getValidationError === 'function') ? iti.getValidationError() : -1;
					setError(input, ERRORS[code] || ERRORS.default);
				}
				return false;
			}
			setError(input, ''); // valid, or utils not loaded yet
			return true;
		}

		input.addEventListener('input', function () {
			// Fallback while utils loads / if the CDN import fails: never let
			// letters sit in a phone field (strictMode covers it once loaded).
			if (checkValidity() === null) {
				var cleaned = stripLetters(input.value);
				if (cleaned !== input.value) { input.value = cleaned; }
			}
			// Live feedback after the first interaction: turn red / clear as they type.
			if (touched) { reflectValidity(); }
		});

		// The moment they leave the field, validate and show the error in red.
		input.addEventListener('blur', function () {
			touched = true;
			reflectValidity();
		});

		var form = input.closest('form');
		if (!form) return;

		// CAPTURE phase → runs before main.js's AJAX submit handler, so a bad
		// number cancels the whole submit (AJAX or classic POST) for every form.
		form.addEventListener('submit', function (e) {
			touched = true;
			var val = (input.value || '').trim();

			// Required phone fields must not be empty.
			if (!val) {
				if (input.hasAttribute('required')) {
					e.preventDefault();
					e.stopImmediatePropagation();
					setError(input, REQUIRED_ERROR);
					input.focus();
				}
				return;
			}

			var valid = checkValidity();
			if (valid === false) {
				e.preventDefault();
				e.stopImmediatePropagation();
				reflectValidity();
				input.focus();
				return;
			}

			// Valid: store the canonical, country-prefixed number (E.164) for the
			// CRM. We ONLY rewrite the field when the number is valid — never
			// prepend a dial code to a wrong number. Form submits immediately
			// after, so this value is what gets posted.
			setError(input, '');
			if (valid === true && typeof iti.getNumber === 'function') {
				var e164 = iti.getNumber(); // e.g. +905321234567
				if (e164) { input.value = e164; }
			}
		}, true);
	});
})();
