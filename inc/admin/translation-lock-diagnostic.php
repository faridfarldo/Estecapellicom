<?php
/**
 * Translation Lock Diagnostic — why an edit to a translated post reverts.
 *
 * Every importer in this theme deletes `_icl_lang_duplicate_of` from the post
 * it just wrote. That meta is WPML's "this post is a duplicate of the original"
 * flag: while it is set, WPML keeps the post mirrored to its source language
 * and pushes the source content back over it. Fourteen importers deleting it is
 * a strong signal that it keeps coming back — but nothing outside the importers
 * ever clears it, and the importers only run when the content lock is open.
 *
 * The screen is READ-ONLY by default. It lists every post that currently
 * carries the flag, plus the ACFML synchronisation state, so the mechanism can
 * be confirmed rather than guessed at. Two earlier attempts at this problem
 * were blind changes to ACFML preferences on production and both had to be
 * reverted; this is deliberately evidence first.
 *
 * The clearing buttons appear only after opting in from wp-config.php:
 *
 *   define( 'ESTECAPELLI_ENABLE_TRANSLATION_UNLOCK', true );
 *
 * Clearing deletes one meta key and nothing else. It cannot alter a single
 * character of content — it only stops WPML treating the post as a mirror.
 *
 * @package Estecapelli
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/** Whether the clearing actions are unlocked. */
function estecapelli_translation_unlock_enabled() {
	return defined( 'ESTECAPELLI_ENABLE_TRANSLATION_UNLOCK' ) && true === ESTECAPELLI_ENABLE_TRANSLATION_UNLOCK;
}

/**
 * Every post currently flagged as a WPML duplicate.
 *
 * Read straight from postmeta: that is the definitive list, and it avoids
 * assuming which post types or languages are affected.
 *
 * @return array<int,object>
 */
function estecapelli_translation_locked_posts() {
	global $wpdb;

	return (array) $wpdb->get_results(
		"SELECT pm.post_id, pm.meta_value AS duplicate_of, p.post_type, p.post_title, p.post_status
		 FROM {$wpdb->postmeta} pm
		 INNER JOIN {$wpdb->posts} p ON p.ID = pm.post_id
		 WHERE pm.meta_key = '_icl_lang_duplicate_of'
		   AND p.post_status <> 'trash'
		 ORDER BY p.post_type ASC, p.post_title ASC"
	);
}

/** WPML language code for a post, read through WPML's own filter. */
function estecapelli_translation_lock_language( $post_id, $post_type ) {
	$details = apply_filters(
		'wpml_element_language_details',
		null,
		array( 'element_id' => (int) $post_id, 'element_type' => $post_type )
	);
	if ( is_object( $details ) ) {
		return (string) ( $details->language_code ?? '' );
	}
	if ( is_array( $details ) ) {
		return (string) ( $details['language_code'] ?? '' );
	}

	return '';
}

add_action( 'admin_menu', 'estecapelli_register_translation_lock_diagnostic' );
/** Register the report under Tools. */
function estecapelli_register_translation_lock_diagnostic() {
	add_management_page(
		__( 'Estecapelli — Translation Lock Diagnostic', 'estecapelli' ),
		__( 'Translation Lock', 'estecapelli' ),
		'manage_options',
		'estecapelli-translation-lock',
		'estecapelli_render_translation_lock_diagnostic'
	);
}

add_action( 'admin_post_estecapelli_translation_unlock', 'estecapelli_handle_translation_unlock' );
/** Clear the duplicate flag from one post, or from every flagged post. */
function estecapelli_handle_translation_unlock() {
	if ( ! current_user_can( 'manage_options' ) ) {
		wp_die( esc_html__( 'You are not allowed to do this.', 'estecapelli' ) );
	}
	check_admin_referer( 'estecapelli_translation_unlock' );

	if ( ! estecapelli_translation_unlock_enabled() ) {
		wp_die( esc_html__( 'Clearing is not enabled on this site.', 'estecapelli' ) );
	}

	$target  = isset( $_POST['post_id'] ) ? sanitize_text_field( wp_unslash( $_POST['post_id'] ) ) : '';
	$cleared = 0;

	if ( 'all' === $target ) {
		foreach ( estecapelli_translation_locked_posts() as $row ) {
			delete_post_meta( (int) $row->post_id, '_icl_lang_duplicate_of' );
			clean_post_cache( (int) $row->post_id );
			$cleared++;
		}
	} elseif ( (int) $target ) {
		delete_post_meta( (int) $target, '_icl_lang_duplicate_of' );
		clean_post_cache( (int) $target );
		$cleared = 1;
	}

	set_transient( 'estecapelli_translation_unlock_done', $cleared, 60 );
	wp_safe_redirect( admin_url( 'tools.php?page=estecapelli-translation-lock' ) );
	exit;
}

/**
 * Walk a field tree, collecting the effective ACFML preference of every field.
 *
 * The top-level view is not enough for the page builder: the visitor-facing text
 * lives in sub-fields inside flexible-content layouts, and it is their
 * preference that decides whether an edited translation survives a save.
 *
 * @param array  $fields ACF field definitions.
 * @param string $path   Parent path for reporting.
 * @param array  $found  Accumulator.
 * @return array<int,array{path:string,type:string,pref:int|null}>
 */
function estecapelli_translation_lock_walk_fields( array $fields, $path = '', array $found = array() ) {
	foreach ( $fields as $field ) {
		if ( ! is_array( $field ) ) {
			continue;
		}

		$name  = (string) ( $field['name'] ?? $field['key'] ?? '?' );
		$here  = $path ? $path . '/' . $name : $name;
		$found[] = array(
			'path' => $here,
			'type' => (string) ( $field['type'] ?? '?' ),
			'pref' => array_key_exists( 'wpml_cf_preferences', $field ) ? (int) $field['wpml_cf_preferences'] : null,
		);

		if ( ! empty( $field['sub_fields'] ) && is_array( $field['sub_fields'] ) ) {
			$found = estecapelli_translation_lock_walk_fields( $field['sub_fields'], $here, $found );
		}
		if ( ! empty( $field['layouts'] ) && is_array( $field['layouts'] ) ) {
			foreach ( $field['layouts'] as $layout ) {
				if ( ! empty( $layout['sub_fields'] ) && is_array( $layout['sub_fields'] ) ) {
					$found = estecapelli_translation_lock_walk_fields(
						$layout['sub_fields'],
						$here . '[' . (string) ( $layout['name'] ?? '?' ) . ']',
						$found
					);
				}
			}
		}
	}

	return $found;
}

/** WPML's own custom-field translation map, as stored. */
function estecapelli_translation_lock_wpml_map() {
	$settings = get_option( 'icl_sitepress_settings', array() );
	$map      = $settings['translation-management']['custom_fields_translation'] ?? array();

	return is_array( $map ) ? $map : array();
}

/** Render the report. */
function estecapelli_render_translation_lock_diagnostic() {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}

	$cleared = get_transient( 'estecapelli_translation_unlock_done' );
	delete_transient( 'estecapelli_translation_unlock_done' );

	$locked   = estecapelli_translation_locked_posts();
	$unlocked = estecapelli_translation_unlock_enabled();
	$sync_raw = get_option( 'acfml_synchronise_repeater_fields', null );
	?>
	<div class="wrap">
		<h1><?php esc_html_e( 'Translation Lock Diagnostic', 'estecapelli' ); ?></h1>
		<p class="description" style="max-width:860px;">
			<?php esc_html_e( 'Why an edit to a translated post can revert. A post carrying WPML’s duplicate flag is kept mirrored to its source language, so WPML pushes the source content back over anything edited on it. Every importer in this theme deletes that flag after writing — nothing else does.', 'estecapelli' ); ?>
		</p>

		<?php if ( false !== $cleared ) : ?>
			<div class="notice notice-success"><p>
				<?php
				printf(
					/* translators: %d: number of posts */
					esc_html__( 'Duplicate flag cleared on %d post(s). Content was not modified.', 'estecapelli' ),
					(int) $cleared
				);
				?>
			</p></div>
		<?php endif; ?>

		<h2><?php esc_html_e( 'Posts WPML is mirroring', 'estecapelli' ); ?></h2>

		<?php if ( empty( $locked ) ) : ?>
			<div class="notice notice-success inline"><p>
				<?php esc_html_e( 'No post carries the duplicate flag. If edits still revert, this is not the cause — the ACFML state below is the next thing to look at.', 'estecapelli' ); ?>
			</p></div>
		<?php else : ?>
			<div class="notice notice-warning inline"><p>
				<?php
				printf(
					/* translators: %d: number of posts */
					esc_html__( '%d post(s) are flagged as duplicates. Editing any of them in the WordPress editor is expected to revert.', 'estecapelli' ),
					count( $locked )
				);
				?>
			</p></div>

			<table class="widefat striped" style="max-width:1100px;margin-top:1rem;">
				<thead>
					<tr>
						<th><?php esc_html_e( 'Post', 'estecapelli' ); ?></th>
						<th><?php esc_html_e( 'Type', 'estecapelli' ); ?></th>
						<th><?php esc_html_e( 'Language', 'estecapelli' ); ?></th>
						<th><?php esc_html_e( 'Mirrored from', 'estecapelli' ); ?></th>
						<?php if ( $unlocked ) : ?><th><?php esc_html_e( 'Action', 'estecapelli' ); ?></th><?php endif; ?>
					</tr>
				</thead>
				<tbody>
					<?php foreach ( $locked as $row ) : ?>
						<tr>
							<td>
								#<?php echo (int) $row->post_id; ?> &middot;
								<a href="<?php echo esc_url( get_edit_post_link( (int) $row->post_id ) ); ?>"><?php echo esc_html( $row->post_title ); ?></a>
								<?php if ( 'publish' !== $row->post_status ) : ?>
									<span class="description">(<?php echo esc_html( $row->post_status ); ?>)</span>
								<?php endif; ?>
							</td>
							<td><code><?php echo esc_html( $row->post_type ); ?></code></td>
							<td><code><?php echo esc_html( estecapelli_translation_lock_language( (int) $row->post_id, $row->post_type ) ?: '—' ); ?></code></td>
							<td>#<?php echo (int) $row->duplicate_of; ?></td>
							<?php if ( $unlocked ) : ?>
								<td>
									<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" style="margin:0;">
										<input type="hidden" name="action" value="estecapelli_translation_unlock">
										<input type="hidden" name="post_id" value="<?php echo (int) $row->post_id; ?>">
										<?php wp_nonce_field( 'estecapelli_translation_unlock' ); ?>
										<button type="submit" class="button"><?php esc_html_e( 'Clear flag', 'estecapelli' ); ?></button>
									</form>
								</td>
							<?php endif; ?>
						</tr>
					<?php endforeach; ?>
				</tbody>
			</table>

			<?php if ( $unlocked ) : ?>
				<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" style="margin-top:1.25rem;">
					<input type="hidden" name="action" value="estecapelli_translation_unlock">
					<input type="hidden" name="post_id" value="all">
					<?php wp_nonce_field( 'estecapelli_translation_unlock' ); ?>
					<button type="submit" class="button button-primary"><?php esc_html_e( 'Clear the flag on all of them', 'estecapelli' ); ?></button>
					<p class="description"><?php esc_html_e( 'Deletes one meta key per post. No post content, title, slug or translation link is touched.', 'estecapelli' ); ?></p>
				</form>
			<?php else : ?>
				<p class="description" style="max-width:860px;margin-top:1rem;">
					<?php esc_html_e( 'To clear these, add to wp-config.php: define( \'ESTECAPELLI_ENABLE_TRANSLATION_UNLOCK\', true ); — then remove it again afterwards.', 'estecapelli' ); ?>
				</p>
			<?php endif; ?>
		<?php endif; ?>

		<h2 style="margin-top:2.5rem;"><?php esc_html_e( 'ACFML row synchronisation', 'estecapelli' ); ?></h2>
		<p class="description" style="max-width:860px;">
			<?php esc_html_e( 'The other way a save can restore old rows. The theme neutralises this at runtime, so what matters is the effective value, not what is stored.', 'estecapelli' ); ?>
		</p>
		<table class="widefat striped" style="max-width:860px;">
			<tbody>
				<tr>
					<th style="width:320px;"><?php esc_html_e( 'Effective value (what ACFML reads)', 'estecapelli' ); ?></th>
					<td>
						<code><?php echo esc_html( wp_json_encode( $sync_raw ) ); ?></code>
						<?php if ( is_array( $sync_raw ) && empty( $sync_raw ) ) : ?>
							<span style="color:#1a7f37;font-weight:600;">&nbsp;<?php esc_html_e( '— row sync is off', 'estecapelli' ); ?></span>
						<?php else : ?>
							<span style="color:#b32d2e;font-weight:600;">&nbsp;<?php esc_html_e( '— the theme filter is NOT taking effect', 'estecapelli' ); ?></span>
						<?php endif; ?>
					</td>
				</tr>
				<tr>
					<th><?php esc_html_e( 'ACFML_REPEATER_SYNC_DEFAULT', 'estecapelli' ); ?></th>
					<td><code><?php echo defined( 'ACFML_REPEATER_SYNC_DEFAULT' ) ? esc_html( wp_json_encode( ACFML_REPEATER_SYNC_DEFAULT ) ) : '—'; ?></code></td>
				</tr>
				<tr>
					<th><?php esc_html_e( 'ACFML active?', 'estecapelli' ); ?></th>
					<td><code><?php echo defined( 'ACFML_PLUGIN_PATH' ) || class_exists( 'ACFML\\Main' ) ? 'yes' : 'unknown'; ?></code></td>
				</tr>
			</tbody>
		</table>

		<h2 style="margin-top:2.5rem;"><?php esc_html_e( 'Field groups and their ACFML preferences', 'estecapelli' ); ?></h2>
		<p class="description" style="max-width:860px;">
			<?php esc_html_e( 'The theme sets these in PHP at registration time. A group that has been synced into the database overrides the PHP definition completely, preferences included — so a synced copy made before the preferences existed would silently undo them. "Copy" means ACFML replaces that field from the source language when the translation is saved.', 'estecapelli' ); ?>
		</p>

		<?php if ( ! function_exists( 'acf_get_field_group' ) ) : ?>
			<div class="notice notice-warning inline"><p><?php esc_html_e( 'ACF is not available, so nothing can be read here.', 'estecapelli' ); ?></p></div>
		<?php else : ?>
			<?php
			$labels = array( 1 => 'Copy', 2 => 'Translate', 3 => 'Copy once' );
			$keys   = array(
				'group_treatment_page_builder',
				'group_doctor_fields',
				'group_result_fields',
				'group_home_patient_stories',
				'group_home_split',
				'group_home_hero_slides',
				'group_home_trust_stats',
				'group_home_why_choose',
				'group_home_methods',
				'group_home_facilities',
			);
			?>
			<table class="widefat striped" style="max-width:1100px;">
				<thead>
					<tr>
						<th><?php esc_html_e( 'Group key', 'estecapelli' ); ?></th>
						<th><?php esc_html_e( 'Source', 'estecapelli' ); ?></th>
						<th><?php esc_html_e( 'ACFML mode', 'estecapelli' ); ?></th>
						<th><?php esc_html_e( 'Top-level field preferences', 'estecapelli' ); ?></th>
					</tr>
				</thead>
				<tbody>
					<?php foreach ( $keys as $key ) :
						$group = acf_get_field_group( $key );
						if ( ! $group ) {
							continue;
						}
						$from_db = ! empty( $group['ID'] );
						$fields  = function_exists( 'acf_get_fields' ) ? (array) acf_get_fields( $key ) : array();
						?>
						<tr>
							<td><code><?php echo esc_html( $key ); ?></code></td>
							<td>
								<?php if ( $from_db ) : ?>
									<span style="color:#b32d2e;font-weight:600;"><?php esc_html_e( 'Database — overrides the theme', 'estecapelli' ); ?></span>
								<?php else : ?>
									<span style="color:#1a7f37;font-weight:600;"><?php esc_html_e( 'Theme (PHP)', 'estecapelli' ); ?></span>
								<?php endif; ?>
							</td>
							<td><code><?php echo esc_html( (string) ( $group['acfml_field_group_mode'] ?? '—' ) ); ?></code></td>
							<td>
								<?php if ( ! $fields ) : ?>
									—
								<?php else : ?>
									<?php foreach ( $fields as $field ) :
										$pref = $field['wpml_cf_preferences'] ?? null;
										?>
										<code><?php echo esc_html( (string) ( $field['name'] ?? $field['key'] ?? '?' ) ); ?></code>
										(<?php echo esc_html( (string) ( $field['type'] ?? '?' ) ); ?>):
										<strong<?php echo ( null === $pref ) ? ' style="color:#b32d2e;"' : ''; ?>>
											<?php echo esc_html( null === $pref ? __( 'not set', 'estecapelli' ) : ( $labels[ (int) $pref ] ?? $pref ) ); ?>
										</strong><br>
									<?php endforeach; ?>
								<?php endif; ?>
							</td>
						</tr>
					<?php endforeach; ?>
				</tbody>
			</table>

			<h2 style="margin-top:2.5rem;"><?php esc_html_e( 'Text inside the page builder', 'estecapelli' ); ?></h2>
			<p class="description" style="max-width:860px;">
				<?php esc_html_e( 'The table above only shows the container. Visitor-facing text lives in sub-fields inside the flexible-content layouts, and it is their preference that decides whether an edited translation survives a save. Anything listed below is a text field that will NOT be kept when the translation is saved.', 'estecapelli' ); ?>
			</p>
			<?php
			$builder_fields = function_exists( 'acf_get_fields' ) ? (array) acf_get_fields( 'group_treatment_page_builder' ) : array();
			$walked         = estecapelli_translation_lock_walk_fields( $builder_fields );
			$text_types     = array( 'text', 'textarea', 'wysiwyg' );
			$at_risk        = array();
			$counts         = array( 'total' => 0, 'translate' => 0, 'copy' => 0, 'copy_once' => 0, 'unset' => 0 );
			foreach ( $walked as $entry ) {
				$counts['total']++;
				if ( null === $entry['pref'] ) {
					$counts['unset']++;
				} elseif ( 2 === $entry['pref'] ) {
					$counts['translate']++;
				} elseif ( 3 === $entry['pref'] ) {
					$counts['copy_once']++;
				} else {
					$counts['copy']++;
				}
				if ( in_array( $entry['type'], $text_types, true ) && 2 !== $entry['pref'] ) {
					$at_risk[] = $entry;
				}
			}
			?>
			<p>
				<?php
				printf(
					/* translators: 1: total, 2: translate, 3: copy once, 4: copy, 5: unset */
					esc_html__( '%1$d fields: %2$d Translate, %3$d Copy once, %4$d Copy, %5$d with no preference.', 'estecapelli' ),
					(int) $counts['total'],
					(int) $counts['translate'],
					(int) $counts['copy_once'],
					(int) $counts['copy'],
					(int) $counts['unset']
				);
				?>
			</p>

			<?php if ( empty( $at_risk ) ) : ?>
				<div class="notice notice-success inline"><p><?php esc_html_e( 'Every text field in the builder is set to Translate. An edit to one of them is not being reverted by an ACFML preference.', 'estecapelli' ); ?></p></div>
			<?php else : ?>
				<div class="notice notice-error inline"><p><?php esc_html_e( 'These text fields are not set to Translate, so ACFML replaces them from the source language when the translation is saved:', 'estecapelli' ); ?></p>
					<ul style="list-style:disc;margin-left:1.25rem;">
						<?php foreach ( $at_risk as $entry ) : ?>
							<li>
								<code><?php echo esc_html( $entry['path'] ); ?></code>
								(<?php echo esc_html( $entry['type'] ); ?>) —
								<strong><?php echo esc_html( null === $entry['pref'] ? __( 'no preference', 'estecapelli' ) : ( $labels[ $entry['pref'] ] ?? $entry['pref'] ) ); ?></strong>
							</li>
						<?php endforeach; ?>
					</ul>
				</div>
			<?php endif; ?>

			<h2 style="margin-top:2.5rem;"><?php esc_html_e( 'WPML’s own custom-field rules', 'estecapelli' ); ?></h2>
			<p class="description" style="max-width:860px;">
				<?php esc_html_e( 'ACF stores each builder value under a numbered meta key such as page_sections_0_title. If WPML holds its own rule for one of those keys it applies regardless of the ACFML preference above. 1 = Copy, 2 = Translate, 3 = Copy once, 0 = ignore.', 'estecapelli' ); ?>
			</p>
			<?php
			$wpml_map = estecapelli_translation_lock_wpml_map();
			$relevant = array();
			foreach ( $wpml_map as $meta_key => $rule ) {
				if ( false !== stripos( (string) $meta_key, 'page_sections' ) ) {
					$relevant[ $meta_key ] = $rule;
				}
			}
			?>
			<?php if ( empty( $wpml_map ) ) : ?>
				<p><em><?php esc_html_e( 'WPML has no custom-field rules stored at all.', 'estecapelli' ); ?></em></p>
			<?php elseif ( empty( $relevant ) ) : ?>
				<p>
					<?php
					printf(
						/* translators: %d: number of rules */
						esc_html__( 'WPML holds %d custom-field rules, none of them for a page_sections key. ACFML is deciding these fields.', 'estecapelli' ),
						count( $wpml_map )
					);
					?>
				</p>
			<?php else : ?>
				<table class="widefat striped" style="max-width:860px;">
					<thead><tr><th><?php esc_html_e( 'Meta key', 'estecapelli' ); ?></th><th><?php esc_html_e( 'WPML rule', 'estecapelli' ); ?></th></tr></thead>
					<tbody>
						<?php foreach ( $relevant as $meta_key => $rule ) : ?>
							<tr>
								<td><code><?php echo esc_html( (string) $meta_key ); ?></code></td>
								<td>
									<strong<?php echo ( 2 !== (int) $rule ) ? ' style="color:#b32d2e;"' : ''; ?>>
										<?php echo esc_html( $labels[ (int) $rule ] ?? (string) $rule ); ?>
									</strong>
								</td>
							</tr>
						<?php endforeach; ?>
					</tbody>
				</table>
			<?php endif; ?>
		<?php endif; ?>
	</div>
	<?php
}
