<?php
/**
 * Inventory Tab Template
 *
 * The template wrapper for the inventory tab in product post editor meta box.
 *
 * @package ProdigyCommerce
 */

?>
<div class="inside hidden">
	<div class="pc-meta-setting-item">
		<label for="_pc_inventory_settings[pc-full-inventory]"><?php echo esc_html__( 'Full Inventory', 'prodigy-commerce' ); ?></label>

		<p class="howto"><?php echo esc_html__( 'Enter the product\'s full inventory count.', 'prodigy-commerce' ); ?></p>

		<input name="_pc_inventory_settings[pc-full-inventory]" type="number" value="<?php echo esc_attr( $inventory['pc-full-inventory'] ); ?>" min="0">

		<label for="_pc_inventory_settings[pc-available-inventory]"><?php echo esc_html__( 'Available Inventory', 'prodigy-commerce' ); ?></label>

		<p class="howto"><?php echo esc_html__( 'Enter the product\'s available inventory count.', 'prodigy-commerce' ); ?></p>

		<input name="_pc_inventory_settings[pc-available-inventory]" type="number" value="<?php echo esc_attr( $inventory['pc-available-inventory'] ); ?>" min="0">
	</div>
</div>
