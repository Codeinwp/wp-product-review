<?php
/**
 * Singleton class layouts handling
 *
 * @package     WPPR
 * @subpackage  Layout
 * @copyright   Copyright (c) 2017, Marius Cristea
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       3.0.0
 */
if ( ! class_exists( 'WPPR_Layout_Manager' ) ) {
	/**
	 * Singleton class for options wrapper
	 */
	class WPPR_Layout_Manager {

		/**
		 * The main instance var.
		 *
		 * @var WPPR_Layout_Manager The one WPPR_Layout_Manager istance.
		 * @since 3.0.3
		 */
		private static $instance;
		/**
		 * The main instance var.
		 *
		 * @var WPPR_Layout_Manager The one WPPR_Layout_Manager istance.
		 * @since 3.0.3
		 */
		private $allowed_templates;

		/**
		 * Init the main singleton instance class.
		 *
		 * @return WPPR_Layout_Manager Return the instance class
		 */
		public static function instance() {
			if ( ! isset( self::$instance ) && ! ( self::$instance instanceof WPPR_Layout_Manager ) ) {
				self::$instance = new WPPR_Layout_Manager;
				self::$instance->init();
			}

			return self::$instance;
		}

		/**
		 *  Init the proprieties used in the manager.
		 */
		public function init() {
			$this->allowed_templates = array(
				'admin' => array( 'header-layout', 'settings', 'upsell-page' ),
			);
		}

		/**
		 * Render the admin template.
		 *
		 * @param string $template The name of the admin template;
		 */
		public function admin_layout( $template ) {
			if ( in_array( $template, $this->allowed_templates['admin'] ) ) {
				include_once WPPR_PATH . '/inc/admin-layouts/' . $template . '.php';
			}
		}
	}
}// End if().
