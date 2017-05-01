<?php
/**
 * Category Typeahead.
 *
 * @package InvestorsCategoryTypeahead
 */

namespace InvestorsCategoryTypeahead;

/**
 * Category Typeahead Class
 *
 * @package InvestorsCategoryTypeahead
 */
class Category_Typeahead {

	/**
	 * Plugin class.
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
	 * Enqueue admin scripts.
	 *
	 * @action admin_enqueue_scripts
	 */
	public function enqueue_admin_scripts() {
		// Don't load assets if categories aren't used.
		if ( true !== is_object_in_taxonomy( get_post_type(), 'category' ) ) {
			return;
		}

		wp_enqueue_style( "{$this->plugin->assets_prefix}-admin" );
		wp_enqueue_script( "{$this->plugin->assets_prefix}-admin" );
		wp_add_inline_script( "{$this->plugin->assets_prefix}-admin", sprintf( '%s.boot( %s );',
			__NAMESPACE__,
			wp_json_encode( array(
				'nonce'             => wp_create_nonce( $this->plugin->meta_prefix ),
				'searchPlaceholder' => __( 'Search Categories', 'investors-category-typeahead' ),
			) )
		) );
	}

	/**
	 * Return categories based on input.
	 *
	 * @action wp_ajax_return_categories
	 */
	public function return_categories() {
		check_ajax_referer( $this->plugin->meta_prefix, 'nonce' );

		if ( ! isset( $_POST['key'] ) || '' === $_POST['key'] ) { // WPCS: input var okay.
			wp_send_json_error( '' );
		}

		$key_input = sanitize_text_field( wp_unslash( $_POST['key'] ) ); // WPCS: input var okay.

		// Search category names LIKE $key_input.
		$related_categories = get_categories( array(
			'name__like' => $key_input,
			'hide_empty' => false,
		) );

		// Create output list if any results exist.
		if ( count( $related_categories ) > 0 ) {
			foreach ( $related_categories as $categories ) {
				$category_option[] = sprintf(
					'<li class="ta-cat-item" data-cat-id="%1$d">%2$s</li>',
					(int) $categories->term_id,
					esc_html( $categories->name )
				);
			}

			wp_send_json_success( $category_option );
		} else {
			wp_send_json_error( 'no results' );
		}
	}
}
