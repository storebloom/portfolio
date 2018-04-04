<?php
/**
 * Cart class to house all of the front end cart logic.
 *
 * @package ProdigyCommerce
 */

namespace ProdigyCommerce;

/**
 * Cart Class.
 *
 * @package ProdigyCommerce
 */
class Cart {

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
	 * Enqueue the cart js file for cart functions.
	 *
	 * @action wp_footer
	 */
	public function enqueue_cart_functionality() {
		if ( ! wp_style_is( "{$this->plugin->assets_prefix}-store", 'enqueued' ) ) {
			return;
		}

		wp_enqueue_script( "{$this->plugin->assets_prefix}-cart" );
		wp_add_inline_script( "{$this->plugin->assets_prefix}-cart", sprintf( 'Cart.boot( %s );',
			wp_json_encode( array(
				'nonce'   => wp_create_nonce( $this->plugin->meta_prefix ),
			) )
		) );
	}

	/**
	 * AJAX Call back function to add to or create cart.
	 *
	 * @action wp_ajax_update_cart
	 */
	public function update_cart() {
		check_ajax_referer( $this->plugin->meta_prefix, 'nonce' );

		if ( ! isset( $_POST['id'], $_POST['method'], $_POST['quantity'] ) || '' === $_POST['method'] || '' === $post['id'] ) { // WPCS: input var okay.
			wp_send_json_error( 'Update Cart Failed' );
		}

		$api_field = get_option( 'prodigy-commerce_api-field' );
		$hid = intval( wp_unslash( $_POST['id'] ) ); // WPCS: input var okay.
		$id = intval( wp_unslash( $_POST['extid'] ) ); // WPCS: input var okay.
		$method = sanitize_text_field( wp_unslash( $_POST['method'] ) ); // WPCS: input var okay.
		$quantity = intval( wp_unslash( $_POST['quantity'] ) ); // WPCS: input var okay.
		$cartid = isset( $_COOKIE['pc_cart'] ) ? '/' . $_COOKIE['pc_cart'] : '';

		switch ( $method ) {
			case 'POST' :
				$data = '{
					"cart":{
						"line_items_attributes": [
							{
								"ext_id": "' . $id . '",
								"quantity": "' . $quantity . '"
							}
						]
					}
				}';
				break;

			case 'PUT' :
				$data = '{
					"cart":{
						"line_items_attributes": [
							{
								"ext_id": "' . $id . '",
								"quantity": "' . $quantity . '"
							}
						]
					}
				}';
				break;

			case 'UPDATE' :
				$data = '{
					"cart":{
						"line_items_attributes": [
							{   
								"id" : "' . $hid . '",
								"ext_id": "' . $id . '",
								"quantity": "' . $quantity . '"
							}
						]
					}
				}';

				$method = 'PUT';
				break;
			case 'REMOVE' :
				$data = '{
					"cart":{
						"line_items_attributes": [
							{   
								"id" : "' . $hid . '",
								"_destroy": "1"
							}
						]
					}
				}';

				$method = 'PUT';
				break;
		} // End switch().

		$url = 'https://demo.global.cardpaysolutions.com/carts' . $cartid;
		$args = array(
			'method' => $method,
			'timeout' => 60,
			'redirection' => 5,
			'blocking' => true,
			'headers' => array(
				'Content-Type' => 'application/json',
				'Authorization' => 'Token token=' . $api_field,
			),
			'body' => $data,
		);

		$response = wp_safe_remote_post( $url, $args );
		$response_decoded = json_decode( $response['body'], ARRAY_N );

		if ( ! isset( $_COOKIE['pc_cart'] ) ) {
			setcookie( 'pc_cart', $response_decoded['id'], 30 * DAYS_IN_SECONDS, COOKIEPATH, COOKIE_DOMAIN );
		}
	}

	/**
	 * AJAX callback to return line item html to populate cart.
	 *
	 * @action wp_ajax_add_item_html
	 */
	public function add_item_html() {
		check_ajax_referer( $this->plugin->meta_prefix, 'nonce' );

		if ( ! isset( $_POST['id'], $_POST['showsubtotal'] ) || '' === $post['id'] ) { // WPCS: input var okay.
			wp_send_json_error( 'Return Cart HTML failed.' );
		}

		$id = intval( wp_unslash( $_POST['id'] ) ); // WPCS: input var okay.
		$showsubtotal = sanitize_text_field( wp_unslash( $_POST['showsubtotal'] ) );
		$product_obj = get_post( $id );
		$product_price = get_post_meta( $id, '_pc_regular_price', true );
		$product_sale_price = get_post_meta( $id, '_pc_sale_price', true );
		$product_subtotal = '' === $product_sale_price ? $product_price : $product_sale_price;
		$line_items = get_pc_cart_items();
		$cart_page = get_option( 'prodigy-commerce_cart-page' );
		$cart_page = null !== $cart_page && false !== $cart_page ? $cart_page : '';
		$cart_page_url = '' !== $cart_page && is_array( $cart_page ) ? get_permalink( array_keys( $cart_page )[0] ) : '';

		if ( ! is_array( $line_items ) ) {
			$cart_html .= '<ul class="pc-cart-line-items">';
		}

		$cart_html .= '<li>';
		$cart_html .= '<div class="pc-cart-thumb">';
		$cart_html .= '<img src= "' . esc_url( get_the_post_thumbnail_url( $id ) ) . '" >';
		$cart_html .= '</div>';
		$cart_html .= '<div class="pc-cart-item-info">';
		$cart_html .= '<div class="pc-cart-item-title" >';
		$cart_html .= esc_html( $product_obj->post_title );
		$cart_html .= '</div>';
		$cart_html .= '<div class="pc-cart-item-price">';
		$cart_html .= '<input id="' . $id . '" class="pc-cart-quantity-control" type="number" value="1" min="1">';
		$cart_html .= get_pc_price( $id, false, true, false );
		$cart_html .= '</div>';
		$cart_html .= '<span id="' . esc_attr( $id ) . '" class="pc-line-item-delete">';
		$cart_html .= 'Remove';
		$cart_html .= '</span>';
		$cart_html .= '</div>';
		$cart_html .= '</li>';

		if ( ! is_array( $line_items ) ) {
			$cart_html .= '</ul>';

			if ( 'yes' === $showsubtotal ) {
				$cart_html .= '<div class="pc-cart-subtotal">';
				$cart_html .= '<span>' . esc_html__( 'Subtotal: ', 'prodigy-commerce' ) . '$' . $product_subtotal . '</span>';
				$cart_html .= '</div>';
			}

			$cart_html .= '<a href="' . esc_url( $cart_page_url ) . '">';
			$cart_html .= '<button type="button" class="pc-check-out">';
			$cart_html .= esc_html( apply_filters( 'pc_view_cart_copy', '' ) );
			$cart_html .= '</button>';
			$cart_html .= '</a>';
		}

		wp_send_json_success( $cart_html );
	}

	/**
	 * Return cart data to cart.
	 *
	 * @action wp_ajax_get_cart_data
	 */
	public function get_cart_data() {
		check_ajax_referer( $this->plugin->meta_prefix, 'nonce' );

		$line_items   = get_pc_cart_items();
		$count = get_pc_cart_quantity( $line_items );

		if ( is_array( $line_items ) ) {
			$cart_html .= '<ul class="pc-cart-line-items">';

			foreach ( $line_items as $cart ) {
				$cart_html .= '<li>';
				$cart_html .= '<div class="pc-cart-thumb">';
				$cart_html .= '<img src= "' . esc_url( get_the_post_thumbnail_url( $cart['ext_id'] ) ) . '" >';
				$cart_html .= '</div>';
				$cart_html .= '<div class="pc-cart-item-info">';
				$cart_html .= '<div class="pc-cart-item-title" >';
				$cart_html .= esc_html( $cart['title'] );
				$cart_html .= '</div>';
				$cart_html .= '<div class="pc-cart-item-price">';
				$cart_html .= '<input id="' . $cart['ext_id'] . '" class="pc-cart-quantity-control" type="number" value="' . esc_html( $cart['quantity'] ) . '" min="1">';
				$cart_html .= get_pc_price( $cart['ext_id'], false, true, false );
				$cart_html .= '</div>';
				$cart_html .= '<span id="' . esc_attr( $cart['id'] ) . '" class="pc-line-item-delete">';
				$cart_html .= 'Remove';
				$cart_html .= '</span>';
				$cart_html .= '</div>';
				$cart_html .= '</li>';
			}

			$cart_html .= '</ul>';
			$cart_html .= '<div class="pc-cart-subtotal">';
			$cart_html .= '<span>' . esc_html__( 'Subtotal: ', 'prodigy-commerce' ) . '$' . get_pc_cart_subtotal( $line_items, false );
			$cart_html .= '</div>';

			$cart_html .= '<button type="button" class="pc-check-out">';
			$cart_html .= esc_html( apply_filters( 'pc_view_cart_copy', '' ) );
			$cart_html .= '</button>';
		} else {
			$cart_html .= apply_filters( 'pc_empty_cart_message', '' );
		} // End if().

		wp_send_json_success( $cart_html );
	}
}
