<?php
/**
 * One-record-per-request importer for remaining Turkish pages and doctors.
 *
 * English remains authoritative for ACF structure and media. Turkish JSON
 * overlays replace visitor-facing copy and every slug is verified against the
 * indexed/live URL contract before a database write is attempted.
 *
 * @package Estecapelli
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/** English page slug => exact indexed Turkish leaf slug, in parent-first order. */
function estecapelli_tr_pages_manifest() {
	return array(
		'hair-transplant'             => 'sac-ekimi',
		'tricholab'                   => 'tricholab',
		'pre-hair-transplant-period'  => 'sac-ekim-oncesi-donem',
		'post-hair-transplant-period' => 'sac-ekimi-sonrasi-donem',
		'plastic-surgery'             => 'estetik-cerrahi',
		'dental-treatment'            => 'dis-tedavisi',
		'about-us'                    => 'hakkimizda',
		'our-team'                    => 'ekibimiz',
		'our-doctors'                 => 'doktorlarimiz',
		'medical-director'            => 'tibbi-direktor',
		'before-after'                => 'oncesi-sonrasi',
		'contact'                     => 'iletisim',
	);
}

/** The sectionless Blog landing page; individual articles remain excluded. */
function estecapelli_tr_template_pages_manifest() {
	return array(
		'blog' => array(
			'slug'  => 'blog',
			'title' => 'Blog',
		),
	);
}

/** Doctor profiles that are present in the live Turkish URL inventory. */
function estecapelli_tr_doctors_manifest() {
	return array(
		'prof-dr-binnur-ustun' => 'prof-dr-binnur-ustun',
		'mehmet-hanifi-kutlar' => 'mehmet-hanifi-kutlar',
		'op-dr-hasan-celik'    => 'op-dr-hasan-celik',
		'op-dr-mehmet-palali'  => 'op-dr-mehmet-palali',
		'op-dr-necdet-derici'  => 'op-dr-necdet-derici',
	);
}

/** Indexed English route key for one regular page. */
function estecapelli_tr_page_route_key( $source_slug ) {
	if ( in_array( $source_slug, array( 'tricholab', 'pre-hair-transplant-period', 'post-hair-transplant-period' ), true ) ) {
		return '/en/hair-transplant/' . $source_slug;
	}
	if ( in_array( $source_slug, array( 'our-team', 'our-doctors', 'medical-director' ), true ) ) {
		return '/en/about-us/' . $source_slug;
	}

	return '/en/' . $source_slug;
}

/** Indexed English route key for one doctor profile. */
function estecapelli_tr_doctor_route_key( $source_slug ) {
	$parent = 'mehmet-hanifi-kutlar' === $source_slug ? 'medical-director' : 'our-doctors';
	return '/en/about-us/' . $parent . '/' . $source_slug;
}

/** Load and strictly validate all Turkish page overlays. */
function estecapelli_tr_pages_load_translations() {
	$directory = get_template_directory() . '/inc/data/translations/tr/pages';
	$loaded    = array();

	foreach ( estecapelli_tr_pages_manifest() as $source_slug => $turkish_slug ) {
		$route = estecapelli_indexed_route_path( estecapelli_tr_page_route_key( $source_slug ), 'tr' );
		if ( ! $route || basename( $route ) !== $turkish_slug ) {
			return new WP_Error( 'tr_pages_indexed_slug_mismatch', sprintf( 'The Turkish slug does not match the indexed URL contract: %s.', $source_slug ) );
		}

		$file = $directory . '/' . $source_slug . '.json';
		if ( ! is_readable( $file ) ) {
			return new WP_Error( 'tr_pages_missing_file', sprintf( 'Missing Turkish page translation: %s.', basename( $file ) ) );
		}
		$translation = json_decode( (string) file_get_contents( $file ), true );
		if ( ! is_array( $translation ) ) {
			return new WP_Error( 'tr_pages_invalid_json', sprintf( 'Invalid JSON in %s: %s', basename( $file ), json_last_error_msg() ) );
		}
		if (
			$source_slug !== ( $translation['source_slug'] ?? '' ) ||
			$turkish_slug !== ( $translation['slug'] ?? '' ) ||
			empty( $translation['title'] ) ||
			empty( $translation['sections'] ) ||
			! is_array( $translation['sections'] )
		) {
			return new WP_Error( 'tr_pages_invalid_translation', sprintf( 'Incomplete or mismatched Turkish page translation: %s.', basename( $file ) ) );
		}

		$seed = estecapelli_it_hair_pages_source_seed( $source_slug );
		if ( is_wp_error( $seed ) ) {
			return $seed;
		}
		$coverage = estecapelli_it_hair_validate_coverage( $seed['sections'], $translation['sections'], 'page_sections', 'Turkish' );
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

/** Load and validate all live Turkish doctor overlays. */
function estecapelli_tr_doctors_load_translations() {
	$directory = get_template_directory() . '/inc/data/translations/tr/doctors';
	$loaded    = array();

	foreach ( estecapelli_tr_doctors_manifest() as $source_slug => $turkish_slug ) {
		$route = estecapelli_indexed_route_path( estecapelli_tr_doctor_route_key( $source_slug ), 'tr' );
		if ( ! $route || basename( $route ) !== $turkish_slug ) {
			return new WP_Error( 'tr_doctors_indexed_slug_mismatch', sprintf( 'The Turkish doctor slug does not match the indexed URL contract: %s.', $source_slug ) );
		}

		$file = $directory . '/' . $source_slug . '.json';
		if ( ! is_readable( $file ) ) {
			return new WP_Error( 'tr_doctors_missing_file', sprintf( 'Missing Turkish doctor translation: %s.', basename( $file ) ) );
		}
		$translation = json_decode( (string) file_get_contents( $file ), true );
		if ( ! is_array( $translation ) ) {
			return new WP_Error( 'tr_doctors_invalid_json', sprintf( 'Invalid JSON in %s: %s', basename( $file ), json_last_error_msg() ) );
		}
		if (
			$source_slug !== ( $translation['source_slug'] ?? '' ) ||
			$turkish_slug !== ( $translation['slug'] ?? '' ) ||
			empty( $translation['name'] ) ||
			empty( $translation['position'] ) ||
			empty( $translation['bio'] ) ||
			empty( $translation['credentials'] ) ||
			! is_array( $translation['credentials'] )
		) {
			return new WP_Error( 'tr_doctors_invalid_translation', sprintf( 'Incomplete or mismatched Turkish doctor translation: %s.', basename( $file ) ) );
		}

		$seed = estecapelli_it_doctor_source_seed( $source_slug );
		if ( is_wp_error( $seed ) ) {
			return $seed;
		}
		if ( count( $translation['credentials'] ) !== count( $seed['credentials'] ?? array() ) ) {
			return new WP_Error( 'tr_doctors_credentials_mismatch', sprintf( 'Credential count does not match the English source: %s.', $source_slug ) );
		}

		$loaded[ $source_slug ] = $translation;
	}

	return $loaded;
}

/** Confirm the Turkish translation of a hierarchical page's parent exists. */
function estecapelli_tr_page_parent_ready( $source_slug ) {
	$source_id = estecapelli_source_post_id( $source_slug, 'page' );
	$post      = $source_id ? get_post( $source_id ) : null;
	if ( ! $post || ! (int) $post->post_parent ) {
		return true;
	}

	$target_parent = (int) apply_filters( 'wpml_object_id', (int) $post->post_parent, 'page', false, 'tr' );
	return $target_parent && $target_parent !== (int) $post->post_parent;
}

/** Whether an English page has a distinct Turkish WPML translation. */
function estecapelli_tr_page_translation_ready( $source_slug ) {
	$source_id = estecapelli_source_post_id( $source_slug, 'page' );
	$target_id = $source_id ? (int) apply_filters( 'wpml_object_id', $source_id, 'page', false, 'tr' ) : 0;
	return $target_id && $target_id !== $source_id;
}

/** Import or repair exactly one Turkish page, template page or doctor. */
function estecapelli_run_tr_content_import( $kind, $source_slug ) {
	$kind        = (string) $kind;
	$source_slug = (string) $source_slug;
	if ( ! function_exists( 'update_field' ) ) {
		return new WP_Error( 'tr_content_acf_missing', 'ACF is required for the Turkish content import.' );
	}
	if ( ! defined( 'ICL_SITEPRESS_VERSION' ) && ! defined( 'WPML_VERSION' ) ) {
		return new WP_Error( 'tr_content_wpml_missing', 'WPML is required for the Turkish content import.' );
	}
	$languages = apply_filters( 'wpml_active_languages', null, array( 'skip_missing' => 0 ) );
	if ( ! is_array( $languages ) || ! isset( $languages['tr'] ) ) {
		return new WP_Error( 'tr_content_turkish_inactive', 'Turkish must be active in WPML before importing content.' );
	}

	if ( 'page' === $kind ) {
		if ( ! isset( estecapelli_tr_pages_manifest()[ $source_slug ] ) ) {
			return new WP_Error( 'tr_content_unknown_page', sprintf( 'Unknown Turkish page source: %s.', $source_slug ) );
		}
		if ( ! estecapelli_tr_page_parent_ready( $source_slug ) ) {
			return new WP_Error( 'tr_content_parent_missing', sprintf( 'Import the Turkish parent page before importing %s.', $source_slug ) );
		}
		$translations = estecapelli_tr_pages_load_translations();
		if ( is_wp_error( $translations ) ) {
			return $translations;
		}
		return estecapelli_it_hair_pages_import_one( $translations[ $source_slug ], 'tr', 'Turkish' );
	}

	if ( 'template' === $kind ) {
		$templates = estecapelli_tr_template_pages_manifest();
		if ( ! isset( $templates[ $source_slug ] ) ) {
			return new WP_Error( 'tr_content_unknown_template', sprintf( 'Unknown Turkish template-page source: %s.', $source_slug ) );
		}
		$route = estecapelli_indexed_route_path( '/en/' . $source_slug, 'tr' );
		if ( ! $route || basename( $route ) !== $templates[ $source_slug ]['slug'] ) {
			return new WP_Error( 'tr_content_template_slug_mismatch', sprintf( 'The Turkish template-page slug does not match the live URL contract: %s.', $source_slug ) );
		}
		return estecapelli_it_template_page_import_one( $source_slug, $templates[ $source_slug ], 'tr', 'Turkish' );
	}

	if ( 'doctor' === $kind ) {
		if ( ! isset( estecapelli_tr_doctors_manifest()[ $source_slug ] ) ) {
			return new WP_Error( 'tr_content_unknown_doctor', sprintf( 'Unknown Turkish doctor source: %s.', $source_slug ) );
		}
		$parent_source = 'mehmet-hanifi-kutlar' === $source_slug ? 'medical-director' : 'our-doctors';
		if ( ! estecapelli_tr_page_translation_ready( $parent_source ) ) {
			return new WP_Error( 'tr_content_doctor_parent_missing', sprintf( 'Import the Turkish %s page before its doctor profiles.', $parent_source ) );
		}
		$translations = estecapelli_tr_doctors_load_translations();
		if ( is_wp_error( $translations ) ) {
			return $translations;
		}
		return estecapelli_it_doctor_import_one( $translations[ $source_slug ], 'tr', 'Turkish' );
	}

	return new WP_Error( 'tr_content_unknown_kind', 'Unknown Turkish content type.' );
}

add_action( 'admin_menu', 'estecapelli_register_tr_pages_importer' );
/** Register the dedicated importer under Tools. */
function estecapelli_register_tr_pages_importer() {
	add_management_page(
		__( 'Turkish Pages Importer', 'estecapelli' ),
		__( 'Turkish Pages', 'estecapelli' ),
		'manage_options',
		'estecapelli-turkish-pages-importer',
		'estecapelli_render_tr_pages_importer'
	);
}

add_action( 'admin_post_estecapelli_import_tr_content', 'estecapelli_handle_tr_content_import' );
/** Process one record and return to the importer screen. */
function estecapelli_handle_tr_content_import() {
	if ( ! current_user_can( 'manage_options' ) ) {
		wp_die( esc_html__( 'You are not allowed to import content.', 'estecapelli' ) );
	}
	check_admin_referer( 'estecapelli_import_tr_content' );

	$kind        = isset( $_POST['kind'] ) ? sanitize_key( wp_unslash( $_POST['kind'] ) ) : '';
	$source_slug = isset( $_POST['source'] ) ? sanitize_title( wp_unslash( $_POST['source'] ) ) : '';
	$result      = estecapelli_run_tr_content_import( $kind, $source_slug );
	if ( is_wp_error( $result ) ) {
		set_transient( 'estecapelli_tr_content_import_error', $source_slug . ': ' . $result->get_error_message(), 5 * MINUTE_IN_SECONDS );
	} else {
		set_transient( 'estecapelli_tr_content_import_success', $source_slug, 5 * MINUTE_IN_SECONDS );
	}

	wp_safe_redirect( add_query_arg( 'page', 'estecapelli-turkish-pages-importer', admin_url( 'tools.php' ) ) );
	exit;
}

/** Return the linked or safely adoptable Turkish target for an importer row. */
function estecapelli_tr_content_target_id( $kind, $source_slug, $target_slug ) {
	$post_type = 'doctor' === $kind ? 'doctor' : 'page';
	$source_id = estecapelli_source_post_id( $source_slug, $post_type );
	if ( ! $source_id ) {
		return array( 0, 0 );
	}

	$linked_id = (int) apply_filters( 'wpml_object_id', $source_id, $post_type, false, 'tr' );
	if ( $linked_id === $source_id ) {
		$linked_id = 0;
	}
	$candidate = 'doctor' === $kind
		? estecapelli_it_doctor_raw_target_id( $target_slug, $source_id, 'tr' )
		: estecapelli_it_hair_page_raw_post_id( $target_slug, $source_id, 'tr' );

	return array( $linked_id, $candidate );
}

/** Render one import table for a record manifest. */
function estecapelli_render_tr_content_table( $heading, $kind, array $manifest ) {
	?>
	<h2><?php echo esc_html( $heading ); ?></h2>
	<table class="widefat striped" style="max-width:1100px;margin:0 0 2rem;">
		<thead><tr><th><?php esc_html_e( 'English source', 'estecapelli' ); ?></th><th><?php esc_html_e( 'Turkish slug', 'estecapelli' ); ?></th><th><?php esc_html_e( 'Status', 'estecapelli' ); ?></th><th><?php esc_html_e( 'Action', 'estecapelli' ); ?></th></tr></thead>
		<tbody>
			<?php foreach ( $manifest as $source_slug => $target ) :
				$target_slug = is_array( $target ) ? (string) $target['slug'] : (string) $target;
				list( $linked_id, $candidate ) = estecapelli_tr_content_target_id( $kind, $source_slug, $target_slug );
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
							<input type="hidden" name="action" value="estecapelli_import_tr_content">
							<input type="hidden" name="kind" value="<?php echo esc_attr( $kind ); ?>">
							<input type="hidden" name="source" value="<?php echo esc_attr( $source_slug ); ?>">
							<?php wp_nonce_field( 'estecapelli_import_tr_content' ); ?>
							<button type="submit" class="button button-primary"><?php esc_html_e( 'Import / Repair', 'estecapelli' ); ?></button>
						</form>
					</td>
				</tr>
			<?php endforeach; ?>
		</tbody>
	</table>
	<?php
}

/** Render the one-record-per-request Turkish content importer. */
function estecapelli_render_tr_pages_importer() {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}

	$success = get_transient( 'estecapelli_tr_content_import_success' );
	$error   = get_transient( 'estecapelli_tr_content_import_error' );
	delete_transient( 'estecapelli_tr_content_import_success' );
	delete_transient( 'estecapelli_tr_content_import_error' );
	?>
	<div class="wrap">
		<h1><?php esc_html_e( 'Turkish Pages Importer', 'estecapelli' ); ?></h1>
		<p><?php esc_html_e( 'Import or repair one record per request. Use the parent-first order shown below. All slugs are verified against the live URL contract; the Turkish homepage remains at /tr/ with no /home suffix.', 'estecapelli' ); ?></p>
		<p><strong><?php esc_html_e( 'Homepage:', 'estecapelli' ); ?></strong> <code>/tr/</code> — <?php esc_html_e( 'template text is translated automatically and does not need an import.', 'estecapelli' ); ?></p>
		<?php if ( $success ) : ?>
			<div class="notice notice-success is-dismissible"><p><?php echo esc_html( sprintf( 'Turkish content imported or repaired: %s.', $success ) ); ?></p></div>
		<?php elseif ( $error ) : ?>
			<div class="notice notice-error"><p><strong><?php esc_html_e( 'Turkish import could not finish.', 'estecapelli' ); ?></strong> <?php echo esc_html( $error ); ?></p></div>
		<?php endif; ?>

		<?php
		estecapelli_render_tr_content_table( 'Pages with translated ACF content', 'page', estecapelli_tr_pages_manifest() );
		estecapelli_render_tr_content_table( 'Blog landing page (articles excluded)', 'template', estecapelli_tr_template_pages_manifest() );
		estecapelli_render_tr_content_table( 'Live doctor profiles', 'doctor', estecapelli_tr_doctors_manifest() );
		?>
	</div>
	<?php
}

/* ---- Auto-import: refresh all tr content once per version (no per-row clicking). ---- */
if ( ! defined( 'ESTECAPELLI_TR_AUTORUN_VERSION' ) ) {
	define( 'ESTECAPELLI_TR_AUTORUN_VERSION', '2026-07-24.1' );
}
add_action( 'admin_init', 'estecapelli_maybe_autorun_tr_content', 95 );
function estecapelli_maybe_autorun_tr_content() {
	if ( ! function_exists( 'estecapelli_autorun_language_import' ) ) {
		return;
	}
	estecapelli_autorun_language_import(
		'estecapelli_tr_autorun_version',
		ESTECAPELLI_TR_AUTORUN_VERSION,
		'estecapelli_tr_autorun_items',
		'estecapelli_run_tr_content_import'
	);
}
function estecapelli_tr_autorun_items() {
	$items = array();
	foreach ( array_keys( estecapelli_tr_pages_manifest() ) as $slug ) {
		$items[] = array( 'kind' => 'page', 'slug' => $slug );
	}
	foreach ( array_keys( estecapelli_tr_doctors_manifest() ) as $slug ) {
		$items[] = array( 'kind' => 'doctor', 'slug' => $slug );
	}
	return $items;
}
