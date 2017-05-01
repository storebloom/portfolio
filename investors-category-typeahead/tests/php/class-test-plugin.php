<?php
/**
 * Tests for Plugin class.
 *
 * @package InvestorsCategoryTypeahead
 */

namespace InvestorsCategoryTypeahead;

/**
 * Tests for Plugin class.
 *
 * @package InvestorsCategoryTypeahead
 */
class Test_Plugin extends \WP_UnitTestCase {

	/**
	 * Test constructor.
	 */
	public function test_construct() {
		$plugin_class_name = __NAMESPACE__ . '\Plugin';

		if ( class_exists( $plugin_class_name ) ) {
			$this->plugin = new $plugin_class_name();
		}

		$this->assertNotEmpty( $this->plugin );
	}

	/**
	 * Test registered admin assets.
	 */
	public function register_admin_assets() {
		$this->assertEquals( 10, has_action( 'admin_enqueue_scripts', array( $plugin, 'register_admin_assets' ) ) );
	}
}
