        <div class="prodlist">
<?php
		while($cwp_top_products_loop->have_posts()) : $cwp_top_products_loop->the_post();

			$product_image = wppr_get_image_id(get_the_ID(),get_post_meta(get_the_ID(), "cwp_rev_product_image", true),'wppr_widget_image');
			$product_title = ($post_type==true) ? get_post_meta($cwp_top_products_loop->post->ID, "cwp_rev_product_name", true)  :  get_the_title();
            $affiliate_link = get_post_meta(get_the_ID(), "cwp_product_affiliate_link", true);
            $review_link = get_the_permalink();
			?>

            <div class="prodrow">
                <div class="prodrowleft">
                    <a href="<?php echo $review_link; ?>" class="wppr-col" title="<?php echo $product_title; ?>">
                        <?php
		                    if ($show_image==true&&!empty($product_image)) {
		                ?>
                        <img class="cwp_rev_image wppr-col" src="<?php echo $product_image;?>" alt="<?php echo $product_title; ?>"/>
                        <?php
                            }
                        ?>
                    </a>
                </div>
                <div class="prodrowright">
                    <p><strong><?php echo $product_title; ?></strong></p>
                        <?php
                        for($i=1; $i<6; $i++) {
                            ${"option".$i."_content"} = get_post_meta($cwp_top_products_loop->post->ID, "option_".$i."_content", true);
                            //if(empty(${"option".$i."_content"})) { ${"option".$i."_content"} = __("Default Feature ".$i, "cwppos"); }
                        }
                        $review_score = cwppos_calc_overall_rating($cwp_top_products_loop->post->ID);
                        $review_score = $review_score['overall'];

                        if(!empty($review_score)) {
                            if($instance['cwp_tp_rating_type'] == "round"){
                        ?>
                        <div class="review-grade-widget wppr-col">
                            <div class="cwp-review-chart">
                                <div class="cwp-review-percentage" data-percent="<?php echo $review_score; ?>"><span></span></div>
                            </div><!-- end .chart -->
                        </div>
                        <div class="clear"></div>
                        <?php
                            }else{
                        ?>
                            <p>
                                <div class='rating'><div style='width:<?php echo $review_score; ?>%;'><?php echo $review_score; ?></div></div>
                            </p>
                        <?php
                            }
                        }
                        ?>
                    <p>
                       <a href='<?php echo $affiliate_link;?>' rel='nofollow' target='_blank' class='bttn'><?php _e($instance['cwp_tp_buynow'], "cwppos");?></a> 
                       <a href='<?php echo $review_link;?>' rel='nofollow' target='_blank' class='bttn'><?php _e($instance['cwp_tp_readreview'], "cwppos");?></a> 
                    </p>
                </div>
                <div class="clear"></div>
            </div>
		<?php endwhile; ?>

		<?php wp_reset_postdata(); // reset the query ?>

    </div>