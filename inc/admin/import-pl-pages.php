<?php
/**
 * Polish importer for the requested non-article pages.
 *
 * Imports one page per request to keep ACFML below the server execution limit.
 * The English seed owns structure and media; Polish overlays replace all
 * visitor-facing copy and the importer repairs the exact WPML relationship.
 *
 * @package Estecapelli
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/** English page slug => exact live Polish leaf slug. */
function estecapelli_pl_pages_manifest() {
	return array(
		'hair-transplant'             => 'przeszczep-wlosow',
		'tricholab'                   => 'tricholab',
		'pre-hair-transplant-period'  => 'okres-przed-przeszczepem-wlosow',
		'post-hair-transplant-period' => 'okres-po-przeszczepie-wlosow',
		'plastic-surgery'             => 'chirurgia-plastyczna',
		'dental-treatment'            => 'leczenie-stomatologiczne',
		'about-us'                    => 'o-nas',
		'our-team'                    => 'nasz-zespol',
		'our-doctors'                 => 'nasi-lekarze',
		'before-after'                => 'przed-po',
		'contact'                     => 'kontakt',
	);
}

/** Template-rendered Polish pages without ACF page_sections. */
function estecapelli_pl_template_pages_manifest() {
	return array(
		'blog' => array(
			'slug'  => 'blog',
			'title' => 'Blog',
		),
	);
}

/** Return the canonical English route key for a regular page. */
function estecapelli_pl_pages_route_key( $source_slug ) {
	if ( in_array( $source_slug, array( 'tricholab', 'pre-hair-transplant-period', 'post-hair-transplant-period' ), true ) ) {
		return '/en/hair-transplant/' . $source_slug;
	}
	if ( in_array( $source_slug, array( 'our-team', 'our-doctors' ), true ) ) {
		return '/en/about-us/' . $source_slug;
	}

	return '/en/' . $source_slug;
}

/** Load and strictly validate all Polish page overlays. */
function estecapelli_pl_pages_load_translations() {
	$directory = get_template_directory() . '/inc/data/translations/pl/pages';
	$loaded    = array();

	foreach ( estecapelli_pl_pages_manifest() as $source_slug => $polish_slug ) {
		$route = estecapelli_indexed_route_path( estecapelli_pl_pages_route_key( $source_slug ), 'pl' );
		if ( ! $route || basename( $route ) !== $polish_slug ) {
			return new WP_Error( 'pl_pages_indexed_slug_mismatch', sprintf( 'The Polish slug does not match the indexed URL contract: %s.', $source_slug ) );
		}

		$file = $directory . '/' . $source_slug . '.json';
		if ( ! is_readable( $file ) ) {
			return new WP_Error( 'pl_pages_missing_file', sprintf( 'Missing Polish page translation: %s', basename( $file ) ) );
		}
		$translation = json_decode( (string) file_get_contents( $file ), true );
		if ( ! is_array( $translation ) ) {
			return new WP_Error( 'pl_pages_invalid_json', sprintf( 'Invalid JSON in %s: %s', basename( $file ), json_last_error_msg() ) );
		}
		if (
			$source_slug !== ( $translation['source_slug'] ?? '' ) ||
			$polish_slug !== ( $translation['slug'] ?? '' ) ||
			empty( $translation['title'] ) ||
			empty( $translation['sections'] ) ||
			! is_array( $translation['sections'] )
		) {
			return new WP_Error( 'pl_pages_invalid_translation', sprintf( 'Incomplete or mismatched Polish page translation: %s', basename( $file ) ) );
		}

		$seed = estecapelli_it_hair_pages_source_seed( $source_slug );
		if ( is_wp_error( $seed ) ) {
			return $seed;
		}
		$coverage = estecapelli_it_hair_validate_coverage( $seed['sections'], $translation['sections'], 'page_sections', 'Polish' );
		if ( is_wp_error( $coverage ) ) {
			return new WP_Error( $coverage->get_error_code(), sprintf( '%s: %s', basename( $file ), $coverage->get_error_message() ) );
		}
		$structure = estecapelli_it_hair_overlay( $seed['sections'], $translation['sections'] );
		if ( is_wp_error( $structure ) ) {
			return new WP_Error( $structure->get_error_code(), sprintf( '%s: %s', basename( $file ), $structure->get_error_message() ) );
		}

		$loaded[ $source_slug ] = $translation;
	}

	return $loaded;
}

/**
 * Find the Polish page already owning a slug, bypassing WPML query filtering.
 * Shared slugs such as `blog` are selected by their raw WPML language details.
 */
function estecapelli_pl_page_raw_target_id( $slug, $source_id ) {
	global $wpdb;
	$ids = $wpdb->get_col(
		$wpdb->prepare(
			"SELECT ID FROM {$wpdb->posts}
			 WHERE post_name = %s AND post_type = 'page' AND post_status <> 'trash' AND ID <> %d
			 ORDER BY ID ASC",
			$slug,
			(int) $source_id
		)
	);

	$unassigned = array();
	foreach ( $ids as $id ) {
		$details = apply_filters(
			'wpml_element_language_details',
			null,
			array(
				'element_id'   => (int) $id,
				'element_type' => 'page',
			)
		);
		$language = (string) estecapelli_it_hair_detail( $details, 'language_code' );
		if ( 'pl' === $language ) {
			return (int) $id;
		}
		if ( '' === $language ) {
			$unassigned[] = (int) $id;
		}
	}

	return 1 === count( $unassigned ) ? (int) reset( $unassigned ) : 0;
}

/** Import or repair one validated Polish page. */
function estecapelli_pl_page_import_one( $source_slug, array $translation, $with_sections ) {
	$seed = null;
	if ( $with_sections ) {
		$seed = estecapelli_it_hair_pages_source_seed( $source_slug );
		if ( is_wp_error( $seed ) ) {
			return $seed;
		}
	}

	$source_id = estecapelli_source_post_id( $source_slug, 'page' );
	if ( ! $source_id ) {
		return new WP_Error( 'pl_pages_missing_source_post', sprintf( 'Published English page not found: %s.', $source_slug ) );
	}
	$source_post = get_post( $source_id );
	if ( ! $source_post ) {
		return new WP_Error( 'pl_pages_invalid_source_post', sprintf( 'English page could not be loaded: %s.', $source_slug ) );
	}

	$element_type   = apply_filters( 'wpml_element_type', 'page' );
	$source_details = apply_filters(
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
		return new WP_Error( 'pl_pages_unlinked_source_post', sprintf( 'WPML language details are missing for %s.', $source_slug ) );
	}

	$target_id = estecapelli_pl_page_raw_target_id( $translation['slug'], $source_id );
	if ( ! $target_id ) {
		$target_id = estecapelli_wpml_group_element_id_raw( $trid, $element_type, 'pl' );
	}
	if ( ! $target_id ) {
		$target_id = (int) apply_filters( 'wpml_object_id', $source_id, 'page', false, 'pl' );
	}
	if ( $target_id === $source_id ) {
		$target_id = 0;
	}
	if ( $target_id ) {
		$raw_target = get_post( $target_id );
		if ( ! $raw_target || 'page' !== $raw_target->post_type || 'trash' === $raw_target->post_status ) {
			estecapelli_wpml_delete_relationship_raw( $target_id, $element_type, $trid, 'pl' );
			$target_id = 0;
		}
	}

	$polish_parent = 0;
	if ( (int) $source_post->post_parent ) {
		$linked_parent = (int) apply_filters( 'wpml_object_id', (int) $source_post->post_parent, 'page', false, 'pl' );
		if ( $linked_parent && $linked_parent !== (int) $source_post->post_parent ) {
			$polish_parent = $linked_parent;
		} else {
			return new WP_Error( 'pl_pages_missing_parent', sprintf( 'Import the Polish parent page before importing %s.', $source_slug ) );
		}
	}

	if ( $target_id ) {
		delete_post_meta( $target_id, '_icl_lang_duplicate_of' );
	}
	$post_args = array(
		'post_type'    => 'page',
		'post_title'   => $translation['title'],
		'post_name'    => $translation['slug'],
		'post_status'  => 'publish',
		'post_content' => '',
		'post_parent'  => $polish_parent,
		'menu_order'   => (int) $source_post->menu_order,
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

	do_action(
		'wpml_set_element_language_details',
		array(
			'element_id'           => (int) $target_id,
			'element_type'         => $element_type,
			'trid'                 => $trid,
			'language_code'        => 'pl',
			'source_language_code' => $source_language,
			'check_duplicates'     => false,
		)
	);
	delete_post_meta( $target_id, '_icl_lang_duplicate_of' );

	if ( ! estecapelli_wpml_replace_language_slot_raw( $target_id, $element_type, $trid, 'pl', $source_language ) ) {
		$reason = estecapelli_wpml_last_slot_error();
		return new WP_Error(
			'pl_pages_force_link_failed',
			sprintf( 'The Polish WPML relationship could not be rebuilt for %s%s', $source_slug, $reason ? ' — ' . $reason : '.' )
		);
	}

	$target_id = wp_update_post(
		array(
			'ID'          => (int) $target_id,
			'post_title'  => $translation['title'],
			'post_name'   => $translation['slug'],
			'post_parent' => $polish_parent,
		),
		true
	);
	if ( is_wp_error( $target_id ) ) {
		return $target_id;
	}
	$target_post = get_post( $target_id );
	if ( ! $target_post || $translation['slug'] !== $target_post->post_name ) {
		return new WP_Error( 'pl_pages_slug_conflict', sprintf( 'The required Polish page slug is already in use: %s.', $translation['slug'] ) );
	}

	if ( $with_sections ) {
		$sections = estecapelli_merge_preserve_media( $seed['sections'], $source_id );
		$sections = estecapelli_it_hair_overlay( $sections, $translation['sections'] );
		if ( is_wp_error( $sections ) ) {
			return $sections;
		}
		$sections = estecapelli_it_hair_localize_urls( $sections, 'pl' );
		$sections = estecapelli_it_hair_normalize_media( $sections );
		update_field( 'page_sections', $sections, $target_id );

		$saved_sections = get_field( 'page_sections', $target_id );
		$saved_title    = is_array( $saved_sections ) ? ( $saved_sections[0]['title'] ?? '' ) : '';
		$expected_title = $translation['sections'][0]['title'] ?? '';
		if ( ! is_array( $saved_sections ) || ! $expected_title || $expected_title !== $saved_title ) {
			return new WP_Error( 'pl_pages_acf_not_saved', sprintf( 'The Polish ACF content was not saved for %s.', $source_slug ) );
		}
		$media_saved = estecapelli_it_hair_validate_media( $sections, $saved_sections );
		if ( is_wp_error( $media_saved ) ) {
			return $media_saved;
		}
	}

	$thumbnail_id = get_post_thumbnail_id( $source_id );
	if ( $thumbnail_id && ! get_post_thumbnail_id( $target_id ) ) {
		set_post_thumbnail( $target_id, $thumbnail_id );
	}

	return (int) $target_id;
}

/** Import or repair one requested Polish page. */
function estecapelli_run_pl_page_import( $source_slug ) {
	$source_slug = (string) $source_slug;
	if ( ! function_exists( 'update_field' ) ) {
		return new WP_Error( 'pl_pages_acf_missing', 'ACF is required for the Polish page import.' );
	}
	if ( ! defined( 'ICL_SITEPRESS_VERSION' ) && ! defined( 'WPML_VERSION' ) ) {
		return new WP_Error( 'pl_pages_wpml_missing', 'WPML is required for the Polish page import.' );
	}
	$active_languages = apply_filters( 'wpml_active_languages', null, array( 'skip_missing' => 0 ) );
	if ( ! is_array( $active_languages ) || ! isset( $active_languages['pl'] ) ) {
		return new WP_Error( 'pl_pages_polish_inactive', 'Polish must be active in WPML before importing the page translations.' );
	}

	if ( isset( estecapelli_pl_pages_manifest()[ $source_slug ] ) ) {
		$translations = estecapelli_pl_pages_load_translations();
		if ( is_wp_error( $translations ) ) {
			return $translations;
		}
		return estecapelli_pl_page_import_one( $source_slug, $translations[ $source_slug ], true );
	}

	$template_pages = estecapelli_pl_template_pages_manifest();
	if ( isset( $template_pages[ $source_slug ] ) ) {
		$route = estecapelli_indexed_route_path( '/en/' . $source_slug, 'pl' );
		if ( ! $route || basename( $route ) !== $template_pages[ $source_slug ]['slug'] ) {
			return new WP_Error( 'pl_template_pages_indexed_slug_mismatch', sprintf( 'The Polish page slug does not match the indexed URL contract: %s.', $source_slug ) );
		}
		return estecapelli_pl_page_import_one( $source_slug, $template_pages[ $source_slug ], false );
	}

	return new WP_Error( 'pl_pages_invalid_source', sprintf( 'Unknown Polish page source: %s.', $source_slug ) );
}

add_action( 'admin_menu', 'estecapelli_register_pl_content_importer' );
/** Register a dedicated, low-risk one-page-at-a-time importer. */
function estecapelli_register_pl_content_importer() {
	add_management_page(
		__( 'Polish Content Importer', 'estecapelli' ),
		__( 'Polish Content Importer', 'estecapelli' ),
		'manage_options',
		'estecapelli-polish-content-importer',
		'estecapelli_render_pl_content_importer'
	);
}

add_action( 'admin_post_estecapelli_import_pl_content', 'estecapelli_handle_pl_content_import' );
/** Process one Polish page or dental-treatment import. */
function estecapelli_handle_pl_content_import() {
	if ( ! current_user_can( 'manage_options' ) ) {
		wp_die( esc_html__( 'You are not allowed to import content.', 'estecapelli' ) );
	}
	check_admin_referer( 'estecapelli_import_pl_content' );

	$raw_target  = isset( $_POST['estecapelli_pl_content_target'] ) ? sanitize_text_field( wp_unslash( $_POST['estecapelli_pl_content_target'] ) ) : '';
	$target      = explode( ':', $raw_target, 2 );
	$content     = $target[0] ?? '';
	$source_slug = sanitize_title( $target[1] ?? '' );

	if ( 'page' === $content ) {
		$result = estecapelli_run_pl_page_import( $source_slug );
	} elseif ( 'dental' === $content && function_exists( 'estecapelli_run_pl_dental_import' ) ) {
		$result = estecapelli_run_pl_dental_import( $source_slug );
	} else {
		$result = new WP_Error( 'pl_content_invalid_target', 'Unknown Polish import target.' );
	}

	if ( is_wp_error( $result ) ) {
		set_transient( 'estecapelli_pl_content_import_error', $source_slug . ': ' . $result->get_error_message(), 5 * MINUTE_IN_SECONDS );
	} else {
		set_transient( 'estecapelli_pl_content_import_success', $source_slug, 5 * MINUTE_IN_SECONDS );
	}

	wp_safe_redirect( add_query_arg( 'page', 'estecapelli-polish-content-importer', admin_url( 'tools.php' ) ) );
	exit;
}

/** Render one importer row with its live slug and current WPML status. */
function estecapelli_render_pl_content_row( $content, $source_slug, $polish_slug, $post_type ) {
	$source_id = estecapelli_source_post_id( $source_slug, $post_type );
	$polish_id = $source_id ? (int) apply_filters( 'wpml_object_id', $source_id, $post_type, false, 'pl' ) : 0;
	if ( $polish_id === $source_id ) {
		$polish_id = 0;
	}
	?>
	<tr>
		<td><code><?php echo esc_html( $source_slug ); ?></code></td>
		<td><code><?php echo esc_html( $polish_slug ); ?></code></td>
		<td>
			<?php if ( $polish_id ) : ?>
				<span style="color:#0d8551;"><?php esc_html_e( 'Exists', 'estecapelli' ); ?></span>
				— <a href="<?php echo esc_url( get_edit_post_link( $polish_id ) ); ?>"><?php esc_html_e( 'edit', 'estecapelli' ); ?></a>
				| <a href="<?php echo esc_url( get_permalink( $polish_id ) ); ?>" target="_blank"><?php esc_html_e( 'view', 'estecapelli' ); ?></a>
			<?php else : ?>
				<span style="color:#888;"><?php esc_html_e( 'Not yet imported', 'estecapelli' ); ?></span>
			<?php endif; ?>
		</td>
		<td>
			<button type="submit" name="estecapelli_pl_content_target" value="<?php echo esc_attr( $content . ':' . $source_slug ); ?>" class="button button-primary">
				<?php esc_html_e( 'Import / Repair', 'estecapelli' ); ?>
			</button>
		</td>
	</tr>
	<?php
}

/** Render the Polish content importer screen. */
function estecapelli_render_pl_content_importer() {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}

	$success = get_transient( 'estecapelli_pl_content_import_success' );
	$error   = get_transient( 'estecapelli_pl_content_import_error' );
	if ( false !== $success ) {
		delete_transient( 'estecapelli_pl_content_import_success' );
	}
	if ( false !== $error ) {
		delete_transient( 'estecapelli_pl_content_import_error' );
	}
	?>
	<div class="wrap">
		<h1><?php esc_html_e( 'Polish Content Importer', 'estecapelli' ); ?></h1>
		<p><?php esc_html_e( 'Imports one Polish page per request to avoid the ACFML execution-time limit. Every action is idempotent and safe to run again.', 'estecapelli' ); ?></p>
		<?php if ( false !== $success ) : ?>
			<div class="notice notice-success is-dismissible"><p><?php echo esc_html( sprintf( 'Polish content imported or repaired: %s.', $success ) ); ?></p></div>
		<?php elseif ( false !== $error ) : ?>
			<div class="notice notice-error"><p><strong><?php esc_html_e( 'Polish import could not finish.', 'estecapelli' ); ?></strong> <?php echo esc_html( $error ); ?></p></div>
		<?php endif; ?>

		<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
			<input type="hidden" name="action" value="estecapelli_import_pl_content">
			<?php wp_nonce_field( 'estecapelli_import_pl_content' ); ?>

			<h2><?php esc_html_e( 'Polish pages', 'estecapelli' ); ?></h2>
			<table class="widefat striped" style="max-width:980px;margin-top:1rem;">
				<thead><tr><th><?php esc_html_e( 'English source', 'estecapelli' ); ?></th><th><?php esc_html_e( 'Polish slug', 'estecapelli' ); ?></th><th><?php esc_html_e( 'Status', 'estecapelli' ); ?></th><th><?php esc_html_e( 'Action', 'estecapelli' ); ?></th></tr></thead>
				<tbody>
					<?php foreach ( estecapelli_pl_pages_manifest() as $source_slug => $polish_slug ) : ?>
						<?php estecapelli_render_pl_content_row( 'page', $source_slug, $polish_slug, 'page' ); ?>
					<?php endforeach; ?>
					<?php foreach ( estecapelli_pl_template_pages_manifest() as $source_slug => $translation ) : ?>
						<?php estecapelli_render_pl_content_row( 'page', $source_slug, $translation['slug'], 'page' ); ?>
					<?php endforeach; ?>
				</tbody>
			</table>

			<h2 style="margin-top:2.5rem;"><?php esc_html_e( 'Polish Dental Treatment procedures', 'estecapelli' ); ?></h2>
			<table class="widefat striped" style="max-width:980px;margin-top:1rem;">
				<thead><tr><th><?php esc_html_e( 'English source', 'estecapelli' ); ?></th><th><?php esc_html_e( 'Polish slug', 'estecapelli' ); ?></th><th><?php esc_html_e( 'Status', 'estecapelli' ); ?></th><th><?php esc_html_e( 'Action', 'estecapelli' ); ?></th></tr></thead>
				<tbody>
					<?php foreach ( estecapelli_pl_dental_manifest() as $source_slug => $polish_slug ) : ?>
						<?php estecapelli_render_pl_content_row( 'dental', $source_slug, $polish_slug, 'treatment' ); ?>
					<?php endforeach; ?>
				</tbody>
			</table>
		</form>
	</div>
	<?php
}

/* ---- Auto-import: refresh all Polish content once per version (no per-row clicking). ---- */
if ( ! defined( 'ESTECAPELLI_PL_AUTORUN_VERSION' ) ) {
	define( 'ESTECAPELLI_PL_AUTORUN_VERSION', '2026-07-24.1' );
}
add_action( 'admin_init', 'estecapelli_maybe_autorun_pl_content', 95 );
function estecapelli_maybe_autorun_pl_content() {
	if ( ! function_exists( 'estecapelli_autorun_language_import' ) ) {
		return;
	}
	estecapelli_autorun_language_import(
		'estecapelli_pl_autorun_version',
		ESTECAPELLI_PL_AUTORUN_VERSION,
		'estecapelli_pl_autorun_items',
		'estecapelli_pl_autorun_import_one'
	);
}
function estecapelli_pl_autorun_items() {
	$items = array();
	foreach ( array_keys( estecapelli_pl_pages_manifest() ) as $slug ) {
		$items[] = array( 'kind' => 'page', 'slug' => $slug );
	}
	if ( function_exists( 'estecapelli_pl_doctors_manifest' ) ) {
		foreach ( array_keys( estecapelli_pl_doctors_manifest() ) as $slug ) {
			$items[] = array( 'kind' => 'doctor', 'slug' => $slug );
		}
	}
	return $items;
}
function estecapelli_pl_autorun_import_one( $kind, $slug ) {
	if ( 'doctor' === $kind && function_exists( 'estecapelli_run_pl_doctors_import' ) ) {
		return estecapelli_run_pl_doctors_import( $slug );
	}
	return estecapelli_run_pl_page_import( $slug );
}
