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

if ( $review_object->wppr_get_option( 'wppr_rich_snippet' ) == 'yes' ) {
	$review   = $review_object->get_review_data();
	$currency = $review['currency'];
	$output   .= '
    <script type="application/ld+json">
    {
        "@context": "http://schema.org/",
      "@type": "Product",
      "name": "' . $review['name'] . '",
      "image": "' . $review['image']['thumb'] . '",
      "description": "' . get_the_excerpt( $review['id'] ) . '",
      "aggregateRating": {
        "@type": "AggregateRating",
        "bestRating": "10",
        "worstRating": "1",
        "ratingValue": "' . ( $review['rating'] / 10 ) . '",
        "reviewCount": "1"
      },
      "offers": {
        "@type": "Offer",
        "price": "' . number_format( $review['price'], 2 ) . '",
        "priceCurrency": "' . $currency . '",
        "availability": "http://schema.org/InStock",
        "seller": {
            "@type": "Organization",
          "name": "' . get_the_author() . '"
        }
      }
    }
    </script>';
}
