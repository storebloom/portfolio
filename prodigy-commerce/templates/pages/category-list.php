<?php
/**
 * Store page product list template.
 *
 * @package Prodigy Commerce
 *
 */

?>
<li class="pc-product-item">
	<a href="<?php echo esc_attr( get_bloginfo( 'url' ) . '/' . $product->slug . '/' ); ?>">
		<div style="overflow: hidden; max-width: <?php echo esc_attr( $thumbs['width'] ); ?>px; height: <?php echo esc_attr( $thumbs['height'] ); ?>px;" class="pc-product-store-image">
			<img src="<?php echo esc_url( $cat_image ); ?>" class="pc-cat-thumb" style="max-width: <?php echo esc_attr( $thumbs['width'] ); ?>px; max-height: <?php echo esc_attr( $thumbs['height'] ); ?>px;"/>
		</div>
		<div class="pc-product-title">
			<?php echo esc_html( $product->name ); ?>
		</div>
	</a>
</li>
