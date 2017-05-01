<?php
/**
 * Bootstraps the Investors Personal Tags plugin.
 *
 * @package InvestorsPersonalTags
 */

namespace InvestorsPersonalTags;

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
			new Personal_Tags( $this ),
		);

		// Add classes doc hooks.
		foreach ( $classes as $instance ) {
			$this->add_doc_hooks( $instance );
		}
	}

	/**
	 * Register admin scripts.
	 *
	 * @action admin_enqueue_scripts
	 */
	public function register_admin_scripts() {
		wp_register_script( "{$this->assets_prefix}-admin", "{$this->dir_url}js/admin.js", array( 'jquery' ) );
	}
}
