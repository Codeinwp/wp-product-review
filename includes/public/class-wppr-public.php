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
		}
	}

	public function dynamic_stylesheet() {
		$options_model = new WPPR_Options_Model();

		$style = '
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
			$content = $content . '<hr/>' . $output;
		}
		return $content;
	}

}
