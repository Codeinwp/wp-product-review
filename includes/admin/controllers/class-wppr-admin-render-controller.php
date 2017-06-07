<?php
/**
 * WPPR Admin Render Controller
 *
 * @package     WPPR
 * @subpackage  Admin
 * @copyright   Copyright (c) 2017, Marius Cristea
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       3.0.0
 *
 */

/**
 * Class WPPR_Admin_Render_Controller for handling page rendering.
 */
class WPPR_Admin_Render_Controller {

    /**
     * Method to add Admin Menu Pages
     *
     * @since   3.0.0
     * @access  public
     */
    public function menu_pages() {
        add_menu_page( __( 'WP Product Review', 'wp-product-review' ), __( 'Product Review', 'wp-product-review' ), 'manage_options', 'wppr', array(
            $this,
            'page_settings'
        ), 'dashicons-star-half', '99.87414' );
        if ( ! defined( 'WPPR_PRO_VERSION' ) ) {
            add_submenu_page( 'wppr', __( 'More Features', 'wp-product-review' ), __( 'More Features ', 'wp-product-review' ) . '<span class="dashicons
		dashicons-star-filled" style="vertical-align:-5px; padding-left:2px; color:#FFCA54;"></span>', 'manage_options', "wppr_pro_upsell", array(
                $this,
                'page_upsell'
            ) );
        }
    }

    public function page_settings() {
        $this->retrive_template( 'settings' );
    }

    public function page_upsell() {
        $this->retrive_template( 'upsell' );
    }

    /**
     * Utility method to include required layout.
     *
     * @since   3.0.0
     * @access  protected
     * @param   string  $name   The name of the layout to be retrieved.
     */
    protected function retrive_template( $name ) {
        include_once( WPPR_PATH . '/includes/admin/layouts/' . $name . '_tpl.php' );
    }
}