<?php
/**
 * Small French fallbacks for theme UI strings used around treatment listings.
 *
 * WPML String Translation remains authoritative: a saved WPML translation is
 * never replaced. These values only prevent English UI copy from leaking onto
 * French pages while a string is still untranslated in WPML.
 *
 * @package Estecapelli
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Whether the current request is being rendered in French.
 *
 * @return bool
 */
function estecapelli_is_french_request() {
	$language = apply_filters( 'wpml_current_language', null );
	if ( $language ) {
		return 'fr' === $language;
	}

	return 0 === strpos( determine_locale(), 'fr_' );
}

add_filter( 'gettext', 'estecapelli_fr_gettext_fallback', 20, 3 );
/**
 * Supply French only when no plugin or language file translated the string.
 *
 * @param string $translation Current translated value.
 * @param string $text        English source value.
 * @param string $domain      Text domain.
 * @return string
 */
function estecapelli_fr_gettext_fallback( $translation, $text, $domain ) {
	if ( 'estecapelli' !== $domain || $translation !== $text || ! estecapelli_is_french_request() ) {
		return $translation;
	}

	$strings = array(
		'From the Journal'                        => 'Le journal',
		'Articles from the Estecapelli journal.' => 'Articles du journal Estecapelli.',
		'All articles'                            => 'Tous les articles',
		'Read more'                               => 'En savoir plus',
		'Posts pagination'                        => 'Pagination des articles',
		'Newer'                                   => 'Plus récents',
		'Older'                                   => 'Plus anciens',
		'No articles found here yet.'             => 'Aucun article trouvé pour le moment.',
	);

	return $strings[ $text ] ?? $translation;
}

add_filter( 'ngettext', 'estecapelli_fr_ngettext_fallback', 20, 5 );
/**
 * Translate the reading-time label shown on archive cards.
 *
 * @param string $translation Current translated value.
 * @param string $single      Singular English source.
 * @param string $plural      Plural English source.
 * @param int    $number      Quantity.
 * @param string $domain      Text domain.
 * @return string
 */
function estecapelli_fr_ngettext_fallback( $translation, $single, $plural, $number, $domain ) {
	if (
		'estecapelli' === $domain &&
		estecapelli_is_french_request() &&
		in_array( $translation, array( $single, $plural ), true ) &&
		'%d min read' === $single &&
		'%d min read' === $plural
	) {
		return '%d min de lecture';
	}

	return $translation;
}
