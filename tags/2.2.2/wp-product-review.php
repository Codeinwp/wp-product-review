<?php

/*

Plugin Name: WP Product Review 

Description: The highest rated and most complete review plugin, now with rich snippets support. Easily turn your basic posts into in-depth reviews with ratings, pros and cons and affiliate links .

Version: 2.2.2

Author: CodeInWP

Author URI:  http://codeinwp.com/

Requires at least: 3.5

Tested up to: 3.9

Stable tag: trunk

License: GPLv2 or later

License URI: http://www.gnu.org/licenses/gpl-2.0.html

*/
if (wp_get_theme() !== "Reviewgine Affiliate PRO") {
include "admin/functions.php";

include "inc/cwp_metabox.php";

include "inc/cwp_frontpage.php";

include "inc/cwp_top_products_widget.php";

include "inc/cwp_comment.php";



/*

	Loading the stylesheet for admin page.

*/ 





 function cwppos_pac_admin_init() { 


       wp_enqueue_style( 'cwp-pac-admin-stylesheet', plugins_url('css/dashboard_styles.css', __FILE__) );

       wp_enqueue_script( 'cwp-pac-script', plugins_url('javascript/admin-review.js', __FILE__),array("jquery"),"20140101",true );

  }

 function cwppos_pac_register() { 

   

    //wp_register_script( 'jquery-ui-core' );

	//wp_register_script( 'jquery-ui-slider' );

    wp_register_script( 'pie-chart', plugins_url('javascript/pie-chart.js', __FILE__),array("jquery"),"20140101",true );

    wp_register_script( 'cwp-pac-main-script', plugins_url('javascript/main.js', __FILE__),array("jquery",'pie-chart'),"20140101",true );

   	wp_register_style( 'cwp-pac-frontpage-stylesheet', plugins_url('css/frontpage.css', __FILE__) ); 

    wp_register_style( 'jqueryui', plugins_url('css/jquery-ui.css', __FILE__) ); 

    wp_register_style( 'cwp-pac-fontawesome-stylesheet', plugins_url('css/font-awesome.min.css', __FILE__) ); 
  }

  function cwppos_pac_print() { 

  	//global $add_my_script;

	global $post; 
   		//echo get_post_meta($post->ID, "cwp_rev_product_name", true);
	if (get_post_meta($post->ID, "cwp_rev_product_name", true)!="" || get_post_meta($post->ID, "cwp_rev_product_image", true)!="") {

		wp_print_styles('cwp-pac-frontpage-stylesheet');
		wp_print_styles('jqueryui');
		wp_print_styles('cwp-pac-fontawesome-stylesheet');

		wp_print_scripts('jquery-ui-core');
		wp_print_scripts('jquery-ui-slider');
		wp_print_scripts('pie-chart');
		wp_print_scripts('cwp-pac-main-script');

    }

	

   }

   function cwppos_dynamic_stylesheet() {



		$options = cwppos();

		?>

		<style type="text/css">

			.user-comments-grades .comment-meta-grade-bar,

			#review-statistics  .review-wu-bars ul li{

				background: <?php echo $options['cwppos_rating_default']; ?>;

				

			}

			<?php if ($options['cwppos_widget_size']!="") {?>

			#review-statistics{
				width:<?php echo $options['cwppos_widget_size']; ?>px!important;
			}

			<?php } ?>
			#review-statistics .review-wrap-up .review-wu-right ul li,#review-statistics  .review-wu-bars h3, .review-wu-bars span,#review-statistics .review-wrap-up .review-top .item-category a{

				color:  <?php echo $options['cwppos_font_color']; ?>;

			}

			#review-statistics .review-wrap-up .review-wu-right .pros h2 {

				color:  <?php echo $options['cwppos_pros_color']; ?>;

			}

			

			#review-statistics .review-wrap-up .review-wu-right .cons h2{

				color:  <?php echo $options['cwppos_cons_color']; ?>;

			}

			.affiliate-button a{

				border:  2px solid  <?php echo $options['cwppos_buttonbd_color']; ?>;

			}

			.affiliate-button a:hover{

				border:  2px solid  <?php echo $options['cwppos_buttonbh_color']; ?>;

			}

			.affiliate-button a{

				background:  <?php echo $options['cwppos_buttonbkd_color']; ?>;

			}

			.affiliate-button a:hover{

				background:  <?php echo $options['cwppos_buttonbkh_color']; ?>;

			}

			.affiliate-button a span{

				color:  <?php echo $options['cwppos_buttontxtd_color']; ?>;

			}

			.affiliate-button a:hover span{

				color:  <?php echo $options['cwppos_buttontxth_color']; ?>;

			}

			<?php if($options['cwppos_show_icon'] == 'yes') { ?>

			.affiliate-button a span {

				background:url("<?php echo plugins_url('', __FILE__); ?>/images/cart-icon.png") no-repeat left center; 

			}

			.affiliate-button a:hover span{

				background:url("<?php echo plugins_url('', __FILE__); ?>/images/cart-icon-hover.png") no-repeat left center;  

			}

			<?php } ?>

			

		</style>

		<script type="text/javascript">

		

				var c1 = "<?php echo $options['cwppos_rating_weak'] ;?>"; 

				var c2 = "<?php echo $options['cwppos_rating_notbad'] ;?>";  

				var c3 = "<?php echo $options['cwppos_rating_good'] ;?>";  

				var c4 = "<?php echo $options['cwppos_rating_very_good'] ;?>";  



		</script>

		<?php

   

   }

add_action('init', 'cwppos_pac_register');
add_action('wp_head', 'cwppos_pac_print');

add_action('wp_head','cwppos_dynamic_stylesheet');
add_action( 'admin_init', 'cwppos_pac_admin_init' );
if (!class_exists('TAV_Remote_Notification_Client')) {
require( 'inc/class-remote-notification-client.php' );
}
$notification = new TAV_Remote_Notification_Client( 36, '71a28628279f6d55', 'http://themeisle.com/?post_type=notification' );
}
