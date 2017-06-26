<?php

class WPPR_Latest_Products_Widget extends WP_Widget {
	const RESTRICT_TITLE_CHARS  = 100;

	public function __construct() {
		parent::__construct(
			'wppr_latest_products_widget',
			__( 'CWP Latest Products Widget', 'cwppos' ),
			// Widget description
			array(
				'description' => __( 'This widget displays the latest products based on their rating.', 'cwppos' ),
			)
		);
	}

	public function register() {
		register_widget( 'WPPR_Latest_Products_Widget' );
	}

	public function adminAssets() {
		if ( is_admin() ) {
			wp_enqueue_style( 'cwp-widget-admin-css',  WPPR_URL . '/assets/css/cwppos-widget-admin.css' );

			// wp_register_script( 'cwp-widget-script-latest', WPPR_URL . '/assets/js/widget-latest.js' );
			// wp_localize_script('cwp-widget-script-latest', 'cwpw_latest', array(
			// 'widgetName'    => $this->id_base,
			// 'imageCheckbox' => $this->get_field_id( 'show_image' ),
			// 'ratingSelect'  => $this->get_field_id( 'cwp_tp_rating_type' ),
			// ));
			// wp_enqueue_script( 'cwp-widget-script-latest' );
		}
	}

	public function widget( $args, $instance ) {}

	public function form( $instance ) {

		$this->adminAssets();

		if ( isset( $instance['title'] ) ) {
			$title = $instance['title'];
		} else {
			$title = __( 'Latest Products', 'cwppos' );
		}

		if ( isset( $instance['no_items'] ) ) {
			$no_items = $instance['no_items'];
		} else {
			$no_items = __( '10', 'cwppos' );
		}

		if ( isset( $instance['cwp_tp_category'] ) ) {
			$cwp_tp_category = $instance['cwp_tp_category'];} else {
			$cwp_tp_category = 'Select Category';
			}

			if ( isset( $instance['title_type'] ) ) {
				$title_type = $instance['title_type'];

			} else {
				$title_type = false;
			}

			if ( isset( $instance['show_image'] ) ) {
				$show_image = $instance['show_image'];
			} else {
				$show_image = false;
			}

			$cwp_tp_buynow = __( 'Buy Now', 'cwppos' );
			if ( isset( $instance['cwp_tp_buynow'] ) ) {
				$cwp_tp_buynow = $instance['cwp_tp_buynow'];
			}

			$cwp_tp_readreview = __( 'Read Review', 'cwppos' );
			if ( isset( $instance['cwp_tp_readreview'] ) ) {
				$cwp_tp_readreview = $instance['cwp_tp_readreview'];
			}

			$cwp_tp_layout = 'default.php';
			if ( isset( $instance['cwp_tp_layout'] ) ) {
				$cwp_tp_layout  = $instance['cwp_tp_layout'];
			}

			if ( $cwp_tp_layout == 'default.php' ) {
				$cwp_tp_rating_type = 'round';
			} else {
				$cwp_tp_rating_type = 'star';
			}

			$cwp_tp_categ_array = get_categories( 'hide_empty=0' );
			foreach ( $cwp_tp_categ_array as $categs ) {
				$cwp_tp_all_categories[ $categs->slug ] = $categs->name;
			}

			include( WPPR_PATH . '/includes/admin/layouts/widget-latest-products-tpl.php' );
	}
}
