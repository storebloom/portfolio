<?php
/**
 * Product List Widget Template
 *
 * The template wrapper for the product list widget form fields.
 *
 * @package ProdigyCommerce
 */

?>
<p>
	<label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>">Title:</label>
	<input type="text" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" value="<?php echo esc_attr( $title ); ?>">
</p>

<p>
	<label for="<?php echo esc_attr( $this->get_field_id( 'count' ) ); ?>">Count:</label>
	<input type="number" id="<?php echo esc_attr( $this->get_field_id( 'count' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'count' ) ); ?>" value="<?php echo esc_attr( $count ); ?>" min="0"/>
</p>

<p>
	<label for="<?php echo esc_attr( $this->get_field_id( 'cart-button' ) ); ?>">Show Cart Buttons:</label>
	<input type="checkbox" id="<?php echo esc_attr( $this->get_field_id( 'cart-button' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'cart-button' ) ); ?>" <?php echo esc_attr( checked( 'on', $cart_button ) ); ?>/>
</p>

<p>
	<label for="<?php echo esc_attr( $this->get_field_id( 'price' ) ); ?>">Show Price:</label>
	<input type="checkbox" id="<?php echo esc_attr( $this->get_field_id( 'price' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'price' ) ); ?>" <?php echo esc_attr( checked( 'on', $price ) ); ?>/>
</p>

<p>
	<label for="<?php echo esc_attr( $this->get_field_id( 'category' ) ); ?>">Category:</label>

	<input id="prodigy-commerce_widget-category" type="text" value="<?php echo esc_attr( $category ); ?>" placeholder="<?php echo esc_html__( 'Search for a category to select.', 'prodigy-commerce' ); ?>" size="40" autocomplete="off">
	<input id="<?php echo esc_attr( $this->get_field_id( 'category' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'category' ) ); ?>" value="<?php echo esc_attr( $category ); ?>" type="hidden">

	<span id="pc-widget-category-search" class="search-st-icon"></span>

	<small class="howto"><?php echo esc_html__( '*Must select from search list to save a category.', 'prodigy-commerce' ); ?></small>

	<ul id="category-widget-result-wrapper"></ul>
</p>

<p>
	<label for="<?php echo esc_attr( $this->get_field_id( 'tag' ) ); ?>">Tag:</label>
	<input id="prodigy-commerce_widget-tag" type="text" value="<?php echo esc_attr( $tag ); ?>" placeholder="<?php echo esc_html__( 'Search for a tag to select.', 'prodigy-commerce' ); ?>" size="40" autocomplete="off">
	<input id="<?php echo esc_attr( $this->get_field_id( 'tag' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'tag' ) ); ?>" value="<?php echo esc_attr( $tag ); ?>" type="hidden">

	<span id="pc-widget-tag-search" class="search-st-icon"></span>

	<small class="howto"><?php echo esc_html__( '*Must select from search list to save a tag.', 'prodigy-commerce' ); ?></small>

	<ul id="tag-widget-result-wrapper"></ul>
</p>

<p>
	<label for="<?php echo esc_attr( $this->get_field_id( 'thumb-width' ) ); ?>">Thumbnail Width:</label>
	<input type="number" id="<?php echo esc_attr( $this->get_field_id( 'thumb-width' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'thumb-width' ) ); ?>" value="<?php echo esc_attr( $thumb_width ); ?>" min="0">px
</p>

<p>
	<label for="<?php echo esc_attr( $this->get_field_id( 'sale-items' ) ); ?>">Sale Items Only:</label>
	<input type="checkbox" id="<?php echo esc_attr( $this->get_field_id( 'sale-items' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'sale-items' ) ); ?>" <?php echo esc_attr( checked( 'on', $sale_items ) ); ?>/>
</p>
