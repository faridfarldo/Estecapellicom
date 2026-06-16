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
			'post_type'      => 'doctor',
			'post_status'    => 'publish',
			'posts_per_page' => -1,
			'orderby'        => array( 'menu_order' => 'ASC', 'title' => 'ASC' ),
		)
	);

	if ( $doctor_posts ) {
		$members = array_map(
			static function ( $doc ) {
				return array(
					'name'       => get_the_title( $doc ),
					'position'   => function_exists( 'get_field' ) ? (string) get_field( 'position', $doc->ID ) : '',
					'photo'      => function_exists( 'get_field' ) ? get_field( 'photo', $doc->ID ) : array(),
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
