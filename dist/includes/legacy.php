<?php
/**
 *
 * Legacy files used for backwards compatibility.
 *
 * @package WPPR
 */

/**
 * Legacy function used for review box.
 *
 * @param int $post_id The post id.
 *
 * @deprecated
 *
 * @return string The review string.
 */
function cwppos_show_review( $post_id ) {
	$plugin        = new WPPR();
	$review_object = new WPPR_Review_Model( $post_id );
	$public        = new Wppr_Public( $plugin->get_plugin_name(), $plugin->get_version() );
	$public->load_review_assets( $review_object );
	$output = '';
	if ( $review_object->is_active() ) {
		$template = new WPPR_Template();
		$output  .= $template->render(
			'default',
			array(
				'review_object' => $review_object,
			),
			false
		);

		$output .= $template->render(
			'rich-json-ld',
			array(
				'review_object' => $review_object,
			),
			false
		);
	}

	return $output;
}

/**
 * Legacy functions for return all the options.
 *
 * @deprecated
 *
 * @return mixed Array of global options.
 */
function cwppos( $option = null ) {
	$options = new WPPR_Options_Model();

	if ( is_null( $option ) ) {
		return $options->get_all();
	}
	return $options->wppr_get_option( $option );
}
