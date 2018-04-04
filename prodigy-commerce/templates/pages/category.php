<?php
/**
 * Store page template. Should strictly belong to the Store page.
 *
 * @package Prodigy Commerce
 */

// Get all products using user selected args.
$pagination = get_option( 'prodigy-commerce_pagination' );
$pagination = null !== $pagination && false !== $pagination ? 'paginate' : '';

// Get current term.
$cat = get_term_by( 'slug', get_query_var( 'term' ), get_query_var( 'taxonomy' ) );

// Get all products using user selected args.
$products = get_pc_products( $pagination, $cat->term_id, '', false, '', $show_categories );

// Get thumbnail width / height
$thumbs = get_option( 'prodigy-commerce_cat-thumb' );

get_header();
?>
	<div id="store-page">
		<div class="content-area">
			<?php include_once( plugin_dir_path( __FILE__ ) . 'breadcrumb.php' ); ?>

			<main id="main" class="site-main" role="main">

				<h1 class="page-title"><?php echo esc_html( $cat->name ); ?></h1>

				<div id="product-catalog-wrap">
					<ul class="pc-product-list">
						<?php foreach ( $products as $product ) :
							// Get product_meta
							$product_meta = get_post_meta( $product->ID,  '_pc_product_general', true );

							// Get thumbnail url
							$image_url = get_the_post_thumbnail_url( $product->ID, 'medium' );

							include( plugin_dir_path( __FILE__ ) . 'product-list.php' );
						endforeach; ?>
					</ul>
				</div>

				<?php get_pc_pagination( true, $cat->term_id ); ?>
			</main>
		</div>
	</div>
<?php get_footer();
