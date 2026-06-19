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
				// The newly shown panel had zero width while hidden — nudge the
				// carousels to recompute their nav (prev/next) enabled state.
				window.dispatchEvent(new Event('resize'));
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

	function initCategoryTabs() {
		var roots = document.querySelectorAll('[data-cats]');
		if (!roots.length) return;

		roots.forEach(function (root) {
			var tabs   = Array.prototype.slice.call(root.querySelectorAll('[data-cats-tab]'));
			var panels = Array.prototype.slice.call(root.querySelectorAll('[data-cats-panel]'));
			if (tabs.length < 2) return;

			function activate(idx) {
				tabs.forEach(function (t, i) {
					var selected = i === idx;
					t.setAttribute('aria-selected', selected ? 'true' : 'false');
					t.setAttribute('tabindex', selected ? '0' : '-1');
				});
				panels.forEach(function (p, i) {
					if (i === idx) { p.removeAttribute('hidden'); }
					else { p.setAttribute('hidden', ''); }
				});
			}

			tabs.forEach(function (tab, idx) {
				tab.addEventListener('click', function () { activate(idx); });
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

	function initHeroSplit() {
		var stages = document.querySelectorAll('[data-split-stage]');
		if (!stages.length) return;

		stages.forEach(function (stage) {
			var toggles = Array.prototype.slice.call(stage.querySelectorAll('[data-split-toggle]'));
			if (!toggles.length) return;

			function setVideo(panel, on) {
				var holder = panel.querySelector('[data-split-video]');
				if (!holder) return;
				if (on) {
					if (holder.querySelector('iframe')) return; // already playing
					var id = holder.getAttribute('data-video-id');
					if (!id) return;
					var src = 'https://www.youtube.com/embed/' + encodeURIComponent(id) +
						'?autoplay=1&mute=1&loop=1&playlist=' + encodeURIComponent(id) +
						'&controls=0&modestbranding=1&rel=0&playsinline=1';
					holder.innerHTML = '<iframe src="' + src + '" title="" tabindex="-1" ' +
						'allow="autoplay; encrypted-media; picture-in-picture" allowfullscreen></iframe>';
				} else {
					holder.innerHTML = ''; // stops playback
				}
			}

			function activate(key) {
				stage.setAttribute('data-split-active', key);
				toggles.forEach(function (t) {
					var on = t.getAttribute('data-split-toggle') === key;
					t.setAttribute('aria-pressed', on ? 'true' : 'false');
					var panel = t.closest('[data-split-panel]');
					if (panel) {
						panel.setAttribute('data-open', on ? 'true' : 'false');
						setVideo(panel, on);
					}
				});
			}

			toggles.forEach(function (t) {
				var key = t.getAttribute('data-split-toggle');
				t.addEventListener('click', function () { activate(key); });
				// Keyboard users land on a panel via Tab; reveal it on focus too.
				t.addEventListener('focus', function () { activate(key); });
			});
		});
	}

	function initSignatureCards() {
		var cards = document.querySelectorAll('.signature__card');
		if (!cards.length) return;

		cards.forEach(function (card) {
			var inner = card.querySelector('.signature__inner');
			if (!inner) return;

			function toggle() {
				// First interaction hands control to the visitor: the card stops
				// auto-rotating and from now on each click flips it front/back.
				card.classList.add('is-controlled');
				card.classList.toggle('is-flipped');
			}

			inner.addEventListener('click', function (e) {
				// Let real links/buttons (the back-face CTAs) behave normally.
				if (e.target.closest('a, button')) return;
				toggle();
			});

			inner.addEventListener('keydown', function (e) {
				if (e.key === 'Enter' || e.key === ' ' || e.key === 'Spacebar') {
					if (e.target.closest('a, button')) return;
					e.preventDefault();
					toggle();
				}
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

	function initStepbooks() {
		var books = document.querySelectorAll('[data-stepbook]');
		if (!books.length) return;

		books.forEach(function (root) {
			var track = root.querySelector('[data-stepbook-track]');
			if (!track) return;

			var pages = Array.prototype.slice.call(track.children);
			var tabs  = Array.prototype.slice.call(root.querySelectorAll('[data-stepbook-tab]'));
			var prev  = root.querySelector('[data-stepbook-prev]');
			var next  = root.querySelector('[data-stepbook-next]');
			var curEl = root.querySelector('[data-stepbook-current]');
			var fill  = root.querySelector('[data-stepbook-progress]');
			var total = pages.length;
			var index = 0;

			if (!total) return;

			function stepWidth() {
				var styles = getComputedStyle(track);
				var gap = parseFloat(styles.columnGap || styles.gap) || 0;
				return pages[0].getBoundingClientRect().width + gap;
			}

			// Size the deck to the active page so short steps don't leave a gap.
			function setHeight() {
				var active = pages[index];
				if (active) { track.style.height = active.offsetHeight + 'px'; }
			}

			function render() {
				tabs.forEach(function (t, i) {
					var on = i === index;
					t.classList.toggle('is-active', on);
					if (on) { t.setAttribute('aria-current', 'step'); }
					else { t.removeAttribute('aria-current'); }
				});
				pages.forEach(function (p, i) { p.classList.toggle('is-active', i === index); });
				if (curEl) { curEl.textContent = String(index + 1); }
				if (fill) { fill.style.width = ((index + 1) / total) * 100 + '%'; }
				if (prev) { prev.disabled = index <= 0; }
				if (next) { next.disabled = index >= total - 1; }
				setHeight();
			}

			function goTo(i, smooth) {
				index = Math.max(0, Math.min(total - 1, i));
				track.scrollTo({ left: index * stepWidth(), behavior: smooth === false ? 'auto' : 'smooth' });
				render();
			}

			tabs.forEach(function (t, i) {
				t.addEventListener('click', function () { goTo(i); });
			});
			if (prev) { prev.addEventListener('click', function () { goTo(index - 1); }); }
			if (next) { next.addEventListener('click', function () { goTo(index + 1); }); }

			var raf;
			track.addEventListener('scroll', function () {
				if (raf) { cancelAnimationFrame(raf); }
				raf = requestAnimationFrame(function () {
					var i = Math.round(track.scrollLeft / stepWidth());
					if (i !== index) { index = Math.max(0, Math.min(total - 1, i)); render(); }
				});
			}, { passive: true });

			window.addEventListener('resize', function () { goTo(index, false); });

			// Recompute height once images finish loading (they change page height).
			track.querySelectorAll('img').forEach(function (img) {
				if (!img.complete) {
					img.addEventListener('load', setHeight);
					img.addEventListener('error', setHeight);
				}
			});
			window.addEventListener('load', setHeight);

			render();
		});
	}

	function initCopyLink() {
		document.querySelectorAll('[data-copy-link]').forEach(function (btn) {
			btn.addEventListener('click', function () {
				var url = btn.getAttribute('data-copy-link');
				var done = function () {
					btn.classList.add('is-copied');
					setTimeout(function () { btn.classList.remove('is-copied'); }, 1600);
				};
				if (navigator.clipboard && navigator.clipboard.writeText) {
					navigator.clipboard.writeText(url).then(done).catch(done);
				} else {
					var t = document.createElement('textarea');
					t.value = url;
					document.body.appendChild(t);
					t.select();
					try { document.execCommand('copy'); } catch (e) {}
					document.body.removeChild(t);
					done();
				}
			});
		});
	}

	ready(function () {
		initMobileNav();
		initLangSwitch();
		initStickyHeader();
		initSignatureCards();
		initHeroSplit();
		initServicesTabs();
		initCategoryTabs();
		initPatientStories();
		initStoriesLightbox();
		initCarousels();
		initStepbooks();
		initCopyLink();
	});
})();
