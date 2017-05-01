<?php
/**
 * Plugin Name: Investors Category Typeahead
 * Description: Adds an input fields for users to search available categories using type ahead fucntionality.
 * Version: 0.1.0
 * Author:  Scott Adrian
 * Text Domain: investors-category-typeahead
 * Domain Path: /languages
 *
 * Copyright (c) 2017 Investors Business Daily (https://investors.com/)
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 *
 * @package InvestorsCategoryTypeahead
 */

if ( version_compare( phpversion(), '5.3', '>=' ) ) {
	require_once __DIR__ . '/instance.php';
} else {
	if ( defined( 'WP_CLI' ) ) {
		WP_CLI::warning( _investors_category_typeahead_php_version_text() );
	} else {
		add_action( 'admin_notices', '_investors_category_typeahead_php_version_error' );
	}
}

/**
 * Admin notice for incompatible versions of PHP.
 */
function _investors_category_typeahead_php_version_error() {
	printf( '<div class="error"><p>%s</p></div>', esc_html( _investors_category_typeahead_php_version_text() ) );
}

/**
 * String describing the minimum PHP version.
 *
 * @return string
 */
function _investors_category_typeahead_php_version_text() {
	return __( 'Investors Category Typeahead plugin error: Your version of PHP is too old to run this plugin. You must be running PHP 5.3 or higher.', 'investors-category-typeahead' );
}
