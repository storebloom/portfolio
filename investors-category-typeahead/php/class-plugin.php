<?php
/**
 * Bootstraps the Investors Category Typeahead plugin.
 *
 * @package InvestorsCategoryTypeahead
 */

namespace InvestorsCategoryTypeahead;

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
			new Category_Typeahead( $this ),
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
		wp_register_script( "{$this->assets_prefix}-admin", "{$this->dir_url}js/admin.js", array( 'jquery' ) );
		wp_register_style( "{$this->assets_prefix}-admin", "{$this->dir_url}css/admin.css", false );
	}
}
