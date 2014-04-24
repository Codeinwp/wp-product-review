<?php
	class cwpposConfig{
		public static $admin_page_menu_name ;
		public static  $admin_page_title 	;
		public static  $admin_page_header 	;
		public static  $admin_template_directory ;
		public static  $admin_template_directory_uri ;
		public static  $admin_uri 	;
		public static $admin_path  ;
		public static  $menu_slug 	;
		public static $structure;
		public static $review_categories_array;
		public static $categories_array;
		public static $shortname;
		public static $all_review_categories_array;
		public static $all_categories_array;
		public static $categories_ids;

		public static function init(){
			self::$admin_page_menu_name 	 = "WP Product Review Options";
			self::$admin_page_title 		 = "WP Product Review Options";
			self::$admin_page_header	 	 = "WP Product Review Options";
			self::$shortname 			     = "cwppos";
			self::$admin_template_directory_uri  =  plugins_url (  'wp-product-review/admin/layout' );
			self::$admin_template_directory  =  plugins_url ('wp-product-review/admin/layout' );
			self::$admin_uri  		= 	 plugins_url (   'wp-product-review/admin/' ); 
			self::$admin_path 	 	= 	 plugins_url ( 'wp-product-review/admin/');
			self::$menu_slug  		= 	"cwppos_options";
			self::$all_categories_array = array();
			self::$all_review_categories_array = array();
			self::$categories_array = array(); 
 
			self::$structure	= array(
						array(
							 "type"=>"tab",
							 "name"=>"Rating colors",
							 "options"=>array(
								
								array(
									"type"=>"title",
									"name"=>"Rating Colors"
								) ,
 
								array(	
									"type"=>"color",
									"name"=>"Rating default color",
									"description"=>"Select the color to be used by default on rating.",
									"id"=>"cwppos_rating_default",
									"default"=>"#E1E2E0"
								),
								array(	
									"type"=>"color",
									"name"=>"Weak rating",
									"description"=>"Select the color to be used when the rating is weak. ( < 2.5)",
									"id"=>"cwppos_rating_weak",
									"default"=>"#FF7F66"
								),
 
								array(	
									"type"=>"color",
									"name"=>"Not bad rating",
									"description"=>"Select the color to be used when the rating is not bad. ( > 2.5 and < 5)",
									"id"=>"cwppos_rating_notbad",
									"default"=>"#FFCE55"
								),
 
								array(	
									"type"=>"color",
									"name"=>"Good rating",
									"description"=>"Select the color to be used when the rating is good. ( >5 and <7.5)",
									"id"=>"cwppos_rating_good",
									"default"=>"#50C1E9"
								),
 
								array(	
									"type"=>"color",
									"name"=>"Very good rating",
									"description"=>"Select the color to be used when the rating is very good. ( 7.5 < and <10)",
									"id"=>"cwppos_rating_very_good",
									"default"=>"#8DC153"
								)
							
							 )
						) ,
						array(
							 "type"=>"tab",
							 "name"=>"Typography",
							 "options"=>array(
								
								array(
									"type"=>"title",
									"name"=>"Typography options"
								) ,
 
								array(	
									"type"=>"color",
									"name"=>"Font color",
									"description"=>"Select the color to be used on the font.",
									"id"=>"cwppos_font_color",
									"default"=>"#3D3D3D"
								),
								array(	
									"type"=>"color",
									"name"=>"'Pros' text color",
									"description"=>"Select the color to be used on the 'Pros' text.",
									"id"=>"cwppos_pros_color",
									"default"=>"#8DC153"
								),
								array(	
									"type"=>"color",
									"name"=>"'Cons' text color",
									"description"=>"Select the color to be used on the 'Cons' text.",
									"id"=>"cwppos_cons_color",
									"default"=>"#C15353"
								),
  
 
								
								array(
								 "type"=>"input_text",
								 "name"=>"Pros text",							 
								 "description"=>"Specify text for pros heading",
								 "id"=>"cwppos_pros_text",
								 "default"=>"Pros"
							   ),
 
								
								array(
								 "type"=>"input_text",
								 "name"=>"Cons text",							 
								 "description"=>"Specify text for cons heading",
								 "id"=>"cwppos_cons_text",
								 "default"=>"Cons"
							   ) 
							
							 )
						) ,
						array(
							 "type"=>"tab",
							 "name"=>"Buy button",
							 "options"=>array(
								
								array(
									"type"=>"title",
									"name"=>"Buy button options"
								) ,
								array(
									"type"=>"select",
									"name"=>"Show button icon",
									"description"=>"Show icon on the cart icon on button.",
									"id"=>"cwppos_show_icon",
									"options"=>array("yes"=>"Yes","no"=>"No"),
									"default"=>"yes"
								),
								array(	
									"type"=>"color",
									"name"=>"Button border",
									"description"=>"Select the border color to be used on the buy button for the default state",
									"id"=>"cwppos_buttonbd_color",
									"default"=>"#3BAEDA"
								),
								array(	
									"type"=>"color",
									"name"=>"Button border hover",
									"description"=>"Select the border color to be used on the buy button for the hover state",
									"id"=>"cwppos_buttonbh_color",
									"default"=>"#3BAEDA"
								),
								array(	
									"type"=>"color",
									"name"=>"Button background",
									"description"=>"Select the background color to be used on the buy button for the default state",
									"id"=>"cwppos_buttonbkd_color",
									"default"=>"#ffffff"
								),
								array(	
									"type"=>"color",
									"name"=>"Button background hover",
									"description"=>"Select the background color to be used on the buy button for the hover  state",
									"id"=>"cwppos_buttonbkh_color",
									"default"=>"#3BAEDA"
								),
								array(	
									"type"=>"color",
									"name"=>"Button text color",
									"description"=>"Select the text color to be used on the buy button for the default state",
									"id"=>"cwppos_buttontxtd_color",
									"default"=>"#3BAEDA"
								),
								array(	
									"type"=>"color",
									"name"=>"Button text color hover",
									"description"=>"Select the text color to be used on the buy button for the hover state",
									"id"=>"cwppos_buttontxth_color",
									"default"=>"#FFFFFF"
								) 
							
							 )
						) ,
						array(
							 "type"=>"tab",
							 "name"=>"General settings",
							 "options"=>array(
								
								array(
									"type"=>"title",
									"name"=>"General settings"
								) , 
								array(
									"type"=>"select",
									"name"=>"Show review comment",
									"description"=>"Activate comment review user",
									"id"=>"cwppos_show_userreview",
									"options"=>array("yes"=>"Yes","no"=>"No"),
									"default"=>"yes"
								), 
								array(
									"type"=>"select",
									"name"=>"Review position",
									"description"=>"Position of the review box",
									"id"=>"cwppos_show_reviewbox",
									"options"=>array("yes"=>"After content","no"=>"Before content"),
									"default"=>"yes"
								)
							
							 )
						) 

		
					);


			 
		}	 
	
	} 
