<?php
/**
 * Product List Widget.
 *
 * @package ProdigyCommerce
 */

namespace ProdigyCommerce;

/**
 * Product List Widget Class
 *
 * @package ProdigyCommerce
 */
class Product_List_Widget extends \WP_Widget {

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

		$widget_options = array(
			'classname' => 'pc_product_list_widget',
			'description' => esc_html__( 'Add a list of your products to your sidebar.', 'prodigy-commerce' ),
		);
		parent::__construct(
			'pc_product_list_widget',
			'Prodigy Commerce Product List',
			$widget_options
		);
	}

	/**
	 * Create the widget output.
	 *
	 * @param array $args Widget output arguments.
	 * @param array $instance The widget instance.
	 */
	public function widget( $args, $instance ) {
		$title = apply_filters( 'widget_title', $instance['title'] );
		$count = ! empty( $instance['count'] ) ? esc_html( $instance['count'] ) : '';
		$cart_button = ! empty( $instance['cart-button'] ) && 'on' === $instance['cart-button'] ? true : false;
		$price = ! empty( $instance['price'] ) && 'on' === $instance['price'] ? true : false;
		$category = ! empty( $instance['category'] ) ? esc_html( $instance['category'] ) : '';
		$tag = ! empty( $instance['tag'] ) ? esc_html( $instance['tag'] ) : '';
		$sale_items = ! empty( $instance['sale-items'] ) && 'on' === $instance['sale-items'] ? true : false;
		$thumb_width = ! empty( $instance['thumb-width'] ) ? esc_html( $instance['thumb-width'] ) : '';
		$custom_css = get_option( 'prodigy-commerce_custom-css' );
		$custom_css = null !== $custom_css && false !== $custom_css ? $custom_css : '';

		wp_enqueue_style( "{$this->plugin->assets_prefix}-store" );
		wp_add_inline_style( "{$this->plugin->assets_prefix}-store", $custom_css );

		$products = get_pc_products( $count, $category, $tag, $sale_items );

		include_once( "{$this->plugin->dir_path}/templates/widgets/product-list-front.php" );
	}

	/**
	 * The widget form.
	 *
	 * @param array $instance The current widget instance.
	 */
	public function form( $instance ) {
		$title = ! empty( $instance['title'] ) ? esc_html( $instance['title'] ) : '';
		$count = ! empty( $instance['count'] ) ? esc_html( $instance['count'] ) : '';
		$cart_button = ! empty( $instance['cart-button'] ) ? esc_html( $instance['cart-button'] ) : '';
		$price = ! empty( $instance['price'] ) ? esc_html( $instance['price'] ) : '';
		$category = ! empty( $instance['category'] ) ? esc_html( $instance['category'] ) : '';
		$thumb_width = ! empty( $instance['thumb-width'] ) ? esc_html( $instance['thumb-width'] ) : '';
		$tag = ! empty( $instance['tag'] ) ? esc_html( $instance['tag'] ) : '';
		$sale_items = ! empty( $instance['sale-items'] ) ? esc_html( $instance['sale-items'] ) : '';

		include( "{$this->plugin->dir_path}/templates/widgets/product-list.php" );
	}

	/**
	 * Enqueue widget assets.
	 *
	 * @action admin_enqueue_scripts
	 * @param string $hook The current admin page.
	 */
	public function enqueue_widget_assets( $hook ) {
		if ( 'widgets.php' === $hook ) {
			wp_enqueue_style( "{$this->plugin->assets_prefix}-admin" );
			wp_enqueue_script( "{$this->plugin->assets_prefix}-admin" );
			wp_add_inline_script( "{$this->plugin->assets_prefix}-admin", sprintf( 'ProdigyCommerce.boot( %s );',
				wp_json_encode( array(
					'nonce' => wp_create_nonce( $this->plugin->meta_prefix ),
				) )
			) );
		}
	}

	/**
	 * Update database with new info
	 *
	 * @param array $new_instance The new instance of the widget values.
	 * @param array $old_instance The old instance of the widget values.
	 *
	 * @return array
	 */
	public function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		$instance['title'] = strip_tags( $new_instance['title'] );
		$instance['count'] = strip_tags( $new_instance['count'] );
		$instance['cart-button'] = strip_tags( $new_instance['cart-button'] );
		$instance['price'] = strip_tags( $new_instance['price'] );
		$instance['category'] = strip_tags( $new_instance['category'] );
		$instance['thumb-width'] = strip_tags( $new_instance['thumb-width'] );
		$instance['tag'] = strip_tags( $new_instance['tag'] );
		$instance['sale-items'] = strip_tags( $new_instance['sale-items'] );

		return $instance;
	}
}
