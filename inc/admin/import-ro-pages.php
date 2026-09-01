<?php
/**
 * One-record-per-request importer for remaining Romanian pages and doctors.
 *
 * English remains authoritative for ACF structure and media. Romanian JSON
 * overlays replace visitor-facing copy and every slug is verified against the
 * indexed/live URL contract before a database write is attempted.
 *
 * @package Estecapelli
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/** Romanian is stored in WPML under the same "ro" code its public routes use. */
function estecapelli_ro_pages_wpml_language() {
	return estecapelli_wpml_language_code( 'ro' );
}

/** English page slug => exact indexed Romanian leaf slug, in parent-first order. */
function estecapelli_ro_pages_manifest() {
	return array(
		'hair-transplant'             => 'transplant-de-par',
		'tricholab'                   => 'tricholab',
		'pre-hair-transplant-period'  => 'perioada-dinainte-de-transplantul-de-par',
		'post-hair-transplant-period' => 'perioada-de-dupa-transplantul-de-par',
		'plastic-surgery'             => 'chirurgie-plastica',
		'dental-treatment'            => 'tratament-dentar',
		'about-us'                    => 'despre-noi',
		'our-team'                    => 'echipa-noastra',
		'our-doctors'                 => 'medicii-nostri',
		'before-after'                => 'inainte-dupa',
		'contact'                     => 'contact',
	);
}

/** The doctor profiles present in the indexed Romanian contract. */
function estecapelli_ro_doctors_manifest() {
	return array(
		'mehmet-hanifi-kutlar'  => 'mehmet-hanifi-kutlar',
		'prof-dr-binnur-ustun'  => 'prof-dr-binnur-ustun',
		'op-dr-hasan-celik'     => 'op-dr-hasan-celik',
		'op-dr-mehmet-palali'   => 'op-dr-mehmet-palali',
		'op-dr-necdet-derici'   => 'op-dr-necdet-derici',
	);
}

/** Indexed English route key for one regular page. */
function estecapelli_ro_page_route_key( $source_slug ) {
	if ( in_array( $source_slug, array( 'tricholab', 'pre-hair-transplant-period', 'post-hair-transplant-period' ), true ) ) {
		return '/en/hair-transplant/' . $source_slug;
	}
	if ( in_array( $source_slug, array( 'our-team', 'our-doctors' ), true ) ) {
		return '/en/about-us/' . $source_slug;
	}

	return '/en/' . $source_slug;
}

/** Indexed English route key for one doctor profile. */
function estecapelli_ro_doctor_route_key( $source_slug ) {
	$parent = 'our-doctors';
	return '/en/about-us/' . $parent . '/' . $source_slug;
}

/** Load and strictly validate all Romanian page overlays. */
function estecapelli_ro_pages_load_translations() {
	$directory = get_template_directory() . '/inc/data/translations/ro/pages';
	$loaded    = array();

	foreach ( estecapelli_ro_pages_manifest() as $source_slug => $romanian_slug ) {
		$route = estecapelli_indexed_route_path( estecapelli_ro_page_route_key( $source_slug ), 'ro' );
		if ( ! $route || basename( $route ) !== $romanian_slug ) {
			return new WP_Error( 'ro_pages_indexed_slug_mismatch', sprintf( 'The Romanian slug does not match the indexed URL contract: %s.', $source_slug ) );
		}

		$file = $directory . '/' . $source_slug . '.json';
		if ( ! is_readable( $file ) ) {
			return new WP_Error( 'ro_pages_missing_file', sprintf( 'Missing Romanian page translation: %s.', basename( $file ) ) );
		}
		$translation = json_decode( (string) file_get_contents( $file ), true );
		if ( ! is_array( $translation ) ) {
			return new WP_Error( 'ro_pages_invalid_json', sprintf( 'Invalid JSON in %s: %s', basename( $file ), json_last_error_msg() ) );
		}
		if (
			$source_slug !== ( $translation['source_slug'] ?? '' ) ||
			$romanian_slug !== ( $translation['slug'] ?? '' ) ||
			empty( $translation['title'] ) ||
			empty( $translation['sections'] ) ||
			! is_array( $translation['sections'] )
		) {
			return new WP_Error( 'ro_pages_invalid_translation', sprintf( 'Incomplete or mismatched Romanian page translation: %s.', basename( $file ) ) );
		}

		$seed = estecapelli_it_hair_pages_source_seed( $source_slug );
		if ( is_wp_error( $seed ) ) {
			return $seed;
		}
		$coverage = estecapelli_it_hair_validate_coverage( $seed['sections'], $translation['sections'], 'page_sections', 'Romanian' );
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

/** Load and validate all indexed Romanian doctor overlays. */
function estecapelli_ro_doctors_load_translations() {
	$directory = get_template_directory() . '/inc/data/translations/ro/doctors';
	$loaded    = array();

	foreach ( estecapelli_ro_doctors_manifest() as $source_slug => $romanian_slug ) {
		$route = estecapelli_indexed_route_path( estecapelli_ro_doctor_route_key( $source_slug ), 'ro' );
		if ( ! $route || basename( $route ) !== $romanian_slug ) {
			return new WP_Error( 'ro_doctors_indexed_slug_mismatch', sprintf( 'The Romanian doctor slug does not match the indexed URL contract: %s.', $source_slug ) );
		}

		$file = $directory . '/' . $source_slug . '.json';
		if ( ! is_readable( $file ) ) {
			return new WP_Error( 'ro_doctors_missing_file', sprintf( 'Missing Romanian doctor translation: %s.', basename( $file ) ) );
		}
		$translation = json_decode( (string) file_get_contents( $file ), true );
		if ( ! is_array( $translation ) ) {
			return new WP_Error( 'ro_doctors_invalid_json', sprintf( 'Invalid JSON in %s: %s', basename( $file ), json_last_error_msg() ) );
		}
		if (
			$source_slug !== ( $translation['source_slug'] ?? '' ) ||
			$romanian_slug !== ( $translation['slug'] ?? '' ) ||
			empty( $translation['name'] ) ||
			empty( $translation['position'] ) ||
			empty( $translation['bio'] ) ||
			empty( $translation['credentials'] ) ||
			! is_array( $translation['credentials'] )
		) {
			return new WP_Error( 'ro_doctors_invalid_translation', sprintf( 'Incomplete or mismatched Romanian doctor translation: %s.', basename( $file ) ) );
		}

		$seed = estecapelli_it_doctor_source_seed( $source_slug );
		if ( is_wp_error( $seed ) ) {
			return $seed;
		}
		if ( count( $translation['credentials'] ) !== count( $seed['credentials'] ?? array() ) ) {
			return new WP_Error( 'ro_doctors_credentials_mismatch', sprintf( 'Credential count does not match the English source: %s.', $source_slug ) );
		}

		$loaded[ $source_slug ] = $translation;
	}

	return $loaded;
}

/** Confirm the Romanian translation of a hierarchical page's parent exists. */
function estecapelli_ro_page_parent_ready( $source_slug ) {
	$source_id = estecapelli_source_post_id( $source_slug, 'page' );
	$post      = $source_id ? get_post( $source_id ) : null;
	if ( ! $post || ! (int) $post->post_parent ) {
		return true;
	}

	$target_parent = (int) apply_filters( 'wpml_object_id', (int) $post->post_parent, 'page', false, estecapelli_ro_pages_wpml_language() );
	return $target_parent && $target_parent !== (int) $post->post_parent;
}

/** Whether an English page has a distinct Romanian WPML translation. */
function estecapelli_ro_page_translation_ready( $source_slug ) {
	$source_id = estecapelli_source_post_id( $source_slug, 'page' );
	$target_id = $source_id ? (int) apply_filters( 'wpml_object_id', $source_id, 'page', false, estecapelli_ro_pages_wpml_language() ) : 0;
	return $target_id && $target_id !== $source_id;
}

/** Import or repair exactly one Romanian page or doctor. */
function estecapelli_run_ro_content_import( $kind, $source_slug ) {
	$kind        = (string) $kind;
	$source_slug = (string) $source_slug;
	if ( ! function_exists( 'update_field' ) ) {
		return new WP_Error( 'ro_content_acf_missing', 'ACF is required for the Romanian content import.' );
	}
	if ( ! defined( 'ICL_SITEPRESS_VERSION' ) && ! defined( 'WPML_VERSION' ) ) {
		return new WP_Error( 'ro_content_wpml_missing', 'WPML is required for the Romanian content import.' );
	}
	$wpml_language = estecapelli_ro_pages_wpml_language();
	$languages     = apply_filters( 'wpml_active_languages', null, array( 'skip_missing' => 0 ) );
	if ( ! is_array( $languages ) || ! isset( $languages[ $wpml_language ] ) ) {
		return new WP_Error( 'ro_content_romanian_inactive', sprintf( 'Romanian (%s) must be active in WPML before importing content.', $wpml_language ) );
	}

	if ( 'page' === $kind ) {
		if ( ! isset( estecapelli_ro_pages_manifest()[ $source_slug ] ) ) {
			return new WP_Error( 'ro_content_unknown_page', sprintf( 'Unknown Romanian page source: %s.', $source_slug ) );
		}
		if ( ! estecapelli_ro_page_parent_ready( $source_slug ) ) {
			return new WP_Error( 'ro_content_parent_missing', sprintf( 'Import the Romanian parent page before importing %s.', $source_slug ) );
		}
		$translations = estecapelli_ro_pages_load_translations();
		if ( is_wp_error( $translations ) ) {
			return $translations;
		}
		return estecapelli_it_hair_pages_import_one( $translations[ $source_slug ], $wpml_language, 'Romanian' );
	}

	if ( 'doctor' === $kind ) {
		if ( ! isset( estecapelli_ro_doctors_manifest()[ $source_slug ] ) ) {
			return new WP_Error( 'ro_content_unknown_doctor', sprintf( 'Unknown Romanian doctor source: %s.', $source_slug ) );
		}
		$parent_source = 'our-doctors';
		if ( ! estecapelli_ro_page_translation_ready( $parent_source ) ) {
			return new WP_Error( 'ro_content_doctor_parent_missing', sprintf( 'Import the Romanian %s page before its doctor profiles.', $parent_source ) );
		}
		$translations = estecapelli_ro_doctors_load_translations();
		if ( is_wp_error( $translations ) ) {
			return $translations;
		}
		return estecapelli_it_doctor_import_one( $translations[ $source_slug ], $wpml_language, 'Romanian' );
	}

	return new WP_Error( 'ro_content_unknown_kind', 'Unknown Romanian content type.' );
}

add_action( 'admin_menu', 'estecapelli_register_ro_pages_importer' );
/** Register the dedicated importer under Tools. */
function estecapelli_register_ro_pages_importer() {
	add_management_page(
		__( 'Romanian Pages Importer', 'estecapelli' ),
		__( 'Romanian Pages', 'estecapelli' ),
		'manage_options',
		'estecapelli-romanian-pages-importer',
		'estecapelli_render_ro_pages_importer'
	);
}

add_action( 'admin_post_estecapelli_import_ro_content', 'estecapelli_handle_ro_content_import' );
/** Process one record and return to the importer screen. */
function estecapelli_handle_ro_content_import() {
	if ( ! current_user_can( 'manage_options' ) ) {
		wp_die( esc_html__( 'You are not allowed to import content.', 'estecapelli' ) );
	}
	check_admin_referer( 'estecapelli_import_ro_content' );

	$kind        = isset( $_POST['kind'] ) ? sanitize_key( wp_unslash( $_POST['kind'] ) ) : '';
	$source_slug = isset( $_POST['source'] ) ? sanitize_title( wp_unslash( $_POST['source'] ) ) : '';
	$result      = estecapelli_run_ro_content_import( $kind, $source_slug );
	if ( is_wp_error( $result ) ) {
		set_transient( 'estecapelli_ro_content_import_error', $source_slug . ': ' . $result->get_error_message(), 5 * MINUTE_IN_SECONDS );
	} else {
		set_transient( 'estecapelli_ro_content_import_success', $source_slug, 5 * MINUTE_IN_SECONDS );
	}

	wp_safe_redirect( add_query_arg( 'page', 'estecapelli-romanian-pages-importer', admin_url( 'tools.php' ) ) );
	exit;
}

/** Return the linked or safely adoptable Romanian target for an importer row. */
function estecapelli_ro_content_target_id( $kind, $source_slug, $target_slug ) {
	$post_type = 'doctor' === $kind ? 'doctor' : 'page';
	$source_id = estecapelli_source_post_id( $source_slug, $post_type );
	if ( ! $source_id ) {
		return array( 0, 0 );
	}

	$wpml_language = estecapelli_ro_pages_wpml_language();
	$linked_id     = (int) apply_filters( 'wpml_object_id', $source_id, $post_type, false, $wpml_language );
	if ( $linked_id === $source_id ) {
		$linked_id = 0;
	}
	$candidate = 'doctor' === $kind
		? estecapelli_it_doctor_raw_target_id( $target_slug, $source_id, $wpml_language )
		: estecapelli_it_hair_page_raw_post_id( $target_slug, $source_id, $wpml_language );

	return array( $linked_id, $candidate );
}

/** Render one import table for a record manifest. */
function estecapelli_render_ro_content_table( $heading, $kind, array $manifest ) {
	?>
	<h2><?php echo esc_html( $heading ); ?></h2>
	<table class="widefat striped" style="max-width:1100px;margin:0 0 2rem;">
		<thead><tr><th><?php esc_html_e( 'English source', 'estecapelli' ); ?></th><th><?php esc_html_e( 'Romanian slug', 'estecapelli' ); ?></th><th><?php esc_html_e( 'Status', 'estecapelli' ); ?></th><th><?php esc_html_e( 'Action', 'estecapelli' ); ?></th></tr></thead>
		<tbody>
			<?php foreach ( $manifest as $source_slug => $target ) :
				$target_slug = is_array( $target ) ? (string) $target['slug'] : (string) $target;
				list( $linked_id, $candidate ) = estecapelli_ro_content_target_id( $kind, $source_slug, $target_slug );
				$target_id = $linked_id ?: $candidate;
				?>
				<tr>
					<td><code><?php echo esc_html( $source_slug ); ?></code></td>
					<td><code><?php echo esc_html( $target_slug ); ?></code></td>
					<td>
						<?php if ( $linked_id ) : ?>
							<span style="color:#0d8551;"><?php esc_html_e( 'Exists', 'estecapelli' ); ?></span>
						<?php elseif ( $candidate ) : ?>
							<span style="color:#b26200;"><?php esc_html_e( 'Exists - link needs repair', 'estecapelli' ); ?></span>
						<?php else : ?>
							<span style="color:#888;"><?php esc_html_e( 'Not yet imported', 'estecapelli' ); ?></span>
						<?php endif; ?>
						<?php if ( $target_id ) : ?>
							— <a href="<?php echo esc_url( get_edit_post_link( $target_id ) ); ?>"><?php esc_html_e( 'edit', 'estecapelli' ); ?></a>
						<?php endif; ?>
					</td>
					<td>
						<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
							<input type="hidden" name="action" value="estecapelli_import_ro_content">
							<input type="hidden" name="kind" value="<?php echo esc_attr( $kind ); ?>">
							<input type="hidden" name="source" value="<?php echo esc_attr( $source_slug ); ?>">
							<?php wp_nonce_field( 'estecapelli_import_ro_content' ); ?>
							<button type="submit" class="button button-primary"><?php esc_html_e( 'Import / Repair', 'estecapelli' ); ?></button>
						</form>
					</td>
				</tr>
			<?php endforeach; ?>
		</tbody>
	</table>
	<?php
}

/** Render the one-record-per-request Romanian content importer. */
function estecapelli_render_ro_pages_importer() {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}

	$success = get_transient( 'estecapelli_ro_content_import_success' );
	$error   = get_transient( 'estecapelli_ro_content_import_error' );
	delete_transient( 'estecapelli_ro_content_import_success' );
	delete_transient( 'estecapelli_ro_content_import_error' );
	?>
	<div class="wrap">
		<h1><?php esc_html_e( 'Romanian Pages Importer', 'estecapelli' ); ?></h1>
		<p><?php esc_html_e( 'Import or repair one record per request. Use the parent-first order shown below. All slugs are verified against the live URL contract; the Romanian homepage remains at /ro/ with no /home suffix.', 'estecapelli' ); ?></p>
		<p><strong><?php esc_html_e( 'Homepage:', 'estecapelli' ); ?></strong> <code>/ro/</code> — <?php esc_html_e( 'template text is translated automatically and does not need an import.', 'estecapelli' ); ?></p>
		<?php if ( $success ) : ?>
			<div class="notice notice-success is-dismissible"><p><?php echo esc_html( sprintf( 'Romanian content imported or repaired: %s.', $success ) ); ?></p></div>
		<?php elseif ( $error ) : ?>
			<div class="notice notice-error"><p><strong><?php esc_html_e( 'Romanian import could not finish.', 'estecapelli' ); ?></strong> <?php echo esc_html( $error ); ?></p></div>
		<?php endif; ?>

		<?php
		estecapelli_render_ro_content_table( 'Pages with translated ACF content', 'page', estecapelli_ro_pages_manifest() );
		estecapelli_render_ro_content_table( 'Indexed doctor profiles', 'doctor', estecapelli_ro_doctors_manifest() );
		?>
	</div>
	<?php
}

/* ---- Auto-import: refresh all ro content once per version (no per-row clicking). ---- */
if ( ! defined( 'ESTECAPELLI_RO_AUTORUN_VERSION' ) ) {
	define( 'ESTECAPELLI_RO_AUTORUN_VERSION', '2026-08-27.1' );
}
add_action( 'admin_init', 'estecapelli_maybe_autorun_ro_content', 95 );
function estecapelli_maybe_autorun_ro_content() {
	if ( ! function_exists( 'estecapelli_autorun_language_import' ) ) {
		return;
	}
	estecapelli_autorun_language_import(
		'estecapelli_ro_autorun_version',
		ESTECAPELLI_RO_AUTORUN_VERSION,
		'estecapelli_ro_autorun_items',
		'estecapelli_run_ro_content_import'
	);
}
function estecapelli_ro_autorun_items() {
	$items = array();
	foreach ( array_keys( estecapelli_ro_pages_manifest() ) as $slug ) {
		$items[] = array( 'kind' => 'page', 'slug' => $slug );
	}
	foreach ( array_keys( estecapelli_ro_doctors_manifest() ) as $slug ) {
		$items[] = array( 'kind' => 'doctor', 'slug' => $slug );
	}
	return $items;
}
