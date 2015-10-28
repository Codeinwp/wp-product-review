<?php
/**
 * Filters  of WPPR
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

	return '<h2 class="cwp-item"  itemprop="name"  >'.$name.'</h2>';

}

add_filter( 'wppr_option_name_html','wppr_option_name_html_filter',10,2 );

/**
 * Filter html tag for the options name
 *
 * @param int $id Id of the review.
 * @param string $name Name of the option.
 * @return string $html tag for the option name
 * @since 2.6.9
 */
function wppr_option_name_html_filter( $id = 0, $name = '' ) {

	return ( ! empty( $name ) )  ? '<h3 >'.$name.'</h3>' : '';

}

add_filter( 'wppr_review_pros_text','wppr_review_pros_text_filter',10,2 );

/**
 * Filter html tag for the pros text heading
 *
 * @param int $id Id of the review.
 * @param string $name The pros heading text.
 * @return string $html tag for the pros text
 * @since 2.6.9
 */
function wppr_review_pros_text_filter( $id = 0, $name = '' ) {

	return ( ! empty( $name ) )  ? '<h2>'.$name.'</h2>' : '';

}

add_filter( 'wppr_review_cons_text','wppr_review_cons_text_filter',10,2 );

/**
 * Filter html tag for the cons text heading
 *
 * @param int $id Id of the review.
 * @param string $name The cons heading text.
 * @return string $html tag for the cons text
 * @since 2.6.9
 */
function wppr_review_cons_text_filter( $id = 0, $name = '' ) {

	return ( ! empty( $name ) )  ? '<h2>'.$name.'</h2>' : '';

}
