<?php
/**
 * Lead captcha — a visible, mandatory security code on the contact page.
 *
 * Most of the spam that reaches the clinic is posted through the contact page
 * form, and the invisible layers (stamp, honeypot, token, optional Turnstile)
 * only score it. This one is different on purpose: the visitor types the five
 * digits shown in an image, and a contact submission without the right digits
 * is REJECTED with a message they can act on. It never vanishes silently — the
 * visitor sees the error, gets a fresh code and their typed details back.
 *
 * Self-hosted by design. Turnstile switches itself off whenever its wp-config
 * keys are missing; this has no keys, no third-party script and no outage mode,
 * so "the contact page always has a captcha" holds in every language and on
 * every install.
 *
 * How it works:
 *   - The challenge is fetched from an uncached REST route (GET
 *     /wp-json/estecapelli/v1/captcha), so a page served from WP Rocket's cache
 *     still gets a fresh code for every visitor.
 *   - The image is an SVG whose digits are drawn as distorted paths among
 *     noise strokes — there is no text in it for a scraper to read.
 *   - The token is stateless and signed: issued-time, a random nonce, and an
 *     HMAC over both plus the answer. Nothing is stored until it is used.
 *   - Every verification burns the nonce, right or wrong, so one challenge
 *     cannot be brute-forced; the visitor simply gets a new one.
 *
 * Which forms require it: estecapelli_lead_captcha_sources() — 'contact' by
 * default. The contact page template is shared by every language (see
 * estecapelli_page_template_router()), and the requirement is enforced on the
 * server by form source, so a bot posting straight to the endpoint gains
 * nothing by skipping the page.
 *
 * @package Estecapelli
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/** Digits in a code. Five makes a blind guess a 1-in-100,000 shot. */
if ( ! defined( 'ESTECAPELLI_CAPTCHA_LENGTH' ) ) {
	define( 'ESTECAPELLI_CAPTCHA_LENGTH', 5 );
}
/** How long a code stays valid — long enough to fill the form in slowly. */
if ( ! defined( 'ESTECAPELLI_CAPTCHA_TTL' ) ) {
	define( 'ESTECAPELLI_CAPTCHA_TTL', 2 * HOUR_IN_SECONDS );
}

/**
 * Form sources that must pass the captcha.
 *
 * @return string[]
 */
function estecapelli_lead_captcha_sources() {
	return (array) apply_filters( 'estecapelli_lead_captcha_sources', array( 'contact' ) );
}

/**
 * Does this form source require the captcha?
 *
 * @param string $source Lead source.
 * @return bool
 */
function estecapelli_lead_captcha_required( $source ) {
	return in_array( (string) $source, estecapelli_lead_captcha_sources(), true );
}

/* -------------------------------------------------------------------------
 * Challenge
 * ---------------------------------------------------------------------- */

add_action(
	'rest_api_init',
	function () {
		register_rest_route(
			'estecapelli/v1',
			'/captcha',
			array(
				'methods'             => 'GET',
				'callback'            => 'estecapelli_lead_captcha_issue',
				'permission_callback' => '__return_true',
			)
		);
	}
);

/** Signature over a challenge and its answer. */
function estecapelli_lead_captcha_sign( $issued, $nonce, $answer ) {
	return substr( hash_hmac( 'sha256', 'captcha|' . $issued . '|' . $nonce . '|' . $answer, wp_salt( 'auth' ) ), 0, 40 );
}

/**
 * Issue a new challenge: an image and the signed token that goes with it.
 *
 * Throttled per IP so the route cannot be farmed; a real visitor refreshing
 * the code a few times never comes near the limit.
 *
 * @return WP_REST_Response
 */
function estecapelli_lead_captcha_issue() {
	$limited = estecapelli_rate_limit( 'lead_captcha', 60, HOUR_IN_SECONDS );
	if ( is_wp_error( $limited ) ) {
		$response = new WP_REST_Response( array( 'image' => '', 'token' => '' ), 429 );
		$response->header( 'Cache-Control', 'no-store, max-age=0' );
		return $response;
	}

	$answer = '';
	for ( $i = 0; $i < ESTECAPELLI_CAPTCHA_LENGTH; $i++ ) {
		$answer .= (string) random_int( 0, 9 );
	}
	$issued = time();
	$nonce  = wp_generate_password( 16, false );
	$token  = $issued . '.' . $nonce . '.' . estecapelli_lead_captcha_sign( $issued, $nonce, $answer );

	$response = new WP_REST_Response(
		array(
			'image' => 'data:image/svg+xml;base64,' . base64_encode( estecapelli_lead_captcha_svg( $answer ) ), // phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.obfuscation_base64_encode
			'token' => $token,
			'ttl'   => ESTECAPELLI_CAPTCHA_TTL,
		),
		200
	);
	$response->header( 'Cache-Control', 'no-store, max-age=0' );
	return $response;
}

/**
 * Stroke outlines for 0–9 in a 20×30 box.
 *
 * @return array<string,string>
 */
function estecapelli_lead_captcha_glyphs() {
	return array(
		'0' => 'M10 2C3 2 2 9 2 15C2 21 3 28 10 28C17 28 18 21 18 15C18 9 17 2 10 2Z',
		'1' => 'M5 7L11 2L11 28M5 28L17 28',
		'2' => 'M3 8C3 3 7 2 10 2C14 2 17 4 17 8C17 13 12 16 3 28L18 28',
		'3' => 'M3 4C6 2 15 1 16 7C17 12 12 14 8 14C13 14 18 16 18 21C18 28 7 29 3 26',
		'4' => 'M14 28L14 2L2 20L19 20',
		'5' => 'M17 2L5 2L4 13C7 11 10 11 13 12C18 14 18 26 11 28C7 29 4 27 3 25',
		'6' => 'M16 4C13 1 6 1 4 8C2 14 2 22 5 26C8 30 16 29 17 22C18 16 13 13 9 14C6 14 4 16 3 19',
		'7' => 'M2 2L18 2L8 28',
		'8' => 'M10 14C4 14 3 10 3 8C3 4 6 2 10 2C14 2 17 4 17 8C17 10 16 14 10 14C4 14 2 18 2 21C2 25 5 28 10 28C15 28 18 25 18 21C18 18 16 14 10 14Z',
		'9' => 'M17 11C16 15 13 16 10 16C5 16 3 13 3 9C3 5 6 2 10 2C15 2 17 6 17 11C17 18 16 24 13 27C10 29 6 28 4 26',
	);
}

/**
 * Draw the code: each digit jittered, rotated and scaled on its own, crossed by
 * noise curves and speckle in the same ink so the strokes cannot be told apart
 * by colour.
 *
 * @param string $answer Digits to draw.
 * @return string SVG markup.
 */
function estecapelli_lead_captcha_svg( $answer ) {
	$w      = 180;
	$h      = 60;
	$glyphs = estecapelli_lead_captcha_glyphs();
	$inks   = array( '#1d3557', '#2b2d42', '#3a2e5c', '#14425a', '#4a2c2a' );
	$r      = static function ( $min, $max ) {
		return random_int( (int) round( $min * 10 ), (int) round( $max * 10 ) ) / 10;
	};

	$out  = sprintf( '<svg xmlns="http://www.w3.org/2000/svg" width="%1$d" height="%2$d" viewBox="0 0 %1$d %2$d">', $w, $h );
	$out .= sprintf( '<rect width="%d" height="%d" fill="#f4f6f9"/>', $w, $h );

	// Speckle behind the digits.
	for ( $i = 0; $i < 40; $i++ ) {
		$out .= sprintf(
			'<circle cx="%s" cy="%s" r="%s" fill="%s" opacity="%s"/>',
			$r( 0, $w ),
			$r( 0, $h ),
			$r( 0.5, 1.8 ),
			$inks[ random_int( 0, count( $inks ) - 1 ) ],
			$r( 0.2, 0.6 )
		);
	}

	$slot = ( $w - 20 ) / strlen( $answer );
	foreach ( str_split( $answer ) as $i => $digit ) {
		$scale = $r( 1.15, 1.45 );
		$x     = 10 + $i * $slot + $r( 0, max( 0, $slot - 20 * $scale ) );
		$y     = ( $h - 30 * $scale ) / 2 + $r( -5, 5 );
		$out  .= sprintf(
			'<path d="%s" transform="translate(%s %s) rotate(%s 10 15) scale(%s) skewX(%s)" fill="none" stroke="%s" stroke-width="%s" stroke-linecap="round" stroke-linejoin="round"/>',
			$glyphs[ $digit ],
			$x,
			$y,
			$r( -22, 22 ),
			$scale,
			$r( -12, 12 ),
			$inks[ random_int( 0, count( $inks ) - 1 ) ],
			$r( 2.2, 3.2 )
		);
	}

	// Noise curves across the digits, in the same ink and weight range.
	for ( $i = 0; $i < 4; $i++ ) {
		$out .= sprintf(
			'<path d="M%s %s C%s %s %s %s %s %s" fill="none" stroke="%s" stroke-width="%s" opacity="0.75"/>',
			$r( -10, 20 ),
			$r( 5, $h - 5 ),
			$r( 30, 80 ),
			$r( -20, $h + 20 ),
			$r( 100, 150 ),
			$r( -20, $h + 20 ),
			$r( $w - 20, $w + 10 ),
			$r( 5, $h - 5 ),
			$inks[ random_int( 0, count( $inks ) - 1 ) ],
			$r( 1.2, 2.2 )
		);
	}

	return $out . '</svg>';
}

/* -------------------------------------------------------------------------
 * Verification
 * ---------------------------------------------------------------------- */

/**
 * Check the submitted code. Burns the challenge either way, so a wrong answer
 * cannot be retried against the same token.
 *
 * @return true|WP_Error
 */
function estecapelli_lead_captcha_verify() {
	$token_raw  = isset( $_POST['lead_captcha_token'] ) ? wp_unslash( $_POST['lead_captcha_token'] ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Missing
	$answer_raw = isset( $_POST['lead_captcha'] ) ? wp_unslash( $_POST['lead_captcha'] ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Missing
	$token      = is_scalar( $token_raw ) ? sanitize_text_field( $token_raw ) : '';
	$answer     = is_scalar( $answer_raw ) ? preg_replace( '/\D+/', '', (string) $answer_raw ) : '';

	if ( '' === $answer || '' === $token ) {
		return new WP_Error( 'captcha_missing', __( 'Please type the security code shown in the image.', 'estecapelli' ) );
	}

	$parts = explode( '.', $token );
	if ( 3 !== count( $parts ) || ! ctype_digit( $parts[0] ) || '' === $parts[1] ) {
		return new WP_Error( 'captcha_failed', __( 'The security code was not correct. Please type the new code.', 'estecapelli' ) );
	}
	list( $issued, $nonce, $sig ) = $parts;

	if ( time() - (int) $issued > ESTECAPELLI_CAPTCHA_TTL ) {
		return new WP_Error( 'captcha_expired', __( 'The security code has expired. Please type the new code.', 'estecapelli' ) );
	}

	$burned = 'ec_cap_' . substr( hash( 'sha256', $nonce ), 0, 32 );
	if ( get_transient( $burned ) ) {
		return new WP_Error( 'captcha_failed', __( 'The security code was not correct. Please type the new code.', 'estecapelli' ) );
	}
	set_transient( $burned, 1, ESTECAPELLI_CAPTCHA_TTL );

	if ( ! hash_equals( estecapelli_lead_captcha_sign( $issued, $nonce, $answer ), (string) $sig ) ) {
		return new WP_Error( 'captcha_failed', __( 'The security code was not correct. Please type the new code.', 'estecapelli' ) );
	}

	return true;
}

/* -------------------------------------------------------------------------
 * Field
 * ---------------------------------------------------------------------- */

/** Load the controller and its strings — only on a page that prints the field. */
function estecapelli_lead_captcha_enqueue() {
	if ( wp_script_is( 'estecapelli-captcha', 'enqueued' ) ) {
		return;
	}
	wp_enqueue_script(
		'estecapelli-captcha',
		get_template_directory_uri() . '/assets/js/lead-captcha.js',
		array(),
		function_exists( 'estecapelli_asset_ver' ) ? estecapelli_asset_ver( '/assets/js/lead-captcha.js' ) : ESTECAPELLI_VERSION,
		true
	);
	wp_localize_script(
		'estecapelli-captcha',
		'EstecapelliCaptcha',
		array(
			'endpoint' => esc_url_raw( rest_url( 'estecapelli/v1/captcha' ) ),
			'ttl'      => ESTECAPELLI_CAPTCHA_TTL,
			'i18n'     => array(
				'loading' => __( 'Loading the security code…', 'estecapelli' ),
				'failed'  => __( 'The security code could not be loaded. Please tap “New code” or reach us on WhatsApp.', 'estecapelli' ),
				'missing' => __( 'Please type the security code shown in the image.', 'estecapelli' ),
			),
		)
	);
}

/**
 * Print the captcha block inside a lead form.
 *
 * @param string $id Unique input id for the label.
 */
function estecapelli_lead_captcha_field( $id = 'lead-captcha' ) {
	estecapelli_lead_captcha_enqueue();
	?>
	<div class="contact-form__field lead-captcha" data-lead-captcha>
		<label for="<?php echo esc_attr( $id ); ?>"><?php esc_html_e( 'Security code', 'estecapelli' ); ?> <span aria-hidden="true">*</span></label>
		<div class="lead-captcha__row">
			<div class="lead-captcha__image" aria-live="polite">
				<img data-lead-captcha-image src="data:," alt="<?php esc_attr_e( 'Security code image', 'estecapelli' ); ?>" width="180" height="60" hidden />
				<span class="lead-captcha__status" data-lead-captcha-status><?php esc_html_e( 'Loading the security code…', 'estecapelli' ); ?></span>
			</div>
			<button type="button" class="lead-captcha__refresh" data-lead-captcha-refresh>
				<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M21 12a9 9 0 1 1-2.64-6.36"/><polyline points="21 3 21 9 15 9"/></svg>
				<?php esc_html_e( 'New code', 'estecapelli' ); ?>
			</button>
		</div>
		<input
			id="<?php echo esc_attr( $id ); ?>"
			type="text"
			name="lead_captcha"
			required
			inputmode="numeric"
			pattern="[0-9]*"
			maxlength="<?php echo esc_attr( ESTECAPELLI_CAPTCHA_LENGTH ); ?>"
			autocomplete="off"
			autocapitalize="off"
			spellcheck="false"
			placeholder="<?php esc_attr_e( 'Type the digits shown above', 'estecapelli' ); ?>"
		/>
		<input type="hidden" name="lead_captcha_token" value="" />
		<noscript>
			<p class="contact-form__error"><?php esc_html_e( 'Please enable JavaScript to see the security code, or reach us on WhatsApp.', 'estecapelli' ); ?></p>
		</noscript>
	</div>
	<?php
}

/* -------------------------------------------------------------------------
 * Translations
 *
 * Same contract as inc/consent-string-fallbacks.php: WPML String Translation
 * stays authoritative; these only stop English leaking onto a translated
 * contact page while a string is still untranslated in WPML. The captcha is
 * mandatory, so an English instruction on /pl/ would cost real enquiries.
 * ---------------------------------------------------------------------- */

/**
 * Captcha + required-email copy per language, keyed by the English source.
 *
 * @return array<string,array<string,string>>
 */
function estecapelli_lead_captcha_strings() {
	return array(
		'tr' => array(
			'Security code'                    => 'Güvenlik kodu',
			'Security code image'              => 'Güvenlik kodu görseli',
			'New code'                         => 'Yeni kod',
			'Type the digits shown above'      => 'Yukarıdaki rakamları yazın',
			'Loading the security code…'       => 'Güvenlik kodu yükleniyor…',
			'The security code could not be loaded. Please tap “New code” or reach us on WhatsApp.' => 'Güvenlik kodu yüklenemedi. Lütfen “Yeni kod”a dokunun veya bize WhatsApp’tan ulaşın.',
			'Please type the security code shown in the image.' => 'Lütfen görseldeki güvenlik kodunu yazın.',
			'The security code was not correct. Please type the new code.' => 'Güvenlik kodu doğru değil. Lütfen yeni kodu yazın.',
			'The security code has expired. Please type the new code.' => 'Güvenlik kodunun süresi doldu. Lütfen yeni kodu yazın.',
			'Please enable JavaScript to see the security code, or reach us on WhatsApp.' => 'Güvenlik kodunu görmek için lütfen JavaScript’i etkinleştirin veya bize WhatsApp’tan ulaşın.',
			'Please enter your email address.' => 'Lütfen e-posta adresinizi girin.',
		),
		'fr' => array(
			'Security code'                    => 'Code de sécurité',
			'Security code image'              => 'Image du code de sécurité',
			'New code'                         => 'Nouveau code',
			'Type the digits shown above'      => 'Saisissez les chiffres ci-dessus',
			'Loading the security code…'       => 'Chargement du code de sécurité…',
			'The security code could not be loaded. Please tap “New code” or reach us on WhatsApp.' => 'Le code de sécurité n’a pas pu être chargé. Appuyez sur « Nouveau code » ou contactez-nous sur WhatsApp.',
			'Please type the security code shown in the image.' => 'Veuillez saisir le code de sécurité affiché dans l’image.',
			'The security code was not correct. Please type the new code.' => 'Le code de sécurité est incorrect. Veuillez saisir le nouveau code.',
			'The security code has expired. Please type the new code.' => 'Le code de sécurité a expiré. Veuillez saisir le nouveau code.',
			'Please enable JavaScript to see the security code, or reach us on WhatsApp.' => 'Veuillez activer JavaScript pour afficher le code de sécurité, ou contactez-nous sur WhatsApp.',
			'Please enter your email address.' => 'Veuillez saisir votre adresse e-mail.',
		),
		'it' => array(
			'Security code'                    => 'Codice di sicurezza',
			'Security code image'              => 'Immagine del codice di sicurezza',
			'New code'                         => 'Nuovo codice',
			'Type the digits shown above'      => 'Digita le cifre mostrate sopra',
			'Loading the security code…'       => 'Caricamento del codice di sicurezza…',
			'The security code could not be loaded. Please tap “New code” or reach us on WhatsApp.' => 'Impossibile caricare il codice di sicurezza. Tocca “Nuovo codice” o scrivici su WhatsApp.',
			'Please type the security code shown in the image.' => 'Digita il codice di sicurezza mostrato nell’immagine.',
			'The security code was not correct. Please type the new code.' => 'Il codice di sicurezza non è corretto. Digita il nuovo codice.',
			'The security code has expired. Please type the new code.' => 'Il codice di sicurezza è scaduto. Digita il nuovo codice.',
			'Please enable JavaScript to see the security code, or reach us on WhatsApp.' => 'Attiva JavaScript per vedere il codice di sicurezza, oppure scrivici su WhatsApp.',
			'Please enter your email address.' => 'Inserisci il tuo indirizzo e-mail.',
		),
		'es' => array(
			'Security code'                    => 'Código de seguridad',
			'Security code image'              => 'Imagen del código de seguridad',
			'New code'                         => 'Nuevo código',
			'Type the digits shown above'      => 'Escribe los dígitos de arriba',
			'Loading the security code…'       => 'Cargando el código de seguridad…',
			'The security code could not be loaded. Please tap “New code” or reach us on WhatsApp.' => 'No se pudo cargar el código de seguridad. Pulsa «Nuevo código» o escríbenos por WhatsApp.',
			'Please type the security code shown in the image.' => 'Escribe el código de seguridad que aparece en la imagen.',
			'The security code was not correct. Please type the new code.' => 'El código de seguridad no es correcto. Escribe el nuevo código.',
			'The security code has expired. Please type the new code.' => 'El código de seguridad ha caducado. Escribe el nuevo código.',
			'Please enable JavaScript to see the security code, or reach us on WhatsApp.' => 'Activa JavaScript para ver el código de seguridad o escríbenos por WhatsApp.',
			'Please enter your email address.' => 'Introduce tu dirección de correo electrónico.',
		),
		'pl' => array(
			'Security code'                    => 'Kod bezpieczeństwa',
			'Security code image'              => 'Obraz kodu bezpieczeństwa',
			'New code'                         => 'Nowy kod',
			'Type the digits shown above'      => 'Wpisz cyfry widoczne powyżej',
			'Loading the security code…'       => 'Wczytywanie kodu bezpieczeństwa…',
			'The security code could not be loaded. Please tap “New code” or reach us on WhatsApp.' => 'Nie udało się wczytać kodu bezpieczeństwa. Kliknij „Nowy kod” lub napisz do nas na WhatsApp.',
			'Please type the security code shown in the image.' => 'Wpisz kod bezpieczeństwa widoczny na obrazku.',
			'The security code was not correct. Please type the new code.' => 'Kod bezpieczeństwa jest nieprawidłowy. Wpisz nowy kod.',
			'The security code has expired. Please type the new code.' => 'Kod bezpieczeństwa wygasł. Wpisz nowy kod.',
			'Please enable JavaScript to see the security code, or reach us on WhatsApp.' => 'Włącz JavaScript, aby zobaczyć kod bezpieczeństwa, lub napisz do nas na WhatsApp.',
			'Please enter your email address.' => 'Podaj swój adres e-mail.',
		),
		'pt' => array(
			'Security code'                    => 'Código de segurança',
			'Security code image'              => 'Imagem do código de segurança',
			'New code'                         => 'Novo código',
			'Type the digits shown above'      => 'Escreva os dígitos acima',
			'Loading the security code…'       => 'A carregar o código de segurança…',
			'The security code could not be loaded. Please tap “New code” or reach us on WhatsApp.' => 'Não foi possível carregar o código de segurança. Toque em «Novo código» ou fale connosco pelo WhatsApp.',
			'Please type the security code shown in the image.' => 'Escreva o código de segurança apresentado na imagem.',
			'The security code was not correct. Please type the new code.' => 'O código de segurança não está correto. Escreva o novo código.',
			'The security code has expired. Please type the new code.' => 'O código de segurança expirou. Escreva o novo código.',
			'Please enable JavaScript to see the security code, or reach us on WhatsApp.' => 'Ative o JavaScript para ver o código de segurança, ou fale connosco pelo WhatsApp.',
			'Please enter your email address.' => 'Introduza o seu endereço de e-mail.',
		),
		'ro' => array(
			'Security code'                    => 'Cod de securitate',
			'Security code image'              => 'Imaginea codului de securitate',
			'New code'                         => 'Cod nou',
			'Type the digits shown above'      => 'Scrie cifrele de mai sus',
			'Loading the security code…'       => 'Se încarcă codul de securitate…',
			'The security code could not be loaded. Please tap “New code” or reach us on WhatsApp.' => 'Codul de securitate nu a putut fi încărcat. Apasă „Cod nou” sau scrie-ne pe WhatsApp.',
			'Please type the security code shown in the image.' => 'Te rugăm să scrii codul de securitate din imagine.',
			'The security code was not correct. Please type the new code.' => 'Codul de securitate nu este corect. Te rugăm să scrii noul cod.',
			'The security code has expired. Please type the new code.' => 'Codul de securitate a expirat. Te rugăm să scrii noul cod.',
			'Please enable JavaScript to see the security code, or reach us on WhatsApp.' => 'Activează JavaScript pentru a vedea codul de securitate sau scrie-ne pe WhatsApp.',
			'Please enter your email address.' => 'Te rugăm să introduci adresa ta de e-mail.',
		),
	);
}

add_filter( 'gettext', 'estecapelli_lead_captcha_gettext_fallback', 20, 3 );
/**
 * Supply captcha copy only when nothing else has translated the string. The URL
 * is read before WPML for the reason on estecapelli_request_language_code().
 *
 * @param string $translation Current translated value.
 * @param string $text        English source value.
 * @param string $domain      Text domain.
 * @return string
 */
function estecapelli_lead_captcha_gettext_fallback( $translation, $text, $domain ) {
	if ( 'estecapelli' !== $domain || $translation !== $text ) {
		return $translation;
	}

	$language = estecapelli_request_language_code();
	if ( ! $language ) {
		$language = estecapelli_indexed_language_code();
	}
	if ( 'en' === $language ) {
		return $translation;
	}

	$strings = estecapelli_lead_captcha_strings();
	return $strings[ $language ][ $text ] ?? $translation;
}
