<?php

/**
 * WordPress unit test plugin.
 *
 * @package     WPPR
 * @subpackage  Tests
 * @copyright   Copyright (c) 2017, ThemeIsle
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since 3.0.10
 */
class Test_WPPR extends WP_UnitTestCase {
	/**
	 * Check if we have SDK loaded.
	 */
	public function test_sdk_exists() {
		$this->assertTrue( class_exists( 'ThemeIsle_SDK_Loader' ) );
	}
}
