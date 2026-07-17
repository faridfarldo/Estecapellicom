<?php
/**
 * Keep WPML's Advanced Translation Editor disabled for site content.
 *
 * Translations on this site are written directly to WordPress and ACF by the
 * version-controlled importers. WPML's Translation Editor uses a separate job
 * store, so allowing ATE to save those posts can overwrite imported content.
 *
 * ATE is bundled with WPML Core and cannot be uninstalled independently. This
 * guard provides the site-level equivalent: every supported post is locked to
 * the native WordPress editor, ATE entry points are removed from wp-admin, and
 * direct requests to the ATE job screens are blocked.
 *
 * @package Estecapelli
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Post types whose translations are managed outside WPML's editors.
 *
 * @return string[]
 */
function estecapelli_wpml_native_editor_post_types() {
	return array( 'post', 'page', 'treatment', 'doctor' );
}

/**
 * Required WPML metadata for the native WordPress translation editor.
 *
 * @return array<string,string>
 */
function estecapelli_wpml_native_editor_meta() {
	return array(
		'_wpml_post_translation_editor_native' => 'yes',
		'_last_translation_edit_mode'          => 'native-editor',
	);
}

/**
 * Whether a post belongs to a content type protected by this guard.
 *
 * @param int $post_id Post ID.
 * @return bool
 */
function estecapelli_wpml_uses_native_editor( $post_id ) {
	$post_type = get_post_type( $post_id );

	return $post_type && in_array( $post_type, estecapelli_wpml_native_editor_post_types(), true );
}

/**
 * Lock one post to the native WordPress editor.
 *
 * This runs before WPML's normal save handlers so newly imported content is
 * marked as native-editor content before WPML can create an ATE job for it.
 *
 * @param int          $post_id Post ID.
 * @param WP_Post|null $post    Post object when called by save_post.
 * @return void
 */
function estecapelli_wpml_force_native_editor( $post_id, $post = null ) {
	if ( wp_is_post_revision( $post_id ) || wp_is_post_autosave( $post_id ) ) {
		return;
	}

	if ( $post instanceof WP_Post && ! in_array( $post->post_type, estecapelli_wpml_native_editor_post_types(), true ) ) {
		return;
	}

	if ( ! estecapelli_wpml_uses_native_editor( $post_id ) ) {
		return;
	}

	foreach ( estecapelli_wpml_native_editor_meta() as $meta_key => $meta_value ) {
		if ( $meta_value !== get_post_meta( $post_id, $meta_key, true ) ) {
			update_post_meta( $post_id, $meta_key, $meta_value );
		}
	}
}
add_action( 'save_post', 'estecapelli_wpml_force_native_editor', 0, 2 );

/**
 * Prevent WPML or an editor from changing the native-editor lock.
 *
 * Returning true short-circuits the metadata operation as successful while
 * preserving the canonical value already stored by the guard.
 *
 * @param mixed  $check      Existing short-circuit result.
 * @param int    $object_id  Post ID.
 * @param string $meta_key   Metadata key.
 * @param mixed  $meta_value Proposed metadata value.
 * @return mixed
 */
function estecapelli_wpml_lock_native_editor_meta( $check, $object_id, $meta_key, $meta_value ) {
	if ( null !== $check || ! estecapelli_wpml_uses_native_editor( $object_id ) ) {
		return $check;
	}

	$locked = estecapelli_wpml_native_editor_meta();
	if ( isset( $locked[ $meta_key ] ) && $locked[ $meta_key ] !== $meta_value ) {
		return true;
	}

	return $check;
}
add_filter( 'add_post_metadata', 'estecapelli_wpml_lock_native_editor_meta', PHP_INT_MAX, 4 );
add_filter( 'update_post_metadata', 'estecapelli_wpml_lock_native_editor_meta', PHP_INT_MAX, 4 );

/**
 * Prevent removal of the native-editor lock from protected posts.
 *
 * @param mixed  $delete     Existing short-circuit result.
 * @param int    $object_id  Post ID.
 * @param string $meta_key   Metadata key.
 * @return mixed
 */
function estecapelli_wpml_keep_native_editor_meta( $delete, $object_id, $meta_key ) {
	if ( null !== $delete || ! estecapelli_wpml_uses_native_editor( $object_id ) ) {
		return $delete;
	}

	if ( array_key_exists( $meta_key, estecapelli_wpml_native_editor_meta() ) ) {
		return true;
	}

	return $delete;
}
add_filter( 'delete_post_metadata', 'estecapelli_wpml_keep_native_editor_meta', PHP_INT_MAX, 3 );

/**
 * Apply the native-editor lock to content that existed before this guard.
 *
 * The version option makes this a one-time migration. New posts are handled by
 * save_post, so there is no recurring full-site query.
 *
 * @return void
 */
function estecapelli_wpml_migrate_existing_content_to_native_editor() {
	if ( '1' === get_option( 'estecapelli_wpml_native_editor_migration', '' ) ) {
		return;
	}

	$post_ids = get_posts(
		array(
			'post_type'              => estecapelli_wpml_native_editor_post_types(),
			'post_status'            => 'any',
			'posts_per_page'         => -1,
			'fields'                 => 'ids',
			'orderby'                => 'ID',
			'order'                  => 'ASC',
			'suppress_filters'       => true,
			'no_found_rows'          => true,
			'update_post_meta_cache' => false,
			'update_post_term_cache' => false,
		)
	);

	foreach ( $post_ids as $post_id ) {
		estecapelli_wpml_force_native_editor( (int) $post_id );
	}

	update_option( 'estecapelli_wpml_native_editor_migration', '1', false );
}
add_action( 'admin_init', 'estecapelli_wpml_migrate_existing_content_to_native_editor', 1 );

/**
 * WPML admin pages that create or open Translation Editor jobs.
 *
 * @return string[]
 */
function estecapelli_wpml_blocked_translation_pages() {
	return array(
		'wpml-translation-management/menu/main.php',
		'wpml-translation-management/menu/translations-queue.php',
		'tm/menu/main.php',
		'tm/menu/translations-queue.php',
	);
}

/**
 * Remove Translation Dashboard and Translations from the WPML menu.
 *
 * String Translation, taxonomy translation, menu sync, language settings and
 * all normal WPML routing features remain available.
 *
 * @return void
 */
function estecapelli_wpml_remove_translation_editor_menus() {
	$parent = 'sitepress-multilingual-cms/menu/languages.php';

	foreach ( estecapelli_wpml_blocked_translation_pages() as $page ) {
		remove_submenu_page( $parent, $page );
	}
}
add_action( 'admin_menu', 'estecapelli_wpml_remove_translation_editor_menus', PHP_INT_MAX );

/**
 * Block bookmarked or stale links to WPML Translation Editor jobs.
 *
 * @return void
 */
function estecapelli_wpml_block_translation_editor_requests() {
	if ( ! isset( $_GET['page'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		return;
	}

	$page = sanitize_text_field( wp_unslash( $_GET['page'] ) ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended
	if ( ! in_array( $page, estecapelli_wpml_blocked_translation_pages(), true ) ) {
		return;
	}

	wp_safe_redirect(
		add_query_arg(
			'estecapelli_ate_disabled',
			'1',
			admin_url( 'edit.php?post_type=page' )
		)
	);
	exit;
}
add_action( 'admin_init', 'estecapelli_wpml_block_translation_editor_requests', PHP_INT_MAX );

/**
 * Explain redirects from stale ATE links.
 *
 * @return void
 */
function estecapelli_wpml_native_editor_notice() {
	if ( ! isset( $_GET['estecapelli_ate_disabled'] ) || '1' !== $_GET['estecapelli_ate_disabled'] ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		return;
	}
	?>
	<div class="notice notice-info is-dismissible">
		<p><?php esc_html_e( 'WPML Advanced Translation Editor is disabled on this site. Edit each language directly with the native WordPress editor.', 'estecapelli' ); ?></p>
	</div>
	<?php
}
add_action( 'admin_notices', 'estecapelli_wpml_native_editor_notice' );

/**
 * Hide any ATE switch or stale ATE link injected into post-editing screens.
 *
 * WPML does not currently expose a supported hook for removing this switch.
 * The metadata lock above is the enforcement layer; this small UI cleanup only
 * prevents editors from being offered a control that the site will reject.
 *
 * @return void
 */
function estecapelli_wpml_hide_translation_editor_controls() {
	$screen = get_current_screen();
	if ( ! $screen || ! in_array( $screen->base, array( 'post', 'post-new' ), true ) ) {
		return;
	}
	?>
	<style id="estecapelli-disable-wpml-ate">
		a[href*="wpml-translation-management/menu/translations-queue.php"],
		a[href*="ate.wpml.org"] {
			display: none !important;
		}
	</style>
	<script>
		(function () {
			'use strict';

			var labels = [
				'advanced translation editor',
				'wpml translation editor',
				'use wpml translation editor',
				"use wpml's translation editor"
			];

			function hideAteControls() {
				document.querySelectorAll('button, a, [role="button"], [role="tab"], label').forEach(function (element) {
					var text = (element.textContent || '').trim().replace(/\s+/g, ' ').toLowerCase();
					var href = element.getAttribute('href') || '';
					var isAteLabel = labels.indexOf(text) !== -1;
					var isAteLink = href.indexOf('wpml-translation-management/menu/translations-queue.php') !== -1 || href.indexOf('ate.wpml.org') !== -1;

					if (isAteLabel || isAteLink) {
						element.hidden = true;
						element.setAttribute('aria-hidden', 'true');
						element.setAttribute('tabindex', '-1');
					}
				});
			}

			hideAteControls();
			new MutationObserver(hideAteControls).observe(document.body, { childList: true, subtree: true });
		}());
	</script>
	<?php
}
add_action( 'admin_footer', 'estecapelli_wpml_hide_translation_editor_controls', PHP_INT_MAX );
