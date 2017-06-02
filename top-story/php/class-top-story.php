<?php

/**
 * Top Story.
 *
 * @package TopStory
 */
namespace TopStory;

/**
 * Top Story Class.
 *
 * Holds the logic for the Top Story function.
 *
 * @package TopStory
 */
class Top_Story {

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
	 * Class constructor.
	 *
	 * @param object $plugin Plugin class.
	 */
	public function __construct( $plugin ) {
		$this->plugin    = $plugin;
		$this->menu_slug = 'Top_Story';
	}

	/**
	 * Add the Top Story option page.
	 *
	 * @action admin_menu
	 * @access public
	 */
	public function define_settings_page() {
		$this->hook_suffix = add_options_page(
			__( 'Top Story Options', 'top-story' ),
			__( 'Top Story', 'top-story' ),
			'manage_options',
			$this->menu_slug,
			array( $this, 'option_page' )
		);
	}

	/**
	 * Enqueue front-end styles
	 *
	 * @action wp_enqueue_scripts
	 */
	public function enqueue_front_styles() {
		if ( is_admin() || ! is_single() ) {
			return;
		}

		wp_enqueue_style( "{$this->plugin->assets_prefix}-top-story" );
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

		wp_enqueue_style( "{$this->plugin->assets_prefix}-admin" );
		wp_enqueue_script( "{$this->plugin->assets_prefix}-admin" );
		wp_add_inline_script( "{$this->plugin->assets_prefix}-admin", sprintf( '%s.boot( %s );',
			__NAMESPACE__,
			wp_json_encode( array(
				'nonce'             => wp_create_nonce( $this->plugin->meta_prefix ),
			) )
		) );
	}

	/**
	 * Call back function for the option page.
	 */
	public function option_page() {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( esc_html__( 'You do not have sufficient permissions to access this page.', 'top-story' ) );
		}

		include_once( "{$this->plugin->dir_path}/templates/option-page.php" );
	}

	/**
	 * Define all settings fields for settings page.
	 *
	 * @action admin_init
	 * @access public
	 */
	public function settings_api_init() {
		// Register sections and settings.
		add_settings_section(
			$this->menu_slug,
			__( 'Top Story', 'top-story' ),
			null,
			$this->menu_slug
		);

		// Register settings and add fields.
		register_setting( $this->menu_slug, 'current-top-story' );

		// Add field.
		add_settings_field(
			$this->menu_slug,
			__( 'Search for post by title to replace current top story. (Leave blank to disable.)', 'top-story' ),
			array( $this, 'option_display' ),
			$this->menu_slug,
			$this->menu_slug
		);
	}

	/**
	 * Callback function for option templates.
	 */
	public function option_display() {
		// Check user capabilities.
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		$current_story = get_option( 'current-top-story' );

		echo '<input type="text" name="current-top-story" id="top-story-value" value="' . esc_html( $current_story ) . '" placeholder="search post by title" size="70" />';
	}

	/**
	 * Top Story display function.
	 *
	 * @action loop_end
	 */
	public function display_top_story() {
		if ( is_admin() || ! is_single() ) {
			return;
		}

		$top_post = $this->get_newest_post();
		$top_author = get_userdata( $top_post->post_author );
		$cat = get_the_category( $top_post->ID )[0]->cat_name;

		// Get time stamp data and make sure termanology matches specs.
		$timestamp_time = human_time_diff( get_the_time( 'U', $top_post->ID ), current_time( 'timestamp' ) );
		$m_timestamp = str_replace( array( ' mins', ' min' ), 'm', $timestamp_time ) . ' ago';
		$timestamp = $timestamp_time . ' ago';

		// Search for min(s).
		if ( false !== strpos( $timestamp_time, 'min' ) ) {
			$timestamp = str_replace( 'min', 'minute', $timestamp_time ) . ' ago';
		} elseif ( false !== strpos( $timestamp_time, 'mins' ) ) {
			$timestamp = str_replace( 'mins', 'minutes', $timestamp_time ) . ' ago';
		}

		include_once( "{$this->plugin->dir_path}/templates/top-story-box.php" );
	}

	/**
	 * AJAX Call back function to return post results for option page
	 *
	 * @action wp_ajax_return_posts
	 */
	public function return_posts() {
		// Security check.
		check_ajax_referer( $this->plugin->meta_prefix, 'nonce' );

		if ( ! isset( $_POST['key'] ) || '' === $_POST['key'] ) { // WPCS: input var okay.
			wp_send_json_error( '' );
		}

		$key_input = sanitize_text_field( wp_unslash( $_POST['key'] ) ); // WPCS: input var okay.

		$args = array(
			'posts_per_page'   => 300, // Set post per page to prevent memory leak. Pagination can be added in future.
			'orderby'          => 'date',
			'order'            => 'DESC',
			'post_status'      => 'publish',
		);

		$posts_array = get_posts( $args );

		if ( ! $posts_array ) {
			wp_send_json_error( 'No Posts Found.' );
		}

		foreach ( $posts_array as $post ) {
			if ( false !== stripos( $post->post_title, $key_input ) ) {
				$post_items[] = sprintf(
					'<li class="top-story-item" id="%1$d">%2$s</li>',
					(int) $post->ID,
					esc_html( $post->post_title )
				);
			}
		}

		if ( ! is_array( $post_items ) || 0 > count( $post_items ) ) {
			wp_send_json_error( 'No Posts Found.' );
		}

		wp_send_json_success( $post_items );
	}

	/**
	 * Get the most recent post.
	 *
	 * @access private
	 * @return array
	 */
	private function get_newest_post() {
		// If set in the admin get the top story.
		$newest_post = get_option( 'current-top-story' );
		$title = '' !== $newest_post ? sanitize_text_field( wp_unslash( $newest_post ) ) : '';

		$args = array(
			'numberposts' => 1,
			'orderby' => 'post_date',
			'order' => 'DESC',
			'post_type' => 'post',
			'post_status' => 'publish',
			's' => $title,
		);

		$post_array = wp_get_recent_posts( $args, OBJECT );

		return $post_array[0];
	}
}
