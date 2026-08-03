/*
 * Cookie consent banner controller — the visitor-facing half of Consent Mode v2.
 *
 * The *defaults* are already set inline in <head>, above the GTM snippet, and a
 * returning visitor's stored choice is replayed there too (see inc/analytics.php).
 * This file only handles the part that needs the DOM: showing the banner to
 * someone who has not chosen yet, recording what they pick, and telling Google.
 *
 * Excluded from WP Rocket's delayed JavaScript. Delaying it would leave the
 * banner invisible until the visitor happened to click something — which is
 * both a consent failure and the point at which they have already been tracked
 * or, worse, not tracked at all.
 */
(function () {
	'use strict';

	var cfg = window.EstecapelliConsent || {};
	var COOKIE = cfg.cookie || 'estecapelli_consent';
	var VERSION = String(cfg.version || '1');
	var LIFETIME = parseInt(cfg.lifetime, 10) || 15552000;

	/* gtag() is defined by the inline bootstrap. Guard anyway: if the container
	   is switched off the banner must still not throw. */
	function gtag() {
		if (typeof window.gtag === 'function') {
			window.gtag.apply(null, arguments);
		}
	}

	function push(payload) {
		window.dataLayer = window.dataLayer || [];
		window.dataLayer.push(payload);
	}

	/** Stored choice, or null when absent, malformed or from an older version. */
	function readChoice() {
		var hit = document.cookie.match(new RegExp('(?:^|; )' + COOKIE + '=([^;]*)'));
		if (!hit) return null;

		var parts = decodeURIComponent(hit[1]).split('|');
		if (parts[0] !== VERSION) return null;

		var choice = { analytics: false, marketing: false };
		for (var i = 1; i < parts.length; i++) {
			var pair = parts[i].split(':');
			if (pair[0] in choice) { choice[pair[0]] = pair[1] === '1'; }
		}
		return choice;
	}

	function writeChoice(choice) {
		var value = VERSION +
			'|analytics:' + (choice.analytics ? '1' : '0') +
			'|marketing:' + (choice.marketing ? '1' : '0');

		document.cookie = COOKIE + '=' + encodeURIComponent(value) +
			'; path=/; max-age=' + LIFETIME + '; SameSite=Lax' +
			(cfg.secure ? '; Secure' : '');
	}

	/**
	 * Hand the choice to Google, then announce it on the dataLayer.
	 *
	 * Order matters: a tag listening for `consent_update` may fire immediately,
	 * and it must not fire while Google still holds the previous state.
	 */
	function applyChoice(choice, method) {
		var analytics = choice.analytics ? 'granted' : 'denied';
		var marketing = choice.marketing ? 'granted' : 'denied';

		gtag('consent', 'update', {
			analytics_storage: analytics,
			ad_storage: marketing,
			ad_user_data: marketing,
			ad_personalization: marketing,
			personalization_storage: marketing
		});

		window.EstecapelliConsentState = {
			analytics: choice.analytics,
			marketing: choice.marketing,
			restored: false
		};

		push({
			event: 'consent_update',
			consent_analytics: choice.analytics ? 'granted' : 'denied',
			consent_marketing: choice.marketing ? 'granted' : 'denied',
			consent_method: method
		});
	}

	function ready(callback) {
		if ('loading' === document.readyState) {
			document.addEventListener('DOMContentLoaded', callback, { once: true });
		} else {
			callback();
		}
	}

	ready(function () {
		var banner = document.getElementById('consent-banner');
		if (!banner) return;

		var options   = banner.querySelector('[data-consent-options]');
		var customise = banner.querySelector('[data-consent-customise]');
		var save      = banner.querySelector('[data-consent-save]');
		var toggles   = Array.prototype.slice.call(banner.querySelectorAll('[data-consent-toggle]'));

		function open(prefill) {
			if (prefill) {
				toggles.forEach(function (input) {
					input.checked = !!prefill[input.getAttribute('data-consent-toggle')];
				});
			}
			banner.hidden = false;
			requestAnimationFrame(function () { banner.classList.add('is-open'); });
		}

		function close() {
			banner.classList.remove('is-open');
			setTimeout(function () { banner.hidden = true; }, 250);
		}

		function decide(choice, method) {
			writeChoice(choice);
			applyChoice(choice, method);
			close();
		}

		banner.querySelectorAll('[data-consent-accept]').forEach(function (btn) {
			btn.addEventListener('click', function () {
				decide({ analytics: true, marketing: true }, 'accept_all');
			});
		});

		banner.querySelectorAll('[data-consent-reject]').forEach(function (btn) {
			btn.addEventListener('click', function () {
				decide({ analytics: false, marketing: false }, 'reject_all');
			});
		});

		if (customise && options && save) {
			customise.addEventListener('click', function () {
				options.hidden = false;
				customise.hidden = true;
				customise.setAttribute('aria-expanded', 'true');
				save.hidden = false;
			});

			save.addEventListener('click', function () {
				var choice = { analytics: false, marketing: false };
				toggles.forEach(function (input) {
					choice[input.getAttribute('data-consent-toggle')] = input.checked;
				});
				decide(choice, 'custom');
			});
		}

		/* Withdrawing consent has to be as easy as giving it, so anything marked
		   [data-consent-open] (the footer "Cookie settings" link) reopens the
		   banner pre-filled with the current choice. */
		document.addEventListener('click', function (e) {
			var trigger = e.target.closest('[data-consent-open]');
			if (!trigger) return;
			e.preventDefault();
			if (options && customise && save) {
				options.hidden = false;
				customise.hidden = true;
				customise.setAttribute('aria-expanded', 'true');
				save.hidden = false;
			}
			open(readChoice() || { analytics: false, marketing: false });
		});

		window.EstecapelliConsentAPI = { open: open, read: readChoice };

		if (!readChoice()) {
			open(null);
		}
	});
})();
