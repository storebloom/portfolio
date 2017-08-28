<?php
/**
 * Bootstraps the Rpa Content Format plugin.
 *
 * @package RpaContentFormat
 */

namespace RpaContentFormat;

/**
 * Main plugin bootstrap file.
 */
class Plugin extends Plugin_Base {

	/**
	 * Plugin assets prefix.
	 *
	 * @var string Lowercased dashed prefix.
	 */
	public $assets_prefix;

	/**
	 * Plugin meta prefix.
	 *
	 * @var string Lowercased underscored prefix.
	 */
	public $meta_prefix;

	/**
	 * Plugin constructor.
	 */
	public function __construct() {
		parent::__construct();

		// Initiate classes doc hooks.
		$this->add_doc_hooks( new Content_Format( $this ) );

		// Define some prefixes to use througout the plugin.
		$this->assets_prefix = strtolower( preg_replace( '/\B([A-Z])/', '-$1', __NAMESPACE__ ) );
		$this->meta_prefix = strtolower( preg_replace( '/\B([A-Z])/', '_$1', __NAMESPACE__ ) );
	}

	/**
	 * Register admin scripts/styles.
	 *
	 * @action admin_enqueue_scripts
	 */
	public function register_admin_assets() {
		wp_register_script( "{$this->assets_prefix}-admin", "{$this->dir_url}js/admin.js", array( 'jquery', 'wp-util', 'tinymce' ) );
		wp_register_style( "{$this->assets_prefix}-admin", "{$this->dir_url}css/admin.css", false );
	}
}
