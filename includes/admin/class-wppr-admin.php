<?php
/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://themeisle.com/
 * @since      3.0.0
 *
 * @package    WPPR
 * @subpackage WPPR/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    WPPR
 * @subpackage WPPR/admin
 * @author     ThemeIsle <friends@themeisle.com>
 */
class WPPR_Admin {

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
	 * Initialize the class and set its properties.
	 *
	 * @since    3.0.0
	 *
	 * @param      string $plugin_name The name of this plugin.
	 * @param      string $version The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version     = $version;

	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since   3.0.0
	 * @access  public
	 *
	 * @param   string $hook The hook used filter loaded styles.
	 */
	public function enqueue_styles( $hook ) {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in WPPR_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The WPPR_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		if ( $hook == 'toplevel_page_wppr' ) {
			wp_enqueue_style( 'wp-color-picker' );
			wp_enqueue_style( $this->plugin_name . '-dashboard-css', WPPR_URL . '/assets/css/dashboard_styles.css', array(), $this->version );
			wp_enqueue_style( $this->plugin_name . '-admin-css', WPPR_URL . '/assets/css/admin.css', array(), $this->version );
		}
		if ( $hook == 'product-review_page_wppr_pro_upsell' || $hook == 'toplevel_page_wppr' ) {
			wp_enqueue_style( $this->plugin_name . '-upsell-css', WPPR_URL . '/assets/css/upsell.css', array(), $this->version );
		}
	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since   3.0.0
	 * @access  public
	 *
	 * @param   string $hook The hook used filter loaded scripts.
	 */
	public function enqueue_scripts( $hook ) {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in WPPR_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The WPPR_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		if ( $hook == 'toplevel_page_wppr' ) {
			wp_enqueue_script(
				$this->plugin_name . '-admin-js', WPPR_URL . '/assets/js/admin.js',
				array(
					'jquery',
					'wp-color-picker',
				),
				$this->version
			);
		}
	}

	/**
	 * Add admin menu items.
	 *
	 * @since   3.0.0
	 * @access  public
	 */
	public function menu_pages() {
		add_menu_page(
			__( 'WP Product Review', 'wp-product-review' ), __( 'Product Review', 'wp-product-review' ), 'manage_options', 'wppr', array(
				$this,
				'page_settings',
			), 'dashicons-star-half', '99.87414'
		);
		if ( ! defined( 'WPPR_PRO_VERSION' ) ) {
			add_submenu_page(
				'wppr', __( 'More Features', 'wp-product-review' ), __( 'More Features ', 'wp-product-review' ) . '<span class="dashicons
		dashicons-star-filled" style="vertical-align:-5px; padding-left:2px; color:#FFCA54;"></span>', 'manage_options', 'wppr_pro_upsell',
				array(
					$this,
					'page_upsell',
				)
			);
		}
	}

	/**
	 * Method to render settings page.
	 *
	 * @since   3.0.0
	 * @access  public
	 */
	public function page_settings() {
		$model  = new WPPR_Options_Model();
		$render = new WPPR_Admin_Render_Controller( $this->plugin_name, $this->version );
		$render->retrive_template( 'settings', $model );
	}

	/**
	 * Method to render up-sell page.
	 *
	 * @since   3.0.0
	 * @access  public
	 */
	public function page_upsell() {
		$render = new WPPR_Admin_Render_Controller( $this->plugin_name, $this->version );
		$render->retrive_template( 'upsell' );
	}

	/**
	 * Method called from AJAX request to update options.
	 *
	 * @since   3.0.0
	 * @access  public
	 */
	public function update_options() {
		$model = new WPPR_Options_Model();
		$data  = $_POST['cwppos_options'];

		$nonce = $data[ count( $data ) - 1 ];
		if ( ! isset( $nonce['name'] ) ) {
			die( 'invalid nonce field' );
		}
		if ( $nonce['name'] != 'wppr_nonce_settings' ) {
			die( 'invalid nonce name' );
		}
		if ( wp_verify_nonce( $nonce['value'],'wppr_save_global_settings' ) != 1 ) {
			die( 'invalid nonce value' );
		}

		foreach ( $data as $option ) {
			$model->wppr_set_option( $option['name'], $option['value'] );
		}
		die();
	}

}
