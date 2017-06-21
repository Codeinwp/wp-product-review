<?php
class WPPR_Model_Abstract {

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
}
