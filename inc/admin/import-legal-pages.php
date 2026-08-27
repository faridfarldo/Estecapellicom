<?php
/**
 * One-record-per-request importer for translated legal pages.
 *
 * The English legal pages remain the WPML sources. Translated post_content is
 * stored in version-controlled JSON files so imports are repeatable and do not
 * depend on WPML's Advanced Translation Editor.
 *
 * @package Estecapelli
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/** Public route language => display name and active WPML language code. */
function estecapelli_legal_page_languages() {
	$languages = array(
		'tr' => 'Turkish',
		'fr' => 'French',
		'it' => 'Italian',
		'es' => 'Spanish',
		'pl' => 'Polish',
		'pt' => 'Portuguese',
		'ro' => 'Romanian',
	);

	foreach ( $languages as $public_code => $name ) {
		$languages[ $public_code ] = array(
			'name'      => $name,
			'wpml_code' => estecapelli_wpml_language_code( $public_code ),
		);
	}

	return $languages;
}

/** Return the source seed after removing its unfinished editorial placeholder. */
function estecapelli_legal_page_source_seed( $source_slug ) {
	if ( ! function_exists( 'estecapelli_pages_seed' ) ) {
		return new WP_Error( 'legal_pages_seed_unavailable', 'The English page seed is unavailable.' );
	}

	foreach ( estecapelli_pages_seed() as $page ) {
		if ( $source_slug === ( $page['slug'] ?? '' ) ) {
			$content = (string) ( $page['content'] ?? '' );
			return str_replace( ' [insert your booking, deposit and cancellation policy here]', '', $content );
		}
	}

	return new WP_Error( 'legal_pages_missing_seed', sprintf( 'English legal-page seed not found: %s.', $source_slug ) );
}

/** Ordered open/close tag names, used to reject damaged translated HTML. */
function estecapelli_legal_page_tag_signature( $html ) {
	preg_match_all( '/<\/?([a-z][a-z0-9]*)\b[^>]*>/i', (string) $html, $matches );
	return array_map(
		static function ( $tag ) {
			preg_match( '/^<(\/?)\s*([a-z][a-z0-9]*)/i', $tag, $parts );
			return ( ! empty( $parts[1] ) ? '/' : '' ) . strtolower( $parts[2] ?? '' );
		},
		$matches[0]
	);
}

/** Load and strictly validate one version-controlled legal translation. */
function estecapelli_legal_page_translation( $public_language, $source_slug ) {
	$manifest = estecapelli_legal_pages_manifest();
	if ( ! isset( $manifest[ $public_language ][ $source_slug ] ) ) {
		return new WP_Error( 'legal_pages_unknown_record', 'Unknown legal-page translation.' );
	}

	$expected = $manifest[ $public_language ][ $source_slug ];
	$file     = get_template_directory() . '/inc/data/translations/' . $public_language . '/legal/' . $source_slug . '.json';
	if ( ! is_readable( $file ) ) {
		return new WP_Error( 'legal_pages_missing_file', sprintf( 'Missing legal translation file: %s/%s.', $public_language, basename( $file ) ) );
	}

	$translation = json_decode( (string) file_get_contents( $file ), true );
	if ( ! is_array( $translation ) ) {
		return new WP_Error( 'legal_pages_invalid_json', sprintf( 'Invalid JSON in %s/%s: %s', $public_language, basename( $file ), json_last_error_msg() ) );
	}
	if (
		$source_slug !== ( $translation['source_slug'] ?? '' ) ||
		$expected['slug'] !== ( $translation['slug'] ?? '' ) ||
		$expected['title'] !== ( $translation['title'] ?? '' ) ||
		empty( $translation['content'] )
	) {
		return new WP_Error( 'legal_pages_mismatched_file', sprintf( 'Incomplete or mismatched legal translation: %s/%s.', $public_language, basename( $file ) ) );
	}

	$content = (string) $translation['content'];
	$source  = estecapelli_legal_page_source_seed( $source_slug );
	if ( is_wp_error( $source ) ) {
		return $source;
	}
	if ( estecapelli_legal_page_tag_signature( $source ) !== estecapelli_legal_page_tag_signature( $content ) ) {
		return new WP_Error( 'legal_pages_html_mismatch', sprintf( 'Translated HTML structure does not match the English source: %s/%s.', $public_language, $source_slug ) );
	}
	if (
		false !== stripos( $content, '[insert your' ) ||
		false !== strpos( $content, '__ECLEGAL_' ) ||
		preg_match( '/\son[a-z]+\s*=|javascript\s*:/i', $content )
	) {
		return new WP_Error( 'legal_pages_unsafe_content', sprintf( 'Unresolved or unsafe content found in %s/%s.', $public_language, $source_slug ) );
	}
	if ( 'privacy-policy' === $source_slug ) {
		$required_links = array(
			'/' . $public_language . '/' . $manifest[ $public_language ]['kvkk-disclosure']['slug'],
			'/' . $public_language . '/' . $manifest[ $public_language ]['cookie-policy']['slug'],
		);
		foreach ( $required_links as $required_link ) {
			if ( false === strpos( $content, 'href="' . $required_link . '"' ) ) {
				return new WP_Error( 'legal_pages_link_mismatch', sprintf( 'Required localized legal link is missing: %s.', $required_link ) );
			}
		}
	}
	if ( preg_match( '~https?://(?:www\.)?estecapelli\.com/en/(?:privacy-policy|terms|cookie-policy|kvkk-[^"\s<]+)~i', $content ) ) {
		return new WP_Error( 'legal_pages_english_link', sprintf( 'An English legal link remains in %s/%s.', $public_language, $source_slug ) );
	}

	$translation['content'] = wp_kses_post( $content );
	return $translation;
}

/** Import or repair one translated legal page and its WPML relationship. */
function estecapelli_import_legal_page( $public_language, $source_slug ) {
	$languages = estecapelli_legal_page_languages();
	if ( ! isset( $languages[ $public_language ] ) ) {
		return new WP_Error( 'legal_pages_unknown_language', 'Unknown legal-page language.' );
	}
	if ( ! defined( 'ICL_SITEPRESS_VERSION' ) && ! defined( 'WPML_VERSION' ) ) {
		return new WP_Error( 'legal_pages_wpml_missing', 'WPML is required for the legal-page import.' );
	}

	$wpml_language    = $languages[ $public_language ]['wpml_code'];
	$language_name    = $languages[ $public_language ]['name'];
	$active_languages = apply_filters( 'wpml_active_languages', null, array( 'skip_missing' => 0 ) );
	if ( ! is_array( $active_languages ) || ! isset( $active_languages[ $wpml_language ] ) ) {
		return new WP_Error( 'legal_pages_language_inactive', sprintf( '%s must be active in WPML before importing.', $language_name ) );
	}

	$translation = estecapelli_legal_page_translation( $public_language, $source_slug );
	if ( is_wp_error( $translation ) ) {
		return $translation;
	}
	$source_id = estecapelli_source_post_id( $source_slug, 'page' );
	if ( ! $source_id ) {
		return new WP_Error( 'legal_pages_missing_source', sprintf( 'Published English page not found: %s. Import or publish the English page first.', $source_slug ) );
	}
	$source_post = get_post( $source_id );
	if ( ! $source_post ) {
		return new WP_Error( 'legal_pages_invalid_source', sprintf( 'English page could not be loaded: %s.', $source_slug ) );
	}

	$element_type    = apply_filters( 'wpml_element_type', 'page' );
	$source_details  = apply_filters(
		'wpml_element_language_details',
		null,
		array(
			'element_id'   => $source_id,
			'element_type' => 'page',
		)
	);
	$trid            = (int) estecapelli_it_hair_detail( $source_details, 'trid' );
	$source_language = (string) estecapelli_it_hair_detail( $source_details, 'language_code' );
	if ( ! $trid || 'en' !== $source_language ) {
		return new WP_Error( 'legal_pages_unlinked_source', sprintf( 'WPML English-source details are missing for %s.', $source_slug ) );
	}

	// Prefer the canonical localized slug, but never overwrite a page belonging
	// to another WPML translation group.
	$target_id = estecapelli_it_template_page_raw_target_id( $translation['slug'], $source_id, $wpml_language );
	if ( $target_id ) {
		$target_details = apply_filters(
			'wpml_element_language_details',
			null,
			array(
				'element_id'   => $target_id,
				'element_type' => 'page',
			)
		);
		$target_trid = (int) estecapelli_it_hair_detail( $target_details, 'trid' );
		if ( $target_trid && $target_trid !== $trid ) {
			return new WP_Error( 'legal_pages_slug_owned', sprintf( 'The required %s slug belongs to another WPML page: %s.', $language_name, $translation['slug'] ) );
		}
	}
	if ( ! $target_id ) {
		$target_id = estecapelli_wpml_group_element_id_raw( $trid, $element_type, $wpml_language );
	}
	if ( ! $target_id ) {
		$target_id = (int) apply_filters( 'wpml_object_id', $source_id, 'page', false, $wpml_language );
	}
	if ( $target_id === $source_id ) {
		$target_id = 0;
	}
	if ( $target_id ) {
		$raw_target = get_post( $target_id );
		if ( ! $raw_target || 'page' !== $raw_target->post_type || 'trash' === $raw_target->post_status || $target_id === $source_id ) {
			estecapelli_wpml_delete_relationship_raw( $target_id, $element_type, $trid, $wpml_language );
			$target_id = 0;
		}
	}
	if ( $target_id ) {
		delete_post_meta( $target_id, '_icl_lang_duplicate_of' );
	}

	$post_args = array(
		'post_type'    => 'page',
		'post_status'  => 'publish',
		'post_title'   => $translation['title'],
		'post_name'    => $translation['slug'],
		'post_content' => $translation['content'],
		'post_parent'  => 0,
		'menu_order'   => (int) $source_post->menu_order,
		'post_author'  => (int) $source_post->post_author,
	);
	if ( $target_id ) {
		$post_args['ID'] = $target_id;
		$target_id       = wp_update_post( $post_args, true );
	} else {
		$target_id = wp_insert_post( $post_args, true );
	}
	if ( is_wp_error( $target_id ) ) {
		return $target_id;
	}

	// Legal pages render their version-controlled post_content. Clear only the
	// repeater root so a stale ACF duplicate cannot hide that body in page.php.
	delete_post_meta( $target_id, 'page_sections' );
	delete_post_meta( $target_id, '_page_sections' );

	$page_template = get_post_meta( $source_id, '_wp_page_template', true );
	if ( '' !== $page_template ) {
		update_post_meta( $target_id, '_wp_page_template', $page_template );
	}

	do_action(
		'wpml_set_element_language_details',
		array(
			'element_id'           => (int) $target_id,
			'element_type'         => $element_type,
			'trid'                 => $trid,
			'language_code'        => $wpml_language,
			'source_language_code' => $source_language,
			'check_duplicates'     => false,
		)
	);
	delete_post_meta( $target_id, '_icl_lang_duplicate_of' );

	if ( ! estecapelli_wpml_replace_language_slot_raw( $target_id, $element_type, $trid, $wpml_language, $source_language ) ) {
		$reason = estecapelli_wpml_last_slot_error();
		return new WP_Error(
			'legal_pages_force_link_failed',
			sprintf( 'The %s WPML relationship could not be rebuilt for %s%s', $language_name, $source_slug, $reason ? ' — ' . $reason : '.' )
		);
	}

	$target_id = wp_update_post(
		array(
			'ID'           => (int) $target_id,
			'post_title'   => $translation['title'],
			'post_name'    => $translation['slug'],
			'post_content' => $translation['content'],
			'post_parent'  => 0,
		),
		true
	);
	if ( is_wp_error( $target_id ) ) {
		return $target_id;
	}
	$slug_result = estecapelli_force_multilingual_post_slug( $target_id, $translation['slug'], 'page', $wpml_language );
	if ( is_wp_error( $slug_result ) ) {
		return $slug_result;
	}

	$target_post = get_post( $target_id );
	if (
		! $target_post ||
		$translation['slug'] !== $target_post->post_name ||
		$translation['title'] !== $target_post->post_title ||
		trim( $translation['content'] ) !== trim( $target_post->post_content )
	) {
		return new WP_Error( 'legal_pages_verification_failed', sprintf( 'The imported %s page did not retain its required title, slug or content.', $language_name ) );
	}
	if ( ! estecapelli_wpml_element_matches_raw( $target_id, $element_type, $trid, $wpml_language ) ) {
		return new WP_Error( 'legal_pages_link_verification_failed', sprintf( 'WPML did not retain the %s link for %s.', $language_name, $source_slug ) );
	}

	$thumbnail_id = get_post_thumbnail_id( $source_id );
	if ( $thumbnail_id && ! get_post_thumbnail_id( $target_id ) ) {
		set_post_thumbnail( $target_id, $thumbnail_id );
	}
	// This install uses verbose page rewrite rules. Refresh them after a legal
	// page is created or renamed so normal WPML routing also knows the new slug.
	flush_rewrite_rules( false );

	return (int) $target_id;
}

add_action( 'admin_menu', 'estecapelli_register_legal_pages_importer' );
/** Register the dedicated legal-page importer under Tools. */
function estecapelli_register_legal_pages_importer() {
	add_management_page(
		__( 'Legal Pages Importer', 'estecapelli' ),
		__( 'Legal Pages', 'estecapelli' ),
		'manage_options',
		'estecapelli-legal-pages-importer',
		'estecapelli_render_legal_pages_importer'
	);
}

add_action( 'admin_post_estecapelli_import_legal_page', 'estecapelli_handle_legal_page_import' );
/** Process one language/page record and return to the importer. */
function estecapelli_handle_legal_page_import() {
	if ( ! current_user_can( 'manage_options' ) ) {
		wp_die( esc_html__( 'You are not allowed to import pages.', 'estecapelli' ) );
	}
	check_admin_referer( 'estecapelli_import_legal_page' );

	$record          = isset( $_POST['record'] ) ? sanitize_text_field( wp_unslash( $_POST['record'] ) ) : '';
	$parts           = explode( '|', $record, 2 );
	$public_language = isset( $parts[0] ) ? sanitize_key( $parts[0] ) : '';
	$source_slug     = isset( $parts[1] ) ? sanitize_title( $parts[1] ) : '';
	$result          = estecapelli_import_legal_page( $public_language, $source_slug );
	if ( is_wp_error( $result ) ) {
		set_transient( 'estecapelli_legal_pages_import_error', $public_language . '/' . $source_slug . ': ' . $result->get_error_message(), 5 * MINUTE_IN_SECONDS );
	} else {
		set_transient( 'estecapelli_legal_pages_import_success', $public_language . '/' . $source_slug, 5 * MINUTE_IN_SECONDS );
	}

	wp_safe_redirect( add_query_arg( 'page', 'estecapelli-legal-pages-importer', admin_url( 'tools.php' ) ) );
	exit;
}

/** Render the 24-record importer screen. */
function estecapelli_render_legal_pages_importer() {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}

	$languages = estecapelli_legal_page_languages();
	$manifest  = estecapelli_legal_pages_manifest();
	$success   = get_transient( 'estecapelli_legal_pages_import_success' );
	$error     = get_transient( 'estecapelli_legal_pages_import_error' );
	delete_transient( 'estecapelli_legal_pages_import_success' );
	delete_transient( 'estecapelli_legal_pages_import_error' );
	?>
	<div class="wrap">
		<h1><?php esc_html_e( 'Legal Pages Importer', 'estecapelli' ); ?></h1>
		<p><?php esc_html_e( 'Imports one translated legal page per request and repairs its WPML link to the published English source. Existing translated pages are updated in place.', 'estecapelli' ); ?></p>
		<p><strong><?php esc_html_e( 'URL note:', 'estecapelli' ); ?></strong> <?php esc_html_e( 'The live URL inventory contains no older localized URLs for these four pages, so the localized slugs below are the new stable routes.', 'estecapelli' ); ?></p>
		<div class="notice notice-warning inline"><p><strong><?php esc_html_e( 'Legal review required.', 'estecapelli' ); ?></strong> <?php esc_html_e( 'These translations follow the current English source, but qualified counsel should approve legal copy before production use.', 'estecapelli' ); ?></p></div>
		<?php if ( $success ) : ?>
			<div class="notice notice-success is-dismissible"><p><?php echo esc_html( sprintf( 'Legal page imported or repaired: %s.', $success ) ); ?></p></div>
		<?php elseif ( $error ) : ?>
			<div class="notice notice-error"><p><strong><?php esc_html_e( 'Legal page import could not finish.', 'estecapelli' ); ?></strong> <?php echo esc_html( $error ); ?></p></div>
		<?php endif; ?>

		<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
			<input type="hidden" name="action" value="estecapelli_import_legal_page">
			<?php wp_nonce_field( 'estecapelli_import_legal_page' ); ?>
			<table class="widefat striped" style="max-width:1180px;margin-top:1rem;">
				<thead><tr><th><?php esc_html_e( 'Language', 'estecapelli' ); ?></th><th><?php esc_html_e( 'English source', 'estecapelli' ); ?></th><th><?php esc_html_e( 'Localized route', 'estecapelli' ); ?></th><th><?php esc_html_e( 'Status', 'estecapelli' ); ?></th><th><?php esc_html_e( 'Action', 'estecapelli' ); ?></th></tr></thead>
				<tbody>
					<?php foreach ( $manifest as $public_language => $pages ) : ?>
						<?php foreach ( $pages as $source_slug => $translation ) :
							$source_id = estecapelli_source_post_id( $source_slug, 'page' );
							$wpml_code = $languages[ $public_language ]['wpml_code'];
							$linked_id = $source_id ? (int) apply_filters( 'wpml_object_id', $source_id, 'page', false, $wpml_code ) : 0;
							$exists    = $linked_id && $linked_id !== $source_id;
							?>
							<tr>
								<td><?php echo esc_html( $languages[ $public_language ]['name'] ); ?> <code><?php echo esc_html( $wpml_code ); ?></code></td>
								<td><code><?php echo esc_html( $source_slug ); ?></code></td>
								<td><code><?php echo esc_html( '/' . $public_language . '/' . $translation['slug'] ); ?></code></td>
								<td>
									<?php if ( ! $source_id ) : ?>
										<span style="color:#b26200;"><?php esc_html_e( 'Published English source missing', 'estecapelli' ); ?></span>
									<?php elseif ( $exists ) : ?>
										<span style="color:#0d8551;"><?php esc_html_e( 'Exists', 'estecapelli' ); ?></span> — <a href="<?php echo esc_url( get_edit_post_link( $linked_id ) ); ?>"><?php esc_html_e( 'edit', 'estecapelli' ); ?></a>
									<?php else : ?>
										<span style="color:#777;"><?php esc_html_e( 'Not yet imported', 'estecapelli' ); ?></span>
									<?php endif; ?>
								</td>
								<td><button type="submit" name="record" value="<?php echo esc_attr( $public_language . '|' . $source_slug ); ?>" class="button button-primary"><?php esc_html_e( 'Import / Repair', 'estecapelli' ); ?></button></td>
							</tr>
						<?php endforeach; ?>
					<?php endforeach; ?>
				</tbody>
			</table>
		</form>
	</div>
	<?php
}
