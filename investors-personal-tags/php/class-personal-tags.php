<?php
/**
 * Personal Tab.
 *
 * @package InvestorsPersonalTags
 */

namespace InvestorsPersonalTags;

/**
 * Personal Tab Class
 *
 * @package InvestorsPersonalTags
 */
class Personal_Tags {

	/**
	 * Plugin class.
	 *
	 * @var object
	 */
	public $plugin;

	/**
	 * User tags meta id.
	 *
	 * @var string
	 */
	private $user_tags_meta_id;

	/**
	 * Class constructor.
	 *
	 * @param object $plugin Plugin class.
	 */
	public function __construct( $plugin ) {
		$this->plugin = $plugin;
		$this->user_tags_meta_id = "{$this->plugin->meta_prefix}_personal_tags";
	}

	/**
	 * Enqueue admin scripts.
	 *
	 * @action admin_enqueue_scripts
	 */
	public function enqueue_admin_scripts() {
		// Don't load assets if post tags aren't used.
		if ( true !== is_object_in_taxonomy( get_post_type(), 'post_tag' ) ) {
			return;
		}

		wp_enqueue_script( "{$this->plugin->assets_prefix}-admin" );
		wp_add_inline_script( "{$this->plugin->assets_prefix}-admin", sprintf( '%s.boot( %s );',
			__NAMESPACE__,
			wp_json_encode( array(
				'nonce'    => wp_create_nonce( $this->plugin->meta_prefix ),
				'userTags' => $this->get_user_tags(),
				'tabName'  => __( 'My Tags', 'Investors' ),
			) )
		) );
	}

	/**
	 * Build the post tags taxonomy metabox as a checkbox.
	 *
	 * @action init
	 */
	public function checkbox_post_tag() {
		$post_tag = get_taxonomy( 'post_tag' );

		if ( is_object( $post_tag ) ) {
			$post_tag->meta_box_cb = function( $post, $box ) {
				$callback = function() {
					return false;
				};

				// Remove parent dropdown.
				add_filter( 'wp_dropdown_cats', $callback );

				// Display checkbox.
				post_categories_meta_box( $post, $box );

				// Reset parent dropdown.
				remove_filter( 'wp_dropdown_cats', $callback );
			};
		}
	}

	/**
	 * Add user personal post tag.
	 *
	 * @action wp_ajax_add_tag
	 */
	public function add_tag() {
		check_ajax_referer( $this->plugin->meta_prefix, 'nonce' );

		if ( ! isset( $_POST['tag'] ) ) { // WPCS: input var okay.
			wp_send_json_error();
		}

		$tag = intval( wp_unslash( $_POST['tag'] ) ); // WPCS: input var okay.
		$user_tags = $this->get_user_tags();

		// Add tag.
		if ( ! in_array( $tag, $user_tags, true ) ) {
			$user_tags[] = $tag;
		}

		// Update user tags.
		update_user_meta( get_current_user_id(), $this->user_tags_meta_id, $user_tags );

		wp_send_json_success( $user_tags );
	}

	/**
	 * Remove user personal post tag.
	 *
	 * @action wp_ajax_remove_tag
	 */
	public function remove_tag() {
		check_ajax_referer( $this->plugin->meta_prefix, 'nonce' );

		if ( ! isset( $_POST['tag'] ) ) { // WPCS: input var okay.
			wp_send_json_error();
		}

		$tag = intval( wp_unslash( $_POST['tag'] ) ); // WPCS: input var okay.
		$user_tags = $this->get_user_tags();

		// Remove tag.
		$user_tags = array_values( array_diff( $user_tags, array( $tag ) ) );

		// Update user tags.
		update_user_meta( get_current_user_id(), $this->user_tags_meta_id, $user_tags );

		wp_send_json_success( $user_tags );
	}


	/**
	 * Getter for user personal post tags.
	 */
	public function get_user_tags() {
		$user_tags = (array) get_user_meta( get_current_user_id(), $this->user_tags_meta_id, true );

		return array_filter( $user_tags );
	}
}
