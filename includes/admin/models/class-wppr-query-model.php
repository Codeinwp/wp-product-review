<?php
/**
 * The file that defines a model class for easier access to DB
 * functionality, an abstract layer for core and addons to use.
 *
 * @link       https://themeisle.com
 * @since      2.0.0
 *
 * @package    WPPR_Pro
 * @subpackage WPPR_Pro/includes/models
 */

/**
 * Class WPPR_Query_Model
 *
 * A model class for abstracting DB actions.
 *
 * @since       2.0.0
 * @package     WPPR_Pro
 * @subpackage  WPPR_Pro/includes/model
 */
class WPPR_Query_Model extends WPPR_Model_Abstract {

	/**
	 * Holds an instance of WPPR_Review_Model
	 *
	 * @since   2.0.0
	 * @access  protected
	 * @var     WPPR_Review_Model $review Holds an instance of WPPR_Review_Model.
	 */
	protected $review;

	/**
	 * Holds an instance of the WP DB Object
	 *
	 * @since   2.0.0
	 * @access  private
	 * @var     WPDB $db Holds an instance of the WP DB Object.
	 */
	private $db;

	/**
	 * WPPR_Pro_Model constructor.
	 *
	 * @since   2.0.0
	 * @access  public
	 */
	public function __construct() {
		parent::__construct();

		global $wpdb;
		$this->db = $wpdb;
	}

	/**
	 * Utility method to return a product by ID.
	 *
	 * @since   2.0.0
	 * @access  public
	 *
	 * @param   int $post_id The post ID.
	 *
	 * @return array
	 */
	public function find_by_id( $post_id ) {
		return $this->find(
			array(
				'post_id' => $post_id,
			)
		);
	}

	/**
	 * Mai utility method to retrive an array of products.
	 *
	 * @since   2.0.0
	 * @access  public
	 *
	 * @param   array|false $post The post data to filter by.
	 * @param   int         $limit The limit for the returned results.
	 * @param   array|false $filter The fields to filter data by.
	 * @param   array|false $order The fields to order by and the order.
	 *
	 * @return array
	 */
	public function find(
		$post = array(
			'post_id'       => false,
			'category_id'   => false,
			'category_name' => false,
		),
		$limit = 20,
		$filter = array(
			'name'   => false,
			'price'  => false,
			'rating' => false,
		),
		$order = array(
			'rating' => false,
			'price'  => false,
		)
	) {

		if ( ! is_numeric( $limit ) && $limit >= 0 ) {
			$limit = 20;
		}

		$sub_query_posts = $this->get_sub_query_posts( $post );

		$order_by = $this->get_order_by( $order );

		$conditions = $this->get_query_conditions( $filter );

		$query = "
        SELECT * FROM (
            SELECT 
                `post_id` AS 'ID',
                GROUP_CONCAT( DISTINCT IF( `meta_key` = 'cwp_meta_box_check', `meta_value`, '' ) SEPARATOR '' ) AS 'cwp_meta_box_check', 
                GROUP_CONCAT( DISTINCT IF( `meta_key` = 'cwp_rev_product_name', `meta_value`, '' ) SEPARATOR '' ) AS 'cwp_rev_product_name', 
                GROUP_CONCAT( DISTINCT IF( `meta_key` = 'cwp_image_link', `meta_value`, '' ) SEPARATOR '' ) AS 'cwp_image_link', 
                GROUP_CONCAT( DISTINCT IF( `meta_key` = 'cwp_rev_product_image', `meta_value`, '' ) SEPARATOR '' ) AS 'cwp_rev_product_image', 
                GROUP_CONCAT( DISTINCT IF( `meta_key` = 'cwp_rev_price', FORMAT( `meta_value`, 2 ), '' ) SEPARATOR '' ) AS 'cwp_rev_price', 
                GROUP_CONCAT( DISTINCT IF( `meta_key` = 'wppr_rating', `meta_value`, '' ) SEPARATOR '' ) AS 'wppr_rating',
                GROUP_CONCAT( DISTINCT IF( `meta_key` = 'wppr_pros', `meta_value`, '' ) SEPARATOR '' ) AS 'wppr_pros',
                GROUP_CONCAT( DISTINCT IF( `meta_key` = 'wppr_cons', `meta_value`, '' ) SEPARATOR '' ) AS 'wppr_cons',
                GROUP_CONCAT( DISTINCT IF( `meta_key` = 'wppr_options', `meta_value`, '' ) SEPARATOR '' ) AS 'wppr_options',
                GROUP_CONCAT( DISTINCT IF( `meta_key` = 'wppr_links', `meta_value`, '' ) SEPARATOR '' ) AS 'wppr_links'
            FROM {$this->db->postmeta}
           {$sub_query_posts}
            GROUP BY `ID`
        ) `pivoted_meta`
        WHERE `cwp_meta_box_check` = 'Yes'
        {$conditions}
        ORDER BY 
        {$order_by}
        `cwp_rev_product_name` ASC
        LIMIT {$limit}
        ";
		$key           = hash( 'sha256', $query );
		$processed_res = wp_cache_get( $key, 'wppr' );
		if ( ! is_array( $processed_res ) ) {
			$results = $this->db->get_results( $query, ARRAY_A );

			$processed_res = array();
			foreach ( $results as $row ) {

				$row['wppr_pros']    = maybe_unserialize( $row['wppr_pros'] );
				$row['wppr_cons']    = maybe_unserialize( $row['wppr_cons'] );
				$row['wppr_options'] = maybe_unserialize( $row['wppr_options'] );
				$row['wppr_links']   = maybe_unserialize( $row['wppr_links'] );

				if ( empty( $row['wppr_options'] ) || $row['wppr_options'] == '' ) {
					$this->review        = new WPPR_Review_Model( $row['ID'] );
					$row['wppr_options'] = $this->review->get_options();
				}

				if ( empty( $row['wppr_pros'] ) || $row['wppr_pros'] == '' ) {
					$this->review     = new WPPR_Review_Model( $row['ID'] );
					$row['wppr_pros'] = $this->review->get_pros();
				}

				if ( empty( $row['wppr_cons'] ) || $row['wppr_cons'] == '' ) {
					$this->review     = new WPPR_Review_Model( $row['ID'] );
					$row['wppr_cons'] = $this->review->get_cons();
				}

				if ( empty( $row['wppr_links'] ) || $row['wppr_links'] == '' ) {
					$this->review      = new WPPR_Review_Model( $row['ID'] );
					$row['wppr_links'] = $this->review->get_links();
				}

				$processed_res[] = $row;
			}

			wp_cache_set( $key, $processed_res, 'wppr', ( 60 * 60 ) );
		}// End if().

		return $processed_res;
	}

	/**
	 * Build the sub query.
	 *
	 * @since   2.0.0
	 * @access  private
	 *
	 * @param   array|false $post The post data to filter by.
	 *
	 * @return string
	 */
	private function get_sub_query_posts( $post ) {
		$sub_query_conditions = $this->get_sub_query_conditions( $post );
		$sub_selection_query = '';
		if ( ! empty( $sub_query_conditions ) ) {
			$sub_selection_query = "INNER JOIN {$this->db->term_relationships } wtr ON wtr.object_id = p.ID
	            INNER JOIN {$this->db->term_taxonomy} wtt on wtt.term_taxonomy_id = wtr.term_taxonomy_id
	            INNER JOIN {$this->db->terms} wt
	            ON wt.term_id = wtt.term_id";
		}
		$sub_query_posts = "
            WHERE 
            `post_id` IN ( 
	            SELECT `ID` 
	             FROM {$this->db->posts} p  
	            {$sub_selection_query}
	            WHERE 
	            ( p.post_type = 'post' or 
	            p.post_type = 'page' ) AND p.post_status = 'publish'
	            {$sub_query_conditions}
            )
        ";

		return $sub_query_posts;
	}

	/**
	 * Build the sub query conditions.
	 *
	 * @since   2.0.0
	 * @access  private
	 *
	 * @param   array|false $post The post data to filter by.
	 *
	 * @return string
	 */
	private function get_sub_query_conditions( $post ) {
		$sub_query_conditions = '';
		if ( isset( $post['post_id'] ) && $post['post_id'] != false && is_numeric( $post['post_id'] ) && $post['post_id'] > 0 ) {
			$sub_query_conditions .= $this->db->prepare( " AND p.ID = '%d' ", $post['post_id'] );
		}

		if ( isset( $post['category_id'] ) && $post['category_id'] != false && is_numeric( $post['category_id'] ) && $post['category_id'] > 0 ) {
			$sub_query_conditions .= $this->db->prepare( " AND wt.term_id = '%d' ", $post['category_id'] );
		}

		if ( isset( $post['category_name'] ) && $post['category_name'] != false ) {
			$sub_query_conditions .= $this->db->prepare( " AND wt.slug like '%s%' ", $post['category_name'] );
		}

		return $sub_query_conditions;
	}

	/**
	 * Build the order by query part.
	 *
	 * @since   2.0.0
	 * @access  private
	 *
	 * @param   array|false $order The fields to order by and the order.
	 *
	 * @return string
	 */
	private function get_order_by( $order ) {
		$order_by = '';
		if ( isset( $order['rating'] ) && in_array( $order['rating'], array( 'ASC', 'DESC' ) ) ) {
			$order_by .= "`wppr_rating` {$order['rating']}, ";
		}

		if ( isset( $order['price'] ) && in_array( $order['price'], array( 'ASC', 'DESC' ) ) ) {
			$order_by .= "`cwp_rev_price` {$order['price']}, ";
		}
		if ( isset( $order['date'] ) && in_array( $order['date'], array( 'ASC', 'DESC' ) ) ) {
			$order_by .= "`ID` {$order['date']}, ";
		}

		return $order_by;
	}

	/**
	 * Build the query conditions.
	 *
	 * @since   2.0.0
	 * @access  private
	 *
	 * @param   array|false $filter The fields to filter data by.
	 *
	 * @return string
	 */
	private function get_query_conditions( $filter ) {
		$conditions = '';
		if ( isset( $filter['name'] ) && $filter['name'] != false ) {
			$conditions .= $this->db->prepare( " AND `cwp_rev_product_name` LIKE '%%%s%%' ", $filter['name'] );
		}

		if ( isset( $filter['price'] ) && $filter['price'] != false && is_numeric( $filter['price'] ) ) {
			$conditions .= $this->db->prepare( " AND `cwp_rev_price` = FORMAT( '%d', 2 ) ", $filter['price'] );
		}

		if ( isset( $filter['rating'] ) && $filter['rating'] != false && is_numeric( $filter['rating'] ) ) {
			$conditions .= $this->db->prepare( " AND `wppr_rating` = FORMAT( '%d', 2 ) ", $filter['rating'] );
		}

		return $conditions;
	}

	/**
	 * Utility method to return products by category ID.
	 *
	 * @since   2.0.0
	 * @access  public
	 *
	 * @param   int         $cat_id The category ID.
	 * @param   int         $limit Optional. The results limit.
	 * @param   array|false $filter Optional. The filter array.
	 * @param   array|false $order Optional. The order array.
	 *
	 * @return array
	 */
	public function find_by_cat_id( $cat_id, $limit = 20, $filter = array(), $order = array() ) {
		return $this->find(
			array(
				'category_id' => $cat_id,
			), $limit, $filter, $order
		);
	}

	/**
	 * Utility method to return products by category name.
	 *
	 * @since   2.0.0
	 * @access  public
	 *
	 * @param   string      $category The category name.
	 * @param   int         $limit Optional. The results limit.
	 * @param   array|false $filter Optional. The filter array.
	 * @param   array|false $order Optional. The order array.
	 *
	 * @return array
	 */
	public function find_by_category( $category, $limit = 20, $filter = array(), $order = array() ) {
		return $this->find(
			array(
				'category_name' => $category,
			), $limit, $filter, $order
		);
	}

	/**
	 * Utility method to find a product or more by name.
	 *
	 * @since   2.0.0
	 * @access  public
	 *
	 * @param   string $name The name to look for.
	 * @param   int    $limit Optional. The results limit.
	 *
	 * @return array
	 */
	public function find_by_name( $name, $limit = 20 ) {
		return $this->find(
			false, $limit, array(
				'name' => $name,
			)
		);
	}

	/**
	 * Utility method to find a product or more by price.
	 *
	 * @since   2.0.0
	 * @access  public
	 *
	 * @param   float|int $price The price to look for.
	 * @param   int       $limit Optional. The results limit.
	 *
	 * @return array
	 */
	public function find_by_price( $price, $limit = 20 ) {
		return $this->find(
			false, $limit, array(
				'price' => $price,
			)
		);
	}

	/**
	 * Utility method to find a product or more by price.
	 *
	 * @since   2.0.0
	 * @access  public
	 *
	 * @param   float|int $rating The rating to look for.
	 * @param   int       $limit Optional. The results limit.
	 *
	 * @return array
	 */
	public function find_by_rating( $rating, $limit = 20 ) {
		return $this->find(
			false, $limit, array(
				'rating' => $rating,
			)
		);
	}
}
