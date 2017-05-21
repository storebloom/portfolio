<?php
/**
 * Color Setting Template.
 *
 * The template for the color setting field.
 *
 * @package AutoQuote
 */

$current_color = false !== get_option( 'auto-quote-color' ) ? get_option( 'auto-quote-color' ) : '#464646';
?>

<div class="color-setting-wrapper">
	<input name="auto-quote-color" id="auto-quote-color" value="<?php echo esc_html( $current_color ); ?>" />
</div>
