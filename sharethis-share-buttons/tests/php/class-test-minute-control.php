<?php
/**
 * Tests for Minute Control.
 *
 * @package ShareThisShareButtons
 */

namespace ShareThisShareButtons;

/**
 * Tests for Minute_Control.
 *
 * @package ShareThisShareButtons
 */
class Test_Minute_Control extends \WP_Ajax_UnitTestCase {

	/**
	 * Plugin instance.
	 *
	 * @var Plugin
	 */
	public $plugin;

	/**
	 * Minute_Control class.
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
		// We need the admin user to run various tests.
		wp_set_current_user( $this->factory->user->create( array(
			'role' => 'administrator',
		) ) );

		parent::setUp();
		$this->plugin = get_plugin_instance();
		$this->class  = new Minute_Control( $this->plugin );

		$this->assertNotEmpty( $this->class );
	}

	/**
	 * Test meta box.
	 */
	public function test_share_buttons_metabox() {
		global $wp_meta_boxes;

		// Call add_meta_boxes action so that we can test if the meta box is registered.
		do_action( 'add_meta_boxes' );

		// Test metabox.
		$this->assertArrayHasKey( 'sharethis_share_buttons', $wp_meta_boxes['post']['side']['high'] );
	}

	/**
	 * Test Minute Control meta box assets.
	 */
	public function test_enqueue_admin_assets() {
		// Create post for global post object to exist.
		$this->create_post();

		// Bring us to the admin.
		do_action( 'admin_menu' );

		// Enqueue scripts on the post.php admin page.
		do_action( 'admin_enqueue_scripts', 'post.php' );

		// Test assets enqueue.
		$this->assertTrue( wp_script_is( "{$this->plugin->assets_prefix}-meta-box" ) );
		$this->assertTrue( wp_style_is( "{$this->plugin->assets_prefix}-meta-box" ) );

		$script_data = wp_scripts()->get_data( "{$this->plugin->assets_prefix}-meta-box", 'after' );

		if ( empty( $script_data ) ) {
			$this->markTestIncomplete( 'Script data could not be found.' );
		}

		// Test inline script boot.
		$this->assertNotFalse( stripos( wp_json_encode( $script_data ), 'MinuteControl.boot(' ) );
	}

	/**
	 * Share buttons meta box view test.
	 */
	public function test_share_buttons_custom_box() {
		$this->assertTrue( file_exists( "{$this->plugin->dir_path}templates/minute-control/meta-box.php" ) );
	}

	/**
	 * Test the list updating ajax call back for the meta box
	 */
	public function test_update_list() {
		$postid = $this->create_post();

		$_POST['nonce'] = wp_create_nonce( $this->plugin->meta_prefix );
		$_POST['type'] = 'inline';
		$_POST['checked'] = 'true';
		$_POST['placement'] = 'top';
		$_POST['postid'] = $postid;

		// Test that proper error response will show if no newsletters are in the template.
		try {
			$this->_handleAjax( 'update_list' );
		} catch ( \WPAjaxDieContinueException $e ) {
			$exception = $e;
		}

		$this->assertNotContains( 'Add to list failed.', $this->_last_response );
		$this->assertArrayHasKey( 'test post', get_option( 'sharethis_inline_post_top_on' ) );
	}

	/**
	 * Test helper function if box should be checked.
	 */
	public function test_is_box_checked() {
		global $post_type;

		$postid = $this->create_post();

		// Set global post type since not available durring test.
		$post_type = 'post'; // WPCS: override ok.

		set_current_screen( 'post.php' );

		update_option( 'sharethis_inline_settings', array(
			'sharethis_inline_post_top' => 'true',
		) );
		update_option( 'sharethis_inline_post_top_on', array(
			'test post' => $postid,
		) );

		$return = $this->invoke_method( 'is_box_checked', array( 'inline', '_top' ) );

		$this->assertEquals( 'true', $return );
	}

	/**
	 * Test inline shortcode.
	 */
	public function test_inline_shortcode() {
		$return = do_shortcode( '[sharethis-inline-buttons]' );

		$this->assertContains( '<div class="sharethis-inline-share-buttons', $return );
	}

	/**
	 * Test setting the content inline button container.
	 */
	public function test_set_inline_content() {
		global $post;

		$postid = $this->create_post();

		// Create option for test.
		update_option( 'sharethis_inline_settings',  array(
			'sharethis_inline_post_top' => 'true',
			'sharethis_inline_post_bottom' => 'true',
			'sharethis_excerpt' => 'false',
		) );

		$output = $this->class->set_inline_content( $post->post_content );

		$this->assertEquals( '<div style="" class="sharethis-inline-share-buttons" ></div>Test content.<div style="" class="sharethis-inline-share-buttons" ></div>', $output );
	}

	/**
	 * Test the sticky visiblity function.
	 */
	public function test_set_sticky_visibility() {
		// Add option to show sticky buttons on post.
		update_option( 'sharethis_sticky_settings', array(
			'sharethis_sticky_post' => 'false',
		) );

		// Create post to set post object.
		$this->create_post();

		// Enqueue scripts for the style to be enqueued.
		do_action( 'wp_enqueue_scripts' );

		// Make sure style enqueued.
		$this->assertTrue( wp_style_is( "{$this->plugin->assets_prefix}-sticky" ) );

		$hide_sticky = '
               .st-sticky-share-buttons{
                        display: none!important;
                }';
		$style_data = wp_styles()->get_data( "{$this->plugin->assets_prefix}-sticky", 'after' );

		if ( empty( $style_data ) ) {
			$this->markTestIncomplete( 'Style data could not be found.' );
		}

		$this->assertEquals( $style_data[0], $hide_sticky );
	}

	/**
	 * Call protected/private method of a class.
	 *
	 * @param string $method Method name to call.
	 * @param array  $parameters Array of parameters to pass into method.
	 *
	 * @return mixed Method return.
	 */
	private function invoke_method( $method, array $parameters = array() ) {
		$reflection = new \ReflectionClass( get_class( $this->class ) );
		$method = $reflection->getMethod( $method );
		$method->setAccessible( true );

		return $method->invokeArgs( $this->class, $parameters );
	}

	/**
	 * Create post.
	 *
	 * @return int The create post id.
	 */
	private function create_post() {
		global $post;

		$post_id = $this->factory->post->create( array(
			'post_title'   => 'test post',
			'post_status'  => 'publish',
			'post_content' => 'Test content.',
		) );

		// WPCS: @codingStandardsIgnoreStart as it is ok to modify the post global in this case.
		$post = get_post( $post_id );
		// WPCS: @codingStandardsIgnoreEnd

		return $post_id;
	}
}
