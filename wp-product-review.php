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
 * Version: 2.9.8
 * Author: Themeisle
 * Author URI:  http://themeisle.com/
 * Plugin URI: http://themeisle.com/plugins/wp-product-review-lite/
 * Requires at least: 3.5
 * Tested up to: 4.6
 * Stable tag: trunk
 * License: GPLv2 or later
 * License URI: http://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: cwppos
 * Domain Path: /languages
 */

define( 'WPPR_LITE_VERSION', '2.9.8' );
define( 'WPPR_PATH',dirname( __FILE__ ) );
define( 'WPPR_SLUG', 'wppr' );
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
	include 'inc/abtesting/abtesting.php';
}

add_filter( WPPR_SLUG . '_upsell_config', 'wppr_upsell_config' );
if ( class_exists( 'TIABTesting' ) ) {

	$abtesting = new TIABTesting( WPPR_SLUG, WPPR_LITE_VERSION );

}
function wppr_upsell_config( $config ) {
	return array(
		'preloader' => array(
			__( ' In order to be able to automatically load your options from another posts, you need the PRO add-on', 'cwppos' ) . '<a href="http://bit.ly/2bpC3vT" target="_blank" class="preload_info_upsell">' . __( 'View Preload features','cwppos' ) . '</a>',
			'<i>' . __( ' You can always import options from other reviews using the preloader feature from the PRO add-on', 'cwppos' ) . '</i><a href="http://bit.ly/2c1QNFK" target="_blank" class="preload_info_upsell">' . __( 'View more PRO features','cwppos' ) . '</a>',
			'<i>' . __( ' Build review more easily by importing them from other posts using the preloader feature from the pro addon', 'cwppos' ) . '</i><a href="http://bit.ly/2bT89Sk" target="_blank" class="preload_info_upsell">' . __( 'View details','cwppos' ) . '</a>',
		),
		'amazon_title'    => array(
			__( 'Hold on', 'cwppos' ),
			__( 'Breaking news', 'cwppos' ),
			__( 'Review tip', 'cwppos' ),
			__( 'Integrate with Amazon', 'cwppos' ),
		),
		'amazon_body'    => array(
			__( "Do you know that you can import products directly from your amazon affiliate account using the <a href='http://bit.ly/2cjpsux'>PRO</a> version", 'cwppos' ),
			__( "Do you know that you can import products from amazon and keep them synced using the <a href='http://bit.ly/2cEefYy'>PRO</a> version ", 'cwppos' ),
		),
	);
}
