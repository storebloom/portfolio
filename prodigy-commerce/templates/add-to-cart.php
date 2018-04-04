<?php
/**
 * Add To Cart Template
 *
 * The template wrapper for the add to cart button settings.
 *
 * @package ProdigyCommerce
 */

?>
<p>
	<input type="checkbox" name="prodigy-commerce_add-to-cart[show-button]" <?php echo esc_attr( checked( 'on', $option['show-button'] ) ); ?>> - <?php echo esc_html__( 'Enable cart buttons on store / category products.', 'prodigy-commerce' ); ?>
</p>

<p>
	<strong>
		<?php echo esc_html__( 'The below settings will override the default "add to cart" buttons style.', 'prodigy-commerce' ); ?>
	</strong>
	<br>
	<input type="text" name="prodigy-commerce_add-to-cart[text]" value="<?php echo esc_attr( $option['text'] ); ?>"> - <?php echo esc_html__( 'Global cart button text', 'prodigy-commerce' ); ?>
</p>
<p>
	<input type="number" name="prodigy-commerce_add-to-cart[padding]" value="<?php echo esc_attr( $option['padding'] ); ?>" min="0"> - <?php echo esc_html__( 'Button padding in px', 'prodigy-commerce' ); ?>
</p>

<p>
	<label for="add-to-cart-color"><?php echo esc_html__( 'Button color', 'prodigy-commerce' ); ?></label>
	<br>
	<input id="add-to-cart-color" name="prodigy-commerce_add-to-cart[color]" value="<?php echo esc_attr( $option['color'] ); ?>">
</p>

<p>
<label for="add-to-cart-font-color"><?php echo esc_html__( 'Button text color', 'prodigy-commerce' ); ?></label>
	<br>
	<input id="add-to-cart-font-color" name="prodigy-commerce_add-to-cart[font-color]" value="<?php echo esc_attr( $option['font-color'] ); ?>">
</p>

<p>
	<input type="number" name="prodigy-commerce_add-to-cart[rounded]" value="<?php echo esc_attr( $option['rounded'] ); ?>" min="0"> - <?php echo esc_html__( 'Border radius in px', 'prodigy-commerce' ); ?>
</p>
