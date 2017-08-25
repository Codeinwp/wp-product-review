<?php
/**
 *  WP Prodact Review front page layout.
 *
 * @package     WPPR
 * @subpackage  Layouts
 * @global      WPPR_Review_Model $review_object The review object.
 * @copyright   Copyright (c) 2017, Bogdan Preda
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       3.0.0
 */

if ( $review_object->wppr_get_option( 'wppr_rich_snippet' ) == 'yes' ) {
	$output   .= '
    <script type="application/ld+json">
    {
        "@context": "http://schema.org/",
      "@type": "Product",
      "name": "' . $review_object->get_name() . '",
      "image": "' . $review_object->get_small_thumbnail() . '",
      "description": "' . get_the_excerpt( $review_object->get_ID() ) . '",';
	$comments = $review_object->get_comments_options();
	if ( intval( $review_object->wppr_get_option( 'cwppos_infl_userreview' ) ) > 0 && $comments > 0 ) {
		$output .= '"aggregateRating": {
				        "@type": "AggregateRating",
				        "bestRating": "10",
				        "worstRating": "1",
				        "ratingValue": "' . number_format( ( $review_object->get_rating() / 10 ), 2 ) . '",
				        "reviewCount": "' . count( $comments ) . '"
				    },';
	} else {
		$output .= '"review": {
					    "@type": "Review",
					    "reviewRating": {
					      "@type": "Rating",
					      "ratingValue": "' . number_format( ( $review_object->get_rating() / 10 ), 2 ) . '"
					    },
					    "name": "' . $review_object->get_name() . '",
					    "author": {
					      "@type": "Person",
					      "name": "' . get_the_author() . '"
					    },
                        "datePublished": "' . get_the_time( 'Y-m-d', $review_object->get_ID() ) . '",
					  },';
	}
	$output .= '"offers": {
        "@type": "Offer",
        "price": "' . number_format( $review_object->get_price(), 2 ) . '",
        "priceCurrency": "' . $review_object->get_currency() . '",
        "seller": {
            "@type": "Organization",
          "name": "' . get_the_author() . '"
        }
      }
    }
    </script>';
}
