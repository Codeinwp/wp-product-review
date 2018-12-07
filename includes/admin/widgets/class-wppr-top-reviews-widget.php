<?php
/**
 * The WPPR Top Reviews Widget Class.
 *
 * @package WPPR
 * @subpackage Widget
 * @copyright   Copyright (c) 2017, Bogdan Preda
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since 3.1.1
 */

/**
 * Class WPPR_Top_Reviews_Widget
 */
class WPPR_Top_Reviews_Widget extends WPPR_Widget_Abstract {

	/**
	 * WPPR_Top_Reviews_Widget constructor.
	 *
	 * @since   3.0.0
	 * @access  public
	 */
	public function __construct() {
		parent::__construct(
			'wppr_top_reviews_widget',
			__( 'Top Review Widget', 'wp-product-review' ),
			array(
				'description' => __( 'This widget displays the top reviews based on the rating.', 'wp-product-review' ),
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
		register_widget( 'WPPR_Top_Reviews_Widget' );
	}

	/**
	 * Method to filter posts order.
	 *
	 * @since   3.0.0
	 * @access  public
	 *
	 * @param   string $orderby The condition string for ordering.
	 *
	 * @return string
	 */
	public function custom_order_by( $orderby ) {
		return 'mt1.meta_value DESC, mt2.meta_value+0 DESC';
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
			$array = explode( ':', $instance['cwp_tp_category'] );
			$post['category_name'] = $array[1];
			$post['taxonomy_name'] = $array[0];
		}

		$dates  = array('', '');
		if ( isset( $instance['cwp_timespan_from'] ) && ! empty( $instance['cwp_timespan_from'] ) ) {
			$dates[0] = $instance['cwp_timespan_from'];
		}
		if ( isset( $instance['cwp_timespan_to'] ) && ! empty( $instance['cwp_timespan_to'] ) ) {
			$dates[1] = $instance['cwp_timespan_to'];
		}
		$post['post_date_range'] = $dates;

		if ( isset( $instance['cwp_tp_post_types'] ) && ! empty( $instance['cwp_tp_post_types'] ) ) {
			$post['post_type'] = $instance['cwp_tp_post_types'];
		}
		$order           = array();
		$order['rating'] = 'DESC';

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
			$instance['title'] = __( 'Top Reviews', 'wp-product-review' );
		}

		if ( ! isset( $instance['cwp_timespan_from'] ) || empty( $instance['cwp_timespan_from'] ) ) {
			$instance['cwp_timespan_from'] = '';
		}

		if ( ! isset( $instance['cwp_timespan_to'] ) || empty( $instance['cwp_timespan_to'] ) ) {
			$instance['cwp_timespan_to'] = '';
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
		wp_enqueue_script( 'jquery-ui-slider' );
		wp_enqueue_script( 'jquery-ui-datepicker' );
		wp_enqueue_style( WPPR_SLUG . '-jqueryui', WPPR_URL . '/assets/css/jquery-ui.css', array(), WPPR_LITE_VERSION );

		$deps        = array();
		$deps['js']  = array( 'jquery-ui-slider', 'jquery-ui-datepicker' );
		$deps['css'] = array( WPPR_SLUG . '-jqueryui' );
		return $deps;
	}

	/**
	 * Method to update widget data.
	 *
	 * @since   3.0.0
	 * @access  public
	 *
	 * @param   array $new_instance The new instance array for the widget.
	 * @param   array $old_instance The old instance array of the widget.
	 *
	 * @return array
	 */
	public function update( $new_instance, $old_instance ) {
		$instance = parent::update( $new_instance, $old_instance );

		$instance['cwp_timespan_from'] = ( ! empty( $new_instance['cwp_timespan_from'] ) ) ? strip_tags( $new_instance['cwp_timespan_from'] ) : '';
		$instance['cwp_timespan_to'] = ( ! empty( $new_instance['cwp_timespan_to'] ) ) ? strip_tags( $new_instance['cwp_timespan_to'] ) : '';
		return $instance;
	}

}
