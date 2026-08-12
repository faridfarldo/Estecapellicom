/*
 * Estecapelli — main script.
 *
 * 1. Mobile nav drawer (hamburger toggle, escape, link-click close, viewport)
 * 2. Language switcher dropdown (click toggle, click-outside, escape)
 * 3. Sticky-header shadow state on scroll
 */

(function () {
	'use strict';

	/*
	 * Announce something worth measuring. assets/js/analytics.js listens for
	 * these and is the only file that knows GA4 exists — this one stays a UI
	 * controller. Dispatching into the void is harmless, so tracking can be
	 * switched off without touching any of the code below.
	 */
	function emit(name, detail) {
		document.dispatchEvent(new CustomEvent('estecapelli:' + name, { detail: detail || {} }));
	}

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
					// Which treatment family the homepage audience actually opens.
					emit('tab', {
						name: (tab.textContent || '').replace(/\s+/g, ' ').trim(),
						group: 'services_home'
					});
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

	/*
	 * Scrollable tab strips (e.g. homepage Before & After technique tabs).
	 * The pill row scrolls horizontally; translated labels can overflow the
	 * frame. This reveals prev/next edge arrows only when the strip actually
	 * overflows, scrolls on click, disables an arrow at each end, and keeps the
	 * active/focused tab in view — on mobile and desktop alike.
	 */
	function initTabScrollers() {
		var rows = document.querySelectorAll('[data-tabscroll]');
		if (!rows.length) return;

		rows.forEach(function (row) {
			var strip = row.querySelector('[data-tabscroll-strip]');
			var prev  = row.querySelector('[data-tabscroll-prev]');
			var next  = row.querySelector('[data-tabscroll-next]');
			if (!strip) return;

			function step() {
				return Math.max(Math.round(strip.clientWidth * 0.7), 140);
			}

			function update() {
				var max = strip.scrollWidth - strip.clientWidth - 2;
				row.classList.toggle('is-overflowing', max > 2);
				if (prev) prev.disabled = strip.scrollLeft <= 2;
				if (next) next.disabled = strip.scrollLeft >= max;
			}

			function bringIntoView(tab) {
				if (!tab) return;
				var target = tab.offsetLeft - (strip.clientWidth - tab.offsetWidth) / 2;
				strip.scrollTo({ left: Math.max(0, target), behavior: 'smooth' });
			}

			if (prev) {
				prev.addEventListener('click', function () {
					strip.scrollBy({ left: -step(), behavior: 'smooth' });
				});
			}
			if (next) {
				next.addEventListener('click', function () {
					strip.scrollBy({ left: step(), behavior: 'smooth' });
				});
			}

			// Keep the tab the visitor lands on visible — covers clicks and the
			// ArrowLeft/ArrowRight/Home/End keyboard nav (initServicesTabs focuses
			// the new tab, which fires focusin).
			strip.addEventListener('focusin', function (e) {
				var tab = e.target.closest('[data-services-tab]');
				if (tab) bringIntoView(tab);
			});
			strip.addEventListener('click', function (e) {
				var tab = e.target.closest('[data-services-tab]');
				if (tab) bringIntoView(tab);
			});

			strip.addEventListener('scroll', update, { passive: true });
			window.addEventListener('resize', update);
			// Web fonts can change label widths after first paint.
			if (document.fonts && document.fonts.ready) {
				document.fonts.ready.then(update).catch(function () {});
			}
			window.addEventListener('load', update);
			update();
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
		var viewport = root.querySelector('.hero-x__viewport');
		var slides = Array.prototype.slice.call(root.querySelectorAll('[data-hero-slide]'));
		var dots = Array.prototype.slice.call(root.querySelectorAll('[data-hero-dot]'));
		if (!track || slides.length < 2) return;

		// The viewport hugs the active slide's height (slides are top-aligned, not
		// stretched) so no slide leaves a black gap or pushes content off-screen.
		function syncHeight() {
			var active = slides[index];
			if (viewport && active) { viewport.style.height = active.offsetHeight + 'px'; }
		}

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
			syncHeight();
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

		// Re-measure after layout settles (fonts/images) and on resize/orientation.
		window.addEventListener('load', syncHeight);
		var rzTimer = null;
		window.addEventListener('resize', function () {
			if (rzTimer) clearTimeout(rzTimer);
			rzTimer = setTimeout(syncHeight, 150);
		});
		slides.forEach(function (s) {
			s.querySelectorAll('img').forEach(function (img) {
				if (!img.complete) { img.addEventListener('load', syncHeight, { once: true }); }
			});
		});

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

		// Warm only the set that is about to be shown. This used to preload every
		// set up front — a dozen patients x 3 photos — which fetched the lot on
		// page load and bypassed lazy loading entirely.
		function preloadSet(idx) {
			var p = data[(idx + data.length) % data.length];
			if (!p || p.__warmed) return;
			p.__warmed = true;
			['after', 'b1', 'b2'].forEach(function (k) { if (p[k]) { var im = new Image(); im.src = p[k]; } });
		}

		var i = 0;
		function show(idx) {
			i = (idx + data.length) % data.length;
			if (data[i].after) after.src = data[i].after;
			if (data[i].b1) b1.src = data[i].b1;
			if (data[i].b2) b2.src = data[i].b2;
			after.style.objectPosition = data[i].pos || 'center 20%';
			// Optional per-patient zoom (for photos whose aspect ~matches the box).
			after.style.transform = (data[i].scale && data[i].scale != 1) ? ('scale(' + data[i].scale + ')') : '';
			after.style.transformOrigin = data[i].origin || '50% 50%';
		}

		var DELAY = 3500;
		var timer = null;

		function tick() { show(i + 1); preloadSet(i + 1); }
		function restart() { clearInterval(timer); preloadSet(i + 1); timer = setInterval(tick, DELAY); }

		// Hold the rotation until the page has finished loading, then wait once
		// more before the first swap.
		//
		// This photo is the page's largest element, so every swap registers a new
		// Largest Contentful Paint. Rotating during load kept redefining LCP and
		// pushed it to 11s — it only stops updating once the visitor interacts,
		// which an audit never does. Starting late also keeps a dozen patient
		// photos from competing with the rest of the page for bandwidth.
		function beginRotation() {
			if (timer) return;
			preloadSet(i + 1);
			timer = setInterval(tick, DELAY);
		}
		function scheduleRotation() { setTimeout(beginRotation, 3000); }
		if ('complete' === document.readyState) scheduleRotation();
		else window.addEventListener('load', scheduleRotation, { once: true });

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
					// Played from a click (a user gesture), so autoplay with sound is
					// allowed. Privacy-enhanced mode avoids carrying a signed-in
					// viewer's caption preference into the embed. YouTube documents that
					// cc_lang_pref without cc_load_policy leaves captions off by default.
					// YouTube may still show its required title/channel overlay.
					var src = 'https://www.youtube-nocookie.com/embed/' + encodeURIComponent(id) +
						'?autoplay=1&rel=0&playsinline=1&controls=0&cc_lang_pref=en&iv_load_policy=3&fs=0&disablekb=1';
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

			// Rebuild the marquee's second half. The server sends one copy only —
			// halving the homepage's <img> tags — and the seamless -50%→0 loop needs
			// two, so clone it here. Must run BEFORE the [data-hba-open] binding
			// below so the clones get their click handlers too.
			var track = root.querySelector('[data-hba-track]');
			if (track && !track.classList.contains('is-looped')) {
				Array.prototype.slice.call(track.children).forEach(function (cell) {
					var clone = cell.cloneNode(true);
					clone.setAttribute('aria-hidden', 'true');
					clone.querySelectorAll('button').forEach(function (btn) {
						btn.setAttribute('tabindex', '-1');
						btn.setAttribute('aria-hidden', 'true');
					});
					track.appendChild(clone);
				});
				track.classList.add('is-looped');
			}

			// The unique ordered list of results (from the thumbnails).
			var slides = thumbs.map(function (t) {
				return { src: t.getAttribute('data-hba-src'), alt: t.getAttribute('data-hba-alt') };
			});
			var current = 0;

			function setMain(src, alt) {
				mainImg.src = src;
				mainImg.alt = alt || '';
				// Clicking the big image enlarges it in the global lightbox.
				mainImg.setAttribute('data-img-zoom', src);
				thumbs.forEach(function (t, i) {
					var on = t.getAttribute('data-hba-src') === src;
					t.setAttribute('aria-current', on ? 'true' : 'false');
					if (on) { current = i; }
				});
			}

			function go(i) {
				if (!slides.length) return;
				current = (i + slides.length) % slides.length;
				setMain(slides[current].src, slides[current].alt);
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

			var prev = root.querySelector('[data-hba-prev]');
			var next = root.querySelector('[data-hba-next]');
			if (prev) prev.addEventListener('click', function () { go(current - 1); });
			if (next) next.addEventListener('click', function () { go(current + 1); });

			var back = root.querySelector('[data-hba-back]');
			if (back) back.addEventListener('click', closeViewer);
		});
	}

	// Plain before/after scroll galleries: left/right arrows nudge the row.
	function initBAScroll() {
		var scrollers = document.querySelectorAll('[data-ba-scroll]');
		if (!scrollers.length) return;

		scrollers.forEach(function (sc) {
			var strip = sc.querySelector('[data-ba-strip]');
			var prev  = sc.querySelector('[data-ba-prev]');
			var next  = sc.querySelector('[data-ba-next]');
			if (!strip) return;

			function amount() { return Math.max(strip.clientWidth * 0.8, 220); }

			// How deep a visitor browses the results is one of the better
			// predictors of intent here, so report the running position rather
			// than a single "gallery used" flag.
			var section = sc.closest('section[class]');
			var gallery = section ? (section.className || '').split(/\s+/)[0] : 'before_after';
			var steps = 0;
			function report() {
				steps++;
				emit('before-after', { gallery: gallery, index: steps });
			}

			if (prev) prev.addEventListener('click', function () { report(); strip.scrollBy({ left: -amount(), behavior: 'smooth' }); });
			if (next) next.addEventListener('click', function () { report(); strip.scrollBy({ left: amount(), behavior: 'smooth' }); });

			// Click-and-drag to scroll with the mouse (touch/pen already scroll
			// natively). A drag past a few px is flagged so the click it ends on is
			// swallowed — otherwise letting go would open the lightbox.
			var down = false, moved = false, startX = 0, startLeft = 0;
			strip.addEventListener('pointerdown', function (e) {
				if (e.pointerType !== 'mouse' || e.button !== 0) return;
				down = true; moved = false; startX = e.clientX; startLeft = strip.scrollLeft;
			});
			strip.addEventListener('pointermove', function (e) {
				if (!down) return;
				var dx = e.clientX - startX;
				if (!moved && Math.abs(dx) > 4) { moved = true; strip.classList.add('is-dragging'); }
				if (moved) { strip.scrollLeft = startLeft - dx; e.preventDefault(); }
			});
			function endDrag() { down = false; strip.classList.remove('is-dragging'); }
			strip.addEventListener('pointerup', endDrag);
			strip.addEventListener('pointercancel', endDrag);
			strip.addEventListener('pointerleave', endDrag);
			strip.addEventListener('click', function (e) {
				if (moved) { e.preventDefault(); e.stopPropagation(); moved = false; }
			}, true);

			// Grey out an arrow at the corresponding end (skipped while the panel is
			// hidden and has no measurable width — re-checked on scroll/resize).
			function update() {
				if (!strip.clientWidth) return;
				var max = strip.scrollWidth - strip.clientWidth - 2;
				if (prev) prev.classList.toggle('is-off', strip.scrollLeft <= 0);
				if (next) next.classList.toggle('is-off', strip.scrollLeft >= max);
			}
			strip.addEventListener('scroll', update, { passive: true });
			window.addEventListener('resize', update);
			// Re-measure when this tab is revealed (its panel's [hidden] is removed).
			var panel = sc.closest('[data-services-panel]');
			if (panel && window.MutationObserver) {
				new MutationObserver(update).observe(panel, { attributes: true, attributeFilter: ['hidden'] });
			}
			update();
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
				// Which of the two routes into the lab a visitor takes — the AI
				// photo wizard or the manual self-assessment.
				emit('hair-lab', { action: 'select', mode: btn.getAttribute('data-hal-pick') });
				show(btn.getAttribute('data-hal-pick'));
			});
		});

		// Deep link: the header "Start AI Analysis" feature links to #ai-analysis
		// and should land directly on the photo-analysis step, not the chooser.
		function openFromHash() {
			if (window.location.hash === '#ai-analysis') {
				show('photos');
				root.scrollIntoView({ behavior: 'smooth', block: 'start' });
			}
		}
		window.addEventListener('hashchange', openFromHash);
		openFromHash();

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
				// The hidden field is the lead's message, so it carries a label
				// rather than a bare list the CRM would have to guess at.
				if (field) {
					var zonesLabel = field.getAttribute('data-hal-zones-label') || 'Selected areas';
					field.value = selected.length ? zonesLabel + ': ' + selected.join(', ') : '';
				}
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
					emit('hair-lab', { action: 'zones', count: selected.length });
					renderZones();
				});
			});
		}

		// Self-assessment form: submits over AJAX to the same lead endpoint as
		// the popup, so it reaches the inbox and the CRM through one path. The
		// classic POST it falls back to is handled server-side, which is what
		// keeps it working with JS off.
		var leadForm = root.querySelector('[data-hal-lead]');
		if (leadForm) {
			var cfg      = window.EstecapelliLead || {};
			var i18n     = cfg.i18n || {};
			var feedback = leadForm.querySelector('[data-hal-feedback]');
			var button   = leadForm.querySelector('.hal__submit');

			function say(message, isError) {
				if (!feedback) return;
				var serverErrors = window.EstecapelliLeadServerErrors || {};
				if (isError && message && serverErrors[message]) { message = serverErrors[message]; }
				feedback.textContent = message;
				feedback.classList.toggle('is-error', !!isError);
				feedback.hidden = false;
			}

			if (cfg.ajax) {
				leadForm.addEventListener('submit', function (e) {
					e.preventDefault();
					if (feedback) { feedback.hidden = true; }

					var data = new FormData(leadForm);
					data.append('action', 'estecapelli_lead');
					data.append('nonce', cfg.nonce || '');

					var label = button ? button.textContent : '';
					if (button) { button.disabled = true; button.textContent = i18n.sending || 'Sending…'; }

					fetch(cfg.ajax, { method: 'POST', body: data, credentials: 'same-origin' })
						.then(function (r) { return r.json().catch(function () { return { success: false }; }); })
						.then(function (res) {
							if (res && res.success) {
								var lang = leadForm.querySelector('[name="lead_lang"]');
								emit('lead-success', {
									location: 'hair_lab',
									treatment: 'hair-analysis',
									language: lang ? lang.value : ''
								});
								leadForm.hidden = true;
								say(i18n.thanks || ((res.data && res.data.message) ? res.data.message : 'Thank you!'), false);
								if (feedback) { feedback.parentNode.insertBefore(feedback, leadForm); }
							} else {
								var failure = (res && res.data && res.data.message) || 'unknown';
								emit('lead-error', { location: 'hair_lab', message: failure });
								say((res && res.data && res.data.message) || i18n.error || 'Something went wrong.', true);
							}
						})
						.catch(function () {
							emit('lead-error', { location: 'hair_lab', message: 'network_error' });
							say(i18n.error || 'Something went wrong.', true);
						})
						.finally(function () {
							if (button) { button.disabled = false; button.textContent = label; }
						});
				});
			}
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
					emit('story-open', { name: p.getAttribute('data-story-title') || key });
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

	// Set by initStoriesLightbox so other overlays can stop the video when they open.
	var closeStoriesVideo = function () {};

	function initStoriesLightbox() {
		var lightbox = document.querySelector('[data-stories-lightbox]');
		if (!lightbox) return;

		// The lightbox markup lives inside .stories, which sets `isolation: isolate`
		// (a stacking context). A fixed child can't paint above later sections from
		// there, so lift it to <body> — now its z-index wins page-wide.
		if (lightbox.parentNode !== document.body) {
			document.body.appendChild(lightbox);
		}

		var frame      = lightbox.querySelector('[data-stories-lightbox-frame]');
		var titleEl    = lightbox.querySelector('[data-stories-lightbox-title]');
		var closeBtns  = lightbox.querySelectorAll('[data-stories-lightbox-close]');
		var triggers   = document.querySelectorAll('[data-stories-play]');
		var lastFocused = null;

		function open(videoId, title) {
			if (!videoId) return;
			// The embed autoplays, so opening the lightbox IS the video start.
			// A YouTube iframe gives us nothing else without loading their API.
			emit('video', { action: 'start', title: title || videoId, provider: 'youtube' });
			lastFocused = document.activeElement;
			// Keep captions off by default and use privacy-enhanced embeds. YouTube
			// may still show its required title/channel overlay around playback.
			frame.innerHTML =
				'<iframe class="stories__lightbox-iframe" src="https://www.youtube-nocookie.com/embed/' +
				encodeURIComponent(videoId) +
				'?autoplay=1&rel=0&controls=0&fs=0&disablekb=1&iv_load_policy=3&cc_lang_pref=en&playsinline=1" title="' +
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
			if (lightbox.getAttribute('data-open') !== 'true') return;
			lightbox.removeAttribute('data-open');
			lightbox.setAttribute('hidden', '');
			frame.innerHTML = ''; // Removing the iframe stops playback/audio.
			document.body.classList.remove('no-scroll');
			if (lastFocused && typeof lastFocused.focus === 'function') {
				lastFocused.focus();
			}
		}
		closeStoriesVideo = close;

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

	/**
	 * Keep the hotel strip still until its eager images are fully decoded.
	 * Chromium can otherwise composite individual WebP frames as black while
	 * the parent is moving, then repaint them only when hover pauses the strip.
	 */
	/**
	 * Freeze every infinite animation while it is off screen.
	 *
	 * The two marquees (.home-ba__walltrack, .facilities__logos) translate
	 * images sideways through the viewport, which defeats lazy loading: the
	 * browser sees each image scroll into view and fetches it, even for a
	 * visitor — or a Lighthouse run — that never scrolls. That alone was
	 * pulling ~9 MB and keeping the main thread busy for ~10s, which is why
	 * Lighthouse could not record an LCP at all.
	 *
	 * Pausing them offscreen costs nothing visually: by the time a section is
	 * on screen it is running normally.
	 */
	function initOffscreenAnimationFreeze() {
		if (!('IntersectionObserver' in window)) return;

		var selector = [
			'.home-ba__walltrack',
			'.facilities__logos',
			'.trust-reel__blob',
			'.signature__inner',
			'.hero-aw__video-ring',
			'.why-choose__mark-pulse',
			'.facilities__play-ring',
			'.signature__card-eyebrow-dot',
			'.signature__hint-icon',
			'.hal__option-badge',
			'.hal__option--ai'
		].join(',');

		var nodes = document.querySelectorAll(selector);
		if (!nodes.length) return;

		var io = new IntersectionObserver(function (entries) {
			entries.forEach(function (entry) {
				entry.target.classList.toggle('is-anim-idle', !entry.isIntersecting);
			});
		}, { rootMargin: '200px 0px' });

		nodes.forEach(function (node) {
			// Start frozen; the observer unfreezes whatever is already in view.
			node.classList.add('is-anim-idle');
			io.observe(node);
		});
	}

	function initFacilitiesMarquee() {
		var marquees = document.querySelectorAll('[data-facilities-marquee]');
		if (!marquees.length) return;

		marquees.forEach(function (marquee) {
			var images = Array.prototype.slice.call(marquee.querySelectorAll('.facilities__logo-photo'));
			if (!images.length) {
				marquee.setAttribute('data-ready', 'true');
				return;
			}

			// Hold the marquee until every image has decoded, so it never starts
			// half-drawn. This is why the images used to be loading="eager": a lazy
			// image never decodes, so the gate would never open.
			function openWhenDecoded() {
				var decoded = images.map(function (img) {
					if (typeof img.decode === 'function') {
						return img.decode().catch(function () {});
					}
					if (img.complete) return Promise.resolve();
					return new Promise(function (resolve) {
						img.addEventListener('load', resolve, { once: true });
						img.addEventListener('error', resolve, { once: true });
					});
				});

				Promise.all(decoded).then(function () {
					requestAnimationFrame(function () {
						marquee.setAttribute('data-ready', 'true');
					});
				});
			}

			// The images are lazy now, keeping ~1.8 MB off the critical path of
			// every page. Promote them to eager only as the section nears the
			// viewport, then run the same decode gate.
			function start() {
				images.forEach(function (img) { img.loading = 'eager'; });
				openWhenDecoded();
			}

			if ('IntersectionObserver' in window) {
				var io = new IntersectionObserver(function (entries) {
					if (!entries.some(function (e) { return e.isIntersecting; })) return;
					io.disconnect();
					start();
				}, { rootMargin: '400px 0px' });
				io.observe(marquee);
			} else {
				start();
			}
		});
	}

	function initImageLightbox() {
		var lightbox = document.querySelector('[data-img-lightbox]');
		if (!lightbox) return;

		var imgEl       = lightbox.querySelector('[data-img-lightbox-img]');
		var closeBtns   = lightbox.querySelectorAll('[data-img-lightbox-close]');
		var prevBtn     = lightbox.querySelector('[data-img-lightbox-prev]');
		var nextBtn     = lightbox.querySelector('[data-img-lightbox-next]');
		var countEl     = lightbox.querySelector('[data-img-lightbox-count]');
		var lastFocused = null;
		var gallery     = [];
		var index       = 0;

		function render() {
			var multi = gallery.length > 1;
			imgEl.setAttribute('src', gallery[index] || '');
			if (prevBtn) prevBtn.hidden = !multi;
			if (nextBtn) nextBtn.hidden = !multi;
			if (countEl) {
				countEl.hidden = !multi;
				countEl.textContent = (index + 1) + ' / ' + gallery.length;
			}
		}

		function openGallery(list, start, alt) {
			gallery = list.filter(Boolean);
			if (!gallery.length) return;
			closeStoriesVideo(); // Opening a photo gallery stops any playing clinic video.
			index = Math.max(0, Math.min(start || 0, gallery.length - 1));
			lastFocused = document.activeElement;
			imgEl.setAttribute('alt', alt || '');
			render();
			lightbox.removeAttribute('hidden');
			lightbox.setAttribute('data-open', 'true');
			document.body.classList.add('no-scroll');
			var firstClose = lightbox.querySelector('.img-lightbox__close');
			if (firstClose) firstClose.focus();
		}
		function step(dir) {
			if (gallery.length < 2) return;
			index = (index + dir + gallery.length) % gallery.length;
			render();
		}
		function close() {
			lightbox.removeAttribute('data-open');
			lightbox.setAttribute('hidden', '');
			imgEl.setAttribute('src', '');
			gallery = [];
			document.body.classList.remove('no-scroll');
			if (lastFocused && typeof lastFocused.focus === 'function') { lastFocused.focus(); }
		}

		// Delegated so it also covers images rendered inside carousels/galleries.
		document.addEventListener('click', function (e) {
			var trigger = e.target.closest('[data-img-gallery], [data-img-zoom]');
			if (!trigger) return;
			e.preventDefault();
			var raw = trigger.getAttribute('data-img-gallery');
			if (raw) {
				var list = [];
				try { list = JSON.parse(raw); } catch (err) { list = []; }
				openGallery(list, 0, trigger.getAttribute('data-caption') || '');
				return;
			}
			var src = trigger.getAttribute('data-img-zoom');
			if (!src) {
				var innerImg = trigger.querySelector('img');
				src = innerImg ? innerImg.currentSrc || innerImg.src : '';
			}
			openGallery([src], 0, trigger.getAttribute('data-caption') || '');
		});
		if (prevBtn) prevBtn.addEventListener('click', function () { step(-1); });
		if (nextBtn) nextBtn.addEventListener('click', function () { step(1); });
		closeBtns.forEach(function (b) { b.addEventListener('click', close); });
		document.addEventListener('keydown', function (e) {
			if (lightbox.getAttribute('data-open') !== 'true') return;
			if (e.key === 'Escape') { close(); }
			else if (e.key === 'ArrowLeft') { step(-1); }
			else if (e.key === 'ArrowRight') { step(1); }
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

	function initTOC() {
		var toc = document.querySelector('[data-toc]');
		if (!toc) return;

		var links = Array.prototype.slice.call(toc.querySelectorAll('[data-toc-link]'));
		if (!links.length) return;

		// Collapse / expand.
		var toggle = toc.querySelector('[data-toc-toggle]');
		if (toggle) {
			toggle.addEventListener('click', function () {
				var collapsed = toc.classList.toggle('is-collapsed');
				toggle.setAttribute('aria-expanded', collapsed ? 'false' : 'true');
			});
		}

		// Scroll-spy: highlight the section you're currently reading.
		var headings = links
			.map(function (l) { return document.getElementById(l.getAttribute('data-toc-link')); })
			.filter(Boolean);
		if (!headings.length) return;

		var current = null;
		function setActive(id) {
			if (id === current) return;
			current = id;
			links.forEach(function (l) {
				l.classList.toggle('is-active', l.getAttribute('data-toc-link') === id);
			});
		}

		var ticking = false;
		function measure() {
			ticking = false;
			var offset = 120; // clear the sticky header
			var active = headings[0];
			for (var i = 0; i < headings.length; i++) {
				if (headings[i].getBoundingClientRect().top - offset <= 0) {
					active = headings[i];
				} else {
					break;
				}
			}
			if (active) setActive(active.id);
		}
		function onScroll() {
			if (!ticking) { ticking = true; window.requestAnimationFrame(measure); }
		}

		window.addEventListener('scroll', onScroll, { passive: true });
		window.addEventListener('resize', onScroll);
		measure();
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
			emit('lead-open', {
				location: 'popup',
				ctaText: trigger ? (trigger.textContent || '').replace(/\s+/g, ' ').trim() : ''
			});
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

		/** Value of a form field by name, for the lead's tracking parameters. */
		function fieldValue(name) {
			var field = form.querySelector('[name="' + name + '"]');
			return field ? field.value : '';
		}

		function showError(msg) {
			emit('lead-error', { location: 'popup', message: msg || 'network_or_unknown' });
			if (!feedback) return;
			var serverErrors = window.EstecapelliLeadServerErrors || {};
			if (msg && serverErrors[msg]) {
				msg = serverErrors[msg];
			}
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
						// Read the tracking fields before the form is replaced by
						// the success panel — after that they no longer exist.
						emit('lead-success', {
							location: 'popup',
							treatment: fieldValue('lead_treatment'),
							language: fieldValue('lead_lang')
						});
						var msg = i18n.thanks || ((res.data && res.data.message) ? res.data.message : 'Thank you!');
						form.innerHTML = '<div class="lead-popup__success" role="status">' +
							'<span class="lead-popup__success-mark" aria-hidden="true"></span>' +
							'<strong></strong></div>';
						form.querySelector('strong').textContent = msg;
					} else {
						showError(res && res.data && res.data.message);
					}
				})
				.catch(function () {
					showError();
				})
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

	// Hide the floating WhatsApp button once the footer is reached; show it again
	// while scrolling back up above the footer.
	function initFloatWhatsApp() {
		var btn = document.querySelector('.float-wp');
		var footer = document.querySelector('.site-footer');
		if (!btn || !footer || !('IntersectionObserver' in window)) return;
		var io = new IntersectionObserver(function (entries) {
			entries.forEach(function (e) {
				btn.classList.toggle('float-wp--hidden', e.isIntersecting);
			});
		}, { threshold: 0 });
		io.observe(footer);
	}

	/**
	 * Fake WhatsApp chat (template-parts/whatsapp-chat.php).
	 *
	 * Intercepts the floating button and anything marked [data-wa-chat], shows a
	 * WhatsApp-looking window, and only hands off to the real wa.me link once the
	 * visitor has written and confirmed a message. Progressive enhancement: the
	 * button keeps its real href, so without JS the click still reaches WhatsApp.
	 */
	function initWhatsAppChat() {
		var chat = document.getElementById('wpChat');
		if (!chat) return;

		var triggers = document.querySelectorAll('.float-wp, [data-wa-chat]');
		if (!triggers.length) return;

		var body      = document.getElementById('wpChatBody');
		var input     = document.getElementById('wpChatInput');
		var composer  = document.getElementById('wpChatComposer');
		var confirm   = document.getElementById('wpConfirm');
		var preview   = document.getElementById('wpConfirmPreview');
		var closeBtn  = document.getElementById('wpChatClose');
		var cancelBtn = document.getElementById('wpConfirmCancel');
		var sendBtn   = document.getElementById('wpConfirmSend');
		if (!body || !input || !composer || !confirm || !preview) return;

		// The real wa.me URL lives on the button, so the number stays in PHP.
		var waBase = triggers[0].getAttribute('href') || '';
		var defaultPlaceholder = input.getAttribute('placeholder') || '';
		var emptyHint = input.getAttribute('data-empty-hint') || defaultPlaceholder;
		var hintTimer;

		function now() {
			var d = new Date();
			return ('0' + d.getHours()).slice(-2) + ':' + ('0' + d.getMinutes()).slice(-2);
		}

		// Stamp the pre-written welcome bubble with the visitor's local time.
		chat.querySelectorAll('[data-wp-now]').forEach(function (el) { el.textContent = now(); });

		function scrollToBottom() { body.scrollTop = body.scrollHeight; }

		function openChat() {
			chat.hidden = false;
			// Next frame, so the transition runs instead of being skipped.
			requestAnimationFrame(function () { chat.classList.add('is-open'); });
			document.body.classList.add('no-scroll');
			setTimeout(function () { input.focus(); scrollToBottom(); }, 320);
		}
		function closeChat() {
			chat.classList.remove('is-open');
			confirm.classList.remove('is-open');
			document.body.classList.remove('no-scroll');
			setTimeout(function () { chat.hidden = true; }, 300);
		}

		triggers.forEach(function (t) {
			t.addEventListener('click', function (e) {
				e.preventDefault();
				// The overlay intercepts the wa.me link, so no whatsapp_click is
				// recorded here — opening the chat is its own, earlier step.
				emit('wa-chat-open', {
					location: t.classList.contains('float-wp') ? 'floating' : 'inline'
				});
				openChat();
			});
		});

		if (closeBtn) closeBtn.addEventListener('click', closeChat);
		chat.addEventListener('click', function (e) { if (e.target === chat) closeChat(); });

		document.addEventListener('keydown', function (e) {
			if (e.key !== 'Escape' || chat.hidden) return;
			if (confirm.classList.contains('is-open')) confirm.classList.remove('is-open');
			else closeChat();
		});

		composer.addEventListener('submit', function (e) {
			e.preventDefault();
			var text = input.value.trim();
			if (!text) {
				input.classList.remove('wp-shake');
				void input.offsetWidth; // reflow, so the animation replays
				input.classList.add('wp-shake');
				clearTimeout(hintTimer);
				input.setAttribute('placeholder', emptyHint);
				hintTimer = setTimeout(function () {
					input.setAttribute('placeholder', defaultPlaceholder);
				}, 2200);
				input.focus();
				return;
			}
			preview.textContent = text; // textContent, so the preview can't inject markup
			confirm.classList.add('is-open');
		});

		if (cancelBtn) {
			cancelBtn.addEventListener('click', function () {
				confirm.classList.remove('is-open');
				input.focus();
			});
		}

		if (sendBtn) {
			sendBtn.addEventListener('click', function () {
				var text = input.value.trim();
				if (!text) { confirm.classList.remove('is-open'); return; }

				// The actual handoff to WhatsApp, with a message the visitor
				// wrote — the strongest intent signal the chat overlay produces.
				emit('wa-chat-send', { length: text.length });

				// Echo the message as an outgoing bubble before handing off, so the
				// illusion holds for the moment before WhatsApp opens.
				var msg = document.createElement('div');
				msg.className = 'wp-msg wp-msg-out';
				var bubble = document.createElement('div');
				bubble.className = 'wp-bubble';
				bubble.textContent = text;
				var time = document.createElement('span');
				time.className = 'wp-msg-time';
				time.textContent = now();
				bubble.appendChild(time);
				msg.appendChild(bubble);
				body.appendChild(msg);
				scrollToBottom();

				input.value = '';
				confirm.classList.remove('is-open');

				var sep = waBase.indexOf('?') === -1 ? '?' : '&';
				var url = waBase + sep + 'text=' + encodeURIComponent(text);
				setTimeout(function () {
					window.open(url, '_blank', 'noopener,noreferrer');
					setTimeout(closeChat, 800);
				}, 600);
			});
		}
	}

	function initIntroSliders() {
		var sliders = document.querySelectorAll('[data-intro-slider]');
		if (!sliders.length) return;

		sliders.forEach(function (slider) {
			var slides = Array.prototype.slice.call(slider.querySelectorAll('.t-intro__slide'));
			if (slides.length < 2) return;

			var dots = Array.prototype.slice.call(slider.querySelectorAll('[data-intro-slider-dot]'));
			var prev = slider.querySelector('[data-intro-slider-prev]');
			var next = slider.querySelector('[data-intro-slider-next]');
			var index = 0;

			function show(i) {
				index = (i + slides.length) % slides.length;
				slides.forEach(function (s, n) { s.classList.toggle('is-active', n === index); });
				dots.forEach(function (d, n) { d.classList.toggle('is-active', n === index); });
			}

			if (prev) prev.addEventListener('click', function () { show(index - 1); });
			if (next) next.addEventListener('click', function () { show(index + 1); });
			dots.forEach(function (d) {
				d.addEventListener('click', function () { show(parseInt(d.getAttribute('data-intro-slider-dot'), 10) || 0); });
			});

			// Touch swipe.
			var startX = null;
			slider.addEventListener('touchstart', function (e) { startX = e.touches[0].clientX; }, { passive: true });
			slider.addEventListener('touchend', function (e) {
				if (startX === null) return;
				var dx = e.changedTouches[0].clientX - startX;
				if (Math.abs(dx) > 40) { show(dx < 0 ? index + 1 : index - 1); }
				startX = null;
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
		initBAScroll();
		initHairAnalysisLab();
		initServicesTabs();
		initTabScrollers();
		initCategoryTabs();
		initPatientStories();
		initStoriesLightbox();
		initFacilitiesMarquee();
		initOffscreenAnimationFreeze();
		initFacilitiesLightbox();
		initImageLightbox();
		initCarousels();
		initIntroSliders();
		initFloatWhatsApp();
		initWhatsAppChat();
		initStepbooks();
		initCopyLink();
		initTOC();
		initLeadPopup();
		initTrustReel();
	});
})();
