<?php
/**
 * Cart Widget Template
 *
 * The template wrapper for the cart widget form fields.
 *
 * @package ProdigyCommerce
 */

?>
<p>
	<label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>">Title:</label>
	<input type="text" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" value="<?php echo esc_attr( $title ); ?>">
</p>

<p>
	<label for="<?php echo esc_attr( $this->get_field_id( 'show-count' ) ); ?>">Show Count:</label>
	<input type="checkbox" id="<?php echo esc_attr( $this->get_field_id( 'show-count' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'show-count' ) ); ?>" <?php echo esc_attr( checked( 'on', $show_count ) ); ?>/>
</p>

<p>
	<label for="<?php echo esc_attr( $this->get_field_id( 'show-subtotal' ) ); ?>">Show Subtotal:</label>
	<input type="checkbox" id="<?php echo esc_attr( $this->get_field_id( 'show-subtotal' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'show-subtotal' ) ); ?>" <?php echo esc_attr( checked( 'on', $show_subtotal ) ); ?>/>
</p>

<p>
	<label for="<?php echo esc_attr( $this->get_field_id( 'dropdown' ) ); ?>">Make Cart Dropdown:</label>
	<input type="checkbox" id="<?php echo esc_attr( $this->get_field_id( 'dropdown' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'dropdown' ) ); ?>" <?php echo esc_attr( checked( 'on', $dropdown ) ); ?>/>
</p>

<p>
	<label for="<?php echo esc_attr( $this->get_field_id( 'icon' ) ); ?>">Cart Icon:</label>
	<input type="text" id="<?php echo esc_attr( $this->get_field_id( 'icon' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'icon' ) ); ?>" value="<?php echo esc_attr( $icon ); ?>">
</p>
