<?php
/**
 * Auto Quote.
 *
 * @package AutoQuote
 */
namespace AutoQuote;
/**
 * Auto Quote Class.
 *
 * Holds the logic for he auto quote shortcode.
 *
 * @package AutoQuote
 */
class Auto_Quote {
	/**
	 * Plugin instance.
	 *
	 * @var Plugin
	 */
	public $plugin;

	/**
	 * Menu slug.
	 *
	 * @var string
	 */
	public $menu_slug;

	/**
	 * Menu hook suffix.
	 *
	 * @var string
	 */
	private $hook_suffix;

	/**
	 * Holds the settings sections.
	 *
	 * @var string
	 */
	public $setting_sections;

	/**
	 * Holds the settings fields.
	 *
	 * @var string
	 */
	public $setting_fields;

	/**
	 * Class constructor.
	 *
	 * @param object $plugin Plugin class.
	 */
	public function __construct( $plugin ) {
		$this->plugin = $plugin;
		$this->menu_slug = 'auto_quote';
		$this->set_settings();

		// Registering shortcode for Auto Quote.
		add_shortcode( 'auto-quote', array( $this, 'display_auto_quote' ) );
	}

	/**
	 * Add the Auto Quote settings page.
	 *
	 * @action admin_menu
	 * @access public
	 */
	public function define_settings_page() {
		$this->hook_suffix = add_options_page(
			'Auto Quote Settings',
			'Auto Quote',
			'manage_options',
			$this->menu_slug,
			array( $this, 'settings_page' )
			);
	}

	/**
	 * Enqueue admin assets.
	 *
	 * @action admin_enqueue_scripts
	 * @param string $hook_suffix The current admin page.
	 */
	public function enqueue_admin_assets( $hook_suffix ) {
		// Only euqueue assets on this plugin admin menu.
		if ( $hook_suffix !== $this->hook_suffix ) {
			return;
		}

		// Get options from settings page.
		$color = get_option( 'auto-quote-color', '#464646' );
		$size  = get_option( 'auto-quote-size', 'medium' );
		$font  = get_option( 'auto-quote-font', 'Arial, sans-serif' );

		wp_enqueue_script( "{$this->plugin->assets_prefix}-admin" );
		wp_enqueue_style( "{$this->plugin->assets_prefix}-admin" );
		wp_add_inline_script( "{$this->plugin->assets_prefix}-admin", sprintf( 'AutoQuoteAdmin.boot( %s );',
			wp_json_encode( array(
				'color' => $color,
				'size'  => $size,
				'font'  => $font,
			) )
		) );

		// Color Picker Styles
		wp_enqueue_style( 'wp-color-picker' );
	}

	/**
	 * Call back function for the settings page.
	 */
	public function settings_page() {
		if ( !current_user_can( 'manage_options' ) )  {
			wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
		}

		include_once( "{$this->plugin->dir_path}/templates/settings-page.php" );
	}

	/**
	 * Set the settings sections and fields.
	 */
	private function set_settings() {
		// Sections config.
		$this->setting_sections = array(
			__( 'Style Options:', 'auto-quote' ),
		);

		// Settings config.
		$this->setting_fields = array(
			array(
				'id_suffix' => 'color',
				'title'     => __( 'Choose color of font.', 'auto-quote' ),
			),
			array(
				'id_suffix' => 'size',
				'title'     => __( 'Choose size of font.', 'auto-quote' ),
			),
			array(
				'id_suffix' => 'font',
				'title'     => __( 'Choose font style.', 'auto-quote' ),
			),
		);
	}

	/**
	 * Define all settings fields for settings page.
	 *
	 * @action admin_init
	 * @access public
	 */
	public function settings_api_init() {
		// Register sections and settings.
		foreach ( $this->setting_sections as $index => $title ) {
			$section = "auto_quote_{$index}";

			// Add setting section.
			add_settings_section(
				$section,
				$title,
				null,
				$this->menu_slug
			);
		}

		// Register settings and add fields.
		foreach ( $this->setting_fields as $index => $setting ) {
			$id = 'auto-quote-' . $setting['id_suffix'];

			// Register setting.
			register_setting( $this->menu_slug, $id );

			// Add field.
			add_settings_field(
				$id,
				$setting['title'],
				array( $this, 'setting_templates' ),
				$this->menu_slug,
				"auto_quote_0",
				$setting['id_suffix']
			);
		}
	}

	/**
	 * Callback function for option templates.
	 */
	public function setting_templates( $name ) {
		// Check user capabilities.
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		$option_array = $this->get_option_array( $name );

		include( "{$this->plugin->dir_path}/templates/settings/{$name}.php" );
	}

	/**
	 * Returns an array of options per request.
	 *
	 * @param string $type
	 * @return array
	 */
	private function get_option_array( $type ) {
		$return = '';

		switch ( $type ) {
			case 'size' :
				$return = array(
					__( 'Small', 'auto-quote' ),
					__( 'Medium', 'auto-quote' ),
					__( 'Large', 'auto-quote' ),
					__( 'XLarge', 'auto-quote' ),
				);
			break;
			case 'font' :
				$return = array(
					__( 'Arial, sans-serif', 'auto-quote' ),
					__( 'Georgia, serif', 'auto-quote' ),
					__( 'Times New Roman, serif', 'auto-quote' ),
					__( 'Courier, monospace', 'auto-quote' ),
				);
			break;
		}

		return $return;
	}

	/**
	 * Display function for the shortcode.
	 *
	 * @access public
	 */
	public function display_auto_quote() {
		// Get options from settings page.
		$color = get_option( 'auto-quote-color', '#464646' );
		$size  = get_option( 'auto-quote-size', 'medium' );
		$font  = get_option( 'auto-quote-font', 'Arial, sans-serif' );

		// Enqueue the scripts and styles only if shortcode is used.
		wp_enqueue_style( "{$this->plugin->assets_prefix}-quote" );
		wp_enqueue_script( "{$this->plugin->assets_prefix}-quote" );
		wp_add_inline_script( "{$this->plugin->assets_prefix}-quote", sprintf( 'AutoQuote.boot( %s );',
			wp_json_encode( array(
				'color' => $color,
				'size'  => $size,
				'font'  => $font,
			) )
		) );

		include_once( "{$this->plugin->dir_path}/templates/quote-wrapper.php" );
	}
}