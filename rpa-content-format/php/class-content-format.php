<?php
/**
 * Rpa Content Format.
 *
 * @package RpaContentFormat
 */

namespace RpaContentFormat;

/**
 * Content Format Class
 *
 * @package RpaContentFormat
 */
class Content_Format {

	/**
	 * Plugin instance.
	 *
	 * @var object
	 */
	public $plugin;

	/**
	 * Class constructor.
	 *
	 * @param object $plugin Plugin class.
	 */
	public function __construct( $plugin ) {
		$this->plugin = $plugin;
	}

	/**
	 * Enqueue admin assets.
	 *
	 * @param string $hook The current admin page.
	 * @action admin_enqueue_scripts
	 */
	public function enqueue_admin_assets( $hook ) {
		global $post;

		// Only euqueue these assets on editor pages.
		if ( 'post-new.php' === $hook || 'post.php' === $hook ) {
			wp_enqueue_style( "{$this->plugin->assets_prefix}-admin" );
			wp_enqueue_script( "{$this->plugin->assets_prefix}-admin" );
		}
	}

	/**
	 * Register the image shortcode
	 *
	 * @shortcode rpa-image
	 */
	public function custom_image( $att ) {
		$float = isset( $att['float'] ) ? sanitize_text_field( wp_unslash( $att['float'] ) ) : 'none';
		$url = isset( $att['url'] ) ? sanitize_text_field( wp_unslash( $att['url'] ) ) : '';
		$width = isset( $att['width'] ) ? sanitize_text_field( wp_unslash( $att['width'] ) ) : 'auto';
		$align = isset( $att['align'] ) ? sanitize_text_field( wp_unslash( $att['align'] ) ) : 'none';
		$title = isset( $att['title'] ) ? sanitize_text_field( wp_unslash( $att['title'] ) ) : '';
		$top = isset( $att['top'] ) ? sanitize_text_field( wp_unslash( $att['top'] ) ) . 'px' : '0px';
		$right = isset( $att['right'] ) ? sanitize_text_field( wp_unslash( $att['right'] ) ) . 'px' : '0px';
		$bottom = isset( $att['bottom'] ) ? sanitize_text_field( wp_unslash( $att['bottom'] ) ) . 'px' : '0px';
		$left = isset( $att['left'] ) ? sanitize_text_field( wp_unslash( $att['left'] ) ) . 'px' : '0px';

		return '<img class="rpa-image" src="' . esc_url( $url ) . '" width="' . esc_attr( $width ) . '" height="auto" style="margin: ' . esc_attr( $top ) . ' ' . esc_attr( $right ) . ' ' . esc_attr( $bottom ) . ' ' . esc_attr( $left ) . '; text-align: ' . esc_attr( $align ) . '; float: ' . esc_attr( $float ) . ';" title="' . esc_attr( $title ) . '">';
	}

	/**
	 * Register the title shortcode
	 *
	 * @shortcode rpa-title
	 */
	public function custom_title( $att ) {
		$align = isset( $att['align'] ) ? sanitize_text_field( wp_unslash( $att['align'] ) ) : 'none';
		$title = isset( $att['title'] ) ? sanitize_text_field( wp_unslash( $att['title'] ) ) : '';
		$htag = isset( $att['h'] ) ? sanitize_text_field( wp_unslash( $att['h'] ) ) : '2';
		$top = isset( $att['top'] ) ? sanitize_text_field( wp_unslash( $att['top'] ) ) . 'px' : '0px';
		$right = isset( $att['right'] ) ? sanitize_text_field( wp_unslash( $att['right'] ) ) . 'px' : '0px';
		$bottom = isset( $att['bottom'] ) ? sanitize_text_field( wp_unslash( $att['bottom'] ) ) . 'px' : '0px';
		$left = isset( $att['left'] ) ? sanitize_text_field( wp_unslash( $att['left'] ) ) . 'px' : '0px';

		// Make sure h tag is not h1
		$htag = '1' === $htag ? '2' : $htag;

		return '<h' . esc_attr( $htag ) . ' style="text-align: ' . esc_attr( $align ) . '; margin: ' . esc_attr( $top ) . ' ' . esc_attr( $right ) . ' ' . esc_attr( $bottom ) . ' ' . esc_attr( $left ) . ';">' . esc_attr( $title ) . '</h' . esc_attr( $htag ) . '>';
	}

	/**
	 * Register the hr shortcode
	 *
	 * @shortcode rpa-hr
	 */
	public function custom_hr( $att ) {
		$top = isset( $att['top'] ) ? sanitize_text_field( wp_unslash( $att['top'] ) ) . 'px' : '0px';
		$bottom = isset( $att['bottom'] ) ? sanitize_text_field( wp_unslash( $att['bottom'] ) ) . 'px' : '0px';
		$color = isset( $att['bottom'] ) ? sanitize_text_field( wp_unslash( $att['color'] ) ) : '#eaeaea';
		$size = isset( $att['size'] ) ? sanitize_text_field( wp_unslash( $att['size'] ) ) . 'px' : '2px';

		return '<hr style="background: none; border-top:' . esc_attr( $size ) . ' solid ' . esc_attr( $color ) . '; margin: ' . esc_attr( $top ) . ' 0 ' . esc_attr( $bottom ) . ' 0;">';
	}

	/**
	 * @action after_setup_theme
	 **/
	public function mytheme_buttons() {
		if ( ! current_user_can( 'edit_posts' ) && ! current_user_can( 'edit_pages' ) ) {
			return;
		}

		if ( get_user_option( 'rich_editing' ) !== 'true' ) {
			return;
		}

		add_filter( 'mce_external_plugins', array( $this, 'mytheme_add_buttons' ) );
		add_filter( 'mce_buttons', array( $this, 'mytheme_register_buttons' ) );
	}



	public function mytheme_add_buttons( $plugin_array ) {
		$buttons = array( 'rpatitle', 'rpaimage', 'rpahr', 'vimeo', 'mwcc', 'impact', 'radiorpa' );

		foreach ( $buttons as $name ) {
			$plugin_array[ $name ] = "{$this->plugin->dir_url}js/admin.js";
		}

		return $plugin_array;
	}


	public function mytheme_register_buttons( $buttons ) {
		array_push( $buttons, 'rpatitle', 'rpaimage', 'rpahr', 'vimeo', 'mwcc', 'impact', 'radiorpa' );
		return $buttons;
	}


	/**
	 * @action after_wp_tiny_mce
	 */
	public function mytheme_tinymce_extra_vars() {
		?>
			<script type="text/javascript">
				var tinyMCE_object = <?php echo wp_json_encode(
						array(
							'button_name' => esc_html__( 'Title', 'rpa-content-format' ),
							'button_title' => esc_html__( 'Custom Title', 'rpa-content-format' ),
							'image_name' => esc_html__( 'Image', 'rpa-content-format' ),
							'image_title' => esc_html__( 'Custom Image', 'rpa-content-format' ),
							'hr_name' => esc_html__( 'HR', 'rpa-content-format' ),
							'hr_title' => esc_html__( 'Custom hr', 'rpa-content-format' ),
							'radio_name' => esc_html__( 'Radio', 'rpa-content-format' ),
							'radio_title' => esc_html__( 'Radio Shortcode', 'rpa-content-format' ),
							'vimeo_name' => esc_html__( 'Vimeo', 'rpa-content-format' ),
							'vimeo_title' => esc_html__( 'Vimeo', 'rpa-content-format' ),
							'impact_name' => esc_html__( 'Impact', 'rpa-content-format' ),
							'impact_title' => esc_html__( 'Impact', 'rpa-content-format' ),
							'module_name' => esc_html__( 'Module', 'rpa-content-format' ),
							'module_title' => esc_html__( 'Module Shortcode', 'rpa-content-format' ),
						)
					);
					?>;
			</script>
		<?php
	}

	/**
	 * Create menu page for vimeo uploading.
	 *
	 * @action admin_menu
	 */
	public function create_vimeo_menu() {
		add_menu_page(
			__( 'Vimeo Video Upload', 'sharethis-share-buttons' ),
			__( 'Vimeo Upload', 'sharethis-share-buttons' ),
			'manage_options',
			'rpa-vimeo-upload',
			array( $this, 'vimeo_upload_menu_display' ),
			'dashicons-video-alt3',
			24
		);
	}

	/**
	 * Call back for vimeo admin menu display
	 */
	public function vimeo_upload_menu_display() {
		global $current_user;

		include_once( "{$this->plugin->dir_path}/template/vimeo-upload.php" );
	}
}
