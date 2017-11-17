<?php

/**
 * Implements templating behaviour in WPPR.
 *
 * @package     WPPR
 * @copyright   Copyright (c) 2017, Marius Cristea
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       3.0.10
 */
class WPPR_Template {
	/**
	 * Directories where to search in.
	 *
	 * @var array Directories path.
	 */
	private $dirs = array();

	/**
	 * WPPR_Template constructor.
	 */
	public function __construct() {
		$this->setup_locations();
	}

	/**
	 * Setup directories where wppr templates resides.
	 */
	private function setup_locations() {
		$this->dirs[]  = WPPR_PATH . '/includes/public/layouts/';
		$custom_paths  = apply_filters( 'wppr_templates_dir', array() );
		$theme_paths   = array();
		$theme_paths[] = get_template_directory() . '/wppr';
		$theme_paths[] = get_stylesheet_directory() . '/wppr';
		$this->dirs    = array_merge( $this->dirs, $custom_paths, $theme_paths );
		$this->dirs    = array_map( 'trailingslashit', $this->dirs );

	}

	/**
	 * Render the template file.
	 *
	 * @param string $template Template name.
	 * @param array  $args Args of variable to load.
	 * @param bool   $echo Either to echo or return content.
	 *
	 * @return string Return template content.
	 */
	public function render( $template, $args = array(), $echo = true ) {
		$location = $this->locate_template( $template );
		if ( empty( $location ) ) {
			return '';
		}

		if ( isset( $args['review_object'] ) ) {
			$this->load_assets( $template, $args['review_object'] );
		}

		foreach ( $args as $name => $value ) {
			if ( is_numeric( $name ) ) {
				continue;
			}
			$$name = $value;
		}
		/**
		 * Store the view output in cache based on the args it needs.
		 */
		$cache_key = md5( $location . serialize( $args ) );
		$content   = wp_cache_get( $cache_key, 'wppr' );
		if ( empty( $content ) ) {
			ob_start();
			require( $location );
			$content = ob_get_contents();
			ob_end_clean();

			wp_cache_set( $cache_key, $content, 'wppr', 5 * 60 );
		}
		if ( ! $echo ) {
			return $content;
		}
		echo $content;

		return '';
	}

	/**
	 * Loads the assets.
	 *
	 * @param string  $template  Name of the template.
	 * @param object  $review The review object.
	 */
	private function load_assets( $template, $review ) {
		if ( 0 === strpos( $template, 'widget/' ) ) {
			// widget template.
			wp_enqueue_style( WPPR_SLUG . '-widget', WPPR_URL . '/assets/css/cwppos-widget.css', array(), WPPR_LITE_VERSION );
			wp_enqueue_style( WPPR_SLUG . '-widget-rating', WPPR_URL . '/assets/css/cwppos-widget-rating.css', array( WPPR_SLUG . '-widget' ), WPPR_LITE_VERSION );
		}

		switch ( $template ) {
			case 'default':
				if ( $review->wppr_get_option( 'cwppos_lighbox' ) == 'no' ) {
					wp_enqueue_script( WPPR_SLUG . '-lightbox', WPPR_URL . '/assets/js/lightbox.min.js', array( 'jquery' ), WPPR_LITE_VERSION, true );
					wp_enqueue_style( WPPR_SLUG . '-lightbox', WPPR_URL . '/assets/css/lightbox.css', array(), WPPR_LITE_VERSION );
				}

				if ( $review->wppr_get_option( 'cwppos_show_userreview' ) == 'yes' ) {
					wp_enqueue_script( 'jquery-ui-slider' );
					wp_enqueue_script(
						WPPR_SLUG . '-frontpage', WPPR_URL . '/assets/js/main.js', array(
							'jquery',
						), WPPR_LITE_VERSION, true
					);
					if ( $review->wppr_get_option( 'cwppos_show_userreview' ) == 'yes' ) {
						wp_enqueue_style( WPPR_SLUG . 'jqueryui', WPPR_URL . '/assets/css/jquery-ui.css', array(), WPPR_LITE_VERSION );
					}
				}
				$icon = $review->wppr_get_option( 'cwppos_change_bar_icon' );

				if ( ! empty( $icon ) && $review->wppr_get_option( 'cwppos_fontawesome' ) == 'no' ) {
					wp_enqueue_style( WPPR_SLUG . 'font-awesome', WPPR_URL . '/assets/css/font-awesome.min.css', array(), WPPR_LITE_VERSION );
				}
				wp_enqueue_style( WPPR_SLUG . '-frontpage', WPPR_URL . '/assets/css/frontpage.css', array(), WPPR_LITE_VERSION );
				wp_enqueue_style(
					WPPR_SLUG . '-percentage-circle', WPPR_URL . '/assets/css/circle.css', array(),
					WPPR_LITE_VERSION
				);

				$conditional_styles = '';
				if ( $review->wppr_get_option( 'cwppos_show_icon' ) == 'yes' ) {
					$conditional_styles .= '
						div.affiliate-button a span {
							background: url("' . WPPR_URL . '/assets/img/cart-icon.png") no-repeat left center;
						} 
				
						div.affiliate-button a:hover span {
							background: url("' . WPPR_URL . '/assets/img/cart-icon-hover.png") no-repeat left center;
						}
						';
				}

				if ( $review->wppr_get_option( 'cwppos_show_userreview' ) == 'yes' ) {
					$conditional_styles .= '
						.commentlist .comment-body p {
							clear: left;
						}
						';
				}

				$style = '                   
							#review-statistics .review-wu-grade .c100,
							 .review-grade-widget .c100 {
								background-color: ' . $review->wppr_get_option( 'cwppos_rating_chart_default' ) . ';
							}
							
							#review-statistics .review-wu-grade .c100.wppr-weak span,
							 .review-grade-widget .c100.wppr-weak span {
								color: ' . $review->wppr_get_option( 'cwppos_rating_weak' ) . ';
							}
							
							#review-statistics .review-wu-grade .c100.wppr-weak .fill,
							#review-statistics .review-wu-grade .c100.wppr-weak .bar,
							 .review-grade-widget .c100.wppr-weak .fill,
							.review-grade-widget .c100.wppr-weak .bar {
								border-color: ' . $review->wppr_get_option( 'cwppos_rating_weak' ) . ';
							}
							
							.user-comments-grades .comment-meta-grade-bar.wppr-weak .comment-meta-grade {
								background: ' . $review->wppr_get_option( 'cwppos_rating_weak' ) . ';
							}
							
							#review-statistics .review-wu-grade .c100.wppr-not-bad span,
							 .review-grade-widget .c100.wppr-not-bad span {
								color: ' . $review->wppr_get_option( 'cwppos_rating_notbad' ) . ';
							}
							
							#review-statistics .review-wu-grade .c100.wppr-not-bad .fill,
							#review-statistics .review-wu-grade .c100.wppr-not-bad .bar,
							 .review-grade-widget .c100.wppr-not-bad .fill,
							.review-grade-widget .c100.wppr-not-bad .bar {
								border-color: ' . $review->wppr_get_option( 'cwppos_rating_notbad' ) . ';
							}
							
							.user-comments-grades .comment-meta-grade-bar.wppr-not-bad .comment-meta-grade {
								background: ' . $review->wppr_get_option( 'cwppos_rating_notbad' ) . ';
							}
							
							#review-statistics .review-wu-grade .c100.wppr-good span,
							 .review-grade-widget .c100.wppr-good span {
								color: ' . $review->wppr_get_option( 'cwppos_rating_good' ) . ';
							}
							
							#review-statistics .review-wu-grade .c100.wppr-good .fill,
							#review-statistics .review-wu-grade .c100.wppr-good .bar,
							 .review-grade-widget .c100.wppr-good .fill,
							.review-grade-widget .c100.wppr-good .bar {
								border-color: ' . $review->wppr_get_option( 'cwppos_rating_good' ) . ';
							}
							
							.user-comments-grades .comment-meta-grade-bar.wppr-good .comment-meta-grade {
								background: ' . $review->wppr_get_option( 'cwppos_rating_good' ) . ';
							}
							
							#review-statistics .review-wu-grade .c100.wppr-very-good span,
							 .review-grade-widget .c100.wppr-very-good span {
								color: ' . $review->wppr_get_option( 'cwppos_rating_very_good' ) . ';
							}
							
							#review-statistics .review-wu-grade .c100.wppr-very-good .fill,
							#review-statistics .review-wu-grade .c100.wppr-very-good .bar,
							 .review-grade-widget .c100.wppr-very-good .fill,
							.review-grade-widget .c100.wppr-very-good .bar {
								border-color: ' . $review->wppr_get_option( 'cwppos_rating_very_good' ) . ';
							}
							
							.user-comments-grades .comment-meta-grade-bar.wppr-very-good .comment-meta-grade {
								background: ' . $review->wppr_get_option( 'cwppos_rating_very_good' ) . ';
							}
							
							#review-statistics .review-wu-bars ul.wppr-weak li.colored {
								background: ' . $review->wppr_get_option( 'cwppos_rating_weak' ) . ';
								color: ' . $review->wppr_get_option( 'cwppos_rating_weak' ) . ';
							}
							
							#review-statistics .review-wu-bars ul.wppr-not-bad li.colored {
								background: ' . $review->wppr_get_option( 'cwppos_rating_notbad' ) . ';
								color: ' . $review->wppr_get_option( 'cwppos_rating_notbad' ) . ';
							}
							
							#review-statistics .review-wu-bars ul.wppr-good li.colored {
								background: ' . $review->wppr_get_option( 'cwppos_rating_good' ) . ';
								color: ' . $review->wppr_get_option( 'cwppos_rating_good' ) . ';
							}
							
							#review-statistics .review-wu-bars ul.wppr-very-good li.colored {
								background: ' . $review->wppr_get_option( 'cwppos_rating_very_good' ) . ';
								color: ' . $review->wppr_get_option( 'cwppos_rating_very_good' ) . ';
							}
							
							#review-statistics .review-wrap-up div.cwpr-review-top {
								border-top: ' . $review->wppr_get_option( 'cwppos_reviewboxbd_width' ) . 'px solid ' . $review->wppr_get_option( 'cwppos_reviewboxbd_color' ) . ';
							}
					
							.user-comments-grades .comment-meta-grade-bar,
							#review-statistics .review-wu-bars ul li {
								background: ' . $review->wppr_get_option( 'cwppos_rating_default' ) . ';
								color: ' . $review->wppr_get_option( 'cwppos_rating_default' ) . ';
							}
					
							#review-statistics .rev-option.customBarIcon ul li {
								color: ' . $review->wppr_get_option( 'cwppos_rating_default' ) . ';
							}
					
							#review-statistics .review-wrap-up .review-wu-right ul li, 
							#review-statistics .review-wu-bars h3, 
							.review-wu-bars span, 
							#review-statistics .review-wrap-up .cwpr-review-top .cwp-item-category a {
								color: ' . $review->wppr_get_option( 'cwppos_font_color' ) . ';
							}
					
							#review-statistics .review-wrap-up .review-wu-right .pros h2 {
								color: ' . $review->wppr_get_option( 'cwppos_pros_color' ) . ';
							}
					
							#review-statistics .review-wrap-up .review-wu-right .cons h2 {
								color: ' . $review->wppr_get_option( 'cwppos_cons_color' ) . ';
							}
						
							div.affiliate-button a {
								border: 2px solid ' . $review->wppr_get_option( 'cwppos_buttonbd_color' ) . ';
							}
					
							div.affiliate-button a:hover {
								border: 2px solid ' . $review->wppr_get_option( 'cwppos_buttonbh_color' ) . ';
							}
					
							div.affiliate-button a {
								background: ' . $review->wppr_get_option( 'cwppos_buttonbkd_color' ) . ';
							}
					
							div.affiliate-button a:hover {
								background: ' . $review->wppr_get_option( 'cwppos_buttonbkh_color' ) . ';
							}
					
							div.affiliate-button a span {
								color: ' . $review->wppr_get_option( 'cwppos_buttontxtd_color' ) . ';
							}
					
							div.affiliate-button a:hover span {
								color: ' . $review->wppr_get_option( 'cwppos_buttontxth_color' ) . ';
							}
							
							' . $conditional_styles . '
					  
				';
				$style = apply_filters( 'wppr_global_style', $style );
				wp_add_inline_style( WPPR_SLUG . '-frontpage', $style );
				break;

			case 'rich-json-ld':
				// empty.
				break;

			case 'comment-fields-tpl':
				wp_enqueue_style( WPPR_SLUG . '-comments', WPPR_URL . '/assets/css/comments.css', array(), WPPR_LITE_VERSION );
				break;

			case 'comment-ratings-tpl':
				wp_enqueue_style( WPPR_SLUG . '-comment-ratings', WPPR_URL . '/assets/css/comment-ratings.css', array(), WPPR_LITE_VERSION );
				break;

			case 'widget/style 1.php':
				wp_enqueue_style( WPPR_SLUG . '-widget-one', WPPR_URL . '/assets/css/cwppos-widget-style1.css', array( WPPR_SLUG . '-widget' ), WPPR_LITE_VERSION );
				break;

			case 'widget/default.php':
				// empty.
				break;

		}

	}


	/**
	 * Locate template file.
	 *
	 * @param string $template Filename to look for.
	 *
	 * @return string The template location.
	 */
	public function locate_template( $template ) {
		$dirs     = array_reverse( $this->dirs );
		$template = str_replace( '.php', '', $template );
		$template = $template . '.php';
		foreach ( $dirs as $dir ) {
			if ( file_exists( $dir . $template ) ) {
				return $dir . $template;
			}
		}

		return '';
	}
}
