<?php
/**
 * Translation Save Recorder — watch one real save instead of auditing config.
 *
 * Five configuration layers have now been checked and all five are correct: no
 * post carries WPML's duplicate flag, ACFML row synchronisation is off, every
 * field group is served from the theme rather than a synced database copy, the
 * ACFML field preferences translate every visitor-facing text field, and WPML's
 * own custom-field map agrees. Auditing settings has stopped being useful.
 *
 * So this records what actually happens. It snapshots every page_sections meta
 * value on a translated page immediately before the save runs, reads them all
 * back on shutdown once every plugin has had its turn, and reports what moved.
 *
 * The decisive column is the last one. If a value changed AND the new value is
 * identical to the same field on the English post, it was copied from the
 * source language — which is the whole question. If it changed to something
 * else, or vanished, that points somewhere different and the report says so.
 *
 * It records; it never writes to a post. The only thing it stores is its own
 * report option, capped at the last few saves.
 *
 * @package Estecapelli
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

const ESTECAPELLI_SAVE_REPORT_OPTION = 'estecapelli_translation_save_report';

/** In-request snapshot, keyed by post id. */
function &estecapelli_save_recorder_store() {
	static $snapshots = array();
	return $snapshots;
}

/** Every page_sections meta value on a post, raw. */
function estecapelli_save_recorder_read( $post_id ) {
	$all = get_post_meta( (int) $post_id );
	$out = array();
	foreach ( (array) $all as $key => $values ) {
		if ( 0 === strpos( (string) $key, 'page_sections' ) || 0 === strpos( (string) $key, '_page_sections' ) ) {
			$out[ $key ] = is_array( $values ) ? ( $values[0] ?? '' ) : $values;
		}
	}

	return $out;
}

/** The post's WPML language, or '' when WPML cannot say. */
function estecapelli_save_recorder_language( $post_id ) {
	$details = apply_filters(
		'wpml_element_language_details',
		null,
		array( 'element_id' => (int) $post_id, 'element_type' => 'page' )
	);
	if ( is_object( $details ) ) {
		return (string) ( $details->language_code ?? '' );
	}
	if ( is_array( $details ) ) {
		return (string) ( $details['language_code'] ?? '' );
	}

	return '';
}

/** Whether this save is a real editor save of a translated page. */
function estecapelli_save_recorder_applies( $post_id, $post ) {
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return false;
	}
	if ( wp_is_post_revision( $post_id ) || wp_is_post_autosave( $post_id ) ) {
		return false;
	}
	if ( ! $post instanceof WP_Post || 'page' !== $post->post_type ) {
		return false;
	}

	$default  = (string) apply_filters( 'wpml_default_language', null );
	$language = estecapelli_save_recorder_language( $post_id );

	return $language && $default && $language !== $default;
}

add_action( 'save_post', 'estecapelli_save_recorder_before', 1, 2 );
/** Snapshot before anything has had a chance to rewrite the rows. */
function estecapelli_save_recorder_before( $post_id, $post ) {
	if ( ! estecapelli_save_recorder_applies( $post_id, $post ) ) {
		return;
	}

	$snapshots = &estecapelli_save_recorder_store();
	$snapshots[ (int) $post_id ] = array(
		'language' => estecapelli_save_recorder_language( $post_id ),
		'before'   => estecapelli_save_recorder_read( $post_id ),
	);

	add_action( 'shutdown', 'estecapelli_save_recorder_after', 1 );
}

/** Read back on shutdown, once every plugin has finished with the post. */
function estecapelli_save_recorder_after() {
	$snapshots = &estecapelli_save_recorder_store();
	if ( empty( $snapshots ) ) {
		return;
	}

	$reports = get_option( ESTECAPELLI_SAVE_REPORT_OPTION, array() );
	$reports = is_array( $reports ) ? $reports : array();

	foreach ( $snapshots as $post_id => $snapshot ) {
		$after = estecapelli_save_recorder_read( $post_id );

		// The English post, to tell "copied from source" from "changed somehow".
		$source_id     = (int) apply_filters( 'wpml_object_id', (int) $post_id, 'page', false, 'en' );
		$source_values = ( $source_id && $source_id !== (int) $post_id )
			? estecapelli_save_recorder_read( $source_id )
			: array();

		$changes = array();
		foreach ( array_unique( array_merge( array_keys( $snapshot['before'] ), array_keys( $after ) ) ) as $key ) {
			$was = $snapshot['before'][ $key ] ?? null;
			$now = $after[ $key ] ?? null;
			if ( $was === $now ) {
				continue;
			}

			$changes[] = array(
				'key'             => $key,
				'was'             => is_scalar( $was ) ? (string) $was : wp_json_encode( $was ),
				'now'             => is_scalar( $now ) ? (string) $now : wp_json_encode( $now ),
				'matches_english' => array_key_exists( $key, $source_values ) && $source_values[ $key ] === $now,
			);
		}

		array_unshift(
			$reports,
			array(
				'post_id'   => (int) $post_id,
				'title'     => get_the_title( (int) $post_id ),
				'language'  => $snapshot['language'],
				'source_id' => $source_id,
				'when'      => current_time( 'mysql' ),
				'total'     => count( $snapshot['before'] ),
				'changes'   => array_slice( $changes, 0, 200 ),
				'changed'   => count( $changes ),
			)
		);
	}

	update_option( ESTECAPELLI_SAVE_REPORT_OPTION, array_slice( $reports, 0, 5 ), false );
	$snapshots = array();
}

add_action( 'admin_menu', 'estecapelli_register_save_recorder' );
/** Register the report under Tools. */
function estecapelli_register_save_recorder() {
	add_management_page(
		__( 'Estecapelli — Translation Save Recorder', 'estecapelli' ),
		__( 'Translation Saves', 'estecapelli' ),
		'manage_options',
		'estecapelli-translation-saves',
		'estecapelli_render_save_recorder'
	);
}

add_action( 'admin_post_estecapelli_clear_save_report', 'estecapelli_clear_save_report' );
/** Empty the recorded reports. */
function estecapelli_clear_save_report() {
	if ( ! current_user_can( 'manage_options' ) ) {
		wp_die( esc_html__( 'You are not allowed to do this.', 'estecapelli' ) );
	}
	check_admin_referer( 'estecapelli_clear_save_report' );
	delete_option( ESTECAPELLI_SAVE_REPORT_OPTION );
	wp_safe_redirect( admin_url( 'tools.php?page=estecapelli-translation-saves' ) );
	exit;
}

/** Render the recorded saves. */
function estecapelli_render_save_recorder() {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}

	$reports = get_option( ESTECAPELLI_SAVE_REPORT_OPTION, array() );
	$reports = is_array( $reports ) ? $reports : array();
	?>
	<div class="wrap">
		<h1><?php esc_html_e( 'Translation Save Recorder', 'estecapelli' ); ?></h1>
		<p class="description" style="max-width:880px;">
			<?php esc_html_e( 'Open a translated page in the editor, change one heading, and press Update. Come back here. Every page_sections value that moved during that save is listed below, with what it was and what it became.', 'estecapelli' ); ?>
		</p>
		<p class="description" style="max-width:880px;">
			<strong><?php esc_html_e( 'Read the last column first.', 'estecapelli' ); ?></strong>
			<?php esc_html_e( '"Copied from English" means the new value is identical to the same field on the English page — that is the source language being written over the translation. Anything else means the content is being lost some other way, which is a different problem with a different fix.', 'estecapelli' ); ?>
		</p>

		<?php if ( empty( $reports ) ) : ?>
			<div class="notice notice-info inline"><p><?php esc_html_e( 'Nothing recorded yet. Save a translated page, then reload this screen.', 'estecapelli' ); ?></p></div>
		<?php else : ?>
			<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" style="margin:1rem 0;">
				<input type="hidden" name="action" value="estecapelli_clear_save_report">
				<?php wp_nonce_field( 'estecapelli_clear_save_report' ); ?>
				<button type="submit" class="button"><?php esc_html_e( 'Clear these records', 'estecapelli' ); ?></button>
			</form>

			<?php foreach ( $reports as $report ) : ?>
				<h2 style="margin-top:2rem;">
					<?php echo esc_html( $report['title'] ); ?>
					<span class="description">
						(#<?php echo (int) $report['post_id']; ?>, <code><?php echo esc_html( $report['language'] ); ?></code>,
						<?php echo esc_html( $report['when'] ); ?>)
					</span>
				</h2>
				<p>
					<?php
					printf(
						/* translators: 1: changed count, 2: total count */
						esc_html__( '%1$d of %2$d builder values changed during this save.', 'estecapelli' ),
						(int) $report['changed'],
						(int) $report['total']
					);
					?>
				</p>

				<?php if ( empty( $report['changes'] ) ) : ?>
					<div class="notice notice-success inline"><p><?php esc_html_e( 'Nothing was rewritten. This save did not revert anything.', 'estecapelli' ); ?></p></div>
				<?php else : ?>
					<?php
					$from_english = 0;
					foreach ( $report['changes'] as $change ) {
						$from_english += $change['matches_english'] ? 1 : 0;
					}
					?>
					<?php if ( $from_english ) : ?>
						<div class="notice notice-error inline"><p>
							<?php
							printf(
								/* translators: %d: number of fields */
								esc_html__( '%d of them now hold exactly the English value. The source language is being written over this translation.', 'estecapelli' ),
								(int) $from_english
							);
							?>
						</p></div>
					<?php endif; ?>

					<table class="widefat striped" style="max-width:1200px;">
						<thead>
							<tr>
								<th style="width:260px;"><?php esc_html_e( 'Meta key', 'estecapelli' ); ?></th>
								<th><?php esc_html_e( 'Before the save', 'estecapelli' ); ?></th>
								<th><?php esc_html_e( 'After the save', 'estecapelli' ); ?></th>
								<th style="width:170px;"><?php esc_html_e( 'Verdict', 'estecapelli' ); ?></th>
							</tr>
						</thead>
						<tbody>
							<?php foreach ( $report['changes'] as $change ) : ?>
								<tr>
									<td><code><?php echo esc_html( $change['key'] ); ?></code></td>
									<td><?php echo esc_html( mb_substr( (string) $change['was'], 0, 160 ) ); ?></td>
									<td><?php echo esc_html( mb_substr( (string) $change['now'], 0, 160 ) ); ?></td>
									<td>
										<?php if ( $change['matches_english'] ) : ?>
											<strong style="color:#b32d2e;"><?php esc_html_e( 'Copied from English', 'estecapelli' ); ?></strong>
										<?php else : ?>
											<span style="color:#8a6d00;"><?php esc_html_e( 'Changed, not from English', 'estecapelli' ); ?></span>
										<?php endif; ?>
									</td>
								</tr>
							<?php endforeach; ?>
						</tbody>
					</table>
				<?php endif; ?>
			<?php endforeach; ?>
		<?php endif; ?>
	</div>
	<?php
}
