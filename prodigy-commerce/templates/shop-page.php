<?php
/**
 * Shop Page Choice Template
 *
 * The template wrapper for the shop page choice.
 *
 * @package ProdigyCommerce
 */

?>
<div class="shop-wrapper">
	<input id="prodigy-commerce_shop-page" type="text" value="<?php echo '' !== $option ? esc_attr( reset( $option ) ) : ''; ?>" placeholder="<?php echo esc_html__( 'Search for a page to select.', 'prodigy-commerce' ); ?>" size="40" autocomplete="off">
	<input name="prodigy-commerce_shop-page[<?php echo esc_attr( key( $option ) ); ?>]" id="shop-page-search-value" type="hidden" value="<?php echo '' !== $option ? esc_attr( reset( $option ) ) : ''; ?>" >

	<span class="search-st-icon"></span>

	<small class="howto"><?php echo esc_html__( '*Must select from search list to save new page.', 'prodigy-commerce' ); ?></small>

	<ul id="shop-page-result-wrapper"></ul>
</div>
