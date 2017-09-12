<?php
/**
 *  WP Prodact Review front page layout.
 *
 * @package     WPPR
 * @subpackage  Layouts
 * @copyright   Copyright (c) 2017, Bogdan Preda
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       3.0.0
 * @deprecated
 */

$review = $review_object->get_review_data();
$sub_title_info = '';
$sub_title_info  = $review['price'];
if ( $sub_title_info != '' ) {
	$is_disabled = apply_filters( 'wppr_disable_price_richsnippet', false );
	$currency = preg_replace( '/[0-9.,]/', '', $review['price'] );
	if ( ! $is_disabled ) {
		$sub_title_info = '<span itemprop="offers" itemscope itemtype="http://schema.org/Offer">
                                <span itemprop="priceCurrency">' . $currency . '</span>
                                <span itemprop="price">' . $review['price'] . '</span>
                           </span>';
	}
}

$lightbox = '';
if ( $review_object->wppr_get_option( 'cwppos_lighbox' ) == 'no' ) {
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

$extra_class = ''; // TODO add check for embeded

$output = '
<div class="review-wu-grade">
    <div class="cwp-review-chart ' . $extra_class . '">
        <span>
            <div class="cwp-review-percentage" data-percent="' . $review['rating'] . '">
                <span class="cwp-review-rating">' . $review['comment_rating'] . '</span>
            </div>
        </span>
    </div><!-- end .chart -->
</div>
';
