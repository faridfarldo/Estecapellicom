<?php
/**
 * Section: Patient Stories — the homepage testimonial stage, reused on a page.
 *
 * The stage renders itself from estecapelli_patient_stories(), which already
 * resolves the right patient set for the current language (and returns nothing
 * for Turkish, where testimonials must not be published). So this section owns
 * no story data of its own: it only lets the page override the heading, and
 * hands off to the shared template part.
 *
 * @package Estecapelli
 */

if ( ! defined( 'ABSPATH' ) ) { exit; }

$section = get_query_var( 'section' );
if ( ! is_array( $section ) ) { return; }

$overrides = array(
	'eyebrow'  => $section['eyebrow'] ?? '',
	'headline' => $section['title']   ?? '',
	'lead'     => $section['lead']    ?? '',
);
$overrides = array_filter( $overrides, static function ( $value ) {
	return is_string( $value ) && '' !== $value;
} );

if ( $overrides ) {
	$estecapelli_stories_override = static function ( $data ) use ( $overrides ) {
		// Never resurrect a heading for a language that publishes no stories.
		if ( empty( $data['stories'] ) ) {
			return $data;
		}
		return array_merge( $data, $overrides );
	};
	add_filter( 'estecapelli_patient_stories', $estecapelli_stories_override );
}

get_template_part( 'template-parts/patient-stories' );

if ( $overrides ) {
	remove_filter( 'estecapelli_patient_stories', $estecapelli_stories_override );
}
