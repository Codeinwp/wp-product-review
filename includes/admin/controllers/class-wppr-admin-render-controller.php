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
        $global_settings = WPPR_Global_Settings::instance();
        $fields = $global_settings->get_fields();
        $defaults = $this->cwppos_get_config_defaults( $fields );
        $this->options = array_merge( $defaults,is_array( $this->options ) ? $this->options : array() );

    }

    public function cwppos_get_config_defaults( $structure ) {
        $defaults = array();
        foreach ( $structure as $k => $fields ) {

            if ( $fields['type'] == 'tab' ) {

                foreach ( $fields['options'] as $r => $field ) {

                    if ( $field['type'] == 'group' ) {

                        foreach ( $field['options'] as $m => $gfield ) {
                            if ( $gfield['type'] != 'title' ) {
                                $defaults[ $gfield['id'] ] = $gfield['default']; }
                        }
                    } else {
                        if ( $field['type'] != 'title' ) {
                            $defaults[ $field['id'] ] = $field['default']; }
                    }
                }
            }
        }
        return $defaults;
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

                $this->add_input_text( $tabid,esc_html( $field['name'] ),esc_html( $field['description'] ),esc_attr( $field['id'] ) );

                break;
            case 'input_number':

                $this->add_input_number( $tabid,esc_html( $field['name'] ),esc_html( $field['description'] ),esc_attr( $field['id'] ) );

                break;
            case 'textarea':

                $this->add_textarea( $tabid,esc_html( $field['name'] ),esc_html( $field['description'] ),esc_attr( $field['id'] ) );

                break;
            case 'textarea_html':
                if ( current_user_can( 'unfiltered_html' ) ) {
                    $this->add_textarea( $tabid,esc_html( $field['name'] ),esc_html( $field['description'] ),esc_attr( $field['id'] ) ); } else { 					$this->add_restriction( $tabid,esc_html( $field['name'] ) ); }

                break;
            case 'editor':

                $this->add_editor( $tabid,esc_html( $field['name'] ),esc_html( $field['description'] ),esc_attr( $field['id'] ) );

                break;
            case 'color':

                $this->add_color( $tabid,esc_html( $field['name'] ),esc_html( $field['description'] ),esc_attr( $field['id'] ) );

                break;
            case 'image':

                $this->add_image( $tabid,esc_html( $field['name'] ),esc_html( $field['description'] ),esc_attr( $field['id'] ) );

                break;
            case 'button':

                $this->add_button( $tabid,esc_html( $field['name'] ),esc_html( $field['description'] ),esc_attr( $field['id'] ) );

                break;
            case 'typography':

                $this->add_typography( $tabid,esc_html( $field['name'] ),esc_html( $field['description'] ),esc_attr( $field['id'] ) );
                break;
            case 'background':

                $this->add_background( $tabid,esc_html( $field['name'] ),esc_html( $field['description'] ),esc_attr( $field['id'] ) );
                break;
            case 'select':

                $no = array();
                foreach ( $field['options'] as $ov => $op ) {
                    $no[ esc_attr( $ov ) ] = esc_html( $op );
                }
                $this->add_select( $tabid,esc_html( $field['name'] ),esc_html( $field['description'] ),esc_attr( $field['id'] ),$no );
                break;
            case 'radio':

                $no = array();
                foreach ( $field['options'] as $ov => $op ) {
                    $no[ esc_attr( $ov ) ] = esc_html( $op );
                }
                $this->add_radio( $tabid,esc_html( $field['name'] ),esc_html( $field['description'] ),esc_attr( $field['id'] ),$no );
                break;
            case 'multiselect':

                $no = array();
                foreach ( $field['options'] as $ov => $op ) {
                    $no[ esc_attr( $ov ) ] = esc_html( $op );
                }

                $this->add_multiselect( $tabid,esc_html( $field['name'] ),esc_html( $field['description'] ),esc_attr( $field['id'] ),$no );
                break;
            case 'checkbox':

                $no = array();
                foreach ( $field['options'] as $ov => $op ) {
                    $no[ esc_attr( $ov ) ] = esc_html( $op );
                }

                $this->add_checkbox( $tabid,esc_html( $field['name'] ),esc_html( $field['description'] ),esc_attr( $field['id'] ),$no );
                break;
            case 'title':
                $this->add_title( $tabid,esc_html( $field['name'] ) );
                break;

            case 'change_icon':
                $this->change_review_icon( $tabid,esc_html( $field['name'] ),esc_html( $field['description'] ),esc_attr( $field['id'] ) );
                break;

        }
        if ( isset( $errors ) ) { return $errors; }
    }

    public function add_input_text( $tabid, $name, $description, $id, $class = '' ) {
        $html = '
				<div class="controls ' . $class . '">
				<div class="explain">' . $name . '</div><p class="field_description">' . $description . '</p> <input class="cwp_input " placeholder="' . $name . '" name="' . 'cwppos_options' . '[' . $id . ']" type="text" value="' . $this->options[ $id ] . '"></div>';

        $this->tabs[ $tabid ]['elements'][] = array(
            'type' => 'input_text',
            'html' => $html,
        );

    }

    public function add_typography( $tabid, $name, $description, $id, $class = '' ) {
        $fonts = $this->get_fonts();
        $style = $this->get_font_styles();
        $sizes = $this->get_font_sizes();
        $html = '
				<div class="controls ' . $class . '">
				<div class="explain">' . $name . '</div><p class="field_description">' . $description . '</p> <div class="cwp_typo">

					 <input type="hidden" id="' . $id . '_color" name="' . 'cwppos_options' . '[' . $id . '][color]" value="' . $this->options[ $id ]['color'] . '"/>
				<input type="text" name=""	class="subo-color-picker" id="' . $id . '_color_selector" value="' . $this->options[ $id ]['color'] . '" />

						<select class="cwp_select cwp_tipsy" original-title="Font family" name="' . 'cwppos_options' . '[' . $id . '][font]" > ';
        foreach ( $fonts as $k => $v ) {

            $html .= "<option value='" . $k . "' " . ($this->options[ $id ]['font'] == $k ? 'selected' : '') . '>' . $v . '</option>';
        }

        $html .= '</select>
						<select class="cwp_select cwp_tipsy" original-title="Font style"  name="' . 'cwppos_options' . '[' . $id . '][style]" > ';
        foreach ( $style as $k => $v ) {

            $html .= "<option value='" . $k . "' " . ($this->options[ $id ]['style'] == $k ? 'selected' : '') . '>' . $v . '</option>';
        }

        $html .= '</select>
						<select class="cwp_select cwp_tipsy" original-title="Font size" " name="' . 'cwppos_options' . '[' . $id . '][size]" > ';
        foreach ( $sizes as $v ) {

            $html .= "<option value='" . $v . "' " . ($this->options[ $id ]['size'] == $v ? 'selected' : '') . '>' . $v . 'px</option>';
        }

        $html .= '</select>

				</div></div>';

        $this->tabs[ $tabid ]['elements'][] = array(
            'type' => 'typography',
            'html' => $html,
        );

    }

    public function add_textarea( $tabid, $name, $description, $id, $class = '' ) {

        $html = '
				<div class="controls ' . $class . '">
				<div class="explain">' . $name . '</div><p class="field_description">' . $description . '</p> <textarea class="cwp_textarea " placeholder="' . $name . '" name="' . 'cwppos_options' . '[' . $id . ']"    >' . $this->options[ $id ] . '</textarea></div>';

        $this->tabs[ $tabid ]['elements'][] = array(
            'type' => 'textarea',
            'html' => $html,
        );

    }

    public function add_restriction( $tabid, $name ) {

        $html = '
				<div class="controls ' . $class . '">
				<div class="explain">' . $name . '</div><p class="field_description">You need to have the capability to add HTML in order to use this feature !</p></div>';

        $this->tabs[ $tabid ]['elements'][] = array(
            'type' => 'textarea_html',
            'html' => $html,
        );

    }
    public function add_editor( $tabid, $name, $description, $id, $class = '' ) {
        ob_start();

        wp_editor( $this->options[ $id ], 'cwppos_options' . '[' . $id . ']' );

        $editor_contents = ob_get_clean();

        $html = '
				<div class="controls ' . $class . '">
				<div class="explain">' . $name . '</div><p class="field_description">' . $description . '</p>' . $editor_contents . '</div>';

        $this->tabs[ $tabid ]['elements'][] = array(
            'type' => 'editor',
            'html' => $html,
        );

    }
    public function add_input_number( $tabid, $name, $description, $id, $min = false, $max = false, $step = false, $class = '' ) {
        $html = '
				<div class="controls ' . $class . '">
				<div class="explain">' . $name . '</div><p class="field_description">' . $description . '</p>  <input placeholder="' . $name . '" type="number"  class="cwp_input" value="' . $this->options[ $id ] . '" name="' . 'cwppos_options' . '[' . $id . ']"
					' . ($min === false ? '' : ' min = "' . $min . '" ') . '
					' . ($max === false ? '' : ' max = "' . $max . '" ') . '
					' . ($step === false ? '' : ' step = "' . $step . '" ') . '
				> </div>';

        $this->tabs[ $tabid ]['elements'][] = array(
            'type' => 'input_number',
            'html' => $html,
        );

    }

    public function add_select( $tabid, $name, $description, $id, $options, $class = '' ) {

        $html = '
				<div class="controls ' . $class . '">
				<div class="explain">' . $name . '</div><p class="field_description">' . $description . '</p>';

        $html .= '<select class=" cwp_select" name="' . 'cwppos_options' . '[' . $id . ']" > ';

        foreach ( $options as $k => $v ) {

            $html .= "<option value='" . $k . "' " . ( ( isset( $this->options[ $id ] ) &&  $this->options[ $id ] == $k ) ? 'selected' : '') . '>' . $v . '</option>';
        }

        $html .= '</select></div>';

        $this->tabs[ $tabid ]['elements'][] = array(
            'type' => 'select',
            'html' => $html,
        );
    }
    public function add_multiselect( $tabid, $name, $description, $id, $options, $class = '' ) {

        $html = '
				<div class="controls ' . $class . '">
				<div class="explain">' . $name . '</div><p class="field_description">' . $description . '</p>   <select   name="' . 'cwppos_options' . '[' . $id . '][]" class="cwp_multiselect" multiple > ';
        foreach ( $options as $k => $v ) {

            $html .= "<option value='" . $k . "' " . (in_array( $k,$this->options[ $id ] ) ? 'selected' : '') . '>' . $v . '</option>';
        }

        $html .= '</select></div>';
        $this->tabs[ $tabid ]['elements'][] = array(
            'type' => 'multiselect',
            'html' => $html,
        );
    }
    public function add_checkbox( $tabid, $name, $description, $id, $options, $class = '' ) {

        $html = '
				<div class="controls ' . $class . '">
				<div class="explain">' . $name . '</div><p class="field_description">' . $description . '</p>  ';
        foreach ( $options as $k => $v ) {

            $html .= "<label class='cwp_label'><input class='cwp_checkbox' type=\"checkbox\" name='" . 'cwppos_options' . '[' . $id . "][]' value='" . $k . "' " . (in_array( $k,$this->options[ $id ] ) ? 'checked' : '') . ' >' . $v . '</label>';
        }

        $html .= '</div>';
        $this->tabs[ $tabid ]['elements'][] = array(
            'type' => 'checkbox',
            'html' => $html,
        );
    }



    public function add_radio( $tabid, $name, $description, $id, $options, $class = '' ) {

        $html = '
				<div class="controls ' . $class . '">
				<div class="explain">' . $name . '</div><p class="field_description">' . $description . '</p>  ';
        foreach ( $options as $k => $v ) {

            $html .= "<label class='cwp_label'><input class='cwp_radio' type=\"radio\" name='" . 'cwppos_options' . '[' . $id . "]' value='" . $k . "' " . ($this->options[ $id ] == $k ? 'checked' : '') . '>' . $v . '</label>';
        }

        $html .= '</div>';
        $this->tabs[ $tabid ]['elements'][] = array(
            'type' => 'radio',
            'html' => $html,
        );
    }



    public function add_image( $tabid, $name, $description, $id, $class = '' ) {
        $html = '
				<div class="controls ' . $class . '">
				<div class="explain">' . $name . '</div><p class="field_description">' . $description . '</p>
				<input type="hidden" id="' . $id . '" name="' . 'cwppos_options' . '[' . $id . ']" value="' . $this->options[ $id ] . '"/>
				<img src="' . $this->options[ $id ] . '" id="' . $id . '_image" class="image-preview-input"/><br/>
				<a id="' . $id . '_button" class="selector-image button"  >Select Image</a>
				<a id="' . $id . '_buttonclear" class="clear-image button"  >Clear image</a>

									</div>';

        $this->tabs[ $tabid ]['elements'][] = array(
            'type' => 'image',
            'html' => $html,
        );
    }

    public function add_button( $tabid, $name, $description, $id, $class = '' ) {
        $html = '
				<div class="controls ' . $class . ' ">
				<div class="explain">' . $name . '</div>
				<a href="https://themeisle.com/plugins/wp-product-review-pro-add-on/" class="button" style="color:red; text-decoration: none; ">' . $name . '</a>
				</div></div>';
        $this->tabs[ $tabid ]['elements'][] = array(
            'type' => 'button',
            'html' => $html,
        );
    }

    public function add_color( $tabid, $name, $description, $id, $class = '' ) {

        $html = '
				<div class="controls ' . $class . ' ">
				<div class="explain">' . $name . '</div><p class="field_description">' . $description . '</p>
				<input type="hidden" id="' . $id . '_color" name="' . 'cwppos_options' . '[' . $id . ']" value="' . $this->options[ $id ] . '"/> </br>
				<input type="text" name=""	class="subo-color-picker" id="' . $id . '_color_selector" value="' . $this->options[ $id ] . '" />				<br/>
									</div>';

        $this->tabs[ $tabid ]['elements'][] = array(
            'type' => 'color',
            'html' => $html,
        );
    }
    public function add_title( $tabid, $name ) {
        $html = '<h1 class="tab-title-area">' . $name . '</h1>';
        $this->tabs[ $tabid ]['elements'][] = array(
            'type' => 'title',
            'html' => $html,
        );

    }

    public function start_group( $tabid, $name ) {
        $html = '<div class="group-in-tab">
						<p class="group-name">' . $name . '</p>
						<div class="group-content">
				';
        $this->tabs[ $tabid ]['elements'][] = array(
            'type' => 'group_start',
            'html' => $html,
        );
    }
    public function end_group( $tabid ) {
        $html = '</div></div>
				';
        $this->tabs[ $tabid ]['elements'][] = array(
            'type' => 'end',
            'html' => $html,
        );
    }
}