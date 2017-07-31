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

if ( $this->review->wppr_get_option( 'wppr_rich_snippet' ) == 'yes' ) {
    $review = $this->review->get_review_data();
    $currency = preg_replace( '/[0-9.,]/', '', $review['price'] );

    $output .= '
    <script type="application/ld+json">
    {
        "@context": "http://schema.org/",
      "@type": "Product",
      "name": "' . $review['name'] . '",
      "image": "' . $review['image']['thumb'] . '",
      "description": "' . get_the_excerpt( $review['ID'] ) . '",
      "aggregateRating": {
        "@type": "AggregateRating",
        "bestRating": "10",
        "ratingValue": "' . $review['rating'] . '",
        "reviewCount": "' . count( $review['options'] ) * 10 . '"
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