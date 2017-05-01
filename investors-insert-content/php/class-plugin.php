<?php
/**
 * Bootstraps the Investors Insert Content plugin.
 *
 * @package InvestorsInsertContent
 */

namespace InvestorsInsertContent;

/**
 * Main plugin bootstrap file.
 */
class Plugin extends Plugin_Base {

	/**
	 * Plugin constructor.
	 */
	public function __construct() {
		parent::__construct();

		// Initiate classes.
		$classes = array(
			new Insert_Content_Page( $this ),
			new Insert_Content( $this ),
		);

		// Add classes doc hooks.
		foreach ( $classes as $instance ) {
			$this->add_doc_hooks( $instance );
		}
	}

	/**
	 * Register admin scripts/styles.
	 *
	 * @action admin_enqueue_scripts
	 */
	public function register_admin_assets() {
		wp_register_style( "{$this->assets_prefix}-insert-content-admin", "{$this->dir_url}/css/insert-content-admin.css", false );
	}

	/**
	 * Register scripts/styles.
	 *
	 * @action wp_enqueue_scripts
	 */
	public function register_assets() {
		wp_register_script( "{$this->assets_prefix}-insert-content", "{$this->dir_url}js/insert-content.js", array( 'jquery' ) );
		wp_register_style( "{$this->assets_prefix}-insert-content", "{$this->dir_url}/css/insert-content.css", false );
	}
}
