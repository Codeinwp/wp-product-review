<?php
/**
 * Filters  of WPPR
 *
 * @package WPPR
 * @author ThemeIsle
 * @since 2.6.9
 */

add_filter( 'wppr_review_product_name','wppr_review_product_name_filter' );

/**
 * Filter name of the review
 *
 * @param int $id Id of the review.
 * @return string $name Name of the review
 * @since 2.6.9
 */
function wppr_review_product_name_filter( $id = '' ) {

	$name = get_post_meta( $id, 'cwp_rev_product_name', true );
	$name = ( empty( $name ) ) ? get_the_title( $id ) : $name;
	return apply_filters( 'wppr_review_product_name_html' , $name );
}

add_filter( 'wppr_review_product_name_html','wppr_review_product_name_html_filter' );

/**
 * Filter html tag for the review name
 *
 * @param string $name name of the review.
 * @return string $html tag for the title
 * @since 2.6.9
 */
function wppr_review_product_name_html_filter( $name = '' ) {

	return '<h2 class="cwp-item"  itemprop="name"  >' . $name . '</h2>';

}

add_filter( 'wppr_option_name_html','wppr_option_name_html_filter',10,2 );

/**
 * Filter html tag for the options name
 *
 * @param int    $id Id of the review.
 * @param string $name Name of the option.
 * @return string $html tag for the option name
 * @since 2.6.9
 */
function wppr_option_name_html_filter( $id = 0, $name = '' ) {

	return ( ! empty( $name ) )  ? '<h3 >' . $name . '</h3>' : '';

}

add_filter( 'wppr_review_pros_text','wppr_review_pros_text_filter',10,2 );

/**
 * Filter html tag for the pros text heading
 *
 * @param int    $id Id of the review.
 * @param string $name The pros heading text.
 * @return string $html tag for the pros text
 * @since 2.6.9
 */
function wppr_review_pros_text_filter( $id = 0, $name = '' ) {

	return ( ! empty( $name ) )  ? '<h2>' . $name . '</h2>' : '';

}

add_filter( 'wppr_review_cons_text','wppr_review_cons_text_filter',10,2 );

/**
 * Filter html tag for the cons text heading
 *
 * @param int    $id Id of the review.
 * @param string $name The cons heading text.
 * @return string $html tag for the cons text
 * @since 2.6.9
 */
function wppr_review_cons_text_filter( $id = 0, $name = '' ) {

	return ( ! empty( $name ) )  ? '<h2>' . $name . '</h2>' : '';

}


/**
 *  Add pro pointer for the amazon upsell link
 *
 * @param array $p The pointers array.
 * @return array The altered pointers array with amazon upsell.
 * @since 2.9.0
 */


add_filter( 'wppr_admin_pointers-post', 'wppr_admin_pointers' );
function wppr_admin_pointers( $p ) {
	$p['amazon_upsell'] = array(
		'target' => '#wppr_product_affiliate_link_upsell',
		'options' => array(
			'content' => sprintf( '<h3> %s </h3> <p> %s </p>',
				apply_filters( 'wppr_amazon_title_upsell_text', null ),
				apply_filters( 'wppr_amazon_body_upsell_text', null )
			),
			'position' => array( 'edge' => 'left', 'align' => 'left' ),
		),
	);
	return $p;
}

/**
 *  Sanitize links in the options review panel
 *
 * @param string $text The raw value.
 * @return string The sanitized value.
 * @since 2.9.6
 */
add_filter( 'wppr_sanitize_link1', 'wppr_sanitize_link' );
add_filter( 'wppr_sanitize_link2', 'wppr_sanitize_link' );

function wppr_sanitize_link( $text ) {
	return esc_url( $text );
}


/**
 *  Sanitize texts in the options review panel
 *
 * @param string $text The raw value.
 * @return string The sanitized value.
 * @since 2.9.6
 */
add_filter( 'wppr_sanitize_product_price', 'wppr_sanitize_text' );
add_filter( 'wppr_sanitize_product_title', 'wppr_sanitize_text' );
add_filter( 'wppr_sanitize_product_image', 'wppr_sanitize_text' );


function wppr_sanitize_text( $text ) {
	return sanitize_text_field( $text );
}
