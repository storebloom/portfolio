<?php
/**
 * Shortcode Generator Template
 *
 * The template wrapper for the shortcode generator menu page.
 *
 * @package ProdigyCommerce
 */

?>
<div class="wrap">
	<h1>
		<?php echo esc_html( get_admin_page_title() ); ?>
	</h1>

	<h4>
		<?php echo esc_html__( 'Here you will find all of the available shortcodes.  Click on accordion for a description and to view options for each shortcode. Generate the shortcode of your liking!', 'prodigy-commerce' ); ?>
	</h4>

	<div class="pc-shortcode-list">
		<ul>
			<?php foreach ( $shortcode_list as $name => $shortcode ) : ?>
				<li class="pc-shortcode-list-items">
					<div class="pc-shortcode-accor-wrap">
						<span class="accor-arrow">&#9658;</span>

						<div class="pc-the-shortcode">
							<textarea id="holdtext" style="display:none;"></textarea>
							<input id="<?php echo esc_attr( $name ); ?>" class="pc-shortcode-value" type="text" value="[<?php echo esc_attr( $name ); ?>]" readonly size="70">
							<button type="button" class="pc-copy-shortcode">copy</button>
						</div>
					</div>
					<div class="pc-shortcode-options">
						<h5 class="pc-shortcode-description">
							<?php echo isset( $shortcode['description'] ) ? esc_html( $shortcode['description'] ) : ''; ?>
						</h5>

						<ul>
							<?php foreach ( $shortcode as $option_name => $option ) :
								if ( 'description' !== $option_name ) : ?>
								<li>
									<label for="<?php echo esc_attr( $name . '-' . $option_name ); ?>">
										<?php echo esc_html( $option_name ); ?>
									</label>
									<input id="<?php echo esc_attr( $name . '-' . $option_name ); ?>" type="<?php echo isset( $option['type'] ) ? esc_attr( $option['type'] ) : ''; ?>" <?php echo isset( $option['default'] ) ? esc_attr( $option['default'] ) : '' ?>/>
									<div class="pc-shortcode-option-description">
										*<?php echo isset( $option['description'] ) ? esc_html( $option['description'] ) : ''; ?>
									</div>
								</li>
							<?php
								endif;
							endforeach; ?>
						</ul>

						<button type="button" class="pc-generate-shortcode"><?php echo esc_html__( 'Generate', 'prodigy-commerce' ); ?></button>
					</div>
				</li>
			<?php endforeach; ?>
		</ul>
	</div>
</div>
