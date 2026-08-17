<?php
/**
 * WhatsApp hand-off notice.
 *
 * Replaces the old fake-chat mockup. Clicking the floating WhatsApp button (or
 * anything marked [data-wa-chat]) opens a small card just above the button that
 * shows the opening line WhatsApp will arrive prefilled with, and asks the
 * visitor to keep it and write underneath. Then it hands off to the real
 * wa.me link.
 *
 * Nothing is sent or stored here — the card is a courtesy note, not a chat.
 *
 * @package Estecapelli
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$wa_intro = estecapelli_whatsapp_intro_message();
$wa_href  = estecapelli_whatsapp_url();
?>
<div class="wa-notice" id="waNotice" role="dialog" aria-modal="false" aria-labelledby="wa-notice-title" hidden>
	<div class="wa-notice__card">

		<div class="wa-notice__head">
			<span class="wa-notice__mark" aria-hidden="true">
				<?php estecapelli_icon( 'whatsapp', array( 'width' => 20, 'height' => 20 ) ); ?>
			</span>
			<span class="wa-notice__heading">
				<strong id="wa-notice-title"><?php esc_html_e( 'Chat on WhatsApp', 'estecapelli' ); ?></strong>
				<span><?php esc_html_e( 'Reply in 2 minutes', 'estecapelli' ); ?></span>
			</span>
			<button type="button" class="wa-notice__close" data-wa-close aria-label="<?php esc_attr_e( 'Close', 'estecapelli' ); ?>">
				<?php estecapelli_icon( 'close', array( 'width' => 18, 'height' => 18 ) ); ?>
			</button>
		</div>

		<p class="wa-notice__text"><?php esc_html_e( 'Please leave the message already prepared in WhatsApp exactly as it is — it tells our team which page you are writing from, so we can reply to you faster. You are very welcome to add your question right after it.', 'estecapelli' ); ?></p>

		<div class="wa-notice__preview">
			<span class="wa-notice__bubble"><?php echo esc_html( $wa_intro ); ?></span>
		</div>

		<a class="wa-notice__go" data-wa-go href="<?php echo esc_url( $wa_href ); ?>" target="_blank" rel="noopener">
			<?php estecapelli_icon( 'whatsapp', array( 'width' => 18, 'height' => 18 ) ); ?>
			<?php esc_html_e( 'Continue to WhatsApp', 'estecapelli' ); ?>
		</a>

	</div>
</div>
