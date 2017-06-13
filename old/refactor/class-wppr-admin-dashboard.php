<?php

/**
 * WPPR Admin Dashboard.
 *
 * @package     WPPR
 * @subpackage  Admin
 * @copyright   Copyright (c) 2017, Marius Cristea
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       3.0.0
 */

/**
 * Class WPPR_Admin_Dashboard for handling global options.
 */
class WPPR_Admin_Dashboard {

	public function __construct() {

		$this->setup_hooks();
	}

	/**
	 * Setup the hooks used.
	 */
	public function setup_hooks() {
		add_action( 'admin_menu', array( $this, 'menu_pages' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'load_scripts' ) );
	}

	/**
	 * Load assets in the admin dashboard
	 *
	 * @param string $hook The name of the page hook.
	 */
	public function load_scripts( $hook ) {
		if ( $hook == 'toplevel_page_wppr' ) {
			wp_enqueue_style( 'wppr-admin-css', WPPR_URL . '/css/admin.css', array(), WPPR_LITE_VERSION );
			wp_enqueue_script( 'wppr-admin-js', WPPR_URL . '/javascript/admin.js', array( 'jquery' ), WPPR_LITE_VERSION );
		}
		if ( $hook == 'product-review_page_wppr_pro_upsell' || $hook == 'toplevel_page_wppr' ) {
			wp_enqueue_style( 'wppr-upsell-css', WPPR_URL . '/css/upsell.css', array(), WPPR_LITE_VERSION );
		}
	}

	/**
	 * Add admin pages.
	 */
	public function menu_pages() {
		add_menu_page( __( 'WP Product Review', 'wp-product-review' ), __( 'Product Review', 'wp-product-review' ), 'manage_options', 'wppr', array(
			$this,
			'render',
		), 'dashicons-star-half', '99.87414' );
		if ( ! defined( 'WPPR_PRO_VERSION' ) ) {
			add_submenu_page( 'wppr', __( 'More Features', 'wp-product-review' ), __( 'More Features ', 'wp-product-review' ) . '<span class="dashicons
		dashicons-star-filled" style="vertical-align:-5px; padding-left:2px; color:#FFCA54;"></span>', 'manage_options', 'wppr_pro_upsell', array(
				$this,
				'render_upsell',
			) );
		}
	}

	/**
	 * Render options page.
	 */
	public function render() {

		wppr_admin_template( 'settings' );
	}

	/**
	 * Render the upsell page.
	 */
	public function render_upsell() {
		wppr_admin_template( 'upsell-page' );
	}
}
