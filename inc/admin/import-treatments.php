<?php
/**
 * Treatment importer — admin tool under Tools → Treatment Importer.
 *
 * Reads /inc/data/treatments-seed.php and creates or updates `treatment`
 * posts (and their treatment_category terms + ACF page_sections) from
 * that array. Idempotent: re-running an import updates the post in place
 * rather than creating duplicates.
 *
 * @package Estecapelli
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

require_once get_template_directory() . '/inc/data/treatments-seed.php';

add_action( 'admin_menu', 'estecapelli_register_treatments_importer' );
function estecapelli_register_treatments_importer() {
	add_management_page(
		__( 'Estecapelli — Treatment Importer', 'estecapelli' ),
		__( 'Treatment Importer', 'estecapelli' ),
		'manage_options',
		'estecapelli-treatment-importer',
		'estecapelli_render_treatments_importer'
	);
}

/**
 * Find or create a treatment by slug; return its ID.
 */
function estecapelli_import_treatment( array $data ) {

	if ( empty( $data['slug'] ) || empty( $data['title'] ) ) {
		return new WP_Error( 'invalid_treatment', __( 'Missing slug or title.', 'estecapelli' ) );
	}

	$existing = get_page_by_path( $data['slug'], OBJECT, 'treatment' );

	$post_args = array(
		'post_type'    => 'treatment',
		'post_title'   => $data['title'],
		'post_name'    => $data['slug'],
		'post_status'  => 'publish',
		'post_content' => '',
	);

	if ( $existing ) {
		$post_args['ID'] = $existing->ID;
		$post_id         = wp_update_post( $post_args, true );
	} else {
		$post_id = wp_insert_post( $post_args, true );
	}

	if ( is_wp_error( $post_id ) ) {
		return $post_id;
	}

	// Assign category — create the term if it doesn't exist.
	if ( ! empty( $data['category'] ) ) {
		$term = term_exists( $data['category'], 'treatment_category' );
		if ( ! $term ) {
			$term = wp_insert_term( $data['category'], 'treatment_category' );
		}
		if ( ! is_wp_error( $term ) ) {
			$term_id = is_array( $term ) ? (int) $term['term_id'] : (int) $term;
			wp_set_object_terms( $post_id, array( $term_id ), 'treatment_category', false );
		}
	}

	// Write ACF Flexible Content.
	if ( function_exists( 'update_field' ) && ! empty( $data['sections'] ) ) {
		update_field( 'page_sections', $data['sections'], $post_id );
	}

	return $post_id;
}

function estecapelli_render_treatments_importer() {

	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}

	$messages = array();

	// Handle import request.
	if ( isset( $_POST['estecapelli_action'] ) && check_admin_referer( 'estecapelli_import_treatments' ) ) {

		$treatments = estecapelli_treatments_seed();
		$target     = sanitize_text_field( wp_unslash( $_POST['estecapelli_action'] ) );

		foreach ( $treatments as $t ) {
			if ( '__all__' !== $target && $t['slug'] !== $target ) {
				continue;
			}
			$result = estecapelli_import_treatment( $t );
			if ( is_wp_error( $result ) ) {
				$messages[] = array(
					'type' => 'error',
					'text' => sprintf( '%s — %s', $t['title'], $result->get_error_message() ),
				);
			} else {
				$messages[] = array(
					'type' => 'success',
					'text' => sprintf(
						/* translators: 1: treatment title, 2: edit url */
						__( 'Imported: %1$s — %2$s', 'estecapelli' ),
						esc_html( $t['title'] ),
						'<a href="' . esc_url( get_edit_post_link( $result ) ) . '">' . esc_html__( 'edit', 'estecapelli' ) . '</a> · ' .
						'<a href="' . esc_url( get_permalink( $result ) ) . '" target="_blank">' . esc_html__( 'view', 'estecapelli' ) . '</a>'
					),
				);
			}
		}
	}

	$treatments = estecapelli_treatments_seed();
	?>
	<div class="wrap">
		<h1><?php esc_html_e( 'Treatment Importer', 'estecapelli' ); ?></h1>
		<p class="description" style="max-width:740px;">
			<?php esc_html_e( 'Each row below is a treatment defined in /inc/data/treatments-seed.php. Click "Import / Re-import" to push that treatment into WordPress. Existing posts with the same slug are updated in place — no duplicates are created.', 'estecapelli' ); ?>
		</p>

		<?php foreach ( $messages as $m ) : ?>
			<div class="notice notice-<?php echo esc_attr( $m['type'] ); ?> is-dismissible">
				<p><?php echo wp_kses_post( $m['text'] ); ?></p>
			</div>
		<?php endforeach; ?>

		<form method="post" style="margin-top:1rem;">
			<?php wp_nonce_field( 'estecapelli_import_treatments' ); ?>

			<table class="widefat striped" style="max-width:980px;">
				<thead>
					<tr>
						<th style="width:32%;"><?php esc_html_e( 'Treatment', 'estecapelli' ); ?></th>
						<th style="width:18%;"><?php esc_html_e( 'Slug', 'estecapelli' ); ?></th>
						<th style="width:15%;"><?php esc_html_e( 'Category', 'estecapelli' ); ?></th>
						<th style="width:10%;"><?php esc_html_e( 'Sections', 'estecapelli' ); ?></th>
						<th style="width:12%;"><?php esc_html_e( 'Status', 'estecapelli' ); ?></th>
						<th style="width:13%;"><?php esc_html_e( 'Action', 'estecapelli' ); ?></th>
					</tr>
				</thead>
				<tbody>
					<?php foreach ( $treatments as $t ) :
						$existing = get_page_by_path( $t['slug'], OBJECT, 'treatment' );
						?>
						<tr>
							<td><strong><?php echo esc_html( $t['title'] ); ?></strong></td>
							<td><code><?php echo esc_html( $t['slug'] ); ?></code></td>
							<td><?php echo esc_html( $t['category'] ); ?></td>
							<td><?php echo (int) count( $t['sections'] ); ?></td>
							<td>
								<?php if ( $existing ) : ?>
									<span style="color:#0d8551;">● <?php esc_html_e( 'Exists', 'estecapelli' ); ?></span><br>
									<a href="<?php echo esc_url( get_edit_post_link( $existing->ID ) ); ?>"><?php esc_html_e( 'edit', 'estecapelli' ); ?></a> ·
									<a href="<?php echo esc_url( get_permalink( $existing->ID ) ); ?>" target="_blank"><?php esc_html_e( 'view', 'estecapelli' ); ?></a>
								<?php else : ?>
									<span style="color:#888;">○ <?php esc_html_e( 'Not yet imported', 'estecapelli' ); ?></span>
								<?php endif; ?>
							</td>
							<td>
								<button type="submit" name="estecapelli_action" value="<?php echo esc_attr( $t['slug'] ); ?>" class="button">
									<?php echo $existing ? esc_html__( 'Re-import', 'estecapelli' ) : esc_html__( 'Import', 'estecapelli' ); ?>
								</button>
							</td>
						</tr>
					<?php endforeach; ?>
				</tbody>
			</table>

			<p style="margin-top:1.25rem;">
				<button type="submit" name="estecapelli_action" value="__all__" class="button button-primary button-large">
					<?php esc_html_e( 'Import / Re-import All Treatments', 'estecapelli' ); ?>
				</button>
			</p>

			<p class="description" style="max-width:740px; margin-top:1rem;">
				<strong><?php esc_html_e( 'Heads-up:', 'estecapelli' ); ?></strong>
				<?php esc_html_e( 'Re-importing overwrites the page_sections field with the seed data. Any edits made in the WordPress editor since the last import will be replaced.', 'estecapelli' ); ?>
			</p>
		</form>
	</div>
	<?php
}
