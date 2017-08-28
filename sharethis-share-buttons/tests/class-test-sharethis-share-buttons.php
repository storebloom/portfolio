<?php
/**
 * Test_ShareThis_Share_Buttons
 *
 * @package ShareThisShareButtons
 */

namespace ShareThisShareButtons;

/**
 * Class Test_ShareThis_Share_Buttons
 *
 * @package TopStory
 */
class Test_ShareThis_Share_Buttons extends \WP_UnitTestCase {
	/**
	 * Test _sharethis_share_buttons_php_version_error().
	 *
	 * @see _sharethis_share_buttons_php_version_error()
	 */
	public function test_sharethis_share_buttons_php_version_error() {
		ob_start();
		_sharethis_share_buttons_php_version_error();
		$buffer = ob_get_clean();
		$this->assertContains( '<div class="error">', $buffer );
	}
	/**
	 * Test _sharethis_share_buttons_php_version_text().
	 *
	 * @see _sharethis_share_buttons_php_version_text()
	 */
	public function test_sharethis_share_buttons_php_version_text() {
		$this->assertContains( 'ShareThis Share Buttons plugin error:', _sharethis_share_buttons_php_version_text() );
	}
}
