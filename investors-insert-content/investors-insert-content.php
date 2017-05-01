<?php
/**
 * Plugin Name: Investors Insert Content
 * Description: Used for inserting promotional content automatically into designated spots on posts paragraphs.
 * Version: 0.1.0
 * Author:  Scott Adrian
 *
 * Text Domain: investors-insert-content
 *
 * Domain Path: /languages
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 *
 * @package InvestorsInsertContent
 */

if ( version_compare( phpversion(), '5.3', '>=' ) ) {
	require_once __DIR__ . '/instance.php';
} else {
	if ( defined( 'WP_CLI' ) ) {
		WP_CLI::warning( _investors_insert_content_php_version_text() );
	} else {
		add_action( 'admin_notices', '_investors_insert_content_php_version_error' );
	}
}

/**
 * Admin notice for incompatible versions of PHP.
 */
function _investors_insert_content_php_version_error() {
	printf( '<div class="error"><p>%s</p></div>', esc_html( _investors_insert_content_php_version_text() ) );
}

/**
 * String describing the minimum PHP version.
 *
 * @return string
 */
function _investors_insert_content_php_version_text() {
	return __( 'Investors Insert Content plugin error: Your version of PHP is too old to run this plugin. You must be running PHP 5.3 or higher.', 'investors-insert-content' );
}
