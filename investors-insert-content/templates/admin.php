<?php
/**
 * Insert Content Template
 *
 * The template wrapper for the insert content menu option page.
 *
 * @package InvestorsInsertContent
 */

?>

<div class="wrap">
	<h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
	<form action="options.php" method="post">
		<?php
		settings_fields( $this->menu_slug );
		do_settings_sections( $this->menu_slug );
		submit_button( 'Save Changes' );
		?>
	</form>
</div>
