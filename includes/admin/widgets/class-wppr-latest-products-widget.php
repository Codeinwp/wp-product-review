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
		$this->assets();

		$instance = parent::widget( $args, $instance );

		$reviews = new WPPR_Query_Model();
		$post    = array();
		if ( isset( $instance['cwp_tp_category'] ) && trim( $instance['cwp_tp_category'] ) != '' ) {
			$post['category_name'] = $instance['cwp_tp_category'];
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

		include WPPR_PATH . '/includes/public/layouts/widget/' . $instance['cwp_tp_layout'];

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

		$instance          = parent::form( $instance );
		$instance['title'] = __( 'Latest Products', 'wp-product-review' );

		include( WPPR_PATH . '/includes/admin/layouts/widget-admin-tpl.php' );
	}

	/**
	 * Method for loading admin widget assets.
	 *
	 * @since   3.0.0
	 * @access  public
	 */
	public function adminAssets() {
		if ( is_admin() ) {
			wp_enqueue_style( WPPR_SLUG . '-widget-admin-css', WPPR_URL . '/assets/css/cwppos-widget-admin.css', array(), WPPR_LITE_VERSION );

			wp_register_script( WPPR_SLUG . '-widget-script-latest', WPPR_URL . '/assets/js/widget-latest.js', array(), WPPR_LITE_VERSION );
			wp_localize_script(
				WPPR_SLUG . '-widget-script-latest', 'wppr_widget_localized_data', array(
					'widgetName'    => $this->id_base,
					'imageCheckbox' => $this->get_field_id( 'show_image' ),
					'ratingSelect'  => $this->get_field_id( 'cwp_tp_rating_type' ),
				)
			);
			wp_enqueue_script( WPPR_SLUG . '-widget-script-latest' );
		}
	}
}
