<?php
/**
 * Tests for Insert Content.
 *
 * @package InvestorsInsertContent
 */

namespace InvestorsInsertContent;

/**
 * Tests for Insert_Content.
 *
 * @package InvestorsInsertContent
 */
class Test_Insert_Content extends \WP_UnitTestCase {

	/**
	 * Plugin instance.
	 *
	 * @var Plugin
	 */
	public $plugin;

	/**
	 * Insert_Content_Page class.
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
		$this->class  = new Insert_Content_Page( $this->plugin );

		$this->assertNotEmpty( $this->class );
	}

	/**
	 * Test promo page assets.
	 */
	public function test_promo_page_assets() {
		do_action( 'wp_enqueue_scripts' );

		// Test assets enqueue.
		$this->assertTrue( wp_script_is( "{$this->plugin->assets_prefix}-insert-content" ) );
		$this->assertTrue( wp_style_is( "{$this->plugin->assets_prefix}-insert-content" ) );

		$script_data = wp_scripts()->get_data( "{$this->plugin->assets_prefix}-insert-content", 'after' );

		if ( empty( $script_data ) ) {
			$this->markTestIncomplete( 'Script data could not be found.' );
		}

		// Test inline script boot.
		$this->assertNotFalse( stripos( wp_json_encode( $script_data ), 'InvestorsInsertContent.boot(' ) );
	}
}
