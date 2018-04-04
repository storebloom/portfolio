<?php
/**
 * Store page product list template.
 *
 * @package Prodigy Commerce
 *
 */

?>
<li class="pc-product-item">
	<a href="<?php echo esc_url( get_post_permalink( $product->ID ) ); ?>">
		<div style="overflow: hidden; max-width: <?php echo esc_attr( $thumbs['width'] ); ?>px; height: <?php echo esc_attr( $thumbs['height'] ); ?>px;" class="pc-product-store-image">
			<img src="<?php echo esc_url( $image_url ); ?>" class="pc-cat-thumb" style="max-width: <?php echo esc_attr( $thumbs['width'] ); ?>px; max-height: <?php echo esc_attr( $thumbs['height'] ); ?>px;"/>
		</div>
		<div class="pc-product-title">
			<?php echo esc_html( $product->post_title ); ?>
		</div>
	</a>
	<hr>
	<?php if ( '' !== $product_meta['pc-short-description'] ) : ?>
		<div class="pc-product-description" style="max-width: <?php echo esc_attr( $thumbs['width'] ); ?>px;">
			<?php echo esc_html( $product_meta['pc-short-description'] ); ?>
		</div>
	<?php endif; ?>

	<div class="pc-product-price">
		<?php get_pc_price( $product->ID ); ?>
	</div>

	<?php get_pc_cart_button( $product->ID ); ?>
</li>
