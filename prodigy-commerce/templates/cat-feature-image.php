<?php
/**
 * Category Feature Image Template
 *
 * The template wrapper for the product category feature image field.
 *
 * @package ProdigyCommerce
 */

?>

<tr class="form-field">
	<th scope="row" valign="top">
		<label for="cat_Image_url">
			<?php echo esc_html__( 'Category Image Url', 'prodigy-commerce' ); ?>
		</label>
	</th>
	<td id="product_category_image">
		<input type="text" name="term_meta[category_image]" id="term_meta[category_image]" size="3" value="<?php echo isset( $term_meta ) ? esc_attr( $term_meta ) : ''; ?>">
		<br>
		<span class="description">
			<?php echo esc_html__( 'Image for product category: use full url.', 'prodigy-commerce' ); ?>
		</span>
	</td>
	<td>
		<button type="button" id="product-cat-img">Upload</button>
	</td>
</tr>
