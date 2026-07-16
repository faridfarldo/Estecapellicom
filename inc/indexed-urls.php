<?php
/**
 * Exact URL contract captured from the live, Google-indexed sitemap.
 *
 * Nothing in the theme should translate a slug or assemble a multilingual URL
 * heuristically. English source paths are keys; the values below are the exact
 * indexed routes for all seven languages.
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
	return array( 'en', 'tr', 'fr', 'it', 'es', 'pl', 'pt' );
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
		'home'             => array( 'en' => '', 'tr' => '', 'fr' => '', 'it' => '', 'es' => '', 'pl' => '', 'pt' => '' ),
		'hair-transplant'  => array( 'en' => 'hair-transplant', 'tr' => 'sac-ekimi', 'fr' => 'greffe-de-cheveux', 'it' => 'trapianto-di-capelli', 'es' => 'trasplante-capilar', 'pl' => 'przeszczep-wlosow', 'pt' => 'transplante-capilar' ),
		'plastic-surgery'  => array( 'en' => 'plastic-surgery', 'tr' => 'estetik-cerrahi', 'fr' => 'chirurgie-plastique', 'it' => 'chirurgia-plastica', 'es' => 'cirugia-plastica', 'pl' => 'chirurgia-plastyczna', 'pt' => 'cirurgia-plastica' ),
		'dental-treatment' => array( 'en' => 'dental-treatment', 'tr' => 'dis-tedavisi', 'fr' => 'traitement-dentaire', 'it' => 'trattamento-dentale', 'es' => 'tratamiento-dental', 'pl' => 'leczenie-stomatologiczne', 'pt' => 'tratamento-dentario' ),
		'before-after'     => array( 'en' => 'before-after', 'tr' => 'oncesi-sonrasi', 'fr' => 'avant-apres', 'it' => 'prima-dopo', 'es' => 'antes-despues', 'pl' => 'przed-po', 'pt' => 'antes-depois' ),
		'about-us'         => array( 'en' => 'about-us', 'tr' => 'hakkimizda', 'fr' => 'a-propos-de-nous', 'it' => 'chi-siamo', 'es' => 'sobre-nosotros', 'pl' => 'o-nas', 'pt' => 'sobre-nos' ),
		'blog'             => array( 'en' => 'blog', 'tr' => 'blog', 'fr' => 'blog', 'it' => 'blog', 'es' => 'blog', 'pl' => 'blog', 'pt' => 'blog' ),
		'contact'          => array( 'en' => 'contact', 'tr' => 'iletisim', 'fr' => 'contact', 'it' => 'contatto', 'es' => 'contacto', 'pl' => 'kontakt', 'pt' => 'contato' ),
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
	);
}

/**
 * Exact about-us child slugs.
 */
function estecapelli_indexed_about_slugs() {
	return array(
		'our-doctors'     => array( 'en' => 'our-doctors', 'tr' => 'doktorlarimiz', 'fr' => 'nos-medecins', 'it' => 'i-nostri-medici', 'es' => 'nuestros-doctores', 'pl' => 'nasi-lekarze', 'pt' => 'nossos-medicos' ),
		'medical-director' => array( 'en' => 'medical-director', 'tr' => 'tibbi-direktor', 'fr' => 'directeur-medical', 'it' => 'direttore-medico', 'es' => 'director-medico', 'pl' => 'dyrektor-medyczny', 'pt' => 'diretor-medico' ),
		'our-team'         => array( 'en' => 'our-team', 'tr' => 'ekibimiz', 'fr' => 'notre-equipe', 'it' => 'il-nostro-team', 'es' => 'nuestro-equipo', 'pl' => 'nasz-zespol', 'pt' => 'nossa-equipe' ),
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
		'hair-transplant-overview'                                => array( 'en' => 'hair-transplant-overview', 'tr' => 'sac-ekimi-genel-bakis', 'fr' => 'apercu-de-la-greffe-de-cheveux', 'it' => 'panoramica-sul-trapianto-di-capelli', 'es' => 'descripcion-general-del-trasplante-capilar', 'pl' => 'przeglad-przeszczepu-wlosow', 'pt' => 'visao-geral-do-transplante-capilar' ),
		'sapphire-fue-hair-transplant'                            => array( 'en' => 'sapphire-fue-hair-transplant', 'tr' => 'safir-fue-sac-ekimi', 'fr' => 'greffe-de-cheveux-fue-sapphire', 'it' => 'trapianto-di-capelli-fue-sapphire', 'es' => 'trasplante-capilar-fue-sapphire', 'pl' => 'przeszczep-wlosow-metoda-sapphire-fue', 'pt' => 'transplante-capilar-fue-sapphire' ),
		'dhi-hair-transplant'                                     => array( 'en' => 'dhi-hair-transplant', 'tr' => 'dhi-sac-ekimi', 'fr' => 'greffe-de-cheveux-dhi', 'it' => 'trapianto-di-capelli-dhi', 'es' => 'trasplante-de-cabello-dhi', 'pl' => 'przeszczep-wlosow-dhi', 'pt' => 'transplante-capilar-dhi' ),
		'exosome-fue-hair-transplant'                             => array( 'en' => 'exosome-fue-hair-transplant', 'tr' => 'exosome-fue-sac-ekimi', 'fr' => 'greffe-capillaire-exosome-fue', 'it' => 'trapianto-di-capelli-exosome-fue', 'es' => 'trasplante-capilar-exosome-fue', 'pl' => 'przeszczep-wlosow-exosome-fue', 'pt' => 'transplante-capilar-exosome-fue' ),
		'vita-treatment'                                          => array( 'en' => 'vita-treatment', 'tr' => 'vita-tedavisi', 'fr' => 'traitement-vita', 'it' => 'trattamento-vita', 'es' => 'tratamiento-vita', 'pl' => 'leczenie-vita', 'pt' => 'tratamento-vita' ),
		'tricholab'                                               => array( 'en' => 'tricholab', 'tr' => 'tricholab', 'fr' => 'tricholab', 'it' => 'tricholab', 'es' => 'tricholab', 'pl' => 'tricholab', 'pt' => 'tricholab' ),
		'female-hair-transplant'                                  => array( 'en' => 'female-hair-transplant', 'tr' => 'kadin-sac-ekimi', 'fr' => 'greffe-de-cheveux-feminine', 'it' => 'trapianto-di-capelli-femminile', 'es' => 'trasplante-capilar-femenino', 'pl' => 'przeszczep-wlosow-u-kobiet', 'pt' => 'transplante-capilar-feminino' ),
		'eyebrow-transplant'                                      => array( 'en' => 'eyebrow-transplant', 'tr' => 'kas-ekimi', 'fr' => 'transplantation-de-sourcils', 'it' => 'trapianto-di-sopracciglia', 'es' => 'trasplante-de-cejas', 'pl' => 'przeszczep-brwi', 'pt' => 'transplante-de-sobrancelhas' ),
		'beard-transplant'                                        => array( 'en' => 'beard-transplant', 'tr' => 'sakal-ekimi', 'fr' => 'transplantation-de-barbe', 'it' => 'beard-trapianto-di-barba', 'es' => 'trasplante-de-barba', 'pl' => 'przeszczep-brody', 'pt' => 'transplante-de-barba' ),
		'hair-mesotherapy'                                        => array( 'en' => 'hair-mesotherapy', 'tr' => 'sac-mezoterapisi', 'fr' => 'mesotherapie-capillaire', 'it' => 'mesoterapia-per-capelli', 'es' => 'mesoterapia-capilar', 'pl' => 'mezoterapia-wlosow', 'pt' => 'mesoterapia-capilar' ),
		'pre-hair-transplant-period'                              => array( 'en' => 'pre-hair-transplant-period', 'tr' => 'sac-ekim-oncesi-donem', 'fr' => 'periode-pre-transplantation-capillaire', 'it' => 'periodo-pre-trapianto-di-capelli', 'es' => 'periodo-previo-al-trasplante-capilar', 'pl' => 'okres-przed-przeszczepem-wlosow', 'pt' => 'periodo-pre-transplante-capilar' ),
		'post-hair-transplant-period'                             => array( 'en' => 'post-hair-transplant-period', 'tr' => 'sac-ekimi-sonrasi-donem', 'fr' => 'periode-post-greffe-de-cheveux', 'it' => 'periodo-post-trapianto-di-capelli', 'es' => 'periodo-posterior-al-trasplante-capilar', 'pl' => 'okres-po-przeszczepie-wlosow', 'pt' => 'periodo-pos-transplante-capilar' ),
		'hair-transplant-techniques-comparison2'                  => array( 'en' => 'hair-transplant-techniques-comparison2', 'tr' => 'sac-ekimi-teknikleri-karsilastirmasi-2', 'fr' => 'comparaison-des-techniques-de-greffe-de-cheveux-2', 'it' => 'confronto-tra-tecniche-di-trapianto-capillare-2', 'es' => 'comparacion-de-tecnicas-de-trasplante-capilar-2', 'pl' => 'porownanie-technik-przeszczepu-wlosow-2', 'pt' => 'comparacao-das-tecnicas-de-transplante-capilar-2' ),

		// Plastic surgery.
		'plastic-surgery-overview'                               => array( 'en' => 'plastic-surgery-overview', 'tr' => 'plastic-surgery-overview', 'fr' => 'plastic-surgery-overview', 'it' => 'plastic-surgery-overview', 'es' => 'plastic-surgery-overview', 'pl' => 'plastic-surgery-overview', 'pt' => 'plastic-surgery-overview' ),
		'rhinoplasty'                                             => array( 'en' => 'rhinoplasty', 'tr' => 'burun-estetigi', 'fr' => 'rhinoplastie', 'it' => 'rinoplastica', 'es' => 'rinoplastia', 'pl' => 'rynoplastyka', 'pt' => 'rinoplastia' ),
		'breast-aesthetics-breast-surgery'                        => array( 'en' => 'breast-aesthetics-breast-surgery', 'tr' => 'meme-estetigi-gogus-estetigi', 'fr' => 'esthetique-mammaire-chirurgie-mammaire', 'it' => 'estetica-del-seno-chirurgia-del-seno', 'es' => 'estetica-mamaria-cirugia-de-pecho', 'pl' => 'estetyka-piersi-chirurgia-piersi', 'pt' => 'estetica-mamaria-cirurgia-de-mama' ),
		'bbl'                                                      => array( 'en' => 'bbl', 'tr' => 'bbl', 'fr' => 'bbl', 'it' => 'bbl', 'es' => 'bbl', 'pl' => 'bbl', 'pt' => 'bbl' ),
		'liposuction'                                              => array( 'en' => 'liposuction', 'tr' => 'liposuction', 'fr' => 'liposuccion', 'it' => 'liposuzione', 'es' => 'liposuccion', 'pl' => 'liposukcja', 'pt' => 'lipoaspiracao' ),
		'face-and-neck-lift-surgery'                              => array( 'en' => 'face-and-neck-lift-surgery', 'tr' => 'yuz-ve-boyun-germe-ameliyati', 'fr' => 'chirurgie-de-lifting-du-visage-et-du-cou', 'it' => 'chirurgia-di-lifting-del-viso-e-del-collo', 'es' => 'cirugia-de-lifting-facial-y-de-cuello', 'pl' => 'chirurgia-liftingu-twarzy-i-szyi', 'pt' => 'cirurgia-de-lifting-facial-e-de-pescoco' ),
		'abdominoplasty-tummy-tuck'                               => array( 'en' => 'abdominoplasty-tummy-tuck', 'tr' => 'karin-germe-ameliyati', 'fr' => 'abdominoplastie', 'it' => 'addominoplastica', 'es' => 'abdominoplastia', 'pl' => 'plastyka-brzucha', 'pt' => 'abdominoplastia' ),
		'gynecomastia'                                            => array( 'en' => 'gynecomastia', 'tr' => 'jinekomasti', 'fr' => 'gynecomastie', 'it' => 'ginecomastia', 'es' => 'ginecomastia', 'pl' => 'ginekomastia', 'pt' => 'ginecomastia' ),
		'obesity-surgeries-bariatric-surgery-and-gastric-balloon' => array( 'en' => 'obesity-surgeries-bariatric-surgery-and-gastric-balloon', 'tr' => 'obezite-ameliyatlari-bariatrik-cerrahi-ve-mide-balonu', 'fr' => 'chirurgies-de-l-obesite-chirurgie-bariatrique-et-ballon-gastrique', 'it' => 'chirurgie-dell-obesita-chirurgia-bariatrica-e-palloncino-gastrico', 'es' => 'cirugias-de-obesidad-cirugia-bariatrica-y-balon-gastrico', 'pl' => 'operacje-otylosci-chirurgia-bariatryczna-i-balon-zoladkowy', 'pt' => 'cirurgias-de-obesidade-cirurgia-bariatrica-e-balao-gastrico' ),

		// Dental treatment.
		'dental-treatment-overview'                               => array( 'en' => 'dental-treatment-overview', 'tr' => 'dis-tedavisi-genel-bakis', 'fr' => 'apercu-du-traitement-dentaire', 'it' => 'panoramica-del-trattamento-dentale', 'es' => 'resumen-del-tratamiento-dental', 'pl' => 'przeglad-leczenia-stomatologicznego', 'pt' => 'visao-geral-do-tratamento-dentario' ),
		'dental-implant'                                          => array( 'en' => 'dental-implant', 'tr' => 'dis-implanti', 'fr' => 'implant-dentaire', 'it' => 'impianto-dentale', 'es' => 'implante-dental', 'pl' => 'implant-zebowy', 'pt' => 'implante-dentario' ),
		'hollywood-smile'                                         => array( 'en' => 'hollywood-smile', 'tr' => 'hollywood-gulusu', 'fr' => 'sourire-hollywoodien', 'it' => 'sorriso-hollywoodiano', 'es' => 'sonrisa-hollywoodense', 'pl' => 'hollywoodzki-usmiech', 'pt' => 'sorriso-hollywoodiano' ),
	);
}

/**
 * Exact blog post slugs. Empty values mean that language has no indexed copy.
 */
function estecapelli_indexed_blog_slugs() {
	return array(
		'hair-transplant-turkey-complete-expert-guide'              => array( 'en' => 'hair-transplant-turkey-complete-expert-guide', 'it' => 'trapianto-capelli-in-turchia-la-guida-completa' ),
		'hair-transplant-for-hiv-positive-patients-in-turkey'       => array( 'en' => 'hair-transplant-for-hiv-positive-patients-in-turkey', 'tr' => 'hiv-pozitif-hastalarina-turkiye-de-sac-ekimi', 'fr' => 'greffe-de-cheveux-pour-patients-seropositifs-en-turquie', 'it' => 'trapianto-di-capelli-per-pazienti-hiv-positivi-in-turchia', 'es' => 'trasplante-capilar-para-pacientes-vih-positivos-en-turquia', 'pl' => 'trasplante-capilar-para-pacientes-vih-positivos-en-turquia', 'pt' => 'transplante-capilar-para-pacientes-hiv-positivos-na-turquia' ),
		'unshaven-hair-transplant'                                  => array( 'en' => 'unshaven-hair-transplant', 'tr' => 'tirassiz-sac-ekimi', 'fr' => 'greffe-de-cheveux-sans-rasage', 'it' => 'greffe-de-cheveux-sans-rasage', 'es' => 'trasplante-de-cabello-sin-afeitar', 'pl' => 'przeszczep-wlosow-bez-golenia', 'pt' => 'transplante-capilar-sem-barbear' ),
		'can-diabetic-patients-undergo-a-hair-transplant'           => array( 'en' => 'can-diabetic-patients-undergo-a-hair-transplant', 'tr' => 'diyabet-hastalari-sac-ekimi-yaptirabilir-mi', 'fr' => 'les-patients-diabetiques-peuvent-ils-subir-une-greffe-de-cheveux', 'it' => 'i-pazienti-diabetici-possono-sottoporsi-a-un-trapianto-di-capelli', 'es' => 'i-pazienti-diabetici-possono-sottoporsi-a-un-trapianto-di-capelli', 'pl' => 'czy-pacjenci-z-cukrzyca-moga-poddac-sie-przeszczepowi-wlosow', 'pt' => 'pacientes-com-diabetes-podem-realizar-transplante-capilar' ),
		'is-hair-transplant-a-painful-procedure'                    => array( 'en' => 'is-hair-transplant-a-painful-procedure', 'tr' => 'sac-ekimi-agrili-bir-islem-midir', 'fr' => 'la-greffe-de-cheveux-est-elle-une-procedure-douloureuse', 'it' => 'il-trapianto-di-capelli-e-una-procedura-dolorosa', 'es' => '-es-doloroso-el-trasplante-capilar', 'pl' => 'czy-przeszczep-wlosow-jest-bolesnym-zabiegiem', 'pt' => 'o-transplante-capilar-e-um-procedimento-doloroso' ),
		'will-my-hair-fall-out-again-after-a-hair-transplant'       => array( 'en' => 'will-my-hair-fall-out-again-after-a-hair-transplant', 'tr' => 'sac-ekimi-sonrasinda-saclarim-tekrar-dokulur-mu', 'fr' => 'mes-cheveux-vont-ils-retomber-apres-une-greffe-de-cheveux', 'it' => 'i-miei-capelli-ricadranno-dopo-un-trapianto-di-capelli', 'es' => '-se-volvera-a-caer-mi-cabello-despues-de-un-trasplante-capilar', 'pl' => 'czy-moje-wlosy-wypadna-ponownie-po-przeszczepie-wlosow', 'pt' => 'meu-cabelo-vai-cair-novamente-apos-o-transplante-capilar' ),
		'hair-transplant-with-the-fue-vita-technique'               => array( 'en' => 'hair-transplant-with-the-fue-vita-technique', 'tr' => 'fue-vita-teknigi-ile-sac-ekimi', 'fr' => 'greffe-de-cheveux-avec-la-technique-fue-vita', 'it' => 'trapianto-di-capelli-con-la-tecnica-fue-vita', 'es' => 'trasplante-capilar-con-la-tecnica-fue-vita', 'pl' => 'przeszczep-wlosow-technika-fue-vita', 'pt' => 'transplante-capilar-com-a-tecnica-fue-vita' ),
		'hair-transplant-for-hiv-positive-patients'                 => array( 'en' => 'hair-transplant-for-hiv-positive-patients', 'tr' => 'hiv-pozitif-hastalara-sac-ekimi', 'fr' => 'greffe-de-cheveux-pour-les-patients-seropositifs-vih', 'it' => 'trapianto-di-capelli-per-pazienti-sieropositivi-hiv', 'es' => 'trasplante-capilar-para-pacientes-vih-positivos', 'pl' => 'przeszczep-wlosow-u-pacjentow-hiv-pozytywnych', 'pt' => 'transplante-capilar-para-pacientes-hiv-positivos' ),
		'unshaven-hair-transplant-for-women'                        => array( 'en' => 'unshaven-hair-transplant-for-women', 'tr' => 'kadinlarda-trassiz-sac-ekimi', 'fr' => 'greffe-de-cheveux-sans-rasage-chez-la-femme', 'it' => 'trapianto-di-capelli-senza-rasatura-nelle-donne', 'es' => 'trasplante-capilar-sin-rasurado-en-mujeres', 'pl' => 'przeszczep-wlosow-bez-golenia-u-kobiet', 'pt' => 'transplante-capilar-sem-raspagem-em-mulheres' ),
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

	$doctor_parents = array(
		'mehmet-hanifi-kutlar' => 'medical-director',
		'op-dr-hasan-celik'     => 'our-doctors',
		'op-dr-mehmet-palali'   => 'our-doctors',
		'op-dr-necdet-derici'   => 'our-doctors',
		'op-dr-ali-durmus'      => 'our-doctors',
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
		foreach ( estecapelli_legacy_home_slugs() as $language => $slug ) {
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

	if ( preg_match( '#^/(?:en|tr|fr|it|es|pl|pt|pt-pt)(?:/|$)#i', $path ) ) {
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

/**
 * Resolve a real translated page outside the legacy indexed set (legal pages).
 * It never guesses a translated slug; if no translation exists, English is the
 * safe published fallback.
 */
function estecapelli_translated_page_url( $english_path, $language = '' ) {
	$source = get_page_by_path( trim( (string) $english_path, '/' ), OBJECT, 'page' );
	if ( ! $source ) {
		return estecapelli_unfiltered_home_url() . '/en/' . trim( (string) $english_path, '/' );
	}
	$source_id = (int) apply_filters( 'wpml_object_id', $source->ID, 'page', true, 'en' );
	$source    = get_post( $source_id ?: $source->ID );

	$language      = estecapelli_indexed_language_code( $language );
	$wpml_language = estecapelli_wpml_language_code( $language );
	$target        = 'en' === $language
		? (int) $source->ID
		: (int) apply_filters( 'wpml_object_id', $source->ID, 'page', false, $wpml_language );
	return get_permalink( $target ?: $source->ID );
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
	foreach ( estecapelli_legacy_home_slugs() as $language => $slug ) {
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
		$parent = 'mehmet-hanifi-kutlar' === $slug ? 'medical-director' : 'our-doctors';
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
	return $key && estecapelli_indexed_route_path( $key, $language ) ? estecapelli_indexed_url( $key, $language ) : estecapelli_localize_theme_url( $url );
}
add_filter( 'post_link', 'estecapelli_indexed_post_link', 999, 2 );

/** Force generated page links to their exact indexed translated hierarchy. */
function estecapelli_indexed_page_link( $url, $post_id ) {
	$post = get_post( $post_id );
	$key  = estecapelli_indexed_post_route_key( $post );
	list( , $language ) = estecapelli_indexed_post_context( $post );
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
	$type     = 'page';
	$slug     = basename( $key );

	if ( '/en/hair-transplant/tricholab' !== $key && isset( estecapelli_indexed_treatment_slugs()[ $slug ] ) ) {
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
	if ( ! $source_id ) {
		return $query_vars;
	}

	$wpml_language = estecapelli_wpml_language_code( $language );
	if ( $wpml_language !== $language ) {
		do_action( 'wpml_switch_language', $wpml_language );
	}
	$target_id = 'en' === $language
		? $source_id
		: (int) apply_filters( 'wpml_object_id', $source_id, $type, false, $wpml_language );
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
	$request  = isset( $_SERVER['REQUEST_URI'] ) ? wp_unslash( $_SERVER['REQUEST_URI'] ) : '';
	$key      = estecapelli_indexed_route_key( $request );
	$language = estecapelli_indexed_language_code();
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
		$fallback = '/en/about-us/medical-director';
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
