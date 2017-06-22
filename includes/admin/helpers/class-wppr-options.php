<?php
/**
 * Singleton class used for options
 *
 * Singleton class used for options managing.
 *
 * @package     WPPR
 * @subpackage Options
 * @copyright   Copyright (c) 2017, Marius Cristea
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       3.0.0
 */
if ( ! class_exists( 'WPPR_Options' ) ) {
	/**
	 * Singleton class for options wrapper
	 */
	class WPPR_Options {

		/**
		 * The main instance var.
		 *
		 * @since   3.0.0
		 * @access  private
		 * @var WPPR_Options $instance The one WPPR_Options istance.
		 */
		private static $instance;

		/**
		 * The main options array.
		 *
		 * @since   3.0.0
		 * @access  private
		 * @var array $options The options array.
		 */
		private $options;

		/**
		 * The option namespace.
		 *
		 * @since   3.0.0
		 * @access  private
		 * @var string $namespace The options namespace.
		 */
		private $namespace = 'cwppos_options';

		/**
		 * The logger class.
		 *
		 * @since   3.0.0
		 * @access  private
		 * @var WPPR_Logger $logger The logger utility class.
		 */
		private $logger;

		/**
		 * Init the main singleton instance class.
		 *
		 * @since   3.0.0
		 * @access  public
		 * @return WPPR_Options
		 */
		public static function instance() {
			if ( ! isset( self::$instance ) && ! ( self::$instance instanceof WPPR_Options ) ) {
				self::$instance = new WPPR_Options;
				self::$instance->init();
			}

			return self::$instance;
		}

		/**
		 * Init the default values of the options class.
		 *
		 * @since   3.0.0
		 * @access  public
		 */
		public function init() {
			self::$instance->options = get_option( $this->namespace );
			self::$instance->logger = new WPPR_Logger();
		}

		/**
		 * Get the key option value from DB.
		 *
		 * @since   3.0.0
		 * @access  public
		 * @param   string $key The key name of the option.
		 * @return bool|mixed
		 */
		public function get_var( $key ) {
			self::$instance->logger->notice( 'Getting value for ' . $key );
			if ( isset( self::$instance->options[ $key ] ) ) {
				return self::$instance->options[ $key ];
			}

			return false;
		}

		/**
		 * Setter method for updating the options array.
		 *
		 * @since   3.0.0
		 * @access  public
		 * @param   string $key The name of option.
		 * @param   string $value The value of the option.
		 * @return bool|mixed
		 */
		public function set_var( $key, $value = '' ) {
			self::$instance->logger->notice( 'Setting value for ' . $key . ' with ' . $value );
			if ( ! isset( self::$instance->options[ $key ] ) ) {
				self::$instance->options[ $key ] = '';
			}
			self::$instance->options[ $key ] = apply_filters( 'wppr_pre_option' . $key, $value );

			return update_option( $this->namespace, self::$instance->options );

		}
	}
}// End if().
