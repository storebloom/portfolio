<?php
/**
 * Size Setting Template.
 *
 * The template for the size setting field.
 *
 * @package AutoQuote
 */

$current_size = false !== get_option( 'auto-quote-size' ) ? get_option( 'auto-quote-size' ) : 'medium';
?>

<div class="size-setting-wrapper">
	<label for="auto-quote-size">
		Size
	</label>
	<select id="auto-quote-size" name="auto-quote-size">
		<?php foreach ( $option_array as $option ) :
			$selected = $current_size === strtolower( $option ) ? 'selected' : '';
			?>
			<option class="size-item" value="<?php echo strtolower( esc_attr( $option ) ); ?>" <?php echo esc_attr( $selected ); ?>><?php echo esc_html( $option ); ?></option>
		<?php endforeach; ?>
	</select>
</div>
