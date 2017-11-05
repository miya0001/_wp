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
	$components = parse_url( home_url() );
	if ( empty( $_SERVER['HTTP_HOST'] ) ) {
		$_SERVER['HTTP_HOST'] = $components['host'] . ':' . $components['port'];
	}
	if ( empty( $_SERVER['SERVER_NAME'] ) ) {
		$_SERVER['SERVER_NAME'] = $components['SERVER_NAME'];
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

// For talog plugin.
add_filter( 'talog_active_levels', function( $active_levels ) {
	$active_levels[] = 'debug';
	$active_levels[] = 'trace';

	return $active_levels;
} );

