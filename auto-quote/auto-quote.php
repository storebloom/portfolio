<?php
/**
 * Plugin Name: Auto Quote
 * Description: Creates shortcode for auto genereated quotes to display.
 * Version: 0.1.0
 * Author: Scott Adrian
 * Text Domain: auto-quote
 * Domain Path: /languages
 *
 * @package AutoQuote
 */

if ( version_compare( phpversion(), '5.3', '>=' ) ) {
	require_once __DIR__ . '/instance.php';
} else {
	if ( defined( 'WP_CLI' ) ) {
		WP_CLI::warning( _auto_quote_php_version_text() );
	} else {
		add_action( 'admin_notices', '_auto_quote_php_version_error' );
	}
}

/**
 * Admin notice for incompatible versions of PHP.
 */
function _auto_quote_php_version_error() {
	printf( '<div class="error"><p>%s</p></div>', esc_html( _auto_quote_php_version_text() ) );
}

/**
 * String describing the minimum PHP version.
 *
 * @return string
 */
function _auto_quote_php_version_text() {
	return __( 'Auto Quote plugin error: Your version of PHP is too old to run this plugin. You must be running PHP 5.3 or higher.', 'auto-quote' );
}
