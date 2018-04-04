<?php
/**
 * Right to cart Setting Template
 *
 * The template wrapper for the sending customers straight to cart on/off setting.
 *
 * @package ProdigyCommerce
 */

?>
<input type="checkbox" name="prodigy-commerce_right-to-cart" <?php echo checked( 'on', get_option( 'prodigy-commerce_right-to-cart' ) ); ?>>
