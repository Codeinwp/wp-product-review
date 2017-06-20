<?php
class WPPR_Front_Render_Controller {

	private $theme_name;

	private $model;

	public function __construct() {
		$current_theme = wp_get_theme();
		$this->model = new WPPR_Comment_Model();
		$this->theme_name = $current_theme->get( 'Name' );
		$this->add_hooks();
	}

	public function add_hooks() {
		if ( $this->theme_name !== 'Bookrev' && $this->theme_name !== 'Book Rev Lite' ) {
			add_filter( 'the_content', array( $this, 'before_content' ) );
		}
	}

	public function before_content( $content ) {
		global $post;
		$cwp_review_stored_meta = get_post_meta( $post->ID );
		$return_string = $this->cwppos_show_review();

		global $page;
		if ( isset( $cwp_review_stored_meta['cwp_meta_box_check'][0] ) && $cwp_review_stored_meta['cwp_meta_box_check'][0] == 'Yes' && ( is_single() || is_page() ) && $page === 1 ) {
			if ( $this->model->get_var( 'cwppos_show_reviewbox' ) == 'yes' ) { return $content . $return_string; }
			if ( $this->model->get_var( 'cwppos_show_reviewbox' ) == 'no' ) { return $return_string . $content; }
			return $content;
		} else {
			return $content; }
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
			wp_enqueue_style( 'cwp-pac-frontpage-stylesheet', WPPR_URL . '/assets/css/frontpage.css', array(), WPPR_LITE_VERSION );
			wp_enqueue_script( 'pie-chart', WPPR_URL . '/assets/js/pie-chart.js', array( 'jquery' ), WPPR_LITE_VERSION, true );
			wp_enqueue_script( 'cwp-pac-main-script', WPPR_URL . '/assets/js/main.js', array(
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
				$product_image_cropped = $this->wppr_get_image_id( $id, $product_image );
			} else {
				$product_image_cropped = $this->wppr_get_image_id( $id );
				$product_image         = $feat_image;
			}
			if ( $imgurl == 'image' ) {
				// no means no disabled
				if ( $this->model->get_var( 'cwppos_lighbox' ) == 'no' ) {
					$lightbox = 'data-lightbox="' . $product_image . '"';
					wp_enqueue_script( 'img-lightbox', WPPR_URL . '/assets/js/lightbox.min.js', array(), WPPR_LITE_VERSION, array() );
					wp_enqueue_style( 'img-lightbox-css', WPPR_URL . '/assets/css/lightbox.css', array(), WPPR_LITE_VERSION );
				}
			} else {
				$product_image = do_shortcode( get_post_meta( $id, 'cwp_product_affiliate_link', true ) );
			}
			$rating    = $this->cwppos_calc_overall_rating( $id );
			$divrating = $rating['overall'] / 10;
			for ( $i = 1; $i <= $this->model->get_var( 'cwppos_option_nr' ); $i ++ ) {
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
				if ( $this->model->get_var( 'cwppos_infl_userreview' ) != 0 && $commentNr > 1 ) {
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
				for ( $i = 1; $i <= $this->model->get_var( 'cwppos_option_nr' ); $i ++ ) {
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
			for ( $i = 1; $i <= $this->model->get_var( 'cwppos_option_nr' ); $i ++ ) {
				${'pro_option_' . $i} = do_shortcode( get_post_meta( $id, 'cwp_option_' . $i . '_pro', true ) );
				if ( empty( ${'pro_option_' . $i} ) ) {
					${'pro_option_' . $i} = '';
				}
			}
			for ( $i = 1; $i <= $this->model->get_var( 'cwppos_option_nr' ); $i ++ ) {
				${'cons_option_' . $i} = do_shortcode( get_post_meta( $id, 'cwp_option_' . $i . '_cons', true ) );
				if ( empty( ${'cons_option_' . $i} ) ) {
					${'cons_option_' . $i} = '';
				}
			}
			if ( $visual == 'full' ) {
				$return_string .= apply_filters( 'wppr_review_pros_text', $id, __( $this->model->get_var( 'cwppos_pros_text' ), 'cwppos' ) ) . ' <ul>';
				for ( $i = 1; $i <= $this->model->get_var( 'cwppos_option_nr' ); $i ++ ) {
					if ( ! empty( ${'pro_option_' . $i} ) ) {
						$return_string .= '   <li>' . ${'pro_option_' . $i} . '</li>';
					}
				}
				$return_string .= '     </ul>
                                    </div><!-- end .pros -->
                                    <div class="cons">';
				$return_string .= apply_filters( 'wppr_review_cons_text', $id, __( $this->model->get_var( 'cwppos_cons_text' ), 'cwppos' ) ) . ' <ul>';
				for ( $i = 1; $i <= $this->model->get_var( 'cwppos_option_nr' ); $i ++ ) {
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
			if ( $this->model->get_var( 'cwppos_show_poweredby' ) == 'yes' && ! class_exists( 'CWP_PR_PRO_Core' ) ) {
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
		}// End if().

		return $return_string;
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

	public function cwppos_calc_overall_rating( $id ) {
		$options = $this->model->get_all_options();
		for ( $i = 1; $i <= $this->model->get_var( 'cwppos_option_nr' ); $i ++ ) {
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
				for ( $i = 1; $i <= $this->model->get_var( 'cwppos_option_nr' ); $i ++ ) {
					if ( get_comment_meta( $comment->comment_ID, "meta_option_{$i}", true ) !== '' ) {
						${'comment_meta_option_nr_' . $i} ++;
						${'comment_meta_option_' . $i} += get_comment_meta( $comment->comment_ID, "meta_option_{$i}", true ) * 10;
					}
				}
			endforeach;
			for ( $i = 1; $i <= $this->model->get_var( 'cwppos_option_nr' ); $i ++ ) {
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
		for ( $i = 1; $i <= $this->model->get_var( 'cwppos_option_nr' ); $i ++ ) {
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

	public function cwppos_dynamic_stylesheet() {
		$options = $this->model->get_all_options();
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
}
