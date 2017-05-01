<?php
/**
 * Test_Investors_Category_Typeahead
 *
 * @package InvestorsCategoryTypeahead
 */

namespace InvestorsCategoryTypeahead;

/**
 * Class Test_Investors_Category_Typeahead
 *
 * @package InvestorsCategoryTypeahead
 */
class Test_Investors_Category_Typeahead extends \WP_UnitTestCase {

	/**
	 * Test _investors_category_typeahead_php_version_error().
	 *
	 * @see _investors_category_typeahead_php_version_error()
	 */
	public function test_investors_category_typeahead_php_version_error() {
		ob_start();
		_investors_category_typeahead_php_version_error();
		$buffer = ob_get_clean();

		$this->assertContains( '<div class="error">', $buffer );
	}

	/**
	 * Test _investors_category_typeahead_php_version_text().
	 *
	 * @see _investors_category_typeahead_php_version_text()
	 */
	public function test_investors_category_typeahead_php_version_text() {
		$this->assertContains( 'Investors Category Typeahead plugin error:', _investors_category_typeahead_php_version_text() );
	}
}
