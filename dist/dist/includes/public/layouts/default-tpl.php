<?php
/**
 * Legacy file used for assuring backwards compatibility with old pro version.
 *
 * @deprecated
 * @package WPPR
 */

ob_start();
include_once WPPR_PATH . '/includes/public/layouts/' . $review_object->get_template() . '.php';
if ( ! isset( $output ) ) {
	$output = '';
}
$output .= ob_get_contents();

ob_end_clean();
