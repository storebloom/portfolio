<?php
/**
 * Tests for Plugin_Base.
 *
 * @package InvestorsCategoryTypeahead
 */

namespace InvestorsCategoryTypeahead;

/**
 * Tests for Plugin_Base.
 *
 * @package InvestorsCategoryTypeahead
 */
class Test_Plugin_Base extends \WP_UnitTestCase {

	/**
	 * Plugin instance.
	 *
	 * @var Plugin
	 */
	public $plugin;

	/**
	 * Setup.
	 *
	 * @inheritdoc
	 */
	public function setUp() {
		parent::setUp();
		$this->plugin = get_plugin_instance();
	}

	/**
	 * Test locate_plugin.
	 *
	 * @see Plugin_Base::locate_plugin()
	 */
	public function test_locate_plugin() {
		$location = $this->plugin->locate_plugin();

		$this->assertEquals( 'investors-category-typeahead', $location['dir_basename'] );
		$this->assertContains( 'plugins/investors-category-typeahead', $location['dir_path'] );
		$this->assertContains( 'plugins/investors-category-typeahead', $location['dir_url'] );
	}

	/**
	 * Test relative_path.
	 *
	 * @see Plugin_Base::relative_path()
	 */
	public function test_relative_path() {
		$this->assertEquals( 'plugins/investors-category-typeahead', $this->plugin->relative_path( '/srv/www/wordpress-develop/src/wp-content/plugins/investors-category-typeahead', 'wp-content', '/' ) );
		$this->assertEquals( 'themes/twentysixteen/plugins/investors-category-typeahead', $this->plugin->relative_path( '/srv/www/wordpress-develop/src/wp-content/themes/twentysixteen/plugins/investors-category-typeahead', 'wp-content', '/' ) );
	}


	/**
	 * Test add_doc_hooks().
	 *
	 * @see Plugin_Base::add_doc_hooks()
	 */
	public function test_add_doc_hooks() {
		$object = new Test_Doc_Hooks();

		$this->assertFalse( has_action( 'init', array( $object, 'init_action' ) ) );
		$this->assertFalse( has_action( 'the_content', array( $object, 'the_content_filter' ) ) );
		$this->plugin->add_doc_hooks( $object );
		$this->assertEquals( 10, has_action( 'init', array( $object, 'init_action' ) ) );
		$this->assertEquals( 10, has_action( 'the_content', array( $object, 'the_content_filter' ) ) );
		$this->plugin->remove_doc_hooks( $object );
	}

	/**
	 * Test add_doc_hooks().
	 *
	 * @see Plugin_Base::add_doc_hooks()
	 */
	public function test_add_doc_hooks_error() {
		$mock = $this->getMockBuilder( 'InvestorsCategoryTypeahead\Plugin' )->getMock();
		$obj = $this;

		// @codingStandardsIgnoreStart
		set_error_handler( function ( $errno, $errstr ) use ( $obj, $mock ) {
			$obj->assertEquals( sprintf( 'The add_doc_hooks method was already called on %s. Note that the Plugin_Base constructor automatically calls this method.', get_class( $mock ) ), $errstr );
			$obj->assertEquals( \E_USER_NOTICE, $errno );
		} );
		// @codingStandardsIgnoreEnd
		$mock->add_doc_hooks();
		restore_error_handler();

		$mock->remove_doc_hooks();
	}

	/**
	 * Test remove_doc_hooks().
	 *
	 * @see Plugin_Base::remove_doc_hooks()
	 */
	public function test_remove_doc_hooks() {
		$object = new Test_Doc_Hooks();

		$this->plugin->add_doc_hooks( $object );
		$this->assertEquals( 10, has_action( 'init', array( $object, 'init_action' ) ) );
		$this->assertEquals( 10, has_action( 'the_content', array( $object, 'the_content_filter' ) ) );
		$this->plugin->remove_doc_hooks( $object );
		$this->assertFalse( has_action( 'init', array( $object, 'init_action' ) ) );
		$this->assertFalse( has_action( 'the_content', array( $object, 'the_content_filter' ) ) );
	}
}

/**
 * Test_Doc_Hooks class.
 */
class Test_Doc_Hooks {

	/**
	 * Load this on the init action hook.
	 *
	 * @action init
	 */
	public function init_action() {}

	/**
	 * Load this on the the_content filter hook.
	 *
	 * @filter the_content
	 *
	 * @param string $content The content.
	 * @return string
	 */
	public function the_content_filter( $content ) {
		return $content;
	}
}
