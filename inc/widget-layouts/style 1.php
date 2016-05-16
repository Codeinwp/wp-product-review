        <div class="wppr-prodlist">
<?php
		while($cwp_products_loop->have_posts()) : $cwp_products_loop->the_post();

			$product_image = wppr_get_image_id(get_the_ID(),get_post_meta(get_the_ID(), "cwp_rev_product_image", true),'thumbnail');
			$product_title = ($post_type==true) ? get_post_meta($cwp_products_loop->post->ID, "cwp_rev_product_name", true)  :  get_the_title();
            $product_title_display  = $product_title;
            if(strlen($product_title_display) > self::RESTRICT_TITLE_CHARS){
                $product_title_display  = substr($product_title_display, 0, self::RESTRICT_TITLE_CHARS) . "...";
            }
            $affiliate_link = get_post_meta(get_the_ID(), "cwp_product_affiliate_link", true);
            $review_link = get_the_permalink();

            $showingImg = $show_image==true&&!empty($product_image);
			?>

            <div class="wppr-prodrow">
                <?php if($showingImg){ ?>
                <div class="wppr-prodrowleft">
                    <a href="<?php echo $review_link; ?>" class="wppr-col" title="<?php echo $product_title; ?>">
                        <img class="cwp_rev_image wppr-col" src="<?php echo $product_image;?>" alt="<?php echo $product_title; ?>"/>
                    </a>
                </div>
                <?php
                    }
                ?>
                <div class="wppr-prodrowright <?php echo $showingImg ? "wppr-prodrowrightadjust" : ""?>">
                    <p><strong><?php echo $product_title_display; ?></strong></p>
                        <?php
                        for($i=1; $i<6; $i++) {
                            ${"option".$i."_content"} = get_post_meta($cwp_products_loop->post->ID, "option_".$i."_content", true);
                            //if(empty(${"option".$i."_content"})) { ${"option".$i."_content"} = __("Default Feature ".$i, "cwppos"); }
                        }
                        $review_score = cwppos_calc_overall_rating($cwp_products_loop->post->ID);
                        $review_score = $review_score['overall'];

                        if(!empty($review_score)) {
                            if($instance['cwp_tp_rating_type'] == "round"){
                        ?>
                        <div class="review-grade-widget wppr-col">
                            <div class="cwp-review-chart relative">
                                <div class="cwp-review-percentage" data-percent="<?php echo $review_score; ?>"><span></span></div>
                            </div><!-- end .chart -->
                        </div>
                        <div class="clear"></div>
                        <?php
                            }else{
                        ?>
                                    <div class="wppr-rating">
                                        <div style="width:<?php echo $review_score; ?>%;"> <?php echo $review_score; ?></div>
                                    </div>
                        <?php
                            }
                        }
                        ?>
                    <p class="wppr-style1-buttons">
                       <a href='<?php echo $affiliate_link;?>' rel='nofollow' target='_blank' class='wppr-bttn'><?php _e($instance['cwp_tp_buynow'], "cwppos");?></a> 
                       <a href='<?php echo $review_link;?>' rel='nofollow' class='wppr-bttn'><?php _e($instance['cwp_tp_readreview'], "cwppos");?></a> 
                    </p>
                </div>
                <div class="clear"></div>
            </div>
		<?php endwhile; ?>

		<?php wp_reset_postdata(); // reset the query ?>

    </div>