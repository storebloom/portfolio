<?php
/**
 * Auto Quote Settings Template
 *
 * The template wrapper for the Auto Quote settings page.
 *
 * @package AutoQuote
 */
?>

<div class="wrap">
	<h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
	<form action="options.php" method="post">
		<?php
		settings_fields( $this->menu_slug );
		do_settings_sections( $this->menu_slug );
		submit_button( 'Update Settings' );
		?>
	</form>
	<div id="quote-preview">
		<h2>Preview Quote</h2>

		<?php do_shortcode( '[auto-quote]' ); ?>
	</div>
</div>
