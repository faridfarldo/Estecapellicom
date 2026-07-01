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

	function initHeroCarousel() {
		var root = document.querySelector('[data-hero-carousel]');
		if (!root) return;
		var track = root.querySelector('[data-hero-track]');
		var slides = Array.prototype.slice.call(root.querySelectorAll('[data-hero-slide]'));
		var dots = Array.prototype.slice.call(root.querySelectorAll('[data-hero-dot]'));
		if (!track || slides.length < 2) return;

		var index = 0;
		var timer = null;
		var DELAY = 7000;

		var nextThumb = root.querySelector('[data-hero-next-thumb]');
		var prevThumb = root.querySelector('[data-hero-prev-thumb]');
		function thumbAt(i) {
			var s = slides[(i + slides.length) % slides.length];
			return s ? (s.getAttribute('data-hero-thumb') || '') : '';
		}
		function setThumb(el, url) {
			if (!el) return;
			if (url) { el.style.backgroundImage = 'url("' + url + '")'; }
		}

		// ── Stop hero videos when the slide changes ──
		// Changing slides must not leave a video playing: the Exosome/VITA video
		// (which has sound) is closed, and the women background video plays only
		// while its own slide is on screen.
		var splitStages = root.querySelectorAll('[data-split-stage]');
		var wmnVideo = root.querySelector('.hero-wmn__video');
		var wmnSlideIndex = -1;
		slides.forEach(function (s, i) { if (s.querySelector('.hero-wmn__bgvideo')) { wmnSlideIndex = i; } });

		function stopHeroMedia(activeIndex) {
			// Close any opened Exosome/VITA split video and collapse the split.
			root.querySelectorAll('[data-split-video]').forEach(function (h) { h.innerHTML = ''; });
			root.querySelectorAll('[data-split-panel]').forEach(function (p) { p.setAttribute('data-open', 'false'); });
			root.querySelectorAll('[data-split-toggle]').forEach(function (t) { t.setAttribute('aria-pressed', 'false'); });
			splitStages.forEach(function (st) { st.setAttribute('data-split-active', ''); });
			// Women background video: a self-hosted muted <video> — play it only on
			// its own slide, pause otherwise. Muted playback is always allowed to
			// autoplay, so this is clean (no chrome) and reliable on mobile.
			if (wmnVideo) {
				if (activeIndex === wmnSlideIndex) {
					wmnVideo.muted = true; // some browsers need this set explicitly
					var p = wmnVideo.play();
					if (p && p.catch) { p.catch(function () {}); }
				} else {
					wmnVideo.pause();
				}
			}
		}

		function go(n) {
			index = (n + slides.length) % slides.length;
			track.style.transform = 'translateX(' + (-index * 100) + '%)';
			dots.forEach(function (d, i) { d.setAttribute('aria-selected', i === index ? 'true' : 'false'); });
			slides.forEach(function (s, i) { s.setAttribute('aria-hidden', i === index ? 'false' : 'true'); });
			// Arrows preview the slide they lead to (mini thumbnail).
			setThumb(nextThumb, thumbAt(index + 1));
			setThumb(prevThumb, thumbAt(index - 1));
			// Stop/gate hero videos so nothing keeps playing after the switch.
			stopHeroMedia(index);
		}
		function next() { go(index + 1); }
		function prev() { go(index - 1); }
		// Auto-advance is intentionally DISABLED: the hero stays on slide 1 and the
		// visitor moves between slides only via the arrows/dots. (The expert slide
		// rotates its own patient photos internally — see initHeroResults.)
		function start() { stop(); }
		function stop() { if (timer) { clearInterval(timer); timer = null; } }

		var nextBtn = root.querySelector('[data-hero-next]');
		var prevBtn = root.querySelector('[data-hero-prev]');
		if (nextBtn) nextBtn.addEventListener('click', function () { next(); start(); });
		if (prevBtn) prevBtn.addEventListener('click', function () { prev(); start(); });
		dots.forEach(function (d) {
			d.addEventListener('click', function () { go(Number(d.getAttribute('data-hero-dot'))); start(); });
		});

		// Pause auto-advance while the visitor is interacting.
		root.addEventListener('mouseenter', stop);
		root.addEventListener('mouseleave', start);
		root.addEventListener('focusin', stop);
		root.addEventListener('focusout', start);

		go(0);
		start();
	}

	/*
	 * Expert hero slide: rotate through the real patient before/after sets every
	 * 3s. The big "after" photo and the two "before" thumbnails advance together
	 * so each frame shows one patient. This runs independently of (and instead of)
	 * any hero auto-advance — only the photos inside this one slide cycle.
	 */
	function initHeroResults() {
		var root = document.querySelector('[data-hero-results]');
		if (!root) return;
		var data;
		try { data = JSON.parse(root.getAttribute('data-hero-results') || '[]'); } catch (e) { return; }
		if (!data || data.length < 2) return;

		var after = root.querySelector('[data-result-after]');
		var b1 = root.querySelector('[data-result-b1]');
		var b2 = root.querySelector('[data-result-b2]');
		if (!after || !b1 || !b2) return;

		// Preload every set so swaps are instant (no flash on first cycle).
		data.forEach(function (p) {
			['after', 'b1', 'b2'].forEach(function (k) { if (p[k]) { var im = new Image(); im.src = p[k]; } });
		});

		var i = 0;
		function show(idx) {
			i = (idx + data.length) % data.length;
			if (data[i].after) after.src = data[i].after;
			if (data[i].b1) b1.src = data[i].b1;
			if (data[i].b2) b2.src = data[i].b2;
			after.style.objectPosition = data[i].pos || 'center 20%';
		}

		var DELAY = 3500;
		var timer = setInterval(function () { show(i + 1); }, DELAY);
		function restart() { clearInterval(timer); timer = setInterval(function () { show(i + 1); }, DELAY); }

		var prev = root.querySelector('[data-result-prev]');
		var next = root.querySelector('[data-result-next]');
		if (prev) prev.addEventListener('click', function () { show(i - 1); restart(); });
		if (next) next.addEventListener('click', function () { show(i + 1); restart(); });
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
					// Played from a click (a user gesture), so autoplay WITH sound is
					// allowed; native YouTube controls give play/pause/volume/fullscreen.
					var src = 'https://www.youtube.com/embed/' + encodeURIComponent(id) +
						'?autoplay=1&rel=0&modestbranding=1&playsinline=1';
					holder.innerHTML = '<iframe src="' + src + '" title="" ' +
						'allow="autoplay; encrypted-media; picture-in-picture; fullscreen" allowfullscreen></iframe>';
				} else {
					holder.innerHTML = ''; // stops playback
				}
			}

			function activate(key) {
				// Toggle: clicking the already-open method (or clicking outside its
				// video, which lands on its full-panel button) closes it back to the
				// split. Otherwise open the clicked one and close the other.
				var opening = stage.getAttribute('data-split-active') !== key;
				stage.setAttribute('data-split-active', opening ? key : '');
				toggles.forEach(function (t) {
					var on = opening && t.getAttribute('data-split-toggle') === key;
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
				// Click only — playing with sound needs a real user gesture (focus
				// isn't one, and would get the audio blocked/muted).
				t.addEventListener('click', function () { activate(key); });
			});

			// The video now fills its wedge, covering the full-panel toggle, so a
			// dedicated close button (sat above the iframe) returns to the split.
			stage.querySelectorAll('[data-split-close]').forEach(function (c) {
				c.addEventListener('click', function (e) {
					e.stopPropagation();
					activate(c.getAttribute('data-split-close'));
				});
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

	function initHomeBaGallery() {
		var roots = document.querySelectorAll('[data-hba]');
		if (!roots.length) return;

		roots.forEach(function (root) {
			var board   = root.querySelector('[data-hba-board]');
			var viewer  = root.querySelector('[data-hba-viewer]');
			var mainImg = root.querySelector('[data-hba-main]');
			var thumbs  = Array.prototype.slice.call(root.querySelectorAll('[data-hba-thumb]'));
			if (!board || !viewer || !mainImg) return;

			function setMain(src, alt) {
				mainImg.src = src;
				mainImg.alt = alt || '';
				thumbs.forEach(function (t) {
					t.setAttribute('aria-current', t.getAttribute('data-hba-src') === src ? 'true' : 'false');
				});
			}

			function openViewer(src, alt) {
				setMain(src, alt);
				board.hidden = true;
				viewer.hidden = false;
			}

			function closeViewer() {
				viewer.hidden = true;
				board.hidden = false;
			}

			root.querySelectorAll('[data-hba-open]').forEach(function (btn) {
				btn.addEventListener('click', function () {
					openViewer(btn.getAttribute('data-hba-src'), btn.getAttribute('data-hba-alt'));
				});
			});

			thumbs.forEach(function (t) {
				t.addEventListener('click', function () {
					setMain(t.getAttribute('data-hba-src'), t.getAttribute('data-hba-alt'));
				});
			});

			var back = root.querySelector('[data-hba-back]');
			if (back) back.addEventListener('click', closeViewer);
		});
	}

	function initHairAnalysisLab() {
		var root = document.querySelector('[data-hal]');
		if (!root) return;

		var options = root.querySelector('[data-hal-options]');
		var forms   = Array.prototype.slice.call(root.querySelectorAll('[data-hal-form]'));
		if (!options || !forms.length) return;

		function show(key) {
			options.hidden = key !== null;
			forms.forEach(function (f) {
				f.hidden = f.getAttribute('data-hal-form') !== key;
			});
		}

		root.querySelectorAll('[data-hal-pick]').forEach(function (btn) {
			btn.addEventListener('click', function () {
				show(btn.getAttribute('data-hal-pick'));
			});
		});

		root.querySelectorAll('[data-hal-back]').forEach(function (btn) {
			btn.addEventListener('click', function () {
				show(null);
			});
		});

		// Interactive scalp-zone picker (self-assessment form).
		var zones = Array.prototype.slice.call(root.querySelectorAll('[data-hal-zone]'));
		if (zones.length) {
			var field   = root.querySelector('[data-hal-zones]');
			var summary = root.querySelector('[data-hal-summary]');
			var list    = root.querySelector('[data-hal-zonelist]');
			var selected = [];

			function renderZones() {
				if (field) field.value = selected.join(', ');
				if (summary) summary.classList.toggle('has-selection', selected.length > 0);
				if (list) {
					list.textContent = '';
					selected.forEach(function (name) {
						var pill = document.createElement('span');
						pill.className = 'hal__zonepill';
						pill.textContent = name;
						list.appendChild(pill);
					});
				}
			}

			zones.forEach(function (zone) {
				zone.addEventListener('click', function () {
					var name    = zone.getAttribute('data-hal-zone');
					var related = root.querySelectorAll('[data-hal-zone="' + name + '"]');
					var idx     = selected.indexOf(name);
					if (idx > -1) {
						selected.splice(idx, 1);
						related.forEach(function (r) { r.classList.remove('is-active'); });
					} else {
						selected.push(name);
						related.forEach(function (r) { r.classList.add('is-active'); });
					}
					renderZones();
				});
			});
		}
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
					var key = p.getAttribute('data-stories-select');
					activate(key);
					// On stacked (mobile) layouts the playlist sits BELOW the video,
					// so jump back up to the now-updated video if it isn't in view.
					var activeHero = null;
					heroes.forEach(function (h) { if (h.getAttribute('data-key') === key) { activeHero = h; } });
					if (activeHero) {
						var poster = activeHero.querySelector('.stories__hero-poster') || activeHero;
						var rect = poster.getBoundingClientRect();
						if (rect.top < 84 || rect.bottom > window.innerHeight) {
							poster.scrollIntoView({ behavior: 'smooth', block: 'center' });
						}
					}
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

	function initFacilitiesLightbox() {
		var lightbox = document.querySelector('[data-facilities-lightbox]');
		if (!lightbox) return;

		var imgEl       = lightbox.querySelector('[data-facilities-lightbox-img]');
		var capEl       = lightbox.querySelector('[data-facilities-lightbox-caption]');
		var closeBtns   = lightbox.querySelectorAll('[data-facilities-lightbox-close]');
		var triggers    = document.querySelectorAll('[data-facilities-zoom]');
		var lastFocused = null;

		function open(src, caption) {
			if (!src) return;
			lastFocused = document.activeElement;
			imgEl.setAttribute('src', src);
			imgEl.setAttribute('alt', caption || '');
			if (capEl) {
				capEl.textContent = caption || '';
				capEl.hidden = !caption;
			}
			lightbox.removeAttribute('hidden');
			lightbox.setAttribute('data-open', 'true');
			document.body.classList.add('no-scroll');
			var firstClose = lightbox.querySelector('.facilities__lightbox-close');
			if (firstClose) firstClose.focus();
		}

		function close() {
			lightbox.removeAttribute('data-open');
			lightbox.setAttribute('hidden', '');
			imgEl.setAttribute('src', '');
			document.body.classList.remove('no-scroll');
			if (lastFocused && typeof lastFocused.focus === 'function') { lastFocused.focus(); }
		}

		triggers.forEach(function (btn) {
			btn.addEventListener('click', function () {
				open(btn.getAttribute('data-facilities-zoom'), btn.getAttribute('data-caption') || '');
			});
		});
		closeBtns.forEach(function (b) { b.addEventListener('click', close); });
		document.addEventListener('keydown', function (e) {
			if (e.key === 'Escape' && lightbox.getAttribute('data-open') === 'true') { close(); }
		});
	}

	function initImageLightbox() {
		var lightbox = document.querySelector('[data-img-lightbox]');
		if (!lightbox) return;

		var imgEl       = lightbox.querySelector('[data-img-lightbox-img]');
		var closeBtns   = lightbox.querySelectorAll('[data-img-lightbox-close]');
		var lastFocused = null;

		function open(src, alt) {
			if (!src) return;
			lastFocused = document.activeElement;
			imgEl.setAttribute('src', src);
			imgEl.setAttribute('alt', alt || '');
			lightbox.removeAttribute('hidden');
			lightbox.setAttribute('data-open', 'true');
			document.body.classList.add('no-scroll');
			var firstClose = lightbox.querySelector('.img-lightbox__close');
			if (firstClose) firstClose.focus();
		}
		function close() {
			lightbox.removeAttribute('data-open');
			lightbox.setAttribute('hidden', '');
			imgEl.setAttribute('src', '');
			document.body.classList.remove('no-scroll');
			if (lastFocused && typeof lastFocused.focus === 'function') { lastFocused.focus(); }
		}

		// Delegated so it also covers images rendered inside carousels/galleries.
		document.addEventListener('click', function (e) {
			var trigger = e.target.closest('[data-img-zoom]');
			if (!trigger) return;
			e.preventDefault();
			var src = trigger.getAttribute('data-img-zoom');
			if (!src) {
				var innerImg = trigger.querySelector('img');
				src = innerImg ? innerImg.currentSrc || innerImg.src : '';
			}
			open(src, trigger.getAttribute('data-caption') || '');
		});
		closeBtns.forEach(function (b) { b.addEventListener('click', close); });
		document.addEventListener('keydown', function (e) {
			if (e.key === 'Escape' && lightbox.getAttribute('data-open') === 'true') { close(); }
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

	/*
	 * Lead popup: opens on any "Free Consultation"/Contact CTA (or [data-lead-popup]),
	 * submits over AJAX so the visitor stays on the page, and records which page +
	 * campaign the lead came from. Also tops up UTM hidden fields on every lead form.
	 */
	function initLeadPopup() {
		var cfg = window.EstecapelliLead || {};
		var i18n = cfg.i18n || {};

		// Fill UTM hidden fields on every lead form from the current URL.
		var params = new URLSearchParams(window.location.search);
		document.querySelectorAll('input[name^="utm_"]').forEach(function (input) {
			if (input.value) return;
			var v = params.get(input.name);
			if (v) { input.value = v; }
		});

		var popup = document.getElementById('lead-popup');
		if (!popup) return;

		var form        = popup.querySelector('form');
		var feedback    = popup.querySelector('.lead-popup__feedback');
		var submit      = popup.querySelector('.lead-popup__submit');
		var submitLabel = popup.querySelector('.lead-popup__submit-label');
		var lastFocus   = null;

		function pathOf(url) {
			try { return new URL(url, window.location.origin).pathname.replace(/\/+$/, ''); }
			catch (e) { return ''; }
		}

		function isContactLink(a) {
			if (!a || a.hasAttribute('data-no-popup')) return false;
			// Only call-to-action buttons open the popup. Plain navigation links
			// (the header menu, the footer sitemap) still go to the Contact page.
			if (!a.classList.contains('btn')) return false;
			var href = a.getAttribute('href') || '';
			if (!href || href.charAt(0) === '#') return false;
			var paths = cfg.contactPaths || ['/en/contact', '/contact'];
			var p = pathOf(a.href);
			return paths.some(function (c) { return p === c.replace(/\/+$/, ''); });
		}

		function openPopup(trigger) {
			lastFocus = trigger || document.activeElement;
			var u = form.querySelector('[name="lead_page_url"]');
			var t = form.querySelector('[name="lead_page_title"]');
			if (u) { u.value = window.location.href; }
			if (t) { t.value = document.title; }
			popup.hidden = false;
			document.body.classList.add('no-scroll');
			requestAnimationFrame(function () {
				popup.classList.add('is-open');
				var first = form.querySelector('input, textarea');
				if (first) { first.focus(); }
			});
		}

		function closePopup() {
			popup.classList.remove('is-open');
			document.body.classList.remove('no-scroll');
			setTimeout(function () { popup.hidden = true; }, 200);
			if (lastFocus && lastFocus.focus) { lastFocus.focus(); }
		}

		document.addEventListener('click', function (e) {
			var trigger = e.target.closest('[data-lead-popup]');
			var link    = e.target.closest('a');
			if (trigger) {
				e.preventDefault();
				openPopup(trigger);
			} else if (link && !popup.contains(link) && isContactLink(link)) {
				e.preventDefault();
				openPopup(link);
			}
		});

		popup.querySelectorAll('[data-lead-close]').forEach(function (el) {
			el.addEventListener('click', closePopup);
		});
		document.addEventListener('keydown', function (e) {
			if (e.key === 'Escape' && popup.classList.contains('is-open')) { closePopup(); }
		});

		function showError(msg) {
			if (!feedback) return;
			feedback.textContent = msg || i18n.error || 'Something went wrong. Please try again or use WhatsApp.';
			feedback.classList.add('is-error');
			feedback.hidden = false;
		}

		// AJAX submit (phone-intl.js has already prefixed the dial code by now).
		if (!cfg.ajax) return;
		form.addEventListener('submit', function (e) {
			e.preventDefault();
			if (feedback) { feedback.hidden = true; feedback.classList.remove('is-error'); }
			var nameField = form.querySelector('[name="lead_name"]');
			if (nameField && !nameField.value.trim()) { nameField.focus(); return; }

			var data = new FormData(form);
			data.append('action', 'estecapelli_lead');
			data.append('nonce', cfg.nonce || '');

			submit.disabled = true;
			var original = submitLabel ? submitLabel.textContent : '';
			if (submitLabel) { submitLabel.textContent = i18n.sending || 'Sending…'; }

			fetch(cfg.ajax, { method: 'POST', body: data, credentials: 'same-origin' })
				.then(function (r) { return r.json().catch(function () { return { success: false }; }); })
				.then(function (res) {
					if (res && res.success) {
						var msg = (res.data && res.data.message) ? res.data.message : (i18n.thanks || 'Thank you!');
						form.innerHTML = '<div class="lead-popup__success" role="status">' +
							'<span class="lead-popup__success-mark" aria-hidden="true"></span>' +
							'<strong></strong></div>';
						form.querySelector('strong').textContent = msg;
					} else {
						showError(res && res.data && res.data.message);
					}
				})
				.catch(function () { showError(); })
				.finally(function () {
					submit.disabled = false;
					if (submitLabel) { submitLabel.textContent = original; }
				});
		});
	}

	/*
	 * Trust reel: an auto-rotating vertical slot reel of stats. Shows three rows —
	 * the centre stat sharp, the previous/next ones blurred above and below — and
	 * advances one row at a time, looping seamlessly. The list is bracketed with
	 * clones (last item before the first; first + second after the last) so a real
	 * neighbour always fills the top and bottom rows. Skipped for reduced motion.
	 */
	function initTrustReel() {
		var vp = document.querySelector('[data-reel]');
		if (!vp) return;
		var track = vp.querySelector('[data-reel-track]');
		if (!track) return;
		var reduce = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;
		if (reduce) return;

		var originals = Array.prototype.slice.call(track.children);
		var N = originals.length;
		if (N < 2) return;

		var ROWS = 1.4; // tight window — neighbours fold away on the drum
		var rowH = originals[0].offsetHeight;
		if (!rowH) return;
		var vpH = rowH * ROWS;

		// [cloneLast, item0 .. itemN-1, cloneFirst, cloneSecond]
		track.insertBefore(originals[N - 1].cloneNode(true), originals[0]);
		track.appendChild(originals[0].cloneNode(true));
		track.appendChild(originals[1].cloneNode(true));
		var all = Array.prototype.slice.call(track.children);

		vp.style.setProperty('--reel-h', vpH + 'px');
		vp.classList.add('is-reel');

		var i = 1, resetting = false; // centre the first real item (index 1)

		function center(idx, animate) {
			var y = Math.round(vpH / 2 - rowH / 2 - idx * rowH);
			track.style.transition = animate ? 'transform 0.7s cubic-bezier(0.5, 0, 0.2, 1)' : 'none';
			track.style.transform = 'translateY(' + y + 'px)';
			all.forEach(function (el) { el.classList.remove('is-active', 'is-prev', 'is-next'); });
			if (all[idx]) { all[idx].classList.add('is-active'); }
			if (all[idx - 1]) { all[idx - 1].classList.add('is-prev'); }
			if (all[idx + 1]) { all[idx + 1].classList.add('is-next'); }
		}

		center(1, false);

		setInterval(function () {
			if (resetting) return;
			i++;
			center(i, true);
			if (i > N) {
				resetting = true;
				// Landed on the cloned first item — jump back to the real first
				// item (an identical frame) without a transition.
				setTimeout(function () { i = 1; center(1, false); resetting = false; }, 720);
			}
		}, 2600);

		var raf;
		window.addEventListener('resize', function () {
			cancelAnimationFrame(raf);
			raf = requestAnimationFrame(function () {
				rowH = originals[0].offsetHeight || rowH;
				vpH = rowH * ROWS;
				vp.style.setProperty('--reel-h', vpH + 'px');
				center(i > N ? 1 : i, false);
			});
		});
	}

	ready(function () {
		initMobileNav();
		initLangSwitch();
		initStickyHeader();
		initSignatureCards();
		initHeroCarousel();
		initHeroResults();
		initHeroSplit();
		initHomeBaGallery();
		initHairAnalysisLab();
		initServicesTabs();
		initCategoryTabs();
		initPatientStories();
		initStoriesLightbox();
		initFacilitiesLightbox();
		initImageLightbox();
		initCarousels();
		initStepbooks();
		initCopyLink();
		initLeadPopup();
		initTrustReel();
	});
})();
