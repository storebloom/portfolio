<?php
/**
 * Test_Investors_Insert_Content
 *
 * @package InvestorsInsertContent
 */

namespace InvestorsInsertContent;

/**
 * Class Test_Investors_Insert_Content
 *
 * @package InvestorsInsertContent
 */
class Test_Investors_Insert_Content extends \WP_UnitTestCase {

	/**
	 * Test _investors_insert_content_php_version_error().
	 *
	 * @see _investors_insert_content_php_version_error()
	 */
	public function test_investors_insert_content_php_version_error() {
		ob_start();
		_investors_insert_content_php_version_error();
		$buffer = ob_get_clean();

		$this->assertContains( '<div class="error">', $buffer );
	}

	/**
	 * Test _investors_insert_content_php_version_text().
	 *
	 * @see _investors_insert_content_php_version_text()
	 */
	public function test_investors_insert_content_php_version_text() {
		$this->assertContains( 'Investors Insert Content plugin error:', _investors_insert_content_php_version_text() );
	}
}
