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
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since   3.0.0
	 * @access  public
	 */
	public function enqueue_styles() {
		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Wppr_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Wppr_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */
		$this->init();
		if ( $this->review->is_active() || $this->is_shortcode_used() ) {
			wp_enqueue_style( $this->plugin_name . '-frontpage-stylesheet', WPPR_URL . '/assets/css/frontpage.css', array(), $this->version );

			if ( $this->review->wppr_get_option( 'cwppos_lighbox' ) == 'no' ) {
				wp_enqueue_style( $this->plugin_name . '-lightbox-css', WPPR_URL . '/assets/css/lightbox.css', array(), $this->version );
			}
			if ( $this->review->wppr_get_option( 'cwppos_show_userreview' ) == 'yes' ) {

				wp_enqueue_style( $this->plugin_name . 'jqueryui', WPPR_URL . '/assets/css/jquery-ui.css', array(), $this->version );
			}
		}
	}

	/**
	 * Method for loading the Review Model.
	 *
	 * @since   3.0.0
	 * @access  public
	 */
	public function init() {
		global $post;
		if ( $post ) {
			$this->review = new WPPR_Review_Model( $post->ID );
		} else {
			$this->review = new WPPR_Review_Model( 0 );
		}
	}

	/**
	 * Check if the current post has a shortcode or not.
	 *
	 * @return bool Either we use the shortcode or not.
	 */
	public function is_shortcode_used() {
		global $post;

		if ( ! has_shortcode( $post->post_content, 'P_REVIEW' ) ) {
			return false;
		}

		return true;
	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since   3.0.0
	 * @access  public
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Wppr_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Wppr_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */
		$this->init();
		if ( $this->review->is_active() || $this->is_shortcode_used() ) {

			wp_enqueue_script( $this->plugin_name . '-pie-chart-js', WPPR_URL . '/assets/js/pie-chart.js', array( 'jquery' ), $this->version, true );
			wp_enqueue_script(
				$this->plugin_name . '-frontpage-js', WPPR_URL . '/assets/js/main.js', array(
					'jquery',
					$this->plugin_name . '-pie-chart-js',
				), $this->version, true
			);
			if ( $this->review->wppr_get_option( 'cwppos_lighbox' ) == 'no' ) {
				wp_enqueue_script( $this->plugin_name . '-lightbox-js', WPPR_URL . '/assets/js/lightbox.min.js', array( 'jquery' ), $this->version, true );
			}

			if ( $this->review->wppr_get_option( 'cwppos_show_userreview' ) == 'yes' ) {
				wp_enqueue_script( 'jquery-ui-slider' );
			}
		}
	}

	/**
	 * Method to define dynamic style and script options based on saved settings.
	 *
	 * @since   3.0.0
	 * @access  public
	 */
	public function dynamic_stylesheet() {
		$this->init();
		if ( $this->review->is_active() || $this->is_shortcode_used() ) {

			$options_model = new WPPR_Options_Model();

			global $content_width;
			if ( $options_model->wppr_get_option( 'cwppos_widget_size' ) != '' ) {
				$width = $options_model->wppr_get_option( 'cwppos_widget_size' );
			} else {
				$width = $content_width;
			}
			if ( $width < 200 ) {
				$width = 600;
			}
			$img_size    = min( 180, $width * 0.51 * 0.4 );
			$height_left = $img_size + 10;

			$conditional_media_styles = '';
			if ( $options_model->wppr_get_option( 'cwppos_widget_size' ) != '' ) {
				$conditional_media_styles = '
                #review-statistics {
                    width: ' . $options_model->wppr_get_option( 'cwppos_widget_size' ) . 'px;
                }
                ';
			}

			$conditional_styles = '';
			if ( $options_model->wppr_get_option( 'cwppos_show_icon' ) == 'yes' ) {
				$conditional_styles .= '
                div.affiliate-button a span {
                    background: url("' . WPPR_URL . '/assets/img/cart-icon.png") no-repeat left center;
                } 
        
                div.affiliate-button a:hover span {
                    background: url("' . WPPR_URL . '/assets/img/cart-icon-hover.png") no-repeat left center;
                }
                ';
			}

			if ( $options_model->wppr_get_option( 'cwppos_show_userreview' ) == 'yes' ) {
				$conditional_styles .= '
                .commentlist .comment-body p {
                    clear: left;
                }
                ';
			}

			$style = '
                <style type="text/css">
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
                        border-top: ' . $options_model->wppr_get_option( 'cwppos_reviewboxbd_width' ) . 'px solid ' . $options_model->wppr_get_option( 'cwppos_reviewboxbd_color' ) . ';
                    }
            
                    .user-comments-grades .comment-meta-grade-bar,
                    #review-statistics .review-wu-bars ul li {
                        background: ' . $options_model->wppr_get_option( 'cwppos_rating_default' ) . ';
                    }
            
                    #review-statistics .rev-option.customBarIcon ul li {
                        color: ' . $options_model->wppr_get_option( 'cwppos_rating_default' ) . ';
                    }
            
                    #review-statistics .review-wrap-up .review-wu-right ul li, #review-statistics .review-wu-bars h3, .review-wu-bars span, #review-statistics .review-wrap-up .cwpr-review-top .cwp-item-category a {
                        color: ' . $options_model->wppr_get_option( 'cwppos_font_color' ) . ';
                    }
            
                    #review-statistics .review-wrap-up .review-wu-right .pros h2 {
                        color: ' . $options_model->wppr_get_option( 'cwppos_pros_color' ) . ';
                    }
            
                    #review-statistics .review-wrap-up .review-wu-right .cons h2 {
                        color: ' . $options_model->wppr_get_option( 'cwppos_cons_color' ) . ';
                    }
                
                    div.affiliate-button a {
                        border: 2px solid ' . $options_model->wppr_get_option( 'cwppos_buttonbd_color' ) . ';
                    }
            
                    div.affiliate-button a:hover {
                        border: 2px solid ' . $options_model->wppr_get_option( 'cwppos_buttonbh_color' ) . ';
                    }
            
                    div.affiliate-button a {
                        background: ' . $options_model->wppr_get_option( 'cwppos_buttonbkd_color' ) . ';
                    }
            
                    div.affiliate-button a:hover {
                        background: ' . $options_model->wppr_get_option( 'cwppos_buttonbkh_color' ) . ';
                    }
            
                    div.affiliate-button a span {
                        color: ' . $options_model->wppr_get_option( 'cwppos_buttontxtd_color' ) . ';
                    }
            
                    div.affiliate-button a:hover span {
                        color: ' . $options_model->wppr_get_option( 'cwppos_buttontxth_color' ) . ';
                    }
                    
                    ' . $conditional_styles . '
                </style>
            ';

			echo $style;

			$script = '
                <script type="text/javascript">
                    var c1 = "' . $options_model->wppr_get_option( 'cwppos_rating_weak' ) . '";
                    var c2 = "' . $options_model->wppr_get_option( 'cwppos_rating_notbad' ) . '";
                    var c3 = "' . $options_model->wppr_get_option( 'cwppos_rating_good' ) . '";
                    var c4 = "' . $options_model->wppr_get_option( 'cwppos_rating_very_good' ) . '";
                </script>
            ';

			echo $script;
		}// End if().
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
		$this->init();
		if ( $this->review->is_active() ) {
			$options_model = new WPPR_Options_Model();
			$output        = '';
			$visual        = 'full';

			if ( $visual == 'full' ) {
				$theme_template = get_template_directory() . '/wppr/default.php';
				if ( file_exists( $theme_template ) ) {
					include( $theme_template );
				} else {
					include( WPPR_PATH . '/includes/public/layouts/default-tpl.php' );
				}
			}

			include_once( WPPR_PATH . '/includes/public/layouts/rich-json-ld.php' );

			$review_position_before_content = $options_model->wppr_get_option( 'cwppos_show_reviewbox' );
			if ( $review_position_before_content == 'yes' ) {
				$content = $output . $content;
			} elseif ( $review_position_before_content == 'no' ) {
				$content = $content . $output;
			}
		}

		return $content;
	}

	/**
	 * Sets the default settings for front end display
	 *
	 * @since   3.0.0
	 * @access  public
	 */
	public function default_settings() {
		$this->init();
		if ( $this->review->is_active() ) {

			$options_model = new WPPR_Options_Model();

			$options_model->wppr_get_option( 'cwppos_rating_default' );

			if ( class_exists( 'WPPR_Pro' ) ) {
				$isSetToPro = true;
			} else {
				$isSetToPro = false;
			}

			if ( $isSetToPro ) {
				$uni_font = $options_model->wppr_get_option( 'cwppos_change_bar_icon' );
			} else {
				$uni_font = '';
			}
			$track = $options_model->wppr_get_option( 'cwppos_rating_chart_default' );
			if ( is_array( $uni_font ) ) {
				$uni_font = $uni_font[0];
			} elseif ( substr( $uni_font, 0, 1 ) == '#' ) {
				$uni_font = $uni_font;
			} else {
				$uni_font = '';
			}

			if ( ! empty( $uni_font ) ) {
				if ( $isSetToPro ) {
					if ( $options_model->wppr_get_option( 'cwppos_fontawesome' ) === 'no' ) {
						wp_enqueue_style( 'cwp-pac-fontawesome-stylesheet', WPPR_URL . '/assets/css/font-awesome.min.css' );
					}
				}
			}
			echo "<script type='text/javascript'>
                    var cwpCustomBarIcon = '" . $uni_font . "';
                    var isSetToPro = '" . $isSetToPro . "';
                    var trackcolor = '" . $track . "';
                </script>";
		}// End if().
	}

	/**
	 * Adds the comment form fields.
	 *
	 * @return string The comment form fields.
	 */
	function add_comment_fields() {
		$this->init();
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
            <input type="text" id="wppr-slider-option-' . $k . '" class="meta_option_input" value="0" name="wppr-slider-option-' . $k . '" readonly="readonly">
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
		foreach ( $option_names as $k => $value ) {
			if ( isset( $_POST[ 'wppr-slider-option-' . $k ] ) ) {
				$option_value = wp_filter_nohtml_kses( $_POST[ 'wppr-slider-option-' . $k ] );
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
		$this->init();
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
			$comment_meta_score = $option['value'] * 10;
			$return             .= '<div class="comment-meta-option">
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
