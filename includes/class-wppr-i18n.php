<?php
/**
 * Define the internationalization functionality
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @link       https://themeisle.com/
 * @since      3.0.0
 *
 * @package    WPPR
 * @subpackage WPPR/includes
 */

/**
 * Define the internationalization functionality.
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @since      3.0.0
 * @package    WPPR
 * @subpackage WPPR/includes
 * @author     ThemeIsle <friends@themeisle.com>
 */
class WPPR_I18n {


	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    3.0.0
	 */
	public function load_plugin_textdomain() {

		load_plugin_textdomain(
			'wp-product-review',
			false,
			dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/'
		);

	}



}
