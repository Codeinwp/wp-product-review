<?php

class cwpposConfig {

	public static $admin_page_menu_name;

	public static $admin_page_title;

	public static $admin_page_header;

	public static $admin_template_directory;

	public static $admin_template_directory_uri;

	public static $admin_uri;

	public static $admin_path;

	public static $menu_slug;

	public static $structure;

	public static $review_categories_array;

	public static $categories_array;

	public static $shortname;

	public static $all_review_categories_array;

	public static $all_categories_array;

	public static $categories_ids;

	public static $pro_page_menu_name;

	public static $pro_page_title;

	public static function init() {
		self::$admin_page_menu_name = 'Product Review';
		self::$admin_page_title = 'WP Product Review Options';
		self::$admin_page_header = 'WP Product Review Options';
		self::$shortname = 'cwppos';
		self::$admin_template_directory_uri = plugins_url( '../layout', __FILE__ );
		self::$admin_template_directory = plugins_url( '../layout', __FILE__ );
		self::$admin_uri = plugins_url( '../', __FILE__ );
		self::$admin_path = plugins_url( '../', __FILE__ );
		self::$menu_slug = 'cwppos_options';
		self::$all_categories_array = array();
		self::$all_review_categories_array = array();
		self::$categories_array = array();
		self::$pro_page_menu_name = 'More Features <span class="dashicons 
		dashicons-star-filled" 
		style="vertical-align:-5px; padding-left:2px; color:#FFCA54;"></span>';
		self::$pro_page_title = 'Go Premium';
		self::$structure = array(
			array(
				'type' => 'tab',
				'name' => 'General settings',
				'options' => array(
					array(
						'type' => 'select',
						'name' => 'Position of the review box',
						'description' => "You can choose manually and use : <?php echo cwppos_show_review('postid'); ?> or you can get the Product in post add-on and use :[P_REVIEW post_id=3067 visual='full']",
						'id' => 'cwppos_show_reviewbox',
						'options' => array(
		'yes'    => 'After content',
						                    'no'     => 'Before content',
						                    'manual' => 'Manually',
						),
						'default' => 'yes',
					),
					array(
						'type' => 'select',
						'name' => 'Show review comment',
						'description' => 'Activate comment review user',
						'id' => 'cwppos_show_userreview',
						'options' => array( 'yes' => 'Yes', 'no' => 'No' ),
						'default' => 'no',
					),
					array(
						'type' => 'select',
						'name' => 'Visitor Review Influence',
						'description' => 'Select how much visitors rating will affect the main one.',
						'id' => 'cwppos_infl_userreview',
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
						'default' => '0',
					),
					array(
						'type'        => 'change_icon',
						'name'        => 'Change Default Rating Icon',
						'description' => 'Choose which icon would you like to use for the rating bar.',
						'id'          => 'cwppos_change_bar_icon',
						'default'     => '',
					),
					array(
						'type' => 'select',
						'name' => 'Number of options/pros/cons',
						'description' => 'You can select the default number of options / pros/ cons (3-10)',
						'id' => 'cwppos_option_nr',
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
						'default' => 5,
					),
					array(
						'type' => 'input_text',
						'name' => 'Content width',
						'description' => 'Write your content width in pixels in this format : 600 if you want to limit the review box width.',
						'id' => 'cwppos_widget_size',
						'default' => '',
					),
					array(
						'type' => 'select',
						'name' => 'Disable Lighbox images',
						'description' => 'Disable lightbox effect on product images (increase loading speed)',
						'id'      => 'cwppos_lighbox',
						'options' => array( 'yes' => 'Yes', 'no' => 'No' ),
						'default' => 'no',
					),
					array(
						'type' => 'select',
						'name' => 'Disable Font Awesome',
						'description' => 'Disable Font Awesome for websites that already are including it (increase loading speed)',
						'id'      => 'cwppos_fontawesome',
						'options' => array( 'yes' => 'Yes', 'no' => 'No' ),
						'default' => 'no',
					),
				),
			),
			array(
				'type' => 'tab',
				'name' => 'Rating colors',
				'options' => array(
					array(
						'type' => 'title',
						'name' => 'Rating Colors',
					),
					array(
						'type' => 'color',
						'name' => 'Rating options default color',
						'description' => 'Select the color to be used by default on rating.',
						'id' => 'cwppos_rating_default',
						'default' => '#E1E2E0',
					),
					array(
						'type' => 'color',
						'name' => 'Rating chart default color',
						'description' => 'Select the color to be used by default on rating chart.',
						'id' => 'cwppos_rating_chart_default',
						'default' => '#ebebeb',
					),
					array(
						'type' => 'color',
						'name' => 'Weak rating',
						'description' => 'Select the color to be used when the rating is weak. ( < 2.5)',
						'id' => 'cwppos_rating_weak',
						'default' => '#FF7F66',
					),
					array(
						'type' => 'color',
						'name' => 'Not bad rating',
						'description' => 'Select the color to be used when the rating is not bad. ( > 2.5 and < 5)',
						'id' => 'cwppos_rating_notbad',
						'default' => '#FFCE55',
					),
					array(
						'type' => 'color',
						'name' => 'Good rating',
						'description' => 'Select the color to be used when the rating is good. ( >5 and <7.5)',
						'id' => 'cwppos_rating_good',
						'default' => '#50C1E9',
					),
					array(
						'type' => 'color',
						'name' => 'Very good rating',
						'description' => 'Select the color to be used when the rating is very good. ( 7.5 < and <10)',
						'id' => 'cwppos_rating_very_good',
						'default' => '#8DC153',
					),
				),
			),
			array(
				'type' => 'tab',
				'name' => 'Typography',
				'options' => array(
					array(
						'type' => 'title',
						'name' => 'Typography options',
					),
					array(
						'type' => 'color',
						'name' => 'Font color',
						'description' => 'Select the color to be used on the font.',
						'id' => 'cwppos_font_color',
						'default' => '#3D3D3D',
					),
					array(
						'type' => 'color',
						'name' => "'Pros' text color",
						'description' => "Select the color to be used on the 'Pros' text.",
						'id' => 'cwppos_pros_color',
						'default' => '#8DC153',
					),
					array(
						'type' => 'color',
						'name' => "'Cons' text color",
						'description' => "Select the color to be used on the 'Cons' text.",
						'id' => 'cwppos_cons_color',
						'default' => '#C15353',
					),
					array(
						'type' => 'input_text',
						'name' => 'Pros text',
						'description' => 'Specify text for pros heading',
						'id' => 'cwppos_pros_text',
						'default' => 'Pros',
					),
					array(
						'type' => 'input_text',
						'name' => 'Cons text',
						'description' => 'Specify text for cons heading',
						'id' => 'cwppos_cons_text',
						'default' => 'Cons',
					),
					array(
						'type'        => 'color',
						'name'        => 'Review box border',
						'description' => 'Select the border color to be used on the review box',
						'id'          => 'cwppos_reviewboxbd_color',
						'default'     => '#3BAEDA',
					),
					array(
						'type'        => 'input_text',
						'name'        => 'Review box border width',
						'description' => 'Select the width in pixels of the top border of the review box',
						'id'          => 'cwppos_reviewboxbd_width',
						'default'     => '5',
					),
				),
			),
			array(
				'type' => 'tab',
				'name' => 'Buy button',
				'options' => array(
					array(
						'type' => 'title',
						'name' => 'Buy button options',
					),
					array(
						'type' => 'select',
						'name' => 'Show button icon',
						'description' => 'Show icon on the cart icon on button.',
						'id' => 'cwppos_show_icon',
						'options' => array( 'yes' => 'Yes', 'no' => 'No' ),
						'default' => 'yes',
					),
					array(
						'type' => 'color',
						'name' => 'Button border',
						'description' => 'Select the border color to be used on the buy button for the default state',
						'id' => 'cwppos_buttonbd_color',
						'default' => '#3BAEDA',
					),
					array(
						'type' => 'color',
						'name' => 'Button border hover',
						'description' => 'Select the border color to be used on the buy button for the hover state',
						'id' => 'cwppos_buttonbh_color',
						'default' => '#3BAEDA',
					),
					array(
						'type' => 'color',
						'name' => 'Button background',
						'description' => 'Select the background color to be used on the buy button for the default state',
						'id' => 'cwppos_buttonbkd_color',
						'default' => '#ffffff',
					),
					array(
						'type' => 'color',
						'name' => 'Button background hover',
						'description' => 'Select the background color to be used on the buy button for the hover  state',
						'id' => 'cwppos_buttonbkh_color',
						'default' => '#3BAEDA',
					),
					array(
						'type' => 'color',
						'name' => 'Button text color',
						'description' => 'Select the text color to be used on the buy button for the default state',
						'id' => 'cwppos_buttontxtd_color',
						'default' => '#3BAEDA',
					),
					array(
						'type' => 'color',
						'name' => 'Button text color hover',
						'description' => 'Select the text color to be used on the buy button for the hover state',
						'id' => 'cwppos_buttontxth_color',
						'default' => '#FFFFFF',
					),
				),
			),
		);

	}

}

