/*
 * Estecapelli — main script.
 *
 * 1. Mobile nav drawer (hamburger toggle, escape, link-click close, viewport)
 * 2. Language switcher dropdown (click toggle, click-outside, escape)
 * 3. Sticky-header shadow state on scroll
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

		nav.addEventListener('click', function (e) {
			var target = e.target.closest('a');
			if (!target) return;
			if (!window.matchMedia('(max-width: 1024px)').matches) return;

			var parentLi = target.parentElement;
			var isMegaTrigger =
				parentLi &&
				parentLi.classList.contains('has-megamenu') &&
				parentLi.parentElement &&
				parentLi.parentElement.classList.contains('site-nav__list');

			if (isMegaTrigger) {
				e.preventDefault();
				var siblings = parentLi.parentElement.querySelectorAll(':scope > li.has-megamenu');
				var willOpen = parentLi.getAttribute('data-mobile-open') !== 'true';
				siblings.forEach(function (sib) {
					sib.setAttribute('data-mobile-open', sib === parentLi && willOpen ? 'true' : 'false');
				});
				return;
			}

			setOpen(false);
		});

		var mq = window.matchMedia('(min-width: 1025px)');
		mq.addEventListener('change', function (ev) {
			if (ev.matches) {
				setOpen(false);
				nav.querySelectorAll('li.has-megamenu[data-mobile-open="true"]').forEach(function (li) {
					li.setAttribute('data-mobile-open', 'false');
				});
			}
		});
	}

	function initLangSwitch() {
		var roots = document.querySelectorAll('[data-lang-switch]');
		if (!roots.length) return;

		roots.forEach(function (root) {
			var toggle = root.querySelector('.lang-switch__toggle');
			var menu   = root.querySelector('.lang-switch__menu');
			if (!toggle || !menu) return;

			function setOpen(open) {
				toggle.setAttribute('aria-expanded', open ? 'true' : 'false');
				if (open) menu.removeAttribute('hidden');
				else menu.setAttribute('hidden', '');
			}

			toggle.addEventListener('click', function (e) {
				e.stopPropagation();
				var isOpen = toggle.getAttribute('aria-expanded') === 'true';
				setOpen(!isOpen);
			});

			document.addEventListener('click', function (e) {
				if (!root.contains(e.target)) setOpen(false);
			});

			document.addEventListener('keydown', function (e) {
				if (e.key === 'Escape' && toggle.getAttribute('aria-expanded') === 'true') {
					setOpen(false);
					toggle.focus();
				}
			});
		});
	}

	function initServicesTabs() {
		var lists = document.querySelectorAll('[data-services-tablist]');
		if (!lists.length) return;

		lists.forEach(function (list) {
			var tabs   = Array.prototype.slice.call(list.querySelectorAll('[data-services-tab]'));
			var section = list.closest('section');
			if (!section) return;
			var panels = Array.prototype.slice.call(section.querySelectorAll('[data-services-panel]'));

			function activate(idx) {
				tabs.forEach(function (t, i) {
					var selected = i === idx;
					t.setAttribute('aria-selected', selected ? 'true' : 'false');
					t.setAttribute('tabindex', selected ? '0' : '-1');
				});
				panels.forEach(function (p, i) {
					if (i === idx) {
						p.removeAttribute('hidden');
					} else {
						p.setAttribute('hidden', '');
					}
				});
			}

			tabs.forEach(function (tab, idx) {
				tab.addEventListener('click', function () {
					activate(idx);
				});
				tab.addEventListener('keydown', function (e) {
					var nextIdx;
					if (e.key === 'ArrowRight') nextIdx = (idx + 1) % tabs.length;
					else if (e.key === 'ArrowLeft') nextIdx = (idx - 1 + tabs.length) % tabs.length;
					else if (e.key === 'Home') nextIdx = 0;
					else if (e.key === 'End') nextIdx = tabs.length - 1;
					else return;
					e.preventDefault();
					activate(nextIdx);
					tabs[nextIdx].focus();
				});
			});
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

	function ready(fn) {
		if (document.readyState === 'loading') {
			document.addEventListener('DOMContentLoaded', fn);
		} else {
			fn();
		}
	}

	ready(function () {
		initMobileNav();
		initLangSwitch();
		initStickyHeader();
		initServicesTabs();
	});
})();
