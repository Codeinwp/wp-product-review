<?php
/**
 * The logger singleton class
 *
 * The logger class used for error/warning/notices messsages.
 *
 * @package     WPPR
 * @subpackage  Logger
 * @copyright   Copyright (c) 2017, Marius Cristea
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       3.0.0
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
if ( ! class_exists( 'WPPR_Logger' ) ) :
	/**
	 * Class WPPR_logger.
	 */
	final class WPPR_Logger {
		/**
		 * The only WPPR_Logger classs.
		 *
		 * @var WPPR_Logger The singleton WPPR_Logger.
		 */
		private static $instance;

		/**
		 * Return the WPPR_Logger instance.
		 *
		 * @return WPPR_Logger The only WPPR_Logger instance.
		 */
		public static function instance() {
			if ( ! isset( self::$instance ) && ! ( self::$instance instanceof WPPR_logger ) ) {
				self::$instance = new WPPR_logger;
			}

			return self::$instance;
		}

		/**
		 * Report a message as warning.
		 *
		 * @param string $msg The message to report.
		 */
		public function warning( $msg = '' ) {
			self::$instance->message( $msg, 'warning' );
		}

		/**
		 * The error msg to report.
		 *
		 * @param string $msg The error msg.
		 */
		public function error( $msg = '' ) {
			self::$instance->message( $msg, 'error' );
		}

		/**
		 * Report a message as notice.
		 *
		 * @param string $msg The message to report as notice.
		 */
		public function notice( $msg = '' ) {
			self::$instance->message( $msg, 'notice' );
		}

		/**
		 * Report a message as error|warning|notice.
		 *
		 * @param string $msg The message.
		 * @param string $type The type of the message.
		 */
		public function message( $msg, $type ) {
			$type   = strtoupper( $type );
			$msg    = $type . ' : ' . $msg;
			$bt     = debug_backtrace();
			$caller = array_shift( $bt );
			$caller = array_shift( $bt );
			$caller = array_shift( $bt );
			$msg    = $msg . ' [ ' . $caller['file'];
			$msg    = $msg . ' : ' . $caller['line'] . ' ]';
			if ( WPPR_DEBUG ) {
				error_log( $msg );
			}
		}
	}
endif;
