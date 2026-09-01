<?php
/**
 * Exact URL contract captured from the live, Google-indexed sitemap.
 *
 * Nothing in the theme should translate a slug or assemble a multilingual URL
 * heuristically. English source paths are keys; the values below are the exact
 * indexed routes for all eight languages.
 *
 * @package Estecapelli
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Public language directories used by the indexed site.
 *
 * @return string[]
 */
function estecapelli_indexed_languages() {
	return array( 'en', 'tr', 'fr', 'it', 'es', 'pl', 'pt', 'ro' );
}

/**
 * Convert WPML's locale-style aliases to the indexed language directory.
 */
function estecapelli_indexed_language_code( $language = '' ) {
	if ( '' === (string) $language ) {
		$language = (string) apply_filters( 'wpml_current_language', null );
		if ( ! $language && defined( 'ICL_LANGUAGE_CODE' ) ) {
			$language = (string) ICL_LANGUAGE_CODE;
		}
	}

	$language = strtolower( str_replace( '_', '-', (string) $language ) );
	$language = sanitize_key( $language );
	$aliases  = array(
		'pt-pt' => 'pt',
	);
	$language = $aliases[ $language ] ?? $language;

	if ( in_array( $language, estecapelli_indexed_languages(), true ) ) {
		return $language;
	}

	$request = isset( $_SERVER['REQUEST_URI'] ) ? wp_unslash( $_SERVER['REQUEST_URI'] ) : '';
	$path    = trim( (string) wp_parse_url( $request, PHP_URL_PATH ), '/' );
	$first   = strtolower( (string) strtok( $path, '/' ) );
	$first   = $aliases[ $first ] ?? $first;

	return in_array( $first, estecapelli_indexed_languages(), true ) ? $first : 'en';
}

/**
 * The language a request URL declares, independent of WPML.
 *
 * WPML is the wrong source in a 404 context twice over: it answers with the
 * site's DEFAULT language rather than the one that was asked for, and it does
 * not recognise `/pt/` at all, because its own directory is `pt-pt`. Since 'en'
 * is itself a valid indexed code, estecapelli_indexed_language_code() accepts
 * that answer and never reaches its URL fallback — which sent every /pt/ 404 to
 * the English page. When the URL states a language, that is the contract.
 *
 * @param string $request Optional request URI; defaults to the current one.
 * @return string Indexed language code, or '' when the URL declares none.
 */
function estecapelli_request_language_code( $request = '' ) {
	if ( '' === (string) $request ) {
		$request = isset( $_SERVER['REQUEST_URI'] ) ? wp_unslash( $_SERVER['REQUEST_URI'] ) : '';
	}

	$path  = trim( (string) wp_parse_url( (string) $request, PHP_URL_PATH ), '/' );
	$first = sanitize_key( strtolower( (string) strtok( $path, '/' ) ) );
	if ( 'pt-pt' === $first ) {
		$first = 'pt';
	}

	return in_array( $first, estecapelli_indexed_languages(), true ) ? $first : '';
}

/**
 * Resolve an indexed public language code to the code active inside WPML.
 *
 * The live contract uses /pt/, while existing installations may still use
 * WPML's built-in Portuguese (Portugal) code, pt-pt. Keep that implementation
 * detail inside WPML and expose /pt/ everywhere publicly.
 */
function estecapelli_wpml_language_code( $language = '' ) {
	$language = estecapelli_indexed_language_code( $language );
	if ( 'pt' !== $language ) {
		return $language;
	}

	$languages = apply_filters( 'wpml_active_languages', null, array( 'skip_missing' => 0 ) );
	$codes     = array();
	if ( is_array( $languages ) ) {
		foreach ( $languages as $item ) {
			$codes[] = strtolower( (string) ( $item['language_code'] ?? '' ) );
		}
	}

	if ( in_array( 'pt', $codes, true ) ) {
		return 'pt';
	}
	return in_array( 'pt-pt', $codes, true ) ? 'pt-pt' : 'pt';
}

/** Use the built-in pt-pt language for /pt/ requests when custom pt is absent. */
function estecapelli_switch_portuguese_alias() {
	if ( is_admin() ) {
		return;
	}
	$request = isset( $_SERVER['REQUEST_URI'] ) ? wp_unslash( $_SERVER['REQUEST_URI'] ) : '';
	$path    = trim( (string) wp_parse_url( $request, PHP_URL_PATH ), '/' );
	if ( 'pt' !== strtolower( (string) strtok( $path, '/' ) ) ) {
		return;
	}

	$wpml_language = estecapelli_wpml_language_code( 'pt' );
	if ( 'pt-pt' === $wpml_language ) {
		do_action( 'wpml_switch_language', $wpml_language );
	}
}
add_action( 'init', 'estecapelli_switch_portuguese_alias', 20 );

/**
 * Site base without WPML's current-language suffix.
 */
function estecapelli_unfiltered_home_url() {
	$home = (string) get_option( 'home' );
	if ( ! $home ) {
		$home = (string) get_option( 'siteurl' );
	}

	$parts = wp_parse_url( $home );
	if ( ! is_array( $parts ) || empty( $parts['host'] ) ) {
		return untrailingslashit( $home );
	}

	$path     = trim( (string) ( $parts['path'] ?? '' ), '/' );
	$segments = '' === $path ? array() : explode( '/', $path );
	$known    = array_merge( estecapelli_indexed_languages(), array( 'pt-pt' ) );
	while ( $segments && in_array( strtolower( (string) end( $segments ) ), $known, true ) ) {
		array_pop( $segments );
	}

	$base = ( $parts['scheme'] ?? 'https' ) . '://' . $parts['host'];
	if ( ! empty( $parts['port'] ) ) {
		$base .= ':' . (int) $parts['port'];
	}
	if ( $segments ) {
		$base .= '/' . implode( '/', $segments );
	}

	return untrailingslashit( $base );
}

/**
 * The exact indexed section slugs.
 *
 * @return array<string,array<string,string>>
 */
function estecapelli_indexed_section_slugs() {
	return array(
		// Home is the language root. It intentionally has no leaf slug.
		'home'             => array( 'en' => '', 'tr' => '', 'fr' => '', 'it' => '', 'es' => '', 'pl' => '', 'pt' => '', 'ro' => '' ),
		'hair-transplant'  => array( 'en' => 'hair-transplant', 'tr' => 'sac-ekimi', 'fr' => 'greffe-de-cheveux', 'it' => 'trapianto-di-capelli', 'es' => 'trasplante-capilar', 'pl' => 'przeszczep-wlosow', 'pt' => 'transplante-capilar', 'ro' => 'transplant-de-par' ),
		'plastic-surgery'  => array( 'en' => 'plastic-surgery', 'tr' => 'estetik-cerrahi', 'fr' => 'chirurgie-plastique', 'it' => 'chirurgia-plastica', 'es' => 'cirugia-plastica', 'pl' => 'chirurgia-plastyczna', 'pt' => 'cirurgia-plastica', 'ro' => 'chirurgie-plastica' ),
		'dental-treatment' => array( 'en' => 'dental-treatment', 'tr' => 'dis-tedavisi', 'fr' => 'traitement-dentaire', 'it' => 'trattamento-dentale', 'es' => 'tratamiento-dental', 'pl' => 'leczenie-stomatologiczne', 'pt' => 'tratamento-dentario', 'ro' => 'tratament-dentar' ),
		'before-after'     => array( 'en' => 'before-after', 'tr' => 'oncesi-sonrasi', 'fr' => 'avant-apres', 'it' => 'prima-dopo', 'es' => 'antes-despues', 'pl' => 'przed-po', 'pt' => 'antes-depois', 'ro' => 'inainte-dupa' ),
		'about-us'         => array( 'en' => 'about-us', 'tr' => 'hakkimizda', 'fr' => 'a-propos-de-nous', 'it' => 'chi-siamo', 'es' => 'sobre-nosotros', 'pl' => 'o-nas', 'pt' => 'sobre-nos', 'ro' => 'despre-noi' ),
		'blog'             => array( 'en' => 'blog', 'tr' => 'blog', 'fr' => 'blog', 'it' => 'blog', 'es' => 'blog', 'pl' => 'blog', 'pt' => 'blog', 'ro' => 'blog' ),
		'contact'          => array( 'en' => 'contact', 'tr' => 'iletisim', 'fr' => 'contact', 'it' => 'contatto', 'es' => 'contacto', 'pl' => 'kontakt', 'pt' => 'contato', 'ro' => 'contact' ),
	);
}

/** Old homepage leaf slugs retained only for canonical redirects. */
function estecapelli_legacy_home_slugs() {
	return array(
		'en' => 'home',
		'tr' => 'ana-sayfa',
		'fr' => 'maison',
		'it' => 'home',
		'es' => 'inicio',
		'pl' => 'strona-glowna',
		'pt' => 'inicio',
		// Romanian was never on the legacy structure, so it has no legacy leaf
		// to redirect from. The entry exists only so callers can read every
		// indexed language without a missing-key check.
		'ro' => '',
	);
}

/**
 * Exact about-us child slugs.
 */
function estecapelli_indexed_about_slugs() {
	return array(
		'our-doctors'     => array( 'en' => 'our-doctors', 'tr' => 'doktorlarimiz', 'fr' => 'nos-medecins', 'it' => 'i-nostri-medici', 'es' => 'nuestros-doctores', 'pl' => 'nasi-lekarze', 'pt' => 'nossos-medicos', 'ro' => 'medicii-nostri' ),
		'our-team'         => array( 'en' => 'our-team', 'tr' => 'ekibimiz', 'fr' => 'notre-equipe', 'it' => 'il-nostro-team', 'es' => 'nuestro-equipo', 'pl' => 'nasz-zespol', 'pt' => 'nossa-equipe', 'ro' => 'echipa-noastra' ),
	);
}

/**
 * Exact treatment-category slugs.
 */
function estecapelli_indexed_category_slugs() {
	$sections = estecapelli_indexed_section_slugs();
	return array(
		'hair-transplant'  => $sections['hair-transplant'],
		'plastic-surgery'  => $sections['plastic-surgery'],
		'dental-treatment' => $sections['dental-treatment'],
	);
}

/**
 * Exact treatment leaf slugs, keyed by the English source slug.
 */
function estecapelli_indexed_treatment_slugs() {
	return array(
		// Hair transplant.
		'hair-transplant-overview'                                => array( 'en' => 'hair-transplant-overview', 'tr' => 'sac-ekimi-genel-bakis', 'fr' => 'apercu-de-la-greffe-de-cheveux', 'it' => 'panoramica-sul-trapianto-di-capelli', 'es' => 'descripcion-general-del-trasplante-capilar', 'pl' => 'przeglad-przeszczepu-wlosow', 'pt' => 'visao-geral-do-transplante-capilar', 'ro' => 'prezentare-generala-transplant-de-par' ),
		'sapphire-fue-hair-transplant'                            => array( 'en' => 'sapphire-fue-hair-transplant', 'tr' => 'safir-fue-sac-ekimi', 'fr' => 'greffe-de-cheveux-fue-sapphire', 'it' => 'trapianto-di-capelli-fue-sapphire', 'es' => 'trasplante-capilar-fue-sapphire', 'pl' => 'przeszczep-wlosow-metoda-sapphire-fue', 'pt' => 'transplante-capilar-fue-sapphire', 'ro' => 'transplant-de-par-fue-sapphire' ),
		'dhi-hair-transplant'                                     => array( 'en' => 'dhi-hair-transplant', 'tr' => 'dhi-sac-ekimi', 'fr' => 'greffe-de-cheveux-dhi', 'it' => 'trapianto-di-capelli-dhi', 'es' => 'trasplante-de-cabello-dhi', 'pl' => 'przeszczep-wlosow-dhi', 'pt' => 'transplante-capilar-dhi', 'ro' => 'transplant-de-par-dhi' ),
		'exosome-fue-hair-transplant'                             => array( 'en' => 'exosome-fue-hair-transplant', 'tr' => 'exosome-fue-sac-ekimi', 'fr' => 'greffe-capillaire-exosome-fue', 'it' => 'trapianto-di-capelli-exosome-fue', 'es' => 'trasplante-capilar-exosome-fue', 'pl' => 'przeszczep-wlosow-exosome-fue', 'pt' => 'transplante-capilar-exosome-fue', 'ro' => 'transplant-de-par-exosome-fue' ),
		'vita-treatment'                                          => array( 'en' => 'vita-treatment', 'tr' => 'vita-tedavisi', 'fr' => 'traitement-vita', 'it' => 'trattamento-vita', 'es' => 'tratamiento-vita', 'pl' => 'leczenie-vita', 'pt' => 'tratamento-vita', 'ro' => 'tratament-vita' ),
		'tricholab'                                               => array( 'en' => 'tricholab', 'tr' => 'tricholab', 'fr' => 'tricholab', 'it' => 'tricholab', 'es' => 'tricholab', 'pl' => 'tricholab', 'pt' => 'tricholab', 'ro' => 'tricholab' ),
		'female-hair-transplant'                                  => array( 'en' => 'female-hair-transplant', 'tr' => 'kadin-sac-ekimi', 'fr' => 'greffe-de-cheveux-feminine', 'it' => 'trapianto-di-capelli-femminile', 'es' => 'trasplante-capilar-femenino', 'pl' => 'przeszczep-wlosow-u-kobiet', 'pt' => 'transplante-capilar-feminino', 'ro' => 'transplant-de-par-la-femei' ),
		'eyebrow-transplant'                                      => array( 'en' => 'eyebrow-transplant', 'tr' => 'kas-ekimi', 'fr' => 'transplantation-de-sourcils', 'it' => 'trapianto-di-sopracciglia', 'es' => 'trasplante-de-cejas', 'pl' => 'przeszczep-brwi', 'pt' => 'transplante-de-sobrancelhas', 'ro' => 'transplant-de-sprancene' ),
		'beard-transplant'                                        => array( 'en' => 'beard-transplant', 'tr' => 'sakal-ekimi', 'fr' => 'transplantation-de-barbe', 'it' => 'beard-trapianto-di-barba', 'es' => 'trasplante-de-barba', 'pl' => 'przeszczep-brody', 'pt' => 'transplante-de-barba', 'ro' => 'transplant-de-barba' ),
		'hair-mesotherapy'                                        => array( 'en' => 'hair-mesotherapy', 'tr' => 'sac-mezoterapisi', 'fr' => 'mesotherapie-capillaire', 'it' => 'mesoterapia-per-capelli', 'es' => 'mesoterapia-capilar', 'pl' => 'mezoterapia-wlosow', 'pt' => 'mesoterapia-capilar', 'ro' => 'mezoterapie-capilara' ),
		'pre-hair-transplant-period'                              => array( 'en' => 'pre-hair-transplant-period', 'tr' => 'sac-ekim-oncesi-donem', 'fr' => 'periode-pre-transplantation-capillaire', 'it' => 'periodo-pre-trapianto-di-capelli', 'es' => 'periodo-previo-al-trasplante-capilar', 'pl' => 'okres-przed-przeszczepem-wlosow', 'pt' => 'periodo-pre-transplante-capilar', 'ro' => 'perioada-dinainte-de-transplantul-de-par' ),
		'post-hair-transplant-period'                             => array( 'en' => 'post-hair-transplant-period', 'tr' => 'sac-ekimi-sonrasi-donem', 'fr' => 'periode-post-greffe-de-cheveux', 'it' => 'periodo-post-trapianto-di-capelli', 'es' => 'periodo-posterior-al-trasplante-capilar', 'pl' => 'okres-po-przeszczepie-wlosow', 'pt' => 'periodo-pos-transplante-capilar', 'ro' => 'perioada-de-dupa-transplantul-de-par' ),
		'hair-transplant-techniques-comparison2'                  => array( 'en' => 'hair-transplant-techniques-comparison2', 'tr' => 'sac-ekimi-teknikleri-karsilastirmasi-2', 'fr' => 'comparaison-des-techniques-de-greffe-de-cheveux-2', 'it' => 'confronto-tra-tecniche-di-trapianto-capillare-2', 'es' => 'comparacion-de-tecnicas-de-trasplante-capilar-2', 'pl' => 'porownanie-technik-przeszczepu-wlosow-2', 'pt' => 'comparacao-das-tecnicas-de-transplante-capilar-2', 'ro' => 'comparatie-intre-tehnicile-de-transplant-de-par-2' ),

		// Plastic surgery.
		'plastic-surgery-overview'                               => array( 'en' => 'plastic-surgery-overview', 'tr' => 'plastic-surgery-overview', 'fr' => 'plastic-surgery-overview', 'it' => 'plastic-surgery-overview', 'es' => 'plastic-surgery-overview', 'pl' => 'plastic-surgery-overview', 'pt' => 'plastic-surgery-overview', 'ro' => 'plastic-surgery-overview' ),
		'rhinoplasty'                                             => array( 'en' => 'rhinoplasty', 'tr' => 'burun-estetigi', 'fr' => 'rhinoplastie', 'it' => 'rinoplastica', 'es' => 'rinoplastia', 'pl' => 'rynoplastyka', 'pt' => 'rinoplastia', 'ro' => 'rinoplastie' ),
		'breast-aesthetics-breast-surgery'                        => array( 'en' => 'breast-aesthetics-breast-surgery', 'tr' => 'meme-estetigi-gogus-estetigi', 'fr' => 'esthetique-mammaire-chirurgie-mammaire', 'it' => 'estetica-del-seno-chirurgia-del-seno', 'es' => 'estetica-mamaria-cirugia-de-pecho', 'pl' => 'estetyka-piersi-chirurgia-piersi', 'pt' => 'estetica-mamaria-cirurgia-de-mama', 'ro' => 'estetica-sanilor-chirurgia-sanilor' ),
		'bbl'                                                      => array( 'en' => 'bbl', 'tr' => 'bbl', 'fr' => 'bbl', 'it' => 'bbl', 'es' => 'bbl', 'pl' => 'bbl', 'pt' => 'bbl', 'ro' => 'bbl' ),
		'liposuction'                                              => array( 'en' => 'liposuction', 'tr' => 'liposuction', 'fr' => 'liposuccion', 'it' => 'liposuzione', 'es' => 'liposuccion', 'pl' => 'liposukcja', 'pt' => 'lipoaspiracao', 'ro' => 'liposuctie' ),
		'face-and-neck-lift-surgery'                              => array( 'en' => 'face-and-neck-lift-surgery', 'tr' => 'yuz-ve-boyun-germe-ameliyati', 'fr' => 'chirurgie-de-lifting-du-visage-et-du-cou', 'it' => 'chirurgia-di-lifting-del-viso-e-del-collo', 'es' => 'cirugia-de-lifting-facial-y-de-cuello', 'pl' => 'chirurgia-liftingu-twarzy-i-szyi', 'pt' => 'cirurgia-de-lifting-facial-e-de-pescoco', 'ro' => 'lifting-facial-si-de-gat' ),
		'abdominoplasty-tummy-tuck'                               => array( 'en' => 'abdominoplasty-tummy-tuck', 'tr' => 'karin-germe-ameliyati', 'fr' => 'abdominoplastie', 'it' => 'addominoplastica', 'es' => 'abdominoplastia', 'pl' => 'plastyka-brzucha', 'pt' => 'abdominoplastia', 'ro' => 'abdominoplastie' ),
		'gynecomastia'                                            => array( 'en' => 'gynecomastia', 'tr' => 'jinekomasti', 'fr' => 'gynecomastie', 'it' => 'ginecomastia', 'es' => 'ginecomastia', 'pl' => 'ginekomastia', 'pt' => 'ginecomastia', 'ro' => 'ginecomastie' ),
		'obesity-surgeries-bariatric-surgery-and-gastric-balloon' => array( 'en' => 'obesity-surgeries-bariatric-surgery-and-gastric-balloon', 'tr' => 'obezite-ameliyatlari-bariatrik-cerrahi-ve-mide-balonu', 'fr' => 'chirurgies-de-l-obesite-chirurgie-bariatrique-et-ballon-gastrique', 'it' => 'chirurgie-dell-obesita-chirurgia-bariatrica-e-palloncino-gastrico', 'es' => 'cirugias-de-obesidad-cirugia-bariatrica-y-balon-gastrico', 'pl' => 'operacje-otylosci-chirurgia-bariatryczna-i-balon-zoladkowy', 'pt' => 'cirurgias-de-obesidade-cirurgia-bariatrica-e-balao-gastrico', 'ro' => 'operatii-de-obezitate-chirurgie-bariatrica-si-balon-gastric' ),

		// Dental treatment.
		'dental-treatment-overview'                               => array( 'en' => 'dental-treatment-overview', 'tr' => 'dis-tedavisi-genel-bakis', 'fr' => 'apercu-du-traitement-dentaire', 'it' => 'panoramica-del-trattamento-dentale', 'es' => 'resumen-del-tratamiento-dental', 'pl' => 'przeglad-leczenia-stomatologicznego', 'pt' => 'visao-geral-do-tratamento-dentario', 'ro' => 'prezentare-generala-tratament-dentar' ),
		'dental-implant'                                          => array( 'en' => 'dental-implant', 'tr' => 'dis-implanti', 'fr' => 'implant-dentaire', 'it' => 'impianto-dentale', 'es' => 'implante-dental', 'pl' => 'implant-zebowy', 'pt' => 'implante-dentario', 'ro' => 'implant-dentar' ),
		'hollywood-smile'                                         => array( 'en' => 'hollywood-smile', 'tr' => 'hollywood-gulusu', 'fr' => 'sourire-hollywoodien', 'it' => 'sorriso-hollywoodiano', 'es' => 'sonrisa-hollywoodense', 'pl' => 'hollywoodzki-usmiech', 'pt' => 'sorriso-hollywoodiano', 'ro' => 'zambet-de-hollywood' ),
	);
}

/**
 * Exact blog post slugs. Empty values mean that language has no indexed copy.
 */
function estecapelli_indexed_blog_slugs() {
	return array(
		'hair-transplant-turkey-complete-expert-guide'              => array( 'en' => 'hair-transplant-turkey-complete-expert-guide', 'it' => 'trapianto-capelli-in-turchia-la-guida-completa' ),
		'unshaven-hair-transplant'                                  => array( 'en' => 'unshaven-hair-transplant', 'tr' => 'tirassiz-sac-ekimi', 'fr' => 'greffe-de-cheveux-sans-rasage', 'it' => 'trapianto-di-capelli-senza-rasatura', 'es' => 'trasplante-de-cabello-sin-afeitar', 'pl' => 'przeszczep-wlosow-bez-golenia', 'pt' => 'transplante-capilar-sem-barbear', 'ro' => 'transplant-de-par-fara-barbierire' ),
		'can-diabetic-patients-undergo-a-hair-transplant'           => array( 'en' => 'can-diabetic-patients-undergo-a-hair-transplant', 'tr' => 'diyabet-hastalari-sac-ekimi-yaptirabilir-mi', 'fr' => 'les-patients-diabetiques-peuvent-ils-subir-une-greffe-de-cheveux', 'it' => 'i-pazienti-diabetici-possono-sottoporsi-a-un-trapianto-di-capelli', 'es' => 'los-pacientes-diabeticos-pueden-someterse-a-un-trasplante-capilar', 'pl' => 'czy-pacjenci-z-cukrzyca-moga-poddac-sie-przeszczepowi-wlosow', 'pt' => 'pacientes-com-diabetes-podem-realizar-transplante-capilar', 'ro' => 'pot-pacientii-diabetici-sa-faca-transplant-de-par' ),
		'is-hair-transplant-a-painful-procedure'                    => array( 'en' => 'is-hair-transplant-a-painful-procedure', 'tr' => 'sac-ekimi-agrili-bir-islem-midir', 'fr' => 'la-greffe-de-cheveux-est-elle-une-procedure-douloureuse', 'it' => 'il-trapianto-di-capelli-e-una-procedura-dolorosa', 'es' => '-es-doloroso-el-trasplante-capilar', 'pl' => 'czy-przeszczep-wlosow-jest-bolesnym-zabiegiem', 'pt' => 'o-transplante-capilar-e-um-procedimento-doloroso', 'ro' => 'este-transplantul-de-par-o-procedura-dureroasa' ),
		'will-my-hair-fall-out-again-after-a-hair-transplant'       => array( 'en' => 'will-my-hair-fall-out-again-after-a-hair-transplant', 'tr' => 'sac-ekimi-sonrasinda-saclarim-tekrar-dokulur-mu', 'fr' => 'mes-cheveux-vont-ils-retomber-apres-une-greffe-de-cheveux', 'it' => 'i-miei-capelli-ricadranno-dopo-un-trapianto-di-capelli', 'es' => '-se-volvera-a-caer-mi-cabello-despues-de-un-trasplante-capilar', 'pl' => 'czy-moje-wlosy-wypadna-ponownie-po-przeszczepie-wlosow', 'pt' => 'meu-cabelo-vai-cair-novamente-apos-o-transplante-capilar', 'ro' => 'imi-va-cadea-parul-din-nou-dupa-transplantul-de-par' ),
		'hair-transplant-with-the-fue-vita-technique'               => array( 'en' => 'hair-transplant-with-the-fue-vita-technique', 'tr' => 'fue-vita-teknigi-ile-sac-ekimi', 'fr' => 'greffe-de-cheveux-avec-la-technique-fue-vita', 'it' => 'trapianto-di-capelli-con-la-tecnica-fue-vita', 'es' => 'trasplante-capilar-con-la-tecnica-fue-vita', 'pl' => 'przeszczep-wlosow-technika-fue-vita', 'pt' => 'transplante-capilar-com-a-tecnica-fue-vita', 'ro' => 'transplant-de-par-cu-tehnica-fue-vita' ),
		'hair-transplant-for-hiv-positive-patients'                 => array( 'en' => 'hair-transplant-for-hiv-positive-patients', 'tr' => 'hiv-pozitif-hastalara-sac-ekimi', 'fr' => 'greffe-de-cheveux-pour-les-patients-seropositifs-vih', 'it' => 'trapianto-di-capelli-per-pazienti-sieropositivi-hiv', 'es' => 'trasplante-capilar-para-pacientes-vih-positivos', 'pl' => 'przeszczep-wlosow-u-pacjentow-hiv-pozytywnych', 'pt' => 'transplante-capilar-para-pacientes-hiv-positivos', 'ro' => 'transplant-de-par-pentru-pacientii-hiv-pozitivi' ),
		'unshaven-hair-transplant-for-women'                        => array( 'en' => 'unshaven-hair-transplant-for-women', 'tr' => 'kadinlarda-trassiz-sac-ekimi', 'fr' => 'greffe-de-cheveux-sans-rasage-chez-la-femme', 'it' => 'trapianto-di-capelli-senza-rasatura-nelle-donne', 'es' => 'trasplante-capilar-sin-rasurado-en-mujeres', 'pl' => 'przeszczep-wlosow-bez-golenia-u-kobiet', 'pt' => 'transplante-capilar-sem-raspagem-em-mulheres', 'ro' => 'transplant-de-par-fara-barbierire-la-femei' ),
	);
}

/**
 * Hair-transplant leaf routes stored as hierarchical Page records.
 *
 * They share the public treatment URL structure, but must never be resolved as
 * `treatment` posts. Treating them as CPT entries makes WordPress fall back to
 * the Hair Transplant landing when the matching treatment post does not exist.
 *
 * @return string[]
 */
function estecapelli_indexed_hair_care_page_slugs() {
	return array(
		'tricholab',
		'pre-hair-transplant-period',
		'post-hair-transplant-period',
	);
}

/**
 * Category owning an English treatment slug.
 */
function estecapelli_indexed_treatment_category( $slug ) {
	$hair = array(
		'hair-transplant-overview', 'sapphire-fue-hair-transplant', 'dhi-hair-transplant',
		'exosome-fue-hair-transplant', 'vita-treatment', 'tricholab', 'female-hair-transplant',
		'eyebrow-transplant', 'beard-transplant', 'hair-mesotherapy',
		'pre-hair-transplant-period', 'post-hair-transplant-period',
		'hair-transplant-techniques-comparison2',
	);
	$plastic = array(
		'plastic-surgery-overview', 'rhinoplasty', 'breast-aesthetics-breast-surgery',
		'bbl', 'liposuction', 'face-and-neck-lift-surgery', 'abdominoplasty-tummy-tuck',
		'gynecomastia', 'obesity-surgeries-bariatric-surgery-and-gastric-balloon',
	);

	if ( in_array( $slug, $hair, true ) ) {
		return 'hair-transplant';
	}
	if ( in_array( $slug, $plastic, true ) ) {
		return 'plastic-surgery';
	}
	return array_key_exists( $slug, estecapelli_indexed_treatment_slugs() ) ? 'dental-treatment' : '';
}

/**
 * Complete canonical route contract, keyed by English indexed path.
 *
 * @return array<string,array<string,string>>
 */
function estecapelli_indexed_route_contract() {
	static $routes = null;
	if ( null !== $routes ) {
		return $routes;
	}

	$routes   = array();
	$langs    = estecapelli_indexed_languages();
	$sections = estecapelli_indexed_section_slugs();
	$about    = estecapelli_indexed_about_slugs();

	foreach ( $sections as $source_slug => $localized ) {
		$key = 'home' === $source_slug ? '/en' : '/en/' . $source_slug;
		foreach ( $langs as $lang ) {
			$routes[ $key ][ $lang ] = 'home' === $source_slug
				? '/' . $lang . '/'
				: '/' . $lang . '/' . $localized[ $lang ];
		}
	}

	foreach ( $about as $source_slug => $localized ) {
		$key = '/en/about-us/' . $source_slug;
		foreach ( $langs as $lang ) {
			$routes[ $key ][ $lang ] = '/' . $lang . '/' . $sections['about-us'][ $lang ] . '/' . $localized[ $lang ];
		}
	}

	foreach ( estecapelli_indexed_treatment_slugs() as $source_slug => $localized ) {
		$category = estecapelli_indexed_treatment_category( $source_slug );
		$key      = '/en/' . $category . '/' . $source_slug;
		foreach ( $langs as $lang ) {
			$path = '/' . $lang . '/' . $sections[ $category ][ $lang ];
			if ( 'hair-transplant-techniques-comparison2' === $source_slug ) {
				$path .= '/' . estecapelli_indexed_treatment_slugs()['dhi-hair-transplant'][ $lang ];
			}
			$routes[ $key ][ $lang ] = $path . '/' . $localized[ $lang ];
		}
	}

	/*
	 * Every profile lives under our-doctors. mehmet-hanifi-kutlar used to sit
	 * under medical-director — the path the old live site had indexed, not
	 * anything to do with his title — and moved here when that page was
	 * retired. inc/redirects.php 301s his old path in all eight languages.
	 */
	$doctor_parents = array(
		'mehmet-hanifi-kutlar'  => 'our-doctors',
		'prof-dr-binnur-ustun'  => 'our-doctors',
		'op-dr-hasan-celik'     => 'our-doctors',
		'op-dr-mehmet-palali'   => 'our-doctors',
		'op-dr-necdet-derici'   => 'our-doctors',
	);
	foreach ( $doctor_parents as $doctor => $parent ) {
		$key = '/en/about-us/' . $parent . '/' . $doctor;
		foreach ( $langs as $lang ) {
			$routes[ $key ][ $lang ] = '/' . $lang . '/' . $sections['about-us'][ $lang ] . '/' . $about[ $parent ][ $lang ] . '/' . $doctor;
		}
	}

	$result_slugs = array( 'beforeafte_13', 'beforeafter_15', 'beforeafter_16', 'beforeafter_17', 'beforeafter_18' );
	foreach ( array( 'vitatreatment' => 10, 'exosomefue' => 12, 'exosome' => 5, 'dhi' => 3 ) as $method => $total ) {
		for ( $i = 1; $i <= $total; $i++ ) {
			$result_slugs[] = 'beforeafter-hairtransplant-' . $method . '-' . $i;
		}
	}
	foreach ( $result_slugs as $result_slug ) {
		$key = '/en/before-after/' . $result_slug;
		foreach ( $langs as $lang ) {
			$routes[ $key ][ $lang ] = '/' . $lang . '/' . $sections['before-after'][ $lang ] . '/' . $result_slug;
		}
	}

	foreach ( estecapelli_indexed_blog_slugs() as $source_slug => $localized ) {
		$key = '/en/blog/' . $source_slug;
		foreach ( $localized as $lang => $slug ) {
			// Unlike every other map, blog rows are sparse — a language appears
			// only where an indexed copy exists. Honour the active language list
			// anyway, so a language whose slugs are staged but not yet switched
			// on does not leak routes here ahead of the rest of the site.
			if ( ! in_array( $lang, $langs, true ) ) {
				continue;
			}
			$routes[ $key ][ $lang ] = '/' . $lang . '/blog/' . $slug;
		}
	}

	return $routes;
}

/**
 * Canonical source key for a path in any indexed language.
 */
function estecapelli_indexed_route_key( $path ) {
	static $reverse = null;
	$path = '/' . trim( (string) wp_parse_url( $path, PHP_URL_PATH ), '/' );
	$path = untrailingslashit( $path );
	$path = $path ?: '/';
	if ( null === $reverse ) {
		$reverse = array();
		foreach ( estecapelli_indexed_route_contract() as $key => $localized ) {
			foreach ( $localized as $route ) {
				$normalized_route             = untrailingslashit( $route ) ?: '/';
				$reverse[ $normalized_route ] = $key;
			}
		}
		$home_leaves = estecapelli_legacy_home_slugs();
		if ( function_exists( 'estecapelli_home_page_slugs' ) ) {
			// Romanian has no legacy leaf but does have a Home page, so its slug
			// only reaches the reverse map through here.
			$home_leaves = array_merge( $home_leaves, estecapelli_home_page_slugs() );
		}
		foreach ( $home_leaves as $language => $slug ) {
			if ( '' === $slug ) {
				continue;
			}
			$reverse[ '/' . $language . '/' . $slug ] = '/en';
		}
		$reverse['/pt-pt/inicio'] = '/en';
	}
	return $reverse[ $path ] ?? '';
}

/**
 * Exact relative route for an English source path and target language.
 */
function estecapelli_indexed_route_path( $english_path, $language = '' ) {
	$language     = estecapelli_indexed_language_code( $language );
	$english_path = '/' . trim( (string) wp_parse_url( $english_path, PHP_URL_PATH ), '/' );
	$english_path = untrailingslashit( $english_path );
	if ( in_array( $english_path, array( '/', '/en', '/en/home' ), true ) ) {
		$english_path = '/en';
	}

	$routes = estecapelli_indexed_route_contract();
	return $routes[ $english_path ][ $language ] ?? '';
}

/**
 * Full exact URL for an English source path and target language.
 */
function estecapelli_indexed_url( $english_path, $language = '' ) {
	$route = estecapelli_indexed_route_path( $english_path, $language );
	if ( ! $route ) {
		return estecapelli_localize_theme_url( (string) $english_path, $language, false );
	}

	$fragment = (string) wp_parse_url( (string) $english_path, PHP_URL_FRAGMENT );
	$query    = (string) wp_parse_url( (string) $english_path, PHP_URL_QUERY );
	$url      = estecapelli_unfiltered_home_url() . $route;
	if ( $query ) {
		$url .= '?' . $query;
	}
	if ( $fragment ) {
		$url .= '#' . $fragment;
	}
	return $url;
}

/** Exact indexed homepage used by the clickable logo. */
function estecapelli_language_root_url( $language = '' ) {
	return estecapelli_indexed_url( '/en', $language );
}

/**
 * Normalize a WPML URL to one indexed prefix, retaining its translated path.
 */
function estecapelli_normalize_wpml_url( $url, $language ) {
	$url      = (string) $url;
	$language = estecapelli_indexed_language_code( $language );
	$base     = estecapelli_unfiltered_home_url();
	$parts    = wp_parse_url( $url );
	$base_p   = wp_parse_url( $base );

	if ( ! is_array( $parts ) ) {
		return $url;
	}
	if ( ! empty( $parts['host'] ) && ! empty( $base_p['host'] ) && strtolower( $parts['host'] ) !== strtolower( $base_p['host'] ) ) {
		return $url;
	}

	$path      = (string) ( $parts['path'] ?? '' );
	$base_path = untrailingslashit( (string) ( $base_p['path'] ?? '' ) );
	if ( $base_path && ( $path === $base_path || 0 === strpos( $path, $base_path . '/' ) ) ) {
		$path = substr( $path, strlen( $base_path ) );
	}

	$segments = array_values( array_filter( explode( '/', trim( $path, '/' ) ), 'strlen' ) );
	$known    = array_merge( estecapelli_indexed_languages(), array( 'pt-pt' ) );
	while ( $segments && in_array( strtolower( $segments[0] ), $known, true ) ) {
		array_shift( $segments );
	}

	$normalized = $base . '/' . $language . '/';
	if ( $segments ) {
		$normalized .= implode( '/', $segments );
	}
	if ( ! empty( $parts['query'] ) ) {
		$normalized .= '?' . $parts['query'];
	}
	if ( ! empty( $parts['fragment'] ) ) {
		$normalized .= '#' . $parts['fragment'];
	}

	return $normalized;
}

/**
 * Localize an internal theme URL only when it belongs to the route contract.
 */
function estecapelli_localize_theme_url( $url, $language = '', $use_contract = true ) {
	$url = (string) $url;
	if ( '' === $url || '#' === $url[0] || preg_match( '#^(?:mailto|tel|sms|javascript|data):#i', $url ) ) {
		return $url;
	}

	$parts  = wp_parse_url( $url );
	$base_p = wp_parse_url( estecapelli_unfiltered_home_url() );
	if ( ! is_array( $parts ) ) {
		return $url;
	}
	if ( ! empty( $parts['host'] ) && ! empty( $base_p['host'] ) && strtolower( $parts['host'] ) !== strtolower( $base_p['host'] ) ) {
		return $url;
	}

	$path = (string) ( $parts['path'] ?? '' );
	$key  = $use_contract ? estecapelli_indexed_route_key( $path ) : '';
	if ( ! $key && $use_contract && preg_match( '#^/?en(?:/|$)#', $path ) ) {
		$key = '/' . trim( $path, '/' );
	}
	if ( $key && estecapelli_indexed_route_path( $key, $language ) ) {
		$suffix = '';
		if ( ! empty( $parts['query'] ) ) {
			$suffix .= '?' . $parts['query'];
		}
		if ( ! empty( $parts['fragment'] ) ) {
			$suffix .= '#' . $parts['fragment'];
		}
		return estecapelli_indexed_url( $key, $language ) . $suffix;
	}

	if ( preg_match( '#^/(?:en|tr|fr|it|es|pl|pt|pt-pt|ro)(?:/|$)#i', $path ) ) {
		$first = strtolower( explode( '/', trim( $path, '/' ) )[0] );
		return estecapelli_normalize_wpml_url( $url, 'pt-pt' === $first ? 'pt' : $first );
	}

	return $url;
}

/**
 * WPML languages for the theme switchers, with exact indexed URLs.
 */
function estecapelli_header_languages() {
	$languages = apply_filters( 'wpml_active_languages', null, array( 'skip_missing' => 0 ) );
	if ( ! is_array( $languages ) || ! $languages ) {
		return array(
			array(
				'active'           => 1,
				'language_code'    => 'en',
				'native_name'      => 'English',
				'translated_name'  => 'English',
				'country_flag_url' => '',
				'url'              => estecapelli_indexed_url( '/en', 'en' ),
			),
		);
	}

	// Prefer a custom `pt` language when both Portuguese variants are active.
	// Otherwise expose the built-in pt-pt language through the indexed /pt/ URL.
	$has_custom_pt = false;
	foreach ( $languages as $language ) {
		if ( 'pt' === strtolower( (string) ( $language['language_code'] ?? '' ) ) ) {
			$has_custom_pt = true;
			break;
		}
	}

	$current_key = estecapelli_indexed_route_key( isset( $_SERVER['REQUEST_URI'] ) ? wp_unslash( $_SERVER['REQUEST_URI'] ) : '' );
	$normalized  = array();
	foreach ( $languages as $language ) {
		$raw_code = strtolower( (string) ( $language['language_code'] ?? '' ) );
		if ( 'pt-pt' === $raw_code && $has_custom_pt ) {
			continue;
		}

		$code                      = estecapelli_indexed_language_code( $raw_code );
		$language['language_code'] = $code;
		if ( $current_key && empty( $language['missing'] ) && estecapelli_indexed_route_path( $current_key, $code ) ) {
			$language['url'] = estecapelli_indexed_url( $current_key, $code );
		} else {
			// Unknown/missing translations must not introduce a guessed route.
			$language['url'] = estecapelli_indexed_url( '/en', $code );
		}
		$normalized[] = $language;
	}

	return $normalized ?: array_values( $languages );
}

/**
 * Backwards-compatible name used throughout the theme navigation.
 */
function estecapelli_nav_url( $english_path ) {
	return estecapelli_indexed_url( $english_path );
}

/** English legal source slug => localized title and stable leaf slug. */
function estecapelli_legal_pages_manifest() {
	return array(
		'tr' => array(
			'privacy-policy'  => array( 'slug' => 'gizlilik-politikasi', 'title' => 'Gizlilik Politikası' ),
			'terms'           => array( 'slug' => 'kullanim-kosullari', 'title' => 'Kullanım Koşulları' ),
			'kvkk-disclosure' => array( 'slug' => 'kvkk-aydinlatma-metni', 'title' => 'KVKK Aydınlatma Metni' ),
			'cookie-policy'   => array( 'slug' => 'cerez-politikasi', 'title' => 'Çerez Politikası' ),
		),
		'fr' => array(
			'privacy-policy'  => array( 'slug' => 'politique-de-confidentialite', 'title' => 'Politique de confidentialité' ),
			'terms'           => array( 'slug' => 'conditions-d-utilisation', 'title' => 'Conditions d’utilisation' ),
			'kvkk-disclosure' => array( 'slug' => 'notice-de-traitement-des-donnees-kvkk', 'title' => 'Notice d’information KVKK sur le traitement des données' ),
			'cookie-policy'   => array( 'slug' => 'politique-relative-aux-cookies', 'title' => 'Politique relative aux cookies' ),
		),
		'it' => array(
			'privacy-policy'  => array( 'slug' => 'informativa-sulla-privacy', 'title' => 'Informativa sulla privacy' ),
			'terms'           => array( 'slug' => 'termini-e-condizioni', 'title' => 'Termini e condizioni' ),
			'kvkk-disclosure' => array( 'slug' => 'informativa-sul-trattamento-dei-dati-kvkk', 'title' => 'Informativa KVKK sul trattamento dei dati' ),
			'cookie-policy'   => array( 'slug' => 'informativa-sui-cookie', 'title' => 'Informativa sui cookie' ),
		),
		'es' => array(
			'privacy-policy'  => array( 'slug' => 'politica-de-privacidad', 'title' => 'Política de privacidad' ),
			'terms'           => array( 'slug' => 'terminos-y-condiciones', 'title' => 'Términos y condiciones' ),
			'kvkk-disclosure' => array( 'slug' => 'aviso-de-tratamiento-de-datos-kvkk', 'title' => 'Aviso KVKK sobre el tratamiento de datos' ),
			'cookie-policy'   => array( 'slug' => 'politica-de-cookies', 'title' => 'Política de cookies' ),
		),
		'pl' => array(
			'privacy-policy'  => array( 'slug' => 'polityka-prywatnosci', 'title' => 'Polityka prywatności' ),
			'terms'           => array( 'slug' => 'regulamin', 'title' => 'Regulamin' ),
			'kvkk-disclosure' => array( 'slug' => 'klauzula-informacyjna-kvkk', 'title' => 'Klauzula informacyjna KVKK dotycząca przetwarzania danych' ),
			'cookie-policy'   => array( 'slug' => 'polityka-plikow-cookie', 'title' => 'Polityka plików cookie' ),
		),
		'pt' => array(
			'privacy-policy'  => array( 'slug' => 'politica-de-privacidade', 'title' => 'Política de privacidade' ),
			'terms'           => array( 'slug' => 'termos-e-condicoes', 'title' => 'Termos e condições' ),
			'kvkk-disclosure' => array( 'slug' => 'aviso-de-tratamento-de-dados-kvkk', 'title' => 'Aviso KVKK sobre o tratamento de dados' ),
			'cookie-policy'   => array( 'slug' => 'politica-de-cookies', 'title' => 'Política de cookies' ),
		),
		'ro' => array(
			'privacy-policy'  => array( 'slug' => 'politica-de-confidentialitate', 'title' => 'Politica de confidențialitate' ),
			'terms'           => array( 'slug' => 'termeni-si-conditii', 'title' => 'Termeni și condiții' ),
			'kvkk-disclosure' => array( 'slug' => 'nota-de-informare-kvkk', 'title' => 'Notă de informare KVKK privind prelucrarea datelor' ),
			'cookie-policy'   => array( 'slug' => 'politica-de-cookie-uri', 'title' => 'Politica de cookie-uri' ),
		),
	);
}

/** Exact public route for one legal source slug and language. */
function estecapelli_legal_page_route( $english_path, $language = '' ) {
	$source_slug = trim( (string) $english_path, '/' );
	$language    = estecapelli_indexed_language_code( $language );
	$manifest    = estecapelli_legal_pages_manifest();
	if ( ! isset( $manifest['tr'][ $source_slug ] ) ) {
		return '';
	}
	if ( 'en' === $language ) {
		return '/en/' . $source_slug;
	}
	return isset( $manifest[ $language ][ $source_slug ] )
		? '/' . $language . '/' . $manifest[ $language ][ $source_slug ]['slug']
		: '';
}

/**
 * Resolve a real translated page outside the legacy indexed set (legal pages).
 * It never guesses a translated slug; if no translation exists, English is the
 * safe published fallback.
 */
function estecapelli_translated_page_url( $english_path, $language = '' ) {
	$source_slug = trim( (string) $english_path, '/' );
	$source_id   = function_exists( 'estecapelli_source_post_id' )
		? estecapelli_source_post_id( $source_slug, 'page' )
		: 0;
	$source      = $source_id ? get_post( $source_id ) : get_page_by_path( $source_slug, OBJECT, 'page' );
	if ( ! $source ) {
		return estecapelli_unfiltered_home_url() . '/en/' . $source_slug;
	}
	$source_id = (int) apply_filters( 'wpml_object_id', $source->ID, 'page', true, 'en' );
	$source    = get_post( $source_id ?: $source->ID );
	if ( ! $source ) {
		return estecapelli_unfiltered_home_url() . '/en/' . $source_slug;
	}

	$language      = estecapelli_indexed_language_code( $language );
	$wpml_language = estecapelli_wpml_language_code( $language );
	$route         = estecapelli_legal_page_route( $source_slug, $language );
	if ( 'en' === $language ) {
		return estecapelli_unfiltered_home_url() . $route;
	}
	$target = (int) apply_filters( 'wpml_object_id', $source->ID, 'page', false, $wpml_language );
	return $target && $route
		? estecapelli_unfiltered_home_url() . $route
		: estecapelli_unfiltered_home_url() . estecapelli_legal_page_route( $source_slug, 'en' );
}

/**
 * Catch legacy home_url('/en/...') calls and seed defaults at one boundary.
 * Only paths present in the contract are changed.
 */
function estecapelli_indexed_home_url_filter( $url, $path ) {
	$source = '/' . trim( (string) wp_parse_url( (string) $path, PHP_URL_PATH ), '/' );
	if ( '/en/home' === $source ) {
		$source = '/en';
	}
	if ( isset( estecapelli_indexed_route_contract()[ $source ] ) ) {
		return estecapelli_indexed_url( $source );
	}
	return estecapelli_localize_theme_url( $url );
}
add_filter( 'home_url', 'estecapelli_indexed_home_url_filter', 999, 2 );

/** Redirect every retired homepage leaf URL to its language root. */
function estecapelli_redirect_legacy_language_home() {
	if ( is_admin() ) {
		return;
	}

	$request = isset( $_SERVER['REQUEST_URI'] ) ? wp_unslash( $_SERVER['REQUEST_URI'] ) : '';
	$path    = strtolower( untrailingslashit( (string) wp_parse_url( $request, PHP_URL_PATH ) ) );
	$targets = array();
	$leaves  = estecapelli_legacy_home_slugs();
	if ( function_exists( 'estecapelli_home_page_slugs' ) ) {
		// The Home pages reuse these leaves, plus a Romanian one that never
		// existed on the legacy structure. Both must 301 to the language root.
		$leaves = array_merge( $leaves, estecapelli_home_page_slugs() );
	}
	foreach ( $leaves as $language => $slug ) {
		// A language with no legacy leaf must not register '/xx/' as a target;
		// that is the language root itself, and redirecting it to itself would
		// be a loop.
		if ( '' === $slug ) {
			continue;
		}
		$targets[ '/' . $language . '/' . $slug ] = $language;
	}
	$targets['/pt-pt/inicio'] = 'pt';

	if ( ! isset( $targets[ $path ] ) ) {
		return;
	}

	$query  = (string) wp_parse_url( $request, PHP_URL_QUERY );
	$target = estecapelli_unfiltered_home_url() . '/' . $targets[ $path ] . '/';
	if ( $query ) {
		$target .= '?' . $query;
	}
	wp_safe_redirect( $target, 301 );
	exit;
}
add_action( 'template_redirect', 'estecapelli_redirect_legacy_language_home', -2 );

/**
 * Keep legacy overview/comparison URLs in their requested language.
 *
 * These four indexed paths never had standalone source records. Their English
 * counterparts already resolve to the owning landing/service page, so mirror
 * that behaviour without allowing WPML to fall back to the English target.
 */
function estecapelli_redirect_legacy_service_aliases() {
	if ( is_admin() ) {
		return;
	}

	$request = isset( $_SERVER['REQUEST_URI'] ) ? wp_unslash( $_SERVER['REQUEST_URI'] ) : '';
	$key     = estecapelli_indexed_route_key( $request );
	$aliases = array(
		'/en/hair-transplant/hair-transplant-overview'              => '/en/hair-transplant',
		'/en/plastic-surgery/plastic-surgery-overview'              => '/en/plastic-surgery',
		'/en/dental-treatment/dental-treatment-overview'             => '/en/dental-treatment',
		'/en/hair-transplant/hair-transplant-techniques-comparison2' => '/en/hair-transplant/dhi-hair-transplant',
	);
	if ( ! isset( $aliases[ $key ] ) ) {
		return;
	}

	// Read the language off the URL, not from WPML. WPML does not recognise the
	// /pt/ contract (its own directory is pt-pt) and answers with the default
	// language, which is precisely the English fallback this function exists to
	// prevent. Only ask WPML when the URL states no language.
	$language = estecapelli_request_language_code( $request );
	if ( '' === $language ) {
		$language = estecapelli_indexed_language_code();
	}
	$query  = (string) wp_parse_url( $request, PHP_URL_QUERY );
	$target = estecapelli_indexed_url( $aliases[ $key ], $language );
	if ( $query ) {
		$target .= '?' . $query;
	}

	wp_safe_redirect( $target, 301 );
	exit;
}
add_action( 'template_redirect', 'estecapelli_redirect_legacy_service_aliases', -3 );

/**
 * English source ID and indexed language for a translated object.
 */
function estecapelli_indexed_post_context( $post ) {
	$post = get_post( $post );
	if ( ! $post ) {
		return array( 0, 'en' );
	}

	$language = apply_filters(
		'wpml_element_language_code',
		null,
		array( 'element_id' => $post->ID, 'element_type' => 'post_' . $post->post_type )
	);
	$language = estecapelli_indexed_language_code( (string) $language );
	$source   = (int) apply_filters( 'wpml_object_id', $post->ID, $post->post_type, true, 'en' );

	return array( $source ?: (int) $post->ID, $language );
}

/**
 * Contract key for a source post.
 */
function estecapelli_indexed_post_route_key( $post ) {
	$post = get_post( $post );
	if ( ! $post ) {
		return '';
	}
	list( $source_id ) = estecapelli_indexed_post_context( $post );
	$slug = (string) get_post_field( 'post_name', $source_id );

	if ( 'treatment' === $post->post_type ) {
		$category = estecapelli_indexed_treatment_category( $slug );
		return $category ? '/en/' . $category . '/' . $slug : '';
	}
	if ( 'doctor' === $post->post_type ) {
		$parent = 'our-doctors';
		return '/en/about-us/' . $parent . '/' . $slug;
	}
	if ( 'post' === $post->post_type && isset( estecapelli_indexed_blog_slugs()[ $slug ] ) ) {
		return '/en/blog/' . $slug;
	}
	if ( 'page' === $post->post_type ) {
		$uri = (string) get_page_uri( $source_id );
		$key = '/en/' . trim( $uri, '/' );
		return isset( estecapelli_indexed_route_contract()[ $key ] ) ? $key : '';
	}

	return '';
}

/**
 * Force generated treatment/doctor permalinks to the full indexed hierarchy.
 */
function estecapelli_indexed_post_type_link( $url, $post ) {
	if ( ! $post || ! in_array( $post->post_type, array( 'treatment', 'doctor' ), true ) ) {
		return $url;
	}
	$key = estecapelli_indexed_post_route_key( $post );
	list( , $language ) = estecapelli_indexed_post_context( $post );
	return $key && estecapelli_indexed_route_path( $key, $language ) ? estecapelli_indexed_url( $key, $language ) : $url;
}
add_filter( 'post_type_link', 'estecapelli_indexed_post_type_link', 999, 2 );

/** Force generated blog-post links to their exact indexed translated slug. */
function estecapelli_indexed_post_link( $url, $post ) {
	$key = estecapelli_indexed_post_route_key( $post );
	list( , $language ) = estecapelli_indexed_post_context( $post );
	if ( $key && estecapelli_indexed_route_path( $key, $language ) ) {
		return estecapelli_indexed_url( $key, $language );
	}

	// New English articles may not yet exist in the historical live-URL
	// contract. They still belong under /en/blog/, never at a bare /blog/ URL.
	if ( 'en' === $language && $post instanceof WP_Post && 'post' === $post->post_type && $post->post_name ) {
		return estecapelli_unfiltered_home_url() . '/en/blog/' . $post->post_name;
	}

	return estecapelli_localize_theme_url( $url );
}
add_filter( 'post_link', 'estecapelli_indexed_post_link', 999, 2 );

/** Force generated page links to their exact indexed translated hierarchy. */
function estecapelli_indexed_page_link( $url, $post_id ) {
	$post = get_post( $post_id );
	$key  = estecapelli_indexed_post_route_key( $post );
	list( $source_id, $language ) = estecapelli_indexed_post_context( $post );
	$source_slug = $source_id ? (string) get_post_field( 'post_name', $source_id ) : '';
	$legal_route = estecapelli_legal_page_route( $source_slug, $language );
	if ( $legal_route ) {
		return estecapelli_unfiltered_home_url() . $legal_route;
	}
	return $key && estecapelli_indexed_route_path( $key, $language ) ? estecapelli_indexed_url( $key, $language ) : estecapelli_localize_theme_url( $url );
}
add_filter( 'page_link', 'estecapelli_indexed_page_link', 999, 2 );

/** Treatment-category links are the indexed section landing pages. */
function estecapelli_indexed_term_link( $url, $term, $taxonomy ) {
	if ( 'treatment_category' !== $taxonomy || ! $term instanceof WP_Term ) {
		return $url;
	}
	$source_id = (int) apply_filters( 'wpml_object_id', $term->term_id, $taxonomy, true, 'en' );
	$source    = $source_id ? get_term( $source_id, $taxonomy ) : $term;
	$slug      = $source && ! is_wp_error( $source ) ? $source->slug : '';
	if ( isset( estecapelli_indexed_category_slugs()[ $slug ] ) ) {
		return estecapelli_indexed_url( '/en/' . $slug );
	}
	return $url;
}
add_filter( 'term_link', 'estecapelli_indexed_term_link', 999, 3 );

/** Localize URLs emitted by WordPress menus and ACF link/url fields. */
function estecapelli_indexed_menu_attributes( $atts ) {
	if ( ! empty( $atts['href'] ) ) {
		$atts['href'] = estecapelli_localize_theme_url( $atts['href'] );
	}
	return $atts;
}
add_filter( 'nav_menu_link_attributes', 'estecapelli_indexed_menu_attributes', 999 );

function estecapelli_indexed_acf_url( $value ) {
	return is_admin() ? $value : estecapelli_localize_theme_url( $value );
}
add_filter( 'acf/format_value/type=url', 'estecapelli_indexed_acf_url', 999 );

function estecapelli_indexed_acf_link( $value ) {
	if ( ! is_admin() && is_array( $value ) && ! empty( $value['url'] ) ) {
		$value['url'] = estecapelli_localize_theme_url( $value['url'] );
	}
	return $value;
}
add_filter( 'acf/format_value/type=link', 'estecapelli_indexed_acf_link', 999 );

/**
 * Resolve a translated post from WPML's relationship table.
 *
 * Fresh imports can leave WPML's persistent object-id cache holding the old
 * "missing translation" result. The translation table is the source of truth,
 * so indexed requests use it as a fallback before treating a live URL as a
 * 404. The localized slug fallback also covers legacy records whose English
 * Page hierarchy does not match the public route hierarchy.
 */
function estecapelli_indexed_raw_translation_id( $source_id, $post_type, $language, $localized_slug = '' ) {
	if ( ! defined( 'ICL_SITEPRESS_VERSION' ) && ! defined( 'WPML_VERSION' ) ) {
		return 0;
	}

	global $wpdb;
	$table        = $wpdb->prefix . 'icl_translations';
	$element_type = (string) apply_filters( 'wpml_element_type', $post_type );
	$target_id    = 0;

	if ( $source_id ) {
		$target_id = (int) $wpdb->get_var(
			$wpdb->prepare(
				"SELECT target.element_id
				 FROM {$table} source
				 INNER JOIN {$table} target
				 ON target.trid = source.trid
				 AND target.element_type = source.element_type
				 INNER JOIN {$wpdb->posts} post ON post.ID = target.element_id
				 WHERE source.element_id = %d
				 AND source.element_type = %s
				 AND target.language_code = %s
				 AND post.post_type = %s
				 AND post.post_status = 'publish'
				 ORDER BY target.translation_id ASC LIMIT 1",
				(int) $source_id,
				$element_type,
				(string) $language,
				(string) $post_type
			)
		);
	}

	if ( ! $target_id && $localized_slug ) {
		$target_id = (int) $wpdb->get_var(
			$wpdb->prepare(
				"SELECT post.ID
				 FROM {$wpdb->posts} post
				 INNER JOIN {$table} translation
				 ON translation.element_id = post.ID
				 AND translation.element_type = %s
				 WHERE post.post_name = %s
				 AND post.post_type = %s
				 AND post.post_status = 'publish'
				 AND translation.language_code = %s
				 ORDER BY translation.translation_id ASC LIMIT 1",
				$element_type,
				(string) $localized_slug,
				(string) $post_type,
				(string) $language
			)
		);
	}

	return $target_id;
}

/**
 * Resolve every exact indexed treatment, doctor, page and blog route directly.
 * This removes any dependency on translated CPT rewrite bases or term order.
 */
function estecapelli_indexed_request( $query_vars ) {
	$request = isset( $_SERVER['REQUEST_URI'] ) ? wp_unslash( $_SERVER['REQUEST_URI'] ) : '';
	$key     = estecapelli_indexed_route_key( $request );
	if ( ! $key ) {
		return $query_vars;
	}

	$path     = trim( (string) wp_parse_url( $request, PHP_URL_PATH ), '/' );
	$language = estecapelli_indexed_language_code( (string) strtok( $path, '/' ) );

	// The /en/ resolver has already validated the nonce and converted a preview
	// URL to an explicit post ID. Do not replace that query with the public,
	// published-only route below or WordPress will lose the draft revision.
	if ( ! empty( $query_vars['preview'] ) && ( ! empty( $query_vars['p'] ) || ! empty( $query_vars['page_id'] ) ) ) {
		return $query_vars;
	}

	/*
	 * Language roots have no Page record or leaf slug. Without this explicit
	 * branch WordPress keeps the rewrite query for a page named "pt", decides
	 * that /pt/ is missing and redirects it to the English root. Clear those
	 * query vars and switch WPML to its real code (pt-pt on this installation)
	 * so front-page.php renders directly at the indexed language root.
	 */
	if ( '/en' === $key ) {
		$wpml_language = estecapelli_wpml_language_code( $language );
		if ( $wpml_language !== $language ) {
			do_action( 'wpml_switch_language', $wpml_language );
		}
		$resolved = array();
		if ( isset( $query_vars['lang'] ) ) {
			$resolved['lang'] = $wpml_language;
		}

		/*
		 * Query this language's Home page when one exists. The layout still
		 * comes from front-page.php — the page is what gives Rank Math a real
		 * post to read its per-language title and meta description from, and
		 * what carries the language's own Homepage Content overrides.
		 *
		 * Safe against a canonical bounce to the page's own leaf: every
		 * language root is in the indexed route contract, so
		 * estecapelli_preserve_indexed_canonical() already vetoes
		 * redirect_canonical here.
		 */
		$home_id = function_exists( 'estecapelli_home_page_id' ) ? estecapelli_home_page_id( $language ) : 0;
		if ( $home_id ) {
			$resolved['page_id'] = $home_id;
		}

		return $resolved;
	}

	$type           = 'page';
	$slug           = basename( $key );
	$route          = estecapelli_indexed_route_path( $key, $language );
	$localized_slug = $route ? basename( $route ) : '';

	$is_hair_care_page = in_array( $slug, estecapelli_indexed_hair_care_page_slugs(), true );
	if ( ! $is_hair_care_page && isset( estecapelli_indexed_treatment_slugs()[ $slug ] ) ) {
		$type = 'treatment';
	} elseif ( 0 === strpos( $key, '/en/blog/' ) ) {
		$type = 'post';
	} elseif ( preg_match( '#^/en/about-us/(?:our-doctors|medical-director)/([^/]+)$#', $key, $match ) ) {
		$type = 'doctor';
		$slug = $match[1];
	} elseif ( 0 === strpos( $key, '/en/before-after/' ) ) {
		return $query_vars; // Thin result URLs are handled by the 301 fallback.
	}

	global $wpdb;
	if ( 'page' === $type ) {
		$source_path = trim( substr( $key, 3 ), '/' );
		$source      = get_page_by_path( $source_path, OBJECT, 'page' );
		if ( ! $source ) {
			// Older imports may contain the right Page without its expected
			// English parent. Resolve the leaf directly and let WPML map it back
			// to the English source instead of falling through to the landing.
			$source_id = (int) $wpdb->get_var(
				$wpdb->prepare(
					"SELECT ID FROM {$wpdb->posts} WHERE post_name = %s AND post_type = 'page' AND post_status = 'publish' ORDER BY ID ASC LIMIT 1",
					$slug
				)
			);
			$source = $source_id ? get_post( $source_id ) : null;
		}
		$source_id   = $source ? (int) apply_filters( 'wpml_object_id', $source->ID, 'page', true, 'en' ) : 0;
	} else {
		$source_id = (int) $wpdb->get_var(
			$wpdb->prepare(
				"SELECT ID FROM {$wpdb->posts} WHERE post_name = %s AND post_type = %s AND post_status = 'publish' ORDER BY ID ASC LIMIT 1",
				$slug,
				$type
			)
		);
	}
	$wpml_language = estecapelli_wpml_language_code( $language );
	if ( $wpml_language !== $language ) {
		do_action( 'wpml_switch_language', $wpml_language );
	}

	/*
	 * A translated post can outlive its English WPML source (for example when
	 * the source is accidentally trashed). The exact indexed slug and language
	 * still identify that published translation, so do not turn every localized
	 * URL in the group into a 404 merely because the source row is unavailable.
	 */
	if ( ! $source_id ) {
		$target_id = 'en' === $language
			? 0
			: estecapelli_indexed_raw_translation_id( 0, $type, $wpml_language, $localized_slug );
	} else {
		$target_id = 'en' === $language
			? $source_id
			: (int) apply_filters( 'wpml_object_id', $source_id, $type, false, $wpml_language );
	}
	if ( $source_id && 'en' !== $language ) {
		$raw_target_id = estecapelli_indexed_raw_translation_id( $source_id, $type, $wpml_language, $localized_slug );
		if ( $raw_target_id ) {
			$target_id = $raw_target_id;
		}
	}
	if ( ! $target_id ) {
		return $query_vars;
	}

	$resolved = 'page' === $type
		? array( 'page_id' => $target_id )
		: array( 'p' => $target_id, 'post_type' => $type );
	if ( isset( $query_vars['lang'] ) ) {
		$resolved['lang'] = $wpml_language;
	}
	return $resolved;
}
add_filter( 'request', 'estecapelli_indexed_request', 30 );

/**
 * Indexed language of a /{lang}/blog/{slug} path that the contract does not own.
 *
 * The contract froze the articles that were live at migration time. Articles
 * published since then have no contract route, and this installation does not
 * resolve /{lang}/blog/{slug} from its rewrite rules alone — the same reason the
 * English side carries its own resolver in inc/local-en-routing.php. Returns ''
 * for anything the resolver below must not touch: /en/ (handled there), an
 * unknown language prefix, and every route the contract already resolves.
 */
function estecapelli_localized_blog_language( $path ) {
	$path = untrailingslashit( '/' . trim( (string) wp_parse_url( (string) $path, PHP_URL_PATH ), '/' ) );
	if ( ! preg_match( '#^/[a-z-]{2,5}/blog/[^/]+$#i', $path ) ) {
		return '';
	}

	$language = estecapelli_request_language_code( $path );
	if ( '' === $language || 'en' === $language || estecapelli_indexed_route_key( $path ) ) {
		return '';
	}
	return $language;
}

/**
 * Published translated post ID for such a path, or 0.
 */
function estecapelli_localized_blog_post_id( $path ) {
	$language = estecapelli_localized_blog_language( $path );
	if ( '' === $language || ! preg_match( '#/blog/([^/]+)/?$#', (string) wp_parse_url( (string) $path, PHP_URL_PATH ), $match ) ) {
		return 0;
	}

	return estecapelli_indexed_raw_translation_id(
		0,
		'post',
		estecapelli_wpml_language_code( $language ),
		sanitize_title( rawurldecode( $match[1] ) )
	);
}

/**
 * Resolve a translated blog post that is newer than the indexed contract.
 *
 * Runs after the contract resolver and only for the routes it declined, so a new
 * article in any translated language is reachable the moment it is published —
 * no contract entry, no re-deploy. A valid preview link resolves to its own post
 * ID first so a draft translation can be previewed at its real URL.
 */
function estecapelli_localized_blog_request( $query_vars ) {
	$request  = isset( $_SERVER['REQUEST_URI'] ) ? wp_unslash( $_SERVER['REQUEST_URI'] ) : '';
	$language = estecapelli_localized_blog_language( $request );
	if ( '' === $language ) {
		return $query_vars;
	}

	$is_preview   = false;
	$preview_id   = estecapelli_en_preview_post_id();
	$preview_post = $preview_id ? get_post( $preview_id ) : null;
	if ( $preview_post && 'post' === $preview_post->post_type ) {
		$target_id  = (int) $preview_id;
		$is_preview = true;
	} else {
		$target_id = estecapelli_localized_blog_post_id( $request );
	}
	if ( ! $target_id ) {
		return $query_vars;
	}

	$wpml_language = estecapelli_wpml_language_code( $language );
	do_action( 'wpml_switch_language', $wpml_language );

	$resolved = array( 'p' => $target_id, 'post_type' => 'post' );
	if ( $is_preview ) {
		$resolved['preview'] = 'true';
	}
	if ( isset( $query_vars['lang'] ) ) {
		$resolved['lang'] = $wpml_language;
	}
	return $resolved;
}
add_filter( 'request', 'estecapelli_localized_blog_request', 35 );

/** Keep WordPress on a translated blog URL resolved by the fallback above. */
function estecapelli_preserve_localized_blog_canonical( $redirect_url, $requested_url ) {
	return estecapelli_localized_blog_post_id( $requested_url ) ? false : $redirect_url;
}
add_filter( 'redirect_canonical', 'estecapelli_preserve_localized_blog_canonical', 998, 2 );

/**
 * Exact source/language context for a legal-page request, or an empty array.
 *
 * Legal pages are newer than the historical indexed contract. They still need
 * deterministic routing because this site's verbose page rewrite rules can be
 * stale immediately after an importer creates or renames a translated page.
 */
function estecapelli_legal_request_context( $request = '' ) {
	static $routes = null;
	if ( null === $routes ) {
		$routes = array();
		foreach ( array_keys( estecapelli_legal_pages_manifest()['tr'] ) as $source_slug ) {
			$routes[ estecapelli_legal_page_route( $source_slug, 'en' ) ] = array( $source_slug, 'en' );
		}
		foreach ( estecapelli_legal_pages_manifest() as $language => $pages ) {
			foreach ( $pages as $source_slug => $translation ) {
				$routes[ '/' . $language . '/' . $translation['slug'] ] = array( $source_slug, $language );
			}
		}
	}

	if ( '' === (string) $request ) {
		$request = isset( $_SERVER['REQUEST_URI'] ) ? wp_unslash( $_SERVER['REQUEST_URI'] ) : '';
	}
	$path = '/' . trim( (string) wp_parse_url( $request, PHP_URL_PATH ), '/' );
	$path = untrailingslashit( $path ) ?: '/';
	return $routes[ $path ] ?? array();
}

/** Resolve a localized legal slug directly to its published WPML page. */
function estecapelli_legal_page_request( $query_vars ) {
	$context = estecapelli_legal_request_context();
	if ( ! $context ) {
		return $query_vars;
	}

	list( $source_slug, $public_language ) = $context;
	$source_id = function_exists( 'estecapelli_source_post_id' )
		? estecapelli_source_post_id( $source_slug, 'page' )
		: 0;
	if ( ! $source_id ) {
		return $query_vars;
	}

	$wpml_language = estecapelli_wpml_language_code( $public_language );
	$target_id     = 'en' === $public_language
		? (int) $source_id
		: (int) apply_filters( 'wpml_object_id', $source_id, 'page', false, $wpml_language );

	// WPML's object cache may still hold "missing" after an import. Confirm the
	// exact target from the translation table before allowing a false 404.
	if ( 'en' !== $public_language ) {
		global $wpdb;
		$element_type = apply_filters( 'wpml_element_type', 'page' );
		$target_slug  = estecapelli_legal_pages_manifest()[ $public_language ][ $source_slug ]['slug'];
		$raw_target   = (int) $wpdb->get_var(
			$wpdb->prepare(
				"SELECT target.element_id
				 FROM {$wpdb->prefix}icl_translations target
				 INNER JOIN {$wpdb->prefix}icl_translations source
				 ON source.trid = target.trid AND source.element_type = target.element_type
				 INNER JOIN {$wpdb->posts} post ON post.ID = target.element_id
				 WHERE source.element_id = %d AND source.element_type = %s
				 AND target.language_code = %s AND post.post_type = 'page'
				 AND post.post_status = 'publish' AND post.post_name = %s
				 ORDER BY target.translation_id ASC LIMIT 1",
				(int) $source_id,
				$element_type,
				$wpml_language,
				$target_slug
			)
		);
		if ( $raw_target ) {
			$target_id = $raw_target;
		}
	}
	if ( ! $target_id ) {
		return $query_vars;
	}

	if ( 'en' !== $public_language ) {
		do_action( 'wpml_switch_language', $wpml_language );
	}
	$resolved = array( 'page_id' => (int) $target_id );
	if ( 'en' !== $public_language || isset( $query_vars['lang'] ) ) {
		$resolved['lang'] = $wpml_language;
	}
	return $resolved;
}
add_filter( 'request', 'estecapelli_legal_page_request', 40 );

/** Keep WordPress on the exact legal route resolved above. */
function estecapelli_preserve_legal_canonical( $redirect_url ) {
	return estecapelli_legal_request_context() ? false : $redirect_url;
}
add_filter( 'redirect_canonical', 'estecapelli_preserve_legal_canonical', 1000 );

/** Keep WordPress from canonicalizing an indexed route to a guessed route. */
function estecapelli_preserve_indexed_canonical( $redirect_url, $requested_url ) {
	return estecapelli_indexed_route_key( $requested_url ) ? false : $redirect_url;
}
add_filter( 'redirect_canonical', 'estecapelli_preserve_indexed_canonical', 999, 2 );

/** Redirect the non-indexed WPML Portuguese alias to the public /pt/ contract. */
function estecapelli_redirect_pt_pt_alias() {
	if ( is_admin() ) {
		return;
	}
	$request = isset( $_SERVER['REQUEST_URI'] ) ? wp_unslash( $_SERVER['REQUEST_URI'] ) : '';
	$path    = (string) wp_parse_url( $request, PHP_URL_PATH );
	if ( ! preg_match( '#^/pt-pt(?:/|$)#i', $path ) ) {
		return;
	}

	$languages      = apply_filters( 'wpml_active_languages', null, array( 'skip_missing' => 0 ) );
	$has_portuguese = false;
	if ( is_array( $languages ) ) {
		foreach ( $languages as $language ) {
			if ( in_array( strtolower( (string) ( $language['language_code'] ?? '' ) ), array( 'pt', 'pt-pt' ), true ) ) {
				$has_portuguese = true;
				break;
			}
		}
	}
	if ( ! $has_portuguese ) {
		return;
	}

	$target_path = preg_replace( '#^/pt-pt(?=/|$)#i', '/pt', $path );
	$query       = (string) wp_parse_url( $request, PHP_URL_QUERY );
	$target      = estecapelli_unfiltered_home_url() . $target_path . ( $query ? '?' . $query : '' );
	wp_safe_redirect( $target, 301 );
	exit;
}
add_action( 'template_redirect', 'estecapelli_redirect_pt_pt_alias', -1 );

/**
 * Never leave an old indexed URL as a 404 while its translated object is absent.
 */
function estecapelli_indexed_404_fallback() {
	if ( is_admin() || ! is_404() ) {
		return;
	}
	$request = isset( $_SERVER['REQUEST_URI'] ) ? wp_unslash( $_SERVER['REQUEST_URI'] ) : '';
	$key     = estecapelli_indexed_route_key( $request );
	// The URL wins here: a visitor who asked for /pt/ must land on /pt/, and on
	// a 404 WPML would answer 'en' for it. Only fall back when the URL is silent.
	$language = estecapelli_request_language_code( $request );
	if ( '' === $language ) {
		$language = estecapelli_indexed_language_code();
	}
	if ( ! $key ) {
		return;
	}

	$fallback = '/en';
	$slug     = basename( $key );
	if ( 0 === strpos( $key, '/en/before-after/' ) ) {
		$fallback = '/en/before-after';
	} elseif ( isset( estecapelli_indexed_treatment_slugs()[ $slug ] ) ) {
		$category = estecapelli_indexed_treatment_category( $slug );
		$fallback = '/en/' . $category;
		if ( 'hair-transplant-techniques-comparison2' === $slug ) {
			$fallback = '/en/hair-transplant/dhi-hair-transplant';
		}
	} elseif ( 0 === strpos( $key, '/en/blog/' ) ) {
		$fallback = '/en/blog';
	} elseif ( false !== strpos( $key, '/our-doctors/' ) ) {
		$fallback = '/en/about-us/our-doctors';
	} elseif ( false !== strpos( $key, '/medical-director/' ) ) {
		// The page is retired, so anything still under it lands on the doctors
		// roster rather than on a route that no longer resolves.
		$fallback = '/en/about-us/our-doctors';
	}

	$target = estecapelli_indexed_url( $fallback, $language );
	$current_path = untrailingslashit( (string) wp_parse_url( $request, PHP_URL_PATH ) );
	$target_path  = untrailingslashit( (string) wp_parse_url( $target, PHP_URL_PATH ) );
	if ( $target && $current_path !== $target_path ) {
		wp_safe_redirect( $target, 301 );
		exit;
	}
}
add_action( 'template_redirect', 'estecapelli_indexed_404_fallback', 0 );
