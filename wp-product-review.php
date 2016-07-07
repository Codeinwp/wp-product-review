<?php
/**
 * Main loader file
 *
 * @package WPPR
 * @author ThemeIsle
 * @since 1.0.0
 *
 *
 * Plugin Name: WP Product Review
 * Description: The highest rated and most complete review plugin, now with rich snippets support. Easily turn your basic posts into in-depth reviews.
 * Version: 2.8.7
 * Author: Themeisle
 * Author URI:  https://themeisle.com/
 * Plugin URI: https://themeisle.com/plugins/wp-product-review-lite/
 * Requires at least: 3.5
 * Tested up to: 4.3.1
 * Stable tag: trunk
 * License: GPLv2 or later
 * License URI: http://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: cwppos
 * Domain Path: /languages
 */

define( 'WPPR_LITE_VERSION','2.8.7' );
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
	include 'inc/cwp-addons.php';
	include 'inc/wppr-main.php';
}
