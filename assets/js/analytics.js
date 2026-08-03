/*
 * GA4 event layer.
 *
 * Every measurable interaction on the site arrives in Google Tag Manager as a
 * named dataLayer event with explicit parameters. GTM's job is then only to map
 * an event name to a GA4 tag — it never has to match a CSS selector or read the
 * DOM, so a redesign cannot silently break the reporting.
 *
 * Two sources feed this file:
 *
 *   1. Delegated listeners on `document`, for interactions that are visible in
 *      the markup (WhatsApp links, tel: links, CTA buttons, FAQ, language menu,
 *      scroll depth). These need no cooperation from the rest of the theme.
 *
 *   2. Namespaced DOM events (`estecapelli:*`) dispatched by the controllers
 *      that own the state — main.js, footer-lead.js and the hair widget. Those
 *      controllers know when a lead actually succeeded or which capture step the
 *      wizard reached; guessing that from the DOM would be fragile. They stay
 *      free of analytics code and this file stays the only place that knows
 *      GA4 exists.
 *
 * dataLayer hygiene: GTM merges every push into one persistent model, so a
 * parameter set by one event is still readable by the next unless it is
 * explicitly cleared. Every push below therefore resets the full parameter
 * vocabulary first — without that, a `generate_lead` from the footer would
 * inherit the `cta_location` of whatever button was clicked ten minutes ago.
 */
(function () {
	'use strict';

	/* Every parameter this file can emit. Reset on each push — see above. */
	var PARAMS = [
		'cta_text', 'cta_location', 'cta_destination',
		'form_location', 'lead_treatment', 'lead_language',
		'error_message',
		'link_location', 'link_url', 'link_text',
		'message_length',
		'analysis_mode', 'photo_step', 'capture_method', 'step_index',
		'norwood_stage', 'graft_estimate', 'zone_count',
		'from_language', 'to_language',
		'story_name', 'video_title', 'video_provider',
		'gallery_name', 'slide_index',
		'faq_question', 'tab_name', 'tab_group',
		'percent',
		'consent_analytics', 'consent_marketing', 'consent_method'
	];

	function push(name, params) {
		var payload = { event: name };
		var i;

		for (i = 0; i < PARAMS.length; i++) {
			payload[PARAMS[i]] = undefined;
		}

		if (params) {
			Object.keys(params).forEach(function (key) {
				var value = params[key];
				if (value !== null && value !== undefined && value !== '') {
					payload[key] = value;
				}
			});
		}

		window.dataLayer = window.dataLayer || [];
		window.dataLayer.push(payload);
	}

	/** Collapse whitespace and cap length — GA4 truncates parameters at 100 chars. */
	function text(el) {
		if (!el) return '';
		return (el.textContent || '').replace(/\s+/g, ' ').trim().slice(0, 100);
	}

	/**
	 * Where on the page an element sits, as a stable reporting label.
	 *
	 * Answers the question the clinic actually asks — "is the header CTA or the
	 * floating button doing the work?" — which page_location alone cannot.
	 * Anything can override the guess with data-track-location.
	 */
	function locationOf(el) {
		if (!el || !el.closest) return 'unknown';

		var explicit = el.closest('[data-track-location]');
		if (explicit) return explicit.getAttribute('data-track-location');

		if (el.closest('#lead-popup')) return 'lead_popup';
		if (el.closest('#wpChat')) return 'whatsapp_chat';
		if (el.closest('#consent-banner')) return 'consent_banner';
		if (el.closest('.float-wp') || (el.classList && el.classList.contains('float-wp'))) return 'floating';
		if (el.closest('.site-header')) return 'header';
		if (el.closest('.site-footer')) return 'footer';

		/* Section templates all lead with a distinctive class (t-faq, t-form,
		   hal, hero-x…), which makes a good section label for free. */
		var section = el.closest('section[class]');
		if (section) {
			var first = (section.className || '').split(/\s+/)[0];
			if (first) return first;
		}

		return 'body';
	}

	/** Which of the six lead forms a field or form belongs to. */
	function formLocation(el) {
		if (!el || !el.closest) return 'unknown';

		var explicit = el.closest('[data-track-form]');
		if (explicit) return explicit.getAttribute('data-track-form');

		if (el.closest('#lead-popup')) return 'popup';
		if (el.closest('#footer-lead')) return 'footer';
		if (el.closest('#hair-widget')) return 'ai_widget';
		if (el.closest('[data-hal]')) return 'hair_lab';
		if (el.closest('.t-form')) return 'treatment_section';
		if (el.closest('.contact-page, .contact-main')) return 'contact_page';

		return locationOf(el);
	}

	function isLeadForm(form) {
		return !!(form && form.querySelector && form.querySelector('[name^="lead_"]'));
	}

	/* ---------------------------------------------------------------------
	 * Delegated clicks
	 * ------------------------------------------------------------------ */

	document.addEventListener('click', function (e) {
		var target = e.target;
		if (!target || !target.closest) return;

		var link = target.closest('a[href]');

		if (link) {
			var href = link.getAttribute('href') || '';

			if (href.indexOf('tel:') === 0) {
				push('phone_click', {
					link_location: locationOf(link),
					link_url: href.replace('tel:', ''),
					link_text: text(link)
				});
				return;
			}

			if (href.indexOf('mailto:') === 0) {
				push('email_click', {
					link_location: locationOf(link),
					link_url: href.replace('mailto:', '')
				});
				return;
			}

			if (href.indexOf('wa.me') > -1 || href.indexOf('api.whatsapp.com') > -1) {
				push('whatsapp_click', {
					link_location: locationOf(link),
					/* wa.me/<number>?text=… — a prefilled message means the visitor
					   arrives with something to say, which converts very differently
					   from an empty thread. */
					message_length: href.indexOf('text=') > -1 ? 1 : 0
				});
				return;
			}

			/* Language menu: the only place a visitor changes language on purpose. */
			var langMenu = link.closest('[data-lang-switch]');
			if (langMenu) {
				var current = langMenu.querySelector('.lang-switch__current');
				push('language_switch', {
					from_language: (text(current) || '').toLowerCase(),
					to_language: (link.getAttribute('hreflang') || '').toLowerCase()
				});
				return;
			}

			if (link.classList.contains('toc__link')) {
				push('toc_click', { link_text: text(link) });
				return;
			}
		}

		/* FAQ: native <details>, whose `toggle` event does not bubble, so read
		   the state at click time — before the browser flips it. */
		var summary = target.closest('summary.t-faq__q');
		if (summary) {
			var item = summary.closest('details');
			if (item && !item.open) {
				push('faq_open', { faq_question: text(summary) });
			}
			return;
		}

		/* Any call-to-action button. The lead popup opens from these too, but
		   that is reported separately by main.js — this records the intent, the
		   popup event records that the form was actually reached. */
		var cta = target.closest('.btn');
		if (cta && !cta.closest('#consent-banner')) {
			push('cta_click', {
				cta_text: text(cta),
				cta_location: locationOf(cta),
				cta_destination: cta.getAttribute('href') || ''
			});
		}
	}, true);

	/* ---------------------------------------------------------------------
	 * Form engagement
	 *
	 * form_start fires on the first field a visitor touches in each lead form.
	 * Paired with generate_lead it gives the abandonment rate per form, which is
	 * the only way to tell "nobody sees this form" from "everybody gives up on
	 * it" — two problems with opposite fixes.
	 * ------------------------------------------------------------------ */

	document.addEventListener('focusin', function (e) {
		var field = e.target;
		if (!field || !field.form || field.type === 'hidden') return;

		var form = field.form;
		if (form.dataset.trackStarted === '1' || !isLeadForm(form)) return;

		form.dataset.trackStarted = '1';
		push('form_start', { form_location: formLocation(form) });
	});

	/* ---------------------------------------------------------------------
	 * Scroll depth
	 *
	 * GA4's enhanced measurement only reports 90%. The earlier thresholds are
	 * what show where a long treatment page loses people.
	 * ------------------------------------------------------------------ */

	(function trackScrollDepth() {
		var thresholds = [25, 50, 75, 90];
		var reached = 0;
		var ticking = false;

		function measure() {
			ticking = false;

			var doc = document.documentElement;
			var scrollable = doc.scrollHeight - window.innerHeight;
			if (scrollable <= 0) return;

			var percent = ((window.pageYOffset || doc.scrollTop) / scrollable) * 100;

			while (reached < thresholds.length && percent >= thresholds[reached]) {
				push('scroll_depth', { percent: thresholds[reached] });
				reached++;
			}

			if (reached >= thresholds.length) {
				window.removeEventListener('scroll', onScroll);
			}
		}

		function onScroll() {
			if (ticking) return;
			ticking = true;
			window.requestAnimationFrame(measure);
		}

		window.addEventListener('scroll', onScroll, { passive: true });
	})();

	/* ---------------------------------------------------------------------
	 * Redirect-based form results
	 *
	 * The contact page and the in-page treatment form POST and redirect, so
	 * their outcome arrives as server-rendered state rather than an interaction
	 * (see estecapelli_analytics_lead_result). Refreshing that URL must not add
	 * a second conversion, so each result is claimed once per session against
	 * the URL that carried it — a genuine second submit produces a new one.
	 * ------------------------------------------------------------------ */

	(function reportRedirectResult() {
		var result = window.EstecapelliLeadResult;
		if (!result) return;

		var key = 'ec_lead_' + window.location.href;
		try {
			if (window.sessionStorage.getItem(key)) return;
			window.sessionStorage.setItem(key, '1');
		} catch (e) {
			/* Private mode or storage disabled — reporting the conversion is
			   worth more than the small risk of a refresh double-counting. */
		}

		if (result.status === 'success') {
			push('generate_lead', {
				form_location: result.location,
				lead_language: result.language
			});
		} else {
			push('form_error', {
				form_location: result.location,
				error_message: result.message || 'unknown'
			});
		}
	})();

	/* ---------------------------------------------------------------------
	 * Events dispatched by the theme's own controllers
	 *
	 * `detail` shapes are documented at each dispatch site.
	 * ------------------------------------------------------------------ */

	function on(name, handler) {
		document.addEventListener('estecapelli:' + name, function (e) {
			handler(e.detail || {});
		});
	}

	/* --- Lead forms (main.js, footer-lead.js) --- */

	on('lead-open', function (d) {
		push('lead_form_open', {
			form_location: d.location || 'popup',
			cta_text: d.ctaText || ''
		});
	});

	/* The clinic's primary conversion. Marked as a Key Event in GA4. */
	on('lead-success', function (d) {
		push('generate_lead', {
			form_location: d.location || 'unknown',
			lead_treatment: d.treatment || '',
			lead_language: d.language || ''
		});
	});

	on('lead-error', function (d) {
		push('form_error', {
			form_location: d.location || 'unknown',
			error_message: (d.message || 'unknown').slice(0, 100)
		});
	});

	/* --- WhatsApp chat overlay (main.js) --- */

	on('wa-chat-open', function (d) {
		push('whatsapp_chat_open', { link_location: d.location || 'floating' });
	});

	/* The handoff to the real WhatsApp, with a message the visitor wrote. This
	   is a stronger signal than a bare whatsapp_click and is its own Key Event. */
	on('wa-chat-send', function (d) {
		push('whatsapp_chat_send', { message_length: d.length || 0 });
	});

	/* --- AI hair analysis funnel (hair-widget) --- */

	on('ai', function (d) {
		switch (d.action) {
			case 'start':
				push('ai_analysis_start', { analysis_mode: d.mode || 'photos' });
				break;
			case 'photo':
				push('ai_photo_captured', {
					photo_step: d.step || '',
					capture_method: d.method || '',
					step_index: d.index
				});
				break;
			case 'contact':
				push('ai_analysis_contact', { form_location: 'ai_widget' });
				break;
			case 'complete':
				push('ai_analysis_complete', {
					norwood_stage: d.norwood || '',
					graft_estimate: d.grafts
				});
				break;
			case 'error':
				push('ai_analysis_error', {
					error_message: (d.message || 'unknown').slice(0, 100)
				});
				break;
		}
	});

	/* --- Hair Analysis Lab chooser + self-assessment (main.js) --- */

	on('hair-lab', function (d) {
		if (d.action === 'select') {
			push('hair_lab_select', { analysis_mode: d.mode || '' });
		} else if (d.action === 'zones') {
			push('self_assessment_zones', { zone_count: d.count || 0 });
		}
	});

	/* --- Content engagement (main.js) --- */

	on('story-open', function (d) {
		push('story_open', { story_name: d.name || '' });
	});

	on('video', function (d) {
		push(d.action === 'complete' ? 'video_complete' : 'video_start', {
			video_title: d.title || '',
			video_provider: d.provider || 'youtube'
		});
	});

	on('before-after', function (d) {
		push('before_after_navigate', {
			gallery_name: d.gallery || '',
			slide_index: d.index
		});
	});

	on('tab', function (d) {
		push('tab_select', {
			tab_name: d.name || '',
			tab_group: d.group || ''
		});
	});

	/* --- Cookie banner (consent.js) --- */

	on('consent', function (d) {
		push('consent_update', {
			consent_analytics: d.analytics,
			consent_marketing: d.marketing,
			consent_method: d.method
		});
	});
})();
