<?php
/**
 * A list of utility functions.
 *
 * @package     WPPR
 * @subpackage  Helpers
 * @copyright   Copyright (c) 2017, Marius Cristea
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       3.0.0
 */
/**
 * Helper function to report a message as info.
 *
 * @param string $msg The message to write as info.
 */
function wppr_notice( $msg ) {
	WPPR_Logger::instance()->notice( $msg );
}

/**
 * Report a message as error.
 *
 * @param string $msg The message to write.
 */
function wppr_error( $msg ) {
	WPPR_Logger::instance()->error( $msg );
}

/**
 * Report a message as warning.
 *
 * @param string $msg The warning msg.
 */
function wppr_warning( $msg ) {
	WPPR_Logger::instance()->warning( $msg );
}

/**
 * Get the global wppr option.
 *
 * @param string $key The option key.
 *
 * @return mixed The global wppr option
 */
function wppr_get_option( $key = '' ) {
	return WPPR_Options::instance()->get_var( $key );
}

/**
 * Update a global wppr option.
 *
 * @param string $key The option key.
 * @param string $value The option value.
 *
 * @return mixed Either was updated or not.
 */
function wppr_set_option( $key = '', $value = '' ) {
	return WPPR_Options::instance()->set_var( $key, $value );
}

/**
 * Helper function for fields class.
 *
 * @return WPPR_Html_Fields singleton.
 */
function wppr_fields() {
	return WPPR_Html_Fields::init();
}

/**
 * Helper function for render admin template.
 *
 * @return WPPR_Layout_Manager singleton.
 */
function wppr_admin_template( $template ) {
	WPPR_Layout_Manager::instance()->admin_layout( $template );
}