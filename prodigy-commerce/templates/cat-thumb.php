<?php
/**
 * Cat Thumbnail Template
 *
 * The template wrapper for the cat / store thumbnail setting.
 *
 * @package ProdigyCommerce
 */

?>
<input type="number" name="prodigy-commerce_cat-thumb[width]" value="<?php echo esc_attr( $option['width'] ); ?>" min="0"> ( <?php echo esc_html__( 'width in px', 'prodigy-commerce' ); ?> )

<br>

<input type="number" name="prodigy-commerce_cat-thumb[height]" value="<?php echo esc_attr( $option['height'] ); ?>" min="0"> ( <?php echo esc_html__( 'height in px', 'prodigy-commerce' ); ?> )
