<?php
/**
 * Test_Auto_Quote
 *
 * @package AutoQuote
 */

namespace AutoQuote;

/**
 * Class Test_Auto_Quote
 *
 * @package AutoQuote
 */
class Test_Auto_Quote extends \WP_UnitTestCase {

	/**
	 * Test _auto_quote_php_version_error().
	 *
	 * @see _auto_quote_php_version_error()
	 */
	public function test_auto_quote_php_version_error() {
		ob_start();
		_auto_quote_php_version_error();
		$buffer = ob_get_clean();

		$this->assertContains( '<div class="error">', $buffer );
	}

	/**
	 * Test _auto_quote_php_version_text().
	 *
	 * @see _auto_quote_php_version_text()
	 */
	public function test_auto_quote_php_version_text() {
		$this->assertContains( 'Auto Quote plugin error:', _auto_quote_php_version_text() );
	}
}
