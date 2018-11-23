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
			),
			$limit,
			$filter,
			$order
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
			'category_id'           => false,
			'category_name'         => false,
			'post_type'             => array( 'post', 'page' ),
			'post_date_range_weeks' => false,
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
			'date'   => false,
		)
	) {
		if ( ! is_numeric( $limit ) && $limit >= 0 ) {
			$limit = 20;
		}
		if ( ! isset( $post['post_type'] ) ) {
			$types          = array( 'post', 'page' );
			if ( 'yes' === $this->wppr_get_option( 'wppr_cpt' ) ) {
				$types[]    = 'wppr_review';
			}
			$post['post_type'] = $types;
		}
		$sub_query_posts = $this->get_sub_query_posts( $post );

		$order_by         = $this->get_order_by( $order );
		$conditions       = $this->get_query_conditions( $post, $filter );
		$conditions_where = '';
		if ( isset( $conditions['where'] ) ) {
			$conditions_where = $conditions['where'];
		}
		$conditions_having = '';
		if ( isset( $conditions['having'] ) ) {
			$conditions_having = $conditions['having'];
		}

		$final_rating       = '`rating`';
		$comment_influence = intval( $this->wppr_get_option( 'cwppos_infl_userreview' ) );
		if ( $comment_influence > 0 ) {
			$final_rating   = "IF(`comment_rating` = 0, `rating`, (`comment_rating` * 10 * ( $comment_influence / 100 ) + `rating` * ( ( 100 - $comment_influence ) / 100 ) ) )";
		}

		$final_order        = isset( $order['rating'] ) && in_array( $order['rating'], array( 'ASC', 'DESC' ) ) ? " ORDER BY `final_rating` {$order['rating']}" : '';

		$query   = " 
		SELECT ID, post_date, post_title, `check`, `name`, `price`, `rating`, `comment_rating`, FORMAT($final_rating, 2) as 'final_rating' FROM
		(
        SELECT 
			ID,
			post_date,
			post_title,
            GROUP_CONCAT( DISTINCT IF( `meta_key` = 'cwp_meta_box_check', `meta_value`, '' ) SEPARATOR '' ) AS 'check', 
            GROUP_CONCAT( DISTINCT IF( `meta_key` = 'cwp_rev_product_name', `meta_value`, '' ) SEPARATOR '' ) AS 'name',   
            GROUP_CONCAT( DISTINCT IF( `meta_key` = 'cwp_rev_price', FORMAT( `meta_value`, 2 ), '' ) SEPARATOR '' ) AS 'price', 
			GROUP_CONCAT( DISTINCT IF( `meta_key` = 'wppr_rating', IF(FORMAT(`meta_value`, 2) = '100.00','99.99', FORMAT(`meta_value`, 2) ), '') SEPARATOR '' ) AS 'rating',
            GROUP_CONCAT( DISTINCT IF( `meta_key` = 'wppr_comment_rating', `meta_value`, '') SEPARATOR '' ) AS 'comment_rating'
        FROM {$this->db->postmeta} m INNER JOIN {$this->db->posts} p on p.ID = m.post_ID
        
        {$sub_query_posts}
        where p.post_status = 'publish' 
         {$conditions_where}
        GROUP BY `ID` 
        HAVING `check` = 'Yes' 
        {$conditions_having}
        ORDER BY 
        {$order_by}
        `name` ASC
        LIMIT {$limit}
		) T1 $final_order
        ";

		do_action( 'themeisle_log_event', WPPR_SLUG, sprintf( 'post = %s, limit = %s, filter = %s, order = %s and query = %s', print_r( $post, true ), $limit, print_r( $filter, true ), print_r( $order, true ), $query ), 'debug', __FILE__, __LINE__ );

		$key     = hash( 'sha256', $query );
		$results = wp_cache_get( $key, 'wppr' );
		if ( ! is_array( $results ) ) {
			$results = $this->db->get_results( $query, ARRAY_A );
			if ( ! WPPR_CACHE_DISABLED ) {
				wp_cache_set( $key, $results, 'wppr', ( 60 * 60 ) );
			}
		}// End if().

		return $results;
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
		// TODO Build validation methods for category name and id and reuse them here and in get_sub_query_conditions method.
		if ( ! isset( $post['category_name'] ) && ! isset( $post['category_id'] ) ) {
			return '';
		}

		$category   = 'yes' === $this->wppr_get_option( 'wppr_cpt' ) ? 'wppr_category' : 'category';
		$sub_selection_query = "INNER JOIN {$this->db->term_relationships } wtr ON wtr.object_id = p.ID
	            INNER JOIN {$this->db->term_taxonomy} wtt on wtt.term_taxonomy_id = wtr.term_taxonomy_id AND wtt.taxonomy = '$category'
	            INNER JOIN {$this->db->terms} wt
	            ON wt.term_id = wtt.term_id";

		return $sub_selection_query;
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
			$order_by .= "`rating` {$order['rating']}, ";
		}

		if ( isset( $order['price'] ) && in_array( $order['price'], array( 'ASC', 'DESC' ) ) ) {
			$order_by .= "`price` {$order['price']}, ";
		}
		if ( isset( $order['date'] ) && in_array( $order['date'], array( 'ASC', 'DESC' ) ) ) {
			$order_by .= "`post_date` {$order['date']}, ";
		}

		$order_by       .= apply_filters( 'wppr_order_by_clause', '', $order );

		return $order_by;
	}

	/**
	 * Build the query conditions.
	 *
	 * @since   2.0.0
	 * @access  private
	 *
	 * @param   array|false $post The fields to filter data by.
	 * @param   array|false $filter The post details to filter data by.
	 *
	 * @return array
	 */
	private function get_query_conditions( $post, $filter ) {
		$conditions          = array( 'where' => '', 'having' => '' );
		$conditions['where'] = $this->get_sub_query_conditions( $post );
		if ( isset( $filter['name'] ) && $filter['name'] != false ) {
			$conditions['having'] .= $this->db->prepare( ' AND `name` LIKE %s ', '%' . $filter['name'] . '%' );
		}

		// TODO comparision arguments for price filter.
		if ( isset( $filter['price'] ) && $filter['price'] != false && is_numeric( $filter['price'] ) ) {
			$conditions['having'] .= $this->db->prepare( ' AND `price` > FORMAT( %d, 2 ) ', $filter['price'] );
		}
		// TODO comparision arguments for rating filter.
		if ( isset( $filter['rating'] ) && $filter['rating'] != false && is_numeric( $filter['rating'] ) ) {
			$conditions['having'] .= $this->db->prepare( ' AND `rating`  > %f ', $filter['rating'] );
		}

		$conditions     = apply_filters( 'wppr_where_clause', $conditions, $post, $filter );

		return $conditions;
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
		if ( isset( $post['category_id'] ) && $post['category_id'] != false && is_numeric( $post['category_id'] ) && $post['category_id'] > 0 ) {
			$sub_query_conditions .= $this->db->prepare( " AND wt.term_id = '%d' ", $post['category_id'] );
		}

		if ( isset( $post['category_name'] ) && $post['category_name'] != false ) {
			$sub_query_conditions .= $this->db->prepare( ' AND wt.slug = %s ', $post['category_name'] );
		}
		// TODO Check against available post_types.
		if ( isset( $post['post_type'] ) && is_array( $post['post_type'] ) ) {
			$filter_post_type      = array_fill( 0, count( $post['post_type'] ), ' p.post_type = %s ' );
			$filter_post_type      = implode( ' OR ', $filter_post_type );
			$filter_post_type      = ' AND ( ' . $filter_post_type . ' ) ';
			$sub_query_conditions .= $this->db->prepare( $filter_post_type, $post['post_type'] );
		}

		if ( isset( $post['post_date_range_weeks'] ) && ! is_bool( $post['post_date_range_weeks'] ) && is_array( $post['post_date_range_weeks'] ) ) {
			$min                   = reset( $post['post_date_range_weeks'] );
			$max                   = end( $post['post_date_range_weeks'] );
			$sub_query_conditions .= $this->db->prepare( ' AND p.post_date >= DATE_ADD(now(), INTERVAL %d WEEK) AND p.post_date <= DATE_ADD(now(), INTERVAL %d WEEK) ', $min, $max );
		}

		$sub_query_conditions       .= apply_filters( 'wppr_where_sub_clause', '', $post );

		return $sub_query_conditions;
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
			),
			$limit,
			$filter,
			$order
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
			false,
			$limit,
			array(
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
			false,
			$limit,
			array(
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
			false,
			$limit,
			array(
				'rating' => $rating,
			)
		);
	}
}
