<?php
/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://themeisle.com/
 * @since      1.0.0
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
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string $plugin_name       The name of the plugin.
	 * @param      string $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
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

		// wp_enqueue_style( $this->plugin_name . '-tiplsy-js', WPPR_URL . '/assets/css/public.css', array(), $this->version );
	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    1.0.0
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

		// wp_enqueue_script( $this->plugin_name . '-tiplsy-js', WPPR_URL . '/assets/js/public.js', array( 'jquery' ), $this->version );
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
		return $content . '<hr/>' . 'RENDER REVIEW HERE !!!';
	}

}
