<?php
/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       https://themeisle.com/
 * @since      3.0.0
 *
 * @package    WPPR
 * @subpackage WPPR/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      3.0.0
 * @package    WPPR
 * @subpackage WPPR/includes
 * @author     ThemeIsle <friends@themeisle.com>
 */
class WPPR {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    3.0.0
	 * @access   protected
	 * @var      WPPR_Loader $loader Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    3.0.0
	 * @access   protected
	 * @var      string $plugin_name The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since    3.0.0
	 * @access   protected
	 * @var      string $version The current version of the plugin.
	 */
	protected $version;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since    3.0.0
	 */
	public function __construct() {
		$this->plugin_name = 'wppr';
		$this->version     = '3.4.2';

		$this->load_dependencies();
		$this->set_locale();
		$this->define_admin_hooks();
		$this->define_public_hooks();
	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - WPPR_Loader. Orchestrates the hooks of the plugin.
	 * - WPPR_I18n. Defines internationalization functionality.
	 * - WPPR_Admin. Defines all hooks for the admin area.
	 * - WPPR_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    3.0.0
	 * @access   private
	 */
	private function load_dependencies() {
		$this->loader = new WPPR_Loader();
	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the WPPR_I18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    3.0.0
	 * @access   private
	 */
	private function set_locale() {
		$plugin_i18n = new WPPR_I18n();
		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );
	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    3.0.0
	 * @access   private
	 */
	private function define_admin_hooks() {

		$plugin_admin = new WPPR_Admin( $this->get_plugin_name(), $this->get_version() );
		$this->loader->add_action( 'admin_menu', $plugin_admin, 'menu_pages' );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );
		$this->loader->add_action( 'wp_ajax_update_options', $plugin_admin, 'update_options' );
		$this->loader->add_action( 'load-edit.php', $plugin_admin, 'get_additional_fields' );

		$plugin_editor = new WPPR_Editor( $this->get_plugin_name(), $this->get_version() );
		$this->loader->add_action( 'add_meta_boxes', $plugin_editor, 'set_editor' );
		$this->loader->add_action( 'save_post', $plugin_editor, 'editor_save' );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_editor, 'load_assets' );

		$plugin_widget_latest = new WPPR_Latest_Products_Widget();
		$this->loader->add_action( 'widgets_init', $plugin_widget_latest, 'register' );

		$plugin_widget_top = new WPPR_Top_Products_Widget();
		$this->loader->add_action( 'widgets_init', $plugin_widget_top, 'register' );
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     3.0.0
	 * @return    string    The name of the plugin.
	 */
	public function get_plugin_name() {
		return $this->plugin_name;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     3.0.0
	 * @return    string    The version number of the plugin.
	 */
	public function get_version() {
		return $this->version;
	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    3.0.0
	 * @access   private
	 */
	private function define_public_hooks() {

		$plugin_public = new WPPR_Public( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'comment_post', $plugin_public, 'save_comment_fields', 1 );

		if ( is_admin() ) {
			return;
		}
		$this->loader->add_action( 'wp', $plugin_public, 'setup_post' );
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'load_review_assets' );
		$this->loader->add_action( 'comment_form_logged_in_after', $plugin_public, 'add_comment_fields' );
		$this->loader->add_action( 'comment_form_after_fields', $plugin_public, 'add_comment_fields' );
		$this->loader->add_filter( 'comment_text', $plugin_public, 'show_comment_ratings' );

		if ( function_exists( 'ampforwp_is_amp_endpoint' ) || function_exists( 'is_amp_endpoint' ) ) {

			$this->loader->add_action( 'amp_post_template_css', $plugin_public, 'wppr_amp_support' );
		}

		$currentTheme = wp_get_theme();
		if ( $currentTheme->get( 'Name' ) !== 'Bookrev' && $currentTheme->get( 'Name' ) !== 'Book Rev Lite' ) {
			$this->loader->add_filter( 'the_content', $plugin_public, 'display_on_front' );
		}

	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    3.0.0
	 */
	public function run() {
		$this->loader->run();
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     3.0.0
	 * @return    WPPR_Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader() {
		return $this->loader;
	}

}
