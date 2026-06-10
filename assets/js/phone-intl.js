/*
 * Estecapelli — international phone input.
 *
 * Upgrades every `input.js-intl-phone` with intl-tel-input, auto-selecting the
 * country dial code from the visitor's IP (geolocation), while letting them
 * change it manually. On submit, the selected dial code is prepended so the
 * stored lead phone always carries the country prefix.
 *
 * Used by: footer quick-form, contact page form, and the ACF "form" section.
 */

(function () {
	'use strict';

	if (typeof window.intlTelInput !== 'function') return;

	var inputs = document.querySelectorAll('input.js-intl-phone');
	if (!inputs.length) return;

	// Resolve the visitor's country once, cached for the session.
	function geoLookup(callback) {
		var cached = null;
		try { cached = sessionStorage.getItem('ec_country'); } catch (e) {}
		if (cached) { callback(cached); return; }

		// intl-tel-input needs a lowercase ISO2 code; only cache a real hit so a
		// failed lookup never sticks the visitor on the 'us' fallback.
		function done(cc) {
			cc = (cc || '').toString().toLowerCase();
			if (cc) {
				try { sessionStorage.setItem('ec_country', cc); } catch (e) {}
				callback(cc);
			} else {
				callback('us');
			}
		}

		// Primary provider, with a fallback if it fails or is rate-limited.
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

	Array.prototype.forEach.call(inputs, function (input) {
		var iti = window.intlTelInput(input, {
			initialCountry: 'auto',
			separateDialCode: true,
			geoIpLookup: geoLookup
		});

		var form = input.closest('form');
		if (!form) return;

		form.addEventListener('submit', function () {
			var num = (input.value || '').trim();
			if (!num || num.charAt(0) === '+') return;
			var data = iti.getSelectedCountryData();
			if (data && data.dialCode) {
				input.value = '+' + data.dialCode + ' ' + num;
			}
		});
	});
})();
