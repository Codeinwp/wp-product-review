<?php
/**
 * Abstract class for Models.
 * Defines inheritable utility methods.
 *
 * @package     WPPR
 * @subpackage  Models
 * @copyright   Copyright (c) 2017, Bogdan Preda
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       3.0.0
 */

/**
 * Class WPPR_Model_Abstract
 */
class WPPR_Model_Abstract {

	/**
	 * Get the global wppr option.
	 *
	 * @since   3.0.0
	 * @access  public
	 * @param   string $key The option key.
	 * @return mixed
	 */
	public function wppr_get_option( $key = '' ) {
		return WPPR_Options::instance()->get_var( $key );
	}

	/**
	 * Update a global wppr option.
	 *
	 * @since   3.0.0
	 * @access  public
	 * @param   string $key The option key.
	 * @param   string $value The option value.
	 * @return mixed
	 */
	public function wppr_set_option( $key = '', $value = '' ) {
		return WPPR_Options::instance()->set_var( $key, $value );
	}
}
