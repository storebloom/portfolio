<?php
/**
 * Breadcrumb Template
 *
 * The template wrapper for the page breadcrumb.
 *
 * @package ProdigyCommerce
 */
global $post;

$bread_crumb = get_pc_breadcrumbs( $post, false );
?>
<div id="pc-breadcrumb">
	<?php echo wp_kses_post( $bread_crumb ); ?>
</div>
