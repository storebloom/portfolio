<?php
/**
 * The template for the product infomation column
 */

?>

<?php
// Get thumbnails
$product_images = get_post_meta( get_the_ID(),  '_pc_product_image', true );
$add_width = count( $product_images ) + 1;
$add_width = 100 / $add_width;
$add_width = $add_width - 5;
$add_width = $add_width . '%';

if ( '' !== $product_images ) : ?>
	<div style="width: <?php echo esc_attr( $add_width ); ?>;" class="pdp-thumbnail">
		<img src="<?php echo esc_url( get_the_post_thumbnail_url() ); ?>">
	</div>
<?php endif; ?>

<?php foreach ( $product_images as $image_url ) : ?>
	<div style="width: <?php echo esc_attr( $add_width ); ?>;" class="pdp-thumbnail">
		<img src="<?php echo esc_url( $image_url ); ?>">
	</div>
<?php endforeach; ?>
