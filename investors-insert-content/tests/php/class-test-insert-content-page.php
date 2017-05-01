<?php
/**
 * Tests for Insert Content Page.
 *
 * @package InvestorsInsertContent
 */

namespace InvestorsInsertContent;

/**
 * Tests for Insert_Content_Page.
 *
 * @package InvestorsInsertContent
 */
class Test_Insert_Content_Page extends \WP_UnitTestCase {

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

		wp_set_current_user( $this->factory->user->create(
			array(
				'role'       => 'administrator',
				'user_login' => 'test_admin',
				'email'      => 'test@land.com',
			)
		) );

		$this->assertNotEmpty( $this->class );
	}

	/**
	 * Test promo page assets.
	 */
	public function test_enqueue_admin_assets() {
		do_action( 'admin_menu' );
		do_action( 'admin_enqueue_scripts', 'toplevel_page_inpage_promo' );

		$this->assertTrue( wp_style_is( "{$this->plugin->assets_prefix}-insert-content-admin" ) );
	}

	/**
	 * Test admin page output.
	 */
	public function test_insert_content_display() {
		$this->assertTrue( file_exists( "{$this->plugin->dir_path}/templates/admin.php" ) );

		ob_start();
		$this->class->insert_content_display();

		$this->assertContains( '<form action="options.php"', ob_get_clean() );
	}

	/**
	 * Test settings registration.
	 */
	public function test_insert_content_settings_api_init() {
		global $wp_settings_sections, $wp_registered_settings, $wp_settings_fields;

		$this->class->insert_content_settings_api_init();
		$this->assertArrayHasKey( $this->class->menu_slug, $wp_settings_sections );

		if ( ! isset( $wp_settings_sections[ $this->class->menu_slug ] ) ) {
			$this->markTestIncomplete( 'Setting sections page could not be found.' );
		}

		$sections = $wp_settings_sections[ $this->class->menu_slug ];

		foreach ( $this->class->setting_sections as $index => $title ) {
			// Since the index starts at 0, let's increment it by 1.
			$i = $index + 1;
			$section = "ic_section_{$i}";

			// Test section registration.
			$this->assertArrayHasKey( $section, $sections );

			if ( ! isset( $sections[ $section ] ) ) {
				$this->markTestIncomplete( 'Settings section could not be found.' );
			}

			// Test section args.
			$this->assertEquals( $section, $sections[ $section ]['id'] );
			$this->assertEquals( $title, $sections[ $section ]['title'] );

			// Register settings and add fields.
			foreach ( $this->class->setting_fields as $setting ) {
				$id = "setting{$i}_" . $setting['id_suffix'];

				// Test field registration.
				$this->assertArrayHasKey( $id, $wp_registered_settings );

				$conditions = (
					isset( $wp_registered_settings[ $id ] )
					&&
					isset( $wp_settings_fields[ $this->class->menu_slug ][ $section ][ $id ] )
				);

				if ( true !== $conditions ) {
					$this->markTestIncomplete( 'Settings field could not be found.' );
				}

				$field = $wp_settings_fields[ $this->class->menu_slug ][ $section ][ $id ];

				// Test field args.
				$this->assertEquals( $id, $field['id'] );
				$this->assertEquals( $setting['title'], $field['title'] );
				$this->assertContains( $this->class, $field['callback'] );
				$this->assertContains( $setting['callback'], $field['callback'] );
				$this->assertContains( $id, $field['args'] );
				$this->assertContains( $i, $field['args'] );
			}
		} // End foreach().
	}

	/**
	 * Test the text call back function.
	 */
	public function test_ic_text_cb() {
		ob_start();
		$this->class->ic_text_cb( array( 'value' ) );

		$this->assertContains( '<input name="value"', ob_get_clean() );
	}

	/**
	 * Test the cat call back function.
	 */
	public function test_ic_cat_cb() {
		ob_start();
		$this->class->ic_cat_cb( array( 'value' ) );

		$this->assertContains( '<ul class="promo-cat-box"', ob_get_clean() );
	}
}
