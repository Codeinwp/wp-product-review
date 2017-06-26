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
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    3.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Stores a WPPR_Review_Model object.
	 *
	 * @since   3.0.0
	 * @access  private
	 * @var WPPR_Review_Model $review  The review model.
	 */
	private $review;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since   3.0.0
	 * @access  public
	 * @param   string $plugin_name       The name of the plugin.
	 * @param   string $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {
		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Method for loading the Review Model.
	 *
	 * @since   3.0.0
	 * @access  public
	 */
	public function init() {
		global $post;
		$this->review = new WPPR_Review_Model( $post->ID );
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
		if ( $this->review->is_active() ) {
			wp_enqueue_style( $this->plugin_name . '-frontpage-stylesheet', WPPR_URL . '/assets/css/frontpage.css', array(), $this->version );

			$review_data = $this->review->get_review_data();
            if ( $this->review->wppr_get_option( 'cwppos_lighbox' ) == 'no' && $review_data['click'] == 'image' ) {
                wp_enqueue_style( $this->plugin_name . '-lightbox-css', WPPR_URL . '/assets/css/lightbox.css', array(), $this->version );
            }
		}
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
		if ( $this->review->is_active() ) {
			wp_enqueue_script( $this->plugin_name . '-pie-chart-js', WPPR_URL . '/assets/js/pie-chart.js', array( 'jquery' ), $this->version, true );
			wp_enqueue_script( $this->plugin_name . '-frontpage-js', WPPR_URL . '/assets/js/main.js', array( 'jquery', $this->plugin_name . '-pie-chart-js' ), $this->version, true );
            $review_data = $this->review->get_review_data();
            if ( $this->review->wppr_get_option( 'cwppos_lighbox' ) == 'no' && $review_data['click'] == 'image' ) {
                wp_enqueue_script( $this->plugin_name . '-lightbox-js', WPPR_URL . '/assets/js/lightbox.min.js', array( 'jquery' ), $this->version, true );
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
		if ( $this->review->is_active() ) {

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
			$img_size = min( 180, $width * 0.51 * 0.4 );
			$height_left    = $img_size + 10;

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
	 * @param   mixed $content The page content.
	 * @return mixed
	 */
	public function display_on_front( $content ) {

		if ( $this->review->is_active() ) {
			$options_model = new WPPR_Options_Model();
			$output = '';
			include_once( WPPR_PATH . '/includes/public/layouts/default-tpl.php' );
			$review_position_before_content = $options_model->wppr_get_option( 'cwppos_show_reviewbox' );
			if ( $review_position_before_content == 'yes' ) {
			    $content = $output . $content;
			} elseif ( $review_position_before_content == 'no' ) {
				$content = $content . $output;
			}
		}
		return $content;
	}

}
