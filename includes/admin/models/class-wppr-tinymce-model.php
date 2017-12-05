<?php
/**
 * The Language function file for tinymce.
 *
 * @link       http://themeisle.com
 *
 * @package    wp-product-review
 * @subpackage wp-product-review/includes/admin/models
 */
/**
 *
 * SECURITY : Exit if accessed directly
 */
if ( ! defined( 'ABSPATH' ) ) {
	die( 'Direct access not allowed!' );
}

/**
 *
 * Translation for TinyMCE
 */

if ( ! class_exists( '_WP_Editors' ) ) {
	require( ABSPATH . WPINC . '/class-wp-editor.php' );
}

/**
 * Class WPPR_Tinymce_Model
 */
class WPPR_Tinymce_Model {

	/**
	 * The strings for translation.
	 *
	 * @access   protected
	 * @var      array $strings The ID of this plugin.
	 */
	protected $strings;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    3.0.0
	 * @access   public
	 */
	public function __construct() {
		$this->strings = array(
			'popup_url'     => wp_nonce_url( 'admin-ajax.php', WPPR_SLUG, 'nonce' ),
			'pro_url'       => WPPR_UPSELL_LINK,
			'plugin_label'  => __( 'WP Product Review', 'wp-product-review' ),
			'plugin_title'  => __( 'Insert WP Product Review Shortcode', 'wp-product-review' ),
			'insert_button' => __( 'Insert Shortcode', 'wp-product-review' ),
			'cancel_button' => __( 'Cancel', 'wp-product-review' ),
			'pro_button'    => __( 'Get WP Product Review Premium', 'wp-product-review' ),
			'buttons'       => json_encode( apply_filters( 'wppr_tinymce_buttons_register', $this->get_shortcodes() ) ),
			'ispro'         => defined( 'WPPR_PRO_SLUG' ),
		);
	}

	/**
	 * Get the shortcodes that will be displayed in the menu button.
	 *
	 * @access  private
	 */
	private function get_shortcodes() {
		return array(
			array(
				'text'  => __( 'Review', 'wp-product-review' ),
				'type'  => 'review',
				'shortcode' => 'P_REVIEW',
			),
			array(
				'text'  => __( 'Listing', 'wp-product-review' ),
				'type'  => 'listing',
				'shortcode' => 'wpr_listing',
			),
			array(
				'text'  => __( 'Comparison Table', 'wp-product-review' ),
				'type'  => 'comparison',
				'shortcode' => 'wpr_landing',
			),
		);
	}

	/**
	 *
	 * The method that returns the translation array
	 *
	 * @since    3.0.0
	 * @access   public
	 * @return string
	 */
	public function wppr_tinymce_translation() {

		$locale     = _WP_Editors::$mce_locale;
		$translated = 'tinyMCE.addI18n("' . $locale . '.wppr_tinymce_plugin", ' . json_encode( $this->strings ) . ");\n";

		return $translated;
	}

}

$wpprTinyMceModel = new WPPR_Tinymce_Model();
$strings         = $wpprTinyMceModel->wppr_tinymce_translation();
