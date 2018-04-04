<?php
/**
 * The template for the product infomation column
 */

?>
<div id="pc-product-info" >
	<?php while ( have_posts() ) : the_post();
		$postid = get_post_field( 'ID' );

		// Get product_meta
		$product_meta = get_post_meta( $postid,  '_pc_product_general', true );
	?>
		<div id="pdp-product-title">
			<?php echo esc_html( get_the_title() ); ?>
		</div>

		<?php get_pc_variables(); ?>

		<div class="pc-pdp-price">
			<?php get_pc_price( $postid, true ); ?>
		</div>

		<?php
		echo wp_kses_post( get_the_content() );
		get_pc_cart_button( $postid );
	endwhile; // End of the loop. ?>
</div>
