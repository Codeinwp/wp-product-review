<?php
/**
 * Core functions of WPPR
 * @package WPPR
 * @author ThemeIsle
 * @since 1.0.0
 *
 */


function cwppos_calc_overall_rating($id){
	$options = cwppos();

	for($i=1; $i<=cwppos("cwppos_option_nr"); $i++) {

		${"option".$i."_grade"} = get_post_meta($id, "option_".$i."_grade", true);
		// echo ${"option".$i."_grade"};

		${"comment_meta_option_nr_".$i} = 0;
		${"comment_meta_option_".$i} = 0;

	}
	$nr_of_comments = 0;
	if( $options['cwppos_show_userreview'] == "yes" ) {
		$args = array(
			'status' => 'approve',
			'post_id' => $id, // use post_id, not post_ID
		);
		$comments = get_comments($args);
		$nr_of_comments = get_comments_number($id);

		foreach($comments as $comment) :
			for($i=1; $i<=cwppos("cwppos_option_nr"); $i++) {

				if (get_comment_meta( $comment->comment_ID, "meta_option_{$i}", true)!=='') {
					${"comment_meta_option_nr_".$i}++;
					${"comment_meta_option_".$i} += get_comment_meta( $comment->comment_ID, "meta_option_{$i}", true)*10;
				}


				//var_dump(${"comment_meta_option_".$i});
			}


		endforeach;

		for($i=1; $i<=cwppos("cwppos_option_nr"); $i++) {
			if (${"comment_meta_option_nr_".$i}!==0)
				${"comment_meta_option_".$i} = ${"comment_meta_option_".$i}/${"comment_meta_option_nr_".$i};
		}

	}
	else {
		$options['cwppos_infl_userreview'] = 0;
	}
	if ( $nr_of_comments==0 )
		$options['cwppos_infl_userreview'] = 0;

	$overall_score = 0;
	$iter = 0;
	$rating = array();

	for ($i=1;$i<=cwppos("cwppos_option_nr");$i++) {

		if (${"comment_meta_option_nr_".$i}!==0)
			$infl = $options['cwppos_infl_userreview'];
		else {
			$infl = 0;

		}
		if(!empty(${'option'.$i.'_grade'})|| ${'option'.$i.'_grade'} === '0') {
			${'option'.$i.'_grade'} = round((${'option'.$i.'_grade'}*(100-$infl) + ${'comment_meta_option_'.$i}*$infl)/100);
			$iter++;
			$rating['option'.$i] = round(${'option'.$i.'_grade'});
			$overall_score+=${'option'.$i.'_grade'};
		}
	}
	//$overall_score = ($option1_grade + $option2_grade + $option3_grade + $option4_grade + $option5_grade) / $iter;
	if ($iter !==0) $rating['overall'] = $overall_score/$iter;
	else $rating['overall'] = 0;
	update_post_meta($id, 'option_overall_score', $rating['overall']);
	return $rating;


}

function cwppos_show_review($id = "") {
	global $post;
	if ( post_password_required($post) ) return false;
	wp_enqueue_style( 'cwp-pac-frontpage-stylesheet', WPPR_URL.'/css/frontpage.css',array(),WPPR_LITE_VERSION );

	wp_enqueue_script( 'pie-chart', WPPR_URL.'/javascript/pie-chart.js',array("jquery"), WPPR_LITE_VERSION,true );
	wp_enqueue_script( 'cwp-pac-main-script', WPPR_URL.'/javascript/main.js',array("jquery",'pie-chart'),WPPR_LITE_VERSION,true );

	if ($id=="")
		$id = $post->ID;


	$cwp_review_stored_meta = get_post_meta( $id );
	$return_string = "";

	if(@$cwp_review_stored_meta['cwp_meta_box_check'][0]  == 'Yes' ) {


		$return_string  = '<section id="review-statistics"  class="article-section" itemscope itemtype="http://schema.org/Review">
                            <div class="review-wrap-up  cwpr_clearfix" >
                                <div class="cwpr-review-top cwpr_clearfix" itemprop="itemReviewed">
                                    <h2 class="cwp-item"  itemprop="name"  >'.get_post_meta($id, "cwp_rev_product_name", true).'</h2>
                                    <span class="cwp-item-price cwp-item"   >'.get_post_meta($id, "cwp_rev_price", true).'</span>
                                </div><!-- end .cwpr-review-top -->
                                <div class="review-wu-left">
                                    <div class="rev-wu-image">';

		$product_image = get_post_meta($id, "cwp_rev_product_image", true);
		$imgurl = get_post_meta($id, "cwp_image_link", true);
		$lightbox = "";
		if ($imgurl =="image") {
			$feat_image = wp_get_attachment_url( get_post_thumbnail_id( $id ) );
			if(cwppos("cwppos_lighbox") == "no"){
				$lightbox   = 'data-lightbox="' . $feat_image . '"';
				wp_enqueue_script("img-lightbox",WPPR_URL.'/javascript/lightbox.min.js',array(), WPPR_LITE_VERSION, array());
				wp_enqueue_style("img-lightbox-css", WPPR_URL.'/css/lightbox.css' , array(), WPPR_LITE_VERSION  );
			}
		}else{
			$feat_image = get_post_meta($id, "cwp_product_affiliate_link", true);
		}
		if(!empty($product_image)) {
			$product_image  = wppr_get_image_id($id,$product_image);
		} else {
			$product_image = wppr_get_image_id($id);
		}
		$return_string .= '<a href="'.$feat_image.'" '.$lightbox.'  rel="nofollow"><img  src="'.$product_image.'" alt="'. get_post_meta($id, "cwp_rev_product_name", true).'" class="photo photo-wrapup wppr-product-image" style="visibility:hidden"/></a>';

		$rating = cwppos_calc_overall_rating($id);

		for($i=1; $i<=cwppos("cwppos_option_nr"); $i++) {
			${"option".$i."_content"} = get_post_meta($id, "option_".$i."_content", true);

			if(empty(${"option".$i."_content"})) {
				${"option".$i."_content"} = __("Default Feature ".$i, "cwppos");
			}
		}

		$commentNr = get_comments_number($id)+1;
		$divrating = $rating['overall']/10;
		$return_string .= '</div><!-- end .rev-wu-image -->
                                <div class="review-wu-grade">
                                    <div class="cwp-review-chart">
                                    <meta itemprop="datePublished" datetime="'.get_the_time("Y-m-d", $id).'">
                                    <span itemprop="author" itemscope itemtype="http://schema.org/Person" display="none" >
                                         <meta itemprop="name"  content="'.get_the_author().'">
                                    </span>';
		if(cwppos("cwppos_infl_userreview") == 0) {

			$return_string .= '<div    itemprop="reviewRating" itemscope itemtype="http://schema.org/Rating" class="cwp-review-percentage" data-percent="';
			$return_string .= $rating['overall'] . '"><span itemprop="ratingValue" class="cwp-review-rating">' . $divrating . '</span>  </div>';

		}else {
			$return_string .= '<div    itemprop="aggregateRating" itemscope itemtype="http://schema.org/AggregateRating" class="cwp-review-percentage" data-percent="';
			$return_string .= $rating['overall'] . '"><span itemprop="ratingValue" class="cwp-review-rating">' . $divrating . '</span><meta itemprop="bestRating" content = "10"/>
                     <meta itemprop="reviewCount" content="' . $commentNr . '"> </div>';
		}
		$return_string .='
                                    </div><!-- end .chart -->
                                </div><!-- end .review-wu-grade -->
                                <div class="review-wu-bars">';

		for($i=1; $i<=cwppos("cwppos_option_nr"); $i++) {

			if (!empty(${'option'.$i.'_content'}) && isset($rating['option'.$i]) && (!empty($rating['option'.$i]) || $rating['option'.$i] === '0' ) &&  strtoupper(${'option'.$i.'_content'}) != 'DEFAULT FEATURE '.$i) {
				$return_string .= '<div class="rev-option" data-value='.$rating['option'.$i].'>
                                            <div class="cwpr_clearfix">
                                                <h3>'. ${'option'.$i.'_content'}.'</h3>
                                                <span>'.$rating['option'.$i].'/10</span>
                                            </div>
                                            <ul class="cwpr_clearfix"></ul>
                                        </div>';
			}


		}

		$return_string .='
                                </div><!-- end .review-wu-bars -->
                            </div><!-- end .review-wu-left -->
                            <div class="review-wu-right">
                                <div class="pros">';

		for($i=1; $i<=cwppos("cwppos_option_nr"); $i++) {
			${"pro_option_".$i} = get_post_meta($id, "cwp_option_".$i."_pro", true);
			if(empty(${"pro_option_".$i})) {
				${"pro_option_".$i}  = "" ;
			}
		}

		for($i=1; $i<=cwppos("cwppos_option_nr"); $i++) {
			${"cons_option_".$i} = get_post_meta($id, "cwp_option_".$i."_cons", true);
			if(empty(${"cons_option_".$i})) {
				${"cons_option_".$i}  = "";
			}

		}

		$return_string .=  '<h2>'.__(cwppos("cwppos_pros_text"), "cwppos").'</h2>
                                    <ul>';
		for($i=1;$i<=cwppos("cwppos_option_nr");$i++) {
			if(!empty(${"pro_option_".$i})) {
				$return_string .=  '   <li>- '.${"pro_option_".$i}.'</li>';
			}
		}

		$return_string .= '     </ul>
                                </div><!-- end .pros -->
                                <div class="cons">';
		$return_string .=' <h2>'.__(cwppos("cwppos_cons_text"), "cwppos").'</h2>  <ul>';

		for($i=1;$i<=cwppos("cwppos_option_nr");$i++){
			if(!empty(${"cons_option_".$i})) {
				$return_string .=  '   <li>- '.${"cons_option_".$i}.'</li>';
			}

		}

		$return_string .='
                                    </ul>
                                </div>';
		$return_string .='
                            </div><!-- end .review-wu-right -->
                            </div><!-- end .review-wrap-up -->
                        </section><!-- end #review-statistics -->';

		if(cwppos("cwppos_show_poweredby") == 'yes' && !class_exists('CWP_PR_PRO_Core')) {
			$return_string.='<div style="font-size:12px;width:100%;float:right"><p style="float:right;">Powered by <a href="http://wordpress.org/plugins/wp-product-review/" target="_blank" rel="nofollow" > WP Product Review</a></p></div>';
		}

		$affiliate_text = get_post_meta($id, "cwp_product_affiliate_text", true);
		$affiliate_link = get_post_meta($id, "cwp_product_affiliate_link", true);
		$affiliate_text2 = get_post_meta($id, "cwp_product_affiliate_text2", true);
		$affiliate_link2 = get_post_meta($id, "cwp_product_affiliate_link2", true);

		if(!empty($affiliate_text2) && !empty($affiliate_link2)) {
			$bclass="affiliate-button2 affiliate-button";
		}
		else
			$bclass="affiliate-button";

		if(!empty($affiliate_text) && !empty($affiliate_link)) {
			$return_string .= '<div class="'.$bclass.'">
                                        <a href="'.$affiliate_link.'" rel="nofollow" target="_blank"><span>'. $affiliate_text.'</span> </a>
                                    </div><!-- end .affiliate-button -->';
		}




		if(!empty($affiliate_text2) && !empty($affiliate_link2)) {
			$return_string .= '<div class="affiliate-button affiliate-button2">
                                        <a href="'.$affiliate_link2.'" rel="nofollow" target="_blank"><span>'. $affiliate_text2.'</span> </a>
                                    </div><!-- end .affiliate-button -->';
		}
	}

	return $return_string;
}



function cwppos_pac_admin_init() {
	wp_enqueue_style( 'cwp-pac-admin-stylesheet', WPPR_URL.'/css/dashboard_styles.css' );
	wp_register_script( 'cwp-pac-script', WPPR_URL.'/javascript/admin-review.js',array("jquery"),"20140101",true );
	wp_localize_script( 'cwp-pac-script', 'ispro', array( 'value' => class_exists('CWP_PR_PRO_Core') ) );
	wp_enqueue_script('cwp-pac-script' );
}

function wppr_get_image_id($post_id, $image_url = "", $size = "thumbnail" ) {

	global $wpdb;
	if(!empty($image_url) && $image_url !== false ) {

		$attachment = $wpdb->get_col($wpdb->prepare("SELECT ID FROM $wpdb->posts WHERE guid='%s';", $image_url ));
		$image_id  = isset($attachment[0]) ? $attachment[0] : '';
	}else{
		$image_id = get_post_thumbnail_id($post_id );

	}
	$image_thumb = "";
	if(!empty($image_id)){
		$image_thumb = wp_get_attachment_image_src($image_id, $size);
		if( $size !== 'thumbnail' ) {
			if($image_thumb[0] === $image_url){
				$image_thumb = wp_get_attachment_image_src($image_id, "thumbnail");
			}
		}
	}
	return isset($image_thumb[0]) ? $image_thumb[0] : $image_url;
}


function custom_bar_icon() {
	$options = cwppos();

	if ($options['cwppos_show_poweredby']=="yes" || function_exists("wppr_ci_custom_bar_icon") || class_exists('CWP_PR_PRO_Core')) {
		wp_register_script("cwp-custom-bar-icon", WPPR_URL.'/javascript/custom-bar-icon.js', false, "1.0", "all");
		wp_enqueue_script("cwp-custom-bar-icon");
	}
}

function cwppos_pac_register() {
	add_image_size("wppr_widget_image",50,50);
}

function cwp_def_settings() {
	global $post;
	$options = cwppos();
	if (function_exists('wppr_ci_custom_bar_icon') || $options['cwppos_show_poweredby']=="yes") {
		$isSetToPro = true;
	} else {
		$isSetToPro = false;
	}

	$uni_font = cwppos("cwppos_change_bar_icon");
	$track = $options['cwppos_rating_chart_default'];

	//if ($uni_font!=="&#")

	if(isset($uni_font[0])) {  if ($uni_font[0]=="#") $uni_font = $uni_font; else $uni_font = $uni_font[0]; } else { $uni_font = ""; }

	if(!empty($uni_font)){
		if(function_exists("wppr_ci_custom_bar_icon") || cwppos('cwppos_show_poweredby') == 'yes'){
			if(cwppos("cwppos_fontawesome") === "no"){
				wp_enqueue_style( 'cwp-pac-fontawesome-stylesheet',  WPPR_URL.'/css/font-awesome.min.css' );
			}
		}
	}
	echo    "<script type='text/javascript'>
                    var cwpCustomBarIcon = '" . $uni_font . "';
                    var isSetToPro = '".$isSetToPro."';
                    var trackcolor = '".$track."';
                </script>";
}

function cwppos_pac_print() {
	//global $add_my_script;
	global $post;
	//echo get_post_meta($post->ID, "cwp_rev_product_name", true);


	/*wp_print_styles('cwp-pac-frontpage-stylesheet');
	wp_print_styles('cwp-pac-widget-stylesheet');
	wp_print_styles('jqueryui');
	wp_print_styles('cwp-pac-fontawesome-stylesheet');
	//wp_print_styles('cwp-pac-widget-stylesheet');
	wp_print_scripts('jquery-ui-core');
	wp_print_scripts('jquery-ui-slider');
	wp_print_scripts('pie-chart');
	wp_print_scripts('cwp-pac-main-script');
	wp_print_scripts('img-lightbox');
	wp_print_styles('img-lightbox-css');*/

	cwp_def_settings();


}

/**
 * Addons menu item
 */
function cwp_addons_menu() {
	add_submenu_page( 'cwppos_options', __( 'WP Product Review Add-ons/Extensions', 'cwppos_options' ),  __( 'Add-ons', 'cwppos' ) , 'manage_options', 'wp-addons', 'cwp_addons');
}


function cwppos_dynamic_stylesheet() {
	$options = cwppos();
	?>
	<style type="text/css">
		#review-statistics .review-wrap-up .cwpr-review-top { border-top: <?php  echo $options['cwppos_reviewboxbd_width']; ?>px solid <?php  echo $options['cwppos_reviewboxbd_color']; ?>;  }
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
		#review-statistics .review-wrap-up .review-wu-right ul li,#review-statistics  .review-wu-bars h3, .review-wu-bars span,#review-statistics .review-wrap-up .cwpr-review-top .cwp-item-category a{
			color:  <?php  echo $options['cwppos_font_color']; ?>;
		}
		#review-statistics .review-wrap-up .review-wu-right .pros h2 {
			color:  <?php  echo $options['cwppos_pros_color']; ?>;
		}
		#review-statistics .review-wrap-up .review-wu-right .cons h2{
			color:  <?php  echo $options['cwppos_cons_color']; ?>;
		}
		div.affiliate-button a{
			border:  2px solid  <?php  echo $options['cwppos_buttonbd_color']; ?>;
		}
		div.affiliate-button a:hover{
			border:  2px solid  <?php  echo $options['cwppos_buttonbh_color']; ?>;
		}
		div.affiliate-button a{
			background:  <?php  echo $options['cwppos_buttonbkd_color']; ?>;
		}
		div.affiliate-button a:hover{
			background:  <?php  echo $options['cwppos_buttonbkh_color']; ?>;
		}
		div.affiliate-button a span{
			color:  <?php  echo $options['cwppos_buttontxtd_color']; ?>;
		}
		div.affiliate-button a:hover span{
			color:  <?php  echo $options['cwppos_buttontxth_color']; ?>;
		}
		<?php  if($options['cwppos_show_icon'] == 'yes') { ?>
		div.affiliate-button a span {
			background:url("<?php  echo WPPR_URL ?>"/images/cart-icon.png") no-repeat left center;
		}
		div.affiliate-button a:hover span{
			background:url("<?php  echo WPPR_URL; ?>/images/cart-icon-hover.png") no-repeat left center;
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
add_action('wp_footer','cwppos_dynamic_stylesheet');
add_action( 'admin_init', 'cwppos_pac_admin_init' );
add_action('admin_menu', 'cwp_addons_menu');
add_action('admin_enqueue_scripts', 'custom_bar_icon');

if (!class_exists('TAV_Remote_Notification_Client')) require( WPPR_PATH.'/inc/class-remote-notification-client.php' );
$notification = new TAV_Remote_Notification_Client( 36, '71a28628279f6d55', 'https://themeisle.com/?post_type=notification' );

if (class_exists('CWP_PR_PRO_Core')) $cwp_pr_pro = new CWP_PR_PRO_Core();

load_plugin_textdomain('cwppos', false, dirname(plugin_basename(WPPR_PATH)).'/languages/');