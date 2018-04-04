<?php
/**
 * Modules Template
 *
 * The template wrapper for the modules tab.
 *
 * @package ProdigyCommerce
 */

?>
<div id="pc-module-wrap">
	<?php foreach ( $modules as $title => $module ) : ?>
		<div class="pc-module-item">
			<h3><?php echo esc_html( $title ); ?></h3>

			<div class="pc-module-description">
				<?php echo esc_html( $module['description'] ); ?>
			</div>

			<?php if ( class_exists( '\\ProdigyCommerce\\' . $module['class'] ) ) : ?>
				<div class="pc-module-status">
					<?php echo esc_html__( 'active', 'prodigy-commerce' ); ?>
				</div>
			<?php else : ?>
				<div class="pc-module-status not-active">
					<a href="<?php echo esc_url( $module['download'] ); ?>">
						<?php echo esc_html__( 'download', 'prodigy-commerce' ); ?>
					</a>
				</div>
			<?php endif; ?>
		</div>
	<?php endforeach; ?>
</div>
