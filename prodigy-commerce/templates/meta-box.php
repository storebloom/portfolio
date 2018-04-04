<?php
/**
 * Meta Box Template
 *
 * The template wrapper for the product settings meta box.
 *
 * @package ProdigyCommerce
 */

?>
<div id="pc-meta-box-wrap">
	<h2 class="nav-tab-wrapper current">
		<a class="nav-tab nav-tab-active" href="javascript:;">Settings</a>
		<a class="nav-tab" href="javascript:;">Tax</a>
		<a class="nav-tab" href="javascript:;">Shipping</a>
		<a class="nav-tab" href="javascript:;">Inventory</a>
		<?php echo apply_filters( 'pc_module_tab_names', '' ); // WPCS: XSS ok. ?>
	</h2>

	<?php
	include_once( "{$this->plugin->dir_path}templates/meta-tabs/product-gen.php" );
	include_once( "{$this->plugin->dir_path}templates/meta-tabs/tax.php" );
	include_once( "{$this->plugin->dir_path}templates/meta-tabs/shipping.php" );
	include_once( "{$this->plugin->dir_path}templates/meta-tabs/inventory.php" );
	echo apply_filters( 'pc_module_tabs', '' ); // WPCS: XSS ok.
	?>
</div>
