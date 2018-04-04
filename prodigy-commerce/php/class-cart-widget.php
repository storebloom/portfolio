<?php
/**
 * Cart Widget.
 *
 * @package ProdigyCommerce
 */

namespace ProdigyCommerce;

/**
 * Cart Widget Class
 *
 * @package ProdigyCommerce
 */
class Cart_Widget extends \WP_Widget {

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
			'classname' => 'pc_cart_widget',
			'description' => esc_html__( 'Add a list of cart items.', 'prodigy-commerce' ),
		);
		parent::__construct(
			'pc_cart_widget',
			'Prodigy Commerce Cart',
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
		$show_count = ! empty( $instance['show-count'] ) && 'on' === $instance['show-count'] ? true : false;
		$dropdown = ! empty( $instance['dropdown'] ) && 'on' === $instance['dropdown'] ? true : false;
		$show_subtotal = ! empty( $instance['show-subtotal'] ) && 'on' === $instance['show-subtotal'] ? true : false;
		$icon = ! empty( $instance['icon'] ) ? esc_html( $instance['icon'] ) : 'fa-shopping-cart';
		$line_items = get_pc_cart_items();
		$count = get_pc_cart_quantity( $line_items );
		$cart_page = get_option( 'prodigy-commerce_cart-page' );
		$cart_page = null !== $cart_page && false !== $cart_page ? $cart_page : '';
		$cart_page_url = '' !== $cart_page && is_array( $cart_page ) ? get_permalink( array_keys( $cart_page )[0] ) : '';

		wp_enqueue_style( "{$this->plugin->assets_prefix}-store" );

		include_once( "{$this->plugin->dir_path}/templates/widgets/cart-front.php" );
	}

	/**
	 * The widget form.
	 *
	 * @param array $instance The current widget instance.
	 */
	public function form( $instance ) {
		$title = ! empty( $instance['title'] ) ? esc_html( $instance['title'] ) : '';
		$show_count = ! empty( $instance['show-count'] ) ? esc_html( $instance['show-count'] ) : '';
		$dropdown = ! empty( $instance['dropdown'] ) ? esc_html( $instance['dropdown'] ) : '';
		$show_subtotal = ! empty( $instance['show-subtotal'] ) ? esc_html( $instance['show-subtotal'] ) : '';
		$icon = ! empty( $instance['icon'] ) ? esc_html( $instance['icon'] ) : 'fa-shopping-cart';

		include( "{$this->plugin->dir_path}/templates/widgets/cart.php" );
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
		$instance['show-count'] = strip_tags( $new_instance['show-count'] );
		$instance['dropdown'] = strip_tags( $new_instance['dropdown'] );
		$instance['show-subtotal'] = strip_tags( $new_instance['show-subtotal'] );
		$instance['icon'] = strip_tags( $new_instance['icon'] );

		return $instance;
	}
}
