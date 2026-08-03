/*
 * Footer lead form — submit in place without a page reload.
 *
 * This intentionally lives outside main.js and is excluded from WP Rocket's
 * delayed-JavaScript pass. The footer must have its submit listener before a
 * visitor's first interaction; the normal POST remains as a no-JS fallback.
 */
(function () {
	'use strict';

	/* Mirrors the helper in main.js — assets/js/analytics.js listens for these. */
	function emit(name, detail) {
		document.dispatchEvent(new CustomEvent('estecapelli:' + name, { detail: detail || {} }));
	}

	function ready(callback) {
		if ('loading' === document.readyState) {
			document.addEventListener('DOMContentLoaded', callback, { once: true });
		} else {
			callback();
		}
	}

	function resetTurnstile(form) {
		if (!window.turnstile) return;
		form.querySelectorAll('.cf-turnstile').forEach(function (widget) {
			try { window.turnstile.reset(widget); } catch (e) {}
		});
	}

	ready(function () {
		var root = document.getElementById('footer-lead');
		if (!root) return;

		var form = root.querySelector('form[data-footer-lead-form]');
		var feedback = root.querySelector('[data-footer-lead-feedback]');
		var feedbackText = feedback ? feedback.querySelector('[data-footer-lead-feedback-text]') : null;
		var button = form ? form.querySelector('button[type="submit"]') : null;
		var cfg = window.EstecapelliLead || {};
		var i18n = cfg.i18n || {};
		if (!form || !feedback || !feedbackText || !button || !cfg.ajax) return;

		form.addEventListener('submit', function (event) {
			event.preventDefault();
			if (form.dataset.submitting === '1') return;

			root.querySelectorAll('.contact-alert:not(.lead-form__feedback)').forEach(function (alert) { alert.hidden = true; });
			feedback.hidden = true;
			feedback.classList.remove('contact-alert--error');
			var data = new FormData(form);
			data.append('action', 'estecapelli_lead');
			data.append('nonce', cfg.nonce || '');

			var originalButton = button.innerHTML;
			form.dataset.submitting = '1';
			button.disabled = true;
			button.textContent = i18n.sending || 'Sending…';

			fetch(cfg.ajax, { method: 'POST', body: data, credentials: 'same-origin' })
				.then(function (response) {
					return response.json().catch(function () { return { success: false }; });
				})
				.then(function (result) {
					if (result && result.success) {
						// Read the language field before form.reset() clears it.
						var lang = form.querySelector('[name="lead_lang"]');
						var treatment = form.querySelector('[name="lead_treatment"]');
						emit('lead-success', {
							location: 'footer',
							treatment: treatment ? treatment.value : '',
							language: lang ? lang.value : ''
						});
						feedbackText.textContent = i18n.thanks || (result.data && result.data.message) || 'Thank you! Your request has been received.';
						feedback.classList.remove('contact-alert--error');
						form.reset();
						root.querySelectorAll('.contact-form__error').forEach(function (error) { error.remove(); });
					} else {
						var message = (result && result.data && result.data.message) || i18n.error || 'Something went wrong. Please try again.';
						emit('lead-error', { location: 'footer', message: message });
						var serverErrors = window.EstecapelliLeadServerErrors || {};
						feedbackText.textContent = serverErrors[message] || message;
						feedback.classList.add('contact-alert--error');
					}
					feedback.hidden = false;
					resetTurnstile(form);
				})
				.catch(function () {
					emit('lead-error', { location: 'footer', message: 'network_error' });
					feedbackText.textContent = i18n.error || 'Something went wrong. Please try again.';
					feedback.classList.add('contact-alert--error');
					feedback.hidden = false;
					resetTurnstile(form);
				})
				.finally(function () {
					delete form.dataset.submitting;
					button.disabled = false;
					button.innerHTML = originalButton;
				});
		});
	});
})();
