<?php
/**
 * Single Job — one vacancy, and the form to apply for it.
 *
 * The role name is the post title and the description is the editor body, so
 * the clinic writes a posting the way it writes any other page. Everything else
 * on this template comes from the short Role Details form (inc/careers.php).
 *
 * The application form posts back to this same URL; inc/careers.php handles it
 * on template_redirect and returns with ?applied=1 or ?apply_error=<code>.
 *
 * @package Estecapelli
 */

get_header();

while ( have_posts() ) :
	the_post();

	$job_id  = get_the_ID();
	$meta    = estecapelli_job_meta( $job_id );
	$is_open = estecapelli_job_is_open( $job_id );
	$closes  = function_exists( 'get_field' ) ? trim( (string) get_field( 'closes_on', $job_id ) ) : '';

	// phpcs:disable WordPress.Security.NonceVerification.Recommended -- display state only.
	$applied = isset( $_GET['applied'] ) && '1' === $_GET['applied'];
	$error   = isset( $_GET['apply_error'] ) ? sanitize_key( wp_unslash( $_GET['apply_error'] ) ) : '';
	// phpcs:enable WordPress.Security.NonceVerification.Recommended
	?>

	<article class="job-single">

		<header class="job-hero">
			<div class="shell job-hero__shell">
				<a class="job-hero__back" href="<?php echo esc_url( estecapelli_indexed_url( '/en/about-us/careers' ) ); ?>">
					<?php estecapelli_icon( 'chevron-left', array( 'width' => 16, 'height' => 16 ) ); ?>
					<?php esc_html_e( 'All open positions', 'estecapelli' ); ?>
				</a>

				<h1 class="job-hero__title"><?php the_title(); ?></h1>

				<?php if ( $meta ) : ?>
					<ul class="job-hero__meta">
						<?php foreach ( $meta as $item ) : ?>
							<li class="job-chip job-chip--lg">
								<?php estecapelli_icon( $item['icon'], array( 'width' => 16, 'height' => 16 ) ); ?>
								<span>
									<span class="job-chip__label"><?php echo esc_html( $item['label'] ); ?></span>
									<span class="job-chip__value"><?php echo esc_html( $item['value'] ); ?></span>
								</span>
							</li>
						<?php endforeach; ?>
					</ul>
				<?php endif; ?>

				<?php if ( $is_open ) : ?>
					<a class="btn btn-primary btn-lg job-hero__cta" href="#apply">
						<?php esc_html_e( 'Apply for this role', 'estecapelli' ); ?>
						<?php estecapelli_icon( 'arrow-right', array( 'width' => 18, 'height' => 18 ) ); ?>
					</a>
					<?php if ( $closes ) : ?>
						<p class="job-hero__deadline">
							<?php
							printf(
								/* translators: %s: closing date */
								esc_html__( 'Applications close on %s', 'estecapelli' ),
								esc_html( date_i18n( get_option( 'date_format' ), (int) strtotime( $closes ) ) )
							);
							?>
						</p>
					<?php endif; ?>
				<?php else : ?>
					<p class="job-hero__closed"><?php esc_html_e( 'Applications for this role have closed.', 'estecapelli' ); ?></p>
				<?php endif; ?>
			</div>
		</header>

		<div class="shell job-body">

			<div class="job-content prose-content">
				<?php the_content(); ?>
			</div>

			<?php if ( $is_open ) : ?>
				<section class="job-apply" id="apply">
					<div class="job-apply__head">
						<h2 class="job-apply__title"><?php esc_html_e( 'Apply for this role', 'estecapelli' ); ?></h2>
						<p class="job-apply__lead"><?php esc_html_e( 'Send us your CV and a few lines about why this role interests you. Every application is read by a person, and we reply either way.', 'estecapelli' ); ?></p>
					</div>

					<?php if ( $applied ) : ?>
						<div class="contact-alert job-apply__alert" role="status">
							<?php esc_html_e( 'Thank you — your application has been received. Our team will be in touch.', 'estecapelli' ); ?>
						</div>
					<?php elseif ( $error ) : ?>
						<div class="contact-alert contact-alert--error job-apply__alert" role="alert">
							<?php echo esc_html( estecapelli_application_error_message( $error ) ); ?>
						</div>
					<?php endif; ?>

					<?php if ( ! $applied ) : ?>
						<form class="contact-form job-apply__form" method="post" enctype="multipart/form-data" action="<?php echo esc_url( get_permalink() ); ?>#apply">

							<div class="contact-form__row">
								<div class="contact-form__field">
									<label for="ap-name"><?php esc_html_e( 'Full name', 'estecapelli' ); ?> <span aria-hidden="true">*</span></label>
									<input id="ap-name" type="text" name="applicant_name" required autocomplete="name" placeholder="<?php esc_attr_e( 'Your name', 'estecapelli' ); ?>" />
								</div>
								<div class="contact-form__field">
									<label for="ap-email"><?php esc_html_e( 'Email address', 'estecapelli' ); ?> <span aria-hidden="true">*</span></label>
									<input id="ap-email" type="email" name="applicant_email" required autocomplete="email" inputmode="email" placeholder="<?php esc_attr_e( 'you@example.com', 'estecapelli' ); ?>" />
								</div>
							</div>

							<div class="contact-form__field">
								<label for="ap-phone"><?php esc_html_e( 'Phone number', 'estecapelli' ); ?></label>
								<?php // Same international phone control the lead forms use, so the
									// dial code travels with the number here too. ?>
								<input id="ap-phone" class="js-intl-phone" type="tel" name="applicant_phone" autocomplete="tel" inputmode="tel" placeholder="<?php esc_attr_e( 'Phone number', 'estecapelli' ); ?>" />
							</div>

							<div class="contact-form__field job-apply__file">
								<label for="ap-cv"><?php esc_html_e( 'Your CV', 'estecapelli' ); ?> <span aria-hidden="true">*</span></label>
								<input id="ap-cv" type="file" name="applicant_cv" required accept=".pdf,.doc,.docx,.rtf,.odt,application/pdf,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document" />
								<p class="job-apply__hint">
									<?php
									printf(
										/* translators: %s: maximum file size, e.g. "5 MB" */
										esc_html__( 'PDF or Word document, up to %s.', 'estecapelli' ),
										esc_html( size_format( ESTECAPELLI_CV_MAX_BYTES ) )
									);
									?>
								</p>
							</div>

							<div class="contact-form__field">
								<label for="ap-message"><?php esc_html_e( 'Why this role?', 'estecapelli' ); ?></label>
								<textarea id="ap-message" name="applicant_message" rows="5" placeholder="<?php esc_attr_e( 'A few lines about your experience and what interests you about this position.', 'estecapelli' ); ?>"></textarea>
							</div>

							<?php
							// The shared honeypot, signed stamp, timer and Turnstile widget.
							// Source must match what inc/careers.php checks against.
							estecapelli_lead_antispam_fields( 'careers' );
							?>
							<input type="hidden" name="job_id" value="<?php echo esc_attr( $job_id ); ?>" />
							<?php wp_nonce_field( 'estecapelli_apply', 'estecapelli_apply_nonce' ); ?>

							<button type="submit" class="btn btn-primary btn-lg job-apply__submit">
								<?php esc_html_e( 'Send application', 'estecapelli' ); ?>
								<?php estecapelli_icon( 'send', array( 'width' => 16, 'height' => 16 ) ); ?>
							</button>

							<p class="job-apply__privacy">
								<?php esc_html_e( 'Your CV is sent straight to our HR inbox and is never published on this website.', 'estecapelli' ); ?>
							</p>
						</form>
					<?php endif; ?>
				</section>
			<?php endif; ?>

		</div>
	</article>

	<?php
endwhile;

get_footer();
