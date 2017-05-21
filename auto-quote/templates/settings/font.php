<?php
/**
 * Font Setting Template.
 *
 * The template for the font setting field.
 *
 * @package AutoQuote
 */

$current_font = false !== get_option( 'auto-quote-font' ) ? get_option( 'auto-quote-font' ) : 'Arial, sans-serif';
?>

<div class="font-setting-wrapper">
	<label for="auto-quote-font">
		Font
	</label>
	<select id="auto-quote-font" name="auto-quote-font" >
		<?php foreach ( $option_array as $option ) :
			$selected = $current_font === strtolower( $option ) ? 'selected' : '';
			?>
			<option class="font-item" value="<?php echo strtolower( esc_attr( $option ) ); ?>" <?php echo esc_attr( $selected ); ?>><?php echo esc_html( $option ); ?></option>
		<?php endforeach; ?>
	</select>
</div>
