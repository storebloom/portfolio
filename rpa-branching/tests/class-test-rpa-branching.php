<?php
/**
 * Test_Rpa_Branching
 *
 * @package RpaBranching
 */

namespace RpaBranching;

/**
 * Class Test_Rpa_Branching
 *
 * @package RpaBranching
 */
class Test_Rpa_Branching extends \WP_UnitTestCase {

	/**
	 * Test class instantiation.
	 */
	public function test_rpa_branching_class_instantiation() {
		$this->class  = new Plugin();

		$this->assertNotEmpty( $this->class );
	}
}
