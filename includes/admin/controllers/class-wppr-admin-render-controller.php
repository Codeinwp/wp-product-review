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
        //var_dump( $this->options );
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
            wp_enqueue_style( 'wp-color-picker' );
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
        if ( file_exists( WPPR_PATH . '/includes/admin/layouts/css/' . $name . '.css' ) ) {
            wp_enqueue_style( $this->plugin_name . '-'. $name . '-css', WPPR_URL . '/includes/admin/layouts/css/' . $name . '.css', array(), $this->version );
        }
        include_once( WPPR_PATH . '/includes/admin/layouts/' . $name . '_tpl.php' );
    }

    /**
     * Validate a class string.
     *
     * @param string $class String to validate.
     *
     * @return string The validate string.
     */
    private function validate_class( $class ) {
        return implode( ' ', array_map( 'sanitize_html_class', explode( ' ', str_replace( '  ', ' ', $class ) ) ) );
    }

    public function add_element( $tabid, $field ) {

        switch ( $field['type'] ) {
            case 'input_text':
                echo $this->add_input_text( $field );
                break;
            case 'select':
                echo $this->add_select( $field );
                break;
            case 'color':
                echo $this->add_color( $field );
                break;
            case 'text':
                echo $this->add_text( $field );
                break;
        }
        if ( isset( $errors ) ) { return $errors; }
    }

    public function add_select( $args ) {
        $option_name = WPPR_Global_Settings::instance()->get_options_name();
        $defaults = array(
            'name'        => null,
            'id'          => null,
            'value'       => null,
            'class'       => 'wppr-text',
            'placeholder' => '',
            'disabled'    => false,
        );
        $args     = wp_parse_args( $args, $defaults );
        $class    = $this->validate_class( $args['class'] );

        $options = array();
        foreach ( $args['options'] as $ov => $op ) {
            $options[ esc_attr( $ov ) ] = esc_html( $op );
        }

        $output = '
				<div class="controls ' . $class . '">
				<div class="explain">' . $args['name'] . '</div><p class="field_description">' . $args['description'] . '</p>';

        $output .= '<select class=" cwp_select" name="' . $option_name . '[' . $args['id'] . ']" > ';

        foreach ( $options as $k => $v ) {

            $output .= "<option value='" . $k . "' " . ( ( isset( $this->options[ 'cwppos_' . $args['id'] ] ) && $this->options[ 'cwppos_' . $args['id'] ] == $k ) ? 'selected' : '') . '>' . $v . '</option>';
        }

        $output .= '</select></div>';

        return apply_filters( 'wppr_field', $output, $args );
    }

    public function add_color( $args ) {
        $option_name = WPPR_Global_Settings::instance()->get_options_name();
        $defaults = array(
            'name'        => null,
            'id'          => null,
            'value'       => null,
            'class'       => 'wppr-text',
            'placeholder' => '',
            'disabled'    => false,
        );
        $args     = wp_parse_args( $args, $defaults );
        $class    = $this->validate_class( $args['class'] );
        $output = '
				<div class="controls ' . $class . ' ">
				    <div class="explain">' . $args['name'] . '</div>
				    <p class="field_description">' . $args['description'] . '</p>
				    <input type="hidden" id="' . esc_attr( $args['id'] ) . '_color" name="' . $option_name . '[' . esc_attr( $args['id'] ) . ']" value="' . $this->options[ 'cwppos_' . esc_attr( $args['id'] ) ] . '"/></br>
				    <input type="text" name=""	class="subo-color-picker" id="' . esc_attr( $args['id'] ) . '_color_selector" value="' . $this->options[ 'cwppos_' . esc_attr( $args['id'] ) ] . '" /><br/>
				</div>';

        return apply_filters( 'wppr_field', $output, $args );
    }

    public function add_radio( $args ) {
        $defaults = array(
            'name'     => null,
            'id'       => null,
            'current'  => null,
            'value'    => null,
            'class'    => 'wppr-radio',
            'disabled' => false,
        );
        $args     = wp_parse_args( $args, $defaults );
        $disabled = '';
        if ( ! empty( $args['options']['disabled'] ) ) {
            $disabled .= ' disabled="disabled"';
        }
        if ( is_null( $args['id'] ) ) {
            $args['id'] = $args['name'];
        }
        $class  = $this->validate_class( $args['class'] );
        $output = '<div class="controls ' . $class . '">
                        <div class="explain">' . $args['name'] . '</div>
                        <p class="field_description">' . $args['description'] . '</p>
                        <input type="radio" ' . $disabled . ' name="' . esc_attr( $args['name'] ) . '" id="' . esc_attr( $args['id'] ) . '" class="' . $class . '" ' . checked( $args['value'], $args['current'], false ) . ' value="' . esc_attr( $args['value'] ) . '" />
                   </div>     
                   ';

        return apply_filters( 'wppr_field', $output, $args );
    }

    public function add_text( $args ) {
        $option_name = WPPR_Global_Settings::instance()->get_options_name();
        $defaults = array(
            'name'        => null,
            'id'          => null,
            'value'       => null,
            'class'       => 'wppr-text',
            'placeholder' => '',
            'disabled'    => false,
        );
        $args     = wp_parse_args( $args, $defaults );
        $class    = $this->validate_class( $args['class'] );
        $output = '
				<div class="controls ' . $class . '">
				    <div class="explain">' . $args['name'] . '</div>
				    <p class="field_description">' . $args['description'] . '</p>
				    <textarea class="cwp_textarea " placeholder="' . $args['name'] . '" name="' . $option_name . '[' . esc_attr( $args['id'] ) . ']"    >' . $this->options[ 'cwppos_' . esc_attr( $args['id'] ) ] . '</textarea>
				</div>';
        return apply_filters( 'wppr_field', $output, $args );
    }

    public function add_input_text( $args ) {
        $option_name = WPPR_Global_Settings::instance()->get_options_name();
        $defaults = array(
            'name'        => null,
            'id'          => null,
            'value'       => null,
            'class'       => 'wppr-text',
            'placeholder' => '',
            'disabled'    => false,
        );
        $args     = wp_parse_args( $args, $defaults );
        $class    = $this->validate_class( $args['class'] );
        $disabled = '';
        if ( $args['disabled'] ) {
            $disabled = ' disabled="disabled"';
        }
        if ( is_null( $args['id'] ) ) {
            $args['id'] = $args['name'];
        }

        $output = '
				<div class="controls ' . $class . '">
				    <div class="explain">' . $args['name'] . '</div>
				    <p class="field_description">' . $args['description'] . '</p>
				    <input type="text" ' . $disabled . ' placeholder="' . $args['name'] . '" name="' . $option_name . '[' . esc_attr( $args['id'] ) . ']" id="' . esc_attr( $args['id'] ) . '" class="' . $class . '"   value="' . esc_attr( $args['value'] ) . '" />
				</div>';

        return apply_filters( 'wppr_field', $output, $args );
    }
}