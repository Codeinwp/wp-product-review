<?php
/**
 * Main loader file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://themeisle.com/
 * @since             3.0.0
 * @package           Wppr
 *
 * @wordpress-plugin
 * Plugin Name:       WP Product Review
 * Plugin URI:        https://themeisle.com/plugins/wp-product-review/
 * Description:       The highest rated and most complete review plugin, now with rich snippets support. Easily turn your basic posts into in-depth reviews.
 * Version:           3.0.0
 * Author:            ThemeIsle
 * Author URI:        https://themeisle.com/
 * Requires at least: 3.5
 * Tested up to:      4.6
 * Stable tag:        trunk
 * License:           GPLv2 or later
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       wp-product-review
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-wppr-activator.php
 */
function activate_wppr() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-wppr-activator.php';
	Wppr_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-wppr-deactivator.php
 */
function deactivate_wppr() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-wppr-deactivator.php';
	Wppr_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_wppr' );
register_deactivation_hook( __FILE__, 'deactivate_wppr' );

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    3.0.0
 */
function run_wppr() {

	define( 'WPPR_LITE_VERSION', '3.0.0' );
	define( 'WPPR_PATH', dirname( __FILE__ ) );
	define( 'WPPR_SLUG', 'wppr' );
	define( 'WPPR_URL', plugins_url( 'wp-product-review' ) );

	$plugin = new WPPR();
	$plugin->run();

}

require( 'class-wppr-autoloader.php' );
WPPR_Autoloader::define_namespaces( array( 'WPPR' ) );
/**
 * Invocation of the Autoloader::loader method.
 *
 * @since   1.0.0
 */
spl_autoload_register( array( 'WPPR_Autoloader', 'loader' ) );

run_wppr();
