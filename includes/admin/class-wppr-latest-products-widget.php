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

	public function assets() {
		wp_enqueue_style( 'cwp-pac-widget-stylesheet',  WPPR_URL . '/assets/css/cwppos-widget.css' );
		wp_enqueue_style( 'cwp-widget-stylesheet1',  WPPR_URL . '/assets/css/cwppos-widget-style1.css' );
		wp_enqueue_style( 'cwp-widget-rating',  WPPR_URL . '/assets/css/cwppos-widget-rating.css' );

		// wp_enqueue_script( 'cwp-pac-main-script', WPPR_URL . '/assets/js/main.js',array( 'jquery', 'pie-chart' ),WPPR_LITE_VERSION,true );
		// wp_enqueue_script( 'pie-chart', WPPR_URL . '/assets/js/pie-chart.js',array( 'jquery' ), WPPR_LITE_VERSION,true );
	}

	public function widget( $args, $instance ) {
	    $this->assets();

		if ( isset( $instance['title'] ) ) { $title = apply_filters( 'widget_title', $instance['title'] ); }
		if ( isset( $instance['no_items'] ) ) { $no_items = apply_filters( 'widget_content', $instance['no_items'] ); }
		if ( isset( $instance['cwp_tp_category'] ) ) { $cwp_tp_category = apply_filters( 'widget_content', $instance['cwp_tp_category'] ); }
		if ( isset( $instance['title_type'] ) ) { $post_type = apply_filters( 'widget_content', $instance['title_type'] ); }
		if ( isset( $instance['show_image'] ) ) { $show_image = apply_filters( 'widget_content', $instance['show_image'] ); }

		// before and after widget arguments are defined by themes
		// echo "<div id='cwp_latest_products_widget'>";
		echo $args['before_widget'];

		if ( ! empty( $title ) ) { echo $args['before_title'] . $title . $args['after_title']; }
		if ( $cwp_tp_category == 'All' ) { $cwp_tp_category = ''; }

		// Loop to get the most popular posts, ordered by the author's final grade.
		$query_args = array(
			'posts_per_page' => $no_items, // limit it to the specified no of posts
			'post_type'	=> 'any',
			'post__not_in' => get_option( 'sticky_posts' ),
			'category_name' => $cwp_tp_category, // limit it to the specified category
			'meta_key' => 'option_overall_score',
			'meta_query' => array(
				array(
					'key'       => 'cwp_meta_box_check',
					'value'     => 'Yes',
				),
			),
			'orderby'	=> 'date',
			'order'		=> 'DESC',
		);

		$cwp_products_loop = new WP_Query( $query_args );
		if ( ! isset( $instance['cwp_tp_buynow'] ) ) { $instance['cwp_tp_buynow'] = __( 'Buy Now', 'cwppos' ); }
		if ( ! isset( $instance['cwp_tp_readreview'] ) ) { $instance['cwp_tp_readreview'] = __( 'Read Review', 'cwppos' ); }
		if ( ! isset( $instance['cwp_tp_layout'] ) ) { $instance['cwp_tp_layout'] = 'default.php'; }
		if ( ! isset( $instance['cwp_tp_rating_type'] ) ) { $instance['cwp_tp_rating_type']   = 'round'; }

		include WPPR_PATH . '/includes/public/layouts/widget/' . $instance['cwp_tp_layout'];
		// Added by Ash/Upwork
		echo $args['after_widget'];
	}

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

	public function update( $new_instance, $old_instance ) {

		$instance = $old_instance;

		$instance['title'] = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';

		$instance['no_items'] = ( ! empty( $new_instance['no_items'] ) ) ? strip_tags( $new_instance['no_items'] ) : '';

		$instance['cwp_tp_category'] = ( ! empty( $new_instance['cwp_tp_category'] ) ) ? strip_tags( $new_instance['cwp_tp_category'] ) : '';

		$instance['title_type'] = (bool) $new_instance['title_type'] ;
		$instance['show_image'] = (bool) $new_instance['show_image'] ;

		// Added by Ash/Upwork
		$instance['cwp_tp_buynow'] = ( ! empty( $new_instance['cwp_tp_buynow'] ) ) ? strip_tags( $new_instance['cwp_tp_buynow'] ) : '';
		$instance['cwp_tp_readreview'] = ( ! empty( $new_instance['cwp_tp_readreview'] ) ) ? strip_tags( $new_instance['cwp_tp_readreview'] ) : '';
		$instance['cwp_tp_layout'] = ( ! empty( $new_instance['cwp_tp_layout'] ) ) ? strip_tags( $new_instance['cwp_tp_layout'] ) : '';
		$instance['cwp_tp_rating_type'] = ( ! empty( $new_instance['cwp_tp_rating_type'] ) ) ? strip_tags( $new_instance['cwp_tp_rating_type'] ) : '';
		// Added by Ash/Upwork
		return $instance;

	}
}
