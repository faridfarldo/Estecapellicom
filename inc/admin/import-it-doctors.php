<?php
/**
 * Italian importer for the five indexed doctor profiles.
 *
 * Text is stored in version-controlled JSON overlays. Portraits and resume
 * images stay attached to the English source and are copied only when the
 * Italian field is empty. WPML linking is repaired without touching profiles
 * in any other language.
 *
 * @package Estecapelli
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! defined( 'ESTECAPELLI_IT_DOCTORS_IMPORT_VERSION' ) ) {
	define( 'ESTECAPELLI_IT_DOCTORS_IMPORT_VERSION', '2026-07-20.1' );
}

/** Indexed English doctor slug => exact indexed Italian slug. */
function estecapelli_it_doctors_manifest() {
	return array(
		'mehmet-hanifi-kutlar'  => 'mehmet-hanifi-kutlar',
		'prof-dr-binnur-ustun'  => 'prof-dr-binnur-ustun',
		'op-dr-hasan-celik'     => 'op-dr-hasan-celik',
		'op-dr-mehmet-palali'   => 'op-dr-mehmet-palali',
		'op-dr-necdet-derici'   => 'op-dr-necdet-derici',
		'op-dr-ali-durmus'      => 'op-dr-ali-durmus',
	);
}

/** Return one English doctor seed. */
function estecapelli_it_doctor_source_seed( $source_slug ) {
	foreach ( estecapelli_doctors_seed() as $doctor ) {
		if ( $source_slug === ( $doctor['slug'] ?? '' ) ) {
			return $doctor;
		}
	}

	return new WP_Error( 'it_doctors_missing_source_seed', sprintf( 'English doctor seed not found for %s.', $source_slug ) );
}

/** Exact indexed route key for a doctor profile. */
function estecapelli_it_doctor_route_key( $source_slug ) {
	$parent = 'mehmet-hanifi-kutlar' === $source_slug ? 'medical-director' : 'our-doctors';
	return '/en/about-us/' . $parent . '/' . $source_slug;
}

/**
 * Find the Italian doctor already using a slug, excluding the English source.
 *
 * Doctor slugs intentionally stay identical across languages, so selecting the
 * first raw post with that slug could overwrite French or another language.
 */
function estecapelli_it_doctor_raw_target_id( $slug, $source_id, $language_code = 'it' ) {
	global $wpdb;
	$ids = $wpdb->get_col(
		$wpdb->prepare(
			"SELECT ID FROM {$wpdb->posts}
			 WHERE post_name = %s AND post_type = 'doctor' AND post_status <> 'trash' AND ID <> %d
			 ORDER BY ID ASC",
			$slug,
			(int) $source_id
		)
	);

	foreach ( $ids as $id ) {
		$details = apply_filters(
			'wpml_element_language_details',
			null,
			array(
				'element_id'   => (int) $id,
				'element_type' => 'doctor',
			)
		);
		if ( $language_code === (string) estecapelli_it_hair_detail( $details, 'language_code' ) ) {
			return (int) $id;
		}
	}

	return 0;
}

/** Load and validate every Italian doctor overlay. */
function estecapelli_it_doctors_load_translations() {
	$directory = get_template_directory() . '/inc/data/translations/it/doctors';
	$loaded    = array();

	foreach ( estecapelli_it_doctors_manifest() as $source_slug => $italian_slug ) {
		$route = estecapelli_indexed_route_path( estecapelli_it_doctor_route_key( $source_slug ), 'it' );
		if ( ! $route || basename( $route ) !== $italian_slug ) {
			return new WP_Error( 'it_doctors_indexed_slug_mismatch', sprintf( 'The Italian doctor slug does not match the indexed URL contract: %s.', $source_slug ) );
		}

		$file = $directory . '/' . $source_slug . '.json';
		if ( ! is_readable( $file ) ) {
			return new WP_Error( 'it_doctors_missing_file', sprintf( 'Missing Italian doctor translation: %s', basename( $file ) ) );
		}

		$translation = json_decode( (string) file_get_contents( $file ), true );
		if ( ! is_array( $translation ) ) {
			return new WP_Error( 'it_doctors_invalid_json', sprintf( 'Invalid JSON in %s: %s', basename( $file ), json_last_error_msg() ) );
		}
		if (
			$source_slug !== ( $translation['source_slug'] ?? '' ) ||
			$italian_slug !== ( $translation['slug'] ?? '' ) ||
			empty( $translation['name'] ) ||
			empty( $translation['position'] ) ||
			empty( $translation['bio'] ) ||
			empty( $translation['credentials'] ) ||
			! is_array( $translation['credentials'] )
		) {
			return new WP_Error( 'it_doctors_invalid_translation', sprintf( 'Incomplete or mismatched Italian doctor translation: %s', basename( $file ) ) );
		}

		$seed = estecapelli_it_doctor_source_seed( $source_slug );
		if ( is_wp_error( $seed ) ) {
			return $seed;
		}
		if ( count( $translation['credentials'] ) !== count( $seed['credentials'] ?? array() ) ) {
			return new WP_Error( 'it_doctors_credentials_mismatch', sprintf( 'Credential count does not match the English source: %s.', $source_slug ) );
		}

		$loaded[ $source_slug ] = $translation;
	}

	return $loaded;
}

/** Import or repair one doctor profile for a requested WPML language. */
function estecapelli_it_doctor_import_one( array $translation, $language_code = 'it', $language_name = 'Italian' ) {
	$source_slug = $translation['source_slug'];
	$seed        = estecapelli_it_doctor_source_seed( $source_slug );
	if ( is_wp_error( $seed ) ) {
		return $seed;
	}

	$source_id = estecapelli_source_post_id( $source_slug, 'doctor' );
	if ( ! $source_id ) {
		return new WP_Error( 'it_doctors_missing_source_post', sprintf( 'Published English doctor not found: %s.', $source_slug ) );
	}
	$source_post = get_post( $source_id );
	if ( ! $source_post ) {
		return new WP_Error( 'it_doctors_invalid_source_post', sprintf( 'English doctor could not be loaded: %s.', $source_slug ) );
	}

	$element_type    = apply_filters( 'wpml_element_type', 'doctor' );
	$source_details  = apply_filters(
		'wpml_element_language_details',
		null,
		array(
			'element_id'   => $source_id,
			'element_type' => 'doctor',
		)
	);
	$trid            = (int) estecapelli_it_hair_detail( $source_details, 'trid' );
	$source_language = (string) estecapelli_it_hair_detail( $source_details, 'language_code' );
	if ( ! $trid || 'en' !== $source_language ) {
		return new WP_Error( 'it_doctors_unlinked_source_post', sprintf( 'WPML language details are missing for %s.', $source_slug ) );
	}

	// Prefer the canonical target profile so a stale WPML slot occupant cannot
	// be overwritten with this doctor's content.
	$target_id = estecapelli_it_doctor_raw_target_id( $translation['slug'], $source_id, $language_code );
	if ( ! $target_id ) {
		$target_id = estecapelli_wpml_group_element_id_raw( $trid, $element_type, $language_code );
	}
	if ( ! $target_id ) {
		$target_id = (int) apply_filters( 'wpml_object_id', $source_id, 'doctor', false, $language_code );
	}
	if ( $target_id === $source_id ) {
		$target_id = 0;
	}
	if ( $target_id ) {
		$raw_target = get_post( $target_id );
		if ( ! $raw_target || 'doctor' !== $raw_target->post_type || 'trash' === $raw_target->post_status || $target_id === $source_id ) {
			estecapelli_wpml_delete_relationship_raw( $target_id, $element_type, $trid, $language_code );
			$target_id = 0;
		}
	}

	if ( $target_id ) {
		delete_post_meta( $target_id, '_icl_lang_duplicate_of' );
	}

	$post_args = array(
		'post_type'    => 'doctor',
		'post_title'   => $translation['name'],
		'post_name'    => $translation['slug'],
		'post_status'  => 'publish',
		'post_content' => '',
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
			'language_code'        => $language_code,
			'source_language_code' => $source_language,
			'check_duplicates'     => false,
		)
	);
	delete_post_meta( $target_id, '_icl_lang_duplicate_of' );

	if ( ! estecapelli_wpml_replace_language_slot_raw( $target_id, $element_type, $trid, $language_code, $source_language ) ) {
		$reason = estecapelli_wpml_last_slot_error();
		return new WP_Error(
			'it_doctors_force_link_failed',
			sprintf( 'The %s WPML relationship could not be rebuilt for %s%s', $language_name, $source_slug, $reason ? ' — ' . $reason : '.' )
		);
	}

	$target_id = wp_update_post(
		array(
			'ID'         => (int) $target_id,
			'post_title' => $translation['name'],
			'post_name'  => $translation['slug'],
		),
		true
	);
	if ( is_wp_error( $target_id ) ) {
		return $target_id;
	}
	$slug_result = estecapelli_force_multilingual_post_slug( $target_id, $translation['slug'], 'doctor', $language_code );
	if ( is_wp_error( $slug_result ) ) {
		return $slug_result;
	}
	$target_post = get_post( $target_id );
	if ( ! $target_post || $translation['slug'] !== $target_post->post_name ) {
		return new WP_Error( 'it_doctors_slug_conflict', sprintf( 'The required %s doctor slug is already in use: %s.', $language_name, $translation['slug'] ) );
	}

	update_field( 'position', $translation['position'], $target_id );
	update_field( 'bio', $translation['bio'], $target_id );
	update_field(
		'credentials',
		array_map(
			static function ( $label ) {
				return array( 'label' => $label );
			},
			$translation['credentials']
		),
		$target_id
	);

	foreach ( array( 'photo', 'resume_photo' ) as $media_field ) {
		$target_media = get_field( $media_field, $target_id, false );
		if ( empty( $target_media ) ) {
			$source_media = get_field( $media_field, $source_id, false );
			if ( ! empty( $source_media ) ) {
				update_field( $media_field, $source_media, $target_id );
			}
		}
	}
	if ( ! get_field( 'resume_photo_url', $target_id, false ) ) {
		$resume_url = get_field( 'resume_photo_url', $source_id, false );
		update_field( 'resume_photo_url', $resume_url ?: ( $seed['resume_photo_url'] ?? '' ), $target_id );
	}

	$thumbnail_id = get_post_thumbnail_id( $source_id );
	if ( $thumbnail_id && ! get_post_thumbnail_id( $target_id ) ) {
		set_post_thumbnail( $target_id, $thumbnail_id );
	}

	$saved_credentials = get_field( 'credentials', $target_id );
	if (
		$translation['position'] !== (string) get_field( 'position', $target_id ) ||
		$translation['bio'] !== (string) get_field( 'bio', $target_id ) ||
		! is_array( $saved_credentials ) ||
		count( $saved_credentials ) !== count( $translation['credentials'] )
	) {
		return new WP_Error( 'it_doctors_acf_not_saved', sprintf( 'The %s doctor fields were not saved for %s.', $language_name, $source_slug ) );
	}

	return (int) $target_id;
}

/**
 * Run the Italian doctor import.
 *
 * @param string $source_slug Import only this doctor; empty imports all.
 * @return array<string,int>|WP_Error
 */
function estecapelli_run_it_doctors_import( $source_slug = '' ) {
	$source_slug = (string) $source_slug;
	if ( '' !== $source_slug && ! isset( estecapelli_it_doctors_manifest()[ $source_slug ] ) ) {
		return new WP_Error( 'it_doctors_invalid_source', sprintf( 'Unknown Italian doctor source: %s.', $source_slug ) );
	}

	if ( ! function_exists( 'update_field' ) ) {
		return new WP_Error( 'it_doctors_acf_missing', 'ACF is required for the Italian doctor import.' );
	}
	if ( ! defined( 'ICL_SITEPRESS_VERSION' ) && ! defined( 'WPML_VERSION' ) ) {
		return new WP_Error( 'it_doctors_wpml_missing', 'WPML is required for the Italian doctor import.' );
	}

	$active_languages = apply_filters( 'wpml_active_languages', null, array( 'skip_missing' => 0 ) );
	if ( ! is_array( $active_languages ) || ! isset( $active_languages['it'] ) ) {
		return new WP_Error( 'it_doctors_italian_inactive', 'Italian must be active in WPML before importing doctor translations.' );
	}

	$translations = estecapelli_it_doctors_load_translations();
	if ( is_wp_error( $translations ) ) {
		return $translations;
	}

	$imported = array();
	foreach ( $translations as $slug => $translation ) {
		if ( '' !== $source_slug && $source_slug !== $slug ) {
			continue;
		}

		$result = estecapelli_it_doctor_import_one( $translation );
		if ( is_wp_error( $result ) ) {
			return new WP_Error( $result->get_error_code(), sprintf( '%s: %s', $slug, $result->get_error_message() ) );
		}
		$imported[ $slug ] = $result;
	}

	// An empty result is not a success: reporting one would hide a manifest whose
	// source slug never matched the requested import.
	if ( ! $imported ) {
		return new WP_Error(
			'it_doctors_nothing_imported',
			sprintf( 'No Italian doctor overlay was applied for %s.', $source_slug ? $source_slug : 'any doctor' )
		);
	}

	return $imported;
}

add_action( 'admin_menu', 'estecapelli_register_it_doctors_importer' );
/** Register the dedicated Italian doctor importer. */
function estecapelli_register_it_doctors_importer() {
	add_management_page(
		__( 'Italian Doctors Importer', 'estecapelli' ),
		__( 'Italian Doctors', 'estecapelli' ),
		'manage_options',
		'estecapelli-italian-doctors-importer',
		'estecapelli_render_it_doctors_importer'
	);
}

add_action( 'admin_post_estecapelli_import_it_doctor', 'estecapelli_handle_it_doctor_import' );
/** Process one Italian doctor and return to the importer. */
function estecapelli_handle_it_doctor_import() {
	if ( ! current_user_can( 'manage_options' ) ) {
		wp_die( esc_html__( 'You are not allowed to import content.', 'estecapelli' ) );
	}
	check_admin_referer( 'estecapelli_import_it_doctor' );

	$source_slug = isset( $_POST['source'] ) ? sanitize_title( wp_unslash( $_POST['source'] ) ) : '';
	$result      = estecapelli_run_it_doctors_import( $source_slug );
	if ( is_wp_error( $result ) ) {
		set_transient( 'estecapelli_it_doctors_import_error', $source_slug . ': ' . $result->get_error_message(), 5 * MINUTE_IN_SECONDS );
	} else {
		set_transient( 'estecapelli_it_doctors_import_success', $source_slug, 5 * MINUTE_IN_SECONDS );
	}

	wp_safe_redirect( add_query_arg( 'page', 'estecapelli-italian-doctors-importer', admin_url( 'tools.php' ) ) );
	exit;
}

/** Render the one-doctor-per-request importer screen. */
function estecapelli_render_it_doctors_importer() {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}

	$success = get_transient( 'estecapelli_it_doctors_import_success' );
	$error   = get_transient( 'estecapelli_it_doctors_import_error' );
	delete_transient( 'estecapelli_it_doctors_import_success' );
	delete_transient( 'estecapelli_it_doctors_import_error' );
	?>
	<div class="wrap">
		<h1><?php esc_html_e( 'Italian Doctors Importer', 'estecapelli' ); ?></h1>
		<p><?php esc_html_e( 'Imports one Italian doctor profile per request. English owns the portrait and structure; the position, biography and credentials are translated.', 'estecapelli' ); ?></p>
		<?php if ( $success ) : ?>
			<div class="notice notice-success is-dismissible"><p><?php echo esc_html( sprintf( 'Italian doctor imported or repaired: %s.', $success ) ); ?></p></div>
		<?php elseif ( $error ) : ?>
			<div class="notice notice-error"><p><strong><?php esc_html_e( 'Italian doctor import could not finish.', 'estecapelli' ); ?></strong> <?php echo esc_html( $error ); ?></p></div>
		<?php endif; ?>

		<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
			<input type="hidden" name="action" value="estecapelli_import_it_doctor">
			<?php wp_nonce_field( 'estecapelli_import_it_doctor' ); ?>
			<table class="widefat striped" style="max-width:980px;margin-top:1rem;">
				<thead><tr><th><?php esc_html_e( 'English source', 'estecapelli' ); ?></th><th><?php esc_html_e( 'Status', 'estecapelli' ); ?></th><th><?php esc_html_e( 'Action', 'estecapelli' ); ?></th></tr></thead>
				<tbody>
					<?php foreach ( estecapelli_it_doctors_manifest() as $source_slug => $italian_slug ) :
						$source_id = estecapelli_source_post_id( $source_slug, 'doctor' );
						$linked_id = $source_id ? (int) apply_filters( 'wpml_object_id', $source_id, 'doctor', false, 'it' ) : 0;
						$exists    = ( $linked_id && $linked_id !== $source_id );
						?>
						<tr>
							<td><code><?php echo esc_html( $source_slug ); ?></code></td>
							<td>
								<?php if ( ! $source_id ) : ?>
									<span style="color:#b26200;"><?php esc_html_e( 'English profile missing — run the English doctor import first', 'estecapelli' ); ?></span>
								<?php elseif ( $exists ) : ?>
									<span style="color:#0d8551;"><?php esc_html_e( 'Exists', 'estecapelli' ); ?></span>
									— <a href="<?php echo esc_url( get_edit_post_link( $linked_id ) ); ?>"><?php esc_html_e( 'edit', 'estecapelli' ); ?></a>
								<?php else : ?>
									<span style="color:#888;"><?php esc_html_e( 'Not yet imported', 'estecapelli' ); ?></span>
								<?php endif; ?>
							</td>
							<td><button type="submit" name="source" value="<?php echo esc_attr( $source_slug ); ?>" class="button button-primary"><?php esc_html_e( 'Import / Repair', 'estecapelli' ); ?></button></td>
						</tr>
					<?php endforeach; ?>
				</tbody>
			</table>
		</form>
	</div>
	<?php
}

add_action( 'admin_init', 'estecapelli_maybe_import_it_doctors', 94 );
/** Run once after deployment; failed runs remain retryable. */
function estecapelli_maybe_import_it_doctors() {
	if (
		get_option( 'estecapelli_it_doctors_import_version' ) === ESTECAPELLI_IT_DOCTORS_IMPORT_VERSION ||
		! current_user_can( 'manage_options' ) ||
		( function_exists( 'wp_doing_ajax' ) && wp_doing_ajax() )
	) {
		return;
	}

	$result = estecapelli_run_it_doctors_import();
	if ( is_wp_error( $result ) ) {
		update_option( 'estecapelli_it_doctors_import_error', $result->get_error_message(), false );
		return;
	}

	update_option( 'estecapelli_it_doctors_import_version', ESTECAPELLI_IT_DOCTORS_IMPORT_VERSION, false );
	delete_option( 'estecapelli_it_doctors_import_error' );
	set_transient( 'estecapelli_it_doctors_import_success', count( $result ), 5 * MINUTE_IN_SECONDS );
}

add_action( 'admin_notices', 'estecapelli_it_doctors_import_notice' );
/** Show the Italian doctor-import result. */
function estecapelli_it_doctors_import_notice() {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}

	$success_count = get_transient( 'estecapelli_it_doctors_import_success' );
	if ( false !== $success_count ) {
		delete_transient( 'estecapelli_it_doctors_import_success' );
		printf(
			'<div class="notice notice-success is-dismissible"><p>%s</p></div>',
			esc_html( sprintf( 'Italian doctor translations imported successfully: %d profiles.', (int) $success_count ) )
		);
		return;
	}

	$error = get_option( 'estecapelli_it_doctors_import_error' );
	if ( $error ) {
		printf(
			'<div class="notice notice-error"><p><strong>%s</strong> %s</p></div>',
			esc_html( 'Italian doctor import could not finish.' ),
			esc_html( $error )
		);
	}
}
