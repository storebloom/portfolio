<?php
/**
 * Bootstraps the Prodigy Commerce plugin.
 *
 * @package ProdigyCommerce
 */

namespace ProdigyCommerce;

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

		// Define some prefixes to use througout the plugin.
		$this->assets_prefix = strtolower( preg_replace( '/\B([A-Z])/', '-$1', __NAMESPACE__ ) );
		$this->meta_prefix = strtolower( preg_replace( '/\B([A-Z])/', '_$1', __NAMESPACE__ ) );

		// Globals
		$product_list_widget = new Product_List_Widget( $this );
		$cart_widget = new Cart_Widget( $this );

		// Initiate classes.
		$classes = array(
			$product_list_widget,
			$cart_widget,
			new Register( $this, $product_list_widget, $cart_widget ),
			new Shortcodes( $this ),
			new Columns( $this ),
			new Store( $this ),
			new Cart( $this ),
			new Filters( $this ),
		);

		// Add classes doc hooks.
		foreach ( $classes as $instance ) {
			$this->add_doc_hooks( $instance );
		}
	}

	/**
	 * Register front end assets.
	 *
	 * @action wp_enqueue_scripts
	 */
	public function register_assets() {
		wp_register_script( "{$this->assets_prefix}-store", "{$this->dir_url}js/store.js", array( 'jquery' ) );
		wp_register_script( "{$this->assets_prefix}-cart", "{$this->dir_url}js/cart.js", array( 'jquery', 'wp-util' ) );
		wp_register_style( "{$this->assets_prefix}-store", "{$this->dir_url}css/store.css", "{$this->assets_prefix}-font-awesome" );
		wp_register_style( "{$this->assets_prefix}-font-awesome", '//maxcdn.bootstrapcdn.com/font-awesome/latest/css/font-awesome.min.css', false );
	}

	/**
	 * Register scripts/styles in the admin.
	 *
	 * @action admin_enqueue_scripts
	 */
	public function register_admin_assets() {
		wp_register_script( "{$this->assets_prefix}-admin", "{$this->dir_url}js/admin.js", array( 'jquery', 'wp-util', 'wp-color-picker' ) );
		wp_register_script( "{$this->assets_prefix}-orders", "{$this->dir_url}js/orders.js", array( 'jquery' ) );
		wp_register_style( "{$this->assets_prefix}-admin", "{$this->dir_url}css/admin.css", false );
	}
}
