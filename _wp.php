<?php
/**
 * Plugin Name:     _wp
 * Plugin URI:      https://github.com/miya0001/_wp
 * Description:     Fixes the some problems with our environment.
 * Author:          Takayuki Miyauchi
 * Author URI:      https://miya.io/
 * Text Domain:     _wp
 * Version:         nightly
 *
 * @package         _wp
 */

// Fixes the warning for the WP-CLI
if ( defined( 'WP_CLI' ) && WP_CLI ) {
	$host = parse_url( home_url(), PHP_URL_HOST );
	$port = parse_url( home_url(), PHP_URL_PORT );
	if ( empty( $_SERVER['HTTP_HOST'] ) ) {
		if ( ! $port || 80 === $port ) {
			$_SERVER['HTTP_HOST'] = $host;
		} else {
			$_SERVER['HTTP_HOST'] = $host . ':' . $port;
		}
	}
	if ( empty( $_SERVER['SERVER_NAME'] ) ) {
		$_SERVER['SERVER_NAME'] = $host;
	}
}

// Gets the correct IP address from reverse proxy.
if ( '127.0.0.1' === $_SERVER['REMOTE_ADDR'] && ! empty( $_SERVER['HTTP_REMOTE_ADDR'] ) ) {
	$_SERVER['REMOTE_ADDR'] = $_SERVER['HTTP_REMOTE_ADDR'];
}

// Hiding the update notification.
add_action( 'admin_head', function() {
	remove_action( 'admin_notices', 'update_nag', 3 );
} );

// For logbook plugin.
add_filter( 'logbook_active_levels', function( $active_levels ) {
	$active_levels[] = 'debug';
	$active_levels[] = 'trace';

	return $active_levels;
} );

add_filter( 'wp_image_editors', function( $editors ) {
	if ( ! class_exists( '_WP_Image_Editor_GD' ) ) {
		class _WP_Image_Editor_GD extends WP_Image_Editor_GD {
			protected function _save( $image, $filename = null, $mime_type = null ) {
				$saved = parent::_save( $image, $filename, $mime_type );
				if ( ! empty( $saved["mime-type"] ) && 'image/jpeg' == $saved["mime-type"] ) {
					jpegoptim( $saved['path'] );
				}

				return $saved;
			}
		};
	}

	if ( ! class_exists( '_WP_Image_Editor_Imagick' ) ) {
		class _WP_Image_Editor_Imagick extends WP_Image_Editor_Imagick {
			protected function _save( $image, $filename = null, $mime_type = null ) {
				$saved = parent::_save( $image, $filename, $mime_type );
				if ( ! empty( $saved["mime-type"] ) && 'image/jpeg' == $saved["mime-type"] ) {
					jpegoptim( $saved['path'] );
				}

				return $saved;
			}
		};
	}

	return array(
		'_WP_Image_Editor_GD',
		'_WP_Image_Editor_Imagick',
	);
}, 10 );


function jpegoptim( $path, $quality = 60 ) {
	$cmd = '/usr/bin/jpegoptim -m%d --strip-all %s 2>&1';
	$result = exec( sprintf( $cmd, $quality, escapeshellarg( $path ) ), $output, $status );
	if ( $status ) {
		trigger_error( $result );
	}
}
