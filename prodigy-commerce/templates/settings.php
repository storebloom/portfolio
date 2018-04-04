<?php
/**
 * Settings Template
 *
 * The template wrapper for the general settings page.
 *
 * @package ProdigyCommerce
 */

?>
<div class="wrap">
	<h1>
		<?php echo esc_html( get_admin_page_title() ); ?>
	</h1>

	<form action="options.php" method="post">
		<?php
		settings_fields( $this->plugin->assets_prefix . '-settings' );
		do_settings_sections( $this->plugin->assets_prefix . '-settings' );
		submit_button( esc_html__( 'Update', 'prodigy-commerce' ) );
		?>
	</form>
</div>

