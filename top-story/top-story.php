<?php
/**
 * Plugin Name: Top Story
 * Description: Creates a content box on article pages to house the newest or selected post.
 * Version: 0.1.0
 * Author: Scott Adrian
 * Text Domain: top-story
 * Domain Path: /languages
 *
 * @package TopStory
 */

if ( version_compare( phpversion(), '5.3', '>=' ) ) {
	require_once __DIR__ . '/instance.php';
} else {
	if ( defined( 'WP_CLI' ) ) {
		WP_CLI::warning( _top_story_php_version_text() );
	} else {
		add_action( 'admin_notices', '_top_story_php_version_error' );
	}
}

/**
 * Admin notice for incompatible versions of PHP.
 */
function _top_story_php_version_error() {
	printf( '<div class="error"><p>%s</p></div>', esc_html( _top_story_php_version_text() ) );
}

/**
 * String describing the minimum PHP version.
 *
 * @return string
 */
function _top_story_php_version_text() {
	return __( 'Top Story plugin error: Your version of PHP is too old to run this plugin. You must be running PHP 5.3 or higher.', 'top-story' );
}
