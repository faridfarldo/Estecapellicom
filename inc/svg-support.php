<?php
/**
 * Safe SVG upload support (admins only) — needed so editors can upload their
 * own icon files on the Custom Icons page. Uploads are sanitised on the way in
 * (scripts, event handlers, external references and DOCTYPE/ENTITY are stripped)
 * and only users who can edit theme options may upload them.
 *
 * @package Estecapelli
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Basic SVG sanitiser: removes active/dangerous content while leaving the
 * drawing shapes intact. Not a full XML parser, but robust enough for the
 * trusted-admin upload path here (belt-and-braces with the render-time output).
 *
 * @param string $svg Raw SVG markup.
 * @return string Sanitised markup.
 */
function estecapelli_sanitize_svg_markup( $svg ) {
	$svg = (string) $svg;

	// Drop XML processing instructions, DOCTYPE and ENTITY (XXE / billion laughs).
	$svg = preg_replace( '#<\?xml[^>]*\?>#i', '', $svg );
	$svg = preg_replace( '#<!DOCTYPE[^>]*>#is', '', $svg );
	$svg = preg_replace( '#<!ENTITY[^>]*>#is', '', $svg );
	$svg = preg_replace( '#<!--.*?-->#s', '', $svg );

	// Strip active/embedding elements together with their content.
	$svg = preg_replace( '#<\s*(script|foreignObject|iframe|embed|object|style|animate|set|handler)[^>]*>.*?<\s*/\s*\1\s*>#is', '', $svg );
	// Strip self-closing variants + <use> (can pull external refs).
	$svg = preg_replace( '#<\s*(script|foreignObject|iframe|embed|object|use|image|animate|set)[^>]*/?>#is', '', $svg );

	// Remove on* event handler attributes.
	$svg = preg_replace( '#\son\w+\s*=\s*"[^"]*"#is', '', $svg );
	$svg = preg_replace( "#\son\w+\s*=\s*'[^']*'#is", '', $svg );

	// Neutralise javascript: URLs in any href.
	$svg = preg_replace( '#(?:xlink:href|href)\s*=\s*(["\'])\s*javascript:[^"\']*\1#is', '', $svg );

	return trim( $svg );
}

/**
 * Allow the SVG mime type for capable users only.
 */
add_filter(
	'upload_mimes',
	function ( $mimes ) {
		if ( current_user_can( 'edit_theme_options' ) ) {
			$mimes['svg']  = 'image/svg+xml';
			$mimes['svgz'] = 'image/svg+xml';
		}
		return $mimes;
	}
);

/**
 * Let WordPress accept the .svg extension (its real-mime check is strict).
 */
add_filter(
	'wp_check_filetype_and_ext',
	function ( $data, $file, $filename ) {
		if ( preg_match( '/\.svgz?$/i', $filename ) && current_user_can( 'edit_theme_options' ) ) {
			$data['ext']  = 'svg';
			$data['type'] = 'image/svg+xml';
		}
		return $data;
	},
	10,
	3
);

/**
 * Sanitise the SVG file before it is stored in the media library.
 */
add_filter(
	'wp_handle_upload_prefilter',
	function ( $file ) {
		if ( empty( $file['name'] ) || ! preg_match( '/\.svgz?$/i', $file['name'] ) ) {
			return $file;
		}
		if ( ! current_user_can( 'edit_theme_options' ) ) {
			$file['error'] = __( 'You are not allowed to upload SVG files.', 'estecapelli' );
			return $file;
		}
		$raw = @file_get_contents( $file['tmp_name'] ); // phpcs:ignore
		if ( false !== $raw ) {
			$clean = estecapelli_sanitize_svg_markup( $raw );
			if ( '' === $clean || false === stripos( $clean, '<svg' ) ) {
				$file['error'] = __( 'This SVG could not be processed safely.', 'estecapelli' );
			} else {
				@file_put_contents( $file['tmp_name'], $clean ); // phpcs:ignore
			}
		}
		return $file;
	}
);
