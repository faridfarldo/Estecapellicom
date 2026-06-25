<?php
/**
 * AI Hair Analysis backend — Claude vision.
 *
 * Two REST routes power the homepage Hair-Analysis Lab photo widget:
 *
 *   POST /wp-json/estecapelli/v1/analyze    {photos:{front,side,top,donor: base64 JPEG}, nonce}
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
} );

/**
 * Verify the widget nonce; returns a WP_REST_Response error or null on success.
 */
function estecapelli_hair_check_nonce( $nonce ) {
	if ( ! $nonce || ! wp_verify_nonce( $nonce, 'estecapelli_hair' ) ) {
		return new WP_REST_Response( array( 'ok' => false, 'error' => 'bad_nonce' ), 403 );
	}
	return null;
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

	if ( ! defined( 'ESTECAPELLI_ANTHROPIC_KEY' ) || ! ESTECAPELLI_ANTHROPIC_KEY ) {
		return new WP_REST_Response( array( 'ok' => false, 'error' => 'not_configured' ), 500 );
	}

	$photos = isset( $body['photos'] ) && is_array( $body['photos'] ) ? $body['photos'] : array();
	if ( empty( $photos ) ) {
		return new WP_REST_Response( array( 'ok' => false, 'error' => 'no_photos' ), 400 );
	}

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
			"You are assisting a hair-transplant clinic with a BROAD, SURFACE-LEVEL screening estimate from patient-submitted photos. " .
			"This is a quick first-impression screen, NOT a precise clinical diagnosis — an approximate, indicative read is exactly what's wanted. " .
			"Looking at the hairline, crown and overall density — and the back/donor photo to gauge donor capacity — give:\n" .
			"1) an approximate Norwood-Hamilton stage (integer 1-7),\n" .
			"2) a wide, indicative FUE graft range for a transplant (realistic counts, usually 1000-5000; keep the range broad rather than a falsely exact number), and\n" .
			"3) a short, friendly, encouraging 2-3 sentence summary the patient can read.\n" .
			"Keep it general and reassuring, and make clear the exact plan is confirmed in a free in-person consultation. " .
			"If the photos are unclear, still give your best broad estimate rather than refusing.",
	);

	$payload = array(
		'model'         => ESTECAPELLI_ANTHROPIC_MODEL,
		'max_tokens'    => 1024,
		'messages'      => array(
			array( 'role' => 'user', 'content' => $content ),
		),
		// Structured output guarantees the exact JSON shape the widget expects.
		'output_config' => array(
			'format' => array(
				'type'   => 'json_schema',
				'schema' => array(
					'type'                 => 'object',
					'additionalProperties' => false,
					'properties'           => array(
						'norwood_stage' => array( 'type' => 'integer', 'enum' => array( 1, 2, 3, 4, 5, 6, 7 ) ),
						'graft_range'   => array(
							'type'                 => 'object',
							'additionalProperties' => false,
							'properties'           => array(
								'min' => array( 'type' => 'integer' ),
								'max' => array( 'type' => 'integer' ),
							),
							'required'             => array( 'min', 'max' ),
						),
						'summary'       => array( 'type' => 'string' ),
					),
					'required'             => array( 'norwood_stage', 'graft_range', 'summary' ),
				),
			),
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
		return new WP_REST_Response( array( 'ok' => false, 'error' => 'upstream_unreachable' ), 502 );
	}

	$code = wp_remote_retrieve_response_code( $response );
	$raw  = wp_remote_retrieve_body( $response );
	if ( 200 !== (int) $code ) {
		error_log( 'Estecapelli hair-analyze API ' . $code . ': ' . $raw );
		return new WP_REST_Response( array( 'ok' => false, 'error' => 'upstream_error' ), 502 );
	}

	$data = json_decode( $raw, true );
	// Find the text block holding the structured JSON and decode it.
	$analysis = null;
	if ( ! empty( $data['content'] ) && is_array( $data['content'] ) ) {
		foreach ( $data['content'] as $block ) {
			if ( isset( $block['type'], $block['text'] ) && 'text' === $block['type'] ) {
				$decoded = json_decode( $block['text'], true );
				if ( is_array( $decoded ) && isset( $decoded['norwood_stage'], $decoded['graft_range'] ) ) {
					$analysis = $decoded;
					break;
				}
			}
		}
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

	$name = sanitize_text_field( (string) $request->get_param( 'lead_name' ) );
	if ( '' === $name ) {
		return new WP_REST_Response( array( 'ok' => false, 'error' => 'missing_name' ), 422 );
	}
	$phone   = sanitize_text_field( (string) $request->get_param( 'lead_phone' ) );
	$email   = sanitize_email( (string) $request->get_param( 'lead_email' ) );
	$consent = '1' === (string) $request->get_param( 'lead_consent' );

	$analysis = json_decode( (string) $request->get_param( 'analysis_json' ), true );
	$norwood  = isset( $analysis['norwood_stage'] ) ? (int) $analysis['norwood_stage'] : 0;
	$grange   = '';
	if ( isset( $analysis['graft_range']['min'], $analysis['graft_range']['max'] ) ) {
		$grange = number_format_i18n( (int) $analysis['graft_range']['min'] ) . '–' . number_format_i18n( (int) $analysis['graft_range']['max'] );
	}
	$summary = isset( $analysis['summary'] ) ? sanitize_textarea_field( $analysis['summary'] ) : '';

	$source_label = __( 'AI Photo Hair Analysis', 'estecapelli' );

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
		'Norwood: ' . ( $norwood ?: '-' ),
		'Greft Aralığı: ' . ( $grange ?: '-' ),
		'Analiz: ' . ( $summary ?: '-' ),
		'Onay (KVKK/GDPR): ' . ( $consent ? 'Evet' : 'Hayır' ),
		'Dil: EN',
		'Kaynak: ' . $source_label,
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

	wp_mail( $to, $subject, implode( "\r\n", $lines ), $headers, $attachments );

	foreach ( $attachments as $tmp ) {
		@unlink( $tmp ); // phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged
	}

	return new WP_REST_Response( array( 'ok' => true ), 200 );
}
