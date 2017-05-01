<?php
/**
 * Insert Content Page.
 *
 * @package InvestorsInsertContent
 */

namespace InvestorsInsertContent;

/**
 * Insert Content Page Class
 *
 * Contains the html and needed functions for the option page.
 *
 * @package InvestorsInsertContent
 */
class Insert_Content_Page {

	/**
	 * Plugin instance.
	 *
	 * @var object
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
		$this->menu_slug = 'inpage_promo';
		$this->set_settings();
	}

	/**
	 * Set the settings sections and fields.
	 */
	private function set_settings() {
		// Sections config.
		$this->setting_sections = array(
			__( 'Free members and Visitors (0 or 1) Promo Message:', 'investors-insert-content' ),
			__( 'Core subscribers (2, 3, 4, 6, or 7) Promo Message:', 'investors-insert-content' ),
			__( 'Leaderboard subscribers (8 or 9) Promo Message:', 'investors-insert-content' ),
			__( 'SwingTrader subscribers (10 or 11) Promo Message:', 'investors-insert-content' ),
			__( 'MarketSmith subscribers (12 or 13) Promo Message:', 'investors-insert-content' ),
		);

		// Settings config.
		$this->setting_fields = array(
			array(
				'id_suffix' => 'text',
				'title'     => __( 'Copy for the promo message', 'investors-insert-content' ),
				'callback'  => 'ic_text_cb',
			),
			array(
				'id_suffix' => 'link',
				'title'     => __( 'Link URL for promo', 'investors-insert-content' ),
				'callback'  => 'ic_text_cb',
			),
			array(
				'id_suffix' => 'class',
				'title'     => __( 'Link Style Class', 'investors-insert-content' ),
				'callback'  => 'ic_text_cb',
			),
			array(
				'id_suffix' => 'cats',
				'title'     => __( 'Categories To Exclude', 'investors-insert-content' ),
				'callback'  => 'ic_cat_cb',
			),
		);
	}

	/**
	 * Add in page promo menu option.
	 *
	 * @action admin_menu
	 * @access public
	 */
	public function define_insert_content_section() {
		$this->hook_suffix = add_menu_page(
			__( 'Insert Page Promo Admin Page', 'investors-insert-content' ),
			__( 'Insert Page Promo', 'investors-insert-content' ),
			'manage_options',
			$this->menu_slug,
			array( $this, 'insert_content_display' ),
			'dashicons-media-document',
			24
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

		wp_enqueue_style( "{$this->plugin->assets_prefix}-insert-content-admin" );
	}

	/**
	 * Display In Page Promo Option Page View.
	 */
	public function insert_content_display() {
		// Check user capabilities.
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		include_once( "{$this->plugin->dir_path}/templates/admin.php" );
	}

	/**
	 * Define all setting sections for in page promos.
	 *
	 * @action admin_init
	 * @access public
	 */
	public function insert_content_settings_api_init() {
		// Register sections and settings.
		foreach ( $this->setting_sections as $index => $title ) {
			// Since the index starts at 0, let's increment it by 1.
			$i = $index + 1;
			$section = "ic_section_{$i}";

			// Add setting section.
			add_settings_section(
				$section,
				$title,
				null,
				$this->menu_slug
			);

			// Register settings and add fields.
			foreach ( $this->setting_fields as $setting ) {
				$id = "setting{$i}_" . $setting['id_suffix'];

				// Register setting.
				register_setting( 'inpage_promo', $id );

				// Add field.
				add_settings_field(
					$id,
					$setting['title'],
					array( $this, $setting['callback'] ),
					$this->menu_slug,
					$section,
					array( $id, $i )
				);
			}
		} // End foreach().
	}

	/**
	 * This is the section 1 call back function for text fields.
	 *
	 * @param array $args The arguments passed to the setting html call back function.
	 */
	public function ic_text_cb( $args ) {
		if ( ! isset( $args[0] ) ) {
			return;
		}

		$option = get_option( $args[0] );

		?>
		<input name="<?php echo esc_attr( $args[0] ); ?>" id="<?php echo esc_attr( $args[0] ); ?>" type="text" value="<?php echo esc_html( $option ); ?>" size="100" class="code" />
		<?php
	}

	/**
	 * This is the section 1 call back function for the category checklist.
	 *
	 * @param array $args The arguments passed to the setting html call back function.
	 */
	public function ic_cat_cb( $args ) {
		if ( ! isset( $args[0] ) ) {
			return;
		}

		$categories = wp_terms_checklist( 0, array(
			'selected_cats' => get_option( $args[0] ),
			'echo'          => false,
		) );

		// Replace input names.
		$categories = str_replace( 'post_category[]', $args[0] . '[]', $categories );

		?>
		<ul class="promo-cat-box">
			<?php echo $categories; // WPCS: XSS ok. ?>
		</ul>
		<?php
	}
}
