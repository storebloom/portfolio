<?php
/**
 * Test_Prodigy_Commerce
 *
 * @package ProdigyCommerce
 */

namespace ProdigyCommerce;

/**
 * Class Test_Prodigy_Commerce
 *
 * @package ProdigyCommerce
 */
class Test_Prodigy_Commerce extends \WP_UnitTestCase {

	/**
	 * Test _prodigy_commerce_php_version_error().
	 *
	 * @see _prodigy_commerce_php_version_error()
	 */
	public function test_prodigy_commerce_php_version_error() {
		ob_start();
		_prodigy_commerce_php_version_error();
		$buffer = ob_get_clean();

		$this->assertContains( '<div class="error">', $buffer );
	}

	/**
	 * Test _prodigy_commerce_php_version_text().
	 *
	 * @see _prodigy_commerce_php_version_text()
	 */
	public function test_prodigy_commerce_php_version_text() {
		$this->assertContains( 'Prodigy Commerce plugin error:', _prodigy_commerce_php_version_text() );
	}
}
