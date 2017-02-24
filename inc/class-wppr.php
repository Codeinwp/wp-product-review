<?php
/**
 * The main loader file for wppr.
 *
 * @package WPPR
 * @copyright   Copyright (c) 2017, Marius Cristea
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since 3.0.0
 */
// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
if ( ! class_exists( 'WPPR' ) ) {
	/**
	 * Main WPPR class file.
	 */
	class WPPR {
		/**
		 * The singleton var.
		 *
		 * @var WPPR singleton var.
		 */
		private static $instance;
		/**
		 * The metabox editor logic.
		 *
		 * @var WPPR_Editor The metabox editor.
		 */
		public $editor;

		/**
		 * Init the WPPR instance.
		 *
		 * @return WPPR The WPPR instance.
		 */
		public static function init() {
			if ( ! isset( self::$instance ) && ! ( self::$instance instanceof WPPR ) ) {
				self::$instance = new WPPR;
				self::$instance->editor = new WPPR_Editor;
			}

			return self::$instance;
		}
	}
}
