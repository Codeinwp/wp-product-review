<?php
/**
 * Class WPPR_Logger.
 */
class WPPR_Logger {

	public function __construct() {}

	/**
	 * Report a message as warning.
	 *
	 * @param string $msg The message to report.
	 */
	public function warning( $msg = '' ) {
		$this->message( $msg, 'warning' );
	}

	/**
	 * The error msg to report.
	 *
	 * @param string $msg The error msg.
	 */
	public function error( $msg = '' ) {
		$this->message( $msg, 'error' );
	}

	/**
	 * Report a message as notice.
	 *
	 * @param string $msg The message to report as notice.
	 */
	public function notice( $msg = '' ) {
		$this->message( $msg, 'notice' );
	}

	/**
	 * Report a message as error|warning|notice.
	 *
	 * @param string $msg The message.
	 * @param string $type The type of the message.
	 */
	private function message( $msg, $type ) {
		$type   = strtoupper( $type );
		$msg    = $type . ' : ' . $msg;
		$bt     = debug_backtrace();
		$caller = array_shift( $bt );
		$caller = array_shift( $bt );
		$caller = array_shift( $bt );
		$msg    = $msg . ' [ ' . $caller['file'];
		$msg    = $msg . ' : ' . $caller['line'] . ' ]';
		if ( defined( 'WPPR_DEBUG' ) && WPPR_DEBUG ) {
			error_log( $msg );
		}
	}
}
