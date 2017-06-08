<?php
/**
 * The logger class
 *
 * The logger class used for error/warning/notices messsages.
 *
 * @package     WPPR
 * @subpackage  Logger
 * @copyright   Copyright (c) 2017, Marius Cristea
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       3.0.0
 */

/**
 * Class WPPR_Logger
 */
class WPPR_Logger {

    /**
     * WPPR_Logger constructor.
     */
    public function __construct() {}

    /**
     * Report a message as warning.
     *
     * @since   3.0.0
     * @access  public
     * @param   string $msg The message to report.
     */
    public function log_warning( $msg = '' ) {
        $this->message( $msg, 'warning' );
    }

    /**
     * The error msg to report.
     *
     * @since   3.0.0
     * @access  public
     * @param   string $msg The error msg.
     */
    public function log_error( $msg = '' ) {
        $this->message( $msg, 'error' );
    }

    /**
     * Report a message as notice.
     *
     * @since   3.0.0
     * @access  public
     * @param   string $msg The message to report as notice.
     */
    public function log_notice( $msg = '' ) {
        $this->message( $msg, 'notice' );
    }

    /**
     * Report a message as error|warning|notice.
     *
     * @since   3.0.0
     * @access  private
     * @param   string  $msg    The message.
     * @param   string  $type   The type of the message.
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
        if ( WPPR_DEBUG ) {
            error_log( $msg );
        }
    }
}