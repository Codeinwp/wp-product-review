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

_deprecated_file( __FILE__, '3.1.0', 'Bundle version', ' The shortcode is no longer supported using this version. You need to update to the latest premium and lite in order to use this feature.' );
$output = number_format( floor( $review_object->get_rating() ) / 10, 1 );
