<?php
/**
 * Product List Front-end Template
 *
 * The template wrapper for the product list widget front-end.
 *
 * @package ProdigyCommerce
 */

?>
<?php echo wp_kses_post( $args['before_widget'] . $args['before_title'] . $title . $args['after_title'] ); ?>

<ul class="pc-product-list">
	<?php foreach ( $products as $product ) :
		// Get product_meta
		$product_meta = get_post_meta( $product->ID, '_pc_product_general', true );

		// Get thumbnail url
		$image_url = get_the_post_thumbnail_url( $product->ID, 'medium' );
	?>

		<li style="max-width: <?php echo esc_attr( $thumb_width ); ?>px;" class="pc-product-item">
			<a href="<?php echo esc_url( get_post_permalink( $product->ID ) ); ?>">
				<div style="overflow: hidden; max-width: <?php echo esc_attr( $thumb_width ); ?>px; height: <?php echo esc_attr( $thumb_width ); ?>px;" class="pc-product-store-image">
					<img src="<?php echo esc_url( $image_url ); ?>" class="pc-cat-thumb" style="max-width: <?php echo esc_attr( $thumb_width ); ?>px; max-height: <?php echo esc_attr( $thumb_width ); ?>px;"/>
				</div>
				<div class="pc-product-title">
					<?php echo esc_html( $product->post_title ); ?>
				</div>

				<hr>

				<?php if ( '' !== $product_meta['pc-short-description'] ) : ?>
					<div class="pc-product-description" style="max-width: <?php echo esc_attr( $thumb_width ); ?>px;">
						<?php echo esc_html( $product_meta['pc-short-description'] ); ?>
					</div>
				<?php endif; ?>

				<div class="pc-product-price">
					<?php get_pc_price( $product->ID, true, $price ); ?>
				</div>
			</a>
			<?php get_pc_cart_button( $product->ID, true, $cart_button ); ?>
		</li>
	<?php endforeach; ?>
</ul>

<?php echo wp_kses_post( $args['after_widget'] );
