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
		 * @var WPPR_Options The one WPPR_Options istance.
		 * @since 3.0.3
		 */
		private static $instance;

		/**
		 * The main options array.
		 *
		 * @var array The options array.
		 * @since 3.0.3
		 */
		private $options;
		/**
		 * The option namespace.
		 *
		 * @var string $namespace The options namespace.
		 */
		private $namespace = 'cwppos_options';

		/**
		 * Init the main singleton instance class.
		 *
		 * @return WPPR_Options Return the instance class
		 */
		public static function instance() {
			if ( ! isset( self::$instance ) && ! ( self::$instance instanceof WPPR_Options ) ) {
				self::$instance = new WPPR_Options;
				self::$instance->init();
			}

			return self::$instance;
		}

		/**
		 *  Init the default values of the options class.
		 */
		public function init() {
			self::$instance->options = get_option( $this->namespace );
		}

		/**
		 * Get the key option value from DB.
		 *
		 * @param string $key The key name of the option.
		 *
		 * @return bool|mixed The value of the option
		 */
		public function get_var( $key ) {
			wppr_notice( 'Getting value for ' . $key );
			if ( isset( self::$instance->options[ $key ] ) ) {
				return self::$instance->options[ $key ];
			}

			return false;
		}

		/**
		 * Setter method for updating the options array.
		 *
		 * @param string $key The name of option.
		 * @param string $value The value of the option.
		 *
		 * @return bool|mixed The value of the option.
		 */
		public function set_var( $key, $value = '' ) {
			wppr_notice( 'Setting value for ' . $key . ' with ' . $value );
			self::$instance->options[ $key ] = apply_filters( 'wppr_pre_option' . $key, $value );

			return update_option( $this->namespace, self::$instance->options );

		}
	}
}