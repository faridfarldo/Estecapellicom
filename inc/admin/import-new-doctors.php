<?php
/**
 * New doctors — create the translations of a doctor added after the original
 * five, and keep the "Our Doctors" roster in the seed's order.
 *
 * Not behind ESTECAPELLI_ENABLE_CONTENT_MUTATIONS, for the same reason as the
 * Homepage Pages setup: it only ever fills blanks. A language that already has
 * a profile for the doctor is left exactly as it is, so nothing authored in the
 * dashboard is replaced. The only value it writes to existing posts is
 * menu_order, which is what puts the roster in the right order.
 *
 * English owns the portrait; the text comes from the JSON overlays in
 * inc/data/translations/{lang}/doctors/{slug}.json, and every slug is checked
 * against the indexed URL contract before anything is written.
 *
 * Runs once per version after deployment (first admin page load), with a
 * manual retry under Tools → New Doctors.
 *
 * @package Estecapelli
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

require_once get_template_directory() . '/inc/data/doctors-seed.php';

if ( ! defined( 'ESTECAPELLI_NEW_DOCTORS_VERSION' ) ) {
	define( 'ESTECAPELLI_NEW_DOCTORS_VERSION', '2026-10-07.2' );
}

/** English doctor slugs whose translations are created here. */
function estecapelli_new_doctors_manifest() {
	return array( 'dr-nisa-nur-polat', 'dt-sevinc-yilmaz-dogan' );
}

/** Indexed language => display name, for every translated language. */
function estecapelli_new_doctors_languages() {
	return array(
		'fr' => 'French',
		'it' => 'Italian',
		'es' => 'Spanish',
		'pt' => 'Portuguese',
		'pl' => 'Polish',
		'tr' => 'Turkish',
		'ro' => 'Romanian',
	);
}

/** One English doctor seed, or null. */
function estecapelli_new_doctor_seed( $source_slug ) {
	foreach ( estecapelli_doctors_seed() as $doctor ) {
		if ( $source_slug === ( $doctor['slug'] ?? '' ) ) {
			return $doctor;
		}
	}
	return null;
}

/** Load and validate one overlay against the seed and the URL contract. */
function estecapelli_new_doctor_load_translation( $source_slug, $language ) {
	$route = estecapelli_indexed_route_path( '/en/about-us/our-doctors/' . $source_slug, $language );
	if ( ! $route ) {
		return new WP_Error( 'new_doctor_missing_route', sprintf( 'No indexed %s route for %s.', $language, $source_slug ) );
	}

	$file = get_template_directory() . '/inc/data/translations/' . $language . '/doctors/' . $source_slug . '.json';
	if ( ! is_readable( $file ) ) {
		return new WP_Error( 'new_doctor_missing_file', sprintf( 'Missing translation file %s/doctors/%s.', $language, basename( $file ) ) );
	}

	$translation = json_decode( (string) file_get_contents( $file ), true );
	if ( ! is_array( $translation ) ) {
		return new WP_Error( 'new_doctor_invalid_json', sprintf( 'Invalid JSON in %s/doctors/%s: %s', $language, basename( $file ), json_last_error_msg() ) );
	}

	$seed = estecapelli_new_doctor_seed( $source_slug );
	if (
		! $seed ||
		$source_slug !== ( $translation['source_slug'] ?? '' ) ||
		basename( $route ) !== ( $translation['slug'] ?? '' ) ||
		empty( $translation['name'] ) ||
		empty( $translation['position'] ) ||
		empty( $translation['bio'] ) ||
		empty( $translation['credentials'] ) ||
		! is_array( $translation['credentials'] ) ||
		count( $translation['credentials'] ) !== count( $seed['credentials'] ?? array() )
	) {
		return new WP_Error( 'new_doctor_invalid_translation', sprintf( 'Incomplete or mismatched translation %s/doctors/%s.', $language, basename( $file ) ) );
	}

	return $translation;
}

/** The WPML language of a post, '' when it has none. */
function estecapelli_new_doctor_post_language( $post_id ) {
	$details = apply_filters(
		'wpml_element_language_details',
		null,
		array(
			'element_id'   => (int) $post_id,
			'element_type' => 'doctor',
		)
	);
	if ( is_object( $details ) ) {
		return (string) ( $details->language_code ?? '' );
	}
	return is_array( $details ) ? (string) ( $details['language_code'] ?? '' ) : '';
}

/**
 * Create one missing translation. Returns the new post ID, 0 when the language
 * already has a profile (left untouched), or a WP_Error.
 */
function estecapelli_new_doctor_create_translation( $source_id, $trid, array $translation, $wpml_code ) {
	global $wpdb;
	$element_type = apply_filters( 'wpml_element_type', 'doctor' );

	// Fill blanks only: an existing, valid profile in this language wins.
	$occupant = (int) estecapelli_wpml_group_element_id_raw( $trid, $element_type, $wpml_code );
	if ( $occupant && $occupant !== (int) $source_id ) {
		$post = get_post( $occupant );
		if ( $post && 'doctor' === $post->post_type && 'trash' !== $post->post_status ) {
			return 0;
		}
	}

	$source = get_post( $source_id );
	$new_id = wp_insert_post(
		array(
			'post_type'    => 'doctor',
			'post_title'   => $translation['name'],
			'post_name'    => $translation['slug'],
			'post_status'  => 'publish',
			'post_content' => '',
			'menu_order'   => (int) $source->menu_order,
		),
		true
	);
	if ( is_wp_error( $new_id ) ) {
		return $new_id;
	}

	// Never leave a half-made profile behind for the next attempt to duplicate.
	$fail = static function ( WP_Error $error ) use ( $new_id ) {
		wp_delete_post( $new_id, true );
		return $error;
	};

	do_action(
		'wpml_set_element_language_details',
		array(
			'element_id'           => (int) $new_id,
			'element_type'         => $element_type,
			'trid'                 => (int) $trid,
			'language_code'        => $wpml_code,
			'source_language_code' => 'en',
			'check_duplicates'     => false,
		)
	);
	if ( ! estecapelli_wpml_replace_language_slot_raw( $new_id, $element_type, $trid, $wpml_code, 'en' ) ) {
		return $fail( new WP_Error( 'new_doctor_link_failed', 'The WPML translation link could not be written. ' . estecapelli_wpml_last_slot_error() ) );
	}

	// WordPress suffixed the slug (-2) because English already uses it; doctor
	// slugs are the same in every language, so write it back unless another
	// profile in this same language holds it.
	if ( $translation['slug'] !== get_post_field( 'post_name', $new_id ) ) {
		$holders = $wpdb->get_col(
			$wpdb->prepare(
				"SELECT ID FROM {$wpdb->posts}
				 WHERE post_name = %s AND post_type = 'doctor' AND post_status <> 'trash' AND ID <> %d",
				$translation['slug'],
				(int) $new_id
			)
		);
		foreach ( array_map( 'intval', (array) $holders ) as $holder ) {
			$holder_language = estecapelli_new_doctor_post_language( $holder );
			if ( '' === $holder_language || $wpml_code === $holder_language ) {
				return $fail( new WP_Error( 'new_doctor_slug_conflict', sprintf( 'Doctor #%d already uses the slug %s in this language.', $holder, $translation['slug'] ) ) );
			}
		}
		$wpdb->update( $wpdb->posts, array( 'post_name' => $translation['slug'] ), array( 'ID' => (int) $new_id ) );
		clean_post_cache( $new_id );
	}

	update_field( 'position', $translation['position'], $new_id );
	update_field( 'bio', $translation['bio'], $new_id );
	update_field(
		'credentials',
		array_map(
			static function ( $label ) {
				return array( 'label' => $label );
			},
			$translation['credentials']
		),
		$new_id
	);

	// Portrait and résumé image are shared with the English profile.
	foreach ( array( 'photo', 'resume_photo', 'resume_photo_url' ) as $media_field ) {
		$value = get_field( $media_field, $source_id, false );
		if ( ! empty( $value ) ) {
			update_field( $media_field, $value, $new_id );
		}
	}
	$thumbnail_id = get_post_thumbnail_id( $source_id );
	if ( $thumbnail_id ) {
		set_post_thumbnail( $new_id, $thumbnail_id );
	}

	if ( $translation['bio'] !== (string) get_field( 'bio', $new_id ) ) {
		return $fail( new WP_Error( 'new_doctor_acf_not_saved', 'The profile fields were not saved.' ) );
	}

	return (int) $new_id;
}

/**
 * Write the seed's menu_order to every doctor post sharing each seed slug.
 *
 * Doctor slugs are identical in every language, so one slug covers the English
 * profile and all of its translations. Returns the number of posts changed.
 */
function estecapelli_sync_doctor_roster_order() {
	global $wpdb;
	$changed = 0;

	foreach ( estecapelli_doctors_seed() as $doctor ) {
		if ( empty( $doctor['slug'] ) ) {
			continue;
		}
		$order = (int) ( $doctor['menu_order'] ?? 0 );
		$ids   = $wpdb->get_col(
			$wpdb->prepare(
				"SELECT ID FROM {$wpdb->posts}
				 WHERE post_name = %s AND post_type = 'doctor' AND post_status <> 'trash' AND menu_order <> %d",
				$doctor['slug'],
				$order
			)
		);
		foreach ( array_map( 'intval', (array) $ids ) as $id ) {
			$wpdb->update( $wpdb->posts, array( 'menu_order' => $order ), array( 'ID' => $id ) );
			clean_post_cache( $id );
			++$changed;
		}
	}

	return $changed;
}

/**
 * Apply the roster order, then create every missing translation.
 *
 * Each language is attempted even when another fails; errors come back
 * together so one problem does not hold back the rest.
 *
 * @return array{created:string[],kept:string[],errors:string[],reordered:int}
 */
function estecapelli_run_new_doctors() {
	$result = array(
		'created'   => array(),
		'kept'      => array(),
		'errors'    => array(),
		'reordered' => 0,
	);

	if ( ! function_exists( 'update_field' ) || ( ! defined( 'ICL_SITEPRESS_VERSION' ) && ! defined( 'WPML_VERSION' ) ) ) {
		$result['errors'][] = 'ACF and WPML must both be active.';
		return $result;
	}

	// Order first: new translations copy the English menu_order.
	$result['reordered'] = estecapelli_sync_doctor_roster_order();

	$active = apply_filters( 'wpml_active_languages', null, array( 'skip_missing' => 0 ) );

	foreach ( estecapelli_new_doctors_manifest() as $source_slug ) {
		$source_id = (int) estecapelli_source_post_id( $source_slug, 'doctor' );
		$details   = $source_id ? apply_filters(
			'wpml_element_language_details',
			null,
			array(
				'element_id'   => $source_id,
				'element_type' => 'doctor',
			)
		) : null;
		$trid      = is_object( $details ) ? (int) ( $details->trid ?? 0 ) : 0;
		if ( ! $source_id || ! $trid || 'en' !== estecapelli_new_doctor_post_language( $source_id ) ) {
			$result['errors'][] = sprintf( '%s: no published English profile linked in WPML.', $source_slug );
			continue;
		}

		foreach ( estecapelli_new_doctors_languages() as $language => $language_name ) {
			$label     = $source_slug . ' (' . $language_name . ')';
			$wpml_code = estecapelli_wpml_language_code( $language );
			if ( ! is_array( $active ) || ! isset( $active[ $wpml_code ] ) ) {
				$result['errors'][] = $label . ': language is not active in WPML.';
				continue;
			}

			$translation = estecapelli_new_doctor_load_translation( $source_slug, $language );
			$created     = is_wp_error( $translation )
				? $translation
				: estecapelli_new_doctor_create_translation( $source_id, $trid, $translation, $wpml_code );

			if ( is_wp_error( $created ) ) {
				$result['errors'][] = $label . ': ' . $created->get_error_message();
			} elseif ( $created ) {
				$result['created'][] = $label;
			} else {
				$result['kept'][] = $label;
			}
		}
	}

	return $result;
}

/** Store a run's outcome; the version is marked done only when nothing failed. */
function estecapelli_new_doctors_record( array $result ) {
	set_transient( 'estecapelli_new_doctors_result', $result, 10 * MINUTE_IN_SECONDS );
	if ( ! $result['errors'] ) {
		update_option( 'estecapelli_new_doctors_version', ESTECAPELLI_NEW_DOCTORS_VERSION, false );
	}
}

add_action( 'admin_init', 'estecapelli_maybe_run_new_doctors', 96 );
/** Run once per version after deployment. */
function estecapelli_maybe_run_new_doctors() {
	if (
		get_option( 'estecapelli_new_doctors_version' ) === ESTECAPELLI_NEW_DOCTORS_VERSION ||
		get_transient( 'estecapelli_new_doctors_attempted' ) ||
		! current_user_can( 'manage_options' ) ||
		( function_exists( 'wp_doing_ajax' ) && wp_doing_ajax() )
	) {
		return;
	}

	// A failing run waits for the manual retry instead of firing on every page.
	set_transient( 'estecapelli_new_doctors_attempted', 1, HOUR_IN_SECONDS );
	estecapelli_new_doctors_record( estecapelli_run_new_doctors() );
}

add_action( 'admin_menu', 'estecapelli_register_new_doctors_page' );
/** Tools → New Doctors. */
function estecapelli_register_new_doctors_page() {
	add_management_page(
		__( 'New Doctors', 'estecapelli' ),
		__( 'New Doctors', 'estecapelli' ),
		'manage_options',
		'estecapelli-new-doctors',
		'estecapelli_render_new_doctors_page'
	);
}

add_action( 'admin_post_estecapelli_run_new_doctors', 'estecapelli_handle_new_doctors' );
/** Manual run from the Tools page. */
function estecapelli_handle_new_doctors() {
	if ( ! current_user_can( 'manage_options' ) ) {
		wp_die( esc_html__( 'You are not allowed to import content.', 'estecapelli' ) );
	}
	check_admin_referer( 'estecapelli_run_new_doctors' );

	estecapelli_new_doctors_record( estecapelli_run_new_doctors() );

	wp_safe_redirect( add_query_arg( 'page', 'estecapelli-new-doctors', admin_url( 'tools.php' ) ) );
	exit;
}

/** Status per language, plus the button. */
function estecapelli_render_new_doctors_page() {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}
	?>
	<div class="wrap">
		<h1><?php esc_html_e( 'New Doctors', 'estecapelli' ); ?></h1>
		<p><?php esc_html_e( 'Creates the missing translations of newly added doctors and applies the roster order. Existing translations are never changed.', 'estecapelli' ); ?></p>
		<table class="widefat striped" style="max-width:980px;margin-top:1rem;">
			<thead><tr>
				<th><?php esc_html_e( 'English profile', 'estecapelli' ); ?></th>
				<?php foreach ( estecapelli_new_doctors_languages() as $language => $language_name ) : ?>
					<th><?php echo esc_html( strtoupper( $language ) ); ?></th>
				<?php endforeach; ?>
			</tr></thead>
			<tbody>
			<?php foreach ( estecapelli_new_doctors_manifest() as $source_slug ) :
				$source_id = (int) estecapelli_source_post_id( $source_slug, 'doctor' );
				?>
				<tr>
					<td><code><?php echo esc_html( $source_slug ); ?></code><?php echo $source_id ? '' : ' — ' . esc_html__( 'missing', 'estecapelli' ); ?></td>
					<?php foreach ( estecapelli_new_doctors_languages() as $language => $language_name ) :
						$linked_id = $source_id ? (int) apply_filters( 'wpml_object_id', $source_id, 'doctor', false, estecapelli_wpml_language_code( $language ) ) : 0;
						?>
						<td>
							<?php if ( $linked_id && $linked_id !== $source_id ) : ?>
								<a href="<?php echo esc_url( get_edit_post_link( $linked_id ) ); ?>" style="color:#0d8551;"><?php esc_html_e( 'Exists', 'estecapelli' ); ?></a>
							<?php else : ?>
								<span style="color:#b26200;"><?php esc_html_e( 'Missing', 'estecapelli' ); ?></span>
							<?php endif; ?>
						</td>
					<?php endforeach; ?>
				</tr>
			<?php endforeach; ?>
			</tbody>
		</table>
		<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" style="margin-top:1rem;">
			<input type="hidden" name="action" value="estecapelli_run_new_doctors">
			<?php wp_nonce_field( 'estecapelli_run_new_doctors' ); ?>
			<button type="submit" class="button button-primary"><?php esc_html_e( 'Create missing translations + apply order', 'estecapelli' ); ?></button>
		</form>
	</div>
	<?php
}

add_action( 'admin_notices', 'estecapelli_new_doctors_notice' );
/** Report the last run once. */
function estecapelli_new_doctors_notice() {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}
	$result = get_transient( 'estecapelli_new_doctors_result' );
	if ( ! is_array( $result ) ) {
		return;
	}
	delete_transient( 'estecapelli_new_doctors_result' );

	$summary = sprintf(
		'New doctors: %d translations created, %d already existed (kept), roster order applied to %d posts.',
		count( $result['created'] ),
		count( $result['kept'] ),
		(int) $result['reordered']
	);
	printf(
		'<div class="notice notice-%s is-dismissible"><p>%s</p>%s</div>',
		$result['errors'] ? 'error' : 'success',
		esc_html( $summary ),
		$result['errors'] ? '<p>' . esc_html( implode( ' | ', $result['errors'] ) ) . '</p>' : ''
	);
}
