<?php
/**
 * Bootstraps the Auto Quote plugin.
 *
 * @package AutoQuote
 */

namespace AutoQuote;

/**
 * Main plugin bootstrap file.
 */
class Plugin extends Plugin_Base {

	public function __construct() {
		parent::__construct();

		// Initiate classes.
		$classes = array(
			new Auto_Quote( $this ),
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
		wp_register_style( "{$this->assets_prefix}-admin", "{$this->dir_url}css/quote.css", false );
		wp_register_script( "{$this->assets_prefix}-admin", "{$this->dir_url}js/admin.js", array( 'jquery', 'wp-color-picker' ) );
	}

	/**
	 * Register scripts/styles.
	 *
	 * @action wp_enqueue_scripts
	 */
	public function register_assets() {
		wp_register_script( "{$this->assets_prefix}-quote", "{$this->dir_url}js/quote.js", array( 'jquery' ) );
		wp_register_style( "{$this->assets_prefix}-quote", "{$this->dir_url}css/quote.css", false );
	}
}