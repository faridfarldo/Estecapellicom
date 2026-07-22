<?php
/**
 * Homepage: "Real Stories" — cinema stage + poster wall.
 *
 * One hero story dominates the left column (editorial pre-title, video
 * poster, 5-star rating, name, country, body, meta pills). On the right,
 * a vertical "poster wall" of the remaining stories the visitor can click
 * to swap into the hero. A YouTube lightbox opens on play.
 *
 * @package Estecapelli
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$data = estecapelli_patient_stories();
if ( empty( $data['stories'] ) ) {
	return;
}

$stories = $data['stories'];

/**
 * Helper: return a YouTube hqdefault thumbnail URL when the ID is set,
 * otherwise an empty string so the renderer falls back to a styled poster.
 */
$thumb = function ( $id ) {
	$id = trim( (string) $id );
	if ( '' === $id ) {
		return '';
	}
	return 'https://img.youtube.com/vi/' . rawurlencode( $id ) . '/hqdefault.jpg';
};
?>

<section class="stories" aria-labelledby="stories-title">

	<div class="stories__bg" aria-hidden="true">
		<span class="stories__bg-quote stories__bg-quote--a">&ldquo;</span>
		<span class="stories__bg-quote stories__bg-quote--b">&rdquo;</span>
	</div>

	<div class="shell">

		<header class="stories__head">
			<?php if ( ! empty( $data['eyebrow'] ) ) : ?>
				<span class="stories__eyebrow">
					<span class="stories__eyebrow-mark" aria-hidden="true"></span>
					<?php echo esc_html( $data['eyebrow'] ); ?>
				</span>
			<?php endif; ?>

			<h2 id="stories-title" class="stories__title">
				<?php echo esc_html( $data['headline'] ); ?>
			</h2>

			<?php if ( ! empty( $data['lead'] ) ) : ?>
				<p class="stories__lead"><?php echo esc_html( $data['lead'] ); ?></p>
			<?php endif; ?>
		</header>

		<div class="stories__stage" data-stories>

			<div class="stories__heroes">
				<?php foreach ( $stories as $i => $story ) :
					$is_active = ( 0 === $i );
					// Big stage poster = the YouTube video's own thumbnail.
					$thumbnail = ! empty( $story['poster'] ) ? $story['poster'] : $thumb( $story['video_id'] );
					$initial   = mb_substr( $story['name'], 0, 1 );
					?>
					<article
						class="stories__hero"
						data-stories-hero
						data-key="<?php echo esc_attr( $story['key'] ); ?>"
						<?php echo $is_active ? '' : 'hidden'; ?>
					>
						<div class="stories__hero-pre">
							<span class="stories__hero-quote" aria-hidden="true">&ldquo;</span>
							<p class="stories__hero-pretitle">
								<?php if ( ! empty( $story['grafts'] ) ) : ?>
									<span class="stories__hero-grafts"><?php echo esc_html( $story['grafts'] ); ?>&nbsp;<?php esc_html_e( 'grafts', 'estecapelli' ); ?></span>
									<span class="stories__hero-divider" aria-hidden="true">·</span>
								<?php endif; ?>
								<span class="stories__hero-tech"><?php echo esc_html( $story['technique'] ); ?></span>
							</p>
						</div>

						<button
							type="button"
							class="stories__hero-poster<?php echo $thumbnail ? '' : ' stories__hero-poster--placeholder'; ?>"
							<?php if ( $thumbnail ) : ?>style="--poster-bg:url('<?php echo esc_url( $thumbnail ); ?>')"<?php endif; ?>
							data-stories-play="<?php echo esc_attr( $story['video_id'] ); ?>"
							data-story-title="<?php echo esc_attr( $story['name'] . ' — ' . $story['pre_title'] ); ?>"
							aria-label="<?php echo esc_attr( sprintf( __( 'Play %s — %s', 'estecapelli' ), $story['name'], $story['pre_title'] ) ); ?>"
						>
							<?php if ( $thumbnail ) : ?>
								<img class="stories__hero-thumb" src="<?php echo esc_url( $thumbnail ); ?>" alt="" loading="lazy" decoding="async"
									<?php if ( ! empty( $story['poster_pos'] ) ) : ?>style="object-position: <?php echo esc_attr( $story['poster_pos'] ); ?>"<?php endif; ?>
									<?php if ( ! empty( $story['video_id'] ) ) : ?>onerror="this.onerror=null;this.src='https://i.ytimg.com/vi/<?php echo esc_js( $story['video_id'] ); ?>/hqdefault.jpg';"<?php endif; ?> />
							<?php else : ?>
								<span class="stories__hero-placeholder" aria-hidden="true">
									<span class="stories__hero-placeholder-initial"><?php echo esc_html( $initial ); ?></span>
								</span>
							<?php endif; ?>
							<span class="stories__hero-poster-wash" aria-hidden="true"></span>

							<span class="stories__hero-pretitle-card" aria-hidden="true">
								<?php echo esc_html( $story['pre_title'] ); ?>
							</span>

							<span class="stories__hero-play" aria-hidden="true">
								<?php estecapelli_icon( 'play', array( 'width' => 22, 'height' => 22 ) ); ?>
							</span>
						</button>

						<div class="stories__hero-body">
							<div class="stories__hero-stars" aria-label="<?php echo esc_attr( sprintf( __( 'Rated %d out of 5', 'estecapelli' ), (int) $story['rating'] ) ); ?>">
								<?php for ( $s = 0; $s < 5; $s++ ) : ?>
									<?php estecapelli_icon( 'star', array( 'width' => 16, 'height' => 16, 'class' => $s < (int) $story['rating'] ? 'stories__star stories__star--on' : 'stories__star' ) ); ?>
								<?php endfor; ?>
							</div>

							<h3 class="stories__hero-name"><?php echo esc_html( $story['name'] ); ?></h3>

							<p class="stories__hero-text"><?php echo esc_html( $story['body'] ); ?></p>

							<ul class="stories__hero-pills" aria-label="<?php esc_attr_e( 'Procedure details', 'estecapelli' ); ?>">
								<?php if ( ! empty( $story['grafts'] ) ) : ?>
								<li class="stories__hero-pill stories__hero-pill--grafts">
									<span class="stories__hero-pill-value"><?php echo esc_html( $story['grafts'] ); ?></span>
									<span class="stories__hero-pill-label"><?php esc_html_e( 'Grafts', 'estecapelli' ); ?></span>
								</li>
								<?php endif; ?>
								<li class="stories__hero-pill stories__hero-pill--tech">
									<span class="stories__hero-pill-value"><?php echo esc_html( $story['technique'] ); ?></span>
									<span class="stories__hero-pill-label"><?php esc_html_e( 'Technique', 'estecapelli' ); ?></span>
								</li>
								<li class="stories__hero-pill stories__hero-pill--country">
									<span class="stories__hero-pill-value">
										<span class="stories__hero-pill-flag" aria-hidden="true"><?php echo esc_html( $story['flag'] ); ?></span>
										<?php echo esc_html( $story['country'] ); ?>
									</span>
									<span class="stories__hero-pill-label"><?php esc_html_e( 'Patient from', 'estecapelli' ); ?></span>
								</li>
							</ul>
						</div>
					</article>
				<?php endforeach; ?>
			</div>

			<aside class="stories__wall" aria-label="<?php esc_attr_e( 'More patient stories', 'estecapelli' ); ?>">
				<header class="stories__wall-head">
					<span class="stories__wall-eyebrow"><?php esc_html_e( 'On the playlist', 'estecapelli' ); ?></span>
					<span class="stories__wall-count">
						<span data-stories-current>1</span>
						<span class="stories__wall-count-sep">/</span>
						<span><?php echo (int) count( $stories ); ?></span>
					</span>
				</header>

				<ul class="stories__wall-list" data-stories-wall>
					<?php foreach ( $stories as $i => $story ) :
						$is_active = ( 0 === $i );
						// Side playlist thumbnail = the patient's own after-photo.
						$thumbnail = ! empty( $story['photo'] ) ? $story['photo'] : $thumb( $story['video_id'] );
						$initial   = mb_substr( $story['name'], 0, 1 );
						?>
						<li class="stories__wall-item">
							<button
								type="button"
								class="stories__poster"
								data-stories-select="<?php echo esc_attr( $story['key'] ); ?>"
								data-index="<?php echo (int) ( $i + 1 ); ?>"
								data-active="<?php echo $is_active ? 'true' : 'false'; ?>"
								aria-pressed="<?php echo $is_active ? 'true' : 'false'; ?>"
							>
								<span class="stories__poster-thumb<?php echo $thumbnail ? '' : ' stories__poster-thumb--placeholder'; ?>"<?php if ( $thumbnail ) : ?> style="--poster-bg:url('<?php echo esc_url( $thumbnail ); ?>')"<?php endif; ?>>
									<?php if ( $thumbnail ) : ?>
										<img src="<?php echo esc_url( $thumbnail ); ?>" alt="" loading="lazy" decoding="async"<?php if ( ! empty( $story['photo_pos'] ) ) : ?> style="object-position: <?php echo esc_attr( $story['photo_pos'] ); ?>"<?php endif; ?> />
									<?php else : ?>
										<span class="stories__poster-initial" aria-hidden="true"><?php echo esc_html( $initial ); ?></span>
									<?php endif; ?>
									<span class="stories__poster-flag" aria-hidden="true"><?php echo esc_html( $story['flag'] ); ?></span>
								</span>
								<span class="stories__poster-body">
									<span class="stories__poster-name"><?php echo esc_html( $story['name'] ); ?></span>
									<span class="stories__poster-meta">
										<?php
										if ( ! empty( $story['grafts'] ) ) {
											printf(
												/* translators: 1: graft count, 2: technique. */
												esc_html__( '%1$s grafts · %2$s', 'estecapelli' ),
												esc_html( $story['grafts'] ),
												esc_html( $story['technique'] )
											);
										} else {
											echo esc_html( $story['technique'] );
										}
										?>
									</span>
								</span>
								<span class="stories__poster-marker" aria-hidden="true"></span>
							</button>
						</li>
					<?php endforeach; ?>
				</ul>
			</aside>

		</div>

	</div>

	<div class="stories__lightbox" data-stories-lightbox hidden role="dialog" aria-modal="true" aria-labelledby="stories-lightbox-title">
		<button type="button" class="stories__lightbox-backdrop" data-stories-lightbox-close aria-label="<?php esc_attr_e( 'Close video', 'estecapelli' ); ?>"></button>
		<div class="stories__lightbox-shell">
			<header class="stories__lightbox-head">
				<h4 id="stories-lightbox-title" class="stories__lightbox-title" data-stories-lightbox-title></h4>
				<button type="button" class="stories__lightbox-close" data-stories-lightbox-close aria-label="<?php esc_attr_e( 'Close', 'estecapelli' ); ?>">
					<?php estecapelli_icon( 'close', array( 'width' => 20, 'height' => 20 ) ); ?>
				</button>
			</header>
			<div class="stories__lightbox-frame" data-stories-lightbox-frame></div>
		</div>
	</div>
</section>
