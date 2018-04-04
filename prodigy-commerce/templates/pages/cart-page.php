<?php
/**
 * Cart page Template
 *
 * The template wrapper for the cart page.
 *
 * @package ProdigyCommerce
 */

$currency = apply_filters( 'pc_currency_symbol', '' );

// Get line items.
$line_items = get_pc_cart_items();

// Get Store Url.
$store = get_option( 'prodigy-commerce_shop-page' );
$storeid = array_keys( $store )[0];
$store_url = get_post_permalink( $storeid, true );

get_header();
?>
<div id="pc-cart-page-wrapper">
	<h1 class="page-title"><?php echo esc_html( the_title() ); ?></h1>

	<div id="pc-cart-page">
		<div id="primary" class="content-area">
			<main id="main" class="site-main">
				<h4>
					Sign In
				</h4>
				<div id="pc-checkout-credentials">
					<input type="text" placeholder="Email">
					<input type="text" placeholder="Password">
				</div>

				<button type="button" class="pc-check-out">
					<?php echo esc_html__( 'Sign In', 'prodigy-commerce' ); ?>
				</button>

				<a href="#" class="pc-guest-check-out">
					<?php echo esc_html__( 'Checkout as Guest', 'prodigy-commerce' ); ?>
				</a>

				<a href="<?php echo esc_url( $store_url ); ?>" class="pc-continue-shopping">
					<?php echo esc_html__( 'Continue Shopping', 'prodigy-commerce' ); ?>
				</a>
			</main>
		</div>
		<div id="secondary">
			<h3>
				<?php echo esc_html__( 'Order Summary:', 'prodigy-commerce' ); ?>
			</h3>

			<?php if ( is_array( $line_items ) ) : ?>
				<ul class="pc-cart-line-items">
					<?php foreach ( $line_items as $cart ) : ?>
						<li>
							<a href="<?php echo esc_url( get_post_permalink( $cart['ext_id'] ) ); ?>">
								<div class="pc-cart-thumb">
									<img src= "<?php echo esc_url( get_the_post_thumbnail_url( $cart['ext_id'] ) ); ?>">
								</div>
							</a>

							<div class="pc-cart-item-info">
								<a href="<?php echo esc_url( get_post_permalink( $cart['ext_id'] ) ); ?>">
									<div class="pc-cart-item-title">
										<?php echo esc_html( $cart['title'] ); ?>
									</div>
								</a>

								<div class="pc-cart-item-price">
									<input id="<?php echo esc_attr( $cart['ext_id'] ); ?>" class="pc-cart-quantity-control" type="number" value="<?php echo esc_html( $cart['quantity'] ); ?>" min="1">

									<?php echo wp_kses_post( get_pc_price( $cart['ext_id'], false, true, false ) ); ?>
								</div>

								<span id="<?php echo esc_attr( $cart['id'] ); ?>" class="pc-line-item-delete">
									<?php echo esc_html__( 'Remove', 'prodigy-commerce' ); ?>
								</span>
							</div>
						</li>
					<?php endforeach; ?>
				</ul>

				<div class="pc-cart-subtotal">
						<span>
							<?php echo esc_html__( 'Subtotal: ', 'prodigy-commerce' ) . esc_html( $currency . get_pc_cart_subtotal( $line_items, false ) ); ?>
						</span>
				</div>
			<?php else : ?>
				<?php echo esc_html( apply_filters( 'pc_empty_cart_message', '' ) ); ?>
			<?php endif; ?>
		</div>
	</div>
</div>
<?php
get_footer();
