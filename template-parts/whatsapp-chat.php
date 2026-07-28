<?php
/**
 * Fake WhatsApp chat popup.
 *
 * Ported from the Chile landing page (`.wp-chat-*`). Clicking the floating
 * WhatsApp button opens a WhatsApp-looking chat window instead of jumping
 * straight to wa.me: the visitor types a message, confirms it, and only then is
 * handed off to the real WhatsApp with that message prefilled. The extra step
 * measurably raises the share of people who arrive at WhatsApp with something
 * to say rather than an empty thread.
 *
 * Nothing here is a real conversation — no message is sent or stored by us.
 *
 * @package Estecapelli
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// The site logo doubles as the chat avatar; fall back to the WhatsApp mark.
$wa_logo_id  = (int) get_theme_mod( 'custom_logo' );
$wa_logo_url = $wa_logo_id ? wp_get_attachment_image_url( $wa_logo_id, 'thumbnail' ) : '';
$wa_peer     = get_bloginfo( 'name' );
?>
<div class="wp-chat-overlay" id="wpChat" role="dialog" aria-modal="true" aria-label="<?php esc_attr_e( 'WhatsApp chat', 'estecapelli' ); ?>" hidden>
	<div class="wp-chat-window">

		<div class="wp-chat-header">
			<button type="button" class="wp-chat-back" id="wpChatClose" aria-label="<?php esc_attr_e( 'Close chat', 'estecapelli' ); ?>">
				<?php estecapelli_icon( 'arrow-left', array( 'width' => 20, 'height' => 20 ) ); ?>
			</button>

			<?php if ( $wa_logo_url ) : ?>
				<img class="wp-chat-avatar" src="<?php echo esc_url( $wa_logo_url ); ?>" alt="" width="40" height="40" decoding="async" />
			<?php else : ?>
				<span class="wp-chat-avatar wp-chat-avatar--fallback">
					<?php estecapelli_icon( 'whatsapp', array( 'width' => 22, 'height' => 22 ) ); ?>
				</span>
			<?php endif; ?>

			<span class="wp-chat-peer">
				<strong><?php echo esc_html( $wa_peer ); ?></strong>
				<span class="wp-chat-status">
					<span class="wp-chat-dot" aria-hidden="true"></span>
					<?php esc_html_e( 'online', 'estecapelli' ); ?>
				</span>
			</span>

			<span class="wp-chat-wa" aria-hidden="true">
				<?php estecapelli_icon( 'whatsapp', array( 'width' => 22, 'height' => 22 ) ); ?>
			</span>
		</div>

		<div class="wp-chat-body" id="wpChatBody">
			<span class="wp-chat-date"><?php esc_html_e( 'Today', 'estecapelli' ); ?></span>
			<div class="wp-msg wp-msg-in">
				<div class="wp-bubble">
					<?php esc_html_e( 'Hi! 👋 Welcome to Estecapelli. Tell us which treatment you are interested in and we will get back to you within minutes.', 'estecapelli' ); ?>
					<span class="wp-msg-time" data-wp-now></span>
				</div>
			</div>
		</div>

		<form class="wp-chat-composer" id="wpChatComposer" autocomplete="off" action="javascript:void(0);">
			<input
				type="text"
				class="wp-chat-input"
				id="wpChatInput"
				placeholder="<?php esc_attr_e( 'Type a message', 'estecapelli' ); ?>"
				data-empty-hint="<?php esc_attr_e( 'Write your message first', 'estecapelli' ); ?>"
				aria-label="<?php esc_attr_e( 'Type a message', 'estecapelli' ); ?>"
			/>
			<button type="submit" class="wp-chat-send" aria-label="<?php esc_attr_e( 'Send', 'estecapelli' ); ?>">
				<?php estecapelli_icon( 'send', array( 'width' => 18, 'height' => 18 ) ); ?>
			</button>
		</form>

		<!-- Confirmation step, layered over the chat. -->
		<div class="wp-confirm" id="wpConfirm">
			<div class="wp-confirm-box">
				<h4><?php esc_html_e( 'Confirm your message', 'estecapelli' ); ?></h4>
				<p><?php esc_html_e( 'This message will be sent to our WhatsApp:', 'estecapelli' ); ?></p>
				<div class="wp-confirm-preview" id="wpConfirmPreview"></div>
				<div class="wp-confirm-actions">
					<button type="button" class="wp-confirm-cancel" id="wpConfirmCancel"><?php esc_html_e( 'Cancel', 'estecapelli' ); ?></button>
					<button type="button" class="wp-confirm-send" id="wpConfirmSend">
						<?php estecapelli_icon( 'whatsapp', array( 'width' => 16, 'height' => 16 ) ); ?>
						<?php esc_html_e( 'Send', 'estecapelli' ); ?>
					</button>
				</div>
			</div>
		</div>

	</div>
</div>
