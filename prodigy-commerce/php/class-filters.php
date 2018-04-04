<?php
/**
 * Filters class to house all of the filters for the plugin.
 *
 * @package ProdigyCommerce
 */

namespace ProdigyCommerce;

/**
 * Filters Class.
 *
 * @package ProdigyCommerce
 */
class Filters {

	/**
	 * Plugin instance.
	 *
	 * @var object
	 */
	public $plugin;

	/**
	 * Class constructor.
	 *
	 * @param object $plugin Plugin class.
	 */
	public function __construct( $plugin ) {
		$this->plugin = $plugin;
	}

	/**
	 * Filter the icon used to separate the breadcrumb pages.
	 *
	 * @filter pc_breadcrumb_spacer
	 */
	public function pc_breadcrumb_spacer() {
		$spacer = '<span class="pc-breadcrumb-spacer">';
		$spacer .= '>';
		$spacer .= '</span>';

		return $spacer;
	}

	/**
	 * Filter currency symbol for product prices.
	 *
	 * @filter pc_currency_symbol
	 */
	public function pc_currency_symbol() {
		return esc_html( '$' );
	}

	/**
	 * Filter the text shown when cart is empty.
	 *
	 * @filter pc_empty_cart_message
	 */
	public function pc_empty_cart_message() {
		return esc_html__( 'No items in your cart yet.', 'prodigy-commerce' );
	}

	/**
	 * Filter the word next to the count at bottom of paginated pages.
	 *
	 * @filter pc_word_next_to_count
	 */
	public function pc_word_next_to_count() {
		return esc_html__( 'Products', 'prodigy-commerce' );
	}

	/**
	 * Filter the view cart button copy.
	 *
	 * @filter pc_view_cart_copy
	 */
	public function pc_view_cart_copy() {
		return esc_html__( 'View Cart', 'prodigy-commerce' );
	}

	/**
	 * Filter for the store template.
	 *
	 * @filter pc_store_template
	 */
	public function pc_store_template() {
		return "{$this->plugin->dir_path}/templates/pages/store.php";
	}

	/**
	 * Filter for the category template.
	 *
	 * @filter pc_category_template
	 */
	public function pc_category_template() {
		return "{$this->plugin->dir_path}/templates/pages/category.php";
	}

	/**
	 * Filter for the product template.
	 *
	 * @filter pc_product_template
	 */
	public function pc_product_template() {
		return "{$this->plugin->dir_path}/templates/pages/pdp.php";
	}

	/**
	 * Filter for the cart page template.
	 *
	 * @filter pc_cart_page_template
	 */
	public function pc_cart_page_template() {
		return "{$this->plugin->dir_path}/templates/pages/cart-page.php";
	}

	/**
	 * Filter for the store template.
	 *
	 * @filter pc_module_tabs
	 */
	public function pc_module_tabs() {
	}
}
