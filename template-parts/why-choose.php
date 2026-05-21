<?php
/**
 * Homepage "Why Choose Estecapelli" — premium comparison verdict board.
 *
 * @package Estecapelli
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$data = estecapelli_why_choose();
if ( empty( $data['features'] ) ) {
	return;
}

$intro  = $data['intro'] ?? array();
$badges = function_exists( 'estecapelli_footer_badges' ) ? estecapelli_footer_badges() : array();
?>

<section class="why-choose" aria-labelledby="why-choose-title">

	<div class="why-choose__bg" aria-hidden="true">
		<span class="why-choose__bg-glow why-choose__bg-glow--a"></span>
		<span class="why-choose__bg-glow why-choose__bg-glow--b"></span>
		<span class="why-choose__bg-grid"></span>
	</div>

	<div class="shell">

		<header class="why-choose__head">
			<?php if ( ! empty( $data['eyebrow'] ) ) : ?>
				<span class="why-choose__eyebrow" aria-hidden="true">
					<span class="why-choose__eyebrow-line"></span>
					<span class="why-choose__eyebrow-mark"></span>
					<span class="why-choose__eyebrow-text"><?php echo esc_html( $data['eyebrow'] ); ?></span>
					<span class="why-choose__eyebrow-mark"></span>
					<span class="why-choose__eyebrow-line"></span>
				</span>
			<?php endif; ?>

			<h2 id="why-choose-title" class="why-choose__title">
				<?php echo esc_html( $data['headline'] ); ?>
			</h2>

			<?php if ( ! empty( $data['lead'] ) ) : ?>
				<p class="why-choose__lead"><?php echo esc_html( $data['lead'] ); ?></p>
			<?php endif; ?>
		</header>

		<div class="why-choose__grid">

			<div class="why-choose__intro">

				<?php if ( ! empty( $intro['title'] ) ) : ?>
					<h3 class="why-choose__intro-title">
						<?php echo esc_html( $intro['title'] ); ?>
					</h3>
				<?php endif; ?>

				<?php if ( ! empty( $intro['body'] ) ) : ?>
					<p class="why-choose__intro-body"><?php echo esc_html( $intro['body'] ); ?></p>
				<?php endif; ?>

				<?php if ( ! empty( $badges ) ) : ?>
					<ul class="why-choose__badges" aria-label="<?php esc_attr_e( 'Accreditations and certifications', 'estecapelli' ); ?>">
						<?php foreach ( $badges as $i => $badge ) : ?>
							<li class="why-choose__badge" style="--i: <?php echo (int) $i; ?>">
								<img
									class="why-choose__badge-img"
									src="<?php echo esc_url( $badge['src'] ); ?>"
									alt="<?php echo esc_attr( $badge['alt'] ); ?>"
									loading="lazy"
									decoding="async"
								/>
							</li>
						<?php endforeach; ?>
					</ul>
				<?php endif; ?>

				<?php if ( ! empty( $intro['caption'] ) ) : ?>
					<p class="why-choose__intro-caption">
						<span class="why-choose__intro-caption-dot" aria-hidden="true"></span>
						<?php echo esc_html( $intro['caption'] ); ?>
					</p>
				<?php endif; ?>

				<?php if ( ! empty( $intro['cta']['url'] ) ) : ?>
					<a class="btn btn-primary why-choose__cta" href="<?php echo esc_url( $intro['cta']['url'] ); ?>">
						<?php echo esc_html( $intro['cta']['label'] ); ?>
						<?php estecapelli_icon( 'arrow-right', array( 'width' => 16, 'height' => 16 ) ); ?>
					</a>
				<?php endif; ?>

			</div>

			<div class="why-choose__board" role="table" aria-label="<?php esc_attr_e( 'Estecapelli versus other clinics — feature comparison', 'estecapelli' ); ?>">

				<div class="why-choose__board-head" role="row">
					<span class="why-choose__board-head-spacer" aria-hidden="true"></span>

					<span class="why-choose__board-col why-choose__board-col--them" role="columnheader">
						<span class="why-choose__board-col-tag"><?php echo esc_html( $data['them_tag'] ); ?></span>
						<span class="why-choose__board-col-label"><?php echo esc_html( $data['them_label'] ); ?></span>
					</span>

					<span class="why-choose__board-col why-choose__board-col--us" role="columnheader">
						<span class="why-choose__board-col-tag"><?php echo esc_html( $data['us_tag'] ); ?></span>
						<span class="why-choose__board-col-label">
							<?php estecapelli_brand_mark( 'comparison' ); ?>
						</span>
						<span class="why-choose__board-col-glow" aria-hidden="true"></span>
					</span>
				</div>

				<ul class="why-choose__rows">
					<?php foreach ( $data['features'] as $i => $row ) : ?>
						<li
							class="why-choose__row"
							role="row"
							style="--row: <?php echo (int) $i; ?>"
						>
							<span class="why-choose__row-label" role="rowheader">
								<span class="why-choose__row-icon" aria-hidden="true">
									<?php estecapelli_icon( $row['icon'], array( 'width' => 18, 'height' => 18 ) ); ?>
								</span>
								<span class="why-choose__row-text"><?php echo esc_html( $row['label'] ); ?></span>
							</span>

							<span class="why-choose__cell why-choose__cell--them" role="cell">
								<?php if ( ! empty( $row['them'] ) ) : ?>
									<span class="why-choose__mark why-choose__mark--yes why-choose__mark--muted" aria-label="<?php esc_attr_e( 'Included', 'estecapelli' ); ?>">
										<?php estecapelli_icon( 'check-circle', array( 'width' => 22, 'height' => 22 ) ); ?>
									</span>
								<?php else : ?>
									<span class="why-choose__mark why-choose__mark--no" aria-label="<?php esc_attr_e( 'Not included', 'estecapelli' ); ?>">
										<?php estecapelli_icon( 'x-circle', array( 'width' => 22, 'height' => 22 ) ); ?>
									</span>
								<?php endif; ?>
							</span>

							<span class="why-choose__cell why-choose__cell--us" role="cell">
								<span class="why-choose__mark why-choose__mark--yes why-choose__mark--ours" aria-label="<?php esc_attr_e( 'Included at Estecapelli', 'estecapelli' ); ?>">
									<?php estecapelli_icon( 'check-circle', array( 'width' => 22, 'height' => 22 ) ); ?>
									<span class="why-choose__mark-pulse" aria-hidden="true"></span>
								</span>
							</span>
						</li>
					<?php endforeach; ?>
				</ul>

				<?php if ( ! empty( $data['footnote'] ) ) : ?>
					<footer class="why-choose__board-foot">
						<span class="why-choose__board-foot-mark" aria-hidden="true"></span>
						<?php echo esc_html( $data['footnote'] ); ?>
					</footer>
				<?php endif; ?>

			</div>

		</div>

	</div>
</section>
