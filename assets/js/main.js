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

	function initPatientStories() {
		var roots = document.querySelectorAll('[data-stories]');
		if (!roots.length) return;

		roots.forEach(function (root) {
			var heroes  = Array.prototype.slice.call(root.querySelectorAll('[data-stories-hero]'));
			var posters = Array.prototype.slice.call(root.querySelectorAll('[data-stories-select]'));
			var counter = root.querySelector('[data-stories-current]');
			if (!heroes.length || !posters.length) return;

			function activate(key) {
				heroes.forEach(function (h) {
					if (h.getAttribute('data-key') === key) {
						h.removeAttribute('hidden');
					} else {
						h.setAttribute('hidden', '');
					}
				});
				posters.forEach(function (p) {
					var isActive = p.getAttribute('data-stories-select') === key;
					p.setAttribute('data-active', isActive ? 'true' : 'false');
					p.setAttribute('aria-pressed', isActive ? 'true' : 'false');
					if (isActive && counter) {
						counter.textContent = p.getAttribute('data-index') || '1';
					}
				});
			}

			posters.forEach(function (p) {
				p.addEventListener('click', function () {
					activate(p.getAttribute('data-stories-select'));
				});
			});
		});
	}

	function initStoriesLightbox() {
		var lightbox = document.querySelector('[data-stories-lightbox]');
		if (!lightbox) return;

		var frame      = lightbox.querySelector('[data-stories-lightbox-frame]');
		var titleEl    = lightbox.querySelector('[data-stories-lightbox-title]');
		var closeBtns  = lightbox.querySelectorAll('[data-stories-lightbox-close]');
		var triggers   = document.querySelectorAll('[data-stories-play]');
		var lastFocused = null;

		function open(videoId, title) {
			if (!videoId) return;
			lastFocused = document.activeElement;
			frame.innerHTML =
				'<iframe class="stories__lightbox-iframe" src="https://www.youtube.com/embed/' +
				encodeURIComponent(videoId) +
				'?autoplay=1&rel=0&modestbranding=1" title="' +
				(title || 'Patient story') +
				'" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>';
			if (titleEl) titleEl.textContent = title || '';
			lightbox.removeAttribute('hidden');
			lightbox.setAttribute('data-open', 'true');
			document.body.classList.add('no-scroll');
			var firstClose = lightbox.querySelector('[data-stories-lightbox-close]:not(.stories__lightbox-backdrop)');
			if (firstClose) firstClose.focus();
		}

		function close() {
			lightbox.removeAttribute('data-open');
			lightbox.setAttribute('hidden', '');
			frame.innerHTML = '';
			document.body.classList.remove('no-scroll');
			if (lastFocused && typeof lastFocused.focus === 'function') {
				lastFocused.focus();
			}
		}

		triggers.forEach(function (btn) {
			btn.addEventListener('click', function () {
				var id = btn.getAttribute('data-stories-play');
				if (!id) return; // No video set yet — bail silently.
				open(id, btn.getAttribute('data-story-title') || '');
			});
		});

		closeBtns.forEach(function (b) {
			b.addEventListener('click', close);
		});

		document.addEventListener('keydown', function (e) {
			if (e.key === 'Escape' && lightbox.getAttribute('data-open') === 'true') {
				close();
			}
		});
	}

	function initCarousels() {
		var carousels = document.querySelectorAll('[data-carousel]');
		if (!carousels.length) return;

		carousels.forEach(function (root) {
			var track = root.querySelector('[data-carousel-track]');
			var prev  = root.querySelector('[data-carousel-prev]');
			var next  = root.querySelector('[data-carousel-next]');
			if (!track) return;

			function step() {
				var card = track.firstElementChild;
				if (!card) return Math.round(track.clientWidth * 0.8);
				var styles = getComputedStyle(track);
				var gap = parseFloat(styles.columnGap || styles.gap) || 24;
				return card.getBoundingClientRect().width + gap;
			}

			function update() {
				if (!prev || !next) return;
				var max = track.scrollWidth - track.clientWidth - 2;
				prev.disabled = track.scrollLeft <= 2;
				next.disabled = track.scrollLeft >= max;
			}

			if (prev) {
				prev.addEventListener('click', function () {
					track.scrollBy({ left: -step(), behavior: 'smooth' });
				});
			}
			if (next) {
				next.addEventListener('click', function () {
					track.scrollBy({ left: step(), behavior: 'smooth' });
				});
			}

			track.addEventListener('scroll', update, { passive: true });
			window.addEventListener('resize', update);
			update();
		});
	}

	ready(function () {
		initMobileNav();
		initLangSwitch();
		initStickyHeader();
		initServicesTabs();
		initPatientStories();
		initStoriesLightbox();
		initCarousels();
	});
})();
