<?php
/**
 * Rpa Branching.
 *
 * @package RpaBranching
 */

namespace RpaBranching;

/**
 * Branching Class
 *
 * @package RpaBranching
 */
class Branching {

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
	 * @action admin_enqueue_scripts
	 */
	public function enqueue_admin_assets() {
		global $post;

		$type = get_post_type( $post->ID );
		$branch = get_post_meta( $post->ID, 'branch_content', true );
		$saved = empty( $branch ) || false === $branch ? false : true;

		if ( $type ) {
			wp_enqueue_style( "{$this->plugin->assets_prefix}-admin" );
			wp_enqueue_script( "{$this->plugin->assets_prefix}-admin" );
			wp_add_inline_script( "{$this->plugin->assets_prefix}-admin", sprintf( 'RpaBranching.boot( %s );',
				wp_json_encode( array(
					'branch_saved' => $saved,
					'id'           => $post->ID,
					'nonce'        => wp_create_nonce( $this->plugin->meta_prefix ),
				) )
			) );
		}
	}

	/**
	 * AJAX Call back function that saves the current post content in a branch.
	 *
	 * @action wp_ajax_save_branch
	 */
	public function save_branch() {
		check_ajax_referer( $this->plugin->meta_prefix, 'nonce' );

		if ( ! isset( $_POST['content'], $_POST['id'] ) ) { // WPCS: input var okay.
			wp_send_json_error( 'Save branch failed.' );
		}

		$content = wp_unslash( $_POST['content'] ); // WPCS: input var okay.  Cannot sanitize text field without losing linebreaks.
		$id = intval( wp_unslash( $_POST['id'] ) ); //WPCS: input var okay.

		// Save the content in the post meta.
		update_post_meta( $id, 'branch_content', $content );
		wp_send_json_success( 'saved' );
	}

	/**
	 * AJAX Call back function that returns the current available branch content.
	 *
	 * @action wp_ajax_load_branch
	 */
	public function load_branch() {
		check_ajax_referer( $this->plugin->meta_prefix, 'nonce' );

		if ( ! isset( $_POST['id'] ) ) { // WPCS: input var okay.
			wp_send_json_error( 'Load branch failed.' );
		}

		$id = intval( wp_unslash( $_POST['id'] ) ); //WPCS: input var okay.

		// Load the content from the post meta.
		$content = get_post_meta( $id, 'branch_content', true );

		wp_send_json_success( $content );
	}
}
