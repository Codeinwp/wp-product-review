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
	$model = new WPPR_Query_Model();
	$model->find();
	$plugin        = new WPPR();
	$review_object = new WPPR_Review_Model( $post_id );
	$public        = new Wppr_Public( $plugin->get_plugin_name(), $plugin->get_version() );
	$public->load_review_assets( $review_object );
	$output = '';
	if ( $review_object->is_active() ) {
		$theme_template = get_template_directory() . '/wppr/default.php';
		if ( file_exists( $theme_template ) ) {
			include( $theme_template );
		} else {
			include( WPPR_PATH . '/includes/public/layouts/default-tpl.php' );
		}
		include( WPPR_PATH . '/includes/public/layouts/rich-json-ld.php' );
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
function cwppos() {
	$options = new WPPR_Options_Model();

	return $options->get_all();
}
