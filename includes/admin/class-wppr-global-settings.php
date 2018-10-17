<?php
/**
 * The main loader file for wppr.
 *
 * @package WPPR
 * @subpackage Settings
 * @copyright   Copyright (c) 2017, Marius Cristea
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since 3.0.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class WPPR_Global_Settings for handling global options.
 */
class WPPR_Global_Settings {
	/**
	 * The main instance var.
	 *
	 * @since   3.0.0
	 * @access  public
	 * @var WPPR_Global_Settings The one WPPR_Global_Settings istance.
	 */
	public static $instance;

	/**
	 * Stores the default fields data.
	 *
	 * @since   3.0.0
	 * @access  public
	 * @var array|mixed|void $fields Options fields.
	 */
	public $fields = array();

	/**
	 * Stores the sections for the settings page.
	 *
	 * @since   3.0.0
	 * @access  public
	 * @var array|mixed|void $sections Sections of the admin page.
	 */
	public $sections = array();

	/**
	 * The instance method for the static class.
	 * Defines and returns the instance of the static class.
	 *
	 * @since   3.0.0
	 * @access  public
	 * @return WPPR_Global_Settings
	 */
	public static function instance() {
		if ( ! isset( self::$instance ) && ! ( self::$instance instanceof WPPR_Global_Settings ) ) {
			self::$instance           = new WPPR_Global_Settings;
			self::$instance->sections = apply_filters(
				'wppr_settings_sections',
				array(
					'general'    => __( 'General settings', 'wp-product-review' ),
					'rating'     => __( 'Rating colors', 'wp-product-review' ),
					'typography' => __( 'Typography', 'wp-product-review' ),
					'buy'        => __( 'Buy button', 'wp-product-review' ),
				)
			);
			self::$instance->fields   = apply_filters(
				'wppr_settings_fields',
				array(
					'general'    => array(
						'cwppos_show_reviewbox'  => array(
							'id'          => 'review_position',
							'name'        => __( 'Position of the review box', 'wp-product-review' ),
							'description' => '',
							'type'        => 'select',
							'options'     => array(
								'yes'    => __( 'After content', 'wp-product-review' ),
								'no'     => __( 'Before content', 'wp-product-review' ),
								'manual' => __( 'Manually placed', 'wp-product-review' ),
							),
							'default'     => 'yes',
						),
						'cwppos_show_userreview' => array(
							'id'          => 'show_review',
							'name'        => __( 'Show review comment', 'wp-product-review' ),
							'description' => self::enable_user_comments() ? __( 'Activate comment review user', 'wp-product-review' ) : self::disable_user_comments_msg(),
							'type'        => 'select',
							'options'     => array(
								'yes' => __( 'Yes', 'wp-product-review' ),
								'no'  => __( 'No', 'wp-product-review' ),
							),
							'disabled'    => ! self::enable_user_comments(),
							'default'     => 'no',
						),
						'cwppos_infl_userreview' => array(
							'id'          => 'comment_influence',
							'name'        => __( 'Visitor Review Influence', 'wp-product-review' ),
							'description' => __( 'Select how much visitors rating will affect the main one', 'wp-product-review' ),
							'type'        => 'select',
							'options'     => array(
								'0'   => 'No influence',
								'10'  => '10%',
								'20'  => '20%',
								'30'  => '30%',
								'40'  => '40%',
								'50'  => '50%',
								'60'  => '60%',
								'70'  => '70%',
								'80'  => '80%',
								'90'  => '90%',
								'100' => '100%',
							),
							'default'     => '0',
						),
						'cwppos_option_nr'       => array(
							'id'          => 'options_no',
							'name'        => __( 'Number of options/pros/cons', 'wp-product-review' ),
							'description' => __( 'You can select the default number of options / pros/ cons (3-10)', 'wp-product-review' ),
							'type'        => 'select',
							'options'     => array(
								'3'  => '3',
								'4'  => '4',
								'5'  => '5',
								'6'  => '6',
								'7'  => '7',
								'8'  => '8',
								'9'  => '9',
								'10' => '10',
							),
							'default'     => '5',
						),
						'cwppos_widget_size'     => array(
							'type'        => 'input_text',
							'name'        => __( 'Content width', 'wp-product-review' ),
							'description' => __( 'Write your content width in pixels in this format : 600 if you want to limit the review box width.', 'wp-product-review' ),
							'id'          => 'widget_size',
							'default'     => '',
						),
						'cwppos_lighbox'         => array(
							'type'        => 'select',
							'name'        => __( 'Disable Lighbox images', 'wp-product-review' ),
							'description' => __( 'Disable lightbox effect on product images (increase loading speed)', 'wp-product-review' ),
							'id'          => 'use_lightbox',
							'options'     => array(
								'yes' => __( 'Yes', 'wp-product-review' ),
								'no'  => __( 'No', 'wp-product-review' ),
							),
							'default'     => 'no',
						),
						'cwppos_fontawesome'     => array(
							'type'        => 'select',
							'name'        => __( 'Disable Font Awesome', 'wp-product-review' ),
							'description' => __( 'Disable Font Awesome for websites that already are including it (increase loading speed)', 'wp-product-review' ),
							'id'          => 'use_fontawesome',
							'options'     => array(
								'yes' => __( 'Yes', 'wp-product-review' ),
								'no'  => __( 'No', 'wp-product-review' ),
							),
							'default'     => 'no',
						),
						'wppr_rich_snippet'      => array(
							'type'        => 'select',
							'name'        => __( 'Enable Rich Snippets', 'wp-product-review' ),
							'description' => __( 'Enable rich snippets on the product page.', 'wp-product-review' ),
							'id'          => 'use_rich_snippet',
							'options'     => array(
								'yes' => __( 'Yes', 'wp-product-review' ),
								'no'  => __( 'No', 'wp-product-review' ),
							),
							'default'     => 'yes',
						),
						'wppr_cpt'      => array(
							'type'        => 'select',
							'name'        => __( 'Enable review post type', 'wp-product-review' ),
							'description' => __( 'Enabling this will create a new post type where you can manage your reviews. Refresh the page on saving changes to see the "Reviews" menu on the left.', 'wp-product-review' ),
							'id'          => 'use_cpt',
							'options'     => array(
								'yes' => __( 'Yes', 'wp-product-review' ),
								'no'  => __( 'No', 'wp-product-review' ),
							),
							'default'     => 'no',
						),
						'wppr_amp'      => array(
							'type'        => 'select',
							'name'        => __( 'Enable AMP CSS.', 'wp-product-review' ),
							'description' => __( 'Enabling this will result in including custom CSS for AMP.', 'wp-product-review' ),
							'id'          => 'wppr_amp',
							'options'     => array(
								'yes' => __( 'Yes', 'wp-product-review' ),
								'no'  => __( 'No', 'wp-product-review' ),
							),
							'default'     => 'yes',
						),
					),
					'rating'     => array(
						'cwppos_rating_default'       => array(
							'type'        => 'color',
							'name'        => __( 'Rating options default color', 'wp-product-review' ),
							'description' => __( 'Select the color to be used by default on rating.', 'wp-product-review' ),
							'id'          => 'rating_default',
							'default'     => '#E1E2E0',
						),
						'cwppos_rating_chart_default' => array(
							'type'        => 'color',
							'name'        => __( 'Rating chart default color', 'wp-product-review' ),
							'description' => __( 'Select the color to be used by default on rating chart.', 'wp-product-review' ),
							'id'          => 'rating_chart_default',
							'default'     => '#ebebeb',
						),
						'cwppos_rating_weak'          => array(
							'type'        => 'color',
							'name'        => __( 'Weak rating', 'wp-product-review' ),
							'description' => __( 'Select the color to be used when the rating is weak. ( < 2.5)', 'wp-product-review' ),
							'id'          => 'rating_weak',
							'default'     => '#FF7F66',
						),
						'cwppos_rating_notbad'        => array(
							'type'        => 'color',
							'name'        => __( 'Not bad rating', 'wp-product-review' ),
							'description' => __( 'Select the color to be used when the rating is not bad. ( > 2.5 and < 5)', 'wp-product-review' ),
							'id'          => 'rating_notbad',
							'default'     => '#FFCE55',
						),
						'cwppos_rating_good'          => array(
							'type'        => 'color',
							'name'        => __( 'Good rating', 'wp-product-review' ),
							'description' => __( 'Select the color to be used when the rating is good. ( >5 and <7.5)', 'wp-product-review' ),
							'id'          => 'rating_good',
							'default'     => '#50C1E9',
						),
						'cwppos_rating_very_good'     => array(
							'type'        => 'color',
							'name'        => __( 'Very good rating', 'wp-product-review' ),
							'description' => __( 'Select the color to be used when the rating is very good. ( 7.5 < and <10)', 'wp-product-review' ),
							'id'          => 'rating_very_good',
							'default'     => '#8DC153',
						),

					),
					'typography' => array(
						'cwppos_font_color'        => array(
							'type'        => 'color',
							'name'        => __( 'Font color', 'wp-product-review' ),
							'description' => __( 'Select the color to be used on the font.', 'wp-product-review' ),
							'id'          => 'font_color',
							'default'     => '#3D3D3D',
						),
						'cwppos_pros_color'        => array(
							'type'        => 'color',
							'name'        => __( 'Pros text color', 'wp-product-review' ),
							'description' => __( 'Select the color to be used on the \'Pros\' text.', 'wp-product-review' ),
							'id'          => 'pros_color',
							'default'     => '#8DC153',
						),
						'cwppos_cons_color'        => array(
							'type'        => 'color',
							'name'        => __( 'Cons text color', 'wp-product-review' ),
							'description' => __( 'Select the color to be used on the Cons text.', 'wp-product-review' ),
							'id'          => 'cons_color',
							'default'     => '#C15353',
						),
						'cwppos_pros_text'         => array(
							'type'        => 'text',
							'name'        => __( 'Pros text', 'wp-product-review' ),
							'description' => __( 'Specify text for pros heading', 'wp-product-review' ),
							'id'          => 'pros_text',
							'default'     => 'Pros',
						),
						'cwppos_cons_text'         => array(
							'type'        => 'text',
							'name'        => __( 'Cons text', 'wp-product-review' ),
							'description' => __( 'Specify text for cons heading', 'wp-product-review' ),
							'id'          => 'cons_text',
							'default'     => 'Cons',
						),
						'cwppos_reviewboxbd_color' => array(
							'type'        => 'color',
							'name'        => __( 'Review box border', 'wp-product-review' ),
							'description' => __( 'Select the border color to be used on the review box', 'wp-product-review' ),
							'id'          => 'reviewboxbd_color',
							'default'     => '#3BAEDA',
						),
						'cwppos_reviewboxbd_width' => array(
							'type'        => 'input_text',
							'name'        => __( 'Review box border width', 'wp-product-review' ),
							'description' => __( 'Select the width in pixels of the top border of the review box', 'wp-product-review' ),
							'id'          => 'review_box_border_width',
							'default'     => '5',
						),
					),
					'buy'        => array(
						'cwppos_show_icon'        => array(
							'type'        => 'select',
							'name'        => __( 'Show button icon', 'wp-product-review' ),
							'description' => __( 'Show icon on the cart icon on button.', 'wp-product-review' ),
							'id'          => 'show_icon',
							'options'     => array(
								'yes' => 'Yes',
								'no'  => 'No',
							),
							'default'     => 'yes',
						),
						'cwppos_buttonbd_color'   => array(
							'type'        => 'color',
							'name'        => __( 'Button border', 'wp-product-review' ),
							'description' => __( 'Select the border color to be used on the buy button for the default state', 'wp-product-review' ),
							'id'          => 'buttonbd_color',
							'default'     => '#3BAEDA',
						),
						'cwppos_buttonbh_color'   => array(
							'type'        => 'color',
							'name'        => __( 'Button border hover', 'wp-product-review' ),
							'description' => __( 'Select the border color to be used on the buy button for the hover state', 'wp-product-review' ),
							'id'          => 'buttonbh_color',
							'default'     => '#3BAEDA',
						),
						'cwppos_buttonbkd_color'  => array(
							'type'        => 'color',
							'name'        => __( 'Button background', 'wp-product-review' ),
							'description' => __( 'Select the background color to be used on the buy button for the default state', 'wp-product-review' ),
							'id'          => 'buttonbkd_color',
							'default'     => '#ffffff',
						),
						'cwppos_buttonbkh_color'  => array(
							'type'        => 'color',
							'name'        => __( 'Button background hover', 'wp-product-review' ),
							'description' => __( 'Select the background color to be used on the buy button for the hover  state', 'wp-product-review' ),
							'id'          => 'buttonbkh_color',
							'default'     => '#3BAEDA',
						),
						'cwppos_buttontxtd_color' => array(
							'type'        => 'color',
							'name'        => __( 'Button text color', 'wp-product-review' ),
							'description' => __( 'Select the text color to be used on the buy button for the default state', 'wp-product-review' ),
							'id'          => 'buttontxtd_color',
							'default'     => '#3BAEDA',
						),
						'cwppos_buttontxth_color' => array(
							'type'        => 'color',
							'name'        => __( 'Button text color hover', 'wp-product-review' ),
							'description' => __( 'Select the text color to be used on the buy button for the hover state', 'wp-product-review' ),
							'id'          => 'buttontxth_color',
							'default'     => '#FFFFFF',
						),
					),
				)
			);

		}// End if().

		return self::$instance;
	}

	/**
	 * When Disqus or Jetpack Comments are enabled, the user review doesn't work.
	 */
	private static function enable_user_comments() {
		if ( ! is_admin() ) {
			return true;
		}
		if ( function_exists( 'is_plugin_active' ) && is_plugin_active( 'disqus-comment-system/disqus.php' ) || ( class_exists( 'Jetpack' ) && Jetpack::is_module_active( 'comments' ) ) ) {
			return false;
		}

		return true;
	}

	/**
	 * When Disqus or Jetpack Comments are enabled, show this to the user.
	 */
	private static function disable_user_comments_msg() {
		if ( ! is_admin() ) {
			return true;
		}
		$active = array();
		if ( function_exists( 'is_plugin_active' ) && is_plugin_active( 'disqus-comment-system/disqus.php' ) ) {
			$active[] = 'Disqus';
		}
		if ( class_exists( 'Jetpack' ) && Jetpack::is_module_active( 'comments' ) ) {
			$active[] = 'Jetpack Comments';
		}
		if ( $active ) {
			return sprintf( __( 'We see %s active, so user feedback is disabled', 'wp-product-review' ), implode( ',', $active ) );
		}

		return '';
	}

	/**
	 * Return the section array.
	 *
	 * @since   3.0.0
	 * @access  public
	 * @return array
	 */
	public function get_sections() {
		return self::$instance->sections;
	}

	/**
	 * Return the fields array.
	 *
	 * @since   3.0.0
	 * @access  public
	 * @return array
	 */
	public function get_fields() {
		return self::$instance->fields;
	}

	/**
	 * Return a filterd array based on sections value.
	 *
	 * @since   3.0.0
	 * @access  public
	 * @return array
	 */
	public function get_filtered_fields() {
		$fields = array();
		foreach ( self::$instance->sections as $key => $value ) {
			foreach ( self::$instance->fields[ $key ] as $field_key => $field_values ) {
				$fields[ $field_key ] = $field_values;
			}
		}

		return $fields;
	}
}
