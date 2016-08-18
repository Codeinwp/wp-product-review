<?php
/**
 * Main loader file
 *
 * @package WPPR
 * @author ThemeIsle
 * @since 1.0.0
 *
 *
 * Plugin Name: WP Product Review Lite
 * Description: The highest rated and most complete review plugin, now with rich snippets support. Easily turn your basic posts into in-depth reviews.
 * Version: 2.9.1
 * Author: Themeisle
 * Author URI:  http://themeisle.com/
 * Plugin URI: http://themeisle.com/plugins/wp-product-review/
 * Requires at least: 3.5
 * Tested up to: 4.6
 * Stable tag: trunk
 * License: GPLv2 or later
 * License URI: http://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: cwppos
 * Domain Path: /languages
 */

define( 'WPPR_LITE_VERSION','2.9.1' );
define( 'WPPR_PATH',dirname( __FILE__ ) );
define( 'WPPR_URL',plugins_url( 'wp-product-review' ) );

if ( wp_get_theme() !== 'Reviewgine Affiliate PRO' ) {
	include 'admin/functions.php';
	include 'inc/wppr-filters.php';
	include 'inc/cwp_metabox.php';
	include 'inc/cwp_frontpage.php';
	include 'inc/cwp_top_products_widget.php';
	include 'inc/cwp_latest_products_widget.php';
	include 'inc/cwp_comment.php';
	include 'inc/cwp_js_preloader.php';
	include 'inc/wppr-main.php';
}
