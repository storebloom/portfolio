<?php
/**
 * Test_Investors_Personal_Tags
 *
 * @package InvestorsPersonalTags
 */

namespace InvestorsPersonalTags;

/**
 * Class Test_Investors_Personal_Tags
 *
 * @package InvestorsPersonalTags
 */
class Test_Investors_Personal_Tags extends \WP_UnitTestCase {

	/**
	 * Test _investors_personal_tags_php_version_error().
	 *
	 * @see _investors_personal_tags_php_version_error()
	 */
	public function test_investors_personal_tags_php_version_error() {
		ob_start();
		_investors_personal_tags_php_version_error();
		$buffer = ob_get_clean();

		$this->assertContains( '<div class="error">', $buffer );
	}

	/**
	 * Test _investors_personal_tags_php_version_text().
	 *
	 * @see _investors_personal_tags_php_version_text()
	 */
	public function test_investors_personal_tags_php_version_text() {
		$this->assertContains( 'Investors Personal Tags plugin error:', _investors_personal_tags_php_version_text() );
	}
}
