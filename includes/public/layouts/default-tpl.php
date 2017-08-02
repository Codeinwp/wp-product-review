<?php
/**
 *  WP Prodact Review front page layout.
 *
 * @package     WPPR
 * @subpackage  Layouts
 * @copyright   Copyright (c) 2017, Bogdan Preda
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       3.0.0
 */

$review = $this->review->get_review_data();
$sub_title_info = '';
$sub_title_info  = $review['price'];
if ( $sub_title_info != '' ) {
	$is_disabled = apply_filters( 'wppr_disable_price_richsnippet', false );
	$currency = preg_replace( '/[0-9.,]/', '', $review['price'] ); // TODO move in model
	if ( ! $is_disabled ) {
		$sub_title_info = '<span>
                                <span>' . $currency . '</span>
                                <span>' . $review['price'] . '</span>
                           </span>';
	}
}

$lightbox = '';
if ( $this->review->wppr_get_option( 'cwppos_lighbox' ) == 'no' ) {
	$lightbox = 'data-lightbox="' . $review['image']['full'] . '"';
}
$image_link_url = $review['image']['full'];
$multiple_affiliates_class = 'affiliate-button';
$display_links_count = 0;
foreach ( $review['links'] as $title => $link ) {
	if ( $title != '' && $link != '' ) {
		if ( $review['click'] != 'image' ) {
			$image_link_url = $link;
		}
		$display_links_count++;
	}
}
if ( $display_links_count > 1 ) {
	$multiple_affiliates_class = 'affiliate-button2 affiliate-button';
}

$extra_class = '';

$output = '
<section id="review-statistics"  class="article-section">
    <div class="review-wrap-up  cwpr_clearfix" >
        <div class="cwpr-review-top cwpr_clearfix">
            <span><h2 class="cwp-item">' . $review['name'] . '</h2></span>
            <span class="cwp-item-price cwp-item">' . $sub_title_info . '</span>
        </div><!-- end .cwpr-review-top -->
        <div class="review-wu-left">
            <div class="rev-wu-image">
                <a href="' . $image_link_url . '" ' . $lightbox . '  rel="nofollow" target="_blank"><img src="' . $review['image']['thumb'] . '" alt="' . do_shortcode( $review['name'] ) . '" class="photo photo-wrapup wppr-product-image"  /></a>
            </div><!-- end .rev-wu-image -->
            <div class="review-wu-grade">
                <div class="cwp-review-chart ' . $extra_class . '">
                    <span>
                        <div class="cwp-review-percentage" data-percent="' . $review['rating'] . '">
                            <span class="cwp-review-rating">' . $review['rating'] . '</span>
                        </div>
                    </span>
                </div><!-- end .chart -->
            </div><!-- end .review-wu-grade -->
            <div class="review-wu-bars">
                ';
if ( isset( $review['options'] ) && ! empty( $review['options'] ) ) {
	foreach ( $review['options'] as $option ) {
		$output .= '
                        <div class="rev-option" data-value=' . $option['value'] . '>
                            <div class="cwpr_clearfix">
                                <h3>' . apply_filters( 'wppr_option_name_html', $option['name'] ) . '</h3>
                                <span>' . round( $option['value'] / 10 ) . '/10</span>
                            </div>
                            <ul class="cwpr_clearfix"></ul>
                        </div>
                    ';
	}
}
			$output .= '
            </div><!-- end .review-wu-bars -->
        </div><!-- end .review-wu-left -->
        <div class="review-wu-right">
            <div class="pros">
            <h2>' .
                // @codingStandardsIgnoreStart
                apply_filters( 'wppr_review_pros_text', __( $options_model->wppr_get_option( 'cwppos_pros_text' ), 'wp-product-review' ) )
                // @codingStandardsIgnoreEnd
				. '</h2>
            <ul>';
if ( isset( $review['pros'] ) && ! empty( $review['pros'] ) ) {
	foreach ( $review['pros'] as $pro ) {
		$output .= '<li>' . $pro . '</li>';
	}
}
				$output .= '
                </ul>
            </div><!-- end .pros -->
            <div class="cons">
            <h2>' .
                    // @codingStandardsIgnoreStart
                    apply_filters( 'wppr_review_cons_text', __( $options_model->wppr_get_option( 'cwppos_cons_text' ), 'wp-product-review' ) )
                    // @codingStandardsIgnoreEnd
					. '</h2>
            <ul>';
if ( isset( $review['cons'] ) && ! empty( $review['cons'] ) ) {
	foreach ( $review['cons'] as $con ) {
		$output .= '<li>' . $con . '</li>';
	}
}
				$output .= '
                </ul>
            </div>
        </div><!-- end .review-wu-right -->
    </div><!-- end .review-wrap-up -->
</section><!-- end #review-statistics -->
';
if ( ! empty( $review['links'] ) ) {
	foreach ( $review['links'] as $title => $link ) {
	    if ( $title != '' && $link != '' ) {
			$output .= '
            <div class="' . $multiple_affiliates_class . '">
                <a href="' . $link . '" rel="nofollow" target="_blank"><span>' . $title . '</span> </a>
            </div><!-- end .affiliate-button -->
            ';
		}
	}
}
