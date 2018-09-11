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
	 * Method to load assets required for front end display.
	 *
	 * @since   3.0.0
	 * @access  public
	 */
	public function assets( $review_object ) {

		$dependencies = $this->load_assets();

		wp_enqueue_style( WPPR_SLUG . '-pac-widget-stylesheet', WPPR_URL . '/assets/css/cwppos-widget.css', isset( $dependencies['css'] ) ? $dependencies['css'] : array(), WPPR_LITE_VERSION );
		wp_enqueue_style( WPPR_SLUG . '-widget-stylesheet-one', WPPR_URL . '/assets/css/cwppos-widget-style1.css', array( WPPR_SLUG . '-pac-widget-stylesheet' ), WPPR_LITE_VERSION );
		wp_enqueue_style( WPPR_SLUG . '-widget-rating', WPPR_URL . '/assets/css/cwppos-widget-rating.css', array( WPPR_SLUG . '-pac-widget-stylesheet' ), WPPR_LITE_VERSION );

		$plugin = new WPPR();
		$public = new Wppr_Public( $plugin->get_plugin_name(), $plugin->get_version() );

		$public->load_review_assets( $review_object );
	}

	/**
	 * Load public assets specific to this widget.
	 *
	 * @since   3.0.0
	 * @access  public
	 */
	public abstract function load_assets();

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
		$instance['title']    = apply_filters( 'widget_title', $instance['title'] );
		$instance['no_items'] = apply_filters( 'widget_content', $instance['no_items'] );

		if ( ! isset( $instance['cwp_tp_post_types'] ) || empty( $instance['cwp_tp_post_types'] ) ) {
			$instance['cwp_tp_post_types'] = array( 'post', 'page' );
		}

		$instance['cwp_tp_post_types'] = apply_filters( 'widget_content', $instance['cwp_tp_post_types'] );
		$instance['cwp_tp_category']   = apply_filters( 'widget_content', $instance['cwp_tp_category'] );
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

		add_filter( 'wppr_review_image_size', array( $this, 'image_size' ), 10, 3 );

		return $instance;
	}

	/**
	 * Change size to wppr-widget.
	 *
	 * @access  public
	 *
	 * @param   string            $size The size of the image.
	 * @param   int               $id The id of the review.
	 * @param   WPPR_Review_Model $model The review model.
	 *
	 * @return string
	 */
	public function image_size( $size, $id, $model ) {
		return 'wppr-widget';
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
			$instance['cwp_tp_category'] = __( 'Select Category', 'wp-product-review' );
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

		if ( ! isset( $instance['cwp_tp_post_types'] ) || empty( $instance['cwp_tp_post_types'] ) ) {
			// backward compatibility with previous versions where you could not select post types
			$instance['cwp_tp_post_types'] = array( 'post', 'page' );
		}

		if ( isset( $instance['cwp_tp_post_types'] ) && ! empty( $instance['cwp_tp_post_types'] ) ) {
			$categories = array();
			foreach ( $instance['cwp_tp_post_types'] as $type ) {
				$post_type = get_post_type_object( $type );
				$cats      = WPPR_Admin::get_category_for_post_type( $type );
				if ( $cats ) {
					$categories[ $post_type->label ] = $cats;
				}
			}
			$instance['cwp_tp_all_categories'] = $categories;
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

		$instance['cwp_tp_post_types'] = ( ! empty( $new_instance['cwp_tp_post_types'] ) ) ? esc_sql( $new_instance['cwp_tp_post_types'] ) : '';
		$instance['cwp_tp_category']   = ( ! empty( $new_instance['cwp_tp_category'] ) ) ? strip_tags( $new_instance['cwp_tp_category'] ) : '';

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

			$dependencies = $this->load_admin_assets();

			wp_enqueue_style( WPPR_SLUG . '-widget-admin-css', WPPR_URL . '/assets/css/cwppos-widget-admin.css', isset( $dependencies['css'] ) ? $dependencies['css'] : array(), WPPR_LITE_VERSION );
			wp_enqueue_style( WPPR_SLUG . '-chosen', WPPR_URL . '/assets/css/chosen.min.css', array(), WPPR_LITE_VERSION );

			wp_enqueue_script( WPPR_SLUG . '-chosen', WPPR_URL . '/assets/js/chosen.jquery.min.js', array( 'jquery' ), WPPR_LITE_VERSION );
			wp_register_script( WPPR_SLUG . '-widget-script', WPPR_URL . '/assets/js/widget-admin.js', array_merge( array( WPPR_SLUG . '-chosen' ), isset( $dependencies['js'] ) ? $dependencies['js'] : array() ), WPPR_LITE_VERSION );

			wp_localize_script(
				WPPR_SLUG . '-widget-script',
				'wppr_widget',
				array(
					'names' => array( 'cwp_top_products_widget', 'cwp_latest_products_widget' ),
					'ajax'  => array(
						'nonce' => wp_create_nonce( WPPR_SLUG ),
					),
				)
			);
			wp_enqueue_script( WPPR_SLUG . '-widget-script' );

		}
	}

	/**
	 * Load admin assets specific to this widget.
	 *
	 * @since   3.0.0
	 * @access  public
	 */
	public abstract function load_admin_assets();
}
