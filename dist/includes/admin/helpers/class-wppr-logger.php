<?php
/**
 * WPPR Logger Controller
 *
 * @package     WPPR
 * @subpackage  Helpers
 * @copyright   Copyright (c) 2017, Bogdan Preda
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       3.0.0
 */

/**
 * Class WPPR_Logger.
 */
class WPPR_Logger {

	/**
	 * Report a message as warning.
	 *
	 * @since   3.0.0
	 * @access  public
	 *
	 * @param   string $msg The message to report.
	 */
	public function warning( $msg = '' ) {
		$this->message( $msg, 'warning' );
	}

	/**
	 * Report a message as error|warning|notice.
	 *
	 * @since   3.0.0
	 * @access  private
	 *
	 * @param   string $msg The message.
	 * @param   string $type The type of the message.
	 */
	private function message( $msg, $type ) {

		if ( ! defined( 'WPPR_DEBUG' ) ) {
			return;
		}
		if ( ! WPPR_DEBUG ) {
			return;
		}
		$type   = strtoupper( $type );
		$msg    = $type . ' : ' . $msg;
		$bt     = debug_backtrace();
		$caller = array_shift( $bt );
		$caller = array_shift( $bt );
		$caller = array_shift( $bt );
		$msg    = $msg . ' [ ' . $caller['file'];
		$msg    = $msg . ' : ' . $caller['line'] . ' ]';
		error_log( $msg );

	}

	/**
	 * The error msg to report.
	 *
	 * @since   3.0.0
	 * @access  public
	 *
	 * @param   string $msg The error msg.
	 */
	public function error( $msg = '' ) {
		$this->message( $msg, 'error' );
	}

	/**
	 * Report a message as notice.
	 *
	 * @since   3.0.0
	 * @access  public
	 *
	 * @param   string $msg The message to report as notice.
	 */
	public function notice( $msg = '' ) {
		$this->message( $msg, 'notice' );
	}
}
