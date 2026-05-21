<?php
/**
 * Homepage: "Your Journey" — six step cards laid around a centred team photo.
 *
 * Cards 1-3 stack on the left, 4-6 on the right; centre photo bridges them.
 * On <=900px the layout reflows to a single-column scroll with the photo on top.
 *
 * @package Estecapelli
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$data = estecapelli_journey_steps();
if ( empty( $data['steps'] ) ) {
	return;
}

$photo_uri = '';
$photo_dir = get_template_directory() . '/assets/images/steps/';
$photo_url = get_template_directory_uri() . '/assets/images/steps/';
$photo_base = ! empty( $data['photo'] ) ? pathinfo( $data['photo'], PATHINFO_FILENAME ) : 'team';
foreach ( array( 'png', 'webp', 'jpg', 'jpeg' ) as $ext ) {
	if ( file_exists( $photo_dir . $photo_base . '.' . $ext ) ) {
		$photo_uri = $photo_url . $photo_base . '.' . $ext;
		break;
	}
}

$closing = $data['closing'] ?? array();
?>

<section class="journey" aria-labelledby="journey-title">

	<div class="journey__bg" aria-hidden="true">
		<span class="journey__bg-glow journey__bg-glow--a"></span>
		<span class="journey__bg-glow journey__bg-glow--b"></span>
		<span class="journey__bg-grid"></span>
	</div>

	<div class="shell">

		<header class="journey__head">
			<?php if ( ! empty( $data['eyebrow'] ) ) : ?>
				<span class="journey__eyebrow">
					<span class="journey__eyebrow-mark" aria-hidden="true"></span>
					<?php echo esc_html( $data['eyebrow'] ); ?>
				</span>
			<?php endif; ?>

			<h2 id="journey-title" class="journey__title">
				<?php echo esc_html( $data['headline'] ); ?>
			</h2>

			<?php if ( ! empty( $data['lead'] ) ) : ?>
				<p class="journey__lead"><?php echo esc_html( $data['lead'] ); ?></p>
			<?php endif; ?>
		</header>

		<div class="journey__stage<?php echo $photo_uri ? '' : ' journey__stage--no-photo'; ?>">

			<ul class="journey__cards journey__cards--left" aria-label="<?php esc_attr_e( 'Steps 1 to 3', 'estecapelli' ); ?>">
				<?php foreach ( array_slice( $data['steps'], 0, 3 ) as $i => $step ) : ?>
					<?php
					$num = $i + 1;
					$tilt = ( 0 === $i % 2 ) ? '-2.2deg' : '1.6deg';
					?>
					<li class="journey__card" style="--num: <?php echo (int) $num; ?>; --tilt: <?php echo esc_attr( $tilt ); ?>">
						<span class="journey__card-num" aria-hidden="true"><?php echo (int) $num; ?></span>
						<div class="journey__card-head">
							<span class="journey__card-icon" aria-hidden="true">
								<?php estecapelli_icon( $step['icon'], array( 'width' => 22, 'height' => 22 ) ); ?>
							</span>
							<span class="journey__card-time"><?php echo esc_html( $step['time'] ); ?></span>
						</div>
						<h3 class="journey__card-title"><?php echo esc_html( $step['title'] ); ?></h3>
						<p class="journey__card-body"><?php echo esc_html( $step['body'] ); ?></p>
						<?php if ( ! empty( $step['link']['url'] ) ) : ?>
							<a class="journey__card-link" href="<?php echo esc_url( $step['link']['url'] ); ?>">
								<?php echo esc_html( $step['link']['label'] ); ?>
								<?php estecapelli_icon( 'arrow-right', array( 'width' => 14, 'height' => 14, 'class' => 'journey__card-arrow' ) ); ?>
							</a>
						<?php endif; ?>
					</li>
				<?php endforeach; ?>
			</ul>

			<?php if ( $photo_uri ) : ?>
				<figure class="journey__photo" aria-hidden="true">
					<span class="journey__photo-aura"></span>
					<img
						class="journey__photo-img"
						src="<?php echo esc_url( $photo_uri ); ?>"
						alt="<?php echo esc_attr( $data['photo_alt'] ); ?>"
						loading="lazy"
						decoding="async"
					/>
				</figure>
			<?php else : ?>
				<div class="journey__photo journey__photo--placeholder" aria-hidden="true">
					<span class="journey__photo-aura"></span>
					<span class="journey__photo-note">
						<?php
						printf(
							/* translators: %s: image path relative to theme */
							esc_html__( 'Drop the team cutout PNG at %s', 'estecapelli' ),
							'<code>/assets/images/steps/' . esc_html( $data['photo'] ?? 'team.png' ) . '</code>'
						); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
						?>
					</span>
				</div>
			<?php endif; ?>

			<ul class="journey__cards journey__cards--right" aria-label="<?php esc_attr_e( 'Steps 4 to 6', 'estecapelli' ); ?>">
				<?php foreach ( array_slice( $data['steps'], 3, 3 ) as $i => $step ) : ?>
					<?php
					$num = $i + 4;
					$tilt = ( 0 === $i % 2 ) ? '2.2deg' : '-1.6deg';
					?>
					<li class="journey__card" style="--num: <?php echo (int) $num; ?>; --tilt: <?php echo esc_attr( $tilt ); ?>">
						<span class="journey__card-num" aria-hidden="true"><?php echo (int) $num; ?></span>
						<div class="journey__card-head">
							<span class="journey__card-icon" aria-hidden="true">
								<?php estecapelli_icon( $step['icon'], array( 'width' => 22, 'height' => 22 ) ); ?>
							</span>
							<span class="journey__card-time"><?php echo esc_html( $step['time'] ); ?></span>
						</div>
						<h3 class="journey__card-title"><?php echo esc_html( $step['title'] ); ?></h3>
						<p class="journey__card-body"><?php echo esc_html( $step['body'] ); ?></p>
						<?php if ( ! empty( $step['link']['url'] ) ) : ?>
							<a class="journey__card-link" href="<?php echo esc_url( $step['link']['url'] ); ?>">
								<?php echo esc_html( $step['link']['label'] ); ?>
								<?php estecapelli_icon( 'arrow-right', array( 'width' => 14, 'height' => 14, 'class' => 'journey__card-arrow' ) ); ?>
							</a>
						<?php endif; ?>
					</li>
				<?php endforeach; ?>
			</ul>

		</div>

		<?php if ( ! empty( $closing ) ) : ?>
			<div class="journey__closing">
				<?php if ( ! empty( $closing['text'] ) ) : ?>
					<p class="journey__closing-text"><?php echo esc_html( $closing['text'] ); ?></p>
				<?php endif; ?>
				<?php if ( ! empty( $closing['cta']['url'] ) ) : ?>
					<a class="btn btn-accent btn-lg journey__closing-cta" href="<?php echo esc_url( $closing['cta']['url'] ); ?>">
						<?php echo esc_html( $closing['cta']['label'] ); ?>
						<?php estecapelli_icon( 'arrow-right', array( 'width' => 18, 'height' => 18 ) ); ?>
					</a>
				<?php endif; ?>
			</div>
		<?php endif; ?>

	</div>
</section>
