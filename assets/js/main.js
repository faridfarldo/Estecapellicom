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

	function initCategoryAccordion() {
		var roots = document.querySelectorAll('[data-category-accordion]');
		if (!roots.length) return;

		roots.forEach(function (root) {
			var items = root.querySelectorAll('[data-category-item]');

			items.forEach(function (item) {
				var toggle = item.querySelector('[data-category-toggle]');
				var panel  = item.querySelector('.categories-home__panel');
				if (!toggle || !panel) return;

				toggle.addEventListener('click', function () {
					var isOpen = toggle.getAttribute('aria-expanded') === 'true';

					items.forEach(function (other) {
						var otherToggle = other.querySelector('[data-category-toggle]');
						var otherPanel  = other.querySelector('.categories-home__panel');
						if (!otherToggle || !otherPanel) return;

						if (other === item) {
							var willOpen = !isOpen;
							otherToggle.setAttribute('aria-expanded', willOpen ? 'true' : 'false');
							if (willOpen) {
								otherPanel.removeAttribute('hidden');
							} else {
								otherPanel.setAttribute('hidden', '');
							}
						} else {
							otherToggle.setAttribute('aria-expanded', 'false');
							otherPanel.setAttribute('hidden', '');
						}
					});
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
		initCategoryAccordion();
	});
})();
