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
 * Class Test_WPPR_Widgets
 */
class Test_WPPR_Widgets extends WP_UnitTestCase {

	/**
	 * Test the top reviews widget.
	 *
	 * @dataProvider postTypeDataProvider
	 */
	function test_top_reviews_widget( $post_type ) {
		// these will be enabled as reviews.
		$posts		= $this->factory->post->create_many( 12, array( 'post_type' => $post_type ) );
		// these will not be enabled as reviews.
		$more		= $this->factory->post->create_many( 3, array( 'post_type' => $post_type ) );
		$model		= new WPPR_Query_Model();
		$tag_tax	= 'post_tag';
		$cat_tax	= 'category';

		switch ( $post_type ) {
			case 'post':
				break;
			case 'wppr_review':
				$model->wppr_set_option( 'wppr_cpt', 'yes' );
				$tag_tax	= 'wppr_tag';
				$cat_tax	= 'wppr_category';
				
				do_action( 'init' );
				break;
		}

		$cat1		= wp_insert_category( array( 'cat_name' => 'test1', 'taxonomy' => $cat_tax ) );
		$cat2		= wp_insert_category( array( 'cat_name' => 'test2', 'taxonomy' => $cat_tax ) );
		$cat3		= wp_insert_category( array( 'cat_name' => 'test3', 'taxonomy' => $cat_tax ) );
		$tag1		= wp_create_term( 'tag1', $tag_tax );
		$tag2		= wp_create_term( 'tag2', $tag_tax );
		$tag3		= wp_create_term( 'tag3', $tag_tax );

		$tags		= array( 'tag1', 'tag2', 'tag3' );
		$cats		= array( $cat1, $cat2, $cat3 );

		// assign one category and one tag to each post.
		for ( $x = 0; $x < count( $posts ); $x++ ) {
			wp_set_post_terms( $posts[ $x ], $cats[ $x%3 ], $cat_tax );
			wp_set_post_terms( $posts[ $x ], $tags[ $x%3 ], $tag_tax );
			update_post_meta( $posts[ $x ], 'cwp_meta_box_check', 'Yes' );
			// one post per month.
			wp_update_post( array( 'ID' => $posts[ $x ], 'post_date' => sprintf( '2018-%s-01 12:12:12', str_pad( $x, 2, '0', STR_PAD_LEFT ) ) ) );
		}

		// fetch only by post type.
		$reviews	= $model->find(
			array( 
				'post_type' => array( $post_type ),
			)
		);
		$this->assertEquals( count( $posts ), count( $reviews ) );

		// fetch only by post type and limit.
		$reviews	= $model->find(
			array( 
				'post_type' => array( $post_type ),
			),
			7
		);
		$this->assertEquals( 7, count( $reviews ) );

		// fetch by post type and category id.
		$reviews	= $model->find(
			array( 
				'post_type' => array( $post_type ),
				'category_id' => $cat1,
			)
		);
		$this->assertEquals( count( $posts )/3, count( $reviews ) );

		// fetch by post type and category name.
		$reviews	= $model->find(
			array( 
				'post_type' => array( $post_type ),
				'category_name' => 'test1',
			)
		);
		$this->assertEquals( count( $posts )/3, count( $reviews ) );

		// fetch by post type and tag.
		$reviews	= $model->find(
			array( 
				'post_type' => array( $post_type ),
				'taxonomy_name' => $tag_tax,
				'category_name' => 'tag1',
			)
		);
		$this->assertEquals( count( $posts )/3, count( $reviews ) );

		// fetch by post type and date after.
		$reviews	= $model->find(
			array( 
				'post_type' => array( $post_type ),
				'post_date_range' => array( '2018-06-15', '' ),
			)
		);
		$this->assertEquals( count( $posts )/2, count( $reviews ) );

		// fetch by post type and date before.
		$reviews	= $model->find(
			array( 
				'post_type' => array( $post_type ),
				'post_date_range' => array( '', '2018-06-15' ),
			)
		);
		$this->assertEquals( count( $posts )/2, count( $reviews ) );
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