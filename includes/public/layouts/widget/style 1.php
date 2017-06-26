<div class="wppr-prodlist">
    <?php
    $post_type = true;
    $show_image = true;

    while ( $cwp_products_loop->have_posts() ) : $cwp_products_loop->the_post();
    $post_id = get_the_ID();
    $review_model = new WPPR_Review_Model( $post_id );
    $review = $review_model->get_review_data();

    $product_image = $review['image']['thumb'];
    $product_title = ($post_type == true) ? $review['name']  :  get_the_title();
    $product_title_display  = $product_title;
    if ( strlen( $product_title_display ) > self::RESTRICT_TITLE_CHARS ) {
        $product_title_display  = substr( $product_title_display, 0, self::RESTRICT_TITLE_CHARS ) . '...';
    }

    $affiliate_link = get_post_meta( get_the_ID(), 'cwp_product_affiliate_link', true );
    $review_link = get_the_permalink();

    $showingImg = $show_image == true && ! empty( $product_image );
    ?>

    <div class="wppr-prodrow">
        <?php if ( $showingImg ) {  ?>
        <div class="wppr-prodrowleft">
            <a href="<?php echo $review_link; ?>" class="wppr-col" title="<?php echo $product_title; ?>">
                <img class="cwp_rev_image wppr-col" src="<?php echo $product_image;?>" alt="<?php echo $product_title; ?>"/>
            </a>
        </div>
        <?php
        }
        ?>
        <div class="wppr-prodrowright <?php echo $showingImg ? 'wppr-prodrowrightadjust' : ''?>">
            <p><strong><?php echo $product_title_display; ?></strong></p>
            <?php
            $review_score = $review['rating'];

            if ( ! empty( $review_score ) ) {
                if ( $instance['cwp_tp_rating_type'] == 'round' ) {
                    ?>
                    <div class="review-grade-widget wppr-col">
                        <div class="cwp-review-chart relative">
                            <div class="cwp-review-percentage" data-percent="<?php echo $review_score; ?>"><span></span></div>
                        </div><!-- end .chart -->
                    </div>
                    <div class="clear"></div>
                    <?php
                } else {
                    ?>
                    <div class="wppr-rating">
                        <div style="width:<?php echo $review_score; ?>%;"> <?php echo $review_score; ?></div>
                    </div>
                    <?php
                }
            }
            ?>
            <p class="wppr-style1-buttons">
                <?php
                $link   = "<a href='{$affiliate_link}' rel='nofollow' target='_blank' class='wppr-bttn'>" . __( $instance['cwp_tp_buynow'], 'cwppos' ) . '</a>';
                if ( ! empty( $instance['cwp_tp_buynow'] ) ) {
                    echo apply_filters( 'wppr_widget_style1_buynow_link', $link, get_the_ID(), $affiliate_link, $instance['cwp_tp_buynow'] );
                }

                $link   = "<a href='{$review_link}' rel='nofollow' target='_blank' class='wppr-bttn'>" . __( $instance['cwp_tp_readreview'], 'cwppos' ) . '</a>';
                if ( ! empty( $instance['cwp_tp_readreview'] ) ) {
                    echo apply_filters( 'wppr_widget_style1_readreview_link', $link, get_the_ID(), $review_link, $instance['cwp_tp_readreview'] );
                }
                ?>
            </p>
        </div>
        <div class="clear"></div>
    </div>
    <?php endwhile; ?>
    <?php wp_reset_postdata(); // reset the query ?>
</div>
