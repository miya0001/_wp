<?php
/**
 * Class SampleTest
 *
 * @package _wp
 */

/**
 * Sample test case.
 */
class SampleTest extends WP_UnitTestCase
{
	/**
	 * A single example test.
	 */
	function test_sample()
	{
		// Replace this with some actual testing code.
		$this->assertTrue( true );
	}
//
//	public function test_image_resize()
//	{
//		$editor = wp_get_image_editor( dirname( __FILE__ ) . '/test.jpg' );
//		$editor->set_quality( 60 );
//		$editor->resize( 1000, 1000 );
//		$editor->save( 'saved-100.jpg' );
//	}
}
