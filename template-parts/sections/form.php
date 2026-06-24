<?php
/**
 * Section: Lead Form — copy + a 4-field capture form (name, phone, email, note).
 *
 * Posts to the lead handler in inc/leads.php (shared with the contact page and
 * footer quick-form). `lead_return` keeps the visitor on this page and shows an
 * inline success message via ?sent=1.
 *
 * @package Estecapelli
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$section = get_query_var( 'section' );
if ( ! is_array( $section ) ) {
	return;
}

$eyebrow      = $section['eyebrow']       ?? '';
$title        = $section['title']         ?? '';
$lead         = $section['lead']          ?? '';
$points       = $section['points']        ?? array();
$submit_label = $section['submit_label']  ?? '';
$show_wa      = ! empty( $section['show_whatsapp'] );

if ( ! $title ) {
	return;
}

$submit_label = $submit_label ?: __( 'Request a Free Consultation', 'estecapelli' );
$return_url   = get_permalink();
$sent         = isset( $_GET['sent'] ) && '1' === $_GET['sent']; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
?>

<section class="t-form" id="lead-form" aria-labelledby="t-form-<?php echo esc_attr( sanitize_title( $title ) ); ?>">
	<div class="shell t-form__shell">

		<div class="t-form__copy">
			<?php if ( $eyebrow ) : ?>
				<span class="t-form__eyebrow">
					<span class="t-form__eyebrow-mark" aria-hidden="true"></span>
					<?php echo esc_html( $eyebrow ); ?>
				</span>
			<?php endif; ?>

			<h2 id="t-form-<?php echo esc_attr( sanitize_title( $title ) ); ?>" class="t-form__title">
				<?php echo esc_html( $title ); ?>
			</h2>

			<?php if ( $lead ) : ?>
				<p class="t-form__lead"><?php echo esc_html( $lead ); ?></p>
			<?php endif; ?>

			<?php if ( ! empty( $points ) && is_array( $points ) ) : ?>
				<ul class="t-form__points">
					<?php foreach ( $points as $point ) : ?>
						<?php if ( empty( $point['label'] ) ) { continue; } ?>
						<li class="t-form__point">
							<?php if ( ! empty( $point['icon'] ) ) : ?>
								<?php estecapelli_icon( $point['icon'], array( 'width' => 20, 'height' => 20, 'class' => 't-form__point-icon' ) ); ?>
							<?php else : ?>
								<?php estecapelli_icon( 'check-circle', array( 'width' => 20, 'height' => 20, 'class' => 't-form__point-icon' ) ); ?>
							<?php endif; ?>
							<span><?php echo esc_html( $point['label'] ); ?></span>
						</li>
					<?php endforeach; ?>
				</ul>
			<?php endif; ?>
		</div>

		<div class="t-form__card contact-form-card">
			<?php if ( $sent ) : ?>
				<div class="contact-alert" role="status">
					<?php estecapelli_icon( 'check-circle', array( 'width' => 20, 'height' => 20 ) ); ?>
					<span><?php esc_html_e( 'Thank you! Your request has been received — our team will contact you shortly.', 'estecapelli' ); ?></span>
				</div>
			<?php endif; ?>

			<form class="contact-form" method="post" action="<?php echo esc_url( $return_url ); ?>">
				<div class="contact-form__field">
					<label for="lf-name"><?php esc_html_e( 'Full name', 'estecapelli' ); ?> <span aria-hidden="true">*</span></label>
					<input id="lf-name" type="text" name="lead_name" required autocomplete="name" placeholder="<?php esc_attr_e( 'Name and surname', 'estecapelli' ); ?>" />
				</div>
				<div class="contact-form__field">
					<label for="lf-phone"><?php esc_html_e( 'Phone', 'estecapelli' ); ?> <span aria-hidden="true">*</span></label>
					<input id="lf-phone" class="js-intl-phone" type="tel" name="lead_phone" required autocomplete="tel" inputmode="tel" placeholder="<?php esc_attr_e( 'Phone number', 'estecapelli' ); ?>" />
				</div>
				<div class="contact-form__field">
					<label for="lf-email"><?php esc_html_e( 'Email', 'estecapelli' ); ?></label>
					<input id="lf-email" type="email" name="lead_email" autocomplete="email" placeholder="<?php esc_attr_e( 'you@example.com', 'estecapelli' ); ?>" />
				</div>
				<div class="contact-form__field">
					<label for="lf-note"><?php esc_html_e( 'Note', 'estecapelli' ); ?></label>
					<textarea id="lf-note" name="lead_message" rows="4" placeholder="<?php esc_attr_e( 'Tell us about your goals, or any questions you have…', 'estecapelli' ); ?>"></textarea>
				</div>

				<?php estecapelli_lead_tracking_fields( 'section' ); ?>
				<input type="hidden" name="lead_return" value="<?php echo esc_url( $return_url ); ?>" />
				<?php wp_nonce_field( 'estecapelli_lead', 'estecapelli_lead_nonce' ); ?>

				<div class="contact-form__actions">
					<button type="submit" class="btn btn-primary btn-lg">
						<?php echo esc_html( $submit_label ); ?>
						<?php estecapelli_icon( 'arrow-right', array( 'width' => 18, 'height' => 18 ) ); ?>
					</button>
					<?php if ( $show_wa ) : ?>
						<a class="btn btn-ghost contact-form__wa" href="<?php echo esc_url( estecapelli_whatsapp_url() ); ?>" target="_blank" rel="noopener">
							<?php estecapelli_icon( 'whatsapp', array( 'width' => 18, 'height' => 18 ) ); ?>
							<?php esc_html_e( 'Or chat on WhatsApp', 'estecapelli' ); ?>
						</a>
					<?php endif; ?>
				</div>
			</form>
		</div>

	</div>
</section>
