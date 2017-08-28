<?php
/**
 * Tests for Plugin class.
 *
 * @package RpaBranching
 */

namespace RpaBranching;

/**
 * Tests for Plugin class.
 *
 * @package RpaBranching
 */
class Test_Plugin extends \WP_UnitTestCase {

	/**
	 * Test constructor.
	 *
	 * @see Plugin::__construct()
	 */
	public function test_construct() {
		$plugin = new Plugin();

		$this->assertEquals( 10, has_action( 'admin_enqueue_scripts', array( $plugin, 'register_admin_assets' ) ) );
		$this->assertEquals( 10, has_action( 'wp_enqueue_scripts', array( $plugin, 'register_assets' ) ) );
	}

	/**
	 * Test registered admin assets.
	 */
	public function register_admin_assets() {
		$this->assertEquals( 10, has_action( 'admin_enqueue_scripts', array( $plugin, 'register_admin_assets' ) ) );
	}

	/**
	 * Test registered assets.
	 */
	public function register_assets() {
		$this->assertEquals( 10, has_action( 'wp_enqueue_scripts', array( $plugin, 'register_assets' ) ) );
	}
}
