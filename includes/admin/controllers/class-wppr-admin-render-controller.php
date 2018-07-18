<?php
/**
 * WPPR Admin Render Controller
 *
 * @package     WPPR
 * @subpackage  Admin
 * @copyright   Copyright (c) 2017, Bogdan Preda
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       3.0.0
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
	 * Stores the helper class to render elements.
	 *
	 * @since   3.0.0
	 * @access  private
	 * @var WPPR_Html_Fields $html_helper The HTML helper class.
	 */
	private $html_helper;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since   3.0.0
	 * @param   string $plugin_name The name of this plugin.
	 * @param   string $version     The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {
		$this->plugin_name = $plugin_name;
		$this->version     = $version;
		$this->html_helper = new WPPR_Html_Fields();
	}

	/**
	 * Utility method to include required layout.
	 *
	 * @since   3.0.0
	 * @access  public
	 * @param   string                   $name   The name of the layout to be retrieved.
	 * @param   bool|WPPR_Abstract_Model $model Optional pass a model to use in template.
	 */
	public function retrive_template( $name, $model = false ) {
		/*
			Let's check for user templates inside the wppr folder in the theme.
			We expect the following files
				/wppr/<name>.php
				/wppr/<name>.css
				/wppr/<name>.png
		*/
		if ( file_exists( get_stylesheet_directory() . '/wppr' ) ) {
			if ( file_exists( get_stylesheet_directory() . '/wppr/' . $name . '.css' ) ) {
				wp_enqueue_style( $this->plugin_name . '-' . $name . '-custom', get_stylesheet_directory_uri() . '/wppr/' . $name . '.css', array(), $this->version );
			}
			if ( file_exists( get_stylesheet_directory() . '/wppr/' . $name . '.php' ) ) {
				include_once( get_stylesheet_directory() . '/wppr/' . $name . '.php' );
				return;
			}
		}

		if ( file_exists( WPPR_PATH . '/includes/admin/layouts/css/' . $name . '.css' ) ) {
			wp_enqueue_style( $this->plugin_name . '-' . $name . '-css', WPPR_URL . '/includes/admin/layouts/css/' . $name . '.css', array(), $this->version );
		}
		include_once( WPPR_PATH . '/includes/admin/layouts/' . $name . '-tpl.php' );
	}

	/**
	 * Render editor metabox.
	 *
	 * @since   3.0.0
	 * @access  public
	 * @param   string                   $template Path to template file or template name.
	 * @param   bool|WPPR_Abstract_Model $model Optional pass a model to use in template.
	 */
	public function render_editor_metabox( $template, $model = false ) {
		if ( ! file_exists( $template ) ) {
			$template = WPPR_PATH . '/includes/admin/layouts/' . $template . '-tpl.php';
		}
		$html_helper = new WPPR_Html_Fields();
		include_once( $template );
	}

	/**
	 * Method to controll what element is rendered based on type.
	 *
	 * @since   3.0.0
	 * @access  public
	 * @param   array $field  The array to use when rendering.
	 * @return mixed
	 */
	public function add_element( $field ) {
		$output = '
            <div class="controls">
				<div class="explain"><h4>' . $field['title'] . '</h4></div>
				<div class="controls-content">
        ';
		switch ( $field['type'] ) {
			case 'input_text':
				$output .= $this->html_helper->text( $field );
				break;
			case 'select':
				$output .= $this->html_helper->select( $field );
				break;
			case 'color':
				$output .= $this->html_helper->color( $field );
				break;
			case 'text':
				$output .= $this->html_helper->text( $field );
				break;
			case 'icon_font':
				$output .= $this->html_helper->icon_font( $field );
				break;
		}

		$output .= '<p class="field_description">' . $field['description'] . '</p></div></div><hr/>';
		echo $output;

		if ( isset( $errors ) ) {
			return $errors; }
	}
}
