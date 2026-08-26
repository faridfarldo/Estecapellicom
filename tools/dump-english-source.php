<?php
/**
 * Dump the English source content out of the PHP seeds as JSON.
 *
 * The seeds wrap every string in __() / esc_html__(), so stubbing the i18n
 * layer to return the source text gives the exact English an overlay has to
 * translate — without going through another language's overlay.
 *
 * Usage: php tools/dump-english-source.php . pages|treatments [slug]
 */

define( 'ABSPATH', __DIR__ );

function __( $t, $d = null ) { return $t; }
function _e( $t, $d = null ) { echo $t; }
function esc_html__( $t, $d = null ) { return $t; }
function esc_attr__( $t, $d = null ) { return $t; }
function _x( $t, $c = '', $d = null ) { return $t; }
function esc_url( $u ) { return $u; }
function esc_html( $t ) { return $t; }
function home_url( $p = '' ) { return 'https://estecapelli.com' . $p; }
function estecapelli_whatsapp_url( $m = '' ) { return 'https://wa.me/900000000000'; }
function get_template_directory() { return dirname( __DIR__ ); }
function get_template_directory_uri() { return 'https://estecapelli.com/wp-content/themes/estecapelli'; }
function wp_json_encode( $d ) { return json_encode( $d ); }
function sanitize_title( $t ) { return strtolower( preg_replace( '/[^a-zA-Z0-9]+/', '-', $t ) ); }
function apply_filters( $h, $v = null ) { return $v; }
function add_action() {}
function add_filter() {}

$theme = rtrim( $argv[1], '/\\' );
$which = $argv[2] ?? 'pages';
$only  = $argv[3] ?? '';

require $theme . '/inc/data/' . ( 'pages' === $which ? 'pages-seed.php' : 'treatments-seed.php' );

$seed = 'pages' === $which ? estecapelli_pages_seed() : estecapelli_treatments_seed();

$out = array();
foreach ( $seed as $entry ) {
	$slug = $entry['slug'] ?? '';
	if ( '' === $slug ) {
		continue;
	}
	if ( '' !== $only && $slug !== $only ) {
		continue;
	}
	$out[] = $entry;
}

echo json_encode(
	1 === count( $out ) ? $out[0] : $out,
	JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES
), "\n";
