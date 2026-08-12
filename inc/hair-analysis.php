<?php
/**
 * AI Hair Analysis backend — Claude vision.
 *
 * Two REST routes power the homepage Hair-Analysis Lab photo widget:
 *
 *   POST /wp-json/estecapelli/v1/analyze    {photos:{front,side,top,donor: base64 JPEG}, nonce, locale}
 *        → calls Claude Opus 4.8 vision and returns a PRELIMINARY estimate:
 *          { ok:true, analysis:{ norwood_stage, graft_range:{min,max}, summary } }
 *
 *   POST /wp-json/estecapelli/v1/hair-lead  (multipart: contact fields + photo_* files + analysis_json)
 *        → stores a private `lead` and emails it (To lead@, BCC the Kommo parser),
 *          with the photos attached and the AI estimate included.
 *
 * The Anthropic API key lives ONLY in wp-config.php and never reaches the
 * browser — the widget talks to these WordPress routes, which proxy to Claude.
 *
 * Add to wp-config.php (NOT committed):
 *   define( 'ESTECAPELLI_ANTHROPIC_KEY', 'sk-ant-...' );
 *
 * @package Estecapelli
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! defined( 'ESTECAPELLI_ANTHROPIC_MODEL' ) ) {
	// Sonnet 4.6 is plenty for a broad, surface-level screening estimate and is
	// far cheaper than Opus. Override in wp-config.php if you want a stronger
	// model: define( 'ESTECAPELLI_ANTHROPIC_MODEL', 'claude-opus-4-8' );
	define( 'ESTECAPELLI_ANTHROPIC_MODEL', 'claude-sonnet-4-6' );
}

add_action( 'rest_api_init', function () {
	register_rest_route(
		'estecapelli/v1',
		'/analyze',
		array(
			'methods'             => 'POST',
			'callback'            => 'estecapelli_hair_analyze',
			'permission_callback' => '__return_true', // nonce-checked inside
		)
	);
	register_rest_route(
		'estecapelli/v1',
		'/hair-lead',
		array(
			'methods'             => 'POST',
			'callback'            => 'estecapelli_hair_lead',
			'permission_callback' => '__return_true',
		)
	);
	// Fresh nonce for the widget. The nonce printed in the page HTML gets frozen
	// by full-page caches and expires after ~12–24h → 403 for every visitor.
	// This uncached route hands out a live nonce right before each submission.
	register_rest_route(
		'estecapelli/v1',
		'/hair-nonce',
		array(
			'methods'             => 'GET',
			'callback'            => 'estecapelli_hair_nonce',
			'permission_callback' => '__return_true',
		)
	);
} );

/**
 * Return a freshly-minted nonce for the widget, with caching disabled so no
 * layer (plugin / server / CDN) can serve a stale one.
 */
function estecapelli_hair_nonce() {
	nocache_headers();
	$res = new WP_REST_Response( array( 'ok' => true, 'nonce' => wp_create_nonce( 'estecapelli_hair' ) ), 200 );
	$res->header( 'Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0' );
	return $res;
}

/**
 * Verify the widget nonce; returns a WP_REST_Response error or null on success.
 */
function estecapelli_hair_check_nonce( $nonce ) {
	if ( ! $nonce || ! wp_verify_nonce( $nonce, 'estecapelli_hair' ) ) {
		return new WP_REST_Response( array( 'ok' => false, 'error' => 'bad_nonce' ), 403 );
	}
	return null;
}

/**
 * Resolve the language Claude must write the summary in, as an English language
 * name it will recognise ("French", "European Portuguese", …).
 *
 * Order of trust: the language the page told us it is rendered in → WPML's
 * current language for this request → WordPress' locale. Deliberately NOT a
 * closed list: any language the site is ever translated into resolves through
 * the intl extension (or its own code as a last hint), so adding a WPML
 * language needs no change here. English is only ever the final fallback when
 * nothing identifies the visitor's language.
 *
 * @param string $locale Language code sent by the widget (may be empty).
 * @return string English name of the language to answer in.
 */
function estecapelli_hair_language_name( $locale = '' ) {

	$candidates = array( $locale );
	if ( function_exists( 'estecapelli_indexed_language_code' ) ) {
		$candidates[] = (string) estecapelli_indexed_language_code();
	}
	$candidates[] = (string) apply_filters( 'wpml_current_language', null );
	$candidates[] = defined( 'ICL_LANGUAGE_CODE' ) ? (string) ICL_LANGUAGE_CODE : '';
	$candidates[] = function_exists( 'determine_locale' ) ? (string) determine_locale() : get_locale();

	// Names worth pinning because the generic lookup is too vague for the
	// clinic's audiences (pt must be the European variant, not Brazilian).
	$named = array(
		'pt'    => 'European Portuguese',
		'pt-pt' => 'European Portuguese',
		'pt-br' => 'Brazilian Portuguese',
		'zh-hant' => 'Traditional Chinese',
		'zh-hans' => 'Simplified Chinese',
	);

	foreach ( $candidates as $candidate ) {
		$code = strtolower( str_replace( '_', '-', trim( (string) $candidate ) ) );
		if ( '' === $code ) {
			continue;
		}
		if ( isset( $named[ $code ] ) ) {
			return $named[ $code ];
		}
		$short = substr( $code, 0, 2 );
		if ( isset( $named[ $short ] ) ) {
			return $named[ $short ];
		}
		if ( function_exists( 'locale_get_display_language' ) ) {
			$name = locale_get_display_language( $code, 'en' );
			// intl echoes the input back when it cannot resolve the code.
			if ( $name && strtolower( $name ) !== $code && strtolower( $name ) !== $short ) {
				return $name;
			}
		}
		// No intl on this host: hand Claude the code itself — it reads BCP-47
		// fine, and that is still better than silently answering in English.
		if ( strlen( $short ) === 2 ) {
			return 'the language with ISO 639-1 code "' . $short . '"';
		}
	}

	return 'English';
}

/* -------------------------------------------------------------------------
 * /analyze — Claude vision
 * ---------------------------------------------------------------------- */
function estecapelli_hair_analyze( WP_REST_Request $request ) {

	$body  = $request->get_json_params();
	$nonce = isset( $body['nonce'] ) ? sanitize_text_field( $body['nonce'] ) : '';
	if ( $err = estecapelli_hair_check_nonce( $nonce ) ) {
		return $err;
	}
	$limited = function_exists( 'estecapelli_rate_limit' )
		? estecapelli_rate_limit( 'hair_analyze', 3, HOUR_IN_SECONDS )
		: true;
	if ( is_wp_error( $limited ) ) {
		return new WP_REST_Response( array( 'ok' => false, 'error' => 'rate_limited' ), 429 );
	}

	if ( ! defined( 'ESTECAPELLI_ANTHROPIC_KEY' ) || ! ESTECAPELLI_ANTHROPIC_KEY ) {
		return new WP_REST_Response( array( 'ok' => false, 'error' => 'not_configured' ), 500 );
	}

	$photos = isset( $body['photos'] ) && is_array( $body['photos'] ) ? $body['photos'] : array();
	if ( empty( $photos ) ) {
		return new WP_REST_Response( array( 'ok' => false, 'error' => 'no_photos' ), 400 );
	}

	// The visitor must always be answered in the language of the page they are
	// on — never English by default. The page sends its own language; if that is
	// missing we ask WPML / WordPress what language this request is in, and only
	// fall back to English when nothing at all is known.
	$locale = isset( $body['locale'] ) ? sanitize_key( (string) $body['locale'] ) : '';
	$summary_language = estecapelli_hair_language_name( $locale );

	// Build the multimodal user content: each photo as a base64 image block,
	// then the instruction. Labels help Claude reason about each angle.
	$labels  = array(
		'front' => 'Front of head / hairline',
		'side'  => 'Side profile',
		'top'   => 'Top / crown',
		'donor' => 'Back / donor area',
	);
	$content = array();
	foreach ( $photos as $id => $b64 ) {
		$b64 = preg_replace( '#^data:image/[^;]+;base64,#', '', (string) $b64 );
		if ( ! $b64 ) {
			continue;
		}
		$content[] = array(
			'type'   => 'text',
			'text'   => isset( $labels[ $id ] ) ? $labels[ $id ] . ':' : ( $id . ':' ),
		);
		$content[] = array(
			'type'   => 'image',
			'source' => array(
				'type'       => 'base64',
				'media_type' => 'image/jpeg',
				'data'       => $b64,
			),
		);
	}
	if ( empty( $content ) ) {
		return new WP_REST_Response( array( 'ok' => false, 'error' => 'no_photos' ), 400 );
	}

	$content[] = array(
		'type' => 'text',
		'text' =>
			"You are screening patient-submitted photos for a hair-transplant clinic. Give a BRIEF, surface-level first-impression estimate — NOT a clinical diagnosis. Be accurate and honest; do not invent detail you cannot see.\n\n" .
			"LANGUAGE — MANDATORY: the visitor is reading a page written in {$summary_language}. Write the \"summary\" value entirely in {$summary_language}. Do not answer in English unless {$summary_language} is English, and do not mix languages. Only the JSON keys and the fixed \"status\" value stay in English.\n\n" .
			"Return:\n" .
			"- status: \"ok\" normally. Use \"no_hair_detected\" if the photos do NOT clearly show a human head/scalp/hair (blank, too dark, an unrelated object, a face with no visible hairline, etc.) — in that case do not guess numbers.\n" .
			"- norwood_stage: approximate Norwood-Hamilton stage, integer 1-7.\n" .
			"- graft_range: a BROAD, indicative FUE graft range {min,max} (realistic, usually 1000-5000; keep it wide, not a falsely exact number). Give this range at every stage, including early ones.\n" .
			"- summary: AT MOST TWO short sentences, plain and friendly, in {$summary_language}. Cover only: whether the donor area looks good or limited, what their Norwood stage means in everyday words, and roughly why that many grafts. NEVER tell the visitor whether they should or should not have a transplant, that their hair loss is too minimal, or that a transplant is not needed — that decision is theirs alone. Just describe the stage you see. If status is \"no_hair_detected\", say you couldn't clearly see their hair and ask for clearer, well-lit photos. Do NOT write a long paragraph, and do NOT mention consultations, bookings or next steps — keep it short.\n\n" .
			"Respond with ONLY a raw JSON object — no markdown, no code fences, no words before or after — with exactly these keys: status (\"ok\" or \"no_hair_detected\"), norwood_stage (integer 1-7), graft_range (object with integer \"min\" and \"max\"), summary (string).",
	);

	$payload = array(
		'model'      => ESTECAPELLI_ANTHROPIC_MODEL,
		'max_tokens' => 600,
		// The prompt asks for a raw JSON object; we parse it from the text reply.
		// (Kept plain for broad compatibility across models / API versions.)
		'messages'   => array(
			array( 'role' => 'user', 'content' => $content ),
		),
	);

	// Shared hosting often caps execution at 30s — keep the call snug.
	@set_time_limit( 60 ); // phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged

	$response = wp_remote_post(
		'https://api.anthropic.com/v1/messages',
		array(
			'timeout' => 45,
			'headers' => array(
				'x-api-key'         => ESTECAPELLI_ANTHROPIC_KEY,
				'anthropic-version' => '2023-06-01',
				'content-type'      => 'application/json',
			),
			'body'    => wp_json_encode( $payload ),
		)
	);

	if ( is_wp_error( $response ) ) {
		// Almost always means the host blocked/failed the outbound HTTPS call
		// (firewall, no DNS, TLS/CA problem, or timeout). Surface the exact
		// reason so it can be diagnosed from the browser Network tab.
		error_log( 'Estecapelli hair-analyze unreachable: ' . $response->get_error_message() );
		return new WP_REST_Response(
			array( 'ok' => false, 'error' => 'upstream_unreachable', 'detail' => $response->get_error_message() ),
			502
		);
	}

	$code = wp_remote_retrieve_response_code( $response );
	$raw  = wp_remote_retrieve_body( $response );
	if ( 200 !== (int) $code ) {
		error_log( 'Estecapelli hair-analyze API ' . $code . ': ' . $raw );
		// Pull the Anthropic error type/message (never contains the key) so the
		// exact cause (auth, billing, request too large, rate limit…) is visible.
		$api    = json_decode( $raw, true );
		$detail = 'HTTP ' . $code;
		if ( isset( $api['error']['type'] ) ) {
			$detail .= ' — ' . $api['error']['type'];
		}
		if ( isset( $api['error']['message'] ) ) {
			$detail .= ': ' . $api['error']['message'];
		}
		return new WP_REST_Response(
			array( 'ok' => false, 'error' => 'upstream_error', 'detail' => $detail ),
			502
		);
	}

	$data = json_decode( $raw, true );
	// Find the text block holding the JSON and decode it, tolerating markdown
	// code fences or stray text around the object.
	$analysis = null;
	if ( ! empty( $data['content'] ) && is_array( $data['content'] ) ) {
		foreach ( $data['content'] as $block ) {
			if ( ! isset( $block['type'], $block['text'] ) || 'text' !== $block['type'] ) {
				continue;
			}
			$text    = trim( (string) $block['text'] );
			$text    = preg_replace( '/^```(?:json)?\s*|\s*```$/i', '', $text ); // strip ``` fences
			$decoded = json_decode( $text, true );
			if ( ! is_array( $decoded ) && preg_match( '/\{.*\}/s', $text, $m ) ) {
				$decoded = json_decode( $m[0], true ); // extract the first {...}
			}
			if ( is_array( $decoded ) && isset( $decoded['norwood_stage'], $decoded['graft_range'] ) ) {
				$analysis = $decoded;
				break;
			}
		}
	}
	if ( ! $analysis ) {
		error_log( 'Estecapelli hair-analyze parse failed. Body: ' . substr( $raw, 0, 1000 ) );
	}

	if ( ! $analysis ) {
		return new WP_REST_Response( array( 'ok' => false, 'error' => 'parse_failed' ), 502 );
	}

	return new WP_REST_Response( array( 'ok' => true, 'analysis' => $analysis ), 200 );
}

/* -------------------------------------------------------------------------
 * /hair-lead — store + notify (with photos attached + AI estimate)
 * ---------------------------------------------------------------------- */
function estecapelli_hair_lead( WP_REST_Request $request ) {

	$nonce = sanitize_text_field( (string) $request->get_param( 'nonce' ) );
	if ( $err = estecapelli_hair_check_nonce( $nonce ) ) {
		return $err;
	}
	$limited = function_exists( 'estecapelli_rate_limit' )
		? estecapelli_rate_limit( 'hair_lead', 5, HOUR_IN_SECONDS )
		: true;
	if ( is_wp_error( $limited ) ) {
		return new WP_REST_Response( array( 'ok' => false, 'error' => 'rate_limited' ), 429 );
	}

	$name = sanitize_text_field( (string) $request->get_param( 'lead_name' ) );
	if ( '' === $name ) {
		return new WP_REST_Response( array( 'ok' => false, 'error' => 'missing_name' ), 422 );
	}
	$phone   = sanitize_text_field( (string) $request->get_param( 'lead_phone' ) );
	$email   = sanitize_email( (string) $request->get_param( 'lead_email' ) );
	$consent = '1' === (string) $request->get_param( 'lead_consent' );
	if ( ! function_exists( 'estecapelli_phone_looks_valid' ) || ! estecapelli_phone_looks_valid( $phone ) ) {
		return new WP_REST_Response( array( 'ok' => false, 'error' => 'invalid_phone' ), 422 );
	}
	if ( ! is_email( $email ) ) {
		return new WP_REST_Response( array( 'ok' => false, 'error' => 'invalid_email' ), 422 );
	}
	if ( ! $consent ) {
		return new WP_REST_Response( array( 'ok' => false, 'error' => 'missing_consent' ), 422 );
	}

	// Preferred contact channel chosen up front (whatsapp | call | email).
	$method        = sanitize_text_field( (string) $request->get_param( 'lead_method' ) );
	$method_labels = array( 'whatsapp' => 'WhatsApp', 'call' => 'Direct call', 'email' => 'Email' );
	$method_label  = isset( $method_labels[ $method ] ) ? $method_labels[ $method ] : ( $method ?: '-' );

	$analysis = json_decode( (string) $request->get_param( 'analysis_json' ), true );
	$norwood  = isset( $analysis['norwood_stage'] ) ? (int) $analysis['norwood_stage'] : 0;
	$grange   = '';
	if ( isset( $analysis['graft_range']['min'], $analysis['graft_range']['max'] ) ) {
		$grange = number_format_i18n( (int) $analysis['graft_range']['min'] ) . '–' . number_format_i18n( (int) $analysis['graft_range']['max'] );
	}
	$summary = isset( $analysis['summary'] ) ? sanitize_textarea_field( $analysis['summary'] ) : '';

	$lead_limits = function_exists( 'estecapelli_check_lead_limits' )
		? estecapelli_check_lead_limits(
			array(
				'name'    => $name,
				'phone'   => $phone,
				'email'   => $email,
				'message' => $summary,
			)
		)
		: true;
	if ( is_wp_error( $lead_limits ) ) {
		if ( 'duplicate_lead' === $lead_limits->get_error_code() ) {
			return new WP_REST_Response( array( 'ok' => true, 'duplicate' => true ), 200 );
		}
		return new WP_REST_Response( array( 'ok' => false, 'error' => $lead_limits->get_error_code() ), 429 );
	}

	$source_label = __( 'AI Photo Hair Analysis', 'estecapelli' );

	// The widget posts to the REST API, where WPML cannot resolve the visitor's
	// language, so it sends the page's own language and URL along. Reuse the
	// shared helpers so this lead reaches Kommo in the same shape as every form.
	$lead_ctx = array(
		'lang'       => sanitize_text_field( (string) $request->get_param( 'lead_lang' ) ),
		'page_url'   => esc_url_raw( (string) $request->get_param( 'lead_page_url' ) ),
		'page_title' => sanitize_text_field( (string) $request->get_param( 'lead_page_title' ) ),
	);
	$lead_lang   = function_exists( 'estecapelli_lead_language_code' ) ? estecapelli_lead_language_code( $lead_ctx ) : 'EN';
	$kommo_source = function_exists( 'estecapelli_lead_kommo_source' ) ? estecapelli_lead_kommo_source( $lead_ctx ) : $source_label;

	// Store the lead.
	$lead_id = wp_insert_post(
		array(
			'post_type'   => 'lead',
			'post_status' => 'private',
			'post_title'  => $name . ( $phone ? ' — ' . $phone : '' ),
		),
		true
	);
	if ( ! is_wp_error( $lead_id ) ) {
		update_post_meta( $lead_id, 'lead_phone', $phone );
		update_post_meta( $lead_id, 'lead_email', $email );
		update_post_meta( $lead_id, 'lead_source', $source_label );
		update_post_meta( $lead_id, 'lead_pref_contact', $method_label );
		update_post_meta( $lead_id, 'lead_norwood', $norwood );
		update_post_meta( $lead_id, 'lead_graft_range', $grange );
		update_post_meta( $lead_id, 'lead_message', $summary );
	}

	// Save the uploaded photos to temp files so they can be attached.
	$attachments = array();
	$files       = $request->get_file_params();
	foreach ( array( 'front', 'side', 'top', 'donor' ) as $key ) {
		$f = isset( $files[ 'photo_' . $key ] ) ? $files[ 'photo_' . $key ] : null;
		if ( $f && empty( $f['error'] ) && ! empty( $f['tmp_name'] ) && is_uploaded_file( $f['tmp_name'] ) ) {
			$dest = trailingslashit( get_temp_dir() ) . 'ec-' . $key . '-' . wp_generate_password( 8, false ) . '.jpg';
			if ( @copy( $f['tmp_name'], $dest ) ) { // phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged
				$attachments[] = $dest;
			}
		}
	}

	// Notify clinic + Kommo CRM (same recipients/format as the other forms).
	$to      = apply_filters( 'estecapelli_lead_email_to', defined( 'ESTECAPELLI_LEAD_TO' ) ? ESTECAPELLI_LEAD_TO : get_option( 'admin_email' ), array() );
	$subject = sprintf( /* translators: %s: lead name */ __( 'New lead (AI Photo Analysis) — %s', 'estecapelli' ), $name );
	$lines   = array(
		'Adı Soyadı: ' . $name,
		'Email: ' . ( $email ?: '-' ),
		'Telefon: ' . ( $phone ?: '-' ),
		'Tercih edilen iletişim: ' . $method_label,
		'Norwood: ' . ( $norwood ?: '-' ),
		'Greft Aralığı: ' . ( $grange ?: '-' ),
		'Analiz: ' . ( $summary ?: '-' ),
		'Onay (KVKK/GDPR): ' . ( $consent ? 'Evet' : 'Hayır' ),
		'Dil: ' . $lead_lang,
		'Kaynak: ' . $kommo_source,
	);

	$from_name = get_bloginfo( 'name' );
	$headers   = array(
		'Content-Type: text/plain; charset=UTF-8',
		sprintf( 'From: %s <%s>', $from_name, defined( 'ESTECAPELLI_MAIL_FROM' ) ? ESTECAPELLI_MAIL_FROM : get_option( 'admin_email' ) ),
	);
	if ( defined( 'ESTECAPELLI_KOMMO_PARSER' ) && ESTECAPELLI_KOMMO_PARSER ) {
		$headers[] = 'Bcc: ' . ESTECAPELLI_KOMMO_PARSER;
	}
	if ( $email && is_email( $email ) ) {
		$headers[] = sprintf( 'Reply-To: %s <%s>', $name, $email );
	}

	$sent = wp_mail( $to, $subject, implode( "\r\n", $lines ), $headers, $attachments );
	if ( function_exists( 'estecapelli_lead_record_delivery' ) ) {
		estecapelli_lead_record_delivery( $lead_id, $sent, 'AI Photo Hair Analysis' );
	}

	foreach ( $attachments as $tmp ) {
		@unlink( $tmp ); // phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged
	}

	return new WP_REST_Response( array( 'ok' => true ), 200 );
}
