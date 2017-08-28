<?php
/**
 * Test_Rpa_Content_Format
 *
 * @package RpaContentFormat
 */

namespace RpaContentFormat;

/**
 * Class Test_Rpa_Content_Format
 *
 * @package RpaContentFormat
 */
class Test_Rpa_Content_Format extends \WP_UnitTestCase {

	/**
	 * Test class instantiation.
	 */
	public function test_rpa_content_format_class_instantiation() {
		$this->class = new Plugin_Base();

		$this->assertNotEmpty( $this->class );
	}
}
