<?php
/**
 * Section: Doctor — profile card linked to a CTA.
 *
 * @package Estecapelli
 */

if ( ! defined( 'ABSPATH' ) ) { exit; }

$section = get_query_var( 'section' );
if ( ! is_array( $section ) ) { return; }

$eyebrow     = $section['eyebrow']     ?? '';
$name        = $section['name']        ?? '';
$role        = $section['role']        ?? '';
$photo       = $section['photo']       ?? array();
$body        = $section['body']        ?? '';
$credentials = $section['credentials'] ?? array();
$cta         = $section['cta']         ?? array();

if ( ! $name ) { return; }
?>

<section class="t-doctor">
	<div class="shell t-doctor__shell">

		<?php if ( ! empty( $photo['url'] ) ) : ?>
			<figure class="t-doctor__photo">
				<div class="t-doctor__photo-aura" aria-hidden="true"></div>
				<img src="<?php echo esc_url( $photo['url'] ); ?>" alt="<?php echo esc_attr( $photo['alt'] ?? $name ); ?>" loading="lazy" decoding="async" />
			</figure>
		<?php endif; ?>

		<div class="t-doctor__copy">
			<?php if ( $eyebrow ) : ?>
				<span class="t-doctor__eyebrow">
					<span class="t-doctor__eyebrow-mark" aria-hidden="true"></span>
					<?php echo esc_html( $eyebrow ); ?>
				</span>
			<?php endif; ?>

			<h2 class="t-doctor__name"><?php echo esc_html( $name ); ?></h2>
			<?php if ( $role ) : ?>
				<p class="t-doctor__role"><?php echo esc_html( $role ); ?></p>
			<?php endif; ?>

			<?php if ( $body ) : ?>
				<p class="t-doctor__body"><?php echo esc_html( $body ); ?></p>
			<?php endif; ?>

			<?php if ( ! empty( $credentials ) ) : ?>
				<ul class="t-doctor__credentials">
					<?php
					foreach ( $credentials as $c ) :
						// Repeater rows carry a label; a credential written as a
						// plain string is used as the label itself.
						$label = is_array( $c ) ? ( $c['label'] ?? '' ) : (string) $c;
						if ( '' === $label ) {
							continue;
						}
						?>
						<li class="t-doctor__credential">
							<span class="t-doctor__credential-dot" aria-hidden="true"></span>
							<?php echo esc_html( $label ); ?>
						</li>
					<?php endforeach; ?>
				</ul>
			<?php endif; ?>

			<?php if ( ! empty( $cta['url'] ) ) : ?>
				<a class="btn btn-primary t-doctor__cta" href="<?php echo esc_url( $cta['url'] ); ?>">
					<?php echo esc_html( $cta['label'] ?: __( 'Meet the doctor', 'estecapelli' ) ); ?>
					<?php estecapelli_icon( 'arrow-right', array( 'width' => 16, 'height' => 16 ) ); ?>
				</a>
			<?php endif; ?>
		</div>

	</div>
</section>
