<?php
/**
 * Investors Insert Content.
 *
 * @package InvestorsInsertContent
 */

namespace InvestorsInsertContent;

/**
 * Insert Content Class.
 *
 * Holds the logic that inserts designated content into pages.
 *
 * @package InvestorsInsertContent
 */
class Insert_Content {

	/**
	 * Plugin instance.
	 *
	 * @var Plugin
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
	 * Add option values to an object that our script can use.
	 *
	 * @action wp_enqueue_scripts
	 * @access public
	 */
	public function promo_page_assets() {
		// Get all category ID's of the post.
		$cat_id     = array();
		$categories = get_the_category();

		foreach ( $categories as $category ) {
			if ( isset( $category->cat_ID, $category->category_parent ) ) {
				array_push( $cat_id, $category->cat_ID );
				array_push( $cat_id, $category->category_parent );
			}
		}

		$promo_data = array(
			'categories' => $cat_id,
		);

		for ( $i = 1; $i <= 5 ; $i++ ) {
			$settings = array(
				"value{$i}"       => get_option( "setting{$i}_text", '' ),
				"value{$i}Link"  => get_option( "setting{$i}_link", '' ),
				"value{$i}Class" => get_option( "setting{$i}_class", '' ),
				"value{$i}Cat"   => is_array( get_option( "setting{$i}_cats" ) ) ? get_option( "setting{$i}_cats" ) : array(),
			);

			$promo_data = array_merge( $promo_data, $settings );
		}

		// Enqueing front end assets.
		wp_enqueue_style( "{$this->plugin->assets_prefix}-insert-content" );
		wp_enqueue_script( "{$this->plugin->assets_prefix}-insert-content" );
		wp_add_inline_script( "{$this->plugin->assets_prefix}-insert-content", sprintf( 'InvestorsInsertContent.boot( %s );',
			wp_json_encode( array(
				'investorsPromoVar' => $promo_data,
			) )
		) );
	}
}
