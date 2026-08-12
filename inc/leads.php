<?php
/**
 * Leads — capture consultation requests from every form on the site (the
 * popup, the contact page, the footer quick-form and the in-page "form"
 * section), store them as a private `lead` CPT, notify the clinic by email,
 * and push them into Kommo CRM.
 *
 * CRM integration: Kommo ingests leads via its inbound email parser. Every
 * notification email is BCC'd to the parser address, so a new lead appears in
 * Kommo automatically — no REST API needed. (Same mechanism the Chile landing
 * page uses.) The visible recipient is lead@estecapelli.com.
 *
 * Source tracking: every submission records WHICH form it came from
 * (popup / contact / footer / section) and the originating page URL + title,
 * surfaced in the email "Kaynak" line and in wp-admin → Leads. This is the
 * critical bit for attributing leads to pages/campaigns.
 *
 * Secrets: SMTP credentials are read from wp-config.php constants and are NEVER
 * committed to the repo. See estecapelli_lead_configure_smtp() below.
 *
 * @package Estecapelli
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/* -------------------------------------------------------------------------
 * Configuration
 * ---------------------------------------------------------------------- */

/** Visible recipient of every lead notification. */
if ( ! defined( 'ESTECAPELLI_LEAD_TO' ) ) {
	define( 'ESTECAPELLI_LEAD_TO', 'lead@estecapelli.com' );
}
/** Kommo CRM inbound email-parser address — BCC'd so Kommo creates the lead. */
if ( ! defined( 'ESTECAPELLI_KOMMO_PARSER' ) ) {
	define( 'ESTECAPELLI_KOMMO_PARSER', '30722399.105794@parser.kommo.com' );
}
/** From address for outgoing notifications. */
if ( ! defined( 'ESTECAPELLI_MAIL_FROM' ) ) {
	define( 'ESTECAPELLI_MAIL_FROM', 'info@estecapelli.com' );
}

/* -------------------------------------------------------------------------
 * Shared anti-spam primitives
 *
 * No third-party CAPTCHA is used. Bots are kept out with a honeypot field, a
 * signed minimum form age, per-IP rate limiting and duplicate suppression —
 * all of which are invisible to a real visitor.
 * ---------------------------------------------------------------------- */

/**
 * Best available visitor IP. The value is only used for abuse throttling;
 * it is never stored with the lead.
 *
 * @return string
 */
function estecapelli_client_ip() {
	$candidates = array(
		isset( $_SERVER['HTTP_CF_CONNECTING_IP'] ) ? wp_unslash( $_SERVER['HTTP_CF_CONNECTING_IP'] ) : '',
		isset( $_SERVER['REMOTE_ADDR'] ) ? wp_unslash( $_SERVER['REMOTE_ADDR'] ) : '',
	);
	foreach ( $candidates as $candidate ) {
		$candidate = trim( (string) $candidate );
		if ( filter_var( $candidate, FILTER_VALIDATE_IP ) ) {
			return $candidate;
		}
	}
	return 'unknown';
}

/**
 * Fixed-window application rate limiter backed by WordPress transients.
 *
 * @param string $scope  Endpoint/form bucket.
 * @param int    $limit  Maximum attempts in the window.
 * @param int    $window Window length in seconds.
 * @return true|WP_Error
 */
function estecapelli_rate_limit( $scope, $limit, $window ) {
	$limit  = max( 1, (int) $limit );
	$window = max( 60, (int) $window );
	$key    = 'ec_rl_' . substr( hash( 'sha256', $scope . '|' . estecapelli_client_ip() ), 0, 32 );
	$now    = time();
	$state  = get_transient( $key );

	if ( ! is_array( $state ) || empty( $state['reset'] ) || (int) $state['reset'] <= $now ) {
		$state = array( 'count' => 0, 'reset' => $now + $window );
	}
	if ( (int) $state['count'] >= $limit ) {
		return new WP_Error(
			'rate_limited',
			__( 'Too many requests. Please wait a few minutes and try again.', 'estecapelli' ),
			array( 'retry_after' => max( 1, (int) $state['reset'] - $now ) )
		);
	}

	$state['count']++;
	set_transient( $key, $state, max( 1, (int) $state['reset'] - $now ) );
	return true;
}

/** Enqueue the footer's small, non-blocking AJAX controller. */
function estecapelli_enqueue_footer_lead_script() {
	wp_enqueue_script(
		'estecapelli-footer-lead',
		get_template_directory_uri() . '/assets/js/footer-lead.js',
		array(),
		function_exists( 'estecapelli_asset_ver' ) ? estecapelli_asset_ver( '/assets/js/footer-lead.js' ) : ESTECAPELLI_VERSION,
		true
	);
}
add_action( 'wp_enqueue_scripts', 'estecapelli_enqueue_footer_lead_script', 21 );

/**
 * The footer form controller must be ready before a visitor can submit. WP
 * Rocket's Delay JavaScript Execution otherwise rewrites this tag and waits for
 * the first interaction, which can race with a first submit click.
 */
function estecapelli_lead_scripts_skip_js_delay( $tag, $handle ) {
	$critical_handles = array( 'estecapelli-footer-lead' );
	if ( ! in_array( $handle, $critical_handles, true ) || false !== strpos( $tag, 'data-nowprocket' ) ) {
		return $tag;
	}
	return preg_replace( '/<script\b/', '<script data-nowprocket', $tag, 1 );
}
add_filter( 'script_loader_tag', 'estecapelli_lead_scripts_skip_js_delay', 10, 2 );

/**
 * Render the shared honeypot and signed form-age fields.
 *
 * @param string $source Form source/action suffix.
 */
function estecapelli_lead_antispam_fields( $source ) {
	$source  = sanitize_key( (string) $source );
	$started = time();
	$sig     = hash_hmac( 'sha256', $started . '|' . $source, wp_salt( 'nonce' ) );
	?>
	<div class="estecapelli-spam-trap" aria-hidden="true">
		<label>
			<?php esc_html_e( 'Leave this field empty', 'estecapelli' ); ?>
			<input type="text" name="lead_company_website" value="" tabindex="-1" autocomplete="off" />
		</label>
	</div>
	<input type="hidden" name="lead_form_started" value="<?php echo esc_attr( $started ); ?>" />
	<input type="hidden" name="lead_form_signature" value="<?php echo esc_attr( $sig ); ?>" />
	<?php
}

/**
 * Check fields that must be present before a lead reaches storage or email.
 *
 * @param array $data Sanitised lead data.
 * @return true|WP_Error
 */
function estecapelli_check_lead_antispam( array $data ) {
	$honeypot_raw = isset( $_POST['lead_company_website'] ) ? wp_unslash( $_POST['lead_company_website'] ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Missing
	$honeypot     = is_scalar( $honeypot_raw ) ? sanitize_text_field( $honeypot_raw ) : 'invalid';
	// The honeypot is the primary bot check: it is hidden from real visitors and
	// carries autocomplete="off", so anything filling it is automated.
	if ( '' !== $honeypot ) {
		return new WP_Error( 'spam_detected', 'Spam detected.' );
	}

	$started_raw = isset( $_POST['lead_form_started'] ) ? wp_unslash( $_POST['lead_form_started'] ) : 0; // phpcs:ignore WordPress.Security.NonceVerification.Missing
	$sig_raw     = isset( $_POST['lead_form_signature'] ) ? wp_unslash( $_POST['lead_form_signature'] ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Missing
	$started     = is_scalar( $started_raw ) ? absint( $started_raw ) : 0;
	$sig         = is_scalar( $sig_raw ) ? sanitize_text_field( $sig_raw ) : '';
	$expect  = $started ? hash_hmac( 'sha256', $started . '|' . $data['source'], wp_salt( 'nonce' ) ) : '';
	if ( ! $started || ! $sig || ! hash_equals( $expect, $sig ) ) {
		return new WP_Error( 'form_expired', __( 'Please refresh the page and submit the form again.', 'estecapelli' ) );
	}
	// A form filled in under three seconds was not filled in by a human reading
	// the page. Kept deliberately low so fast autofill never drops a real lead.
	if ( time() - $started < 3 ) {
		return new WP_Error( 'spam_detected', 'Spam detected.' );
	}

	return true;
}

/**
 * Apply global lead throttling and suppress exact replay submissions.
 *
 * @param array $data Sanitised lead data.
 * @return true|WP_Error
 */
function estecapelli_check_lead_limits( array $data ) {
	$limited = estecapelli_rate_limit(
		'lead_submission',
		(int) apply_filters( 'estecapelli_lead_rate_limit', 5 ),
		(int) apply_filters( 'estecapelli_lead_rate_window', 15 * MINUTE_IN_SECONDS )
	);
	if ( is_wp_error( $limited ) ) {
		return $limited;
	}

	$fingerprint = strtolower( implode( '|', array( $data['name'], $data['phone'], $data['email'], $data['message'] ) ) );
	$duplicate   = 'ec_lead_dup_' . substr( hash( 'sha256', $fingerprint ), 0, 32 );
	if ( get_transient( $duplicate ) ) {
		return new WP_Error( 'duplicate_lead', __( 'This request has already been received.', 'estecapelli' ) );
	}
	set_transient( $duplicate, 1, 30 * MINUTE_IN_SECONDS );

	return true;
}

/* -------------------------------------------------------------------------
 * Lead post type
 * ---------------------------------------------------------------------- */

/**
 * Register the private `lead` post type so submissions are visible in
 * wp-admin → Leads.
 */
function estecapelli_register_lead_cpt() {
	register_post_type(
		'lead',
		array(
			'labels'              => array(
				'name'          => __( 'Leads', 'estecapelli' ),
				'singular_name' => __( 'Lead', 'estecapelli' ),
				'menu_name'     => __( 'Leads', 'estecapelli' ),
			),
			'public'              => false,
			'show_ui'             => true,
			'show_in_menu'        => true,
			'menu_icon'           => 'dashicons-email-alt',
			'menu_position'       => 26,
			'capability_type'     => 'post',
			'map_meta_cap'        => true,
			'supports'            => array( 'title' ),
			'has_archive'         => false,
			'rewrite'             => false,
			'exclude_from_search' => true,
		)
	);
}
add_action( 'init', 'estecapelli_register_lead_cpt' );

/* -------------------------------------------------------------------------
 * Capture + processing (shared by the PRG and AJAX entry points)
 * ---------------------------------------------------------------------- */

/**
 * Pull and sanitise the lead fields from $_POST. Nonce verification is the
 * caller's responsibility (each entry point checks its own).
 *
 * @return array Sanitised lead data.
 */
function estecapelli_collect_lead() {
	$g = static function ( $key, $filter = 'text', $max_length = 255 ) {
		if ( ! isset( $_POST[ $key ] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Missing
			return '';
		}
		$raw = wp_unslash( $_POST[ $key ] ); // phpcs:ignore WordPress.Security.NonceVerification.Missing
		if ( ! is_scalar( $raw ) ) {
			return '';
		}
		switch ( $filter ) {
			case 'email':
				$value = sanitize_email( $raw );
				break;
			case 'textarea':
				$value = sanitize_textarea_field( $raw );
				break;
			case 'url':
				$value = esc_url_raw( $raw );
				break;
			default:
				$value = sanitize_text_field( $raw );
				break;
		}
		return function_exists( 'mb_substr' )
			? mb_substr( (string) $value, 0, $max_length )
			: substr( (string) $value, 0, $max_length );
	};

	return array(
		'name'       => $g( 'lead_name', 'text', 120 ),
		'phone'      => $g( 'lead_phone', 'text', 40 ),
		'email'      => $g( 'lead_email', 'email', 254 ),
		'treatment'  => $g( 'lead_treatment', 'text', 160 ),
		'message'    => $g( 'lead_message', 'textarea', 4000 ),
		'source'     => $g( 'lead_source', 'text', 32 ) ?: 'contact',
		'lang'       => $g( 'lead_lang', 'text', 10 ),
		'page_url'   => $g( 'lead_page_url', 'url', 1000 ),
		'page_title' => $g( 'lead_page_title', 'text', 250 ),
		'utm'        => array(
			'source'   => $g( 'utm_source', 'text', 200 ),
			'medium'   => $g( 'utm_medium', 'text', 200 ),
			'campaign' => $g( 'utm_campaign', 'text', 200 ),
			'content'  => $g( 'utm_content', 'text', 200 ),
			'term'     => $g( 'utm_term', 'text', 200 ),
		),
	);
}

/**
 * Basic server-side phone sanity check (defence-in-depth behind the browser's
 * intl-tel-input validation). We can't run libphonenumber in PHP, so we assert
 * the shape only: no letters, and a digit count within the E.164 range (a
 * leading country prefix included). This rejects garbage like "abc" or "12".
 *
 * @param string $phone Raw submitted phone.
 * @return bool
 */
function estecapelli_phone_looks_valid( $phone ) {
	// Letters are never valid in a phone number.
	if ( preg_match( '/[a-z]/i', $phone ) ) {
		return false;
	}
	$digits = preg_replace( '/\D+/', '', $phone );
	$len    = strlen( $digits );
	// E.164 allows up to 15 digits; shortest usable international numbers are ~8.
	return $len >= 8 && $len <= 15;
}

/**
 * Map a lead WP_Error code to a visitor-facing message (used by the classic,
 * no-JS POST path which redirects back with ?lead_error=<code>).
 *
 * @param string $code Error code from estecapelli_process_lead().
 * @return string Empty string for unknown codes.
 */
function estecapelli_lead_error_message( $code ) {
	$map = array(
		'invalid_phone'            => __( 'Please enter a valid phone number.', 'estecapelli' ),
		'invalid_email'            => __( 'Please enter a valid email address.', 'estecapelli' ),
		'missing_name'             => __( 'Please enter your name.', 'estecapelli' ),
		'missing_phone'            => __( 'Please enter your phone number.', 'estecapelli' ),
		'form_expired'             => __( 'Please refresh the page and submit the form again.', 'estecapelli' ),
		'rate_limited'             => __( 'Too many requests. Please wait a few minutes and try again.', 'estecapelli' ),
	);
	return $map[ $code ] ?? '';
}

/**
 * Two-letter uppercase language code for the CRM's `Dil` field.
 *
 * Order matters. The popup submits through admin-ajax.php and the hair widget
 * through the REST API — in both, WPML has no idea which language page the
 * visitor was actually on. So the value the form carried, and then the language
 * prefix of the page URL, are both trusted ahead of asking WPML.
 *
 * @param array $d Collected lead data (may be empty when rendering a form).
 * @return string Two uppercase letters, e.g. "TR". "EN" only as a last resort.
 */
function estecapelli_lead_language_code( array $d = array() ) {
	$candidates = array();

	if ( ! empty( $d['lang'] ) ) {
		$candidates[] = $d['lang'];
	}
	if ( ! empty( $d['page_url'] ) ) {
		$path = (string) wp_parse_url( $d['page_url'], PHP_URL_PATH );
		if ( preg_match( '#^/([a-z]{2})(?:/|$)#i', $path, $m ) ) {
			$candidates[] = $m[1];
		}
	}
	$candidates[] = (string) apply_filters( 'wpml_current_language', null );
	$candidates[] = defined( 'ICL_LANGUAGE_CODE' ) ? (string) ICL_LANGUAGE_CODE : '';
	$candidates[] = function_exists( 'determine_locale' ) ? (string) determine_locale() : (string) get_locale();

	foreach ( $candidates as $candidate ) {
		$code = strtoupper( substr( (string) preg_replace( '/[^a-z]/i', '', (string) $candidate ), 0, 2 ) );
		if ( 2 === strlen( $code ) ) {
			return $code;
		}
	}

	return 'EN';
}

/**
 * The page a lead came from, cleaned for the CRM.
 *
 * Document titles arrive as "Sapphire FUE Hair Transplant – Estecapelli"; Kommo
 * only wants the page part. The homepage's title IS the site name, so it falls
 * through to the URL path.
 *
 * @param array $d Collected lead data.
 * @return string
 */
function estecapelli_lead_page_name( array $d ) {
	$site  = trim( wp_strip_all_tags( (string) get_bloginfo( 'name' ) ) );
	$title = html_entity_decode( trim( (string) ( $d['page_title'] ?? '' ) ), ENT_QUOTES, 'UTF-8' );

	if ( '' !== $site ) {
		$title = (string) preg_replace(
			'/\s*[-–—|:]\s*' . preg_quote( $site, '/' ) . '\s*$/ui',
			'',
			$title
		);
	}
	$title = trim( $title );

	if ( '' !== $title && 0 !== strcasecmp( $title, $site ) ) {
		return $title;
	}

	$path = ! empty( $d['page_url'] ) ? trim( (string) wp_parse_url( $d['page_url'], PHP_URL_PATH ), '/' ) : '';
	// "" or a bare language segment ("fr") is that language's front page.
	if ( '' === $path || preg_match( '#^[a-z]{2}$#i', $path ) ) {
		return 'Homepage';
	}

	return $path;
}

/**
 * The `Kaynak` value sent to Kommo: "website - <page>".
 *
 * Kommo groups and filters leads on this string, so it stays a short, stable
 * shape. The richer, form-aware label below is what wp-admin and the email
 * subject use — no attribution detail is lost, it just isn't in this field.
 *
 * @param array $d Collected lead data.
 * @return string
 */
function estecapelli_lead_kommo_source( array $d ) {
	// The footer form is on every page, so the page it was sent from says
	// nothing about it. Kommo gets the form itself as the source instead.
	$source = ( 'footer' === ( $d['source'] ?? '' ) )
		? 'website - footer'
		: 'website - ' . estecapelli_lead_page_name( $d );

	return apply_filters( 'estecapelli_lead_kommo_source', $source, $d );
}

/**
 * Human-readable source label, e.g. "Popup · Hair Transplant (/en/hair-transplant)".
 * This is what makes a lead attributable to a page/campaign.
 */
function estecapelli_lead_source_label( array $d ) {
	$labels = array(
		'popup'   => __( 'Popup', 'estecapelli' ),
		'contact' => __( 'Contact page', 'estecapelli' ),
		'footer'  => __( 'Footer form', 'estecapelli' ),
		'section' => __( 'In-page form', 'estecapelli' ),
		'home'    => __( 'Homepage', 'estecapelli' ),
		'analysis' => __( 'Hair analysis', 'estecapelli' ),
	);
	$base = $labels[ $d['source'] ] ?? ucfirst( $d['source'] );

	$where = '';
	if ( ! empty( $d['page_title'] ) || ! empty( $d['page_url'] ) ) {
		$path  = $d['page_url'] ? wp_parse_url( $d['page_url'], PHP_URL_PATH ) : '';
		$title = $d['page_title'] ?: '';
		$where = trim( $title . ( $path ? " ($path)" : '' ) );
	}

	// The contact page is self-describing; others gain a lot from the page.
	return ( $where && 'contact' !== $d['source'] ) ? "$base · $where" : $base;
}

/**
 * Store the lead as a CPT and send the notification (To: lead@, BCC: Kommo).
 *
 * @return int|WP_Error Lead post ID on success.
 */
function estecapelli_process_lead( array $d ) {

	if ( '' === $d['name'] ) {
		return new WP_Error( 'missing_name', __( 'Name is required.', 'estecapelli' ) );
	}
	if ( '' === $d['phone'] ) {
		return new WP_Error( 'missing_phone', __( 'Please enter your phone number.', 'estecapelli' ) );
	}

	// Server-side safety net (the browser already blocks letters and checks the
	// per-country format via intl-tel-input, but never trust the client).
	if ( ! estecapelli_phone_looks_valid( $d['phone'] ) ) {
		return new WP_Error( 'invalid_phone', __( 'Please enter a valid phone number.', 'estecapelli' ) );
	}
	if ( '' !== $d['email'] && ! is_email( $d['email'] ) ) {
		return new WP_Error( 'invalid_email', __( 'Please enter a valid email address.', 'estecapelli' ) );
	}

	$antispam = estecapelli_check_lead_antispam( $d );
	if ( is_wp_error( $antispam ) ) {
		return $antispam;
	}
	$limits = estecapelli_check_lead_limits( $d );
	if ( is_wp_error( $limits ) ) {
		return $limits;
	}

	$source_label = estecapelli_lead_source_label( $d );

	// 1) Store.
	$lead_id = wp_insert_post(
		array(
			'post_type'   => 'lead',
			'post_status' => 'private',
			'post_title'  => $d['name'] . ( $d['phone'] ? ' — ' . $d['phone'] : '' ),
		),
		true
	);
	if ( ! is_wp_error( $lead_id ) ) {
		update_post_meta( $lead_id, 'lead_phone', $d['phone'] );
		update_post_meta( $lead_id, 'lead_email', $d['email'] );
		update_post_meta( $lead_id, 'lead_treatment', $d['treatment'] );
		update_post_meta( $lead_id, 'lead_message', $d['message'] );
		update_post_meta( $lead_id, 'lead_source', $source_label );
		update_post_meta( $lead_id, 'lead_page_url', $d['page_url'] );
		update_post_meta( $lead_id, 'lead_utm', $d['utm'] );
	}

	// 2) Notify clinic + Kommo CRM. Body uses the labelled format the Kommo
	//    parser expects (mirrors the Chile landing page), so fields map cleanly.
	$to      = apply_filters( 'estecapelli_lead_email_to', ESTECAPELLI_LEAD_TO, $d );
	$subject = sprintf(
		/* translators: 1: source label, 2: lead name */
		__( 'New lead (%1$s) — %2$s', 'estecapelli' ),
		$source_label,
		$d['name']
	);

	$lines = array(
		'Adı Soyadı: ' . $d['name'],
		'Email: ' . ( $d['email'] ?: '-' ),
		'Telefon: ' . ( $d['phone'] ?: '-' ),
	);
	if ( $d['treatment'] ) {
		$lines[] = 'İlgilenilen: ' . $d['treatment'];
	}
	$lines[] = 'Mesajınız: ' . ( $d['message'] ?: '-' );
	$lines[] = 'Dil: ' . strtoupper( substr( (string) apply_filters( 'estecapelli_lead_lang', estecapelli_lead_language_code( $d ), $d ), 0, 5 ) );
	$lines[] = 'Kaynak: ' . estecapelli_lead_kommo_source( $d );
	if ( $d['page_url'] ) {
		$lines[] = 'Sayfa: ' . $d['page_url'];
	}
	$lines[] = 'UTM Source: ' . $d['utm']['source'];
	$lines[] = 'UTM Medium: ' . $d['utm']['medium'];
	$lines[] = 'UTM Campaign: ' . $d['utm']['campaign'];
	$lines[] = 'UTM Content: ' . $d['utm']['content'];
	$lines[] = 'UTM Term: ' . $d['utm']['term'];

	$from_name = get_bloginfo( 'name' );
	$headers   = array(
		'Content-Type: text/plain; charset=UTF-8',
		sprintf( 'From: %s <%s>', $from_name, ESTECAPELLI_MAIL_FROM ),
		'Bcc: ' . ESTECAPELLI_KOMMO_PARSER,
	);
	if ( $d['email'] && is_email( $d['email'] ) ) {
		$headers[] = sprintf( 'Reply-To: %s <%s>', $d['name'], $d['email'] );
	}

	$sent = wp_mail( $to, $subject, implode( "\r\n", $lines ), apply_filters( 'estecapelli_lead_headers', $headers, $d ) );
	estecapelli_lead_record_delivery( $lead_id, $sent, $source_label );

	return $lead_id;
}

/**
 * Kommo only ever sees a lead that actually left as email (it ingests the BCC'd
 * copy), so a silent wp_mail() failure looks exactly like "the form works but
 * nothing reaches the CRM". Record the outcome on the lead and in the PHP log
 * instead of discarding it.
 *
 * @param int|WP_Error $lead_id Stored lead, if storing succeeded.
 * @param bool         $sent    wp_mail() return value.
 * @param string       $source  Source label, for the log line.
 */
function estecapelli_lead_record_delivery( $lead_id, $sent, $source ) {
	if ( ! is_wp_error( $lead_id ) && $lead_id ) {
		update_post_meta( $lead_id, 'lead_mail_sent', $sent ? '1' : '0' );
		if ( ! $sent ) {
			update_post_meta( $lead_id, 'lead_mail_error', estecapelli_lead_last_mail_error() );
		}
	}
	if ( ! $sent ) {
		error_log( // phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_error_log
			sprintf(
				'[estecapelli] Lead email NOT sent (source: %s, lead #%s) — CRM will not receive it. %s',
				$source,
				is_wp_error( $lead_id ) ? 'not stored' : (string) $lead_id,
				estecapelli_lead_last_mail_error()
			)
		);
	}
}

/** Remember the last PHPMailer failure so the lead can be annotated with it. */
function estecapelli_lead_capture_mail_error( $error ) {
	$GLOBALS['estecapelli_last_mail_error'] = is_wp_error( $error ) ? $error->get_error_message() : '';
}
add_action( 'wp_mail_failed', 'estecapelli_lead_capture_mail_error' );

/** Last wp_mail failure reason for this request, if any. */
function estecapelli_lead_last_mail_error() {
	$error = isset( $GLOBALS['estecapelli_last_mail_error'] ) ? (string) $GLOBALS['estecapelli_last_mail_error'] : '';
	return $error ?: 'No PHPMailer error was reported — check SMTP settings and the host mail log.';
}

/* -------------------------------------------------------------------------
 * SMTP — configure wp_mail from wp-config constants (so the Kommo parser
 * actually receives the mail; PHP mail() is unreliable for external delivery).
 *
 * Add to wp-config.php (NOT committed to the repo):
 *   define( 'ESTECAPELLI_SMTP_HOST', 'mail.estecapelli.com' );
 *   define( 'ESTECAPELLI_SMTP_USER', 'info@estecapelli.com' );
 *   define( 'ESTECAPELLI_SMTP_PASS', '••••••••' );
 *   define( 'ESTECAPELLI_SMTP_PORT', 465 );          // optional, default 465
 *   define( 'ESTECAPELLI_SMTP_SECURE', 'ssl' );      // optional: 'ssl' | 'tls'
 * If ESTECAPELLI_SMTP_HOST is undefined, wp_mail falls back to the server /
 * any SMTP plugin already installed.
 * ---------------------------------------------------------------------- */
function estecapelli_lead_configure_smtp( $phpmailer ) {
	if ( ! defined( 'ESTECAPELLI_SMTP_HOST' ) || ! ESTECAPELLI_SMTP_HOST ) {
		return;
	}
	$phpmailer->isSMTP();
	$phpmailer->Host       = ESTECAPELLI_SMTP_HOST;
	$phpmailer->SMTPAuth   = true;
	$phpmailer->Username   = defined( 'ESTECAPELLI_SMTP_USER' ) ? ESTECAPELLI_SMTP_USER : '';
	$phpmailer->Password   = defined( 'ESTECAPELLI_SMTP_PASS' ) ? ESTECAPELLI_SMTP_PASS : '';
	$phpmailer->Port       = defined( 'ESTECAPELLI_SMTP_PORT' ) ? (int) ESTECAPELLI_SMTP_PORT : 465;
	$phpmailer->SMTPSecure = defined( 'ESTECAPELLI_SMTP_SECURE' ) ? ESTECAPELLI_SMTP_SECURE : 'ssl';
	$phpmailer->CharSet    = 'UTF-8';
}
add_action( 'phpmailer_init', 'estecapelli_lead_configure_smtp' );

/* -------------------------------------------------------------------------
 * Entry point 1 — classic POST (no-JS / progressive enhancement).
 * Both the inline forms and a JS-disabled popup post here.
 * ---------------------------------------------------------------------- */
function estecapelli_handle_lead() {
	if ( empty( $_POST['estecapelli_lead_nonce'] ) || ! is_scalar( $_POST['estecapelli_lead_nonce'] ) ) {
		return;
	}
	if ( ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['estecapelli_lead_nonce'] ) ), 'estecapelli_lead' ) ) {
		return;
	}

	$data = estecapelli_collect_lead();
	$result = estecapelli_process_lead( $data );
	$silent = is_wp_error( $result ) && in_array( $result->get_error_code(), array( 'spam_detected', 'duplicate_lead' ), true );

	// Post/Redirect/Get → return to the submitting page. On a validation error
	// come back with ?error=… instead of ?sent=1 so we never fake success.
	$return = isset( $_POST['lead_return'] ) ? wp_validate_redirect( esc_url_raw( wp_unslash( $_POST['lead_return'] ) ), '' ) : '';
	$base   = $return ?: estecapelli_indexed_url( '/en/contact' );

	// The footer form sits on every page alongside whatever else that page
	// renders, so the result has to say which form it belongs to. Without the
	// marker an in-page form section would also claim a footer submit's success
	// or error, because both read the same query string.
	$is_footer = 'footer' === $data['source'];
	$anchor    = $return ? ( $is_footer ? '#footer-lead' : '#lead-form' ) : '#contact-form';
	$args      = is_wp_error( $result ) && ! $silent
		? array( 'lead_error' => rawurlencode( $result->get_error_code() ) )
		: array( 'sent' => '1' );
	if ( $is_footer ) {
		$args['lead_form'] = 'footer';
	}
	$redirect = add_query_arg( $args, $base ) . $anchor;
	wp_safe_redirect( $redirect );
	exit;
}
add_action( 'template_redirect', 'estecapelli_handle_lead', 5 );

/* -------------------------------------------------------------------------
 * Entry point 2 — AJAX (the popup, so the visitor never leaves the page).
 * ---------------------------------------------------------------------- */
function estecapelli_handle_lead_ajax() {
	check_ajax_referer( 'estecapelli_lead_ajax', 'nonce' );

	$data = estecapelli_collect_lead();
	$result = estecapelli_process_lead( $data );
	if ( is_wp_error( $result ) ) {
		if ( in_array( $result->get_error_code(), array( 'spam_detected', 'duplicate_lead' ), true ) ) {
			wp_send_json_success(
				array( 'message' => __( 'Thank you! Your request has been received — our team will contact you shortly.', 'estecapelli' ) )
			);
		}
		$status = 'rate_limited' === $result->get_error_code() ? 429 : 422;
		wp_send_json_error( array( 'message' => $result->get_error_message() ), $status );
	}

	wp_send_json_success(
		array( 'message' => __( 'Thank you! Your request has been received — our team will contact you shortly.', 'estecapelli' ) )
	);
}
add_action( 'wp_ajax_estecapelli_lead', 'estecapelli_handle_lead_ajax' );
add_action( 'wp_ajax_nopriv_estecapelli_lead', 'estecapelli_handle_lead_ajax' );

/* -------------------------------------------------------------------------
 * Helper — current page context for the hidden tracking fields on inline
 * forms (the popup fills these from JS instead).
 *
 * @return array{url:string,title:string}
 * ---------------------------------------------------------------------- */
function estecapelli_lead_context() {
	$url = is_singular() ? get_permalink() : '';
	if ( ! $url ) {
		$req = isset( $_SERVER['REQUEST_URI'] ) ? wp_unslash( $_SERVER['REQUEST_URI'] ) : '';
		$url = $req ? home_url( $req ) : home_url( '/' );
	}
	$title = wp_strip_all_tags( wp_get_document_title() );
	return array( 'url' => $url, 'title' => $title );
}

/**
 * Print the shared hidden tracking inputs (page url/title + UTM placeholders).
 * JS tops up the UTM fields from the query string on every page.
 */
function estecapelli_lead_tracking_fields( $source, $form_type = 'inline' ) {
	$ctx = estecapelli_lead_context();
	printf( '<input type="hidden" name="lead_source" value="%s" />', esc_attr( $source ) );
	// Stamped server-side: the page is already cached per language, and the
	// AJAX/REST endpoints these forms post to can't work the language out.
	printf( '<input type="hidden" name="lead_lang" value="%s" />', esc_attr( estecapelli_lead_language_code() ) );
	printf( '<input type="hidden" name="lead_page_url" value="%s" />', esc_url( $ctx['url'] ) );
	printf( '<input type="hidden" name="lead_page_title" value="%s" />', esc_attr( $ctx['title'] ) );
	foreach ( array( 'source', 'medium', 'campaign', 'content', 'term' ) as $k ) {
		printf( '<input type="hidden" name="utm_%s" value="" />', esc_attr( $k ) );
	}
	estecapelli_lead_antispam_fields( $source );
}

/* -------------------------------------------------------------------------
 * Route special pages (by slug) to their dedicated templates.
 * ---------------------------------------------------------------------- */
function estecapelli_page_template_router( $template ) {
	if ( ! is_page() ) {
		return $template;
	}
	$map     = array(
		'contact'      => 'page-contact.php',
		'blog'         => 'page-blog.php',
		'before-after' => 'page-before-after.php',
	);
	$page_id = get_queried_object_id();
	$slug    = get_post_field( 'post_name', $page_id );

	// Templates are keyed by the default-language (English) slug. A translated
	// page carries a localized slug (e.g. avant-apres, prima-e-dopo, contatti),
	// so resolve it back to its English original before matching — otherwise the
	// translation falls through to the generic page.php and renders only its ACF
	// hero instead of the coded template.
	if ( ! isset( $map[ $slug ] ) ) {
		$default_lang = apply_filters( 'wpml_default_language', null );
		if ( $default_lang ) {
			$source_id = (int) apply_filters( 'wpml_object_id', $page_id, 'page', true, $default_lang );
			if ( $source_id && $source_id !== (int) $page_id ) {
				$source_slug = get_post_field( 'post_name', $source_id );
				if ( $source_slug && isset( $map[ $source_slug ] ) ) {
					$slug = $source_slug;
				}
			}
		}
	}

	if ( isset( $map[ $slug ] ) ) {
		$found = locate_template( $map[ $slug ] );
		if ( $found ) {
			return $found;
		}
	}
	return $template;
}
add_filter( 'template_include', 'estecapelli_page_template_router' );

/* -------------------------------------------------------------------------
 * Admin columns for quick triage.
 * ---------------------------------------------------------------------- */
add_filter( 'manage_lead_posts_columns', function ( $cols ) {
	$new                   = array( 'cb' => $cols['cb'] ?? '', 'title' => __( 'Name / Phone', 'estecapelli' ) );
	$new['lead_email']     = __( 'Email', 'estecapelli' );
	$new['lead_treatment'] = __( 'Interested in', 'estecapelli' );
	$new['lead_source']    = __( 'Source', 'estecapelli' );
	// A lead reaches Kommo only via the notification email, so whether that email
	// left the server is the one thing this screen could not previously answer.
	$new['lead_mail_sent'] = __( 'Sent to CRM', 'estecapelli' );
	$new['date']           = __( 'Received', 'estecapelli' );
	return $new;
} );
add_action( 'manage_lead_posts_custom_column', function ( $col, $post_id ) {
	if ( in_array( $col, array( 'lead_email', 'lead_treatment', 'lead_source' ), true ) ) {
		echo esc_html( get_post_meta( $post_id, $col, true ) );
		return;
	}
	if ( 'lead_mail_sent' !== $col ) {
		return;
	}
	$state = get_post_meta( $post_id, 'lead_mail_sent', true );
	if ( '' === $state ) {
		echo '—';
		return;
	}
	if ( '1' === $state ) {
		echo '✅ ' . esc_html__( 'Sent', 'estecapelli' );
		return;
	}
	printf(
		'<span style="color:#b32d2e">❌ %s</span><br /><small>%s</small>',
		esc_html__( 'Failed', 'estecapelli' ),
		esc_html( (string) get_post_meta( $post_id, 'lead_mail_error', true ) )
	);
}, 10, 2 );
