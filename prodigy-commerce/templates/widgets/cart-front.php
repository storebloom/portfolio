<?php
/**
 * Cart Front-end Template
 *
 * The template wrapper for the cart widget front-end.
 *
 * @package ProdigyCommerce
 */

?>
<?php
echo wp_kses_post( $args['before_widget'] . $args['before_title'] . $title . $args['after_title'] );
$class_dd = ! $dropdown ? ' pc-cart-nondropdown' : '';
$currency = apply_filters( 'pc_currency_symbol', '' );
?>

<div class="pc-cart-shortcode-wrap<?php echo esc_attr( $class_dd ); ?>">
	<?php
	$class = 'pc-cart-shortcode-wrapper';
	$pc_empty_class = ! $show_count || ! is_array( $line_items ) ? ' pc-empty-cart' : '';

	if ( $dropdown ) :
		$class = 'pc-cart-dropdown';
	?>
		<div class="pc-cart-top-icon">
			<span class="pc-cart-dropdown-icon fa <?php echo esc_attr( $icon ); ?>"></span>
			<div class="pc-cart-spinner">
				<img src="<?php echo esc_attr( $this->plugin->dir_url . 'assets/spinner.gif' ); ?>">
			</div>
			<div class="pc-cart-count<?php echo esc_attr( $pc_empty_class ); ?>">
				<?php echo $show_count && is_array( $line_items ) ? esc_html( $count ) : '' ?>
			</div>
		</div>
	<?php endif; ?>

	<div class="<?php echo esc_attr( $class ); ?>">

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

			<?php if ( $show_subtotal ) : ?>
				<div class="pc-cart-subtotal">
					<span>
						<?php echo esc_html__( 'Subtotal: ', 'prodigy-commerce' ) . esc_html( $currency . get_pc_cart_subtotal( $line_items, false ) ); ?>
					</span>
				</div>
			<?php endif; ?>
			<a href="<?php echo esc_url( $cart_page_url ); ?>">
				<button type="button" class="pc-check-out">
					<?php echo esc_html( apply_filters( 'pc_view_cart_copy', '' ) ); ?>
				</button>
			</a>
		<?php else : ?>
			<?php echo esc_html( apply_filters( 'pc_empty_cart_message', '' ) ); ?>
		<?php endif; ?>
	</div>
</div>

<?php echo wp_kses_post( $args['after_widget'] ); ?>
