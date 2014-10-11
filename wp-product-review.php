<?php
/*
Plugin Name: WP Product Review 
Description: The highest rated and most complete review plugin, now with rich snippets support. Easily turn your basic posts into in-depth reviews.
Version: 2.4.4
Author: Themeisle
Author URI:  https://themeisle.com/
Plugin URI: https://themeisle.com/plugins/wp-product-review-lite/
Requires at least: 3.5
Tested up to: 4.0
Stable tag: trunk
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html
Text Domain: cwppos
Domain Path: /languages
*/

if (wp_get_theme() !== "Reviewgine Affiliate PRO") {
    include "admin/functions.php";
    include "inc/cwp_metabox.php";
    include "inc/cwp_frontpage.php";
    include "inc/cwp_top_products_widget.php";
    include "inc/cwp_latest_products_widget.php";
    include "inc/cwp_comment.php";
    /*
Loading the stylesheet for admin page.
*/

    function cwppos_calc_overall_rating($id){
        $options = cwppos();
        
        for($i=1; $i<=cwppos("cwppos_option_nr"); $i++) {

            ${"option".$i."_grade"} = get_post_meta($id, "option_".$i."_grade", true);
           // echo ${"option".$i."_grade"};

            ${"comment_meta_option_".$i} = 0;

        }

        if( $options['cwppos_show_userreview'] == "yes" ) {
            $args = array(
                'status' => 'approve',
                'post_id' => $id, // use post_id, not post_ID
            );
            $comments = get_comments($args);
            $nr_of_comments = get_comments_number($id);
            
            foreach($comments as $comment) :
                for($i=1; $i<=cwppos("cwppos_option_nr"); $i++) {
                    
                    ${"comment_meta_option_".$i} += get_comment_meta( $comment->comment_ID, "meta_option_{$i}", true)*10/$nr_of_comments;
                    //var_dump(${"comment_meta_option_".$i});
                }
            endforeach;

        }
        else {
            $options['cwppos_infl_userreview'] = 0;
        }
        if ( $nr_of_comments==0 )
            $options['cwppos_infl_userreview'] = 0;
            
        $overall_score = 0;
        $iter = 0;
        $rating = array();
        for ($i=1;$i<=cwppos("cwppos_option_nr");$i++)
        if(!empty(${'option'.$i.'_grade'})|| ${'option'.$i.'_grade'} === '0') { ${'option'.$i.'_grade'} = round((${'option'.$i.'_grade'}*(100-$options['cwppos_infl_userreview']) + ${'comment_meta_option_'.$i}*$options['cwppos_infl_userreview'])/100); $iter++; $rating['option'.$i] = round(${'option'.$i.'_grade'});  $overall_score+=${'option'.$i.'_grade'}; }
        //$overall_score = ($option1_grade + $option2_grade + $option3_grade + $option4_grade + $option5_grade) / $iter;
        $rating['overall'] = $overall_score/$iter;
        update_post_meta($id, 'option_overall_score', $overall_score);
        return $rating;

        
    }



    function cwppos_pac_admin_init() {
        wp_enqueue_style( 'cwp-pac-admin-stylesheet', plugins_url('css/dashboard_styles.css', __FILE__) );
        wp_enqueue_script( 'cwp-pac-script', plugins_url('javascript/admin-review.js', __FILE__),array("jquery"),"20140101",true );
    }


    function preloader_js(){
        $options = cwppos();

        if (class_exists('CWP_PR_PRO_Core') || $options['cwppos_show_poweredby']=="yes") {
            wp_register_script("cwp-review-preload", plugins_url( 'inc/cwp-review-preload.php', __FILE__ ), false, "1.0", "all");
            wp_enqueue_script("cwp-review-preload");

        }

    }

    function custom_bar_icon() {
        $options = cwppos();

        if (class_exists('CWP_PR_PRO_Core') || $options['cwppos_show_poweredby']=="yes") {
            wp_register_script("cwp-custom-bar-icon", plugins_url('javascript/custom-bar-icon.js', __FILE__), false, "1.0", "all");
            wp_enqueue_script("cwp-custom-bar-icon");
            wp_register_style("font-awesome-cdn", "//maxcdn.bootstrapcdn.com/font-awesome/4.1.0/css/font-awesome.min.css");
            wp_enqueue_style("font-awesome-cdn");
        }
    }

    function cwppos_pac_register() {
        //wp_register_script( 'jquery-ui-core' );
        //wp_register_script( 'jquery-ui-slider' );
        wp_register_script( 'pie-chart', plugins_url('javascript/pie-chart.js', __FILE__),array("jquery"),"20140101",true );
        wp_register_script( 'cwp-pac-main-script', plugins_url('javascript/main.js', __FILE__),array("jquery",'pie-chart'),"20140101",true );
        wp_register_style( 'cwp-pac-frontpage-stylesheet', plugins_url('css/frontpage.css', __FILE__) );
        wp_register_style( 'cwp-pac-widget-stylesheet', plugins_url('css/cwppos-widget.css', __FILE__) );
        wp_register_style( 'jqueryui', plugins_url('css/jquery-ui.css', __FILE__) );
        wp_register_style( 'cwp-pac-fontawesome-stylesheet', plugins_url('css/font-awesome.min.css', __FILE__) );
        wp_enqueue_script("img-lightbox", plugins_url( 'javascript/lightbox.min.js', __FILE__ ), false, "1.0", "all");
        wp_enqueue_style("img-lightbox-css", plugins_url( 'css/lightbox.css', __FILE__ ), false, "1.0", "all");
              
    }

    function cwp_def_settings() {
        global $post;
        $options = cwppos();
        if (class_exists('CWP_PR_PRO_Core') || $options['cwppos_show_poweredby']=="yes") {
            $isSetToPro = true;
        } else {
            $isSetToPro = false;
        }

        $uni_font = cwppos("cwppos_change_bar_icon");
        
        //if ($uni_font!=="&#")

        if(isset($uni_font[0])) {  if ($uni_font[0]=="#") $uni_font = $uni_font; else $uni_font = $uni_font[0]; } else { $uni_font = ""; }
      
        echo    "<script type='text/javascript'>
                    var cwpCustomBarIcon = '" . $uni_font . "';
                    var isSetToPro = '".$isSetToPro."';
                </script>";
    }

    function cwppos_pac_print() {
        //global $add_my_script;
        global $post;
        //echo get_post_meta($post->ID, "cwp_rev_product_name", true);

        if (get_post_meta($post->ID, "cwp_rev_product_name", true)!="" || get_post_meta($post->ID, "cwp_rev_product_image", true)!="") {
            wp_print_styles('cwp-pac-frontpage-stylesheet');
            wp_print_styles('cwp-pac-widget-stylesheet');
            wp_print_styles('jqueryui');
            wp_print_styles('cwp-pac-fontawesome-stylesheet');
            //wp_print_styles('cwp-pac-widget-stylesheet');
            wp_print_scripts('jquery-ui-core');
            wp_print_scripts('jquery-ui-slider');
            wp_print_scripts('pie-chart');
            wp_print_scripts('cwp-pac-main-script');
            wp_print_scripts('img-lightbox');
            wp_print_styles('img-lightbox-css');
            wp_print_scripts('jquery-ui-core');
            cwp_def_settings();
        } else {
            wp_print_styles('cwp-pac-widget-stylesheet');
            wp_print_scripts('pie-chart');
            wp_print_scripts('cwp-pac-main-script');
        }

    }


    function cwppos_dynamic_stylesheet() {
        $options = cwppos();
        ?>
        <style type="text/css">
            #review-statistics .review-wrap-up .review-top { border-top: <?php  echo $options['cwppos_reviewboxbd_width']; ?>px solid <?php  echo $options['cwppos_reviewboxbd_color']; ?>;  }
            .user-comments-grades .comment-meta-grade-bar,
            #review-statistics  .review-wu-bars ul li{
                background: <?php  echo $options['cwppos_rating_default']; ?>;
            }
            
            #review-statistics .rev-option.customBarIcon ul li {
                color: <?php  echo $options['cwppos_rating_default']; ?>;
            }

            <?php  if ($options['cwppos_widget_size']!="") { ?>
            #review-statistics{
                width:<?php  echo $options['cwppos_widget_size']; ?>px!important;
            }
            <?php  } ?>
            #review-statistics .review-wrap-up .review-wu-right ul li,#review-statistics  .review-wu-bars h3, .review-wu-bars span,#review-statistics .review-wrap-up .review-top .cwp-item-category a{
                color:  <?php  echo $options['cwppos_font_color']; ?>;
            }
            #review-statistics .review-wrap-up .review-wu-right .pros h2 {
                color:  <?php  echo $options['cwppos_pros_color']; ?>;
            }
            #review-statistics .review-wrap-up .review-wu-right .cons h2{
                color:  <?php  echo $options['cwppos_cons_color']; ?>;
            }
            .affiliate-button a{
                border:  2px solid  <?php  echo $options['cwppos_buttonbd_color']; ?>;
            }
            .affiliate-button a:hover{
                border:  2px solid  <?php  echo $options['cwppos_buttonbh_color']; ?>;
            }
            .affiliate-button a{
                background:  <?php  echo $options['cwppos_buttonbkd_color']; ?>;
            }
            .affiliate-button a:hover{
                background:  <?php  echo $options['cwppos_buttonbkh_color']; ?>;
            }
            .affiliate-button a span{
                color:  <?php  echo $options['cwppos_buttontxtd_color']; ?>;
            }
            .affiliate-button a:hover span{
                color:  <?php  echo $options['cwppos_buttontxth_color']; ?>;
            }
            <?php  if($options['cwppos_show_icon'] == 'yes') { ?>
            .affiliate-button a span {
                background:url("<?php  echo plugins_url('', __FILE__); ?>/images/cart-icon.png") no-repeat left center;
            }
            .affiliate-button a:hover span{
                background:url("<?php  echo plugins_url('', __FILE__); ?>/images/cart-icon-hover.png") no-repeat left center;
            }
            <?php  } ?>
        </style>
        <script type="text/javascript">
            var c1 = "<?php echo $options['cwppos_rating_weak'] ; ?>";
            var c2 = "<?php echo $options['cwppos_rating_notbad'] ; ?>";
            var c3 = "<?php echo $options['cwppos_rating_good'] ; ?>";
            var c4 = "<?php echo $options['cwppos_rating_very_good'] ; ?>";
        </script>
    <?php
    }

    add_action('init', 'cwppos_pac_register');
    add_action('wp_head', 'cwppos_pac_print');
    add_action('wp_head','cwppos_dynamic_stylesheet');
    add_action( 'admin_init', 'cwppos_pac_admin_init' );
    add_action('admin_enqueue_scripts','preloader_js');

    add_action('admin_enqueue_scripts', 'custom_bar_icon');

    if (!class_exists('TAV_Remote_Notification_Client')) require( 'inc/class-remote-notification-client.php' );
    $notification = new TAV_Remote_Notification_Client( 36, '71a28628279f6d55', 'https://themeisle.com/?post_type=notification' );

    if (class_exists('CWP_PR_PRO_Core')) $cwp_pr_pro = new CWP_PR_PRO_Core();

    load_plugin_textdomain('cwppos', false, dirname(plugin_basename(__FILE__)).'/languages/');

    // [bartag foo="foo-value"]

}