<?php
/**
 * Module and Theme Template
 *
 * The template wrapper for the modules and themes menu page.
 *
 * @package ProdigyCommerce
 */

?>
<div class="wrap">
	<h1>
		<?php echo esc_html( get_admin_page_title() ); ?>
	</h1>

	<h2 class="nav-tab-wrapper">
		<a href="?page=prodigy-commerce-module-theme&tab=themes" class="nav-tab <?php 'themes' === $active_tab ? esc_attr_e( 'nav-tab-active' ) : ''; ?>">Themes</a>
		<a href="?page=prodigy-commerce-module-theme&tab=modules" class="nav-tab <?php 'modules' === $active_tab ? esc_attr_e( 'nav-tab-active' ) : ''; ?>">Modules</a>
	</h2>

	<?php
	if ( 'modules' === $active_tab ) {
		include_once( "{$this->plugin->dir_path}/templates/modules.php" );
	} else {
		include_once( "{$this->plugin->dir_path}/templates/themes.php" );
	}
	?>
</div>
