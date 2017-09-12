<?php
/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://themeisle.com/
 * @since      3.0.0
 *
 * @package    Wppr
 * @subpackage Wppr/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Wppr
 * @subpackage Wppr/public
 * @author     ThemeIsle <friends@themeisle.com>
 */
class Wppr_Public {

	/**
	 * The ID of this plugin.
	 *
	 * @since    3.0.0
	 * @access   private
	 * @var      string $plugin_name The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    3.0.0
	 * @access   private
	 * @var      string $version The current version of this plugin.
	 */
	private $version;

	/**
	 * Stores a WPPR_Review_Model object.
	 *
	 * @since   3.0.0
	 * @access  private
	 * @var WPPR_Review_Model $review The review model.
	 */
	private $review;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since   3.0.0
	 * @access  public
	 *
	 * @param   string $plugin_name The name of the plugin.
	 * @param   string $version The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {
		$this->plugin_name = $plugin_name;
		$this->version     = $version;
	}

	/**
	 * Setup review of the current post.
	 */
	public function setup_post() {

		global $post;
		$this->review = new WPPR_Review_Model( ! empty( $post ) ? $post->ID : 0 );
	}

	/**
	 *
	 * Load the review assets based on the context.
	 *
	 * @param WPPR_Review_Model $review Review model.
	 */
	public function load_review_assets( $review = null ) {
		$load = false;
		if ( ! empty( $review ) ) {
			if ( $review->is_active() ) {
				$load = true;
			}
		} else {
			$review = $this->review;
			if ( empty( $review ) ) {
				$load = false;
			} elseif ( $review->is_active() ) {
				$load = true;
			}
		}

		if ( ! $load ) {
			return;
		}

		wp_enqueue_script( $this->plugin_name . '-pie-chart-js', WPPR_URL . '/assets/js/pie-chart.js', array( 'jquery' ), $this->version, true );
		wp_enqueue_script(
			$this->plugin_name . '-frontpage-js', WPPR_URL . '/assets/js/main.js', array(
				'jquery',
				$this->plugin_name . '-pie-chart-js',
			), $this->version, true
		);
		if ( $review->wppr_get_option( 'cwppos_lighbox' ) == 'no' ) {
			wp_enqueue_script( $this->plugin_name . '-lightbox-js', WPPR_URL . '/assets/js/lightbox.min.js', array( 'jquery' ), $this->version, true );
		}

		if ( $review->wppr_get_option( 'cwppos_show_userreview' ) == 'yes' ) {
			wp_enqueue_script( 'jquery-ui-slider' );
		}

		wp_enqueue_style( $this->plugin_name . '-frontpage-stylesheet', WPPR_URL . '/assets/css/frontpage.css', array(), $this->version );
		if ( $review->wppr_get_option( 'cwppos_lighbox' ) == 'no' ) {
			wp_enqueue_style( $this->plugin_name . '-lightbox-css', WPPR_URL . '/assets/css/lightbox.css', array(), $this->version );
		}
		if ( $review->wppr_get_option( 'cwppos_show_userreview' ) == 'yes' ) {

			wp_enqueue_style( $this->plugin_name . 'jqueryui', WPPR_URL . '/assets/css/jquery-ui.css', array(), $this->version );
		}

		global $content_width;
		if ( $review->wppr_get_option( 'cwppos_widget_size' ) != '' ) {
			$width = $review->wppr_get_option( 'cwppos_widget_size' );
		} else {
			$width = $content_width;
		}
		if ( $width < 200 ) {
			$width = 600;
		}
		$img_size    = min( 180, $width * 0.51 * 0.4 );
		$height_left = $img_size + 10;

		$conditional_media_styles = '';
		if ( $review->wppr_get_option( 'cwppos_widget_size' ) != '' ) {
			$conditional_media_styles = '
                #review-statistics {
                    width: ' . $review->wppr_get_option( 'cwppos_widget_size' ) . 'px;
                }
                ';
		}

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
                 
                    @media (min-width: 820px) {
                        #review-statistics .review-wrap-up .review-wu-left .rev-wu-image, #review-statistics .review-wrap-up .review-wu-left .review-wu-grade {
                            height: ' . $height_left . 'px;
                        }
            
                        #review-statistics .review-wrap-up .review-wu-left .review-wu-grade .cwp-review-chart .cwp-review-percentage {
                
                            margin-top: ' . ( $img_size * 0.1 ) . '%;
                        }
            
                        #review-statistics .review-wrap-up .review-wu-left .review-wu-grade .cwp-review-chart span {
                            font-size: ' . round( 30 * $img_size / 140 ) . 'px;
                        }
                        
                        ' . $conditional_media_styles . '
			        }
                
                    #review-statistics .review-wrap-up div.cwpr-review-top {
                        border-top: ' . $review->wppr_get_option( 'cwppos_reviewboxbd_width' ) . 'px solid ' . $review->wppr_get_option( 'cwppos_reviewboxbd_color' ) . ';
                    }
            
                    .user-comments-grades .comment-meta-grade-bar,
                    #review-statistics .review-wu-bars ul li {
                        background: ' . $review->wppr_get_option( 'cwppos_rating_default' ) . ';
                    }
            
                    #review-statistics .rev-option.customBarIcon ul li {
                        color: ' . $review->wppr_get_option( 'cwppos_rating_default' ) . ';
                    }
            
                    #review-statistics .review-wrap-up .review-wu-right ul li, #review-statistics .review-wu-bars h3, .review-wu-bars span, #review-statistics .review-wrap-up .cwpr-review-top .cwp-item-category a {
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

		$script = ' 
                    var c1 = "' . $review->wppr_get_option( 'cwppos_rating_weak' ) . '";
                    var c2 = "' . $review->wppr_get_option( 'cwppos_rating_notbad' ) . '";
                    var c3 = "' . $review->wppr_get_option( 'cwppos_rating_good' ) . '";
                    var c4 = "' . $review->wppr_get_option( 'cwppos_rating_very_good' ) . '"; 
                    
            ';

		if ( class_exists( 'WPPR_Pro' ) ) {
			$isSetToPro = true;
		} else {
			$isSetToPro = false;
		}

		if ( $isSetToPro ) {
			$uni_font = $review->wppr_get_option( 'cwppos_change_bar_icon' );
		} else {
			$uni_font = '';
		}
		$track = $review->wppr_get_option( 'cwppos_rating_chart_default' );
		if ( is_array( $uni_font ) ) {
			$uni_font = $uni_font[0];
		} elseif ( substr( $uni_font, 0, 1 ) == '#' ) {
			$uni_font = $uni_font;
		} else {
			$uni_font = '';
		}

		if ( ! empty( $uni_font ) ) {
			if ( $isSetToPro ) {
				if ( $review->wppr_get_option( 'cwppos_fontawesome' ) === 'no' ) {
					wp_enqueue_style( 'cwp-pac-fontawesome-stylesheet', WPPR_URL . '/assets/css/font-awesome.min.css' );
				}
			}
		}
		$script .= "
                    var cwpCustomBarIcon = '" . $uni_font . "';
                    var isSetToPro = '" . $isSetToPro . "';
                    var trackcolor = '" . $track . "';
                ";
		wp_add_inline_style( $this->plugin_name . '-frontpage-stylesheet', $style );
		wp_add_inline_script( $this->plugin_name . '-frontpage-js', $script );
	}

	/**
	 * Temporary methods
	 *
	 * @since   3.0.0
	 * @access  public
	 *
	 * @param   mixed $content The page content.
	 *
	 * @return mixed
	 */
	public function display_on_front( $content ) {
		if ( empty( $this->review ) ) {
			return $content;
		}
		if ( $this->review->is_active() && is_singular() ) {
			$output        = '';
			$review_object = $this->review;
			$template      = new WPPR_Template();

			$output .= $template->render(
				'default', array(
					'review_object' => $review_object,
				), false
			);

			$output .= $template->render(
				'rich-json-ld', array(
					'review_object' => $review_object,
				), false
			);

			$review_position_before_content = $this->review->wppr_get_option( 'cwppos_show_reviewbox' );
			if ( $review_position_before_content == 'yes' ) {
				$content = $content . $output;
			} elseif ( $review_position_before_content == 'no' ) {
				$content = $output . $content;
			}
		}

		return $content;
	}


	/**
	 * Adds the comment form fields.
	 *
	 * @return string The comment form fields.
	 */
	function add_comment_fields() {
		if ( ! $this->review->is_active() ) {
			return '';
		}
		if ( $this->review->wppr_get_option( 'cwppos_show_userreview' ) != 'yes' ) {
			return '';
		}
		$options      = $this->review->get_options();
		$option_names = wp_list_pluck( $options, 'name' );
		$sliders      = array();
		foreach ( $option_names as $k => $value ) {
			$sliders[] =
				'<div class="wppr-comment-form-meta">
            <label for="wppr-slider-option-' . $k . '">' . $value . '</label>
            <input type="text" id="wppr-slider-option-' . $k . '" class="meta_option_input" value="" name="wppr-slider-option-' . $k . '" readonly="readonly">
            <div class="wppr-comment-meta-slider"></div>
            <div class="cwpr_clearfix"></div>
		</div>';
		}
		echo '<div id="wppr-slider-comment">' . implode( '', $sliders ) . '<div class="cwpr_clearfix"></div></div>';

	}

	/**
	 * Update the comment meta fields with the rating.
	 *
	 * @param int $comment_id The comment id.
	 */
	public function save_comment_fields( $comment_id ) {
		$comment = get_comment( $comment_id );
		if ( empty( $comment ) ) {
			return;
		}
		$review = new WPPR_Review_Model( $comment->comment_post_ID );
		if ( empty( $review ) ) {
			return;
		}
		if ( ! $review->is_active() ) {
			return;
		}
		if ( $review->wppr_get_option( 'cwppos_show_userreview' ) != 'yes' ) {
			return;
		}

		$options      = $review->get_options();
		$option_names = wp_list_pluck( $options, 'name' );
		$valid_review = false;
		foreach ( $option_names as $k => $value ) {
			if ( isset( $_POST[ 'wppr-slider-option-' . $k ] ) && ! empty( $_POST[ 'wppr-slider-option-' . $k ] ) ) {
				$valid_review = true;
				break;
			}
		}
		if ( ! $valid_review ) {
			return;
		}
		foreach ( $option_names as $k => $value ) {
			if ( isset( $_POST[ 'wppr-slider-option-' . $k ] ) ) {

				$option_value = wp_filter_nohtml_kses( $_POST[ 'wppr-slider-option-' . $k ] );
				$option_value = empty( $value ) ? 0 : $option_value;
				update_comment_meta( $comment_id, 'meta_option_' . $k, $option_value );

			}
		}
	}

	/**
	 * Alter the comment text and add the review ratings.
	 *
	 * @param string $text Comment text.
	 *
	 * @return string Comment text with review.
	 */
	public function show_comment_ratings( $text ) {

		if ( empty( $this->review ) ) {
			return $text;
		}
		if ( ! $this->review->is_active() ) {
			return $text;
		}
		if ( $this->review->wppr_get_option( 'cwppos_show_userreview' ) != 'yes' ) {
			return $text;
		}

		global $comment;

		$options = $this->review->get_comment_options( $comment->comment_ID );
		if ( empty( $options ) ) {
			return $text;
		}
		$return = '';
		$return .= '<div class="user-comments-grades">';
		foreach ( $options as $k => $option ) {
			$return .= '<div class="comment-meta-option">
                            <p class="comment-meta-option-name">' . $option['name'] . '</p>
                            <p class="comment-meta-option-grade">' . $option['value'] . '</p>
                            <div class="cwpr_clearfix"></div>
                            <div class="comment-meta-grade-bar">
                                <div class="comment-meta-grade" style="width: ' . intval( $option['value'] ) * 10 . '%"></div>
                            </div><!-- end .comment-meta-grade-bar -->
                        </div><!-- end .comment-meta-option -->
					';
		}
		$return .= '</div>';

		return $return . $text . '<div class="cwpr_clearfix"></div>';
	}

}
