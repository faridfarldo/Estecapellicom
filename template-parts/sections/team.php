<?php
/**
 * Section: Team — member grid.
 *
 * Two variants share one layout:
 *   - "medical"     → shows each member's role/position under the name.
 *   - "consultant"  → shows the languages they speak as flag chips.
 *
 * @package Estecapelli
 */

if ( ! defined( 'ABSPATH' ) ) { exit; }

$section = get_query_var( 'section' );
if ( ! is_array( $section ) ) { return; }

$eyebrow = $section['eyebrow'] ?? '';
$title   = $section['title']   ?? '';
$lead    = $section['lead']    ?? '';
$variant = $section['variant'] ?? 'medical';
$members = $section['members'] ?? array();

if ( ! $title || empty( $members ) ) { return; }

/**
 * Build initials from a name for the photo-less placeholder.
 */
$initials = static function ( $name ) {
	$parts = preg_split( '/\s+/', trim( wp_strip_all_tags( $name ) ) );
	$first = mb_substr( $parts[0] ?? '', 0, 1 );
	$last  = count( $parts ) > 1 ? mb_substr( end( $parts ), 0, 1 ) : '';
	return mb_strtoupper( $first . $last );
};
?>

<section class="t-team t-team--<?php echo esc_attr( $variant ); ?>" aria-labelledby="t-team-<?php echo esc_attr( sanitize_title( $title ) ); ?>">
	<div class="shell">

		<header class="t-team__head">
			<?php if ( $eyebrow ) : ?>
				<span class="t-team__eyebrow">
					<span class="t-team__eyebrow-mark" aria-hidden="true"></span>
					<?php echo esc_html( $eyebrow ); ?>
				</span>
			<?php endif; ?>
			<h2 id="t-team-<?php echo esc_attr( sanitize_title( $title ) ); ?>" class="t-team__title"><?php echo esc_html( $title ); ?></h2>
			<?php if ( $lead ) : ?>
				<p class="t-team__lead"><?php echo esc_html( $lead ); ?></p>
			<?php endif; ?>
		</header>

		<ul class="t-team__grid">
			<?php foreach ( $members as $m ) :
				$name      = $m['name'] ?? '';
				if ( ! $name ) { continue; }
				$role      = $m['role'] ?? '';
				$photo     = $m['photo'] ?? array();
				$languages = $m['languages'] ?? array();
				// An uploaded ACF image wins; otherwise fall back to a plain URL
				// (used for theme-bundled photos passed through the page seed).
				$photo_src = ! empty( $photo['url'] ) ? $photo['url'] : ( $m['photo_url'] ?? '' );
				?>
				<li class="t-team__card">
					<figure class="t-team__photo">
						<span class="t-team__aura" aria-hidden="true"></span>
						<?php if ( $photo_src ) : ?>
							<img class="t-team__img" src="<?php echo esc_url( $photo_src ); ?>" alt="<?php echo esc_attr( $photo['alt'] ?? $name ); ?>" loading="lazy" decoding="async" />
						<?php else : ?>
							<span class="t-team__placeholder" aria-hidden="true"><?php echo esc_html( $initials( $name ) ); ?></span>
						<?php endif; ?>
					</figure>

					<div class="t-team__info">
						<h3 class="t-team__name"><?php echo esc_html( $name ); ?></h3>

						<?php if ( 'consultant' === $variant && ! empty( $languages ) ) : ?>
							<ul class="t-team__langs" aria-label="<?php esc_attr_e( 'Languages spoken', 'estecapelli' ); ?>">
								<?php foreach ( $languages as $lang ) :
									$label   = $lang['label']   ?? '';
									$country = strtolower( trim( $lang['country'] ?? '' ) );
									if ( ! $label ) { continue; }
									?>
									<li class="t-team__lang">
										<?php if ( $country ) : ?>
											<img class="t-team__flag"
												src="https://flagcdn.com/w40/<?php echo esc_attr( $country ); ?>.png"
												srcset="https://flagcdn.com/w80/<?php echo esc_attr( $country ); ?>.png 2x"
												width="20" height="15"
												alt="" loading="lazy" decoding="async" />
										<?php endif; ?>
										<span><?php echo esc_html( $label ); ?></span>
									</li>
								<?php endforeach; ?>
							</ul>
						<?php elseif ( $role ) : ?>
							<span class="t-team__role"><?php echo esc_html( $role ); ?></span>
						<?php endif; ?>
					</div>
				</li>
			<?php endforeach; ?>
		</ul>

	</div>
</section>
