<?php
/**
 * Vimeo Upload Template
 *
 * The template wrapper for the vimeo upload form.
 *
 * @package RpaContentFormat
 */

?>
<div class="wrap">
	<h1>
		<?php echo esc_html( get_admin_page_title() ); ?>
	</h1>

	<h4>
		Hi there, <?php echo esc_html( $current_user->display_name ); ?>!
		Go ahead and use the form below for all your Vimeo Video Uploading needs.
	</h4>

	<label for="rpa-vimeo-upload">Upload Here</label>
	<input type="file" id="rpa-vimeo-upload" name="rpa-vimeo-upload">
</div>