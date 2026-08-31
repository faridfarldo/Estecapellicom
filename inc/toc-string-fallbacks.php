<?php
/**
 * Translations for the "On this page" table of contents.
 *
 * Same contract as the other *-string-fallbacks.php files: WPML String
 * Translation stays authoritative and a saved translation is never replaced.
 * These values only stop English copy leaking onto a translated page while the
 * string is still untranslated in WPML.
 *
 * The TOC heading is rendered from inc/toc.php on every long-form blog post and
 * plain page, in every language, and was the last piece of chrome still reading
 * "ON THIS PAGE" beside translated body copy.
 *
 * Like the consent banner, this is keyed by language rather than split across
 * the per-language files: the strings belong to one small component and are
 * easier to keep in step side by side.
 *
 * @package Estecapelli
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * TOC copy per language, keyed by the English source string.
 *
 * @return array<string,array<string,string>>
 */
function estecapelli_toc_strings() {
	return array(
		'tr' => array(
			'On this page'      => 'Bu sayfada',
			'Table of contents' => 'İçindekiler',
		),
		'fr' => array(
			'On this page'      => 'Sur cette page',
			'Table of contents' => 'Table des matières',
		),
		'it' => array(
			'On this page'      => 'In questa pagina',
			'Table of contents' => 'Indice dei contenuti',
		),
		'es' => array(
			'On this page'      => 'En esta página',
			'Table of contents' => 'Índice de contenidos',
		),
		'pl' => array(
			'On this page'      => 'Na tej stronie',
			'Table of contents' => 'Spis treści',
		),
		'pt' => array(
			'On this page'      => 'Nesta página',
			'Table of contents' => 'Índice',
		),
		'ro' => array(
			'On this page'      => 'Pe această pagină',
			'Table of contents' => 'Cuprins',
		),
	);
}

add_filter( 'gettext', 'estecapelli_toc_gettext_fallback', 22, 3 );
/**
 * Supply the TOC labels only when nothing else has translated the string.
 *
 * The URL is read before WPML is asked, for the reason documented on
 * estecapelli_request_language_code(): on a request WPML cannot resolve it
 * answers with the default language, which would leave the English heading on
 * a page the visitor is reading in Portuguese.
 *
 * @param string $translation Current translated value.
 * @param string $text        English source value.
 * @param string $domain      Text domain.
 * @return string
 */
function estecapelli_toc_gettext_fallback( $translation, $text, $domain ) {
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

	$strings = estecapelli_toc_strings();
	return $strings[ $language ][ $text ] ?? $translation;
}
