<?php
/**
 * Shortcodes.
 *
 * @package ProdigyCommerce
 */

namespace ProdigyCommerce;

/**
 * Shortcodes Class
 *
 * @package ProdigyCommerce
 */
class Shortcodes {

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
	 * Get products shortcodes.
	 *
	 * @param array $attr The shortcode attribute values.
	 * @shortcode get-pc-products
	 */
	public function get_pc_products( $attr ) {
		// The shortcode attribute values.
		$count = isset( $attr['count'] ) ? intval( wp_unslash( $attr['count'] ) ) : '';
		$shortcode = isset( $attr['cart-button'] ) && 'false' === $attr['cart-button'] ? false : true;
		$show_price = isset( $attr['price'] ) && 'false' === $attr['price'] ? false : true;
		$thumbnail = isset( $attr['thumbnail-width'] ) ? intval( wp_unslash( $attr['thumbnail-width'] ) ) . 'px' : '';
		$category = isset( $attr['category'] ) ? sanitize_text_field( wp_unslash( $attr['category'] ) ) : '';
		$show_categories = isset( $attr['show-categories'] ) && 'true' === $attr['show-categories'] ? true : false;
		$tag = isset( $attr['tag'] ) ? sanitize_text_field( wp_unslash( $attr['tag'] ) ) : '';
		$sale_items = isset( $attr['sale-items'] ) && 'true' === $attr['sale-items'] ? true : false;
		$custom_css = get_option( 'prodigy-commerce_custom-css' );
		$custom_css = null !== $custom_css && false !== $custom_css ? $custom_css : '';

		wp_enqueue_style( "{$this->plugin->assets_prefix}-store" );
		wp_add_inline_style( "{$this->plugin->assets_prefix}-store", $custom_css );

		$products = get_pc_products( $count, $category, $tag, $sale_items, '', $show_categories );
		$product_html = '<ul class="pc-product-list">';

		foreach ( $products as $product ) {
			$id = false === $show_categories ? $product->ID : $product->term_id;
			$title = false === $show_categories ? $product->post_title : $product->name;
			$url = false === $show_categories ? get_post_permalink( $id ) : get_bloginfo( 'url' ) . '/' . $product->slug . '/';

			// Get product_meta.
			$product_meta = get_post_meta( $id, '_pc_product_general', true );

			// Get thumbnail url.
			$image_url = false === $show_categories ? get_the_post_thumbnail_url( $id, 'medium' ) : get_option( 'category_image_' . $id );

			$product_html .= '<li style="max-width: ' . esc_attr( $thumbnail ) . ';" class="pc-product-item">';
			$product_html .= '<a href="' . esc_url( $url ) . '">';
			$product_html .= '<div style="overflow: hidden; max-width: ' . esc_attr( $thumbnail ) . '; height: auto;" class="pc-product-store-image">';
			$product_html .= '<img src="' . esc_url( $image_url ) . '" class="pc-cat-thumb" style="max-width: ' . esc_attr( $thumbnail ) . ';"/>';
			$product_html .= '</div>';
			$product_html .= '<div class="pc-product-title">';
			$product_html .= esc_html( $title );
			$product_html .= '</div>';
			$product_html .= '</a>';

			if ( false === $show_categories ) {
				$product_html .= '<hr>';

				if ( '' !== $product_meta['pc-short-description'] ) {
					$product_html .= '<div class="pc-product-description" style="max-width: ' . esc_attr( $thumbnail ) . ';">';
					$product_html .= esc_html( $product_meta['pc-short-description'] );
					$product_html .= '</div>';
				}

				$product_html .= '<div class="pc-product-price">';
				$product_html .= get_pc_price( $id, false, $show_price );
				$product_html .= '</div>';
				$product_html .= get_pc_cart_button( $id, false, $shortcode );
			}

			$product_html .= '</li>';
		} // End foreach().

		$product_html .= '</ul>';

		return $product_html;
	}

	/**
	 * Get the current product or specified product ids thumbnail.
	 *
	 * @param array $attr The shortcode attribute values.
	 * @shortcode get-pc-thumbnail
	 */
	public function get_product_thumbnail( $attr ) {
		global $post;

		$custom_css = get_option( 'prodigy-commerce_custom-css' );
		$custom_css = null !== $custom_css && false !== $custom_css ? $custom_css : '';

		wp_enqueue_style( "{$this->plugin->assets_prefix}-store" );
		wp_add_inline_style( "{$this->plugin->assets_prefix}-store", $custom_css );

		$product_id = isset( $attr['id'] ) ? intval( wp_unslash( $attr['id'] ) ) : $post->ID;
		$thumbnails = isset( $attr['additional-images'] ) && 'true' === $attr['additional-images'] ? true : false;
		$width = isset( $attr['width'] ) ? sanitize_text_field( wp_unslash( $attr['width'] ) ) : '';
		$height = isset( $attr['height'] ) ? sanitize_text_field( wp_unslash( $attr['height'] ) ) : '';

		// Build the html.
		$thumbnail_html = '<div style="max-width: ' . $width . ';" class="pc-pdp-image">';
		$thumbnail_html .= '<img id="main-pdp-image" src="' . esc_url( get_the_post_thumbnail_url( $product_id ) ) . '">';
		$thumbnail_html .= '<div class="pc-thumbnails">';

		if ( $thumbnails ) {
			wp_enqueue_script( "{$this->plugin->assets_prefix}-store" );
			wp_add_inline_script( "{$this->plugin->assets_prefix}-store", sprintf( 'Store.boot( %s );',
				wp_json_encode( array(
					'nonce' => wp_create_nonce( $this->plugin->meta_prefix ),
				) )
			) );

			$product_images = get_post_meta( $product_id,  '_pc_product_image', true );
			$add_width = count( $product_images ) + 1;
			$add_width = 100 / $add_width;
			$add_width = $add_width - 5;
			$add_width = $add_width . '%';

			if ( 1 < count( $product_images ) ) {
				$thumbnail_html .= '<div style="width: ' . $add_width . ';" class="pdp-thumbnail">';
				$thumbnail_html .= '<img src="' . esc_url( get_the_post_thumbnail_url( $product_id ) ) . '">';
				$thumbnail_html .= '</div>';
			}

			foreach ( $product_images as $image_url ) {
				$thumbnail_html .= '<div style="width: ' . $add_width . ';" class="pdp-thumbnail" >';
				$thumbnail_html .= '<img src="' . esc_url( $image_url ) . '" >';
				$thumbnail_html .= '</div>';
			}
		}

		$thumbnail_html .= '</div>';
		$thumbnail_html .= '</div>';

		return $thumbnail_html;
	}

	/**
	 * Get the current product or specified product ids description.
	 *
	 * @param array $attr The shortcode attribute values.
	 * @shortcode get-pc-description
	 */
	public function get_product_description( $attr ) {
		global $post;

		$custom_css = get_option( 'prodigy-commerce_custom-css' );
		$custom_css = null !== $custom_css && false !== $custom_css ? $custom_css : '';

		wp_enqueue_style( "{$this->plugin->assets_prefix}-store" );
		wp_add_inline_style( "{$this->plugin->assets_prefix}-store", $custom_css );

		$product_id  = isset( $attr['id'] ) ? intval( wp_unslash( $attr['id'] ) ) : $post->ID;
		$short = isset( $attr['short'] ) && 'true' === $attr['short'] ? true : false;
		$product_obj = get_post( $product_id );
		$short_desc  = get_post_meta( $product_id, '_pc_product_general', true );

		if ( ! $short ) {
			return wp_kses_post( $product_obj->post_content );
		} elseif ( '' !== $short_desc['pc-short-description'] ) {
			return esc_html( $short_desc['pc-short-description'] );
		}
	}

	/**
	 * Get the current product or specified product ids price.
	 *
	 * @param array $attr The shortcode attributed values.
	 * @shortcode get-pc-price
	 */
	public function get_product_price( $attr ) {
		global $post;

		$custom_css = get_option( 'prodigy-commerce_custom-css' );
		$custom_css = null !== $custom_css && false !== $custom_css ? $custom_css : '';

		wp_enqueue_style( "{$this->plugin->assets_prefix}-store" );
		wp_add_inline_style( "{$this->plugin->assets_prefix}-store", $custom_css );

		$product_id = isset( $attr['id'] ) ? intval( wp_unslash( $attr['id'] ) ) : $post->ID;

		return get_pc_price( $product_id, false );
	}

	/**
	 * Get the current product or specified product ids cart button.
	 *
	 * @param array $attr The shortcode attributed values.
	 * @shortcode get-pc-cart-button
	 */
	public function get_product_cart_button( $attr ) {
		global $post;

		$custom_css = get_option( 'prodigy-commerce_custom-css' );
		$custom_css = null !== $custom_css && false !== $custom_css ? $custom_css : '';

		wp_enqueue_style( "{$this->plugin->assets_prefix}-store" );
		wp_add_inline_style( "{$this->plugin->assets_prefix}-store", $custom_css );

		$product_id = isset( $attr['id'] ) ? intval( wp_unslash( $attr['id'] ) ) : $post->ID;

		return get_pc_cart_button( $product_id, false );
	}

	/**
	 * Get the current user's cart.
	 *
	 * @param array $attr The shortcode attributed values.
	 * @shortcode get-pc-cart
	 */
	public function get_pc_cart( $attr ) {
		$dropdown     = isset( $attr['dropdown'] ) && 'true' === $attr['dropdown'] ? true : false;
		$showcount    = isset( $attr['showcount'] ) && 'true' === $attr['showcount'] ? true : false;
		$showsubtotal = isset( $attr['showsubtotal'] ) && 'true' === $attr['showsubtotal'] ? true : false;
		$icon         = isset( $attr['icon'] ) ? sanitize_text_field( wp_unslash( $attr['icon'] ) ) : 'fa-shopping-cart';
		$line_items   = get_pc_cart_items();
		$count = get_pc_cart_quantity( $line_items );
		$currency = apply_filters( 'pc_currency_symbol', '' );
		$cart_html = '<div class="pc-cart-shortcode-wrap">';
		$class = 'pc-cart-shortcode-wrapper';
		$pc_empty_class = ! $showcount || ! is_array( $line_items ) ? esc_attr( ' pc-empty-cart' ) : '';
		$cart_page = get_option( 'prodigy-commerce_cart-page' );
		$cart_page = null !== $cart_page && false !== $cart_page ? $cart_page : '';
		$cart_page_url = '' !== $cart_page && is_array( $cart_page ) ? get_permalink( array_keys( $cart_page )[0] ) : '';

		wp_enqueue_style( "{$this->plugin->assets_prefix}-font-awesome" );
		wp_enqueue_style( "{$this->plugin->assets_prefix}-store" );

		if ( $dropdown ) {
			$cart_html .= '<div class="pc-cart-top-icon">';
			$cart_html .= '<span class="pc-cart-dropdown-icon fa ' . $icon . '"></span>';
			$cart_html .= '<div class="pc-cart-spinner"><img src="' . $this->plugin->dir_url . 'assets/spinner.gif"></div>';
			$cart_html .= '<div class="pc-cart-count' . $pc_empty_class . '">';

			if ( $showcount && is_array( $line_items ) ) {
				$cart_html .= $count;
			}

			$cart_html .= '</div>';
			$cart_html .= '</div>';
			$class = 'pc-cart-dropdown';
		}
		$cart_html .= '<div class="' . $class . '">';

		if ( is_array( $line_items ) ) {
			$cart_html .= '<ul class="pc-cart-line-items">';

			foreach ( $line_items as $cart ) {
				$cart_html .= '<li>';
				$cart_html .= '<a href="' . esc_url( get_post_permalink( $cart['ext_id'] ) ) . '">';
				$cart_html .= '<div class="pc-cart-thumb">';
				$cart_html .= '<img src= "' . esc_url( get_the_post_thumbnail_url( $cart['ext_id'] ) ) . '" >';
				$cart_html .= '</div>';
				$cart_html .= '</a>';
				$cart_html .= '<div class="pc-cart-item-info">';
				$cart_html .= '<a href="' . esc_url( get_post_permalink( $cart['ext_id'] ) ) . '">';
				$cart_html .= '<div class="pc-cart-item-title" >';
				$cart_html .= esc_html( $cart['title'] );
				$cart_html .= '</div>';
				$cart_html .= '</a>';
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

			if ( $showsubtotal ) {
				$cart_html .= '<div class="pc-cart-subtotal">';
				$cart_html .= '<span>' . esc_html__( 'Subtotal: ', 'prodigy-commerce' ) . esc_html( $currency ) . get_pc_cart_subtotal( $line_items, false );
				$cart_html .= '</div>';
			}

			$cart_html .= '<a href="' . esc_url( $cart_page_url ) . '">';
			$cart_html .= '<button type="button" class="pc-check-out">';
			$cart_html .= apply_filters( 'pc_view_cart_copy', '' );
			$cart_html .= '</button>';
			$cart_html .= '</a>';
		} else {
				$cart_html .= apply_filters( 'pc_empty_cart_message', '' );
		} // End if().

		$cart_html .= '</div>';
		$cart_html .= '</div>';

		return $cart_html;
	}
}
