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
	 * @var WPPR_Global_Settings The one WPPR_Global_Settings istance.
	 * @since 3.0.0
	 */
	public static $instance;
	/**
	 * @var array|mixed|void Options fields.
	 */
	public $fields = array();
	/**
	 * @var array|mixed|void Sections of the admin page.
	 */
	public $sections = array();

	public static function instance() {
		if ( ! isset( self::$instance ) && ! ( self::$instance instanceof WPPR_Global_Settings ) ) {
			self::$instance           = new WPPR_Global_Settings;
			self::$instance->sections = apply_filters( 'wppr_settings_sections', array(
				'general'    => __( 'General settings', 'wp-product-review' ),
				'rating'     => __( 'Rating colors', 'wp-product-review' ),
				'typography' => __( 'Typography', 'wp-product-review' ),
				'buy'        => __( 'Buy button', 'wp-product-review' ),
			) );
			self::$instance->fields   = apply_filters( 'wppr_settings_fields', array(
					'general'    => array(
						'cwppos_show_reviewbox'  => array(
							'id'      => 'review_position',
							'name'    => __( 'Position of the review box', 'wp-product-review' ),
							'desc'    => __( 'You can choose manually and use : <?php echo cwppos_show_review(\'postid\'); ?> or you can get the Product in post add-on and use :[P_REVIEW post_id=3067 visual=\'full\']', 'wp-product-review' ),
							'type'    => 'select',
							'options' => array(
								'yes'    => __( 'Before content', 'wp-product-review' ),
								'no'     => __( 'After content', 'wp-product-review' ),
								'manual' => __( 'Manually placed', 'wp-product-review' ),
							),
						),
						'cwppos_show_userreview' => array(
							'id'      => 'show_review',
							'name'    => __( 'Show review comment', 'wp-product-review' ),
							'desc'    => __( 'Activate comment review user', 'wp-product-review' ),
							'type'    => 'select',
							'options' => array(
								'yes' => __( 'Yes', 'wp-product-review' ),
								'no'  => __( 'No', 'wp-product-review' ),
							),
						),
						'cwppos_infl_userreview' => array(
							'id'      => 'comment_influence',
							'name'    => __( 'Visitor Review Influence', 'wp-product-review' ),
							'desc'    => __( 'Select how much visitors rating will affect the main one', 'wp-product-review' ),
							'type'    => 'select',
							'options' => array(
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
						),
						'cwppos_option_nr'       => array(
							'id'      => 'options_no',
							'name'    => __( 'Number of options/pros/cons', 'wp-product-review' ),
							'desc'    => __( 'You can select the default number of options / pros/ cons (3-10)', 'wp-product-review' ),
							'type'    => 'select',
							'options' => array(
								3  => '3',
								4  => '4',
								5  => '5',
								6  => '6',
								7  => '7',
								8  => '8',
								9  => '9',
								10 => '10',
							),
						),
						'cwppos_widget_size'     => array(
							'type'        => 'input_text',
							'name'        => __( 'Content width', 'wp-product-review' ),
							'description' => __( 'Write your content width in pixels in this format : 600 if you want to limit the review box width.', 'wp-product-review' ),
							'id'          => 'widget_size',
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
						),
					),
					'rating'     => array(
						'cwppos_rating_default'       => array(
							'type'        => 'color',
							'name'        => __( 'Rating options default color', 'wp-product-review' ),
							'description' => __( 'Select the color to be used by default on rating.', 'wp-product-review' ),
							'id'          => 'default_color',
						),
						'cwppos_rating_chart_default' => array(
							'type'        => 'color',
							'name'        => __( 'Rating chart default color', 'wp-product-review' ),
							'description' => __( 'Select the color to be used by default on rating chart.', 'wp-product-review' ),
							'id'          => 'chart_color',
						),
						'cwppos_rating_weak'          => array(
							'type'        => 'select',
							'name'        => __( 'Weak rating', 'wp-product-review' ),
							'description' => __( 'Select the color to be used when the rating is weak. ( < 2.5)', 'wp-product-review' ),
							'id'          => 'weak_color',
						),
						'cwppos_rating_notbad'        => array(
							'type'        => 'select',
							'name'        => __( 'Not bad rating', 'wp-product-review' ),
							'description' => __( 'Select the color to be used when the rating is not bad. ( > 2.5 and < 5)
', 'wp-product-review' ),
							'id'          => 'notbad_color',
						),
						'cwppos_rating_very_good'     => array(
							'type'        => 'select',
							'name'        => __( 'Good rating', 'wp-product-review' ),
							'description' => __( 'Select the color to be used when the rating is good. ( >5 and <7.5)', 'wp-product-review' ),
							'id'          => 'good_color',
						),
						'cwppos_fontawesome'          => array(
							'type'        => 'select',
							'name'        => __( 'Very good rating', 'wp-product-review' ),
							'description' => __( 'Select the color to be used when the rating is very good. ( 7.5 < and <10)', 'wp-product-review' ),
							'id'          => 'verygood_color',
						),

					),
					'typography' => array(
						'cwppos_font_color'        => array(
							'type'        => 'color',
							'name'        => __( 'Font color', 'wp-product-review' ),
							'description' => __( 'Select the color to be used on the font.', 'wp-product-review' ),
							'id'          => 'font_color',
						),
						'cwppos_pros_color'        => array(
							'type'        => 'color',
							'name'        => __( 'Pros text color', 'wp-product-review' ),
							'description' => __( 'Select the color to be used on the \'Pros\' text.', 'wp-product-review' ),
							'id'          => 'pros_color',
						),
						'cwppos_cons_color'        => array(
							'type'        => 'color',
							'name'        => __( 'Cons text color', 'wp-product-review' ),
							'description' => __( 'Select the color to be used on the Cons text.', 'wp-product-review' ),
							'id'          => 'cons_color',
						),
						'cwppos_pros_text'         => array(
							'type'        => 'text',
							'name'        => __( 'Pros text', 'wp-product-review' ),
							'description' => __( 'Specify text for pros heading', 'wp-product-review' ),
							'id'          => 'pros_text',
						),
						'cwppos_cons_text'         => array(
							'type'        => 'text',
							'name'        => __( 'Cons text', 'wp-product-review' ),
							'description' => __( 'Specify text for cons heading', 'wp-product-review' ),
							'id'          => 'cons_text',
						),
						'cwppos_reviewboxbd_color' => array(
							'type'        => 'color',
							'name'        => __( 'Review box border', 'wp-product-review' ),
							'description' => __( 'Select the border color to be used on the review box', 'wp-product-review' ),
						),
						'cwppos_reviewboxbd_width' => array(
							'type'        => 'text',
							'name'        => __( 'Review box border width', 'wp-product-review' ),
							'description' => __( 'Select the width in pixels of the top border of the review box', 'wp-product-review' ),
							'id'          => 'review_box_border_width',
						),
					),
					'buy'        => array(
						'cwppos_show_icon'        => array(
							'type'        => 'select',
							'name'        => __( 'Show button icon', 'wp-product-review' ),
							'description' => __( 'Show icon on the cart icon on button.', 'wp-product-review' ),
							'id'          => 'show_cart_icon',
							'options'     => array(
								'yes' => 'Yes',
								'no'  => 'No',
							),
						),
						'cwppos_buttonbd_color'   => array(
							'type'        => 'color',
							'name'        => __( 'Button border', 'wp-product-review' ),
							'description' => __( 'Select the border color to be used on the buy button for the default state', 'wp-product-review' ),
							'id'          => 'button_border_color',
						),
						'cwppos_buttonbh_color'   => array(
							'type'        => 'color',
							'name'        => __( 'Button border hover', 'wp-product-review' ),
							'description' => __( 'Select the border color to be used on the buy button for the hover state', 'wp-product-review' ),
							'id'          => 'button_buy_border_hcolor',
						),
						'cwppos_buttonbkd_color'  => array(
							'type'        => 'color',
							'name'        => __( 'Button background', 'wp-product-review' ),
							'description' => __( 'Select the background color to be used on the buy button for the default state', 'wp-product-review' ),
							'id'          => 'button_buy_bg_hcolor',
						),
						'cwppos_buttonbkh_color'  => array(
							'type'        => 'color',
							'name'        => __( 'Button background hover', 'wp-product-review' ),
							'description' => __( 'Select the background color to be used on the buy button for the hover  state', 'wp-product-review' ),
							'id'          => 'button_buy_bgh_hcolor',
						),
						'cwppos_buttontxtd_color' => array(
							'type'        => 'color',
							'name'        => __( 'Button text color', 'wp-product-review' ),
							'description' => __( 'Select the text color to be used on the buy button for the default state', 'wp-product-review' ),
							'id'          => 'button_buy_txt_hcolor',
						),
						'cwppos_buttontxth_color' => array(
							'type'        => 'color',
							'name'        => __( 'Button text color hover', 'wp-product-review' ),
							'description' => __( 'Select the text color to be used on the buy button for the hover state', 'wp-product-review' ),
							'id'          => 'button_buy_txth_hcolor',
						),
					),
				)
			);

		}// End if().

		return self::$instance;
	}

	/**
	 * Return the section array.
	 *
	 * @return array The sections array.
	 */
	public function get_sections() {
		return self::$instance->sections;
	}

	/**
	 * Return the fields array.
	 *
	 * @return array The fields array.
	 */
	public function get_fields() {
		return self::$instance->fields;
	}
}
