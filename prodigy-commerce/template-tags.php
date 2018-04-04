<?php
/**
 * Helper functions for store use.
 *
 * @package ProdigyCommerce
 */

/**
 * Get all products based on args you pass.
 *
 * @param integer $count The count limit of products to return.
 * @param string  $category The provided product category.
 * @param string  $tag The provided product tag.
 * @param bool    $sale_items Query only items on sale.
 * @param integer $offset The offset number.
 * @param bool    $show_categories Show categories instead of products.
 */
function get_pc_products( $count = -1, $category = '', $tag = '', $sale_items = false, $offset = '', $show_categories = false ) {
	if ( $show_categories ) {
		return get_pc_categories( $count );
	}

	$tax_query = array();
	$meta_key = '';
	$meta_value = '';
	$meta_compare = '';
	$pagination = get_option( 'prodigy-commerce_pagination' );
	$pagination = null !== $pagination && false !== $pagination ? $pagination : '';
	$count = 'paginate' === $count ? $pagination : $count;

	// If only a tag or category is passed.
	if ( '' !== $category && '' === $tag ) {
		$tax_query = array(
			array(
				'taxonomy' => 'pc_product_cat',
				'terms'    => array( $category ),
				'operator' => 'IN',
			),
		);
	} elseif ( '' === $category && '' !== $tag ) {
		$tax_query = array(
			array(
				'taxonomy' => 'pc_product_tag',
				'terms'    => array( $tag ),
				'operator' => 'IN',
			),
		);
	}

	// If both category and tag are passed.
	if ( '' !== $category && '' !== $tag ) {
		$tax_query = array(
			array(
				'taxonomy' => 'pc_product_cat',
				'terms'    => array( $category ),
				'operator' => 'IN',
			),
			array(
				'taxonomy' => 'pc_product_tag',
				'terms'    => array( $tag ),
				'operator' => 'IN',
			),
		);
	}

	if ( $sale_items ) {
		$meta_key = '_pc_sale_price';
		$meta_value = '';
		$meta_compare = '!==';
	}

	// Get all products for template.
	$product_args = array(
		'numberposts'      => $count,
		'post_type'        => 'pc_product',
		'offset'           => $offset,
		'tax_query'        => $tax_query,
		'meta_key'         => $meta_key,
		'meta_value'       => $meta_value,
		'meta_compare'     => $meta_compare,
		'suppress_filters' => false,
	);

	$products = get_posts( $product_args );

	return $products;
}

/**
 * Get the product price.  If sale price entered cross out regular and return both.
 *
 * @param integer $productid The product id in question.
 * @param bool    $echo Echo the price or return.
 * @param bool    $shortcode Shortcode override to hide price.
 */
function get_pc_price( $productid, $echo = true, $shorcode = true, $sale = true ) {
	// If shorcode disables price just return.
	if ( ! $shorcode ) {
		return;
	}

	// Get price and sale price.
	$price = get_post_meta( $productid, '_pc_regular_price', true );
	$sale_price = get_post_meta( $productid, '_pc_sale_price', true );
	$currency = apply_filters( 'pc_currency_symbol', '' );
	$price = '' !== $price && null !== $price && false !== $price ? number_format( (float) $price, 2, '.', '' ) : '';
	$sale_price = '' !== $sale_price && null !== $sale_price && false !== $sale_price ? number_format( (float) $sale_price, 2, '.', '' ) : '';
	$price_class = '';

	if ( '' !== $sale_price ) {
		$price_class = 'on-sale ';
	}

	if ( ! $echo ) {
		if ( $sale ) {
			$price_html = '' !== $sale_price ? '<div class="pc-sale-price">' . esc_html( $currency . $sale_price ) . '</div><div class="' . esc_attr( $price_class ) . 'pc-price">' . esc_html( $currency . $price ) . '</div>' : '<div class="' . esc_attr( $price_class ) . 'pc-price">' . esc_html( $currency . $price ) . '</div>';
		} else {
			$price_html = '' !== $sale_price ? '<div class="pc-sale-price">' . esc_html( $currency . $sale_price ) . '</div>' : '<div class="' . esc_attr( $price_class ) . 'pc-price">' . esc_html( $currency . $price ) . '</div>';
		}

		return $price_html;
	}
	?>

	<?php if ( '' !== $sale_price ) : ?>
		<div class="pc-sale-price"><?php echo esc_html( $currency . $sale_price ); ?></div>
	<?php endif; ?>

	<div class="<?php echo esc_attr( $price_class ); ?>pc-price"><?php echo esc_html( $currency . $price ); ?></div>
	<?php
}

/**
 * Get the breadcrumb according to the page you are on.
 *
 * @param object $page_obj The current page post object.
 * @param bool   $echo Should the function return or echo.
 */
function get_pc_breadcrumbs( $page_obj, $echo = false ) {
	$is_tax = is_tax( 'pc_product_cat' );
	$store = get_option( 'prodigy-commerce_shop-page' );
	$show_breadcrumb = 'on' === get_option( 'prodigy-commerce_breadcrumb' ) ? true : false;
	$storeid = array_keys( $store )[0];
	$storetitle = array_values( $store )[0];
	$store_url = get_post_permalink( $storeid );
	$category = get_the_terms( $page_obj->ID, 'pc_product_cat' );
	$spacer = apply_filters( 'pc_breadcrumb_spacer', '' );
	$breadcrumb_html = '<ul class="breadcrumb-list">';
	$breadcrumb_html .= "<li><a href='{$store_url}'>{$storetitle}</a>";

	if ( $storeid !== $page_obj->ID ) {
		$breadcrumb_html .= $spacer;
	}

	$breadcrumb_html .= '</li>';

	if ( $category ) {
		$category_url = get_term_link( $category[0]->term_id, 'pc_product_cat' );
		$breadcrumb_html .= "<li><a href='{$category_url}'>{$category[0]->name}</a>";

		if ( ! is_archive() && ! $is_tax ) {
			$breadcrumb_html .= $spacer;
		}

		$breadcrumb_html .= '</li>';
	}

	$breadcrumb_html .= ! is_archive() && ! is_tax( 'pc_product_cat' ) && $storeid !== $page_obj->ID ? "<li>{$page_obj->post_title}</li>" : '';
	$breadcrumb_html .= '</ul>';

	if ( ! $echo && $show_breadcrumb ) {
		return wp_kses_post( $breadcrumb_html );
	} elseif ( $show_breadcrumb ) {
		echo wp_kses_post( $breadcrumb_html );
	}
}

/**
 * Return the cart button defined by options and current product settings.
 *
 * @param integer $prodid The specified product id.
 * @param bool    $echo Whether to echo or return.
 * @param bool    $shortcode Shortcode show button override.
 */
function get_pc_cart_button( $prodid, $echo = true, $shortcode = true ) {
	$product_meta = get_post_meta( $prodid, '_pc_product_general', true );
	$cart_settings = get_option( 'prodigy-commerce_add-to-cart' );
	$cart_settings = '' !== $cart_settings && null !== $cart_settings && false !== $cart_settings ? $cart_settings : '';

	if ( is_pc_store_page() && 'on' !== $cart_settings['show-button'] || ! $shortcode ) {
		return;
	}

	if ( ! $echo ) {
		$cart_html = '<div class="pc-add-to-cart">';
		$cart_html .= '<button id="' . esc_attr( $prodid ) . '" style="padding: ' . esc_attr( $cart_settings['padding'] ) . 'px; border-radius: ' . esc_attr( $cart_settings['rounded'] ) . 'px; background: ' . esc_attr( $cart_settings['color'] ) . '; color: ' . esc_attr( $cart_settings['font-color'] ) . ';">';
		$cart_html .= esc_html( $product_meta['pc-cart-button'] );
		$cart_html .= '</button>';
		$cart_html .= '</div>';

		return $cart_html;
	}
	?>
	<div class="pc-add-to-cart">
		<button id="<?php echo esc_attr( $prodid ); ?>" style="padding: <?php echo esc_attr( $cart_settings['padding'] ); ?>px; border-radius: <?php echo esc_attr( $cart_settings['rounded'] ); ?>px; background: <?php echo esc_attr( $cart_settings['color'] ); ?>; color: <?php echo esc_attr( $cart_settings['font-color'] ); ?>;">
			<?php echo esc_html( $product_meta['pc-cart-button'] ); ?>
		</button>
	</div>
	<?php
}

/**
 * Helper function to determine if the current page is the store page.
 */
function is_pc_store_page() {
	global $post;

	$store = get_option( 'prodigy-commerce_shop-page' );
	$storeid = array_keys( $store )[0];

	if ( $post->ID === $storeid ) {
		return true;
	}

	return false;
}

/**
 * Function to get a list of current cart items.
 *
 * @return array
 */
function get_pc_cart_items() {
	$cartid = isset( $_COOKIE['pc_cart'] ) ? $_COOKIE['pc_cart'] : '';
	$api_token = get_option( 'prodigy-commerce_api-field' );

	if ( '' === $cartid ) {
		return apply_filters( 'pc_empty_cart_message', '' );
	}

	$url = 'https://demo.global.cardpaysolutions.com/carts/' . $cartid;
	$get_args = array(
		'method' => 'GET',
		'headers' => array(
			'Content-Type' => 'application/json',
			'Authorization' => 'Token token=' . $api_token,
		),
	);

	$response = wp_safe_remote_get( $url, $get_args );

	if ( is_wp_error( $response ) ) {
		return;
	}

	$products = json_decode( $response['body'], ARRAY_N );

	if ( is_array( $products['line_items'] ) ) {
		foreach ( $products['line_items'] as $product ) {
			$post_obj = get_post( (int) $product['ext_id'] );
			$meta = get_post_meta( $post_obj->ID, '_pc_product_general', true );
			$regular_price = get_post_meta( $post_obj->ID, '_pc_regular_price', true );
			$sale_price = get_post_meta( $post_obj->ID, '_pc_sale_price', true );
			$product_array[] = array(
				'id'          => $product['id'],
				'ext_id'      => $post_obj->ID,
				'quantity'    => $product['quantity'],
				'title'       => $post_obj->post_title,
				'price'       => $regular_price,
				'sale-price'  => $sale_price,
				'description' => $meta['short-description'],

			);
		}
	} else {
		$product_array = apply_filters( 'pc_empty_cart_message', '' );
	}

	return $product_array;
}

/**
 * Get the current cart subtotal.
 *
 * @param array $line_items The current cart line items.
 * @param bool  $echo Echo the result or return.
 */
function get_pc_cart_subtotal( $line_items, $echo = false ) {
	foreach ( $line_items as $product ) {
		if ( '' !== $product['sale-price'] ) {
			$subtotal[] = (float) $product['sale-price'] * (int) $product['quantity'];
		} else {
			$subtotal[] = (float) $product['price'] * (int) $product['quantity'];
		}
	}

	return array_sum( $subtotal );
}

/**
 * Get the current cart subtotal.
 *
 * @param array $line_items The current cart line items.
 * @param bool  $echo Echo the result or return.
 */
function get_pc_cart_quantity( $line_items, $echo = false ) {
	foreach ( $line_items as $product ) {
		$quantity[] = (int) $product['quantity'];
	}

	return array_sum( $quantity );
}

/**
 * Return pagination html for the provided product count.
 *
 * @param bool    $echo To echo or not to echo.
 * @param integer $category The category if any.
 */
function get_pc_pagination( $echo = false, $category = '' ) {
	$products = get_pc_products( -1, $category );
	$pagination_count = get_option( 'prodigy-commerce_pagination' );
	$pagination_count = null !== $pagination_count && false !== $pagination_count ? $pagination_count : '';

	if ( '' === $pagination_count || (int) $pagination_count >= (int) count( $products ) ) {
		return;
	}

	$page_count = intval( (int) count( $products ) / (int) $pagination_count );
	$current = '';
	$pagination_html = '<div class="pc-pagination-wrap">';
	$pagination_html .= '<div class="pc-product-count">';
	$pagination_html .= (int) count( $products ) . ' ';
	$pagination_html .= apply_filters( 'pc_word_next_to_count', '' );
	$pagination_html .= '</div>';
	$pagination_html .= '<ul>';

	for ( $x = 1; $x <= $page_count; $x ++ ) {
		if ( 1 === $x ) {
			$pagination_html .= '<li class="pc-page-number pc-current-page">' . (int) $x . '</li>';
		} else {
			$pagination_html .= '<li class="pc-page-number">' . (int) $x . '</li>';
		}
	}

	$pagination_html .= '</ul>';
	$pagination_html .= '<span class="pc-next-page">';
	$pagination_html .= '>';
	$pagination_html .= '</span>';

	if ( ! $echo ) {
		return $pagination_html;
	}

	echo wp_kses_post( $pagination_html );
}

/**
 * Get the current product categories.
 *
 * @param integer $count The count to return.
 */
function get_pc_categories( $count = '' ) {
	$count = -1 !== $count ? $count : '';

	$product_cat = get_terms( array(
		'taxonomy' => 'pc_product_cat',
		'hide_empty' => false,
	) );

	return $product_cat;
}

/**
 * Get the product's variables.
 *
 * @param integer $productid The specific product to get variable for.
 * @param bool    $html Whether to return drop down html or just array.
 * @param bool    $echo Whether to echo or return
 */
function get_pc_variables( $productid = '', $echo = true, $html = true ) {
	global $post;

	$productid = '' !== $productid ? $productid : $post->ID;
	$variables = get_post_meta( $productid, '_pc_variable_settings', true );

	if ( ! $html ) {
		return $variables;
	}

	$variable_html = '<div class="pc-variable-wrap">';

	foreach ( $variables as $name => $variable ) {
		if ( 'variant_options' !== $name ) {
			$variable_html .= '<h4>';
			$variable_html .= $name;
			$variable_html .= '</h4>';
			$variable_html .= '<select class="pc-variable-select">';

			foreach ( $variable as $type => $price ) {
				$variable_html .= '<option value="' . $type . '">' . $type . '</option>';
			}

			$variable_html .= '</select>';
		}
	}

	$variable_html .= '</div>';

	if ( $echo ) {
		echo $variable_html;
	} else {
		return $variables_html;
	}

}