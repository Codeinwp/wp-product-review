<?php
function cwp_pac_before_content( $content ) {
	global $post;
	$cwp_review_stored_meta = get_post_meta( $post->ID );
	$return_string = cwppos_show_review();

	global $page;
	if ( isset( $cwp_review_stored_meta['cwp_meta_box_check'][0] ) && $cwp_review_stored_meta['cwp_meta_box_check'][0] == 'Yes' && (is_single() || is_page()) && $page === 1 ) {
		if ( cwppos( 'cwppos_show_reviewbox' ) == 'yes' ) { return $content . $return_string; }
		if ( cwppos( 'cwppos_show_reviewbox' ) == 'no' ) { return $return_string . $content; }
		 return $content;
	} else {
		return $content; }
}

$currentTheme = wp_get_theme();
if ( $currentTheme->get( 'Name' ) !== 'Bookrev' && $currentTheme->get( 'Name' ) !== 'Book Rev Lite' ) {

	add_filter( 'the_content', 'cwp_pac_before_content' );
}
