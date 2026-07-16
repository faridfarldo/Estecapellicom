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
	$g = static function ( $key, $filter = 'text' ) {
		if ( ! isset( $_POST[ $key ] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Missing
			return '';
		}
		$raw = wp_unslash( $_POST[ $key ] ); // phpcs:ignore WordPress.Security.NonceVerification.Missing
		switch ( $filter ) {
			case 'email':
				return sanitize_email( $raw );
			case 'textarea':
				return sanitize_textarea_field( $raw );
			case 'url':
				return esc_url_raw( $raw );
			default:
				return sanitize_text_field( $raw );
		}
	};

	return array(
		'name'       => $g( 'lead_name' ),
		'phone'      => $g( 'lead_phone' ),
		'email'      => $g( 'lead_email', 'email' ),
		'treatment'  => $g( 'lead_treatment' ),
		'message'    => $g( 'lead_message', 'textarea' ),
		'source'     => $g( 'lead_source' ) ?: 'contact',
		'page_url'   => $g( 'lead_page_url', 'url' ),
		'page_title' => $g( 'lead_page_title' ),
		'utm'        => array(
			'source'   => $g( 'utm_source' ),
			'medium'   => $g( 'utm_medium' ),
			'campaign' => $g( 'utm_campaign' ),
			'content'  => $g( 'utm_content' ),
			'term'     => $g( 'utm_term' ),
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
		'invalid_phone' => __( 'Please enter a valid phone number.', 'estecapelli' ),
		'invalid_email' => __( 'Please enter a valid email address.', 'estecapelli' ),
		'missing_name'  => __( 'Please enter your name.', 'estecapelli' ),
	);
	return $map[ $code ] ?? '';
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

	// Server-side safety net (the browser already blocks letters and checks the
	// per-country format via intl-tel-input, but never trust the client).
	if ( '' !== $d['phone'] && ! estecapelli_phone_looks_valid( $d['phone'] ) ) {
		return new WP_Error( 'invalid_phone', __( 'Please enter a valid phone number.', 'estecapelli' ) );
	}
	if ( '' !== $d['email'] && ! is_email( $d['email'] ) ) {
		return new WP_Error( 'invalid_email', __( 'Please enter a valid email address.', 'estecapelli' ) );
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
	$lines[] = 'Dil: ' . strtoupper( substr( (string) apply_filters( 'estecapelli_lead_lang', 'EN', $d ), 0, 5 ) );
	$lines[] = 'Kaynak: ' . $source_label;
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

	wp_mail( $to, $subject, implode( "\r\n", $lines ), apply_filters( 'estecapelli_lead_headers', $headers, $d ) );

	return $lead_id;
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
	if ( empty( $_POST['estecapelli_lead_nonce'] ) ) {
		return;
	}
	if ( ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['estecapelli_lead_nonce'] ) ), 'estecapelli_lead' ) ) {
		return;
	}

	$data = estecapelli_collect_lead();
	if ( '' === $data['name'] ) {
		return; // Name is the minimum required field.
	}
	$result = estecapelli_process_lead( $data );

	// Post/Redirect/Get → return to the submitting page. On a validation error
	// come back with ?error=… instead of ?sent=1 so we never fake success.
	$return = isset( $_POST['lead_return'] ) ? wp_validate_redirect( esc_url_raw( wp_unslash( $_POST['lead_return'] ) ), '' ) : '';
	$base   = $return ?: estecapelli_indexed_url( '/en/contact' );
	$anchor = $return ? '#lead-form' : '#contact-form';
	if ( is_wp_error( $result ) ) {
		$redirect = add_query_arg( 'lead_error', rawurlencode( $result->get_error_code() ), $base ) . $anchor;
	} else {
		$redirect = add_query_arg( 'sent', '1', $base ) . $anchor;
	}
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
	if ( '' === $data['name'] ) {
		wp_send_json_error( array( 'message' => __( 'Please enter your name.', 'estecapelli' ) ), 422 );
	}

	$result = estecapelli_process_lead( $data );
	if ( is_wp_error( $result ) ) {
		wp_send_json_error( array( 'message' => $result->get_error_message() ), 500 );
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
	printf( '<input type="hidden" name="lead_page_url" value="%s" />', esc_url( $ctx['url'] ) );
	printf( '<input type="hidden" name="lead_page_title" value="%s" />', esc_attr( $ctx['title'] ) );
	foreach ( array( 'source', 'medium', 'campaign', 'content', 'term' ) as $k ) {
		printf( '<input type="hidden" name="utm_%s" value="" />', esc_attr( $k ) );
	}
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
	$new['date']           = __( 'Received', 'estecapelli' );
	return $new;
} );
add_action( 'manage_lead_posts_custom_column', function ( $col, $post_id ) {
	if ( in_array( $col, array( 'lead_email', 'lead_treatment', 'lead_source' ), true ) ) {
		echo esc_html( get_post_meta( $post_id, $col, true ) );
	}
}, 10, 2 );
