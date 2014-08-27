<?php
function cwp_review_meta_boxes(){
    add_meta_box( 'cwp_review_meta_box', 'Product Review Extra Settings', 'cwp_review_meta_box_callback');
}

/*
* Function for rendering the review custom meta boxes.
*/	function cwp_review_meta_box_callback( $post ){
    wp_nonce_field( 'cwp_product_review_meta_box_nonce', 'cwp_meta_box_nonce' );
    $cwp_review_stored_meta = get_post_meta( $post->ID );
    $check = isset( $cwp_review_stored_meta['cwp_meta_box_check'][0] ) ? esc_attr( $cwp_review_stored_meta['cwp_meta_box_check'][0] ) : "No";
    ?>
    <p class="isReview<?php echo $check; ?>">
        <label for="my_meta_box_check">Is this a review post ? </label>
        <input type="radio" id="cwp_meta_box_check_yes" name="cwp_meta_box_check" value="Yes" <?php  checked( $check, 'Yes' ); ?> />
        <label for="my_meta_box_check">Yes</label>
        <input type="radio" id="cwp_meta_box_check_no" name="cwp_meta_box_check" value="No" <?php  checked( $check, 'No' ); ?> />
        <label for="my_meta_box_check">No</label>
    </p>
    <div class="product-review-meta-<?php  echo $check; ?>">
    <div class="review-settings-notice">
        <h4><?php  _e("Product Details", "cwppos"); ?></h4>
        <p style="margin:0;"><?php  _e("Specify the general details for the reviewed product.", "cwppos"); ?></p>
    </div><!-- end .review-settings-notice -->
    <div class="review-settings-group">
        <div class="review-settings-group-option">
            <ul>
                <li>
                    <label for="cwp_rev_product_name"><?php  _e("Product Name", "cwppos"); ?></label>
                    <input type="text" name="cwp_rev_product_name" id="cwp_rev_product_name" value="<?php

                    if(isset($cwp_review_stored_meta['cwp_rev_product_name'][0])) {
                        echo $cwp_review_stored_meta['cwp_rev_product_name'][0];
                    }

                    ?>"/>
                </li>
                <li>
                    <label for="cwp_rev_product_image" class="cwp_rev_product_image-title"><?php  _e( 'Product Image', 'cwp' ) ?></label>
                    <input type="text" name="cwp_rev_product_image" id="cwp_rev_product_image" value="<?php  if ( isset ( $cwp_review_stored_meta['cwp_rev_product_image'][0] ) ) echo $cwp_review_stored_meta['cwp_rev_product_image'][0]; ?>" />
                    <input type="button" id="cwp_rev_product_image-button" class="button" value="<?php  _e( 'Choose or Upload an Image', 'cwp' ) ?>" />
                    <p><?php  if (cwppos("cwppos_show_poweredby") == 'no' && !class_exists('CWP_PR_PRO_Core')) { ?> <del> <?php  _e("*If no image is provided, featured image is used","cwppos"); ?> </del> <span style="color:red;"><?php  _e("This is only available in the PRO version.","cwppos"); ?></span> <?php  } else _e("*If no image is provided, featured image is used"); ?>
                </li>
                <li>
                    <label for="cwp_product_affiliate_text"><?php  _e("Affiliate Button Text", "cwppos"); ?></label>
                    <input type="text" name="cwp_product_affiliate_text" id="cwp_product_affiliate_text" value="<?php

                    if(isset($cwp_review_stored_meta['cwp_product_affiliate_text'][0])) {
                        echo $cwp_review_stored_meta['cwp_product_affiliate_text'][0];
                    }

                    ?>"/>
                </li>
                <li>
                    <label for="cwp_product_affiliate_link"><?php  _e("Affiliate Link", "cwppos"); ?></label>
                    <input type="text" name="cwp_product_affiliate_link" id="cwp_product_affiliate_link" value="<?php

                    if(isset($cwp_review_stored_meta['cwp_product_affiliate_link'][0])) {
                        echo $cwp_review_stored_meta['cwp_product_affiliate_link'][0];
                    }

                    ?>"/>
                </li>

                <li>
                    <label for="cwp_cwp_rev_price"><?php  _e("Product Price", "cwppos"); ?></label>
                    <input type="text" name="cwp_rev_price" id="cwp_rev_price" value="<?php

                    if(isset($cwp_review_stored_meta['cwp_rev_price'][0])) {
                        echo $cwp_review_stored_meta['cwp_rev_price'][0];
                    }

                    ?>"/>
                </li>
               

            </ul>
        </div><!-- end .review-settings-group option -->
    </div><!-- end .review-settings group -->
    <div class="review-settings-notice">
        <h4><?php  _e("Product Options Setting", "cwppos"); ?></h4>
        <div class="preloadInfo"><?php  _e("Insert your options and their grades. Grading must be done <b><i>from 0 to 100</i></b>. In order to be able to automatically preload your settings from another posts, you need to <a href='http://www.readythemes.com/wp-product-review-pro/' target='_blank'>Upgrade to PRO</a>.", "cwppos"); ?></div>
        <?php  if(cwppos("cwppos_show_poweredby") === 'yes' || class_exists('CWP_PR_PRO_Core')) { ?>
            <a href="#" class="preload_info"><?php  _e("Preload Info","cwppos"); ?></a>
        <?php
        } else {
            $pageURL = admin_url('admin.php?page=cwppos_options#tab-upgrade_to_pro');
            $pageURL = str_replace(":80","",$pageURL);
            ?>
            <a href="<?php  echo $pageURL; ?>" target="_blank" class="preload_info"><?php  _e("Preload Info","cwppos"); ?></a>
        <?php  } ?>
    </div><!-- end .review-settings-notice -->
    <div class="review-settings-group">
        <div class="review-settings-group-option">
            <label for="option_1_content" class="option_label">1</label>
            <input type="text" name="option_1_content" id="option_1_content" class="option_content" placeholder="Option 1" value="<?php

            if(isset($cwp_review_stored_meta['option_1_content'][0])) {
                echo $cwp_review_stored_meta['option_1_content'][0];
            }

            ?>"/>
            <input type="text" name="option_1_grade" class="option_grade" placeholder="Grade" value="<?php

            if(isset($cwp_review_stored_meta['option_1_grade'][0])) {
                echo $cwp_review_stored_meta['option_1_grade'][0];
            }

            ?>"/>
        </div><!-- end .review-settings-group option -->
        <div class="review-settings-group-option">
            <label for="option_2_content" class="option_label">2</label>
            <input type="text" name="option_2_content" id="option_2_content" class="option_content" placeholder="Option 2" value="<?php

            if(isset($cwp_review_stored_meta['option_2_content'][0])) {
                echo $cwp_review_stored_meta['option_2_content'][0];
            }

            ?>"/>
            <input type="text" name="option_2_grade" class="option_grade" placeholder="Grade" value="<?php

            if(isset($cwp_review_stored_meta['option_2_grade'][0])) {
                echo $cwp_review_stored_meta['option_2_grade'][0];
            }

            ?>"/>
        </div><!-- end .review-settings-group option -->
        <div class="review-settings-group-option">
            <label for="option_3_content" class="option_label">3</label>
            <input type="text" name="option_3_content" id="option_3_content" class="option_content" placeholder="Option 3" value="<?php

            if(isset($cwp_review_stored_meta['option_3_content'][0])) {
                echo $cwp_review_stored_meta['option_3_content'][0];
            }

            ?>"/>
            <input type="text" name="option_3_grade" class="option_grade" placeholder="Grade" value="<?php

            if(isset($cwp_review_stored_meta['option_3_grade'][0])) {
                echo $cwp_review_stored_meta['option_3_grade'][0];
            }

            ?>"/>
        </div><!-- end .review-settings-group option -->
        <div class="review-settings-group-option">
            <label for="option_4_content" class="option_label">4</label>
            <input type="text" name="option_4_content" id="option_4_content" class="option_content" placeholder="Option 4" value="<?php

            if(isset($cwp_review_stored_meta['option_4_content'][0])) {
                echo $cwp_review_stored_meta['option_4_content'][0];
            }

            ?>"/>
            <input type="text" name="option_4_grade" class="option_grade" placeholder="Grade" value="<?php

            if(isset($cwp_review_stored_meta['option_4_grade'][0])) {
                echo $cwp_review_stored_meta['option_4_grade'][0];
            }

            ?>"/>
        </div><!-- end .review-settings-group option -->
        <div class="review-settings-group-option">
            <label for="option_5_content" class="option_label">5</label>
            <input type="text" name="option_5_content" id="option_5_content" class="option_content" placeholder="Option 5" value="<?php

            if(isset($cwp_review_stored_meta['option_5_content'][0])) {
                echo $cwp_review_stored_meta['option_5_content'][0];
            }

            ?>"/>
            <input type="text" name="option_5_grade" class="option_grade" placeholder="Grade" value="<?php

            if(isset($cwp_review_stored_meta['option_5_grade'][0])) {
                echo $cwp_review_stored_meta['option_5_grade'][0];
            }

            ?>"/>
        </div><!-- end .review-settings-group option -->
    </div><!-- end .review-settings group -->
    <div class="review-settings-notice">
        <h4><?php  _e("Pro Features", "cwppos"); ?></h4>
        <p style="margin:0;"><?php  _e("Insert product's pro features below.", "cwppos"); ?></p>
    </div><!-- end .review-settings-notice -->
    <div class="review-settings-group">
        <div class="review-settings-group-option">
            <label for="cwp_option_2_pro" class="option_label">1</label>
            <input type="text" name="cwp_option_1_pro" id="cwp_option_1_pro" class="option_content" placeholder="Option 1" value="<?php

            if(isset($cwp_review_stored_meta['cwp_option_1_pro'][0])) {
                echo $cwp_review_stored_meta['cwp_option_1_pro'][0];
            }

            ?>"/>
        </div><!-- end .review-settings-group option -->
        <div class="review-settings-group-option">
            <label for="cwp_option_2_pro" class="option_label">2</label>
            <input type="text" name="cwp_option_2_pro" id="cwp_option_2_pro" class="option_content" placeholder="Option 2" value="<?php

            if(isset($cwp_review_stored_meta['cwp_option_2_pro'][0])) {
                echo $cwp_review_stored_meta['cwp_option_2_pro'][0];
            }

            ?>"/>
        </div><!-- end .review-settings-group option -->
        <div class="review-settings-group-option">
            <label for="cwp_option_3_pro" class="option_label">3</label>
            <input type="text" name="cwp_option_3_pro" id="cwp_option_3_pro" class="option_content" placeholder="Option 3" value="<?php

            if(isset($cwp_review_stored_meta['cwp_option_3_pro'][0])) {
                echo $cwp_review_stored_meta['cwp_option_3_pro'][0];
            }

            ?>"/>
        </div><!-- end .review-settings-group option -->
        <div class="review-settings-group-option">
            <label for="cwp_option_4_pro" class="option_label">4</label>
            <input type="text" name="cwp_option_4_pro" id="cwp_option_4_pro" class="option_content" placeholder="Option 4" value="<?php

            if(isset($cwp_review_stored_meta['cwp_option_4_pro'][0])) {
                echo $cwp_review_stored_meta['cwp_option_4_pro'][0];
            }

            ?>"/>
        </div><!-- end .review-settings-group option -->
        <div class="review-settings-group-option">
            <label for="cwp_option_5_pro" class="option_label">5</label>
            <input type="text" name="cwp_option_5_pro" id="cwp_option_5_pro" class="option_content" placeholder="Option 5" value="<?php

            if(isset($cwp_review_stored_meta['cwp_option_5_pro'][0])) {
                echo $cwp_review_stored_meta['cwp_option_5_pro'][0];
            }

            ?>"/>
        </div><!-- end .review-settings-group option -->
    </div><!-- end .review-settings group -->
    <div class="review-settings-notice">
        <h4><?php  _e("Cons Features", "cwppos"); ?></h4>
        <p style="margin:0;"><?php  _e("Insert product's cons features below.", "cwppos"); ?></p>
    </div><!-- end .review-settings-notice -->
    <div class="review-settings-group">
        <div class="review-settings-group-option">
            <label for="cwp_option_1_cons" class="option_label">1</label>
            <input type="text" name="cwp_option_1_cons" id="cwp_option_1_cons" class="option_content" placeholder="Option 1" value="<?php

            if(isset($cwp_review_stored_meta['cwp_option_1_cons'][0])) {
                echo $cwp_review_stored_meta['cwp_option_1_cons'][0];
            }

            ?>"/>
        </div><!-- end .review-settings-group option -->
        <div class="review-settings-group-option">
            <label for="cwp_option_2_cons" class="option_label">2</label>
            <input type="text" name="cwp_option_2_cons" id="cwp_option_2_cons" class="option_content" placeholder="Option 2" value="<?php

            if(isset($cwp_review_stored_meta['cwp_option_2_cons'][0])) {
                echo $cwp_review_stored_meta['cwp_option_2_cons'][0];
            }

            ?>"/>
        </div><!-- end .review-settings-group option -->
        <div class="review-settings-group-option">
            <label for="cwp_option_3_cons" class="option_label">3</label>
            <input type="text" name="cwp_option_3_cons" id="cwp_option_3_cons" class="option_content" placeholder="Option 3" value="<?php

            if(isset($cwp_review_stored_meta['cwp_option_3_cons'][0])) {
                echo $cwp_review_stored_meta['cwp_option_3_cons'][0];
            }

            ?>"/>
        </div><!-- end .review-settings-group option -->
        <div class="review-settings-group-option">
            <label for="cwp_option_4_cons" class="option_label">4</label>
            <input type="text" name="cwp_option_4_cons" id="cwp_option_4_cons" class="option_content" placeholder="Option 4" value="<?php

            if(isset($cwp_review_stored_meta['cwp_option_4_cons'][0])) {
                echo $cwp_review_stored_meta['cwp_option_4_cons'][0];
            }

            ?>"/>
        </div><!-- end .review-settings-group option -->
        <div class="review-settings-group-option">
            <label for="cwp_option_5_cons" class="option_label">5</label>
            <input type="text" name="cwp_option_5_cons" id="cwp_option_5_cons" class="option_content" placeholder="Option 5" value="<?php

            if(isset($cwp_review_stored_meta['cwp_option_5_cons'][0])) {
                echo $cwp_review_stored_meta['cwp_option_5_cons'][0];
            }

            ?>"/>
        </div><!-- end .review-settings-group option -->
    </div><!-- end .review-settings group -->
    </div>
<?php
}

/**
 * Function for saving the review custom meta boxes.
 */
function cwp_review_meta_boxes_save($post_id){
    $is_autosave = wp_is_post_autosave( $post_id );
    $is_revision = wp_is_post_revision( $post_id );
    $is_valid_nonce = ( isset( $_POST[ 'cwp_meta_box_nonce' ] ) && wp_verify_nonce( $_POST[ 'cwp_meta_box_nonce' ], 'cwp_product_review_meta_box_nonce' ) ) ? 'true' :
        'false';

    if ( $is_autosave || $is_revision || !$is_valid_nonce ) {
        return;
    }


    if( isset( $_POST[ 'cwp_rev_product_name' ] ) ) {
        update_post_meta( $post_id, 'cwp_rev_product_name', sanitize_text_field( $_POST[ 'cwp_rev_product_name' ] ) );
    }

    if( isset( $_POST[ 'cwp_rev_price' ] ) ) {
        update_post_meta( $post_id, 'cwp_rev_price', sanitize_text_field( $_POST[ 'cwp_rev_price' ] ) );
    }

    


    if( isset( $_POST[ 'cwp_meta_box_check' ] ) ) {
        update_post_meta( $post_id, 'cwp_meta_box_check', sanitize_text_field( $_POST[ 'cwp_meta_box_check' ] ) );
    }


    if( isset( $_POST[ 'cwp_product_affiliate_text' ] ) ) {
        update_post_meta( $post_id, 'cwp_product_affiliate_text', sanitize_text_field( $_POST[ 'cwp_product_affiliate_text' ] ) );
    }


    if( isset( $_POST[ 'cwp_product_affiliate_link' ] ) ) {
        update_post_meta( $post_id, 'cwp_product_affiliate_link', esc_url( $_POST[ 'cwp_product_affiliate_link' ] ) );
    }

    if(  !empty($_POST[ 'cwp_bar_icon' ] )) {
        update_post_meta( $post_id, 'cwp_bar_icon', esc_url( $_POST[ 'cwp_bar_icon' ] ) );
    } else {
        update_post_meta( $post_id, 'cwp_bar_icon', "");
    }


    if( isset( $_POST[ 'option_1_content' ] ) ) {
        update_post_meta( $post_id, 'option_1_content', sanitize_text_field( $_POST[ 'option_1_content' ] ) );
    }


    if( isset( $_POST[ 'option_1_grade' ] ) ) {
        update_post_meta( $post_id, 'option_1_grade', sanitize_text_field( $_POST[ 'option_1_grade' ] ) );
    }


    if( isset( $_POST[ 'option_2_content' ] ) ) {
        update_post_meta( $post_id, 'option_2_content', sanitize_text_field( $_POST[ 'option_2_content' ] ) );
    }


    if( isset( $_POST[ 'option_2_grade' ] ) ) {
        update_post_meta( $post_id, 'option_2_grade', sanitize_text_field( $_POST[ 'option_2_grade' ] ) );
    }


    if( isset( $_POST[ 'option_3_content' ] ) ) {
        update_post_meta( $post_id, 'option_3_content', sanitize_text_field( $_POST[ 'option_3_content' ] ) );
    }


    if( isset( $_POST[ 'option_3_grade' ] ) ) {
        update_post_meta( $post_id, 'option_3_grade', sanitize_text_field( $_POST[ 'option_3_grade' ] ) );
    }


    if( isset( $_POST[ 'option_4_content' ] ) ) {
        update_post_meta( $post_id, 'option_4_content', sanitize_text_field( $_POST[ 'option_4_content' ] ) );
    }


    if( isset( $_POST[ 'option_4_grade' ] ) ) {
        update_post_meta( $post_id, 'option_4_grade', sanitize_text_field( $_POST[ 'option_4_grade' ] ) );
    }


    if( isset( $_POST[ 'option_5_content' ] ) ) {
        update_post_meta( $post_id, 'option_5_content', sanitize_text_field( $_POST[ 'option_5_content' ] ) );
    }


    if( isset( $_POST[ 'option_5_grade' ] ) ) {
        update_post_meta( $post_id, 'option_5_grade', sanitize_text_field( $_POST[ 'option_5_grade' ] ) );
    }


    if( isset( $_POST[ 'cwp_rev_product_image' ] )&&$_POST[ 'cwp_rev_product_image' ] !="" ) {
        update_post_meta( $post_id, 'cwp_rev_product_image', sanitize_text_field( $_POST[ 'cwp_rev_product_image' ] ) );
    }

    elseif (cwppos("cwppos_show_poweredby") == 'yes' || class_exists('CWP_PR_PRO_Core')) {
        $image="";

        if ( strlen( $img = get_the_post_thumbnail( $post_id, array( 150, 150 ) ) ) ) :
            $image_array = wp_get_attachment_image_src( get_post_thumbnail_id( $post_id ), 'optional-size' );
            $image = $image_array[0]; else :
            $post = get_post($post_id);
            $image = '';
            ob_start();
            ob_end_clean();
            $output = preg_match_all('/<img.+src=[\'"]([^\'"]+)[\'"].*>/i', $post->post_content, $matches);
            //$image = $matches[1][0];
            
        endif;
        update_post_meta( $post_id, 'cwp_rev_product_image', $image );
    }


    if( isset( $_POST[ 'cwp_option_1_pro' ] ) ) {
        update_post_meta( $post_id, 'cwp_option_1_pro', sanitize_text_field( $_POST[ 'cwp_option_1_pro' ] ) );
    }


    if( isset( $_POST[ 'cwp_option_2_pro' ] ) ) {
        update_post_meta( $post_id, 'cwp_option_2_pro', sanitize_text_field( $_POST[ 'cwp_option_2_pro' ] ) );
    }


    if( isset( $_POST[ 'cwp_option_3_pro' ] ) ) {
        update_post_meta( $post_id, 'cwp_option_3_pro', sanitize_text_field( $_POST[ 'cwp_option_3_pro' ] ) );
    }


    if( isset( $_POST[ 'cwp_option_4_pro' ] ) ) {
        update_post_meta( $post_id, 'cwp_option_4_pro', sanitize_text_field( $_POST[ 'cwp_option_4_pro' ] ) );
    }


    if( isset( $_POST[ 'cwp_option_5_pro' ] ) ) {
        update_post_meta( $post_id, 'cwp_option_5_pro', sanitize_text_field( $_POST[ 'cwp_option_5_pro' ] ) );
    }


    if( isset( $_POST[ 'cwp_option_1_cons' ] ) ) {
        update_post_meta( $post_id, 'cwp_option_1_cons', sanitize_text_field( $_POST[ 'cwp_option_1_cons' ] ) );
    }


    if( isset( $_POST[ 'cwp_option_2_cons' ] ) ) {
        update_post_meta( $post_id, 'cwp_option_2_cons', sanitize_text_field( $_POST[ 'cwp_option_2_cons' ] ) );
    }


    if( isset( $_POST[ 'cwp_option_3_cons' ] ) ) {
        update_post_meta( $post_id, 'cwp_option_3_cons', sanitize_text_field( $_POST[ 'cwp_option_3_cons' ] ) );
    }


    if( isset( $_POST[ 'cwp_option_4_cons' ] ) ) {
        update_post_meta( $post_id, 'cwp_option_4_cons', sanitize_text_field( $_POST[ 'cwp_option_4_cons' ] ) );
    }


    if( isset( $_POST[ 'cwp_option_5_cons' ] ) ) {
        update_post_meta( $post_id, 'cwp_option_5_cons', sanitize_text_field( $_POST[ 'cwp_option_5_cons' ] ) );
    }

    for($i=1; $i<6; $i++) {
        ${"option".$i."_grade"}= get_post_meta($post_id, "option_".$i."_grade", true);
    }

    $overall_score = "";
    $iter = 0;

    if(!empty($option1_grade) || $option1_grade === '0') {
        $overall_score += $option1_grade;
        $iter++;
    }


    if(!empty($option2_grade) || $option2_grade === '0' ) {
        $overall_score += $option2_grade;
        $iter++;
    }


    if(!empty($option3_grade) || $option3_grade === '0' ) {
        $overall_score += $option3_grade;
        $iter++;
    }


    if(!empty($option4_grade) || $option4_grade === '0' ) {
        $overall_score += $option4_grade;
        $iter++;
    }


    if(!empty($option5_grade) || $option5_grade === '0' ) {
        $overall_score += $option5_grade;
        $iter++;
    }


    if($iter == 0){
        $overall_score = 0;
    } else {
        $overall_score = $overall_score / $iter;
    }

    update_post_meta($post_id, 'option_overall_score', $overall_score/10);
}

/**
 * Hooks.
 */add_action( 'add_meta_boxes', 'cwp_review_meta_boxes' );
add_action( 'save_post', 'cwp_review_meta_boxes_save' );
?>