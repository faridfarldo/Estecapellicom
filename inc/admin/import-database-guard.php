<?php
/**
 * Protect Estecapelli content imports from a missing Rank Math Analytics table.
 *
 * Rank Math's save_post watcher queries rank_math_analytics_objects whenever an
 * imported post is saved. If that plugin-owned table is missing, wpdb prints an
 * error before the importer redirects and WordPress then raises "headers already
 * sent" warnings. On import requests, this guard asks Rank Math's own installer
 * to restore its current schema. If the database user cannot create the table,
 * the broken watcher is disabled for that request and wpdb errors remain logged
 * without being sent to the browser.
 *
 * @package Estecapelli
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/** Determine whether the current admin request is running a content import. */
function estecapelli_is_content_import_request() {
	$action = isset( $_REQUEST['action'] ) ? sanitize_key( wp_unslash( $_REQUEST['action'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
	if ( 0 === strpos( $action, 'estecapelli_import_' ) ) {
		return true;
	}

	// The legacy Treatment Importer submits to its own Tools screen.
	return isset( $_POST['estecapelli_action'] ); // phpcs:ignore WordPress.Security.NonceVerification.Missing
}

/** Return the exact Rank Math Analytics objects table for this WordPress site. */
function estecapelli_rank_math_analytics_objects_table() {
	global $wpdb;

	return $wpdb->prefix . 'rank_math_analytics_objects';
}

/** Check the Rank Math table without issuing a query against the missing table. */
function estecapelli_rank_math_analytics_objects_table_exists() {
	global $wpdb;

	$table = estecapelli_rank_math_analytics_objects_table();
	$found = $wpdb->get_var(
		$wpdb->prepare(
			'SHOW TABLES LIKE %s',
			$wpdb->esc_like( $table )
		)
	);

	return $table === (string) $found;
}

/**
 * Repair the table before imports or isolate Rank Math when repair is blocked.
 */
function estecapelli_prepare_import_database() {
	if ( ! current_user_can( 'manage_options' ) || ! estecapelli_is_content_import_request() ) {
		return;
	}
	if ( estecapelli_rank_math_analytics_objects_table_exists() ) {
		return;
	}

	global $wpdb;
	$previous_show_errors = $wpdb->hide_errors();
	$repair_error         = '';

	/*
	 * Use Rank Math's version-matched installer instead of duplicating its SQL
	 * schema in the theme. This is the same API used by its Database Tools page.
	 */
	if ( class_exists( '\RankMath\Installer' ) ) {
		try {
			\RankMath\Installer::create_tables( get_option( 'rank_math_modules', array() ) );
		} catch ( Throwable $exception ) {
			$repair_error = $exception->getMessage();
		}
	}

	if ( estecapelli_rank_math_analytics_objects_table_exists() ) {
		if ( $previous_show_errors ) {
			$wpdb->show_errors();
		}
		set_transient( 'estecapelli_rank_math_table_repaired', 1, 10 * MINUTE_IN_SECONDS );
		delete_transient( 'estecapelli_rank_math_table_repair_failed' );
		return;
	}

	/*
	 * The database user may not have CREATE/ALTER privileges. Rank Math cannot
	 * maintain an index that does not exist, so skip only its save watcher for
	 * this request. Imported content and all other save hooks still run normally.
	 */
	if ( class_exists( '\RankMath\Analytics\Watcher' ) ) {
		$watcher = \RankMath\Analytics\Watcher::get();
		remove_action( 'save_post', array( $watcher, 'update_post_info' ), 101 );
	}

	$message = sprintf(
		/* translators: %s: missing database table name. */
		__( 'Rank Math could not recreate the missing %s table automatically. Its Analytics save hook was skipped for this import, so the content import can finish safely. In Rank Math SEO > Status & Tools > Database Tools, run Re-create Missing Database Tables and verify that the database user has CREATE and ALTER privileges.', 'estecapelli' ),
		estecapelli_rank_math_analytics_objects_table()
	);
	if ( $repair_error ) {
		$message .= ' ' . sprintf(
			/* translators: %s: Rank Math installer error. */
			__( 'Installer error: %s', 'estecapelli' ),
			$repair_error
		);
	}

	set_transient( 'estecapelli_rank_math_table_repair_failed', $message, 10 * MINUTE_IN_SECONDS );
}
add_action( 'admin_init', 'estecapelli_prepare_import_database', 0 );

/** Show the repair result after the importer redirects back to its Tools page. */
function estecapelli_import_database_notice() {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}

	if ( get_transient( 'estecapelli_rank_math_table_repaired' ) ) {
		delete_transient( 'estecapelli_rank_math_table_repaired' );
		?>
		<div class="notice notice-success is-dismissible"><p><?php esc_html_e( 'The missing Rank Math Analytics database table was recreated automatically before the import.', 'estecapelli' ); ?></p></div>
		<?php
	}

	$error = get_transient( 'estecapelli_rank_math_table_repair_failed' );
	if ( $error ) {
		delete_transient( 'estecapelli_rank_math_table_repair_failed' );
		?>
		<div class="notice notice-warning"><p><?php echo esc_html( $error ); ?></p></div>
		<?php
	}
}
add_action( 'admin_notices', 'estecapelli_import_database_notice', 5 );
