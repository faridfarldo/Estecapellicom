<?php
/**
 * Footer-label fallbacks for the localized legal pages.
 *
 * WPML String Translation remains authoritative. These labels are used only
 * while WPML still returns the original English string.
 *
 * @package Estecapelli
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_filter( 'gettext', 'estecapelli_legal_gettext_fallback', 30, 3 );
/** Supply the four legal labels in every supported non-English language. */
function estecapelli_legal_gettext_fallback( $translation, $text, $domain ) {
	if ( 'estecapelli' !== $domain || $translation !== $text ) {
		return $translation;
	}

	$language = (string) apply_filters( 'wpml_current_language', null );
	if ( function_exists( 'estecapelli_indexed_language_code' ) ) {
		$language = estecapelli_indexed_language_code( $language );
	}
	$strings = array(
		'tr' => array(
			'Privacy Policy' => 'Gizlilik Politikası',
			'Terms'          => 'Kullanım Koşulları',
			'Cookie Policy'  => 'Çerez Politikası',
			'KVKK Notice'    => 'KVKK Aydınlatma Metni',
		),
		'fr' => array(
			'Privacy Policy' => 'Politique de confidentialité',
			'Terms'          => 'Conditions d’utilisation',
			'Cookie Policy'  => 'Politique relative aux cookies',
			'KVKK Notice'    => 'Notice d’information KVKK',
		),
		'it' => array(
			'Privacy Policy' => 'Informativa sulla privacy',
			'Terms'          => 'Termini e condizioni',
			'Cookie Policy'  => 'Informativa sui cookie',
			'KVKK Notice'    => 'Informativa KVKK',
		),
		'es' => array(
			'Privacy Policy' => 'Política de privacidad',
			'Terms'          => 'Términos y condiciones',
			'Cookie Policy'  => 'Política de cookies',
			'KVKK Notice'    => 'Aviso KVKK',
		),
		'pl' => array(
			'Privacy Policy' => 'Polityka prywatności',
			'Terms'          => 'Regulamin',
			'Cookie Policy'  => 'Polityka plików cookie',
			'KVKK Notice'    => 'Klauzula informacyjna KVKK',
		),
		'pt' => array(
			'Privacy Policy' => 'Política de privacidade',
			'Terms'          => 'Termos e condições',
			'Cookie Policy'  => 'Política de cookies',
			'KVKK Notice'    => 'Aviso KVKK',
		),
		'ro' => array(
			'Privacy Policy' => 'Politica de confidențialitate',
			'Terms'          => 'Termeni și condiții',
			'Cookie Policy'  => 'Politica de cookie-uri',
			'KVKK Notice'    => 'Notificare KVKK',
		),
	);

	return $strings[ $language ][ $text ] ?? $translation;
}
