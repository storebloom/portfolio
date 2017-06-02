<?php
/**
 * Top Story Box Template
 *
 * The template wrapper for the Top Story box that shows on article pages.
 *
 * @package TopStory
 */
?>

<div id="top-story-article-wrap">
	<div class="top-thumb">
		<?php echo wp_kses_post( get_the_post_thumbnail( $top_post->ID ) ); ?>
	</div>
	<div class="top-post-content">
		<div class="top-cat">
			<?php echo esc_html( $cat ); ?>
		</div>
		<div class="top-mobile-timestamp">
			| <?php echo esc_html( $m_timestamp ); ?>
		</div>
		<div class="top-title">
			<a href="<?php echo esc_url( get_post_permalink( $top_post->ID ) ); ?>">
				<?php echo esc_html( $top_post->post_title ); ?>
			</a>
		</div>
		<div class="top-author">
			By:
			<div class="top-author-name">
				<a href="<?php echo esc_url( get_author_posts_url( $top_author->ID ) ); ?>">
					<?php echo esc_html( $top_author->display_name ); ?>
				</a>
			</div>
			<div class="top-timestamp">
				<?php echo esc_html( $timestamp ); ?>
			</div>
		</div>
	</div>
</div>
