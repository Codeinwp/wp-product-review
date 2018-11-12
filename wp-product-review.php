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
 * @package           WPPR
 *
 * @wordpress-plugin
 * Plugin Name:       WP Product Review Lite
 * Plugin URI:        https://themeisle.com/plugins/wp-product-review/
 * Description:       The highest rated and most complete review plugin, now with rich snippets support. Easily turn your basic posts into in-depth reviews.
 * Version:           3.4.10
 * Author:            ThemeIsle
 * Author URI:        https://themeisle.com/
 * Requires at least: 3.5
 * Tested up to:      4.6
 * Stable tag:        trunk
 * WordPress Available:  yes
 * Pro Slug:          wp-product-review-pro
 * Requires License:    no
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

	define( 'WPPR_LITE_VERSION', '3.4.10' );
	define( 'WPPR_PATH', dirname( __FILE__ ) );
	define( 'WPPR_SLUG', 'wppr' );
	define( 'WPPR_UPSELL_LINK', 'https://themeisle.com/plugins/wp-product-review/' );
	define( 'WPPR_URL', untrailingslashit( plugins_url( '/', __FILE__ ) ) );
	define( 'WPPR_CACHE_DISABLED', false );

	$plugin = new WPPR();
	$plugin->run();

	require_once WPPR_PATH . '/includes/legacy.php';
	require_once WPPR_PATH . '/includes/functions.php';

	$vendor_file = WPPR_PATH . '/vendor/autoload_52.php';
	if ( is_readable( $vendor_file ) ) {
		require_once $vendor_file;
	}
	add_filter( 'pirate_parrot_log', 'wppr_lite_register_parrot', 10, 1 );
	add_filter( 'themeisle_sdk_products', 'wppr_lite_register_sdk' );
}

/**
 * Registers with the parrot plugin
 *
 * @param array $plugins Array of plugins.
 */
function wppr_lite_register_parrot( $plugins ) {
	$plugins[] = WPPR_SLUG;
	return $plugins;
}

/**
 * Register product to sdk.
 *
 * @param array $products Array of products.
 *
 * @return array All products registered to sdk.
 */
function wppr_lite_register_sdk( $products ) {
	$products[] = __FILE__;

	return $products;
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
