<?php
/**
 * General Setting Tab Template
 *
 * The template wrapper for the product settings tab in product post editor meta box.
 *
 * @package ProdigyCommerce
 */

?>
<div class="inside">
	<div class="pc-meta-setting-item">
		<label for="_pc_product_general[pc-short-description]">Short Description</label>

		<p class="howto">Add text if you want category product descriptions.</p>

		<textarea name="_pc_product_general[pc-short-description]" cols="100"><?php echo esc_html( $product_gen['pc-short-description'] ); ?></textarea>

		<label for="_pc_product_general[pc-product-type]">Product Type</label>

		<p class="howto">Currently only supporting Simple product types.</p>

		<select id="pc-product-type" name="_pc_product_general[pc-product-type]">
			<option value="simple" <?php echo selected( 'simple', $product_gen['pc-product-type'] ); ?>>Simple</option>

			<?php echo apply_filters( 'pc_module_product_type', '' ); // WPCS: XSS ok. ?>
		</select>

		<label for="_pc_product_general[pc-product-sku]">SKU</label>

		<p class="howto">Enter product SKU.</p>

		<input name="_pc_product_general[pc-product-sku]" type="text" value="<?php echo esc_attr( $product_gen['pc-product-sku'] ); ?>" />

		<label for="_pc_regular_price">Price</label>

		<p class="howto">Enter product regular price.</p>

		<input name="_pc_regular_price" type="number" value="<?php echo esc_attr( $regular_price ); ?>" min="0" step=".01">

		<label for="_pc_sale_price">Sale Price</label>

		<p class="howto">Enter product sale price.</p>

		<input name="_pc_sale_price" type="number" value="<?php echo esc_attr( $sale_price ); ?>" min="0" step=".01">

		<label for="_pc_product_general[pc-cart-button]">Add to cart</label>

		<p class="howto">Change the add to cart text here.</p>

		<input name="_pc_product_general[pc-cart-button]" type="text" value="<?php echo esc_attr( $product_gen['pc-cart-button'] ); ?>" />

		<?php echo apply_filters( 'pc_module_product_settings', '' ); // WPCS: XSS ok. ?>
	</div>
</div>
