<?php
/**
 * Plugin Name: Prodigy Commerce
 * Description: Prodigy Commerce is a core integration of the Prodigy Commerce checkout, cart and payment gateway system.
 * Version: 1.0.0
 * Author: Prodigy Commerce
 * Text Domain: prodigy-commerce
 * Domain Path: /languages
 *
 * @package ProdigyCommerce
 */

if ( version_compare( phpversion(), '5.3', '>=' ) ) {
	require_once __DIR__ . '/instance.php';
	require_once __DIR__ . '/template-tags.php';
} else {
	if ( defined( 'WP_CLI' ) ) {
		WP_CLI::warning( _prodigy_commerce_php_version_text() );
	} else {
		add_action( 'admin_notices', '_prodigy_commerce_php_version_error' );
	}
}

/**
 * Admin notice for incompatible versions of PHP.
 */
function _prodigy_commerce_php_version_error() {
	printf( '<div class="error"><p>%s</p></div>', esc_html( _prodigy_commerce_php_version_text() ) );
}

/**
 * String describing the minimum PHP version.
 *
 * @return string
 */
function _prodigy_commerce_php_version_text() {
	return __( 'Prodigy Commerce plugin error: Your version of PHP is too old to run this plugin. You must be running PHP 5.3 or higher.', 'prodigy-commerce' );
}
