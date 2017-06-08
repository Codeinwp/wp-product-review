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
     * Initialize the class and set its properties.
     *
     * @since    3.0.0
     * @param      string    $plugin_name       The name of this plugin.
     * @param      string    $version    The version of this plugin.
     */
    public function __construct( $plugin_name, $version ) {

        $this->plugin_name = $plugin_name;
        $this->version = $version;

        $this->options = get_option( 'cwppos_options' );
        var_dump( $this->options );
    }

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

    /**
     * Load assets in the admin dashboard.
     *
     * @since   3.0.0
     * @access  public
     * @param   string  $hook   The name of the page hook.
     */
    public function render_page_scripts( $hook ) {
        if ( $hook == 'toplevel_page_wppr' ) {
            wp_enqueue_style( $this->plugin_name . '-dashboard-css', WPPR_URL . '/assets/css/dashboard_styles.css', array(), $this->version );
            wp_enqueue_style( $this->plugin_name . '-admin-css', WPPR_URL . '/assets/css/admin.css', array(), $this->version );
            wp_enqueue_script( $this->plugin_name . '-tiplsy-js', WPPR_URL . '/assets/js/tipsy.js', array( 'jquery' ), $this->version );
            wp_enqueue_script( $this->plugin_name . '-admin-js', WPPR_URL . '/assets/js/admin.js', array( 'jquery', 'wp-color-picker' ), $this->version );
        }
        if ( $hook == 'product-review_page_wppr_pro_upsell' || $hook == 'toplevel_page_wppr' ) {
            wp_enqueue_style( $this->plugin_name . '-upsell-css', WPPR_URL . '/assets/css/upsell.css', array(), $this->version );
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

    public function add_element( $tabid, $field ) {

        switch ( $field['type'] ) {
            case 'input_text':

                $this->add_input_text( $tabid, esc_html( $field['name'] ), esc_html( $field['description'] ), esc_attr( $field['id'] ) );

                break;
            case 'select':
                $no = array();
                foreach ( $field['options'] as $ov => $op ) {
                    $no[ esc_attr( $ov ) ] = esc_html( $op );
                }
                $this->add_select( $tabid, esc_html( $field['name'] ), esc_html( $field['description'] ), esc_attr( $field['id'] ), $no );
                break;
        }
        if ( isset( $errors ) ) { return $errors; }
    }

    public function add_select( $tabid, $name, $description, $id, $options, $class = '' ) {

        $html = '
				<div class="controls ' . $class . '">
				<div class="explain">' . $name . '</div><p class="field_description">' . $description . '</p>';

        $html .= '<select class=" cwp_select" name="' . 'cwppos_options' . '[' . $id . ']" > ';

        foreach ( $options as $k => $v ) {

            $html .= "<option value='" . $k . "' " . ( ( isset( $this->options[ 'cwpps_' . $id ] ) && $this->options[ 'cwpps_' . $id ] == $k ) ? 'selected' : '') . '>' . $v . '</option>';
        }

        $html .= '</select></div>';

        echo $html;
    }

    public function add_input_text( $tabid, $name, $description, $id, $class = '' ) {
        $html = '
				<div class="controls ' . $class . '">
				<div class="explain">' . $name . '</div><p class="field_description">' . $description . '</p> <input class="cwp_input " placeholder="' . $name . '" name="' . 'cwppos_options' . '[' . $id . ']" type="text" value="' . $this->options[ 'cwppos_' . $id ] . '"></div>';
        echo $html;
    }
}