<?php
/**
 * Top Story Option Template
 *
 * The template wrapper for the Top Story option page.
 *
 * @package TopStory
 */
?>

<div class="wrap">
	<h1><?php echo esc_html( get_admin_page_title() ); ?></h1>

	<form action="options.php" method="post">
		<?php
		settings_fields( $this->menu_slug );
		do_settings_sections( $this->menu_slug );
		submit_button( 'Update Top Story' );
		?>

	</form>

	<div class="top-story-result-wrapper">
		<h3>search results:</h3>
		<div class="top-story-results"></div>
	</div>
</div>
