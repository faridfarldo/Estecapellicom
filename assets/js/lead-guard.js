/*
 * Lead guard — mint the interaction token every lead form carries.
 *
 * The point is not secrecy, it is cost: the honeypot and the signed timestamp
 * are printed into the page HTML, so a bot that reads the page once can replay
 * a POST to admin-ajax.php forever without ever running JavaScript. The token
 * comes from a separate, uncached REST call instead, so a submission that has
 * one was made by a client that executed this file.
 *
 * Minting happens on the visitor's first contact with a lead form — focus, tap
 * or keypress — which is always seconds before they can submit, so the request
 * has long since resolved by the time the form is sent. A form submitted
 * without a token is never rejected; it is only scored (see inc/lead-guard.php).
 */
(function () {
	'use strict';

	var cfg = window.EstecapelliLeadGuard || {};
	if (!cfg.endpoint) return;

	var SELECTOR = 'input[name="lead_token"]';
	var minted = '';
	var inFlight = null;

	function fill(token) {
		minted = token;
		var fields = document.querySelectorAll(SELECTOR);
		for (var i = 0; i < fields.length; i++) {
			fields[i].value = token;
		}
	}

	function mint() {
		if (minted || inFlight) return;
		inFlight = fetch(cfg.endpoint, {
			credentials: 'same-origin',
			headers: { 'X-Requested-With': 'XMLHttpRequest' }
		})
			.then(function (response) { return response.json(); })
			.then(function (data) {
				if (data && data.token) { fill(data.token); }
			})
			.catch(function () { /* Offline or blocked — the lead still goes through. */ })
			.then(function () { inFlight = null; });
	}

	/*
	 * The popup is injected/opened after load and the token field inside it is
	 * empty until then, so a token minted earlier has to be written into it too.
	 */
	function isLeadField(target) {
		if (!target || !target.closest) return false;
		var form = target.closest('form');
		return !!(form && form.querySelector(SELECTOR));
	}

	['focusin', 'pointerdown', 'keydown', 'touchstart'].forEach(function (type) {
		document.addEventListener(type, function (event) {
			if (!isLeadField(event.target)) return;
			if (minted) { fill(minted); return; }
			mint();
		}, { capture: true, passive: true });
	});

	/*
	 * The AI photo widget has no <form> and no token field, so none of the
	 * listeners above ever fire for it. Without this every AI lead arrived with
	 * no token at all and carried that penalty permanently — three of the five
	 * points needed to quarantine it, on a lane made of real patients sending
	 * photographs of their own heads. It mints when the widget opens, minutes
	 * before the visitor can finish, so the token is neither missing nor spent
	 * the instant it was issued.
	 */
	cfg.mint = mint;
	cfg.token = function () { return minted; };
	cfg.ready = function () {
		if (minted) { return Promise.resolve(minted); }
		mint();
		return Promise.resolve(inFlight).then(function () { return minted; });
	};
})();
