<?php
/**
 * The WPPR Latest Widget Class.
 *
 * @package WPPR
 * @subpackage Widget
 * @copyright   Copyright (c) 2017, Bogdan Preda
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since 3.0.0
 */

/**
 * Class WPPR_Latest_Products_Widget
 */
class WPPR_Latest_Products_Widget extends WPPR_Widget_Abstract {

	/**
	 * WPPR_Latest_Products_Widget constructor.
	 *
	 * @since   3.0.0
	 * @access  public
	 */
	public function __construct() {
		parent::__construct(
			'cwp_latest_products_widget',
			__( 'Latest Products Widget', 'wp-product-review' ),
			array(
				'description' => __( 'This widget displays the latest products based on their rating.', 'wp-product-review' ),
			)
		);
	}

	/**
	 * Method to register the widget.
	 *
	 * @since   3.0.0
	 * @access  public
	 */
	public function register() {
		register_widget( 'WPPR_Latest_Products_Widget' );
	}

	/**
	 * Method for displaying the widget on the front end.
	 *
	 * @since   3.0.0
	 * @access  public
	 *
	 * @param   array $args Arguments for this method.
	 * @param   array $instance Instance array for the widget.
	 *
	 * @return mixed
	 */
	public function widget( $args, $instance ) {
		$instance = parent::widget( $args, $instance );

		$reviews = new WPPR_Query_Model();
		$post    = array();
		if ( isset( $instance['cwp_tp_category'] ) && trim( $instance['cwp_tp_category'] ) != '' ) {
			$post['category_name'] = $instance['cwp_tp_category'];
		}
		if ( isset( $instance['cwp_tp_post_types'] ) && ! empty( $instance['cwp_tp_post_types'] ) ) {
			$post['post_type'] = $instance['cwp_tp_post_types'];
		}
		$order         = array();
		$order['date'] = 'DESC';

		$results = $reviews->find( $post, $instance['no_items'], array(), $order );
		if ( ! empty( $results ) ) {
			$first  = reset( $results );
			$first  = isset( $first['ID'] ) ? $first['ID'] : 0;
			$review = new WPPR_Review_Model( $first );
			$this->assets( $review );
		}
		// before and after widget arguments are defined by themes
		echo $args['before_widget'];

		if ( ! empty( $instance['title'] ) ) {
			echo $args['before_title'] . $instance['title'] . $args['after_title'];
		}
		$template = new WPPR_Template();
		$template->render(
			'widget/' . $instance['cwp_tp_layout'],
			array(
				'results'      => $results,
				'title_length' => self::RESTRICT_TITLE_CHARS,
				'instance'     => $instance,
			)
		);

		echo $args['after_widget'];
	}

	/**
	 * The admin widget form method.
	 *
	 * @since   3.0.0
	 * @access  public
	 *
	 * @param   array $instance The instance array for this widget.
	 *
	 * @return mixed
	 */
	public function form( $instance ) {
		$this->adminAssets();
		if ( ! isset( $instance['title'] ) ) {
			$instance['title'] = __( 'Latest Products', 'wp-product-review' );
		}

		$instance = parent::form( $instance );

		include( WPPR_PATH . '/includes/admin/layouts/widget-admin-tpl.php' );
	}

	/**
	 * Load public assets specific to this widget.
	 *
	 * @since   3.0.0
	 * @access  public
	 */
	public function load_assets() {
		// empty.
	}

	/**
	 * Load admin assets specific to this widget.
	 *
	 * @since   3.0.0
	 * @access  public
	 */
	public function load_admin_assets() {
		// empty.
	}
}
