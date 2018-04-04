<?php
/**
 * Variable Meta Box Template
 *
 * The template wrapper for the variable product settings meta box.
 *
 * @package ProdigyCommerce
 */

?>
<div id="pc-meta-box-wrap">
	<div class="inside">
		<div class="pc-meta-setting-item">
			<label for="_pc_product_general[pc-short-description]">Short Description</label>

			<p class="howto">Add text if you want category product descriptions.</p>

			<textarea name="_pc_product_general[pc-short-description]" cols="100"><?php echo esc_html( $product_gen['pc-short-description'] ); ?></textarea>

			<label for="_pc_product_general[pc-product-type]">Product Type</label>

			<p class="howto">Currently only supporting Simple product types.</p>

			<select name="_pc_product_general[pc-product-type]">
				<option value="simple">Simple</option>
			</select>
		</div>
	</div>

</div>
