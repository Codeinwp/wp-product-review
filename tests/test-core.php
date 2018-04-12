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
	 * Create posts of different post types and associate with different taxonomy and test whether the query model is able to fetch them correctly.
	 */
	function test_review_query_model( /* even number */ $number = 20 ) {
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

		$reviews	= array();
		$post_ids	= array();
		$index		= 0;
		// ensure that the number of tags/cats below is not a factor of $number i.e. $number should not be completely divisible by the count of below.
		$tags		= array( 'tag1', 'tag2', 'tag3' );
		$cats		= array( 'cat1', 'cat2', 'cat3' );
		$cat_ids	= array();
		foreach ( $cats as $cat ) {
			$term	= wp_insert_term( $cat, 'category' );
			$cat_ids[]	= $term['term_id'];
		}

		$posts		= $this->factory->post->create_many( $number, array( 'post_type' => 'post' ) );
		foreach ( $posts as $p ) {
			$post_ids[]	= $p;
			$tag		= $tags[ $index%count($tags) ];
			$cat		= $cats[ $index%count($cats) ];
			wp_set_post_tags( $p, $tag );
			wp_set_object_terms( $p, $cat, 'category' );
			$index++;

			$review = new WPPR_Review_Model( $p );
			$review->activate();
			$this->assertTrue( $review->is_active() );
			$review->set_options( $options );
			$review->set_price( $index * $number );
			if ( 0 === $index%2 ) {
				$review->set_name('custom' . rand());
			}

			$reviews[]	= $review;
		}

		$cpt_ids	= array();
		$index		= 0;
		register_post_type( 'wppr_test' );
		register_taxonomy( 'wppr_test_cat', 'wppr_test', array('hierarchical' => true ) );
		register_taxonomy( 'wppr_test_tag', 'wppr_test', array('hierarchical' => false ) );
		$custom_cat_ids	= array();
		foreach ( $cats as $cat ) {
			$term	= wp_insert_term( $cat, 'wppr_test_cat' );
			$custom_cat_ids[]	= $term['term_id'];
		}
		$posts_test		= $this->factory->post->create_many( $number, array( 'post_type' => 'wppr_test' ) );
		foreach ( $posts_test as $p ) {
			$cpt_ids[]	= $p;
			$tag		= $tags[ $index%count($tags) ];
			$cat		= $cats[ $index%count($cats) ];
			wp_set_object_terms( $p, $tag, 'wppr_test_tag'  );
			wp_set_object_terms( $p, $cat, 'wppr_test_cat' );
			$index++;

			$review = new WPPR_Review_Model( $p );
			$review->activate();
			$this->assertTrue( $review->is_active() );
			$review->set_options( $options );
			$review->set_price( $index * $number );

			$reviews[]	= $review;
		}

		$model		= new WPPR_Query_Model();

		// basic.
		$results	= $model->find();
		$this->assertEquals( $number, count($results));

		// date.
		$results	= $model->find(
			array(
				'post_date_range_weeks'	=> array( 1, 4 ),
			)
		);
		$this->assertEquals( 0, count($results));

		// backward compatibility with name.
		$results	= $model->find(
			array(
				'category_name'	=> 'cat1',
				'post_type'		=> 'post',
			)
		);
		$this->assertEquals( intval(floor($number/count($cats))) + 1, count($results));

		// backward compatibility with id.
		$results	= $model->find(
			array(
				'category_id'	=> $cat_ids[0],
				'post_type'		=> 'post',
			)
		);
		$this->assertEquals( intval(floor($number/count($cats))) + 1, count($results));

		// category, with id.
		$results	= $model->find(
			array(
				'taxonomy'		=> 'category',
				'term_ids'		=> $cat_ids[0],
				'post_type'		=> 'post',
			)
		);
		$this->assertEquals( intval(floor($number/count($cats))) + 1, count($results));

		// custom taxonomy, with id.
		$results	= $model->find(
			array(
				'taxonomy'		=> 'wppr_test_cat',
				'term_ids'		=> $custom_cat_ids[0],
				'post_type'		=> 'post',
			)
		);
		$this->assertEquals( intval(floor($number/count($cats))) + 1, count($results));

		// custom taxonomy, with id and exclude.
		$results	= $model->find(
			array(
				'taxonomy'		=> 'wppr_test_cat',
				'term_ids'		=> $custom_cat_ids[0],
				'post_type'		=> 'wppr_test',
				'exclude'		=> $cpt_ids[0],
			)
		);
		$this->assertEquals( intval(floor($number/count($cats))), count($results));

		// custom taxonomy, with slug,
		$results	= $model->find(
			array(
				'taxonomy'		=> 'wppr_test_tag',
				'term_ids'		=> $tags[0],
				'post_type'		=> 'wppr_test',
			)
		);
		$this->assertEquals( intval(floor($number/count($cats))) + 1, count($results));

		// custom taxonomy, with slug,
		$results	= $model->find(
			array(
				'taxonomy'		=> 'wppr_test_tag',
				'term_ids'		=> $tags,
				'post_type'		=> 'wppr_test',
			)
		);
		$this->assertEquals( $number, count($results));

		/* does not work

		// find by price.
		$results	= $model->find(
			array(),
			200,
			array(
				'price' => $number,
			)
		);
		$this->assertEquals( $number - 1, count($results));
		*/

		// find by name.
		$results	= $model->find(
			array(),
			200,
			array(
				'name' => 'cust',
			)
		);
		$this->assertEquals( $number/2, count($results));

	}

	/**
	 * Create post of a particular post_type and see if it behaves like a review.
	 *
	 * @dataProvider postTypeDataProvider
	 */
	function test_post_review( $post_type ) {
		$is_cpt = 'wppr_review' === $post_type;
		$title	= 'Test Post' . rand();

		if ( $is_cpt ) {
			// enable the CPT feature.
			$model = new WPPR_Query_Model();
			$model->wppr_set_option( 'wppr_cpt', 'yes' );
		}

		do_action( 'init' );

		$p = $this->factory->post->create( array(
			'post_title' => $title,
			'post_type'	=> $post_type,
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

		if ( ! $is_cpt ) {
			$review_data['name'] = 'Test param change';
			$review->set_name( $review_data['name'] );
		}
		$review_data['price'] = floatval( '10.00' );
		$review_data['price_raw'] = floatval( '10.00' );
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
		$this->assertEquals( '63.72', number_format( $review->get_rating(), 2 ) );

		if ( $is_cpt ) {
			// let's navigate to the review page and then see what is the name of the review.
			$this->go_to( get_permalink( $p ) );
			$this->assertEquals( $title, $review->get_name() );
		}
	}

	/**
	 * Provide the different post_types to test with.
	 */
	public function postTypeDataProvider() {
		return array(
			array( 'post' ),
			array( 'wppr_review' ),
		);
	}
}
