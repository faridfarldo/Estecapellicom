<?php
/**
 * Section: Our Clinic — the written account, then the shared photo wall.
 *
 * The homepage already owns a photo wall of the Istanbul clinic. Rather than a
 * second copy of those images, this section writes the substance the homepage
 * block has no room for — equipment, sterility, the safety chain — and then
 * hands off to the same template part for the photos, relabelled for this page
 * and without the partner-hotel strip, which belongs on the homepage.
 *
 * Leaving the "Heading above the photo wall" field empty renders the copy
 * alone, for a page where the photos would repeat something above them.
 *
 * @package Estecapelli
 */

if ( ! defined( 'ABSPATH' ) ) { exit; }

$section = get_query_var( 'section' );
if ( ! is_array( $section ) ) { return; }

$eyebrow       = $section['eyebrow']       ?? '';
$title         = $section['title']         ?? '';
$lead          = $section['lead']          ?? '';
$body          = $section['body']          ?? '';
$points        = ! empty( $section['points'] ) && is_array( $section['points'] ) ? $section['points'] : array();
$gallery_title = trim( (string) ( $section['gallery_title'] ?? '' ) );
$gallery_lead  = trim( (string) ( $section['gallery_lead'] ?? '' ) );

if ( ! $title ) { return; }
?>

<section class="t-clinic" aria-labelledby="t-clinic-title">
	<div class="shell">

		<header class="t-clinic__head">
			<?php if ( $eyebrow ) : ?>
				<span class="t-clinic__eyebrow">
					<span class="t-clinic__eyebrow-mark" aria-hidden="true"></span>
					<?php echo esc_html( $eyebrow ); ?>
				</span>
			<?php endif; ?>

			<h2 id="t-clinic-title" class="t-clinic__title"><?php echo esc_html( $title ); ?></h2>

			<?php if ( $lead ) : ?>
				<p class="t-clinic__lead"><?php echo esc_html( $lead ); ?></p>
			<?php endif; ?>
		</header>

		<?php if ( $body ) : ?>
			<div class="t-clinic__body"><?php echo wp_kses_post( $body ); ?></div>
		<?php endif; ?>

		<?php if ( $points ) : ?>
			<ul class="t-clinic__points">
				<?php foreach ( $points as $point ) :
					if ( empty( $point['title'] ) ) { continue; }
					?>
					<li class="t-clinic__point">
						<span class="t-clinic__point-icon" aria-hidden="true">
							<?php
							estecapelli_render_item_icon(
								array(
									'icon'      => $point['icon'] ?? 'shield-check',
									'icon_file' => $point['icon_file'] ?? null,
								),
								array( 'width' => 22, 'height' => 22 )
							);
							?>
						</span>
						<span class="t-clinic__point-title"><?php echo esc_html( $point['title'] ); ?></span>
						<?php if ( ! empty( $point['body'] ) ) : ?>
							<span class="t-clinic__point-body"><?php echo esc_html( $point['body'] ); ?></span>
						<?php endif; ?>
					</li>
				<?php endforeach; ?>
			</ul>
		<?php endif; ?>

	</div>
</section>

<?php
if ( '' === $gallery_title ) {
	return;
}

// Relabel the shared photo wall for this page and drop the partner hotels.
$estecapelli_clinic_gallery = static function ( $data ) use ( $gallery_title, $gallery_lead ) {
	$data['eyebrow']  = '';
	$data['headline'] = $gallery_title;
	$data['lead']     = $gallery_lead;
	$data['partners'] = array();
	return $data;
};

add_filter( 'estecapelli_facilities', $estecapelli_clinic_gallery );
get_template_part( 'template-parts/facilities' );
remove_filter( 'estecapelli_facilities', $estecapelli_clinic_gallery );
