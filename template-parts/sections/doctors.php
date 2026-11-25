<?php
/**
 * Section: Doctors — roster grid.
 *
 * Each card shows the doctor's photo, name and position, plus a "View Résumé"
 * button that links to their own profile page.
 *
 * The roster is populated automatically from the `doctor` post type: every
 * published doctor appears here, ordered by their menu order then title, with
 * the résumé button pointing at their profile permalink. No manual card-by-card
 * editing — adding a doctor in the Doctors menu is all it takes. The manual
 * `members` repeater is kept only as a fallback for installs that have no
 * doctor posts yet (e.g. a fresh import before the doctors are created).
 *
 * @package Estecapelli
 */

if ( ! defined( 'ABSPATH' ) ) { exit; }

$section = get_query_var( 'section' );
if ( ! is_array( $section ) ) { return; }

$eyebrow = $section['eyebrow'] ?? '';
$title   = $section['title']   ?? '';
$lead    = $section['lead']    ?? '';
$members = $section['members'] ?? array();

// Prefer live doctor posts over the manual repeater.
if ( post_type_exists( 'doctor' ) ) {
	$doctor_posts = get_posts(
		array(
			'post_type'        => 'doctor',
			'post_status'      => 'publish',
			'posts_per_page'   => -1,
			'orderby'          => array( 'menu_order' => 'ASC', 'title' => 'ASC' ),
			// get_posts() defaults suppress_filters to true, which bypasses WPML's
			// language filter and leaks every language's doctors onto one page.
			'suppress_filters' => false,
		)
	);

	// Doctors removed from the site stay hidden in every language, even before
	// Tools → New Doctors has moved their translations to the trash.
	if ( $doctor_posts && function_exists( 'estecapelli_retired_doctor_slugs' ) ) {
		$retired      = estecapelli_retired_doctor_slugs();
		$doctor_posts = array_values(
			array_filter(
				$doctor_posts,
				static function ( $doc ) use ( $retired ) {
					return ! in_array( preg_replace( '/-\d+$/', '', $doc->post_name ), $retired, true );
				}
			)
		);
	}

	if ( $doctor_posts ) {
		$default_lang = apply_filters( 'wpml_default_language', null );
		$current_lang = apply_filters( 'wpml_current_language', null );

		$members = array_map(
			static function ( $doc ) use ( $default_lang, $current_lang ) {
				$photo = function_exists( 'get_field' ) ? get_field( 'photo', $doc->ID ) : array();
				// Fall back to the theme-bundled résumé photo URL when no portrait
				// has been uploaded (an uploaded photo always wins).
				if ( empty( $photo['url'] ) && function_exists( 'get_field' ) ) {
					$rurl = (string) get_field( 'resume_photo_url', $doc->ID );
					if ( '' !== $rurl ) {
						$photo = array( 'url' => $rurl, 'alt' => get_the_title( $doc ) );
					}
				}
				// Portraits are uploaded on the English profile only. A translated
				// card with no photo of its own borrows it, the same way the
				// profile page (single-doctor.php) does. Display only.
				if ( empty( $photo['url'] ) && function_exists( 'get_field' ) && $default_lang && $current_lang && $default_lang !== $current_lang ) {
					$source_id = (int) apply_filters( 'wpml_object_id', $doc->ID, 'doctor', false, $default_lang );
					if ( $source_id && $source_id !== (int) $doc->ID ) {
						$src_photo = get_field( 'photo', $source_id );
						$src_url   = (string) get_field( 'resume_photo_url', $source_id );
						if ( is_array( $src_photo ) && ! empty( $src_photo['url'] ) ) {
							$photo = array( 'url' => $src_photo['url'], 'alt' => get_the_title( $doc ) );
						} elseif ( '' !== $src_url ) {
							$photo = array( 'url' => $src_url, 'alt' => get_the_title( $doc ) );
						}
					}
				}
				return array(
					'name'       => get_the_title( $doc ),
					'position'   => function_exists( 'get_field' ) ? (string) get_field( 'position', $doc->ID ) : '',
					'photo'      => $photo,
					'resume_url' => get_permalink( $doc ),
				);
			},
			$doctor_posts
		);
	}
}

if ( ! $title || empty( $members ) ) { return; }

/**
 * Build initials from a name for the photo-less placeholder.
 */
$initials = static function ( $name ) {
	$parts = preg_split( '/\s+/', trim( wp_strip_all_tags( $name ) ) );
	// Skip honorifics so initials read from the actual name.
	$parts = array_values( array_filter( $parts, static function ( $p ) {
		return ! preg_match( '/^(op\.?|dr\.?|prof\.?|assoc\.?|md)$/i', $p );
	} ) );
	$first = mb_substr( $parts[0] ?? '', 0, 1 );
	$last  = count( $parts ) > 1 ? mb_substr( end( $parts ), 0, 1 ) : '';
	return mb_strtoupper( $first . $last );
};
?>

<section class="t-docs" aria-labelledby="t-docs-<?php echo esc_attr( sanitize_title( $title ) ); ?>">
	<div class="shell">

		<header class="t-docs__head">
			<?php if ( $eyebrow ) : ?>
				<span class="t-docs__eyebrow">
					<span class="t-docs__eyebrow-mark" aria-hidden="true"></span>
					<?php echo esc_html( $eyebrow ); ?>
				</span>
			<?php endif; ?>
			<h2 id="t-docs-<?php echo esc_attr( sanitize_title( $title ) ); ?>" class="t-docs__title"><?php echo esc_html( $title ); ?></h2>
			<?php if ( $lead ) : ?>
				<p class="t-docs__lead"><?php echo esc_html( $lead ); ?></p>
			<?php endif; ?>
		</header>

		<ul class="t-docs__grid">
			<?php foreach ( $members as $m ) :
				$name = $m['name'] ?? '';
				if ( ! $name ) { continue; }
				$position = $m['position']   ?? '';
				$photo    = $m['photo']      ?? array();
				$resume   = $m['resume_url'] ?? '';
				?>
				<li class="t-docs__card">
					<figure class="t-docs__photo">
						<?php if ( ! empty( $photo['url'] ) ) : ?>
							<img class="t-docs__img" src="<?php echo esc_url( $photo['url'] ); ?>" alt="<?php echo esc_attr( $photo['alt'] ?? $name ); ?>" loading="lazy" decoding="async" />
						<?php else : ?>
							<span class="t-docs__placeholder" aria-hidden="true"><?php echo esc_html( $initials( $name ) ); ?></span>
						<?php endif; ?>
					</figure>

					<div class="t-docs__info">
						<h3 class="t-docs__name"><?php echo esc_html( $name ); ?></h3>
						<?php if ( $position ) : ?>
							<p class="t-docs__position"><?php echo esc_html( $position ); ?></p>
						<?php endif; ?>

						<?php if ( $resume ) : ?>
							<a class="t-docs__resume" href="<?php echo esc_url( $resume ); ?>">
								<?php echo esc_html__( 'View Résumé', 'estecapelli' ); ?>
								<?php estecapelli_icon( 'arrow-right', array( 'width' => 16, 'height' => 16 ) ); ?>
							</a>
						<?php endif; ?>
					</div>
				</li>
			<?php endforeach; ?>
		</ul>

	</div>
</section>
