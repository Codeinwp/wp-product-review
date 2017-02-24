<?php
function cwp_review_meta_boxes() {
	add_meta_box( 'cwp_review_meta_box', 'Product Review Extra Settings', 'cwp_review_meta_box_callback' );
}

/*
* Function for rendering the review custom meta boxes.
*/
function cwp_review_meta_box_callback( $post ) {
	wp_nonce_field( 'cwp_product_review_meta_box_nonce', 'cwp_meta_box_nonce' );
	$cwp_review_stored_meta = get_post_meta( $post->ID );
	$check                  = isset( $cwp_review_stored_meta['cwp_meta_box_check'][0] ) ? esc_attr( $cwp_review_stored_meta['cwp_meta_box_check'][0] ) : 'No';
	$query_args = array(
		'posts_per_page' => '1', // limit it to the specified no of posts
		'post_type'      => 'any',
		'post__not_in'   => get_option( 'sticky_posts' ),
		'meta_key'       => 'option_overall_score',
		'meta_query' => array(
			array(
				'key' => 'cwp_meta_box_check',
				'value' => 'Yes',
			),
		),
		'orderby'    => 'date',
		'order'      => 'DESC',
	);
	$cwp_latest_products_loop = get_posts( $query_args );
	foreach ( $cwp_latest_products_loop as $w_post ) : setup_postdata( $w_post );
		$wppr_id = $w_post->ID;
	endforeach;
	wp_reset_postdata();
	$p_meta = isset( $wppr_id ) ? get_post_meta( $wppr_id ) : array();
	if ( isset( $cwp_review_stored_meta['cwp_image_link'][0] ) ) {
		$checkset = esc_attr( $cwp_review_stored_meta['cwp_image_link'][0] );
	} else {
		$checkset = isset( $p_meta['cwp_image_link'][0] ) ? esc_attr( $p_meta['cwp_image_link'][0] ) : 'image';

	}
	// $checkset = isset( $cwp_review_stored_meta['cwp_image_link'][0] ) ?  ) : "image";
	?>
	<p class="isReview<?php echo $check; ?>">
		<label for="my_meta_box_check">Is this a review post ? </label>
		<input type="radio" id="cwp_meta_box_check_yes" name="cwp_meta_box_check"
		       value="Yes" <?php checked( $check, 'Yes' ); ?> />
		<label for="my_meta_box_check">Yes</label>
		<input type="radio" id="cwp_meta_box_check_no" name="cwp_meta_box_check"
		       value="No" <?php checked( $check, 'No' ); ?> />
		<label for="my_meta_box_check" style="margin-right:30px;">No</label>

	</p>
	<div class="product-review-meta-<?php echo $check; ?>">

		<div class="review-settings-notice">
			<h4><?php _e( 'Product Details', 'cwppos' ); ?></h4>
			<p style="margin:0;"><?php _e( 'Specify the general details for the reviewed product.', 'cwppos' ); ?></p>
		</div><!-- end .review-settings-notice -->
		<div class="review-settings-group">
			<div class="review-settings-group-option">
				<ul>
					<?php
					// Added by Ash/Upwork
					do_action( 'wppr-amazon-addfields', $cwp_review_stored_meta );
					// Added by Ash/Upwork
					?>

					<li>
						<label for="cwp_rev_product_name"><?php _e( 'Product Name', 'cwppos' ); ?></label>
						<input type="text" name="cwp_rev_product_name" id="cwp_rev_product_name" value="<?php
						if ( isset( $cwp_review_stored_meta['cwp_rev_product_name'][0] ) ) {
							echo $cwp_review_stored_meta['cwp_rev_product_name'][0];
						}
						?>"/>
					</li>
					<li>
						<label for="cwp_rev_product_image"
						       class="cwp_rev_product_image-title"><?php _e( 'Product Image', 'cwppos' ) ?></label>
						<input type="text" name="cwp_rev_product_image" id="cwp_rev_product_image"
						       value="<?php if ( isset( $cwp_review_stored_meta['cwp_rev_product_image'][0] ) ) {
									echo $cwp_review_stored_meta['cwp_rev_product_image'][0];
} ?>"/>
						<input type="button" id="cwp_rev_product_image-button" class="button"
						       value="<?php _e( 'Choose or Upload an Image', 'cwppos' ) ?>"/>
						<p><?php _e( '*If no image is provided, featured image is used','cwppos' ); ?>
					</li>
					<li class="cwp_image_link">
						<label for="cwp_image_link_aff">Product Image Click : </label>
						<input type="radio" id="cwp_image_link_aff" name="cwp_image_link"
						       value="image" <?php checked( $checkset, 'image' ); ?> />
						<label for="cwp_image_link_image">Show Whole Image</label>
						<input type="radio" id="cwp_image_link_image" name="cwp_image_link"
						       value="link" <?php checked( $checkset, 'link' ); ?> />
						<label for="my_meta_box_check">Open Affiliate link</label>
					</li>
					<li>
						<label
							for="cwp_product_affiliate_text"><?php _e( 'Affiliate Button Text', 'cwppos' ); ?></label>
						<input type="text" name="cwp_product_affiliate_text" id="cwp_product_affiliate_text"
						       value="<?php
								if ( isset( $cwp_review_stored_meta['cwp_product_affiliate_text'][0] ) ) {
									echo $cwp_review_stored_meta['cwp_product_affiliate_text'][0];
								} else {
									if ( isset( $p_meta['cwp_product_affiliate_text'][0] ) ) {
										echo $p_meta['cwp_product_affiliate_text'][0];
									}
								}
								?>"/>
					</li>
					<li>
						<label for="cwp_product_affiliate_link"><?php _e( 'Affiliate Link', 'cwppos' ); ?></label>
						<input type="text" name="cwp_product_affiliate_link" id="cwp_product_affiliate_link"
						       value="<?php
								if ( isset( $cwp_review_stored_meta['cwp_product_affiliate_link'][0] ) ) {
									echo $cwp_review_stored_meta['cwp_product_affiliate_link'][0];
								}
								?>"/>
						<span id="wppr_product_affiliate_link_upsell" style="display: none">&nbsp;</span>
						<?php
						$hide_button2 = false;
						if ( ! isset( $cwp_review_stored_meta['cwp_product_affiliate_text2'][0] ) ) {
							$hide_button2 = true;
							?>
							<a href="#" id="cwp_add_button" title="Add new button">+</a>
						<?php } ?>
					</li>

					<li class="<?php if ( $hide_button2 ) {
						echo 'cwp_hide_button2';
} ?>">
						<label
							for="cwp_product_affiliate_text2"><?php _e( 'Affiliate Text 2', 'cwppos' ); ?></label>
						<input type="text" name="cwp_product_affiliate_text2" id="cwp_product_affiliate_text2"
						       value="<?php
								if ( isset( $cwp_review_stored_meta['cwp_product_affiliate_text2'][0] ) ) {
									echo $cwp_review_stored_meta['cwp_product_affiliate_text2'][0];
								}
								?>"/>
					</li>
					<li class="<?php if ( $hide_button2 ) {
						echo 'cwp_hide_button2';
} ?>">
						<label for="cwp_product_affiliate_link2"><?php _e( 'Affiliate Link 2', 'cwppos' ); ?></label>
						<input type="text" name="cwp_product_affiliate_link2" id="cwp_product_affiliate_link2"
						       value="<?php
								if ( isset( $cwp_review_stored_meta['cwp_product_affiliate_link2'][0] ) ) {
									echo $cwp_review_stored_meta['cwp_product_affiliate_link2'][0];
								}
								?>"/>
					</li>

					<li>
						<label for="cwp_cwp_rev_price"><?php _e( 'Product Price', 'cwppos' ); ?></label>
						<input type="text" name="cwp_rev_price" id="cwp_rev_price" value="<?php
						if ( isset( $cwp_review_stored_meta['cwp_rev_price'][0] ) ) {
							echo $cwp_review_stored_meta['cwp_rev_price'][0];
						}
						?>"/>
					</li>

				</ul>
			</div><!-- end .review-settings-group option -->
		</div><!-- end .review-settings group -->
		<div class="review-settings-notice">
			<h4><?php _e( 'Product Options Setting', 'cwppos' ); ?></h4>
			<?php _e( 'Insert your options and their grades. Grading must be done <b><i>from 0 to 100</i></b>.','cwppos' );
			if ( cwppos( 'cwppos_show_poweredby' ) === 'yes' || class_exists( 'CWP_PR_PRO_Core' ) || function_exists( 'wppr_ep_js_preloader' ) ) { ?>
				<a href="#" class="preload_info"><?php _e( 'Preload Info', 'cwppos' ); ?></a>
				<?php
			} else {
				$pageURL = admin_url( 'admin.php?page=cwppos_options#tab-upgrade_to_pro' );
				$pageURL = str_replace( ':80', '', $pageURL );
				echo apply_filters( WPPR_SLUG . '_' . 'preloader' . '_upsell_text', '' );
			} ?>
		</div><!-- end .review-settings-notice -->
		<div class="review-settings-group">
			<?php
			for ( $i = 1; $i <= cwppos( 'cwppos_option_nr' ); $i ++ ) { ?>
				<div class="review-settings-group-option">
					<label for="option_<?php echo $i; ?>_content" class="option_label"><?php echo $i; ?></label>
					<input type="text" name="option_<?php echo $i; ?>_content" id="option_<?php echo $i; ?>_content"
					       class="option_content" placeholder="Option <?php echo $i; ?>" value="<?php
							if ( isset( $cwp_review_stored_meta[ 'option_' . $i . '_content' ][0] ) ) {
								echo $cwp_review_stored_meta[ 'option_' . $i . '_content' ][0];
							} else {
								// Get latest modified post from the same category id
								if ( isset( $p_meta[ 'option_' . $i . '_content' ][0] ) ) {
									echo $p_meta[ 'option_' . $i . '_content' ][0];
								}
							}
					?>"/>
					<input type="text" name="option_<?php echo $i; ?>_grade" class="option_grade" placeholder="Grade"
					       value="<?php
							if ( isset( $cwp_review_stored_meta[ 'option_' . $i . '_grade' ][0] ) ) {
								echo $cwp_review_stored_meta[ 'option_' . $i . '_grade' ][0];
							}
							?>"/>
				</div><!-- end .review-settings-group option -->
			<?php } ?>

		</div><!-- end .review-settings group -->
		<div class="cwp_proscons">
			<div class="review-settings-notice">
				<h4><?php _e( 'Pro Features', 'cwppos' ); ?></h4>
				<p style="margin:0;"><?php _e( "Insert product's pro features below.", 'cwppos' ); ?></p>
			</div><!-- end .review-settings-notice -->
			<div class="review-settings-group">
				<?php for ( $i = 1; $i <= cwppos( 'cwppos_option_nr' ); $i ++ ) { ?>

					<div class="review-settings-group-option">
						<label for="cwp_option_<?php echo $i; ?>_pro" class="option_label"><?php echo $i; ?></label>
						<input type="text" name="cwp_option_<?php echo $i; ?>_pro" id="cwp_option_<?php echo $i; ?>_pro"
						       class="option_content" placeholder="Option <?php echo $i; ?>" value="<?php
								if ( isset( $cwp_review_stored_meta[ 'cwp_option_' . $i . '_pro' ][0] ) ) {
									echo $cwp_review_stored_meta[ 'cwp_option_' . $i . '_pro' ][0];
								}
						?>"/>
					</div><!-- end .review-settings-group option -->
				<?php } ?>

			</div><!-- end .review-settings group -->
		</div>
		<div class="cwp_proscons">
			<div class="review-settings-notice">
				<h4><?php _e( 'Cons Features', 'cwppos' ); ?></h4>
				<p style="margin:0;"><?php _e( "Insert product's cons features below.", 'cwppos' ); ?></p>
			</div><!-- end .review-settings-notice -->
			<div class="review-settings-group">
				<?php for ( $i = 1; $i <= cwppos( 'cwppos_option_nr' ); $i ++ ) { ?>

					<div class="review-settings-group-option">
						<label for="cwp_option_<?php echo $i; ?>_cons" class="option_label"><?php echo $i; ?></label>
						<input type="text" name="cwp_option_<?php echo $i; ?>_cons"
						       id="cwp_option_<?php echo $i; ?>_cons" class="option_content"
						       placeholder="Option <?php echo $i; ?>" value="<?php
								if ( isset( $cwp_review_stored_meta[ 'cwp_option_' . $i . '_cons' ][0] ) ) {
									echo $cwp_review_stored_meta[ 'cwp_option_' . $i . '_cons' ][0];
								}
						?>"/>
					</div><!-- end .review-settings-group option -->
				<?php } ?>

			</div><!-- end .review-settings group -->
		</div>
		<?php if ( ! shortcode_exists( 'P_REVIEW' ) ) :  ?>
			<label> You can use the shortcode <b>[P_REVIEW]</b> to show a review you already made or
				<b>[wpr_landing]</b> to display a comparision table of them. The shortcodes are available on the <a
					target="_blank" href="http://bit.ly/2bpKIlP">Pro Bundle</a><br/><br/></label>
		<?php endif; ?>
	</div>
	<?php
}

/**
 * Function for saving the review custom meta boxes.
 */
function cwp_review_meta_boxes_save( $post_id ) {
	$is_autosave    = wp_is_post_autosave( $post_id );
	$is_revision    = wp_is_post_revision( $post_id );
	$is_valid_nonce = ( isset( $_POST['cwp_meta_box_nonce'] ) && wp_verify_nonce( $_POST['cwp_meta_box_nonce'], 'cwp_product_review_meta_box_nonce' ) ) ? 'true' :
		'false';
	if ( $is_autosave || $is_revision || ! $is_valid_nonce ) {
		return;
	}
	// Added by Ash/Upwork
	do_action( 'wppr-amazon-savefields', $post_id );
	// Added by Ash/Upwork
	// Moved from inside the if loop to here by Ash/Upwork
	if ( isset( $_POST['cwp_meta_box_check'] ) ) {
		update_post_meta( $post_id, 'cwp_meta_box_check', sanitize_text_field( $_POST['cwp_meta_box_check'] ) );
	}
	// Moved from inside the if loop to here by Ash/Upwork
	if ( isset( $_POST['cwp_meta_box_check'] ) && $_POST['cwp_meta_box_check'] == 'Yes' ) {
		if ( isset( $_POST['cwp_rev_product_name'] ) ) {
			update_post_meta( $post_id, 'cwp_rev_product_name', apply_filters( 'wppr_sanitize_product_title', $_POST['cwp_rev_product_name'] ) );
		}
		if ( isset( $_POST['cwp_rev_price'] ) ) {
			update_post_meta( $post_id, 'cwp_rev_price', apply_filters( 'wppr_sanitize_product_price', $_POST['cwp_rev_price'] ) );
		}
		if ( isset( $_POST['cwp_image_link'] ) ) {
			update_post_meta( $post_id, 'cwp_image_link', apply_filters( 'wppr_sanitize_product_image', $_POST['cwp_image_link'] ) );
		}
		if ( isset( $_POST['cwp_product_affiliate_text'] ) ) {
			update_post_meta( $post_id, 'cwp_product_affiliate_text', sanitize_text_field( $_POST['cwp_product_affiliate_text'] ) );
		}
		if ( isset( $_POST['cwp_product_affiliate_link'] ) ) {
			update_post_meta( $post_id, 'cwp_product_affiliate_link', apply_filters( 'wppr_sanitize_link1', $_POST['cwp_product_affiliate_link'] ) );
		}
		if ( isset( $_POST['cwp_product_affiliate_text2'] ) ) {
			update_post_meta( $post_id, 'cwp_product_affiliate_text2', sanitize_text_field( $_POST['cwp_product_affiliate_text2'] ) );
		}
		if ( isset( $_POST['cwp_product_affiliate_link2'] ) ) {
			update_post_meta( $post_id, 'cwp_product_affiliate_link2', apply_filters( 'wppr_sanitize_link2', $_POST['cwp_product_affiliate_link2'] ) );
		}
		if ( ! empty( $_POST['cwp_bar_icon'] ) ) {
			update_post_meta( $post_id, 'cwp_bar_icon', esc_url( $_POST['cwp_bar_icon'] ) );
		} else {
			update_post_meta( $post_id, 'cwp_bar_icon', '' );
		}
		for ( $i = 1; $i <= cwppos( 'cwppos_option_nr' ); $i ++ ) {
			if ( isset( $_POST[ 'option_' . $i . '_content' ] ) ) {
				update_post_meta( $post_id, 'option_' . $i . '_content', sanitize_text_field( $_POST[ 'option_' . $i . '_content' ] ) );
			}
			if ( isset( $_POST[ 'option_' . $i . '_grade' ] ) ) {
				update_post_meta( $post_id, 'option_' . $i . '_grade', sanitize_text_field( $_POST[ 'option_' . $i . '_grade' ] ) );
			}
		}
		if ( isset( $_POST['cwp_rev_product_image'] ) ) {
			update_post_meta( $post_id, 'cwp_rev_product_image', sanitize_text_field( $_POST['cwp_rev_product_image'] ) );
		} elseif ( cwppos( 'cwppos_show_poweredby' ) == 'yes' || class_exists( 'CWP_PR_PRO_Core' ) ) {
			$image = '';
			if ( strlen( $img = get_the_post_thumbnail( $post_id, array( 150, 150 ) ) ) ) :
				$image_array = wp_get_attachment_image_src( get_post_thumbnail_id( $post_id ), 'optional-size' );
				$image       = $image_array[0];
			else :
				$post  = get_post( $post_id );
				$image = '';
				ob_start();
				ob_end_clean();
				$output = preg_match_all( '/<img.+src=[\'"]([^\'"]+)[\'"].*>/i', $post->post_content, $matches );
				// $image = $matches[1][0];
			endif;
			update_post_meta( $post_id, 'cwp_rev_product_image', $image );
		}
		for ( $i = 1; $i <= cwppos( 'cwppos_option_nr' ); $i ++ ) {
			if ( isset( $_POST[ 'cwp_option_' . $i . '_pro' ] ) ) {
				update_post_meta( $post_id, 'cwp_option_' . $i . '_pro', sanitize_text_field( $_POST[ 'cwp_option_' . $i . '_pro' ] ) );
			}
			if ( isset( $_POST[ 'cwp_option_' . $i . '_cons' ] ) ) {
				update_post_meta( $post_id, 'cwp_option_' . $i . '_cons', sanitize_text_field( $_POST[ 'cwp_option_' . $i . '_cons' ] ) );
			}
		}
		$overall_score = '';
		$iter          = 0;
		for ( $i = 1; $i <= cwppos( 'cwppos_option_nr' ); $i ++ ) {
			${'option' . $i . '_grade'} = get_post_meta( $post_id, 'option_' . $i . '_grade', true );
			if ( ! empty( ${'option' . $i . '_grade'} ) || ${'option' . $i . '_grade'} === '0' ) {
				$overall_score += ${'option' . $i . '_grade'};
				$iter ++;
			}
		}
		if ( $iter == 0 ) {
			$overall_score = 0;
		} else {
			$overall_score = $overall_score / $iter;
		}
		update_post_meta( $post_id, 'option_overall_score', $overall_score / 10 );
	}
}

function cwp_review_plugin_activation() {
	if ( 'yes' != get_option( 'cwp_review_activate' ) ) {
		add_option( 'cwp_review_activate', 'yes' );
		update_option( 'cwp_review_activate', 'yes' );
		$html = '<div class="updated">';
		$html .= '<p>';
		$html .= __( 'In order to use the WP Product Review plugin, go on and edit a post and set : <strong>Is this a review to yes</strong>.', 'cwppos' );
		$html .= '</p>';
		$html .= '</div><!-- /.updated -->';
		echo $html;

	} // end if
} // end plugin_activation
/**
 * Hooks.
 */
add_action( 'add_meta_boxes', 'cwp_review_meta_boxes' );
add_action( 'save_post', 'cwp_review_meta_boxes_save' );
add_action( 'admin_notices', 'cwp_review_plugin_activation' );
?>
