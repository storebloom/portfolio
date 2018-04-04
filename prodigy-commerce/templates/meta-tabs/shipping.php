<?php
/**
 * Shipping Tab Template
 *
 * The template wrapper for the shipping tab in product post editor meta box.
 *
 * @package ProdigyCommerce
 */

?>
<div class="inside hidden">
	<div class="pc-meta-setting-item">
		<label for="_pc_shipping_settings[pc-shippable]"><?php echo esc_html__( 'Shippable', 'prodigy-commerce' ); ?></label>

		<p class="howto"><?php echo esc_html__( 'Is this product shippable?', 'prodigy-commerce' ); ?></p>

		<input name="_pc_shipping_settings[pc-shippable]" type="checkbox" <?php echo checked( 'on', $shippable ); ?>>

		<label for="_pc_shipping_settings[pc-weight]"><?php echo esc_html__( 'Weight (oz)', 'prodigy-commerce' ); ?></label>

		<p class="howto"><?php echo esc_html__( 'Enter the product\'s weight in ounces.', 'prodigy-commerce' ); ?></p>

		<input name="_pc_shipping_settings[pc-weight]" type="number" value="<?php echo esc_attr( $shipping['pc-weight'] ); ?>" min="0">

		<label for="_pc_shipping_settings[pc-length]"><?php echo esc_html__( 'Length (in)', 'prodigy-commerce' ); ?></label>

		<p class="howto"><?php echo esc_html__( 'Enter the product\'s length in inches.', 'prodigy-commerce' ); ?></p>

		<input name="_pc_shipping_settings[pc-length]" type="number" value="<?php echo esc_attr( $shipping['pc-length'] ); ?>" min="0">

		<label for="_pc_shipping_settings[pc-width]"><?php echo esc_html__( 'Width (in)', 'prodigy-commerce' ); ?></label>

		<p class="howto"><?php echo esc_html__( 'Enter the product\'s width in inches.', 'prodigy-commerce' ); ?></p>

		<input name="_pc_shipping_settings[pc-width]" type="number" value="<?php echo esc_attr( $shipping['pc-width'] ); ?>" min="0">

		<label for="_pc_shipping_settings[pc-height]"><?php echo esc_html__( 'Height (in)', 'prodigy-commerce' ); ?></label>

		<p class="howto"><?php echo esc_html__( 'Enter the product\'s height in inches.', 'prodigy-commerce' ); ?></p>

		<input name="_pc_shipping_settings[pc-height]" type="number" value="<?php echo esc_attr( $shipping['pc-height'] ); ?>" min="0">
	</div>
</div>
