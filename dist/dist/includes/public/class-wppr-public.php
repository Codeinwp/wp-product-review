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
	 * @param   string $version     The version of this plugin.
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

				$this->review = $review;
				$this->amp_support();
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
			wp_enqueue_script( 'jquery-touch-punch' );
			wp_enqueue_script(
				$this->plugin_name . '-frontpage-js',
				WPPR_URL . '/assets/js/main.js',
				array(
					'jquery-ui-slider',
				),
				$this->version,
				true
			);
			wp_enqueue_style( $this->plugin_name . 'jqueryui', WPPR_URL . '/assets/css/jquery-ui.css', array(), $this->version );
			wp_enqueue_style( $this->plugin_name . 'comments', WPPR_URL . '/assets/css/comments.css', array(), $this->version );
		}
		$icon = $review->wppr_get_option( 'cwppos_change_bar_icon' );

		if ( 'default' !== $review->get_template() || ( ! empty( $icon ) && $review->wppr_get_option( 'cwppos_fontawesome' ) == 'no' ) ) {
			wp_enqueue_style( $this->plugin_name . 'font-awesome', WPPR_URL . '/assets/css/font-awesome.min.css', array(), $this->version );
		}

		if ( $review->wppr_get_option( 'cwppos_show_icon' ) == 'yes' ) {
			wp_enqueue_style( 'dashicons' );
		}

		wp_enqueue_style( $this->plugin_name . '-' . $review->get_template() . '-stylesheet', WPPR_URL . '/assets/css/' . $review->get_template() . '.css', array(), $this->version );
		wp_enqueue_style(
			$this->plugin_name . '-percentage-circle',
			WPPR_URL . '/assets/css/circle.css',
			array(),
			$this->version
		);
		wp_enqueue_style(
			$this->plugin_name . '-common',
			WPPR_URL . '/assets/css/common.css',
			array(),
			$this->version
		);
		$style = $this->generate_styles();

		$style = apply_filters( 'wppr_global_style', $style );

		wp_add_inline_style( $this->plugin_name . '-common', $style );
	}

	/**
	 * Load AMP logic.
	 */
	public function amp_support() {
		if ( ! $this->review->is_active() ) {
			return;
		}
		if ( ! function_exists( 'ampforwp_is_amp_endpoint' ) || ! function_exists( 'is_amp_endpoint' ) ) {
			return;
		}
		if ( ! ampforwp_is_amp_endpoint() || ! is_amp_endpoint() ) {
			return;
		}

		/**
		 * Remove any custom icon.
		 */
		add_filter( 'wppr_option_custom_icon', '__return_empty_string', 99 );
		add_action( 'amp_post_template_head', array( $this, 'wppr_amp_add_fa' ), 999 );

		$model = new WPPR_Query_Model();
		if ( 'yes' === $model->wppr_get_option( 'wppr_amp' ) ) {
			add_filter( 'wppr_review_option_rating_css', array( $this, 'amp_width_support' ), 99, 2 );
			add_action( 'amp_post_template_css', array( $this, 'amp_styles' ), 999 );
		}
	}

	/**
	 * Function to generate styles on the basis of Ratings.
	 */
	public function generate_styles() {

		$review             = new WPPR_Review_Model();
		$conditional_styles = '';
		if ( $review->wppr_get_option( 'cwppos_show_icon' ) == 'yes' ) {
			$adverb         = is_rtl() ? 'after' : 'before';
			$direction      = is_rtl() ? 'left' : 'right';
			$conditional_styles .= '
                div.affiliate-button a span:' . $adverb . ', div.affiliate-button a:hover span:' . $adverb . ' {
					font-family: "dashicons";
                    content: "\f174";
					padding-' . $direction . ': 5px
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
                    .review-wu-grade .wppr-c100,
                     .review-grade-widget .wppr-c100 {
                        background-color: ' . $review->wppr_get_option( 'cwppos_rating_chart_default' ) . ';
                    }
                    
                    .review-wu-grade .wppr-c100.wppr-weak span,
                     .review-grade-widget .wppr-c100.wppr-weak span {
                        color: ' . $review->wppr_get_option( 'cwppos_rating_weak' ) . ';
                    }
                    
                    .review-wu-grade .wppr-c100.wppr-weak .wppr-fill,
                    .review-wu-grade .wppr-c100.wppr-weak .wppr-bar,
                     .review-grade-widget .wppr-c100.wppr-weak .wppr-fill,
                    .review-grade-widget .wppr-c100.wppr-weak .wppr-bar {
                        border-color: ' . $review->wppr_get_option( 'cwppos_rating_weak' ) . ';
                    }
                    
                    .user-comments-grades .comment-meta-grade-bar.wppr-weak .comment-meta-grade {
                        background: ' . $review->wppr_get_option( 'cwppos_rating_weak' ) . ';
                    }
                    
                    #review-statistics .review-wu-grade .wppr-c100.wppr-not-bad span,
                     .review-grade-widget .wppr-c100.wppr-not-bad span {
                        color: ' . $review->wppr_get_option( 'cwppos_rating_notbad' ) . ';
                    }
                    
                    .review-wu-grade .wppr-c100.wppr-not-bad .wppr-fill,
                    .review-wu-grade .wppr-c100.wppr-not-bad .wppr-bar,
                     .review-grade-widget .wppr-c100.wppr-not-bad .wppr-fill,
                    .review-grade-widget .wppr-c100.wppr-not-bad .wppr-bar {
                        border-color: ' . $review->wppr_get_option( 'cwppos_rating_notbad' ) . ';
                    }
                    
                    .user-comments-grades .comment-meta-grade-bar.wppr-not-bad .comment-meta-grade {
                        background: ' . $review->wppr_get_option( 'cwppos_rating_notbad' ) . ';
                    }
                    
                    .review-wu-grade .wppr-c100.wppr-good span,
                     .review-grade-widget .wppr-c100.wppr-good span {
                        color: ' . $review->wppr_get_option( 'cwppos_rating_good' ) . ';
                    }
                    
                    .review-wu-grade .wppr-c100.wppr-good .wppr-fill,
                    .review-wu-grade .wppr-c100.wppr-good .wppr-bar,
                     .review-grade-widget .wppr-c100.wppr-good .wppr-fill,
                    .review-grade-widget .wppr-c100.wppr-good .wppr-bar {
                        border-color: ' . $review->wppr_get_option( 'cwppos_rating_good' ) . ';
                    }
                    
                    .user-comments-grades .comment-meta-grade-bar.wppr-good .comment-meta-grade {
                        background: ' . $review->wppr_get_option( 'cwppos_rating_good' ) . ';
                    }
                    
                    .review-wu-grade .wppr-c100.wppr-very-good span,
                     .review-grade-widget .wppr-c100.wppr-very-good span {
                        color: ' . $review->wppr_get_option( 'cwppos_rating_very_good' ) . ';
                    }
                    
                    .review-wu-grade .wppr-c100.wppr-very-good .wppr-fill,
                    .review-wu-grade .wppr-c100.wppr-very-good .wppr-bar,
                     .review-grade-widget .wppr-c100.wppr-very-good .wppr-fill,
                    .review-grade-widget .wppr-c100.wppr-very-good .wppr-bar {
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

		/**
		 * Template specific styles.
		 */
		$style .= ' 
			.wppr-template-1 .wppr-review-grade-option-rating.wppr-very-good.rtl,
			.wppr-template-2 .wppr-review-grade-option-rating.wppr-very-good.rtl {
					background: ' . $review->wppr_get_option( 'cwppos_rating_very_good' ) . ';
			}
			.wppr-template-1 .wppr-review-grade-option-rating.wppr-good.rtl,
			.wppr-template-2 .wppr-review-grade-option-rating.wppr-good.rtl {
					background: ' . $review->wppr_get_option( 'cwppos_rating_good' ) . ';
			}
			.wppr-template-1 .wppr-review-grade-option-rating.wppr-not-bad.rtl,
			.wppr-template-2 .wppr-review-grade-option-rating.wppr-not-bad.rtl {
					background: ' . $review->wppr_get_option( 'cwppos_rating_notbad' ) . ';
			}
			.wppr-template-1 .wppr-review-grade-option-rating.wppr-weak.rtl,
			.wppr-template-2 .wppr-review-grade-option-rating.wppr-weak.rtl {
					background: ' . $review->wppr_get_option( 'cwppos_rating_weak' ) . ';
			}

			.wppr-template-1    .wppr-review-grade-option .wppr-very-good {
					background: ' . ( is_rtl() ? $review->wppr_get_option( 'cwppos_rating_default' ) : $review->wppr_get_option( 'cwppos_rating_very_good' ) ) . ';
			}
			.wppr-template-2    .wppr-review-rating .wppr-very-good {
					background: ' . $review->wppr_get_option( 'cwppos_rating_very_good' ) . ';
			} 
			.wppr-template-1    .wppr-review-grade-option .wppr-good {
					background: ' . ( is_rtl() ? $review->wppr_get_option( 'cwppos_rating_default' ) : $review->wppr_get_option( 'cwppos_rating_good' ) ) . ';
			}
			.wppr-template-2     .wppr-review-rating  .wppr-good {
					background: ' . $review->wppr_get_option( 'cwppos_rating_good' ) . ';
			} 
			.wppr-template-1    .wppr-review-grade-option .wppr-not-bad {
					background: ' . ( is_rtl() ? $review->wppr_get_option( 'cwppos_rating_default' ) : $review->wppr_get_option( 'cwppos_rating_notbad' ) ) . ';
			}
			.wppr-template-2    .wppr-review-rating .wppr-not-bad {
					background: ' . $review->wppr_get_option( 'cwppos_rating_notbad' ) . ';
			}
			 
			.wppr-template-1    .wppr-review-grade-option .wppr-weak {
					background: ' . ( is_rtl() ? $review->wppr_get_option( 'cwppos_rating_default' ) : $review->wppr_get_option( 'cwppos_rating_weak' ) ) . ';
			}
			.wppr-template-2    .wppr-review-rating  .wppr-weak {
					background: ' . $review->wppr_get_option( 'cwppos_rating_weak' ) . ';
			}  
			.wppr-template-1    .wppr-review-grade-option .wppr-default,
			.wppr-template-2   .wppr-review-rating  .wppr-default{
					background: ' . $review->wppr_get_option( 'cwppos_rating_default' ) . ';
			} 
			
			
			
			.wppr-template-1    .wppr-review-grade-number .wppr-very-good,
			.wppr-template-1    .wppr-review-stars .wppr-very-good,
			.wppr-template-2    .wppr-review-option-rating .wppr-very-good{
					color: ' . $review->wppr_get_option( 'cwppos_rating_very_good' ) . ';
			}
			.wppr-template-1    .wppr-review-grade-number .wppr-good,
			.wppr-template-1    .wppr-review-stars .wppr-good,
			.wppr-template-2    .wppr-review-option-rating  .wppr-good{
					color: ' . $review->wppr_get_option( 'cwppos_rating_good' ) . ';
			}
			
			.wppr-template-1    .wppr-review-grade-number .wppr-not-bad,
			.wppr-template-1    .wppr-review-stars .wppr-not-bad,
			.wppr-template-2  .wppr-review-option-rating .wppr-not-bad{
					color: ' . $review->wppr_get_option( 'cwppos_rating_notbad' ) . ';
					color: ' . $review->wppr_get_option( 'cwppos_rating_notbad' ) . ';
			}
			.wppr-template-1    .wppr-review-grade-number .wppr-weak,
			.wppr-template-1    .wppr-review-stars .wppr-weak,
			.wppr-template-2  .wppr-review-option-rating  .wppr-weak{
					color: ' . $review->wppr_get_option( 'cwppos_rating_weak' ) . ';
			} 
			.wppr-template-1    .wppr-review-grade-number .wppr-default,
			.wppr-template-1    .wppr-review-stars .wppr-default,
			.wppr-review-option-rating  .wppr-default{
					color: ' . $review->wppr_get_option( 'cwppos_rating_default' ) . ';
			} 
			
			
			.wppr-template .wppr-review-name{
					color: ' . $review->wppr_get_option( 'cwppos_font_color' ) . ';
			} 
			.wppr-template h3.wppr-review-cons-name{
					color: ' . $review->wppr_get_option( 'cwppos_cons_color' ) . ';
			} 
			.wppr-template h3.wppr-review-pros-name{
					color: ' . $review->wppr_get_option( 'cwppos_pros_color' ) . ';
			} 
		';

		return $style;
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
			$output        .= $template->render(
				$review_object->get_template(),
				array(
					'review_object' => $review_object,
				),
				false
			);

			$output .= $template->render(
				'rich-json-ld',
				array(
					'review_object' => $review_object,
				),
				false
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
		$review->update_comments_rating();
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
			$intGrade = intval( $option['value'] * 10 );
			$return   .= '<div class="comment-meta-option">
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

	/**
	 * Adds min-width for amp support.
	 *
	 * @param string $value Old value.
	 * @param string $width Width value.
	 *
	 * @return string New css rule.
	 */
	public function amp_width_support( $value, $width ) {
		return 'min-width:' . esc_attr( $width ) . '%';
	}

	/**
	 * AMP styles for WPPR review amp page.
	 */
	public function amp_styles() {

		if ( empty( $this->review ) ) {
			return;
		}
		$template_style = $this->review->get_template();
		$amp_cache_key  = '_wppr_amp_css_' . str_replace( '.', '_', $this->version ) . '_' . $template_style;
		$output         = get_transient( $amp_cache_key );
		if ( empty( $output ) ) {

			require_once( ABSPATH . 'wp-admin/includes/file.php' );
			WP_Filesystem();
			/**
			 * Filesystem variable.
			 *
			 * @global \WP_Filesystem_Direct $wp_filesystem
			 */
			global $wp_filesystem;
			$output = '';
			$output .= $wp_filesystem->get_contents( WPPR_PATH . '/assets/css/common.css' );
			$output .= $wp_filesystem->get_contents( WPPR_PATH . '/assets/css/circle.css' );
			if ( $wp_filesystem->is_readable( WPPR_PATH . '/assets/css/' . $template_style . '.css' ) ) {
				$output .= $wp_filesystem->get_contents( WPPR_PATH . '/assets/css/' . $template_style . '.css' );
			}
			$output .= $wp_filesystem->get_contents( WPPR_PATH . '/assets/css/rating-amp.css' );
			$output .= $this->generate_styles();
			$output = $this->minify_amp_css( $output );

			set_transient( $amp_cache_key, $output, HOUR_IN_SECONDS );
		}
		echo apply_filters( 'wppr_add_amp_css', $output );
	}

	/**
	 * Minify css for AMP support.
	 *
	 * @param string $css Raw css.
	 *
	 * @return string The minified css.
	 */
	function minify_amp_css( $css ) {
		// some of the following functions to minimize the css-output are directly taken
		// from the awesome CSS JS Booster: https://github.com/Schepp/CSS-JS-Booster
		// all credits to Christian Schaefer: http://twitter.com/derSchepp
		// remove comments
		$css = preg_replace( '!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $css );
		// backup values within single or double quotes
		preg_match_all( '/(\'[^\']*?\'|"[^"]*?")/ims', $css, $hit, PREG_PATTERN_ORDER );
		for ( $i = 0; $i < count( $hit[1] ); $i ++ ) {
			$css = str_replace( $hit[1][ $i ], '##########' . $i . '##########', $css );
		}
		// remove traling semicolon of selector's last property
		$css = preg_replace( '/;[\s\r\n\t]*?}[\s\r\n\t]*/ims', "}\r\n", $css );
		// remove any whitespace between semicolon and property-name
		$css = preg_replace( '/;[\s\r\n\t]*?([\r\n]?[^\s\r\n\t])/ims', ';$1', $css );
		// remove any whitespace surrounding property-colon
		$css = preg_replace( '/[\s\r\n\t]*:[\s\r\n\t]*?([^\s\r\n\t])/ims', ':$1', $css );
		// remove any whitespace surrounding selector-comma
		$css = preg_replace( '/[\s\r\n\t]*,[\s\r\n\t]*?([^\s\r\n\t])/ims', ',$1', $css );
		// remove any whitespace surrounding opening parenthesis
		$css = preg_replace( '/[\s\r\n\t]*{[\s\r\n\t]*?([^\s\r\n\t])/ims', '{$1', $css );
		// remove any whitespace between numbers and units
		$css = preg_replace( '/([\d\.]+)[\s\r\n\t]+(px|em|pt|%)/ims', '$1$2', $css );
		// shorten zero-values
		$css = preg_replace( '/([^\d\.]0)(px|em|pt|%)/ims', '$1', $css );
		// constrain multiple whitespaces
		$css = preg_replace( '/\p{Zs}+/ims', ' ', $css );
		// remove newlines
		$css = str_replace( array( "\r\n", "\r", "\n" ), '', $css );
		$css = str_replace( '!important', '', $css );
		// Restore backupped values within single or double quotes
		for ( $i = 0; $i < count( $hit[1] ); $i ++ ) {
			$css = str_replace( '##########' . $i . '##########', $hit[1][ $i ], $css );
		}

		return $css;
	}

	/**
	 * Adding Font Awesome at the header for AMP.
	 */
	public function wppr_amp_add_fa() {
		echo '<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">';
	}

	/**
	 * Handle RTL for rating circle colored part.
	 */
	public function rating_circle_bar_styles( $styles, $rating ) {
		$degress    = ( is_rtl() ? ( $rating - 100 ) : $rating ) * 3.6;
		return "
		-webkit-transform: rotate({$degress}deg);
		-ms-transform: rotate({$degress}deg);
		transform: rotate({$degress}deg);
		";
	}

	/**
	 * Handle RTL for rating circle empty part.
	 */
	public function rating_circle_fill_styles( $styles, $rating ) {
		if ( is_rtl() ) {
			return '
            -webkit-transform: rotate(0deg);
            -ms-transform: rotate(0deg);
            transform: rotate(0deg);
            ';
		}
		return $styles;
	}

}
