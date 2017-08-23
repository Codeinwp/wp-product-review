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

/**
 * Class Test_WPPR
 */
class Test_WPPR extends WP_UnitTestCase {

	/**
	 * A single example test.
	 */
	function test_core() {
		$p = $this->factory->post->create( array(
			'post_title' => 'Test Post',
		) );

		$review = new WPPR_Review_Model( $p );

		// Check new Review is not active
		$this->assertFalse( $review->is_active() );
		// Activate Review for this post.
		$review->activate();
		// Check new Review is active
		$this->assertTrue( $review->is_active() );

		$options = array(
			array(
				'name' => 'Excellent',
				'value' => '100',
			),
			array(
				'name' => 'Great',
				'value' => '90',
			),
			array(
				'name' => 'Good',
				'value' => '80',
			),
			array(
				'name' => 'Average',
				'value' => '70',
			),
			array(
				'name' => 'Bad',
				'value' => '50',
			),
		);
		$review->set_options( $options );
		// Check rating is as expected
		$this->assertEquals( 78, $review->get_rating() );

		$review_data = $review->get_review_data();
		$review_data['name'] = 'Test param change';
		$review_data['price'] = '10.00$';
		$review->set_name( $review_data['name'] );
		$review->set_price( $review_data['price'] );
		// Check Param save
		$this->assertEquals( $review_data, $review->get_review_data() );

		$settings = new WPPR_Options_Model();
		$settings->wppr_set_option( 'cwppos_option_nr', 5 );
		$review->wppr_set_option( 'cwppos_show_userreview', 'yes' );
		$this->assertEquals( 5, $settings->wppr_get_option( 'cwppos_option_nr' ) );
		$this->assertEquals( 'yes', $review->wppr_get_option( 'cwppos_show_userreview' ) );

		$c = $this->factory->comment->create( array(
			'comment_post_ID' => $p,
			'comment_content' => 'Test Comment',
			'status' => 'approve',
		) );
		add_comment_meta( $c, 'meta_option_1', 5.4 );
		add_comment_meta( $c, 'meta_option_2', 4.3 );
		add_comment_meta( $c, 'meta_option_3', 3.2 );
		add_comment_meta( $c, 'meta_option_4', 2.3 );
		add_comment_meta( $c, 'meta_option_5', 1.2 );
		$this->assertEquals( 3.04, $review->get_comments_rating() );
		$review->wppr_set_option( 'cwppos_infl_userreview', '30' );
		$this->assertEquals( 55.51, number_format( $review->get_rating(), 2 ) );

		// var_dump( $review );
	}

    public function test_sdk_exists() {
        $this->assertTrue( class_exists( 'ThemeIsle_SDK_Loader' ) );
    }
}
