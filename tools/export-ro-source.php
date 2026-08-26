<?php
/** Temporary CLI helper: expose English seed records as JSON. */

define( 'ABSPATH', __DIR__ . '/../' );

function __( $text, $domain = null ) { return $text; }
function esc_html__( $text, $domain = null ) { return $text; }
function esc_url( $url ) { return $url; }
function home_url( $path = '' ) { return $path; }
function get_template_directory_uri() { return ''; }

require __DIR__ . '/../inc/data/treatments-seed.php';
require __DIR__ . '/../inc/data/pages-seed.php';
require __DIR__ . '/../inc/data/doctors-seed.php';

$kind = $argv[1] ?? '';
$slug = $argv[2] ?? '';

$records = 'treatment' === $kind
	? estecapelli_treatments_seed()
	: ( 'page' === $kind ? estecapelli_pages_seed() : estecapelli_doctors_seed() );

foreach ( $records as $record ) {
	if ( ( $record['slug'] ?? '' ) === $slug ) {
		echo json_encode( $record, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE );
		exit( 0 );
	}
}

fwrite( STDERR, "Seed not found: {$kind}/{$slug}\n" );
exit( 1 );
