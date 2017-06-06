<?php
/**
 * Tests for Personal_Tags.
 *
 * @package InvestorsPersonalTags
 */

namespace InvestorsPersonalTags;

/**
 * Tests for Personal_Tags.
 *
 * @package InvestorsPersonalTags
 */
class Test_Personal_Tags extends \WP_UnitTestCase {

	/**
	 * Plugin instance.
	 *
	 * @var Plugin
	 */
	public $plugin;

	/**
	 * Personal_Tags class.
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
		$this->class = new Personal_Tags( $this->plugin );

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
	}


	/**
	 * Test post tag callback.
	 */
	public function test_checkbox_post_tag() {
		$this->create_post();
		$post_tag = get_taxonomy( 'post_tag' );

		$this->assertInternalType( 'object', $post_tag->meta_box_cb );
	}

	/**
	 * Test getter for user personal post tags.
	 */
	public function test_get_user_tags() {
		$this->assertInternalType( 'array', $this->class->get_user_tags() );
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
