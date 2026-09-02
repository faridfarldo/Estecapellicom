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
 * Bots are slowed down by per-IP rate limiting, a signed form stamp, a honeypot,
 * a submission timer and an invisible Cloudflare Turnstile challenge — none of
 * which a real visitor ever sees or has to click.
 *
 * Rule for everything in here: NEVER discard a submission silently. A lost
 * enquiry costs the clinic a patient; a spam email costs it ten seconds. Only
 * the signed-stamp check rejects, and it does so with a message the visitor can
 * act on. The heuristics merely flag.
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
	// The guard must be able to mint its token on the visitor's first focus; if
	// WP Rocket holds it back until "first interaction" it can lose that race and
	// a genuine lead arrives looking tokenless. Turnstile is in the same boat: a
	// widget that has not run by the time the visitor submits produces no token,
	// and the submission is then scored for a challenge it never got to answer.
	$critical_handles = array( 'estecapelli-footer-lead', 'estecapelli-lead-guard', 'estecapelli-turnstile' );
	if ( ! in_array( $handle, $critical_handles, true ) ) {
		return $tag;
	}
	// Cloudflare's implicit renderer expects to be loaded out of the parser's
	// way; wp_enqueue_script() has no portable way to put both attributes on a
	// registered handle, so they go on here.
	if ( 'estecapelli-turnstile' === $handle && false === strpos( $tag, ' async' ) ) {
		$tag = preg_replace( '/<script\b/', '<script async defer', $tag, 1 );
	}
	if ( false !== strpos( $tag, 'data-nowprocket' ) ) {
		return $tag;
	}
	return preg_replace( '/<script\b/', '<script data-nowprocket', $tag, 1 );
}
add_filter( 'script_loader_tag', 'estecapelli_lead_scripts_skip_js_delay', 10, 2 );

/* -------------------------------------------------------------------------
 * Cloudflare Turnstile — the one challenge layer, and an optional one
 *
 * Turnstile runs invisibly in front of the scoring in inc/lead-guard.php: the
 * widget solves itself in the visitor's browser, the token it produces rides
 * along with the form, and this file verifies it server-side. A real visitor
 * sees nothing and clicks nothing.
 *
 * Two rules keep it inside the theme's "never discard a real enquiry"
 * philosophy:
 *
 *   1. No keys, no Turnstile. If either constant is missing the entire layer is
 *      switched off — the script is not loaded, no widget is printed and no
 *      submission is ever scored for failing a challenge it was never shown.
 *   2. A failed or missing challenge NEVER rejects. It is a weighted signal fed
 *      to estecapelli_lead_assess(), so the worst it can do is quarantine a lead
 *      the clinic releases with one click — the same lane a link in the message
 *      field lands in.
 *
 * Add to wp-config.php (NOT committed to the repo — same handling as the SMTP
 * credentials below):
 *   define( 'ESTECAPELLI_TURNSTILE_SITE_KEY', '0x4AAAAAAA…' );
 *   define( 'ESTECAPELLI_TURNSTILE_SECRET', '0x4AAAAAAA…' );
 * Both come from dash.cloudflare.com → Turnstile → the estecapelli.com widget,
 * which must be created in Invisible mode. See docs/CLOUDFLARE-ANTISPAM.md.
 * ---------------------------------------------------------------------- */

/** Public site key, or '' when Turnstile is not configured. */
function estecapelli_turnstile_site_key() {
	return defined( 'ESTECAPELLI_TURNSTILE_SITE_KEY' ) ? trim( (string) ESTECAPELLI_TURNSTILE_SITE_KEY ) : '';
}

/** Secret key, or '' when Turnstile is not configured. */
function estecapelli_turnstile_secret() {
	return defined( 'ESTECAPELLI_TURNSTILE_SECRET' ) ? trim( (string) ESTECAPELLI_TURNSTILE_SECRET ) : '';
}

/**
 * Is the layer switched on? Half a key pair is the same as none: verifying with
 * a missing secret would fail every submission, and a widget with no site key
 * would mint no token for it to fail on.
 *
 * @return bool
 */
function estecapelli_turnstile_enabled() {
	return '' !== estecapelli_turnstile_site_key() && '' !== estecapelli_turnstile_secret();
}

/**
 * Load Cloudflare's script, once, and only from a page that actually printed a
 * lead form. Enqueuing from the render of the form itself is what keeps it off
 * every other page: all of the theme's lead forms are output before wp_footer(),
 * which is where this handle prints.
 */
function estecapelli_turnstile_enqueue() {
	if ( ! estecapelli_turnstile_enabled() || wp_script_is( 'estecapelli-turnstile', 'enqueued' ) ) {
		return;
	}
	wp_enqueue_script(
		'estecapelli-turnstile',
		'https://challenges.cloudflare.com/turnstile/v0/api.js',
		array(),
		null, // Cloudflare versions the endpoint itself; ?ver= would only break its cache.
		true
	);
}

/**
 * Print one invisible widget for a form.
 *
 * Implicit rendering: Cloudflare's script finds every .cf-turnstile element on
 * load and drops its token into a hidden `cf-turnstile-response` input inside
 * the surrounding <form>. That is why this works unchanged for the classic POST
 * forms and for the popup and footer, which both send new FormData( form ).
 *
 * @param string $source Form source, used as the Turnstile analytics label.
 */
function estecapelli_turnstile_field( $source ) {
	if ( ! estecapelli_turnstile_enabled() ) {
		return;
	}
	estecapelli_turnstile_enqueue();
	printf(
		// interaction-only: even a site key configured as "managed" stays out of
		// the visitor's way unless Cloudflare genuinely wants a click.
		// refresh-expired: a token dies after five minutes, and someone reading a
		// treatment page before filling the form in takes longer than that.
		'<div class="cf-turnstile" data-sitekey="%s" data-action="%s" data-appearance="interaction-only" data-refresh-expired="auto" data-response-field-name="cf-turnstile-response"></div>',
		esc_attr( estecapelli_turnstile_site_key() ),
		esc_attr( 'lead-' . $source )
	);
}

/**
 * Verify the submitted challenge with Cloudflare.
 *
 * Memoised: both entry points reach the scorer through this, and one submission
 * must cost exactly one HTTP request. The verdict is also cached per token,
 * because Turnstile tokens are single-use — a visitor who trips a validation
 * error ("please enter a valid phone number") and submits the same form again
 * would otherwise have their perfectly good token come back as
 * `timeout-or-duplicate` on the second try.
 *
 * Fails open everywhere it can: no keys, no form stamp, or Cloudflare
 * unreachable all return no signal at all.
 *
 * @return string Empty when the challenge passed (or did not apply); otherwise
 *                the signal text for inc/lead-guard.php.
 */
function estecapelli_lead_turnstile_signal() {
	static $signal = null;
	if ( null !== $signal ) {
		return $signal;
	}
	$signal = '';

	if ( ! estecapelli_turnstile_enabled() ) {
		return $signal;
	}

	// The widget is printed by estecapelli_lead_antispam_fields(), always
	// alongside the signed form stamp, so a submission arriving without a stamp
	// was never handed a challenge to solve. That is the AI photo widget, which
	// posts JSON to the REST API: scoring it for a missing token would quarantine
	// every patient who sends photographs of their own head.
	if ( ! isset( $_POST['lead_form_started'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Missing
		return $signal;
	}

	$raw   = isset( $_POST['cf-turnstile-response'] ) ? wp_unslash( $_POST['cf-turnstile-response'] ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Missing
	$token = is_scalar( $raw ) ? sanitize_text_field( $raw ) : '';
	if ( '' === $token ) {
		$signal = 'missing Turnstile challenge';
		return $signal;
	}

	$cache_key = 'ec_ts_' . substr( hash( 'sha256', $token ), 0, 32 );
	$cached    = get_transient( $cache_key );
	if ( false !== $cached ) {
		$signal = '1' === $cached ? '' : 'failed Turnstile challenge';
		return $signal;
	}

	$body = array(
		'secret'   => estecapelli_turnstile_secret(),
		'response' => $token,
	);
	// Only when we actually have one — estecapelli_client_ip() answers 'unknown'
	// behind a proxy that strips both headers, and siteverify rejects the call
	// outright rather than ignoring an unparseable address.
	$ip = estecapelli_client_ip();
	if ( filter_var( $ip, FILTER_VALIDATE_IP ) ) {
		$body['remoteip'] = $ip;
	}

	$response = wp_remote_post(
		'https://challenges.cloudflare.com/turnstile/v0/siteverify',
		array(
			'timeout' => 5,
			'body'    => $body,
		)
	);

	$decoded = is_wp_error( $response ) ? null : json_decode( (string) wp_remote_retrieve_body( $response ), true );
	if ( ! is_array( $decoded ) || ! isset( $decoded['success'] ) ) {
		// Cloudflare is down, slow, or blocked by the host's egress rules. Say
		// nothing and let the other layers decide — an outage at Cloudflare must
		// never cost the clinic a patient. Not cached: the next submit retries.
		error_log( // phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_error_log
			sprintf(
				'[estecapelli] Turnstile verification unavailable (%s) — submission scored without it.',
				is_wp_error( $response ) ? $response->get_error_message() : 'unreadable siteverify response'
			)
		);
		return $signal;
	}

	$passed = ! empty( $decoded['success'] );
	// Ten minutes covers a resubmit after a validation error and is far short of
	// anything a replay run could use — a token that passed once is already
	// spent as far as Cloudflare is concerned.
	set_transient( $cache_key, $passed ? '1' : '0', 10 * MINUTE_IN_SECONDS );

	if ( ! $passed ) {
		$codes = isset( $decoded['error-codes'] ) && is_array( $decoded['error-codes'] )
			? implode( ', ', array_map( 'sanitize_text_field', $decoded['error-codes'] ) )
			: 'no error code';
		error_log( // phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_error_log
			sprintf( '[estecapelli] Turnstile rejected a submission (%s) — scored, not blocked.', $codes )
		);
		$signal = 'failed Turnstile challenge';
	}

	return $signal;
}

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
			<?php // The name must mean nothing to a browser. "lead_company_website"
				// meant "organization" and "url" to Chrome's autofill and to every
				// password manager, so they filled the trap for real visitors. ?>
			<input type="text" name="lead_delta" value="" tabindex="-1" autocomplete="off" />
		</label>
	</div>
	<input type="hidden" name="lead_form_started" value="<?php echo esc_attr( $started ); ?>" />
	<input type="hidden" name="lead_form_signature" value="<?php echo esc_attr( $sig ); ?>" />
	<?php
	// Filled in by assets/js/lead-guard.js on the visitor's first interaction.
	// Deliberately empty in the HTML: a value baked into a cached page would be
	// exactly as free for a bot to copy as the two fields above.
	?>
	<input type="hidden" name="lead_token" value="" />
	<?php
	// Invisible Cloudflare challenge. Prints nothing at all when the keys are
	// absent from wp-config.php, so a site without them behaves exactly as it
	// did before.
	estecapelli_turnstile_field( $source );
}

/**
 * Integrity check: the form must carry the signed stamp this site rendered.
 * This is the only anti-spam condition allowed to REJECT a submission, and it
 * fails loudly ("please refresh"), never silently — a visitor who sees an
 * error can retry, a visitor whose lead is quietly binned cannot.
 *
 * @param array $data Sanitised lead data.
 * @return true|WP_Error
 */
function estecapelli_check_lead_antispam( array $data ) {
	$started_raw = isset( $_POST['lead_form_started'] ) ? wp_unslash( $_POST['lead_form_started'] ) : 0; // phpcs:ignore WordPress.Security.NonceVerification.Missing
	$sig_raw     = isset( $_POST['lead_form_signature'] ) ? wp_unslash( $_POST['lead_form_signature'] ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Missing
	$started     = is_scalar( $started_raw ) ? absint( $started_raw ) : 0;
	$sig         = is_scalar( $sig_raw ) ? sanitize_text_field( $sig_raw ) : '';
	$expect  = $started ? hash_hmac( 'sha256', $started . '|' . $data['source'], wp_salt( 'nonce' ) ) : '';
	if ( ! $started || ! $sig || ! hash_equals( $expect, $sig ) ) {
		return new WP_Error( 'form_expired', __( 'Please refresh the page and submit the form again.', 'estecapelli' ) );
	}

	// Resolve the Cloudflare challenge here, on the one path that is allowed to
	// say no — and deliberately do not use it to say no. The verdict is memoised
	// and read back by estecapelli_lead_assess(), which scores a failed or absent
	// challenge instead of rejecting it, so a visitor whose browser could not run
	// Turnstile still reaches the clinic. This line never produces a WP_Error.
	estecapelli_lead_turnstile_signal();

	return true;
}

/**
 * Heuristic spam signals — the honeypot and the "filled in impossibly fast"
 * timer.
 *
 * These deliberately do NOT discard anything. Both misfire on real people:
 * browsers and password managers autofill off-screen inputs, and a visitor who
 * pastes their details is through the form in under three seconds. One hair
 * transplant enquiry is worth far more than the nuisance of a flagged spam
 * mail, so a suspicious lead is delivered like any other, marked so the clinic
 * can judge it.
 *
 * @param array $data Sanitised lead data.
 * @return string[] Human-readable signals; empty means clean.
 */
function estecapelli_lead_spam_signals( array $data ) {
	$signals = array();

	$honeypot_raw = isset( $_POST['lead_delta'] ) ? wp_unslash( $_POST['lead_delta'] ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Missing
	$honeypot     = is_scalar( $honeypot_raw ) ? sanitize_text_field( $honeypot_raw ) : 'non-scalar value';
	if ( '' !== $honeypot ) {
		$signals[] = 'honeypot field was filled';
	}

	$started_raw = isset( $_POST['lead_form_started'] ) ? wp_unslash( $_POST['lead_form_started'] ) : 0; // phpcs:ignore WordPress.Security.NonceVerification.Missing
	$started     = is_scalar( $started_raw ) ? absint( $started_raw ) : 0;
	if ( $started && time() - $started < 3 ) {
		$signals[] = 'submitted less than 3 seconds after the page was rendered';
	}

	return $signals;
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

	// Suppress an identical resend — a double-clicked button or a replayed POST.
	// Kept short on purpose: the window only has to cover an accidental repeat,
	// and anything longer silently swallows a visitor who genuinely submits
	// again (or the clinic testing its own forms).
	$window      = (int) apply_filters( 'estecapelli_lead_duplicate_window', 5 * MINUTE_IN_SECONDS );
	$fingerprint = strtolower( implode( '|', array( $data['name'], $data['phone'], $data['email'], $data['message'] ) ) );
	$duplicate   = 'ec_lead_dup_' . substr( hash( 'sha256', $fingerprint ), 0, 32 );
	if ( get_transient( $duplicate ) ) {
		error_log( // phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_error_log
			sprintf( '[estecapelli] Lead suppressed as an identical resend within %d min: %s', (int) round( $window / 60 ), $data['name'] )
		);
		return new WP_Error( 'duplicate_lead', __( 'This request has already been received.', 'estecapelli' ) );
	}
	set_transient( $duplicate, 1, $window );

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
		error_log( // phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_error_log
			sprintf( '[estecapelli] Lead rejected (%s) from source "%s".', $antispam->get_error_code(), $d['source'] )
		);
		return $antispam;
	}
	$limits = estecapelli_check_lead_limits( $d );
	if ( is_wp_error( $limits ) ) {
		return $limits;
	}

	// Scored, never dropped — see inc/lead-guard.php. A quarantined lead is
	// stored and emailed to the clinic exactly like any other; the only thing it
	// loses is the BCC that would have created a Kommo record, and wp-admin can
	// hand it that back with one click.
	$assessment   = function_exists( 'estecapelli_lead_assess' )
		? estecapelli_lead_assess( $d )
		: array( 'signals' => estecapelli_lead_spam_signals( $d ), 'score' => 0, 'quarantine' => false );
	$spam_signals = $assessment['signals'];
	$quarantine   = ! empty( $assessment['quarantine'] );

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
		if ( $spam_signals ) {
			update_post_meta( $lead_id, 'lead_spam_signals', implode( '; ', $spam_signals ) );
			update_post_meta( $lead_id, 'lead_spam_score', (int) $assessment['score'] );
		}
		if ( $quarantine ) {
			update_post_meta( $lead_id, 'lead_is_spam', '1' );
		}
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
	// Only a lead that was actually held back is marked, because only that one
	// needs the clinic to do something about it. A low score on a lead that
	// reached Kommo anyway is tuning information, not news: it stays in the log
	// and on the lead in wp-admin, and out of an inbox where "[?]" beside a real
	// patient's name is just alarming.
	if ( $quarantine ) {
		$subject = '[SPAM?] ' . $subject;
	}
	if ( $spam_signals ) {
		error_log( // phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_error_log
			sprintf(
				'[estecapelli] Lead %s (score %d: %s): %s',
				$quarantine ? 'QUARANTINED — not sent to Kommo' : 'flagged but delivered',
				(int) $assessment['score'],
				implode( '; ', $spam_signals ),
				$d['name']
			)
		);
	}

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

	$body      = implode( "\r\n", $lines );
	$from_name = get_bloginfo( 'name' );
	$headers   = array(
		'Content-Type: text/plain; charset=UTF-8',
		sprintf( 'From: %s <%s>', $from_name, ESTECAPELLI_MAIL_FROM ),
	);
	// The single line that keeps spam out of Kommo: the CRM only ever sees a lead
	// through this BCC, so withholding it withholds the CRM record — while the
	// clinic's own copy above goes out either way.
	if ( ! $quarantine ) {
		$headers[] = 'Bcc: ' . ESTECAPELLI_KOMMO_PARSER;
	}
	if ( $d['email'] && is_email( $d['email'] ) ) {
		$headers[] = sprintf( 'Reply-To: %s <%s>', $d['name'], $d['email'] );
	}

	if ( $quarantine ) {
		// Stored before the explanation below is appended, so releasing a false
		// positive re-sends the exact mail the Kommo parser expects rather than a
		// rebuilt approximation of it.
		if ( ! is_wp_error( $lead_id ) ) {
			update_post_meta( $lead_id, 'lead_mail_subject', $subject );
			update_post_meta( $lead_id, 'lead_mail_body', $body );
		}
		$body .= "\r\n\r\n--\r\n"
			. 'NOT sent to the CRM — scored as automated spam (' . implode( '; ', $spam_signals ) . ").\r\n"
			. 'If this is a real enquiry, release it from wp-admin → Leads → Quarantined.';
	}

	$sent = wp_mail( $to, $subject, $body, apply_filters( 'estecapelli_lead_headers', $headers, $d ) );
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
	$silent = is_wp_error( $result ) && in_array( $result->get_error_code(), array( 'duplicate_lead' ), true );

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
		if ( in_array( $result->get_error_code(), array( 'duplicate_lead' ), true ) ) {
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
		$flags = 'lead_source' === $col ? (string) get_post_meta( $post_id, 'lead_spam_signals', true ) : '';
		if ( $flags ) {
			// Two very different states share this cell: a lead that scored but
			// still reached Kommo, and one that was held back from it.
			$held = '1' === get_post_meta( $post_id, 'lead_is_spam', true );
			printf(
				'<br /><small title="%s" style="color:%s">%s %s</small>',
				esc_attr( $flags ),
				$held ? '#b32d2e' : 'inherit',
				$held ? '⛔' : '⚠',
				$held
					? esc_html__( 'Quarantined — not sent to CRM', 'estecapelli' )
					: esc_html__( 'Possible spam (still sent)', 'estecapelli' )
			);
		}
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
