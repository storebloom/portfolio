<?php
/**
 * Tax Tab Template
 *
 * The template wrapper for the tax tab in product post editor meta box.
 *
 * @package ProdigyCommerce
 */

?>
<div class="inside hidden">
	<div class="pc-meta-setting-item">
		<label for="_pc_tax_settings[pc-taxable]"><?php echo esc_html__( 'Taxable', 'prodigy-commerce' ); ?></label>

		<p class="howto"><?php echo esc_html__( 'Is this product taxable?', 'prodigy-commerce' ); ?></p>

		<input name="_pc_tax_settings[pc-taxable]" type="checkbox" <?php echo checked( 'on', $taxable ); ?>>

		<label for="_pc_tax_settings[pc-tax-code]"><?php echo esc_html__( 'Tax Code', 'prodigy-commerce' ); ?></label>

		<p class="howto"><?php echo esc_html__( 'Select the product\'s tax code.  If you don\'t know it leave it default.', 'prodigy-commerce' ); ?></p>

		<select name="_pc_tax_settings[pc-tax-code]">
			<?php foreach ( $tax_codes as $tax_name => $tax_value ) :
				if ( ! is_int( $tax_name ) ) : ?>
				<option value="<?php echo esc_attr( $tax_value ); ?>" <?php echo '' !== $tax['pc-tax-code'] && (int) $tax['pc-tax-code'] === $tax_value ? 'selected' : ''; ?>><?php echo esc_html( $tax_name ); ?></option>
				<?php endif;
			endforeach; ?>
		</select>
		<button id="tax-legend" type="button">?</button>
		<div id="tax-legend-popout">
			<button id="close-tax-legend" type="button">x</button>
			<h3>Tax Code Help</h3>
			<ul class="legend-list">
				<?php foreach ( $tax_codes as $tax_name => $tax_value ) :
					if ( ! is_int( $tax_name ) && '' !== $tax_value ) : ?>
					<li class="legend-list-item">
						<strong><?php echo esc_html( $tax_name ); ?>: </strong>
						<?php echo esc_html( $tax_codes[ $tax_value ] ); ?>
					</li>
					<?php endif;
				endforeach; ?>
			</ul>
		</div>
	</div>
</div>
