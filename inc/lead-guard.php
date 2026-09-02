<?php
/**
 * Lead Guard — keep automated spam out of Kommo CRM without ever losing a real
 * enquiry.
 *
 * Background: in August 2026 the forms were hit by a bot run (~70 submissions in
 * one night) whose payload was a link stuffed into the name field. The existing
 * heuristics in inc/leads.php DID flag every one of them — the emails arrived
 * with the "[?]" subject prefix — but a flagged lead was still delivered exactly
 * like a clean one, and because Kommo ingests leads from the BCC'd copy of that
 * same email, every flagged lead also became a CRM record.
 *
 * So the fix is not better detection, it is a separate delivery lane:
 *
 *   clean lead       → stored + emailed to lead@ + BCC to the Kommo parser
 *   quarantined lead → stored + emailed to lead@ + NO BCC (never reaches Kommo)
 *
 * Nothing is ever discarded. A quarantined lead still lands in the clinic's
 * inbox and in wp-admin → Leads, and one click on "Send to CRM" pushes it to
 * Kommo if it turns out to be genuine. That is what makes it safe to score
 * aggressively here: the cost of a false positive is a click, not a lost patient.
 *
 * Five layers feed the score:
 *   1. an interaction token that only a JS-executing client can mint (below),
 *   2. content signals (links/emoji/foreign scripts in a name, throwaway email),
 *   3. the pre-existing honeypot and submission timer,
 *   4. a burst breaker that reacts to a flood in progress,
 *   5. an invisible Cloudflare Turnstile challenge, verified server-side in
 *      inc/leads.php and read back here.
 *
 * Layer 0 lives at the edge — see docs/CLOUDFLARE-ANTISPAM.md.
 *
 * @package Estecapelli
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Score at or above which a lead is quarantined (kept out of Kommo).
 *
 * Weights below are chosen so that no single signal that can misfire on a real
 * visitor reaches this on its own. Only signals a genuine enquiry cannot
 * plausibly produce — a link in the name field, a filled honeypot — are fatal
 * alone.
 */
if ( ! defined( 'ESTECAPELLI_LEAD_SPAM_THRESHOLD' ) ) {
	define( 'ESTECAPELLI_LEAD_SPAM_THRESHOLD', 5 );
}

/* -------------------------------------------------------------------------
 * Layer 1 — interaction token
 *
 * The honeypot and the signed timestamp are both printed into the page HTML, so
 * a bot that fetches the page once gets them for free and can then POST to
 * admin-ajax.php forever without running any JavaScript. The token closes that:
 * it is minted by a separate, uncached REST call that the theme's JS fires on
 * the visitor's first interaction with a lead form, and it is consumable only a
 * few times before it expires.
 *
 * A missing token is NOT fatal — visitors with JavaScript disabled post through
 * the classic form path and must still reach the CRM. It is worth 3 points, so
 * an otherwise clean no-JS lead is delivered normally.
 * ---------------------------------------------------------------------- */

/** Lifetime of a minted token. Long enough to fill a form in, short enough to be useless to a scraper. */
if ( ! defined( 'ESTECAPELLI_LEAD_TOKEN_TTL' ) ) {
	define( 'ESTECAPELLI_LEAD_TOKEN_TTL', 45 * MINUTE_IN_SECONDS );
}

/**
 * How often one token may be spent. More than one, because a visitor who trips a
 * validation error ("please enter a valid phone number") submits the same form
 * again and must not be penalised for it. Far fewer than a flood needs.
 */
if ( ! defined( 'ESTECAPELLI_LEAD_TOKEN_USES' ) ) {
	define( 'ESTECAPELLI_LEAD_TOKEN_USES', 3 );
}

add_action(
	'rest_api_init',
	function () {
		register_rest_route(
			'estecapelli/v1',
			'/lead-token',
			array(
				'methods'             => 'GET',
				'callback'            => 'estecapelli_lead_token_mint',
				'permission_callback' => '__return_true',
			)
		);
	}
);

/**
 * Mint a single interaction token.
 *
 * Farming tokens is throttled per IP so the route cannot simply be looped by the
 * same bot that would otherwise POST blindly. Being throttled returns no token
 * rather than an error the visitor could see — worst case their lead scores 3
 * and is still delivered to the CRM.
 *
 * @return WP_REST_Response
 */
function estecapelli_lead_token_mint() {
	$limited = estecapelli_rate_limit( 'lead_token', 30, HOUR_IN_SECONDS );
	if ( is_wp_error( $limited ) ) {
		return new WP_REST_Response( array( 'token' => '' ), 429 );
	}

	$issued = time();
	$secret = wp_generate_password( 16, false );
	$token  = $issued . '.' . $secret . '.' . substr(
		hash_hmac( 'sha256', $issued . '|' . $secret, wp_salt( 'auth' ) ),
		0,
		32
	);

	set_transient( estecapelli_lead_token_key( $token ), 0, ESTECAPELLI_LEAD_TOKEN_TTL );

	$response = new WP_REST_Response( array( 'token' => $token ), 200 );
	$response->header( 'Cache-Control', 'no-store, max-age=0' );
	return $response;
}

/** Transient key tracking how often a token has been spent. */
function estecapelli_lead_token_key( $token ) {
	return 'ec_ltok_' . substr( hash( 'sha256', (string) $token ), 0, 32 );
}

/**
 * Validate and consume the submitted token.
 *
 * @param string $token Raw token from the form.
 * @return string Empty when the token is good; otherwise the signal to record.
 */
function estecapelli_lead_token_check( $token ) {
	$token = trim( (string) $token );
	if ( '' === $token ) {
		return 'no interaction token (form posted without running the page JS)';
	}

	$parts = explode( '.', $token );
	if ( 3 !== count( $parts ) ) {
		return 'malformed interaction token';
	}
	list( $issued, $secret, $sig ) = $parts;
	$issued = absint( $issued );
	$expect = substr( hash_hmac( 'sha256', $issued . '|' . $secret, wp_salt( 'auth' ) ), 0, 32 );
	if ( ! $issued || ! hash_equals( $expect, (string) $sig ) ) {
		return 'forged interaction token';
	}
	if ( time() - $issued > ESTECAPELLI_LEAD_TOKEN_TTL ) {
		return 'expired interaction token';
	}

	// A token minted and spent in the same instant was never typed into by a
	// human — the theme mints on first focus, which is seconds before a submit.
	if ( time() - $issued < 2 ) {
		return 'interaction token spent immediately after being issued';
	}

	$key  = estecapelli_lead_token_key( $token );
	$used = get_transient( $key );
	if ( false === $used ) {
		return 'unknown or already-expired interaction token';
	}
	if ( (int) $used >= ESTECAPELLI_LEAD_TOKEN_USES ) {
		return 'interaction token replayed';
	}
	set_transient( $key, (int) $used + 1, ESTECAPELLI_LEAD_TOKEN_TTL );

	return '';
}

/* -------------------------------------------------------------------------
 * Layer 2 — content signals
 * ---------------------------------------------------------------------- */

/**
 * Throwaway inbox providers. The August run used dx6ffjl4tt6gud@emalupe.com;
 * these services exist to be unreachable, which is the opposite of what someone
 * asking a clinic to call them back wants.
 *
 * @return string[]
 */
/**
 * Providers a person signs up to by hand, and where the shape of the local part
 * therefore says nothing about whether a machine created it.
 *
 * @return string[]
 */
function estecapelli_mainstream_email_domains() {
	return (array) apply_filters(
		'estecapelli_mainstream_email_domains',
		array(
			'gmail.com', 'googlemail.com', 'outlook.com', 'hotmail.com', 'hotmail.co.uk',
			'live.com', 'msn.com', 'yahoo.com', 'yahoo.co.uk', 'yahoo.fr', 'yahoo.it',
			'yahoo.es', 'ymail.com', 'icloud.com', 'me.com', 'mac.com', 'aol.com',
			'proton.me', 'protonmail.com', 'gmx.com', 'gmx.de', 'web.de', 't-online.de',
			'orange.fr', 'free.fr', 'wanadoo.fr', 'libero.it', 'virgilio.it', 'alice.it',
			'wp.pl', 'onet.pl', 'o2.pl', 'interia.pl', 'sapo.pt', 'terra.es',
			'hotmail.fr', 'hotmail.it', 'hotmail.es', 'outlook.fr', 'outlook.it', 'outlook.es',
		)
	);
}

function estecapelli_disposable_email_domains() {
	return (array) apply_filters(
		'estecapelli_disposable_email_domains',
		array(
			'emalupe.com', 'mailinator.com', 'guerrillamail.com', 'guerrillamail.net',
			'sharklasers.com', 'grr.la', 'spam4.me', '10minutemail.com', '10minutemail.net',
			'tempmail.com', 'temp-mail.org', 'tempmailo.com', 'throwawaymail.com',
			'yopmail.com', 'yopmail.fr', 'trashmail.com', 'trashmail.de', 'mailnesia.com',
			'dispostable.com', 'fakeinbox.com', 'getnada.com', 'nada.email', 'inboxbear.com',
			'maildrop.cc', 'mailcatch.com', 'mytemp.email', 'mohmal.com', 'tmpmail.org',
			'tmpmail.net', 'burnermail.io', 'moakt.com', 'mailpoof.com', 'harakirimail.com',
			'anonaddy.me', 'spambog.com', 'spamgourmet.com', 'discard.email', 'einrot.com',
			'cuvox.de', 'dayrep.com', 'fleckens.hu', 'gustr.com', 'jourrapide.com',
			'rhyta.com', 'superrito.com', 'teleworm.us', 'armyspy.com',
			'byom.de', 'emailfake.com', 'emailondeck.com', 'linshiyouxiang.net',
			'mailtemp.net', 'tempr.email', 'vomoto.com', 'zetmail.com',
		)
	);
}

/**
 * Does the email domain accept mail at all?
 *
 * Cached for a week per domain — a DNS lookup on the request path would
 * otherwise slow every submission down, and the answer effectively never
 * changes. Hosts that disable checkdnsrr() simply skip this signal.
 *
 * @param string $domain Email domain.
 * @return bool True when mail could be delivered (or when we cannot tell).
 */
function estecapelli_email_domain_deliverable( $domain ) {
	$domain = strtolower( trim( (string) $domain ) );
	if ( '' === $domain || ! function_exists( 'checkdnsrr' ) ) {
		return true;
	}

	$key    = 'ec_mx_' . substr( hash( 'sha256', $domain ), 0, 32 );
	$cached = get_transient( $key );
	if ( false !== $cached ) {
		return '1' === $cached;
	}

	// An A record is enough: RFC 5321 falls back to it when a domain has no MX.
	$ok = checkdnsrr( $domain, 'MX' ) || checkdnsrr( $domain, 'A' );
	set_transient( $key, $ok ? '1' : '0', WEEK_IN_SECONDS );
	return $ok;
}

/**
 * Score the submitted content.
 *
 * Every rule here answers one question: could a real person asking about a hair
 * transplant plausibly have sent this? A name containing a URL could not. A name
 * in a script none of the seven site languages use might, so it scores lower.
 *
 * @param array $d Sanitised lead data (name/phone/email/message).
 * @return array<string,int> Signal text => weight.
 */
function estecapelli_lead_content_signals( array $d ) {
	$signals = array();
	$name    = (string) ( $d['name'] ?? '' );
	$message = (string) ( $d['message'] ?? '' );
	$email   = (string) ( $d['email'] ?? '' );
	$phone   = (string) ( $d['phone'] ?? '' );

	// Each alternative swallows a WHOLE url, so one link counts once. Matching
	// only the scheme let "https://instagram.com/p/x" score twice — as a scheme
	// and again as a .com — and a patient pasting a single link to the result
	// they want was two points from being treated as a spam run.
	$link = '#(?:https?://\S+|www\.\S+|\b[a-z0-9][a-z0-9-]*\.(?:com|net|org|ru|xyz|top|info|biz|club|online|site|shop|link|click|live|icu|cc|pw|su|space|website|store)\b)#i';

	// A link in the name field is the signature of the August run and has no
	// legitimate explanation whatsoever.
	if ( preg_match( $link, $name ) ) {
		$signals['name field contains a link'] = 5;
	}
	// BBCode/Markdown link syntax anywhere — only ever posted by a bot aiming at
	// a forum or comment field.
	if ( preg_match( '/\[url[=\]]|\[link[=\]]|\]\(https?:/i', $name . ' ' . $message ) ) {
		$signals['link markup in the submission'] = 5;
	}
	// Pictographs and symbols such as the "🔻" and "№" the run used. Emoji do not
	// belong in a legal name on a medical enquiry.
	if ( preg_match( '/[\p{So}\p{Cf}]/u', $name ) ) {
		$signals['name field contains emoji or symbols'] = 4;
	}
	// None of the seven site languages are written in these scripts.
	if ( preg_match( '/[\p{Cyrillic}\p{Han}\p{Hiragana}\p{Katakana}\p{Hangul}]/u', $name ) ) {
		$signals['name field is in a script no site language uses'] = 3;
	}
	// One link is weak on purpose: patients really do paste an Instagram link of
	// the result they want, and that must never combine with a missing JS token
	// to quarantine an ordinary enquiry. Several links is a different animal —
	// the August/Russian runs posted five to ten product URLs per submission,
	// and no patient has ever done that.
	$link_count = preg_match_all( $link, $message );
	if ( $link_count >= 5 ) {
		$signals['message is a list of links'] = 5;
	} elseif ( $link_count >= 3 ) {
		$signals['message contains several links'] = 4;
	} elseif ( $link_count >= 2 ) {
		$signals['message contains more than one link'] = 2;
	} elseif ( $link_count >= 1 ) {
		$signals['message contains a link'] = 1;
	}

	// A message in a script none of the site's languages use. Worth 1, for the
	// same reason a single link is worth 1: Turkish clinics genuinely do
	// receive Russian-speaking patients, and at 2 this quietly quarantined one
	// the moment it met the 3 points that any submission without JS already
	// carries. Paired with a wall of links it is still decisive, which is the
	// shape the run actually took.
	if ( preg_match( '/[\p{Cyrillic}\p{Han}\p{Hiragana}\p{Katakana}\p{Hangul}]/u', $message ) ) {
		$signals['message is in a script no site language uses'] = 1;
	}

	// Cold outreach ("I noticed your website and…") runs long. A patient asking
	// about grafts and dates does not. Alone it means nothing; alongside links
	// it completes the picture. Falls back to strlen because a host without
	// mbstring would otherwise switch the rule off without saying so.
	$length = function_exists( 'mb_strlen' ) ? mb_strlen( $message ) : strlen( $message );
	if ( $length > 1200 ) {
		$signals['unusually long message'] = 2;
	}
	// Spam runs write the payload into the name and leave the real fields blank.
	if ( '' === trim( $message, " -\t\n\r" ) && function_exists( 'mb_strlen' ) && mb_strlen( $name ) > 45 ) {
		$signals['very long name with an empty message'] = 2;
	}

	if ( $email && strpos( $email, '@' ) ) {
		list( $local, $domain ) = array_pad( explode( '@', strtolower( $email ), 2 ), 2, '' );
		if ( in_array( $domain, estecapelli_disposable_email_domains(), true ) ) {
			$signals['throwaway email provider'] = 3;
		} elseif ( ! estecapelli_email_domain_deliverable( $domain ) ) {
			$signals['email domain cannot receive mail'] = 3;
		}
		// "dx6ffjl4tt6gud" — long, no separator, digits shuffled THROUGH the
		// letters. The digits have to be interleaved, not a trailing run: this
		// rule used to fire on "shashankreddy9994@gmail.com", which is a name
		// and a number like millions of real addresses, and those two points
		// were enough to mark a patient's own photographs as spam. A mailbox at
		// a provider people sign up to by hand is exempt outright.
		$trailing_stripped = rtrim( $local, '0123456789' );
		if (
			strlen( $local ) >= 12
			&& ! preg_match( '/[._\-+]/', $local )
			&& preg_match( '/\d/', $trailing_stripped )
			&& ! in_array( $domain, estecapelli_mainstream_email_domains(), true )
		) {
			$signals['machine-generated email address'] = 2;
		}
	}

	$digits = preg_replace( '/\D+/', '', $phone );
	if ( $digits && preg_match( '/^(\d)\1+$/', $digits ) ) {
		$signals['phone number is a single repeated digit'] = 3;
	}

	return $signals;
}

/* -------------------------------------------------------------------------
 * Layer 4 — burst breaker
 *
 * A flood looks different from a trickle. While one is underway every borderline
 * submission is treated as part of it; the extra weight is deliberately below
 * the threshold on its own, so a genuine enquiry sent during an attack still
 * reaches the CRM.
 * ---------------------------------------------------------------------- */

/** Record that a suspicious submission just arrived, and report whether a flood is on. */
function estecapelli_lead_burst_state( $suspicious ) {
	$key    = 'ec_lead_burst';
	$window = (int) apply_filters( 'estecapelli_lead_burst_window', 15 * MINUTE_IN_SECONDS );
	$trip   = (int) apply_filters( 'estecapelli_lead_burst_trip', 8 );
	$count  = (int) get_transient( $key );

	if ( $suspicious ) {
		++$count;
		set_transient( $key, $count, $window );
	}

	return $count >= $trip;
}

/* -------------------------------------------------------------------------
 * The verdict
 * ---------------------------------------------------------------------- */

/**
 * Full spam assessment for a submission.
 *
 * @param array $d Sanitised lead data.
 * @return array{score:int,signals:string[],quarantine:bool}
 */
function estecapelli_lead_assess( array $d ) {
	$weighted = estecapelli_lead_content_signals( $d );

	// Layer 3 — the original honeypot and timer.
	//
	// The honeypot needs corroboration rather than deciding on its own. It was
	// fatal here for one day and immediately quarantined a genuine test lead:
	// the trap was named "lead_company_website", which Chrome reads as
	// organization + url and fills from the visitor's own saved profile. The
	// field is named "lead_delta" now, but a browser that fills a hidden input
	// once will find another excuse, so 3 points — enough to quarantine
	// alongside any second signal, never enough alone.
	$honeypot_raw = isset( $_POST['lead_delta'] ) ? wp_unslash( $_POST['lead_delta'] ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Missing
	$honeypot     = is_scalar( $honeypot_raw ) ? sanitize_text_field( $honeypot_raw ) : 'non-scalar value';
	if ( '' !== $honeypot ) {
		// Autofill puts the visitor's OWN details in the trap, so a trap echoing
		// a field they really filled in is a browser, not a bot.
		$autofilled = false;
		foreach ( array( $d['name'] ?? '', $d['phone'] ?? '', $d['email'] ?? '' ) as $own ) {
			if ( '' !== trim( (string) $own ) && 0 === strcasecmp( trim( (string) $own ), $honeypot ) ) {
				$autofilled = true;
				break;
			}
		}
		if ( ! $autofilled ) {
			$weighted['honeypot field was filled'] = 3;
		}
	}

	$started_raw = isset( $_POST['lead_form_started'] ) ? wp_unslash( $_POST['lead_form_started'] ) : 0; // phpcs:ignore WordPress.Security.NonceVerification.Missing
	$started     = is_scalar( $started_raw ) ? absint( $started_raw ) : 0;
	if ( $started && time() - $started < 3 ) {
		$weighted['submitted less than 3 seconds after the page was rendered'] = 2;
	}

	// Layer 1.
	$token_raw = isset( $_POST['lead_token'] ) ? wp_unslash( $_POST['lead_token'] ) : ( $d['token'] ?? '' ); // phpcs:ignore WordPress.Security.NonceVerification.Missing
	$token     = is_scalar( $token_raw ) ? sanitize_text_field( $token_raw ) : '';
	$token_bad = estecapelli_lead_token_check( $token );
	if ( $token_bad ) {
		$weighted[ $token_bad ] = ( false !== strpos( $token_bad, 'replayed' ) || false !== strpos( $token_bad, 'forged' ) ) ? 4 : 3;
	}

	// Layer 5 — Cloudflare Turnstile. The verdict was reached in inc/leads.php
	// (memoised, so this costs nothing) and is only ever weighted here: a
	// challenge Cloudflare turned down is worth 4, which quarantines alongside
	// any second signal and never on its own.
	//
	// The exception is the reason this reads as more than one line. A client
	// that ran no JavaScript at all cannot produce a Turnstile token either, and
	// it has already paid 3 points for that above. Charging it a second time for
	// the same fact would put an ordinary no-JS enquiry — a real person on a
	// locked-down work laptop — one weak signal away from quarantine, which is
	// exactly what the weights in this file are built to avoid.
	$turnstile = function_exists( 'estecapelli_lead_turnstile_signal' ) ? estecapelli_lead_turnstile_signal() : '';
	$no_js     = ( 0 === strpos( (string) $token_bad, 'no interaction token' ) );
	if ( $turnstile && ! ( $no_js && false !== strpos( $turnstile, 'missing' ) ) ) {
		$weighted[ $turnstile ] = 4;
	}

	// One IP filling the form all day is not a patient — but "one IP" is a much
	// blunter idea than it sounds. Mobile visitors in Turkey and Italy sit behind
	// carrier-grade NAT in their thousands, and the clinic testing its own forms
	// looks identical to abuse. Hence a cap high enough that only a machine
	// reaches it, and only 3 points when it does.
	$daily = estecapelli_lead_ip_daily_count();
	if ( $daily > (int) apply_filters( 'estecapelli_lead_daily_ip_cap', 25 ) ) {
		$weighted[ sprintf( 'more than %d submissions from this address today', $daily - 1 ) ] = 3;
	}

	$score = array_sum( $weighted );

	// Layer 4 — feed and read the breaker. Anything already carrying weight
	// counts towards the flood.
	if ( estecapelli_lead_burst_state( $score >= 3 ) ) {
		$weighted['a submission flood is in progress'] = 3;
		$score                                        += 3;
	}

	$threshold = (int) apply_filters( 'estecapelli_lead_spam_threshold', ESTECAPELLI_LEAD_SPAM_THRESHOLD );

	return array(
		'score'      => (int) $score,
		'signals'    => array_keys( $weighted ),
		'quarantine' => $score >= $threshold,
	);
}

/** Submissions seen from this IP today, incremented as a side effect. */
function estecapelli_lead_ip_daily_count() {
	$key   = 'ec_lead_day_' . substr( hash( 'sha256', estecapelli_client_ip() . '|' . gmdate( 'Y-m-d' ) ), 0, 32 );
	$count = (int) get_transient( $key ) + 1;
	set_transient( $key, $count, DAY_IN_SECONDS );
	return $count;
}

/* -------------------------------------------------------------------------
 * Front-end token minting script
 * ---------------------------------------------------------------------- */

function estecapelli_lead_guard_enqueue() {
	wp_enqueue_script(
		'estecapelli-lead-guard',
		get_template_directory_uri() . '/assets/js/lead-guard.js',
		array(),
		function_exists( 'estecapelli_asset_ver' ) ? estecapelli_asset_ver( '/assets/js/lead-guard.js' ) : ESTECAPELLI_VERSION,
		true
	);
	wp_localize_script(
		'estecapelli-lead-guard',
		'EstecapelliLeadGuard',
		array( 'endpoint' => esc_url_raw( rest_url( 'estecapelli/v1/lead-token' ) ) )
	);
}
add_action( 'wp_enqueue_scripts', 'estecapelli_lead_guard_enqueue', 21 );

/* -------------------------------------------------------------------------
 * wp-admin — the quarantine queue
 *
 * A quarantined lead is one click away from the CRM. Without that click this
 * whole file would just be a nicer way to lose a patient.
 * ---------------------------------------------------------------------- */

/** "All | Spam" filter links above the Leads list. */
add_filter(
	'views_edit-lead',
	function ( $views ) {
		$count = (int) count(
			get_posts(
				array(
					'post_type'      => 'lead',
					'post_status'    => 'private',
					'posts_per_page' => 200,
					'fields'         => 'ids',
					'meta_key'       => 'lead_is_spam', // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key
					'meta_value'     => '1', // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_value
				)
			)
		);
		$active = isset( $_GET['lead_spam'] ) ? ' class="current"' : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended

		$views['lead_spam'] = sprintf(
			'<a href="%s"%s>%s <span class="count">(%s%s)</span></a>',
			esc_url( add_query_arg( array( 'post_type' => 'lead', 'lead_spam' => '1' ), admin_url( 'edit.php' ) ) ),
			$active,
			esc_html__( 'Quarantined', 'estecapelli' ),
			esc_html( (string) $count ),
			200 === $count ? '+' : ''
		);
		return $views;
	}
);

/** Apply that filter to the query. */
add_action(
	'pre_get_posts',
	function ( $query ) {
		if ( ! is_admin() || ! $query->is_main_query() || 'lead' !== $query->get( 'post_type' ) ) {
			return;
		}
		if ( empty( $_GET['lead_spam'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			return;
		}
		$query->set(
			'meta_query', // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_query
			array(
				array(
					'key'   => 'lead_is_spam',
					'value' => '1',
				),
			)
		);
	}
);

/** "Send to CRM" row action on a quarantined lead. */
add_filter(
	'post_row_actions',
	function ( $actions, $post ) {
		if ( 'lead' !== $post->post_type || '1' !== get_post_meta( $post->ID, 'lead_is_spam', true ) ) {
			return $actions;
		}
		if ( '1' === get_post_meta( $post->ID, 'lead_crm_released', true ) ) {
			$actions['lead_crm'] = '<span style="color:#2271b1">' . esc_html__( 'Sent to CRM', 'estecapelli' ) . '</span>';
			return $actions;
		}
		$actions['lead_crm'] = sprintf(
			'<a href="%s">%s</a>',
			esc_url(
				wp_nonce_url(
					admin_url( 'admin-post.php?action=estecapelli_release_lead&lead=' . $post->ID ),
					'estecapelli_release_lead_' . $post->ID
				)
			),
			esc_html__( 'Not spam — send to CRM', 'estecapelli' )
		);
		return $actions;
	},
	10,
	2
);

/**
 * Release a quarantined lead: re-send the notification it already produced, this
 * time BCC'd to the Kommo parser so the CRM picks it up.
 */
function estecapelli_release_lead() {
	$lead_id = isset( $_GET['lead'] ) ? absint( $_GET['lead'] ) : 0;
	if ( ! $lead_id || ! current_user_can( 'edit_post', $lead_id ) ) {
		wp_die( esc_html__( 'You are not allowed to do that.', 'estecapelli' ) );
	}
	check_admin_referer( 'estecapelli_release_lead_' . $lead_id );

	$body    = (string) get_post_meta( $lead_id, 'lead_mail_body', true );
	$subject = (string) get_post_meta( $lead_id, 'lead_mail_subject', true );
	if ( ! $body ) {
		wp_die( esc_html__( 'This lead has no stored notification to release.', 'estecapelli' ) );
	}

	// Straight to the parser: the clinic already has its copy in the inbox, and a
	// second one would only duplicate it.
	$sent = wp_mail(
		ESTECAPELLI_KOMMO_PARSER,
		str_replace( '[SPAM?] ', '', $subject ),
		$body,
		array(
			'Content-Type: text/plain; charset=UTF-8',
			sprintf( 'From: %s <%s>', get_bloginfo( 'name' ), ESTECAPELLI_MAIL_FROM ),
		)
	);

	// `lead_is_spam` deliberately stays: the lead keeps its place in the
	// Quarantined view, now labelled as released. Clearing it would hide the
	// record of a false positive, which is the one thing worth being able to
	// look back at when tuning the weights.
	update_post_meta( $lead_id, 'lead_crm_released', $sent ? '1' : '0' );

	wp_safe_redirect(
		add_query_arg(
			array( 'post_type' => 'lead', 'lead_released' => $sent ? '1' : '0' ),
			admin_url( 'edit.php' )
		)
	);
	exit;
}
add_action( 'admin_post_estecapelli_release_lead', 'estecapelli_release_lead' );

/** Result notice for the release action. */
add_action(
	'admin_notices',
	function () {
		if ( ! isset( $_GET['lead_released'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			return;
		}
		$ok = '1' === $_GET['lead_released']; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		printf(
			'<div class="notice notice-%s is-dismissible"><p>%s</p></div>',
			$ok ? 'success' : 'error',
			esc_html(
				$ok
					? __( 'Lead released — Kommo will pick it up from the parser mailbox.', 'estecapelli' )
					: __( 'The lead could not be sent to the CRM. Check the SMTP settings.', 'estecapelli' )
			)
		);
	}
);
