<?php
/**
 * Core functions of WPPR
 *
 * @package WPPR
 * @author ThemeIsle
 * @since 1.0.0
 */
function cwppos_calc_overall_rating( $id ) {
	$options = cwppos();
	for ( $i = 1; $i <= cwppos( 'cwppos_option_nr' ); $i ++ ) {
		${'option' . $i . '_grade'} = get_post_meta( $id, 'option_' . $i . '_grade', true );
		// echo ${"option".$i."_grade"};
		${'comment_meta_option_nr_' . $i} = 0;
		${'comment_meta_option_' . $i}    = 0;

	}
	$nr_of_comments = 0;
	if ( $options['cwppos_show_userreview'] == 'yes' ) {
		$args           = array(
			'status'  => 'approve',
			'post_id' => $id, // use post_id, not post_ID
		);
		$comments       = get_comments( $args );
		$nr_of_comments = get_comments_number( $id );
		foreach ( $comments as $comment ) :
			for ( $i = 1; $i <= cwppos( 'cwppos_option_nr' ); $i ++ ) {
				if ( get_comment_meta( $comment->comment_ID, "meta_option_{$i}", true ) !== '' ) {
					${'comment_meta_option_nr_' . $i} ++;
					${'comment_meta_option_' . $i} += get_comment_meta( $comment->comment_ID, "meta_option_{$i}", true ) * 10;
				}

				// var_dump(${"comment_meta_option_".$i});
			}
		endforeach;
		for ( $i = 1; $i <= cwppos( 'cwppos_option_nr' ); $i ++ ) {
			if ( ${'comment_meta_option_nr_' . $i} !== 0 ) {
				${'comment_meta_option_' . $i} = ${'comment_meta_option_' . $i} / ${'comment_meta_option_nr_' . $i};
			}
		}
	} else {
		$options['cwppos_infl_userreview'] = 0;
	}
	if ( $nr_of_comments == 0 ) {
		$options['cwppos_infl_userreview'] = 0;
	}
	$overall_score = 0;
	$iter          = 0;
	$rating        = array();
	for ( $i = 1; $i <= cwppos( 'cwppos_option_nr' ); $i ++ ) {
		if ( ${'comment_meta_option_nr_' . $i} !== 0 ) {
			$infl = $options['cwppos_infl_userreview'];
		} else {
			$infl = 0;

		}
		if ( ! empty( ${'option' . $i . '_grade'} ) || ${'option' . $i . '_grade'} === '0' ) {
			// if($infl !== 0 ){
			${'option' . $i . '_grade'} = round( ( ${'option' . $i . '_grade'} * ( 100 - $infl ) + ${'comment_meta_option_' . $i} * $infl ) / 100 );
			// }else{
			// }
			$iter ++;
			$rating[ 'option' . $i ] = round( ${'option' . $i . '_grade'} );
			$overall_score += ${'option' . $i . '_grade'};
		}
	}
	// $overall_score = ($option1_grade + $option2_grade + $option3_grade + $option4_grade + $option5_grade) / $iter;
	if ( $iter !== 0 ) {
		$rating['overall'] = $overall_score / $iter;
	} else {
		$rating['overall'] = 0;
	}
	update_post_meta( $id, 'option_overall_score', $rating['overall'] );

	return $rating;

}

function cwppos_show_review( $id = '', $visual = 'full' ) {
	global $post;
	if ( (is_null( $post ) && $id == '') || post_password_required( $post ) ) {
		return false;
	}
	if ( $id == '' ) {
		$id = $post->ID;
	}
	$cwp_review_stored_meta = get_post_meta( $id );
	$return_string          = '';
	if ( isset( $cwp_review_stored_meta['cwp_meta_box_check'][0] ) && $cwp_review_stored_meta['cwp_meta_box_check'][0] == 'Yes' ) {
		wp_enqueue_style( 'cwp-pac-frontpage-stylesheet', WPPR_URL . '/css/frontpage.css', array(), WPPR_LITE_VERSION );
		wp_enqueue_script( 'pie-chart', WPPR_URL . '/javascript/pie-chart.js', array( 'jquery' ), WPPR_LITE_VERSION, true );
		wp_enqueue_script( 'cwp-pac-main-script', WPPR_URL . '/javascript/main.js', array(
			'jquery',
			'pie-chart',
		), WPPR_LITE_VERSION, true );
		$cwp_price = get_post_meta( $id, 'cwp_rev_price', true );
		$p_string  = $cwp_price;
		$p_name    = apply_filters( 'wppr_review_product_name', $id );
		if ( $p_string != '' ) {
			// Added by Ash/Upwork
			$cwp_price = do_shortcode( $cwp_price );
			// Added by Ash/Upwork
			$p_price    = preg_replace( '/[^0-9.,]/', '', $cwp_price );
			$p_currency = preg_replace( '/[0-9.,]/', '', $cwp_price );
			// Added by Ash/Upwork
			$p_disable = apply_filters( 'wppr_disable_price_richsnippet', false );
			// Added by Ash/Upwork
			if ( ! $p_disable ) {
				$p_string = '<span itemprop="offers" itemscope itemtype="http://schema.org/Offer"><span itemprop="priceCurrency">' . $p_currency . '</span><span itemprop="price">' . $p_price . '</span></span>';
			}
		}
		$product_image = do_shortcode( get_post_meta( $id, 'cwp_rev_product_image', true ) );
		$imgurl        = do_shortcode( get_post_meta( $id, 'cwp_image_link', true ) );
		$lightbox      = '';
		$feat_image    = wp_get_attachment_url( get_post_thumbnail_id( $id ) );
		if ( ! empty( $product_image ) ) {
			$product_image_cropped = wppr_get_image_id( $id, $product_image );
		} else {
			$product_image_cropped = wppr_get_image_id( $id );
			$product_image         = $feat_image;
		}
		if ( $imgurl == 'image' ) {
			// no means no disabled
			if ( cwppos( 'cwppos_lighbox' ) == 'no' ) {
				$lightbox = 'data-lightbox="' . $product_image . '"';
				wp_enqueue_script( 'img-lightbox', WPPR_URL . '/javascript/lightbox.min.js', array(), WPPR_LITE_VERSION, array() );
				wp_enqueue_style( 'img-lightbox-css', WPPR_URL . '/css/lightbox.css', array(), WPPR_LITE_VERSION );
			}
		} else {
			$product_image = do_shortcode( get_post_meta( $id, 'cwp_product_affiliate_link', true ) );
		}
		$rating    = cwppos_calc_overall_rating( $id );
		$divrating = $rating['overall'] / 10;
		for ( $i = 1; $i <= cwppos( 'cwppos_option_nr' ); $i ++ ) {
			${'option' . $i . '_content'} = do_shortcode( get_post_meta( $id, 'option_' . $i . '_content', true ) );
			if ( empty( ${'option' . $i . '_content'} ) ) {
				${'option' . $i . '_content'} = __( 'Default Feature ' . $i, 'cwppos' );
			}
		}
		$commentNr = get_comments_number( $id ) + 1;
		if ( $visual == 'full' ) {
			$return_string .= '<section id="review-statistics"  class="article-section" itemscope itemtype="http://schema.org/Product">
                                <div class="review-wrap-up  cwpr_clearfix" >
                                    <div class="cwpr-review-top cwpr_clearfix">
                                        <span itemprop="name">' . $p_name . '</span>

                                        <span class="cwp-item-price cwp-item">' . $p_string . '</span>
                                    </div><!-- end .cwpr-review-top -->
                                    <div class="review-wu-left">
                                        <div class="rev-wu-image">
    		                        <a href="' . $product_image . '" ' . $lightbox . '  rel="nofollow" target="_blank"><img itemprop="image" src="' . $product_image_cropped . '" alt="' . do_shortcode( get_post_meta( $id, 'cwp_rev_product_name', true ) ) . '" class="photo photo-wrapup wppr-product-image"  /></a>
                                    </div><!-- end .rev-wu-image -->
                                    <div class="review-wu-grade">';
		}
		if ( $visual == 'full' || $visual == 'yes' ) {
			$extra_class = $visual == 'yes' ? 'cwp-chart-embed' : '';
			$return_string .= '<div class="cwp-review-chart ' . $extra_class . '">
                                    <meta itemprop="datePublished" datetime="' . get_the_time( 'Y-m-d', $id ) . '">';
			if ( cwppos( 'cwppos_infl_userreview' ) != 0 && $commentNr > 1 ) {
				$return_string .= '<div itemprop="aggregateRating" itemscope itemtype="http://schema.org/AggregateRating" class="cwp-review-percentage" data-percent="';
				$return_string .= $rating['overall'] . '"><span itemprop="ratingValue" class="cwp-review-rating">' . $divrating . '</span><meta itemprop="bestRating" content = "10"/>
                         <meta itemprop="ratingCount" content="' . $commentNr . '"> </div>';

			} else {
				$return_string .= '<span itemscope itemtype="http://schema.org/Review"><span itemprop="author" itemscope itemtype="http://schema.org/Person"  >
                                             <meta itemprop="name"  content="' . get_the_author() . '"/>
                                        </span><span itemprop="itemReviewed" itemscope itemtype="http://schema.org/Product"><meta itemprop="name" content="' . do_shortcode( get_post_meta( $id, 'cwp_rev_product_name', true ) ) . '"/></span><div itemprop="reviewRating" itemscope itemtype="http://schema.org/Rating" class="cwp-review-percentage" data-percent="';
				$return_string .= $rating['overall'] . '"><span itemprop="ratingValue" class="cwp-review-rating">' . $divrating . '</span> <meta itemprop="bestRating" content="10">  </div></span>';
			}
			$return_string .= '</div><!-- end .chart -->';
		}
		if ( $visual == 'full' ) {
			$return_string .= '</div><!-- end .review-wu-grade -->
                                <div class="review-wu-bars">';
			for ( $i = 1; $i <= cwppos( 'cwppos_option_nr' ); $i ++ ) {
				if ( ! empty( ${'option' . $i . '_content'} ) && isset( $rating[ 'option' . $i ] ) && ( ! empty( $rating[ 'option' . $i ] ) || $rating[ 'option' . $i ] === '0' ) && strtoupper( ${'option' . $i . '_content'} ) != 'DEFAULT FEATURE ' . $i ) {
					$return_string .= '<div class="rev-option" data-value=' . $rating[ 'option' . $i ] . '>
                                                <div class="cwpr_clearfix">
                                                    ' . apply_filters( 'wppr_option_name_html', $id, ${'option' . $i . '_content'} ) . '
                                                    <span>' . round( $rating[ 'option' . $i ] / 10 ) . '/10</span>
                                                </div>
                                                <ul class="cwpr_clearfix"></ul>
                                            </div>';
				}
			}
			$return_string .= '</div><!-- end .review-wu-bars -->
                                </div><!-- end .review-wu-left -->
                                <div class="review-wu-right">
                                    <div class="pros">';
		}
		for ( $i = 1; $i <= cwppos( 'cwppos_option_nr' ); $i ++ ) {
			${'pro_option_' . $i} = do_shortcode( get_post_meta( $id, 'cwp_option_' . $i . '_pro', true ) );
			if ( empty( ${'pro_option_' . $i} ) ) {
				${'pro_option_' . $i} = '';
			}
		}
		for ( $i = 1; $i <= cwppos( 'cwppos_option_nr' ); $i ++ ) {
			${'cons_option_' . $i} = do_shortcode( get_post_meta( $id, 'cwp_option_' . $i . '_cons', true ) );
			if ( empty( ${'cons_option_' . $i} ) ) {
				${'cons_option_' . $i} = '';
			}
		}
		if ( $visual == 'full' ) {
			$return_string .= apply_filters( 'wppr_review_pros_text', $id, __( cwppos( 'cwppos_pros_text' ), 'cwppos' ) ) . ' <ul>';
			for ( $i = 1; $i <= cwppos( 'cwppos_option_nr' ); $i ++ ) {
				if ( ! empty( ${'pro_option_' . $i} ) ) {
					$return_string .= '   <li>' . ${'pro_option_' . $i} . '</li>';
				}
			}
			$return_string .= '     </ul>
                                    </div><!-- end .pros -->
                                    <div class="cons">';
			$return_string .= apply_filters( 'wppr_review_cons_text', $id, __( cwppos( 'cwppos_cons_text' ), 'cwppos' ) ) . ' <ul>';
			for ( $i = 1; $i <= cwppos( 'cwppos_option_nr' ); $i ++ ) {
				if ( ! empty( ${'cons_option_' . $i} ) ) {
					$return_string .= '   <li>' . ${'cons_option_' . $i} . '</li>';
				}
			}
			$return_string .= '
                                        </ul>
                                    </div>
                                </div><!-- end .review-wu-right -->
                                </div><!-- end .review-wrap-up -->
                            </section><!-- end #review-statistics -->';
		}
		if ( cwppos( 'cwppos_show_poweredby' ) == 'yes' && ! class_exists( 'CWP_PR_PRO_Core' ) ) {
			$return_string .= '<div style="font-size:12px;width:100%;float:right"><p style="float:right;">Powered by <a href="http://wordpress.org/plugins/wp-product-review/" target="_blank" rel="nofollow" > WP Product Review</a></p></div>';
		}
		$affiliate_text  = do_shortcode( get_post_meta( $id, 'cwp_product_affiliate_text', true ) );
		$affiliate_link  = do_shortcode( get_post_meta( $id, 'cwp_product_affiliate_link', true ) );
		$affiliate_text2 = do_shortcode( get_post_meta( $id, 'cwp_product_affiliate_text2', true ) );
		$affiliate_link2 = do_shortcode( get_post_meta( $id, 'cwp_product_affiliate_link2', true ) );
		if ( ! empty( $affiliate_text2 ) && ! empty( $affiliate_link2 ) ) {
			$bclass = 'affiliate-button2 affiliate-button';
		} else {
			$bclass = 'affiliate-button';
		}
		if ( $visual == 'full' && ! empty( $affiliate_text ) && ! empty( $affiliate_link ) ) {
			$return_string .= '<div class="' . $bclass . '">
                                        <a href="' . $affiliate_link . '" rel="nofollow" target="_blank"><span>' . $affiliate_text . '</span> </a>
                                    </div><!-- end .affiliate-button -->';
		}
		if ( $visual == 'full' && ! empty( $affiliate_text2 ) && ! empty( $affiliate_link2 ) ) {
			$return_string .= '<div class="affiliate-button affiliate-button2">
                                        <a href="' . $affiliate_link2 . '" rel="nofollow" target="_blank"><span>' . $affiliate_text2 . '</span> </a>
                                    </div><!-- end .affiliate-button -->';
		}
		if ( $visual == 'no' ) {
			$return_string = round( $divrating );
		}
	}

	return $return_string;
}

function cwppos_pac_admin_init() {
	wp_enqueue_style( 'cwp-pac-admin-stylesheet', WPPR_URL . '/css/dashboard_styles.css' );
	wp_register_script( 'cwp-pac-script', WPPR_URL . '/javascript/admin-review.js', array( 'jquery' ), '20140101', true );
	wp_localize_script( 'cwp-pac-script', 'ispro', array( 'value' => class_exists( 'CWP_PR_PRO_Core' ) ) );
	wp_enqueue_script( 'cwp-pac-script' );
	if ( class_exists( 'CWP_PR_PRO_Core' ) ) {
		wp_enqueue_style( 'cwp-pac-pro-admin-stylesheet', WPPR_URL . '/css/pro_dashboard_styles.css' );
	}
	do_action( 'wppr-amazon-enqueue' );
}

function wppr_get_image_id( $post_id, $image_url = '', $size = 'thumbnail' ) {
	global $wpdb;
	// filter for image size;
	$size = apply_filters( 'wppr_review_image_size', $size, $post_id );
	if ( ! empty( $image_url ) && $image_url !== false ) {
		$attachment = $wpdb->get_col( $wpdb->prepare( "SELECT ID FROM $wpdb->posts WHERE guid='%s';", $image_url ) );
		$image_id   = isset( $attachment[0] ) ? $attachment[0] : '';
	} else {
		$image_id = get_post_thumbnail_id( $post_id );

	}
	$image_thumb = '';
	if ( ! empty( $image_id ) ) {
		$image_thumb = wp_get_attachment_image_src( $image_id, $size );
		if ( $size !== 'thumbnail' ) {
			if ( $image_thumb[0] === $image_url ) {
				$image_thumb = wp_get_attachment_image_src( $image_id, 'thumbnail' );
			}
		}
	}

	return isset( $image_thumb[0] ) ? $image_thumb[0] : $image_url;
}

function custom_bar_icon() {
	$options = cwppos();
	if ( ( isset( $options['cwppos_show_poweredby'] ) && $options['cwppos_show_poweredby'] == 'yes' ) || function_exists( 'wppr_ci_custom_bar_icon' ) || class_exists( 'CWP_PR_PRO_Core' ) ) {
		wp_register_script( 'cwp-custom-bar-icon', WPPR_URL . '/javascript/custom-bar-icon.js', false, '1.0', 'all' );
		wp_enqueue_script( 'cwp-custom-bar-icon' );
	}
	wppr_add_pointers();
}

function wppr_add_pointers() {
	$screen    = get_current_screen();
	$screen_id = $screen->id;
	// Get pointers for this screen
	$pointers = apply_filters( 'wppr_admin_pointers-' . $screen_id, array() );
	if ( ! $pointers || ! is_array( $pointers ) ) {
		return;
	}
	$dismissed = explode( ',', (string) get_user_meta( get_current_user_id(), 'dismissed_wp_pointers', true ) );
	$valid     = array();
	foreach ( $pointers as $pointer_id => $pointer ) {
		// Sanity check
		if ( in_array( $pointer_id, $dismissed ) || empty( $pointer ) || empty( $pointer_id ) || empty( $pointer['target'] ) || empty( $pointer['options'] ) ) {
			continue;
		}
		$pointer['pointer_id'] = $pointer_id;
		// Add the pointer to $valid_pointers array
		$valid['pointers'][] = $pointer;
	}
	if ( empty( $valid ) ) {
		return;
	}
	// Add pointers style to queue.
	wp_enqueue_style( 'wp-pointer', array( 'jquery' ) );
	wp_enqueue_script( 'wppr-pointers', WPPR_URL . '/javascript/cwp-pointers.js', array( 'wp-pointer' ), WPPR_LITE_VERSION, true );
	wp_localize_script( 'wppr-pointers', 'cwpp', array( 'pointers' => $valid ) );
}

function cwppos_pac_register() {
	add_image_size( 'wppr_widget_image', 50, 50 );
}

function cwp_def_settings() {
	global $post;
	$options = cwppos();
	if ( function_exists( 'wppr_ci_custom_bar_icon' ) || ( isset( $options['cwppos_show_poweredby'] ) && $options['cwppos_show_poweredby'] == 'yes' ) ) {
		$isSetToPro = true;
	} else {
		$isSetToPro = false;
	}
	$uni_font = cwppos( 'cwppos_change_bar_icon' );
	$track    = $options['cwppos_rating_chart_default'];
	// if ($uni_font!=="&#")
	if ( isset( $uni_font[0] ) ) {
		if ( $uni_font[0] == '#' ) {
			$uni_font = $uni_font;
		} else {
			$uni_font = $uni_font[0];
		}
	} else {
		$uni_font = '';
	}
	if ( ! empty( $uni_font ) ) {
		if ( function_exists( 'wppr_ci_custom_bar_icon' ) || cwppos( 'cwppos_show_poweredby' ) == 'yes' ) {
			if ( cwppos( 'cwppos_fontawesome' ) === 'no' ) {
				wp_enqueue_style( 'cwp-pac-fontawesome-stylesheet', WPPR_URL . '/css/font-awesome.min.css' );
			}
		}
	}
	echo "<script type='text/javascript'>
                    var cwpCustomBarIcon = '" . $uni_font . "';
                    var isSetToPro = '" . $isSetToPro . "';
                    var trackcolor = '" . $track . "';
                </script>";
}

function cwppos_pac_print() {
	cwp_def_settings();
}

function cwppos_dynamic_stylesheet() {
	$options = cwppos();
	// Get theme content width or plugin setting content width
	global $content_width;
	$c_width = 700;
	if ( $options['cwppos_widget_size'] != '' ) {
		$c_width = $options['cwppos_widget_size'];
	} else {
		$c_width = $content_width;
	}
	if ( $c_width < 200 ) {
		$c_width = 600;
	}
	$f_img_size = min( 180, $c_width * 0.51 * 0.4 );
	$h_tleft    = $f_img_size + 10;
	$chart_size = 0.8 * $f_img_size;
	?>
	<style type="text/css">

		@media (min-width: 820px) {
			#review-statistics .review-wrap-up .review-wu-left .rev-wu-image, #review-statistics .review-wrap-up .review-wu-left .review-wu-grade {
				height: <?php echo $h_tleft;?>px;
			}

			#review-statistics .review-wrap-up .review-wu-left .review-wu-grade .cwp-review-chart .cwp-review-percentage {

				margin-top: <?php echo $f_img_size * 0.1;?>%;
			}

			#review-statistics .review-wrap-up .review-wu-left .review-wu-grade .cwp-review-chart span {
				font-size: <?php echo round( 30 * $f_img_size / 140 );?>px;
			}

		<?php  if ( $options['cwppos_widget_size'] != '' ) { ?>
			#review-statistics {
				width: <?php  echo $options['cwppos_widget_size']; ?>px;
			}

		<?php  } ?>

		}

		#review-statistics .review-wrap-up div.cwpr-review-top {
			border-top: <?php  echo $options['cwppos_reviewboxbd_width']; ?>px solid <?php  echo $options['cwppos_reviewboxbd_color']; ?>;
		}

		.user-comments-grades .comment-meta-grade-bar,
		#review-statistics .review-wu-bars ul li {
			background: <?php  echo $options['cwppos_rating_default']; ?>;
		}

		#review-statistics .rev-option.customBarIcon ul li {
			color: <?php  echo $options['cwppos_rating_default']; ?>;
		}

		#review-statistics .review-wrap-up .review-wu-right ul li, #review-statistics .review-wu-bars h3, .review-wu-bars span, #review-statistics .review-wrap-up .cwpr-review-top .cwp-item-category a {
			color: <?php  echo $options['cwppos_font_color']; ?>;
		}

		#review-statistics .review-wrap-up .review-wu-right .pros h2 {
			color: <?php  echo $options['cwppos_pros_color']; ?>;
		}

		#review-statistics .review-wrap-up .review-wu-right .cons h2 {
			color: <?php  echo $options['cwppos_cons_color']; ?>;
		}

		div.affiliate-button a {
			border: 2px solid <?php  echo $options['cwppos_buttonbd_color']; ?>;
		}

		div.affiliate-button a:hover {
			border: 2px solid <?php  echo $options['cwppos_buttonbh_color']; ?>;
		}

		div.affiliate-button a {
			background: <?php  echo $options['cwppos_buttonbkd_color']; ?>;
		}

		div.affiliate-button a:hover {
			background: <?php  echo $options['cwppos_buttonbkh_color']; ?>;
		}

		div.affiliate-button a span {
			color: <?php  echo $options['cwppos_buttontxtd_color']; ?>;
		}

		div.affiliate-button a:hover span {
			color: <?php  echo $options['cwppos_buttontxth_color']; ?>;
		}

		<?php  if ( $options['cwppos_show_icon'] == 'yes' ) { ?>
		div.affiliate-button a span {
			background: url("<?php  echo WPPR_URL; ?>/images/cart-icon.png") no-repeat left center;
		}

		div.affiliate-button a:hover span {
			background: url("<?php  echo WPPR_URL; ?>/images/cart-icon-hover.png") no-repeat left center;
		}

		<?php  } ?>

		<?php if ( $options['cwppos_show_userreview'] == 'yes' ) { ?>
		.commentlist .comment-body p {
			clear: left;
		}

		<?php } ?>
	</style>
	<script type="text/javascript">
		var c1 = "<?php echo $options['cwppos_rating_weak']; ?>";
		var c2 = "<?php echo $options['cwppos_rating_notbad']; ?>";
		var c3 = "<?php echo $options['cwppos_rating_good']; ?>";
		var c4 = "<?php echo $options['cwppos_rating_very_good']; ?>";
	</script>
	<?php
}

add_action( 'init', 'cwppos_pac_register' );
add_action( 'wp_head', 'cwppos_pac_print' );
add_action( 'wp_footer', 'cwppos_dynamic_stylesheet' );
add_action( 'admin_init', 'cwppos_pac_admin_init' );
add_action( 'admin_enqueue_scripts', 'custom_bar_icon' );
add_action( 'wp_ajax_wppr-dismiss-amazon-link', 'wppr_dismiss_amazon_link' );
function wppr_dismiss_amazon_link() {
	$pointer_id = isset( $_POST['pointer'] ) ? $_POST['pointer'] : null;
	if ( $pointer_id ) {
		$dismissed = explode( ',', (string) get_user_meta( get_current_user_id(), 'dismissed_wp_pointers', true ) );
		if ( ! $dismissed ) {
			$dismissed = array();
		}
		$dismissed[] = $pointer_id;
		update_user_meta( get_current_user_id(), 'dismissed_wp_pointers', implode( ',', $dismissed ) );
	}
}

if ( class_exists( 'CWP_PR_PRO_Core' ) ) {
	$cwp_pr_pro = new CWP_PR_PRO_Core();
}
load_plugin_textdomain( 'cwppos', false, dirname( plugin_basename( WPPR_PATH ) ) . '/languages/' );
