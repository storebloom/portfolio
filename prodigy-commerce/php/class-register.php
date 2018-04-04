<?php
/**
 * Register.
 *
 * @package ProdigyCommerce
 */

namespace ProdigyCommerce;

/**
 * Register Class
 *
 * @package ProdigyCommerce
 */
class Register {

	/**
	 * Plugin instance.
	 *
	 * @var object
	 */
	public $plugin;

	/**
	 * Product List Widget instance.
	 *
	 * @var object
	 */
	public $product_list_widget;

	/**
	 * Cart Widget instance.
	 *
	 * @var object
	 */
	public $cart_widget;

	/**
	 * Menu hook suffix.
	 *
	 * @var string
	 */
	private $orders_hook_suffix;

	/**
	 * PC Menus.
	 *
	 * @var string
	 */
	private $pc_menus;

	/**
	 * Holds the settings sections.
	 *
	 * @var string
	 */
	public $setting_sections;

	/**
	 * Holds the settings fields.
	 *
	 * @var string
	 */
	public $setting_fields;

	/**
	 * Holds sub menu pages.
	 *
	 * @var string
	 */
	public $submenu_pages;

	/**
	 * Class constructor.
	 *
	 * @param object $plugin Plugin class.
	 */
	public function __construct( $plugin, $product_list_widget, $cart_widget ) {
		$this->plugin = $plugin;
		$this->product_list_widget = $product_list_widget;
		$this->cart_widget = $cart_widget;

		$this->set_settings();
		$this->set_submenus();

		// Configure your buttons notice on activation.
		register_activation_hook( $this->plugin->dir_path . '/prodigy-commerce.php', array( $this, 'pc_activation_hook' ) );

		// Clean up plugin information on deactivation.
		register_deactivation_hook( $this->plugin->dir_path . '/prodigy-commerce.php', array( $this, 'pc_deactivation_hook' ) );
	}

	/**
	 * Functions to run upon activation of plugin
	 */
	public function pc_activation_hook() {
		// Create shop page if it doesn't exist.
		if ( null === get_page_by_title( 'store' ) ) {
			$shop_args = array(
				'post_title'   => 'Store',
				'post_status'  => 'publish',
				'post_type'    => 'page',
				'post_author'  => 1,

			);

			$prodid = wp_insert_post( $shop_args );
		} else {
			$prodobj = get_page_by_title( 'Store' );
			$prodid = $prodobj->ID;
		}

		// Create cart page if it doesn't exist.
		if ( null === get_page_by_title( 'cart' ) ) {
			$cart_args = array(
				'post_title'   => 'Cart',
				'post_status'  => 'publish',
				'post_type'    => 'page',
				'post_author'  => 1,

			);

			$cartid = wp_insert_post( $cart_args );
		} else {
			$cartobj = get_page_by_title( 'Cart' );
			$cartid = $cartobj->ID;
		}

		// Save default settings.
		$default_settings = array(
			'prodigy-commerce_shop-page' => array(
				$prodid => 'Store',
			),
			'prodigy-commerce_cart-page' => array(
				$cartid => 'Cart',
			),
		);

		foreach ( $default_settings as $setting_key => $setting_value ) {
			update_option( $setting_key, $setting_value );
		}
	}

	/**
	 * Enqueue the prodigy commerce admin assets.
	 *
	 * @action admin_enqueue_scripts
	 * @param string $hook_suffix The current admin page.
	 */
	public function enqueue_admin_assets( $hook_suffix ) {
		global $post_type;

		if ( in_array( $hook_suffix, $this->pc_menus, true ) || 'pc_product' === $post_type ) {
			wp_enqueue_script( "{$this->plugin->assets_prefix}-admin" );
			wp_add_inline_script( "{$this->plugin->assets_prefix}-admin", sprintf( 'ProdigyCommerce.boot( %s );',
				wp_json_encode( array(
					'nonce' => wp_create_nonce( $this->plugin->meta_prefix ),
				) )
			) );

			wp_enqueue_style( "{$this->plugin->assets_prefix}-admin" );

			// Add the color picker css file.
			wp_enqueue_style( 'wp-color-picker' );

			if ( $hook_suffix === $this->orders_hook_suffix ) {
				wp_enqueue_script( "{$this->plugin->assets_prefix}-orders" );
			}
		}
	}

	/**
	 * Set the submenu page items.
	 *
	 * @access private
	 */
	private function set_submenus() {
		$this->submenu_pages = array(
			array(
				'title'    => esc_html__( 'Product Categories', 'prodigy-commerce' ),
				'menu'     => esc_html__( 'Categories', 'prodigy-commerce' ),
				'slug'     => 'edit-tags.php?taxonomy=pc_product_cat&post_type=pc_product',
				'function' => null,
			),
			array(
				'title'    => esc_html__( 'Product Tags', 'prodigy-commerce' ),
				'menu'     => esc_html__( 'Tags', 'prodigy-commerce' ),
				'slug'     => 'edit-tags.php?taxonomy=pc_product_tag&post_type=pc_product',
				'function' => null,
			),
			array(
				'title'    => esc_html__( 'Modules and Themes', 'prodigy-commerce' ),
				'menu'     => esc_html__( 'Modules/Themes', 'prodigy-commerce' ),
				'slug'     => $this->plugin->assets_prefix . '-module-theme',
				'function' => array( $this, 'module_theme_menu_display' ),
			),
			array(
				'title'    => esc_html__( 'Shortcodes', 'prodigy-commerce' ),
				'menu'     => esc_html__( 'Shortcodes', 'prodigy-commerce' ),
				'slug'     => $this->plugin->assets_prefix . '-shortcodes',
				'function' => array( $this, 'shortcode_generator_display' ),
			),
			array(
				'title'    => esc_html__( 'Prodigy Commerce General Settings', 'prodigy-commerce' ),
				'menu'     => esc_html__( 'Settings', 'prodigy-commerce' ),
				'slug'     => $this->plugin->assets_prefix . '-settings',
				'function' => array( $this, 'settings_display' ),
			),
			array(
				'title'    => esc_html__( 'Add new product', 'prodigy-commerce' ),
				'menu'     => null,
				'slug'     => 'post-new.php?post_type=pc_product',
				'function' => null,
			),
		);
	}

	/**
	 * Register the Prodigy Commerce menu.
	 *
	 * @action admin_menu
	 */
	public function set_pc_menu() {
		// Main sharethis menu.
		add_menu_page(
			esc_html__( 'Prodigy Commerce', 'prodigy-commerce' ),
			esc_html__( 'Prodigy Commerce', 'prodigy-commerce' ),
			'manage_options',
			$this->plugin->assets_prefix . '-module-theme',
			null,
			'dashicons-cart',
			25
		);

		add_theme_page(
			'Theme Options',
			'Theme Options',
			'edit_theme_options',
			'customize.php',
			null
		);
	}

	/**
	 * Add all the submenus.
	 *
	 * @action admin_menu
	 */
	public function register_submenus() {
		$i = 0;

		// Set all submenus.
		foreach ( $this->submenu_pages as $submenu ) {
			$parent = $this->plugin->assets_prefix . '-module-theme';

			if ( 2 === $i ) {
				// Create orders submenu item separately to set hook.
				$this->orders_hook_suffix = add_submenu_page(
					$parent,
					esc_html__( 'Orders', 'prodigy-commerce' ),
					esc_html__( 'Orders', 'prodigy-commerce' ),
					'manage_options',
					$this->plugin->assets_prefix . '-order',
					array( $this, 'orders_display' )
				);

				$this->pc_menus[] = $this->orders_hook_suffix;
			}

			$this->pc_menus[] = add_submenu_page(
				$parent,
				$submenu['title'],
				$submenu['menu'],
				'manage_options',
				$submenu['slug'],
				$submenu['function']
			);

			$i ++;
		}
	}

	/**
	 * Set the settings sections and fields.
	 *
	 * @access private
	 */
	private function set_settings() {
		$this->setting_sections = array(
			esc_html__( 'Connection', 'prodigy-commerce' ),
			esc_html__( 'Page Options', 'prodigy-commerce' ),
			esc_html__( 'Customization', 'prodigy-commerce' ),
		);

		// Setting configs.
		$this->setting_fields = array(
			array(
				'id_suffix'   => 'api-field',
				'description' => esc_html__( 'Your personal API key recieved from Prodigy Commerce.', 'prodigy-commerce' ),
				'callback'    => 'setting_callback',
				'section'     => 'settings_section_1',
				'arg'         => array( 'api-field', '' ),
			),
			array(
				'id_suffix'   => 'shop-page',
				'description' => esc_html__( 'Search here to change the default "Store" page to another page of your choice.', 'prodigy-commerce' ),
				'callback'    => 'setting_callback',
				'section'     => 'settings_section_2',
				'arg'         => array( 'shop-page', '' ),
			),
			array(
				'id_suffix'   => 'cart-page',
				'description' => esc_html__( 'Search here to change the default "Cart" page to another page of your choice.', 'prodigy-commerce' ),
				'callback'    => 'setting_callback',
				'section'     => 'settings_section_2',
				'arg'         => array( 'cart-page', '' ),
			),
			array(
				'id_suffix'   => 'right-to-cart',
				'description' => esc_html__( 'Adding product to cart takes customer straight to the cart.', 'prodigy-commerce' ),
				'callback'    => 'setting_callback',
				'section'     => 'settings_section_2',
				'arg'         => array( 'right-to-cart', true ),
			),
			array(
				'id_suffix'   => 'cat-thumb',
				'description' => esc_html__( 'Choose the width and height of the main store / category page product thumbnails.', 'prodigy-commerce' ),
				'callback'    => 'setting_callback',
				'section'     => 'settings_section_3',
				'arg'         => array(
					'cat-thumb',
					array(
						'width' => 100,
						'height' => 100,
					),
				),
			),
			array(
				'id_suffix'   => 'breadcrumb',
				'description' => esc_html__( 'Turn the store breadcrumbs on and off here.', 'prodigy-commerce' ),
				'callback'    => 'setting_callback',
				'section'     => 'settings_section_2',
				'arg'         => array( 'breadcrumb', true ),
			),
			array(
				'id_suffix'   => 'category-view',
				'description' => esc_html__( 'Show categories instead of products', 'prodigy-commerce' ),
				'callback'    => 'setting_callback',
				'section'     => 'settings_section_2',
				'arg'         => array( 'category-view', true ),
			),
			array(
				'id_suffix'   => 'pagination',
				'description' => esc_html__( 'Pagination: Provide the product count per page you wish to display on store / category pages.', 'prodigy-commerce' ),
				'callback'    => 'setting_callback',
				'section'     => 'settings_section_2',
				'arg'         => array( 'pagination', true ),
			),
			array(
				'id_suffix'   => 'add-to-cart',
				'description' => esc_html__( 'Customize the cart button style.', 'prodigy-commerce' ),
				'callback'    => 'setting_callback',
				'section'     => 'settings_section_3',
				'arg'         => array(
					'add-to-cart',
					array(
						'show-button' => '',
						'rounded'     => '',
						'color'       => '',
						'font-color'  => '',
						'text'        => '',
						'padding'     => '',
					),
				),
			),
			array(
				'id_suffix'   => 'custom-css',
				'description' => esc_html__( 'Add custom CSS here.', 'prodigy-commerce' ),
				'callback'    => 'setting_callback',
				'section'     => 'settings_section_3',
				'arg'         => array( 'custom-css', '' ),
			),
		);
	}

	/**
	 * Register all settings and secitons.
	 *
	 * @action admin_init
	 */
	public function register_settings_settings() {
		// Register sections.
		foreach ( $this->setting_sections as $index => $title ) {
			// Since the index starts at 0, let's increment it by 1.
			$i = $index + 1;
			$section = "settings_section_{$i}";

			// Add setting section.
			add_settings_section(
				$section,
				$title,
				null,
				$this->plugin->assets_prefix . '-settings'
			);
		}

		// Register setting fields.
		foreach ( $this->setting_fields as $setting_field ) {
			register_setting(
				$this->plugin->assets_prefix . '-settings',
				$this->plugin->assets_prefix . '_' . $setting_field['id_suffix']
			);
			add_settings_field(
				$this->plugin->assets_prefix . '_' . $setting_field['id_suffix'],
				$setting_field['description'],
				array( $this, $setting_field['callback'] ),
				$this->plugin->assets_prefix . '-settings',
				$setting_field['section'],
				$setting_field['arg']
			);
		}
	}

	/**
	 * Call back function for the product menu.
	 */
	public function module_theme_menu_display() {
		// Check user capabilities.
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		$modules = array(
			esc_html__( 'Variable Products', 'prodigy-commerce' ) => array(
				'description' => esc_html__( 'Add a Variable option to the product type.  This module allows you to create variable products with variants ex. color, size.', 'prodigy-commerce' ),
				'class' => 'Variable',
				'download' => 'http://google.com',
			),
			esc_html__( 'Subscriptions', 'prodigy-commerce' ) => array(
				'description' => esc_html__( 'Turn your products into subscriptions.  This will add subscription options and settings to your products for recurring payments.', 'prodigy-commerce' ),
				'class' => 'Subscription',
				'download' => 'http://google.com',
			),
		);

		// Default tab is themes.
		$active_tab = 'modules';

		// Set active tab depending on tab clicked.
		if ( isset( $_GET['tab'] ) ) { // WPCS: CSRF ok.
			$active_tab = $_GET['tab']; // WPCS: CSRF ok.
		}

		include_once( "{$this->plugin->dir_path}/templates/module-theme.php" );
	}

	/**
	 * Call back function for the shortcode generator.
	 */
	public function shortcode_generator_display() {
		// Check user capabilities.
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		$shortcode_list = array(
			'get-pc-products' => array(
				'count'       => array(
					'type'        => 'number',
					'description' => esc_html__( 'The maximum amount of products to display.', 'prodigy-commerce' ),
				),
				'thumbnail-width'   => array(
					'type'        => 'number',
					'description' => esc_html__( 'The width of the product thumbnails. Will override the global settings.', 'prodigy-commerce' ),
				),
				'category'    => array(
					'type'        => 'text',
					'description' => esc_html__( 'The category to filter the products by.', 'prodigy-commerce' ),
				),
				'tag'         => array(
					'type'        => 'text',
					'description' => esc_html__( 'The tag to filter the products by.', 'prodigy-commerce' ),
				),
				'cart-button' => array(
					'type'        => 'checkbox',
					'description' => esc_html__( 'Show the add to cart button.', 'prodigy-commerce' ),
					'default'     => 'checked=checked',
				),
				'price'       => array(
					'type'        => 'checkbox',
					'description' => esc_html__( 'Show the product price.', 'prodigy-commerce' ),
					'default'     => 'checked=checked',
				),
				'sale-items'  => array(
					'type'        => 'checkbox',
					'description' => esc_html__( 'Only display sale items.', 'prodigy-commerce' ),
				),
				'show-categories'  => array(
					'type'        => 'checkbox',
					'description' => esc_html__( 'Show categories instead of products.', 'prodigy-commerce' ),
				),
				'description' => esc_html__( 'This shortcode is used to display a list of products based on the attributes you provide.', 'prodigy-commerce' ),
			),
			'get-pc-thumbnail' => array(
				'id'       => array(
					'type'        => 'number',
					'description' => esc_html__( 'The product ID to get thumbnails from.  Defaults to current post id of page.', 'prodigy-commerce' ),
				),
				'additional-images'       => array(
					'type'        => 'checkbox',
					'description' => esc_html__( 'Show additional images under main thumbnail.', 'prodigy-commerce' ),
				),
				'width'       => array(
					'type'        => 'number',
					'description' => esc_html__( 'The width of the thumbnail in px.', 'prodigy-commerce' ),
				),
				'height'       => array(
					'type'        => 'number',
					'description' => esc_html__( 'The height of the thumbnail in px. Defaults to auto.', 'prodigy-commerce' ),
				),
				'description' => esc_html__( 'This shortcode is used to call a products thumbnail(s).', 'prodigy-commerce' ),
			),
			'get-pc-description' => array(
				'id'       => array(
					'type'        => 'number',
					'description' => esc_html__( 'The product ID to get description from.  Defaults to current post id of page.', 'prodigy-commerce' ),
				),
				'short'       => array(
					'type'        => 'checkbox',
					'description' => esc_html__( 'Show short description.', 'prodigy-commerce' ),
				),

				'description' => esc_html__( 'This shortcode is used to call a products description.', 'prodigy-commerce' ),
			),
			'get-pc-price' => array(
				'id' => array(
					'type'        => 'number',
					'description' => esc_html__( 'The product ID to get the price for. Defaults to current post id of page.', 'prodigy-commerce' ),
				),

				'description' => esc_html__( 'This shortcode is used to call a products price.', 'prodigy-commerce' ),
			),
			'get-pc-cart-button' => array(
				'id' => array(
					'type'        => 'number',
					'description' => esc_html__( 'The product ID to get the add to cart button for. Defaults to current post id of page.', 'prodigy-commerce' ),
				),

				'description' => esc_html__( 'This shortcode is used to call a products add to cart button.', 'prodigy-commerce' ),
			),
			'get-pc-cart' => array(
				'dropdown' => array(
					'type'        => 'checkbox',
					'description' => esc_html__( 'Display cart with a drop down view.  Will display icon and open on mouse over.', 'prodigy-commerce' ),
				),
				'showcount' => array(
					'type'        => 'checkbox',
					'description' => esc_html__( 'Show the count icon. Must have dropdown=true.', 'prodigy-commerce' ),
				),
				'showsubtotal' => array(
					'type'        => 'checkbox',
					'description' => esc_html__( 'Show the cart subtotal.', 'prodigy-commerce' ),
				),
				'icon' => array(
					'type'        => 'text',
					'description' => esc_html__( 'The icon to use for the drop down display. Must have dropdown=true. Defaul is "fa-shopping-cart"', 'prodigy-commerce' ),
				),

				'description' => esc_html__( 'This shortcode is used to call the users current cart.', 'prodigy-commerce' ),
			),
		);

		include_once( "{$this->plugin->dir_path}/templates/shortcodes.php" );
	}

	/**
	 * Call back function for settings menu display.
	 */
	public function settings_display() {
		// Check user capabilities.
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		include_once( "{$this->plugin->dir_path}/templates/settings.php" );
	}

	/**
	 * Call back function for the orders menu display.
	 */
	public function orders_display() {
		// Check user capabilities.
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}
	}

	/**
	 * Call back for settings fields.
	 *
	 * @param array $args call back args
	 * @param string|array $default The option default value.
	 */
	public function setting_callback( $args ) {
		$option = get_option( 'prodigy-commerce_' . $args[0] );
		$option = null !== $option && false !== $option ? $option : $args[1] ;

		include( "{$this->plugin->dir_path}/templates/{$args[0]}.php" );
	}

	/**
	 * Register the Product post type.
	 *
	 * @action init
	 */
	public function register_product() {
		$supports = array( 'title', 'editor', 'excerpt', 'thumbnail', 'custom-fields', 'publicize', 'wpcom-markdown' );
		$labels = array(
			'name'                  => esc_html__( 'Products', 'prodigy-commerce' ),
			'singular_name'         => esc_html__( 'Product', 'prodigy-commerce' ),
			'all_items'             => esc_html__( 'Products', 'prodigy-commerce' ),
			'menu_name'             => _x( 'Products', 'Admin menu name', 'prodigy-commerce' ),
			'add_new'               => esc_html__( 'Add New', 'prodigy-commerce' ),
			'add_new_item'          => esc_html__( 'Add new product', 'prodigy-commerce' ),
			'edit'                  => esc_html__( 'Edit', 'prodigy-commerce' ),
			'edit_item'             => esc_html__( 'Edit product', 'prodigy-commerce' ),
			'new_item'              => esc_html__( 'New product', 'prodigy-commerce' ),
			'view'                  => esc_html__( 'View product', 'prodigy-commerce' ),
			'view_item'             => esc_html__( 'View product', 'prodigy-commerce' ),
			'search_items'          => esc_html__( 'Search products', 'prodigy-commerce' ),
			'not_found'             => esc_html__( 'No products found', 'prodigy-commerce' ),
			'not_found_in_trash'    => esc_html__( 'No products found in trash', 'prodigy-commerce' ),
			'parent'                => esc_html__( 'Parent product', 'prodigy-commerce' ),
			'featured_image'        => esc_html__( 'Product image', 'prodigy-commerce' ),
			'set_featured_image'    => esc_html__( 'Set product image', 'prodigy-commerce' ),
			'remove_featured_image' => esc_html__( 'Remove product image', 'prodigy-commerce' ),
			'use_featured_image'    => esc_html__( 'Use as product image', 'prodigy-commerce' ),
			'insert_into_item'      => esc_html__( 'Insert into product', 'prodigy-commerce' ),
			'uploaded_to_this_item' => esc_html__( 'Uploaded to this product', 'prodigy-commerce' ),
			'filter_items_list'     => esc_html__( 'Filter products', 'prodigy-commerce' ),
			'items_list_navigation' => esc_html__( 'Products navigation', 'prodigy-commerce' ),
			'items_list'            => esc_html__( 'Products list', 'prodigy-commerce' ),
		);

		$args = array(
			'labels'             => $labels,
			'description'        => esc_html__( 'You store\'s products', 'prodigy-commerce' ),
			'public'             => true,
			'publicly_queryable' => true,
			'show_ui'            => true,
			'show_in_menu'       => $this->plugin->assets_prefix . '-module-theme',
			'query_var'          => true,
			'rewrite'            => array(
				'slug' => 'pc_product',
			),
			'capability_type'    => 'post',
			'has_archive'        => true,
			'hierarchical'       => false,
			'menu_position'      => null,
			'supports'           => $supports,
			'show_in_rest'       => true,
			'taxonomy'           => array(
				'pc_product_cat',
				'pc_product_tag',
			),
		);

		register_post_type( 'pc_product', $args );
	}

	/**
	 * Register custom taxonomies.
	 *
	 * @action init
	 */
	public function register_pc_taxonomies() {
		// Product category labels.
		$cat_labels = array(
			'name'              => _x( 'Product Categories', 'taxonomy general name', 'prodigy-commerce' ),
			'singular_name'     => _x( 'Product Category', 'taxonomy singular name', 'prodigy-commerce' ),
			'search_items'      => esc_html__( 'Search Product Categories', 'prodigy-commerce' ),
			'all_items'         => esc_html__( 'All Product Categories', 'prodigy-commerce' ),
			'parent_item'       => esc_html__( 'Parent Product Category', 'prodigy-commerce' ),
			'parent_item_colon' => esc_html__( 'Parent Product Category:', 'prodigy-commerce' ),
			'edit_item'         => esc_html__( 'Edit Product Category', 'prodigy-commerce' ),
			'update_item'       => esc_html__( 'Update Product Category', 'prodigy-commerce' ),
			'add_new_item'      => esc_html__( 'Add New Product Category', 'prodigy-commerce' ),
			'new_item_name'     => esc_html__( 'New Product Category', 'prodigy-commerce' ),
			'menu_name'         => esc_html__( 'Product Categories', 'prodigy-commerce' ),
			'not_found'         => esc_html__( 'No product categories found.', 'prodigy-commerce' ),
		);

		// Product tag labels.
		$tag_labels = array(
			'name'              => _x( 'Product Tags', 'taxonomy general name', 'prodigy-commerce' ),
			'singular_name'     => _x( 'Product Tag', 'taxonomy singular name', 'prodigy-commerce' ),
			'search_items'      => esc_html__( 'Search Product Tags', 'prodigy-commerce' ),
			'all_items'         => esc_html__( 'All Product Tags', 'prodigy-commerce' ),
			'parent_item'       => esc_html__( 'Parent Product Tag', 'prodigy-commerce' ),
			'parent_item_colon' => esc_html__( 'Parent Product Tag:', 'prodigy-commerce' ),
			'edit_item'         => esc_html__( 'Edit Product Tag', 'prodigy-commerce' ),
			'update_item'       => esc_html__( 'Update Product Tag', 'prodigy-commerce' ),
			'add_new_item'      => esc_html__( 'Add New Product Tag', 'prodigy-commerce' ),
			'new_item_name'     => esc_html__( 'New Product Tag', 'prodigy-commerce' ),
			'menu_name'         => esc_html__( 'Product Tags', 'prodigy-commerce' ),
			'not_found'         => esc_html__( 'No product tags found.', 'prodigy-commerce' ),
		);

		$taxonomies = array(
			array(
				'slug'         => 'pc_product_cat',
				'labels'       => $cat_labels,
				'hierarchical' => true,
			),
			array(
				'slug'         => 'pc_product_tag',
				'labels'       => $tag_labels,
				'hierarchical' => false,
			),
		);

		// Register all custom taxonomies
		foreach ( $taxonomies as $type ) {
			$args = array(
				'hierarchical'       => $type['hierarchical'],
				'labels'             => $type['labels'],
				'show_ui'            => true,
				'show_admin_column'  => true,
				'query_var'          => true,
				'rewrite'            => array(
					'slug' => $type['slug'],
				),
				'show_in_nav_menus'  => true,
				'show_tagcloud'      => false,
				'show_in_quick_edit' => true,
			);

			register_taxonomy( $type['slug'], 'pc_product', $args );
		}
	}

	/**
	 * Fix custom taxonomy highlighting in sub menu.
	 *
	 * @filter parent_file
	 */
	function set_correct_menu_highlight( $parent_file ) {
		global $submenu_file, $current_screen, $pagenow;

		// Set the submenu as active/current while anywhere in my sub menu.
		if ( 'pc_product' === $current_screen->post_type ) {
			switch ( $submenu_file ) {
				case 'edit-tags.php?taxonomy=pc_product_cat&amp;post_type=pc_product' :
					$submenu_file = 'edit-tags.php?taxonomy=pc_product_cat&post_type=' . $current_screen->post_type; // WPCS: Override global ok.
				break;
				case 'edit-tags.php?taxonomy=pc_product_tag&amp;post_type=pc_product' :
					$submenu_file = 'edit-tags.php?taxonomy=pc_product_tag&post_type=' . $current_screen->post_type; // WPCS: Override global ok.
				break;
				case 'post-new.php?post_type=pc_product' :
					$submenu_file = 'edit.php?post_type=' . $current_screen->post_type; // WPCS: Override global ok.
				break;
			}

			$parent_file = $this->plugin->assets_prefix . '-module-theme';
		}

		return $parent_file;
	}

	/**
	 * Add additional thumbnail uploads to feature image metabox.
	 *
	 * @filter admin_post_thumbnail_html
	 * @param $content
	 * @param $post_id
	 *
	 * @return string
	 */
	public function additional_images( $content, $post_id ) {
		global $post_type;

		if ( 'pc_product' !== $post_type ) {
			return $content;
		}

		// Get current additional images if any.
		$field_value = get_post_meta( $post_id, '_pc_product_image', true );
		$field_value = isset( $field_value ) ? $field_value : '';

		$additional_field = esc_html__( 'Additional product images', 'prodigy-commerce' );
		$additional_field .= '<p>';
		$additional_field .= '<button type="button" class="upload-add-image">';
		$additional_field .= esc_html__( 'add additional images', 'prodigy-commerce' );
		$additional_field .= '</button>';
		$additional_field .= '</p>';
		$additional_field .= '<div id="pc-additional-image-wrap">';

		if ( is_array( $field_value ) ) {
			foreach ( $field_value as $num => $image_url ) {
				$additional_field .= '<div class="pc-additional-image">';
				$additional_field .= '<span class="pc-remove-additional-image">';
				$additional_field .= 'x';
				$additional_field .= '</span>';
				$additional_field .= '<img src="' . $image_url . '">';
				$additional_field .= '<input type="hidden" name="_pc_product_image[' . $num . ']" id="pc-product-image-' . $num . '" value="' . $image_url . '">';
				$additional_field .= '</div>';
			}
		}

		$additional_field .= '</div>';

		return $content .= $additional_field;
	}

	/**
	 * Register the product settings metabox in pc_product editor.
	 *
	 * @action add_meta_boxes
	 */
	public function register_settings_metabox() {
		add_meta_box(
			$this->plugin->meta_prefix,
			esc_html__( 'Product Settings', 'prodigy-commerce' ),
			array( $this, 'product_setting_metabox' ),
			'pc_product',
			'normal',
			'high'
		);
	}

	/**
	 * The meta box call back function.
	 */
	public function product_setting_metabox() {
		$postid = get_the_ID();

		// Default setting values.
		$default = array(
			'inventory' => array(
				'pc-full-inventory'      => 0,
				'pc-available-inventory' => 0,
			),
			'shipping' => array(
				'pc-shippable' => '',
				'pc-weight'    => 0,
				'pc-length'    => 0,
				'pc-width'     => 0,
				'pc-height'    => 0,
			),
			'tax' => array(
				'pc-tax-code' => '',
				'pc-taxable'  => '',
			),
			'general' => array(
				'pc-product-type'      => 'simple',
				'pc-product-sku'       => '',
				'pc-regular-price'     => 0,
				'pc-sale-price'        => 0,
				'pc-cart-button'       => 'Add to cart',
				'pc-short-description' => '',
			),
		);

		// Tax code options.
		$tax_codes = array(
			esc_html__( 'Regular Taxable Item', 'prodigy-commerce' )              => '',
			esc_html__( 'Clothing - 20010', 'prodigy-commerce' )                  => 20010,
			esc_html__( 'Software as as Service - 30070', 'prodigy-commerce' )    => 30070,
			esc_html__( 'Digital Goods - 31000', 'prodigy-commerce' )             => 31000,
			esc_html__( 'Food & Groceries - 40030', 'prodigy-commerce' )          => 40030,
			esc_html__( 'Non-Prescription - 51010', 'prodigy-commerce' )          => 51010,
			esc_html__( 'Prescription - 51020', 'prodigy-commerce' )              => 51020,
			esc_html__( 'Books - 81100', 'prodigy-commerce' )                     => 81100,
			esc_html__( 'Textbooks - 81110', 'prodigy-commerce' )                 => 81110,
			esc_html__( 'Religious Books - 81120', 'prodigy-commerce' )           => 81120,
			esc_html__( 'Magazines & Subscriptions - 81300', 'prodigy-commerce' ) => 81300,
			esc_html__( 'Magazine - 81310', 'prodigy-commerce' )                  => 81310,
			20010                                                                              => esc_html__( 'All human wearing apparel suitable for general use.', 'prodigy-commerce' ),
			30070                                                                              => esc_html__( 'Pre-written software, delivered electronically, but access remotely.', 'prodigy-commerce' ),
			31000                                                                              => esc_html__( 'Digital products transferred electronically, meaning obtained by the purchaser by means other than tangible storage media.', 'prodigy-commerce' ),
			40030                                                                              => esc_html__( 'Food for humans consumption, unprepared.', 'prodigy-commerce' ),
			51010                                                                              => esc_html__( 'Drugs for human use without a prescription.', 'prodigy-commerce' ),
			51020                                                                              => esc_html__( 'Drugs for human use with a prescription.', 'prodigy-commerce' ),
			81100                                                                              => esc_html__( 'Books, printed.', 'prodigy-commerce' ),
			81110                                                                              => esc_html__( 'Textbooks, printed.', 'prodigy-commerce' ),
			81120                                                                              => esc_html__( 'Religious books and manuals, printed.', 'prodigy-commerce' ),
			81300                                                                              => esc_html__( 'Periodicals, printed, sold by subscription.', 'prodigy-commerce' ),
			81310                                                                              => esc_html__( 'Periodicals, printed, sold individually.', 'prodigy-commerce' ),
		);

		// Get the location data if its already been entered.
		$product_gen = get_post_meta( $postid, '_pc_product_general', true );
		$regular_price = get_post_meta( $postid, '_pc_regular_price', true );
		$sale_price = get_post_meta( $postid, '_pc_sale_price', true );
		$shipping = get_post_meta( $postid, '_pc_shipping_settings', true );
		$tax = get_post_meta( $postid, '_pc_tax_settings', true );
		$inventory = get_post_meta( $postid, '_pc_inventory_settings', true );

		// Set default values if empty.
		$inventory = '' !== $inventory ? $inventory : $default['inventory'];
		$shipping = '' !== $shipping ? $shipping : $default['shipping'];
		$tax = '' !== $tax ? $tax : $default['tax'];
		$taxable = isset( $tax['pc-taxable'] ) ? $tax['pc-taxable'] : '';
		$shippable = isset( $shipping['pc-shippable'] ) ? $shipping['pc-shippable'] : '';
		$product_gen = '' !== $product_gen ? $product_gen : $default['general'];
		$regular_price = '' !== $regular_price ? $regular_price : $default['general']['pc-regular-price'];
		$sale_price = '' !== $sale_price ? $sale_price : $default['general']['pc-sale-price'];

		// Noncename needed to verify where the data originated.
		wp_nonce_field( 'pc-product-settings', 'pc_product_noncename' );

		include_once( "{$this->plugin->dir_path}/templates/meta-box.php" );
	}

	/**
	 * Save the product setting post metabox data.
	 *
	 * @action save_post
	 *
	 * @param integer $post_id the current posts id.
	 * @param object  $post the current post object.
	 */
	public function save_product_meta( $post_id, $post ) {
		if ( isset( $_POST['pc_product_noncename'] ) && ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['pc_product_noncename'] ) ), 'pc-product-settings' ) ) { // WPSC: input var ok;
			return $post->ID;
		}

		// Is the user allowed to edit the post or page?
		if ( ! current_user_can( 'edit_post', $post->ID ) ) {
			return $post->ID;
		}

		// Make sure this is not a revision.
		if ( 'revision' === $post->post_type ) {
			return;
		}

		// Set the event meta to be saved.
		$events_meta['_pc_product_general'] = isset( $_POST['_pc_product_general'] ) ? array_map( 'sanitize_text_field', $_POST['_pc_product_general'] ) : ''; // WPSC: input var ok;
		$events_meta['_pc_regular_price'] = isset( $_POST['_pc_regular_price'] ) ? floatval( wp_unslash( $_POST['_pc_regular_price'] ) ) : ''; // WPSC: input var ok;
		$events_meta['_pc_sale_price'] = isset( $_POST['_pc_sale_price'] ) ? floatval( wp_unslash( $_POST['_pc_sale_price'] ) ) : ''; // WPSC: input var ok;
		$events_meta['_pc_shipping_settings'] = isset( $_POST['_pc_shipping_settings'] ) ? array_map( 'sanitize_text_field', $_POST['_pc_shipping_settings'] ) : ''; // WPSC: input var ok;
		$events_meta['_pc_inventory_settings'] = isset( $_POST['_pc_inventory_settings'] ) ? array_map( 'sanitize_text_field', $_POST['_pc_inventory_settings'] ) : ''; // WPSC: input var ok;
		$events_meta['_pc_tax_settings'] = isset( $_POST['_pc_tax_settings'] ) ? array_map( 'sanitize_text_field', $_POST['_pc_tax_settings'] ) : ''; // WPSC: input var ok;
		$events_meta['_pc_product_image'] = isset( $_POST['_pc_product_image'] ) ? array_map( 'sanitize_text_field', $_POST['_pc_product_image'] ) : ''; // WPSC: input var ok;

		// Add values of $events_meta as custom fields.
		foreach ( $events_meta as $key => $value ) {
			update_post_meta( $post->ID, $key, $value );

			if ( ! $value ) {
				delete_post_meta( $post->ID, $key );
			}
		}
	}

	/**
	 * Send user to the hosted Orders page.
	 *
	 * @action wp_ajax
	 */
	public function navigate_to_orders() {
		global $current_screen;

		check_ajax_referer( $this->plugin->meta_prefix, 'nonce' );

		// If user navigates to the orders menu page redirect them to the host page.
		if ( 'prodigy-commerce_page_prodigy-commerce-orders' === $current_screen->base ) {
			wp_send_json( true );
		}
	}

	/**
	 * AJAX call back function for returning pages.
	 *
	 * @action wp_ajax_return_pages
	 */
	public function return_pages() {
		check_ajax_referer( $this->plugin->meta_prefix, 'nonce' );

		if ( ! isset( $_POST['key'] ) || '' === $_POST['key'] ) { // WPCS: input var ok.
			wp_send_json_error( '' );
		}

		$key_input = sanitize_text_field( wp_unslash( $_POST['key'] ) ); // WPCS: input var ok.
		$type = sanitize_text_field( wp_unslash( $_POST['type'] ) ); // WPCS: input var ok.

		if ( 'widget-category' === $type ) {
			// Search category names LIKE $key_input.
			$categories = get_categories( array(
				'name__like' => $key_input,
				'hide_empty' => false,
			) );

			foreach ( $categories as $cats ) {
				$related[] = array(
					'id'    => $cats->term_id,
					'title' => $cats->name,
				);
			}
		} elseif ( 'widget-tag' === $type ) {
			// Search tag names LIKE $key_input.
			$tags = get_tags( array(
				'name__like' => $key_input,
				'hide_empty' => false,
			) );

			foreach ( $tags as $tag ) {
				$related[] = array(
					'id'    => $tag->term_id,
					'title' => $tag->name,
				);
			}
		} else {
			// Search page names like $key_input.
			$pages = get_pages();

			foreach ( $pages as $page ) {
				if ( false !== stripos( $page->post_title, $key_input ) && $this->not_in_list( $page->ID ) ) {
					$related[] = array(
						'id'    => $page->ID,
						'title' => $page->post_title,
					);
				}
			}
		} // End if().

		// Create output list if any results exist.
		if ( count( $related ) > 0 ) {
			foreach ( $related as $items ) {
				$item_option[] = sprintf(
					'<li class="ta-' . $type . '-item" data-id="%1$d">%2$s</li>',
					(int) $items['id'],
					esc_html( $items['title'] )
				);
			}

			wp_send_json_success( $item_option );
		} else {
			wp_send_json_error( 'no results' );
		}
	}

	/**
	 * Helper function to determine if page is in the list already.
	 *
	 * @param integer $id The page id.
	 *
	 * @return bool
	 */
	private function not_in_list( $id ) {
		$current_pages = array_values( get_option( 'prodigy-commerce_shop-page' ) );

		if ( ! is_array( $current_pages ) || array() === $current_pages || ! in_array( (string) $id, $current_pages, true ) ) {
			return true;
		}

		return false;
	}

	/**
	 * Register the product list widget.
	 *
	 * @action widgets_init
	 */
	public function register_widgets() {
		register_widget( $this->product_list_widget );
		register_widget( $this->cart_widget );
	}

	/**
	 * Add category feature image field to product categories.
	 *
	 * @param object $cat The category in question.
	 * @action pc_product_cat_edit_form_fields
	 */
	public function pc_product_cat_taxonomy_custom_fields( $cat ) {
		wp_enqueue_media();

		$t_id = $cat->term_id; // Get the ID of the term you're editing.
		$term_meta = get_option( "category_image_{$t_id}" ); // Do the check.

		include_once( "{$this->plugin->dir_path}/templates/cat-feature-image.php" );
	}

	/**
	 * A callback function to save our extra taxonomy field(s)
	 *
	 * @param $term_id
	 * @action edited_pc_product_cat
	 */
	public function save_taxonomy_custom_fields( $term_id ) {
		if ( isset( $_POST['term_meta']['category_image'] ) ) { // WPCS: CSRF ok.
			// Save the option array.
			update_option( "category_image_{$term_id}", $_POST['term_meta']['category_image'] ); // WPCS: CSRF ok.
		}
	}

}
