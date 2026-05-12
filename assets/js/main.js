/*
 * Estecapelli — main script.
 *
 * Responsibilities (Phase 2):
 *   1. Mobile nav toggle (open/close drawer, escape key, focus-trap-lite)
 *   2. Sticky-header scrolled state (adds shadow once page scrolled)
 */

(function () {
	'use strict';

	function initMobileNav() {
		var toggle = document.querySelector('[data-nav-toggle]');
		var nav    = document.querySelector('[data-site-nav]');
		if (!toggle || !nav) return;

		function setOpen(open) {
			toggle.setAttribute('aria-expanded', open ? 'true' : 'false');
			nav.setAttribute('data-open', open ? 'true' : 'false');
			document.body.classList.toggle('no-scroll', open);
		}

		toggle.addEventListener('click', function () {
			var isOpen = toggle.getAttribute('aria-expanded') === 'true';
			setOpen(!isOpen);
		});

		document.addEventListener('keydown', function (e) {
			if (e.key === 'Escape' && toggle.getAttribute('aria-expanded') === 'true') {
				setOpen(false);
				toggle.focus();
			}
		});

		// Close drawer when a nav link is clicked (mobile only).
		nav.addEventListener('click', function (e) {
			var target = e.target.closest('a');
			if (!target) return;
			if (window.matchMedia('(max-width: 960px)').matches) {
				setOpen(false);
			}
		});

		// Close drawer if viewport grows past breakpoint.
		var mq = window.matchMedia('(min-width: 961px)');
		mq.addEventListener('change', function (ev) {
			if (ev.matches) setOpen(false);
		});
	}

	function initStickyHeader() {
		var header = document.querySelector('[data-site-header]');
		if (!header) return;

		function update() {
			header.setAttribute('data-scrolled', window.scrollY > 8 ? 'true' : 'false');
		}
		update();
		window.addEventListener('scroll', update, { passive: true });
	}

	if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', function () {
			initMobileNav();
			initStickyHeader();
		});
	} else {
		initMobileNav();
		initStickyHeader();
	}
})();
