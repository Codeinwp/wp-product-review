<?php
/**
 * The WPPR Widget Abstract Class.
 *
 * @package WPPR
 * @subpackage Widget
 * @copyright   Copyright (c) 2017, Bogdan Preda
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since 3.0.0
 */

/**
 * Class WPPR_Widget_Abstract
 */
abstract class WPPR_Widget_Abstract extends WP_Widget {
	const RESTRICT_TITLE_CHARS = 100;

	/**
	 * Load public assets specific to this widget.
	 *
	 * @since   3.0.0
	 * @access  public
	 */
	public abstract function load_assets();

	/**
	 * Load admin assets specific to this widget.
	 *
	 * @since   3.0.0
	 * @access  public
	 */
	public abstract function load_admin_assets();

	/**
	 * Method to load assets required for front end display.
	 *
	 * @since   3.0.0
	 * @access  public
	 */
	public function assets( $review_object ) {
		$dependencies   = $this->load_assets();

		wp_enqueue_style( WPPR_SLUG . '-pac-widget-stylesheet', WPPR_URL . '/assets/css/cwppos-widget.css', isset( $dependencies['css'] ) ? $dependencies['css'] : array(), WPPR_LITE_VERSION );
		wp_enqueue_style( WPPR_SLUG . '-widget-stylesheet-one', WPPR_URL . '/assets/css/cwppos-widget-style1.css', array(WPPR_SLUG . '-pac-widget-stylesheet'), WPPR_LITE_VERSION );
		wp_enqueue_style( WPPR_SLUG . '-widget-rating', WPPR_URL . '/assets/css/cwppos-widget-rating.css', array(WPPR_SLUG . '-pac-widget-stylesheet'), WPPR_LITE_VERSION );

		$plugin = new WPPR();
		$public = new Wppr_Public( $plugin->get_plugin_name(), $plugin->get_version() );

		$public->load_review_assets( $review_object );
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
	 * @return array
	 */
	public function widget( $args, $instance ) {
		$instance['title']           = apply_filters( 'widget_title', $instance['title'] );
		$instance['no_items']        = apply_filters( 'widget_content', $instance['no_items'] );
		$instance['cwp_tp_category'] = apply_filters( 'widget_content', $instance['cwp_tp_category'] );
		if ( isset( $instance['title_type'] ) ) {
			$instance['post_type'] = apply_filters( 'widget_content', $instance['title_type'] );
		} else {
			$instance['post_type'] = false;
		}
		if ( isset( $instance['show_image'] ) ) {
			$instance['show_image'] = apply_filters( 'widget_content', $instance['show_image'] );
		} else {
			$instance['show_image'] = false;
		}
		// @codingStandardsIgnoreStart
		if ( $instance['cwp_tp_category'] == 'All' ) {
			$instance['cwp_tp_category'] = '';
		}
		if ( ! isset( $instance['cwp_tp_buynow'] ) ) {
			$instance['cwp_tp_buynow'] = __( 'Buy Now', 'wp-product-review' );
		}
		if ( ! isset( $instance['cwp_tp_readreview'] ) ) {
			$instance['cwp_tp_readreview'] = __( 'Read Review', 'wp-product-review' );
		}
		if ( ! isset( $instance['cwp_tp_layout'] ) ) {
			$instance['cwp_tp_layout'] = 'default.php';
		}
		if ( ! isset( $instance['cwp_tp_rating_type'] ) ) {
			$instance['cwp_tp_rating_type'] = 'round';
		}

		// @codingStandardsIgnoreEnd

		return $instance;
	}

	/**
	 * The admin widget form method.
	 *
	 * @since   3.0.0
	 * @access  public
	 *
	 * @param   array $instance The instance array for this widget.
	 *
	 * @return array
	 */
	public function form( $instance ) {

		if ( ! isset( $instance['no_items'] ) ) {
			$instance['no_items'] = __( '10', 'wp-product-review' );
		}

		if ( ! isset( $instance['cwp_tp_category'] ) ) {
			$instance['cwp_tp_category'] = 'Select Category';
		}

		if ( ! isset( $instance['title_type'] ) ) {
			$instance['title_type'] = false;
		}

		if ( ! isset( $instance['show_image'] ) ) {
			$instance['show_image'] = false;
		}

		if ( ! isset( $instance['cwp_tp_buynow'] ) ) {
			$instance['cwp_tp_buynow'] = __( 'Buy Now', 'wp-product-review' );
		}

		if ( ! isset( $instance['cwp_tp_readreview'] ) ) {
			$instance['cwp_tp_readreview'] = __( 'Read Review', 'wp-product-review' );
		}

		if ( ! isset( $instance['cwp_tp_layout'] ) ) {
			$instance['cwp_tp_layout'] = 'default.php';
		}

		if ( $instance['cwp_tp_layout'] == 'default.php' ) {
			$instance['cwp_tp_rating_type'] = 'round';
		} else {
			$instance['cwp_tp_rating_type'] = 'star';
		}

		$instance['cwp_tp_categ_array'] = get_categories( 'hide_empty=0' );
		foreach ( $instance['cwp_tp_categ_array'] as $categs ) {
			$instance['cwp_tp_all_categories'][ $categs->slug ] = $categs->name;
		}

		return $instance;
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

		$instance = $old_instance;

		$instance['title'] = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';

		$instance['no_items'] = ( ! empty( $new_instance['no_items'] ) ) ? strip_tags( $new_instance['no_items'] ) : '';

		$instance['cwp_tp_category'] = ( ! empty( $new_instance['cwp_tp_category'] ) ) ? strip_tags( $new_instance['cwp_tp_category'] ) : '';

		$instance['title_type'] = ( isset( $new_instance['title_type'] ) ) ? (bool) $new_instance['title_type'] : false;
		$instance['show_image'] = ( isset( $new_instance['show_image'] ) ) ? (bool) $new_instance['show_image'] : false;

		$instance['cwp_tp_buynow']      = ( ! empty( $new_instance['cwp_tp_buynow'] ) ) ? strip_tags( $new_instance['cwp_tp_buynow'] ) : '';
		$instance['cwp_tp_readreview']  = ( ! empty( $new_instance['cwp_tp_readreview'] ) ) ? strip_tags( $new_instance['cwp_tp_readreview'] ) : '';
		$instance['cwp_tp_layout']      = ( ! empty( $new_instance['cwp_tp_layout'] ) ) ? strip_tags( $new_instance['cwp_tp_layout'] ) : '';
		$instance['cwp_tp_rating_type'] = ( ! empty( $new_instance['cwp_tp_rating_type'] ) ) ? strip_tags( $new_instance['cwp_tp_rating_type'] ) : '';

		return $instance;
	}

	/**
	 * Method for loading admin widget assets.
	 *
	 * @since   3.0.0
	 * @access  public
	 */
	public function adminAssets() {
		if ( is_admin() ) {
			$dependencies   = $this->load_admin_assets();

			wp_enqueue_style( WPPR_SLUG . '-widget-admin-css', WPPR_URL . '/assets/css/cwppos-widget-admin.css', isset( $dependencies['css'] ) ? $dependencies['css'] : array(), WPPR_LITE_VERSION );

			wp_register_script( WPPR_SLUG . '-widget-script', WPPR_URL . '/assets/js/widget-admin.js', isset( $dependencies['js'] ) ? $dependencies['js'] : array(), WPPR_LITE_VERSION );
			wp_localize_script(
				WPPR_SLUG . '-widget-script', 'wppr_widget', array(
					'names' => array( 'cwp_top_products_widget', 'cwp_latest_products_widget' ),
				)
			);
			wp_enqueue_script( WPPR_SLUG . '-widget-script' );

		}
	}
}
