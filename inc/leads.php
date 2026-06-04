<?php
/**
 * Leads — capture consultation requests from the contact page and the
 * footer quick-form, store them as a private `lead` CPT, and notify the
 * clinic by email (best-effort; real delivery needs SMTP on the server).
 *
 * Also routes the Contact page (slug "contact") to page-contact.php.
 *
 * @package Estecapelli
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Register the private `lead` post type so submissions are visible in
 * wp-admin → Leads.
 */
function estecapelli_register_lead_cpt() {
	register_post_type(
		'lead',
		array(
			'labels'            => array(
				'name'          => __( 'Leads', 'estecapelli' ),
				'singular_name' => __( 'Lead', 'estecapelli' ),
				'menu_name'     => __( 'Leads', 'estecapelli' ),
			),
			'public'            => false,
			'show_ui'           => true,
			'show_in_menu'      => true,
			'menu_icon'         => 'dashicons-email-alt',
			'menu_position'     => 26,
			'capability_type'   => 'post',
			'map_meta_cap'      => true,
			'supports'          => array( 'title' ),
			'has_archive'       => false,
			'rewrite'           => false,
			'exclude_from_search' => true,
		)
	);
}
add_action( 'init', 'estecapelli_register_lead_cpt' );

/**
 * Handle a submitted lead (contact page form or footer quick-form).
 * Both post to /en/contact with the `estecapelli_lead` nonce.
 */
function estecapelli_handle_lead() {

	if ( empty( $_POST['estecapelli_lead_nonce'] ) ) {
		return;
	}
	if ( ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['estecapelli_lead_nonce'] ) ), 'estecapelli_lead' ) ) {
		return;
	}

	$name = isset( $_POST['lead_name'] ) ? sanitize_text_field( wp_unslash( $_POST['lead_name'] ) ) : '';
	if ( '' === $name ) {
		return; // Name is the minimum required field.
	}

	$phone     = isset( $_POST['lead_phone'] ) ? sanitize_text_field( wp_unslash( $_POST['lead_phone'] ) ) : '';
	$email     = isset( $_POST['lead_email'] ) ? sanitize_email( wp_unslash( $_POST['lead_email'] ) ) : '';
	$treatment = isset( $_POST['lead_treatment'] ) ? sanitize_text_field( wp_unslash( $_POST['lead_treatment'] ) ) : '';
	$message   = isset( $_POST['lead_message'] ) ? sanitize_textarea_field( wp_unslash( $_POST['lead_message'] ) ) : '';
	$source    = isset( $_POST['lead_source'] ) ? sanitize_text_field( wp_unslash( $_POST['lead_source'] ) ) : 'contact';

	// Store the lead.
	$lead_id = wp_insert_post(
		array(
			'post_type'   => 'lead',
			'post_status' => 'private',
			'post_title'  => $name . ( $phone ? ' — ' . $phone : '' ),
		),
		true
	);

	if ( ! is_wp_error( $lead_id ) ) {
		update_post_meta( $lead_id, 'lead_phone', $phone );
		update_post_meta( $lead_id, 'lead_email', $email );
		update_post_meta( $lead_id, 'lead_treatment', $treatment );
		update_post_meta( $lead_id, 'lead_message', $message );
		update_post_meta( $lead_id, 'lead_source', $source );
	}

	// Notify the clinic (best-effort — needs SMTP configured to actually send).
	$to      = apply_filters( 'estecapelli_lead_email_to', get_option( 'admin_email' ) );
	$subject = sprintf( /* translators: %s: lead name */ __( 'New consultation request — %s', 'estecapelli' ), $name );
	$lines   = array(
		__( 'A new consultation request was submitted on the website.', 'estecapelli' ),
		'',
		sprintf( '%s: %s', __( 'Name', 'estecapelli' ), $name ),
		sprintf( '%s: %s', __( 'Phone', 'estecapelli' ), $phone ),
		sprintf( '%s: %s', __( 'Email', 'estecapelli' ), $email ),
		sprintf( '%s: %s', __( 'Interested in', 'estecapelli' ), $treatment ),
		sprintf( '%s: %s', __( 'Source', 'estecapelli' ), $source ),
		'',
		__( 'Message', 'estecapelli' ) . ':',
		$message,
	);
	wp_mail( $to, $subject, implode( "\n", $lines ) );

	// Post/Redirect/Get → land back on the contact page with a success flag.
	$redirect = add_query_arg( 'sent', '1', home_url( '/en/contact' ) ) . '#contact-form';
	wp_safe_redirect( $redirect );
	exit;
}
add_action( 'template_redirect', 'estecapelli_handle_lead', 5 );

/**
 * Route the Contact page to its dedicated template.
 */
function estecapelli_contact_template( $template ) {
	if ( is_page() && 'contact' === get_post_field( 'post_name', get_queried_object_id() ) ) {
		$contact = locate_template( 'page-contact.php' );
		if ( $contact ) {
			return $contact;
		}
	}
	return $template;
}
add_filter( 'template_include', 'estecapelli_contact_template' );

/**
 * Show stored lead details as admin columns for quick triage.
 */
add_filter( 'manage_lead_posts_columns', function ( $cols ) {
	$new = array( 'cb' => $cols['cb'] ?? '', 'title' => __( 'Name / Phone', 'estecapelli' ) );
	$new['lead_email']     = __( 'Email', 'estecapelli' );
	$new['lead_treatment'] = __( 'Interested in', 'estecapelli' );
	$new['lead_source']    = __( 'Source', 'estecapelli' );
	$new['date']           = __( 'Received', 'estecapelli' );
	return $new;
} );
add_action( 'manage_lead_posts_custom_column', function ( $col, $post_id ) {
	if ( in_array( $col, array( 'lead_email', 'lead_treatment', 'lead_source' ), true ) ) {
		echo esc_html( get_post_meta( $post_id, $col, true ) );
	}
}, 10, 2 );
