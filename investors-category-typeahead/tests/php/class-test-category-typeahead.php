<?php
/**
 * Tests for Plugin_Base.
 *
 * @package InvestorsCategoryTypeahead
 */

namespace InvestorsCategoryTypeahead;

/**
 * Tests for Category_Typeahead.
 *
 * @package InvestorsCategoryTypeahead
 */
class Test_Category_Typeahead extends \WP_UnitTestCase {
	/**
	 * Plugin instance.
	 *
	 * @var Plugin
	 */
	public $plugin;

	/**
	 * Category_Typeahead class.
	 *
	 * @var object
	 */
	public $class;

	/**
	 * Setup.
	 *
	 * @inheritdoc
	 */
	public function setUp() {
		parent::setUp();
		$this->plugin = get_plugin_instance();
		$this->class = new Category_Typeahead( $this->plugin );

		$this->assertNotEmpty( $this->class );
	}

	/**
	 * Test enqueue admin scripts.
	 */
	public function test_enqueue_admin_scripts() {
		$post_id = $this->create_post();
		set_current_screen( 'post' );
		do_action( 'admin_enqueue_scripts' );

		$this->assertTrue( wp_script_is( "{$this->plugin->assets_prefix}-admin" ) );
		$this->assertTrue( wp_style_is( "{$this->plugin->assets_prefix}-admin" ) );
	}

	/**
	 * Create post.
	 *
	 * @return int The create post id.
	 */
	private function create_post() {
		global $post;

		$post_id = $this->factory->post->create( array(
			'post_title'  => 'Test',
			'post_status' => 'publish',
		) );

		// WPCS: @codingStandardsIgnoreStart as it is ok to modify the post global in this case.
		$post = get_post( $post_id );
		// WPCS: @codingStandardsIgnoreEnd

		return $post_id;
	}
}
