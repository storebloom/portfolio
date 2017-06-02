<?php
/**
 * Test_Top_Story
 *
 * @package TopStory
 */

namespace TopStory;

/**
 * Class Test_Top_Story
 *
 * @package TopStory
 */
class Test_Top_Story extends \WP_UnitTestCase {

	/**
	 * Test _top_story_php_version_error().
	 *
	 * @see _top_story_php_version_error()
	 */
	public function test_top_story_php_version_error() {
		ob_start();
		_top_story_php_version_error();
		$buffer = ob_get_clean();

		$this->assertContains( '<div class="error">', $buffer );
	}

	/**
	 * Test _top_story_php_version_text().
	 *
	 * @see _top_story_php_version_text()
	 */
	public function test_top_story_php_version_text() {
		$this->assertContains( 'Top Story plugin error:', _top_story_php_version_text() );
	}
}
