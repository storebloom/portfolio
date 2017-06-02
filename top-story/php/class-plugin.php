<?php
/**
 * Bootstraps the Top Story plugin.
 *
 * @package TopStory
 */

namespace TopStory;

/**
 * Main plugin bootstrap file.
 */
class Plugin extends Plugin_Base {

	/**
	 * Plugin constructor.
	 */
	public function __construct() {
		parent::__construct();

		// Initiate classes and add classes doc hooks.
		$this->add_doc_hooks( new Top_Story( $this ) );
	}

	/**
	 * Register styles.
	 *
	 * @action wp_enqueue_scripts
	 */
	public function register_styles() {
		wp_register_style( "{$this->assets_prefix}-top-story", "{$this->dir_url}/css/top-story.css", false );
	}

	/**
	 * Register admin scripts/styles.
	 *
	 * @action admin_enqueue_scripts
	 */
	public function register_admin_assets() {
		wp_register_script( "{$this->assets_prefix}-admin", "{$this->dir_url}js/admin.js", array( 'jquery', 'wp-util' ) );
		wp_register_style( "{$this->assets_prefix}-admin", "{$this->dir_url}css/admin.css", false );
	}
}
