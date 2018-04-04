<?php
/**
 * The template for displaying all single products
 */

get_header(); ?>
	<div class="wrap">
		<main id="main" class="site-main" role="main">
			<?php include_once( plugin_dir_path( __FILE__ ) . 'breadcrumb.php' ); ?>

			<div class="pc-left-column">
				<?php while ( have_posts() ) : the_post(); ?>
					<div class="pc-pdp-image">
						<img id="main-pdp-image" src="<?php echo esc_url( get_the_post_thumbnail_url() ); ?>">
						<div class="pc-thumbnails">
							<?php include_once( plugin_dir_path( __FILE__ ) . 'pdp-thumbnails.php' ); ?>
						</div>
					</div>
				<?php endwhile; ?>
			</div>

			<?php include_once( plugin_dir_path( __FILE__ ) . 'pdp-info.php' ); ?>
		</main><!-- #main -->
	</div><!-- .wrap -->
<?php get_footer();
