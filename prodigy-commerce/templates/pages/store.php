<?php
/**
 * Store page template. Should strictly belong to the Store page.
 *
 * @package Prodigy Commerce
 */

// Get all products using user selected args.
$pagination = get_option( 'prodigy-commerce_pagination' );
$pagination = null !== $pagination && false !== $pagination ? 'paginate' : '';
$show_categories = get_option( 'prodigy-commerce_category-view' );
$show_categories = null !== $show_categories && false !== $show_categories && 'on' === $show_categories ? true : false;
$products = get_pc_products( $pagination, '', '', false, '', $show_categories );

// Get thumbnail width / height
$thumbs = get_option( 'prodigy-commerce_cat-thumb' );

get_header();
?>
<div id="store-page">
	<div class="content-area">
		<main id="main" class="site-main" role="main">
			<h1 class="page-title"><?php echo esc_html( the_title() ); ?></h1>

			<div id="product-catalog-wrap">
				<ul class="pc-product-list">
					<?php foreach ( $products as $product ) :
						// Get product_meta
						$product_meta = get_post_meta( $product->ID,  '_pc_product_general', true );

						// Get thumbnail url
						$image_url = get_the_post_thumbnail_url( $product->ID, 'medium' );

						if ( $show_categories ) {
							// Get category image.
							$cat_image = get_option( 'category_image_' . $product->term_id );
							$cat_image = null !== $cat_image && false !== $cat_image ? $cat_image : '';

							include( plugin_dir_path( __FILE__ ) . 'category-list.php' );
						} else {
							include( plugin_dir_path( __FILE__ ) . 'product-list.php' );
						}
					 endforeach; ?>
				</ul>
			</div>

			<?php if ( ! $show_categories ) { get_pc_pagination( true ); } ?>
		</main>
	</div>
</div>
<?php get_footer();
