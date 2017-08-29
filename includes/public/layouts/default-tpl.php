<?php
/**
 *  WP Prodact Review front page layout.
 *
 * @package     WPPR
 * @subpackage  Layouts
 * @global      WPPR_Review_Model $review_object The inherited review object.
 * @copyright   Copyright (c) 2017, Bogdan Preda
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       3.0.0
 */

$price_raw = $review_object->get_price_raw();

$lightbox = '';
if ( $review_object->get_click() == 'image' ) {
	$lightbox = 'data-lightbox="' . esc_url( $review_object->get_small_thumbnail() ) . '"';
}
$links                     = $review_object->get_links();
$multiple_affiliates_class = 'affiliate-button';
$links                     = array_filter( $links );
if ( count( $links ) > 1 ) {
	$multiple_affiliates_class = 'affiliate-button2 affiliate-button';
}
$output = '
<section id="review-statistics"  class="article-section">
    <div class="review-wrap-up  cwpr_clearfix" >
        <div class="cwpr-review-top cwpr_clearfix">
            <span><h2 class="cwp-item">' . esc_html( $review_object->get_name() ) . '</h2></span>
            <span class="cwp-item-price cwp-item"><span>
                                <span>' . esc_html( empty( $price_raw ) ? '' : $price_raw ) . '</span>
                           </span></span>
        </div><!-- end .cwpr-review-top -->
        <div class="review-wu-left">
            <div class="rev-wu-image">
                <a href="' . esc_url( reset( $links ) ) . '" ' . $lightbox . '  rel="nofollow" target="_blank"><img src="' . esc_attr( $review_object->get_small_thumbnail() ) . '" alt="' . esc_attr( $review_object->get_name() ) . '" class="photo photo-wrapup wppr-product-image"  /></a>
            </div><!-- end .rev-wu-image -->
            <div class="review-wu-grade">
                <div class="cwp-review-chart ">
                    <span>
                        <div class="cwp-review-percentage" data-percent="' . esc_attr( $review_object->get_rating() ) . '">
                            <span class="cwp-review-rating">' . esc_html( $review_object->get_rating() ) . '</span>
                        </div>
                    </span>
                </div><!-- end .chart -->
            </div><!-- end .review-wu-grade -->
            <div class="review-wu-bars">
                ';
foreach ( $review_object->get_options() as $option ) {
	$output .= '
                        <div class="rev-option" data-value=' . $option['value'] . '>
                            <div class="cwpr_clearfix">
                                <h3>' . esc_html( apply_filters( 'wppr_option_name_html', $option['name'] ) ) . '</h3>
                                <span>' . esc_html( round( $option['value'] / 10 ) ) . '/10</span>
                            </div>
                            <ul class="cwpr_clearfix"></ul>
                        </div>
                    ';
}
$output .= '
            </div><!-- end .review-wu-bars -->
        </div><!-- end .review-wu-left -->
        <div class="review-wu-right">
            <div class="pros">
            <h2>' .
		   esc_html( apply_filters( 'wppr_review_pros_text', $review_object->wppr_get_option( 'cwppos_pros_text' ) ) )
		   . '</h2>
            <ul>';
foreach ( $review_object->get_pros() as $pro ) {
	$output .= '<li>' . esc_html( $pro ) . '</li>';
}
$output .= '
                </ul>
            </div><!-- end .pros -->
            <div class="cons">
            <h2>' .
		   esc_html( apply_filters( 'wppr_review_cons_text', $review_object->wppr_get_option( 'cwppos_cons_text' ) ) )
		   .
		   '</h2>
            <ul>';
foreach ( $review_object->get_cons() as $con ) {
	$output .= '<li>' . esc_html( $con ) . '</li>';
}
$output .= '
                </ul>
            </div>
        </div><!-- end .review-wu-right -->
    </div><!-- end .review-wrap-up -->
</section><!-- end #review-statistics -->
';

foreach ( $links as $title => $link ) {
	if ( ! empty( $title ) && ! empty( $link ) ) {
		$output .= '
            <div class="' . esc_attr( $multiple_affiliates_class ) . '">
                <a href="' . esc_url( $link ) . '" rel="nofollow" target="_blank"><span>' . esc_html( $title ) . '</span> </a>
            </div><!-- end .affiliate-button -->
            ';
	}
}
