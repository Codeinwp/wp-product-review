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

		if ( $review->wppr_get_option( 'cwppos_lighbox' ) == 'no' ) {
			wp_enqueue_script( $this->plugin_name . '-lightbox-js', WPPR_URL . '/assets/js/lightbox.min.js', array( 'jquery' ), $this->version, true );
			wp_enqueue_style( $this->plugin_name . '-lightbox-css', WPPR_URL . '/assets/css/lightbox.css', array(), $this->version );
		}

		if ( $review->wppr_get_option( 'cwppos_show_userreview' ) == 'yes' ) {
			wp_enqueue_script( 'jquery-ui-slider' );
			wp_enqueue_script(
				$this->plugin_name . '-frontpage-js', WPPR_URL . '/assets/js/main.js', array(
					'jquery',
				), $this->version, true
			);
			if ( $review->wppr_get_option( 'cwppos_show_userreview' ) == 'yes' ) {
				wp_enqueue_style( $this->plugin_name . 'jqueryui', WPPR_URL . '/assets/css/jquery-ui.css', array(), $this->version );
			}
		}
		$icon = $review->wppr_get_option( 'cwppos_change_bar_icon' );

		if ( ! empty( $icon ) && $review->wppr_get_option( 'cwppos_fontawesome' ) == 'no' ) {
			wp_enqueue_style( $this->plugin_name . 'font-awesome', WPPR_URL . '/assets/css/font-awesome.min.css', array(), $this->version );
		}
		wp_enqueue_style( $this->plugin_name . '-frontpage-stylesheet', WPPR_URL . '/assets/css/frontpage.css', array(), $this->version );
		wp_enqueue_style(
			$this->plugin_name . '-percentage-circle', WPPR_URL . '/assets/css/circle.css', array(),
			$this->version
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
		wp_add_inline_style( $this->plugin_name . '-frontpage-stylesheet', $style );
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
		$return  = '';
		$return .= '<div class="user-comments-grades">';
		foreach ( $options as $k => $option ) {
			$intGrade = intval( $option['value'] * 10 );
			$return  .= '<div class="comment-meta-option">
                            <p class="comment-meta-option-name">' . $option['name'] . '</p>
                            <p class="comment-meta-option-grade">' . $option['value'] . '</p>
                            <div class="cwpr_clearfix"></div>
                            <div class="comment-meta-grade-bar ' . $this->review->get_rating_class( $intGrade ) . '">
                                <div class="comment-meta-grade" style="width: ' . $intGrade . '%"></div>
                            </div><!-- end .comment-meta-grade-bar -->
                        </div><!-- end .comment-meta-option -->
					';
		}
		$return .= '</div>';

		return $return . $text . '<div class="cwpr_clearfix"></div>';
	}

}
