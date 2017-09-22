<?php

/**
 * Implements templating behaviour in WPPR.
 *
 * @package     WPPR
 * @copyright   Copyright (c) 2017, Marius Cristea
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       3.0.10
 */
class WPPR_Template {
	/**
	 * Directories where to search in.
	 *
	 * @var array Directories path.
	 */
	private $dirs = array();

	/**
	 * WPPR_Template constructor.
	 */
	public function __construct() {
		$this->setup_locations();
	}

	/**
	 * Setup directories where wppr templates resides.
	 */
	private function setup_locations() {
		$this->dirs[]  = WPPR_PATH . '/includes/public/layouts/';
		$custom_paths  = apply_filters( 'wppr_templates_dir', array() );
		$theme_paths   = array();
		$theme_paths[] = get_template_directory() . '/wppr';
		$theme_paths[] = get_stylesheet_directory() . '/wppr';
		$this->dirs    = array_merge( $this->dirs, $custom_paths, $theme_paths );
		$this->dirs    = array_map( 'trailingslashit', $this->dirs );

	}

	/**
	 * Render the template file.
	 *
	 * @param string $template Template name.
	 * @param array  $args Args of variable to load.
	 * @param bool   $echo Either to echo or return content.
	 *
	 * @return string Return template content.
	 */
	public function render( $template, $args = array(), $echo = true ) {
		$location = $this->locate_template( $template );
		if ( empty( $location ) ) {
			return '';
		}
		foreach ( $args as $name => $value ) {
			if ( is_numeric( $name ) ) {
				continue;
			}
			$$name = $value;
		}
		/**
		 * Store the view output in cache based on the args it needs.
		 */
		$cache_key = md5( $location . serialize( $args ) );
		$content   = wp_cache_get( $cache_key, 'wppr' );
		if ( empty( $content ) ) {
			ob_start();
			require( $location );
			$content = ob_get_contents();
			ob_end_clean();

			wp_cache_set( $cache_key, $content, 'wppr', 5 * 60 );
		}
		if ( ! $echo ) {
			return $content;
		}
		echo $content;

		return '';
	}

	/**
	 * Locate template file.
	 *
	 * @param string $template Filename to look for.
	 *
	 * @return string The template location.
	 */
	public function locate_template( $template ) {
		$dirs     = array_reverse( $this->dirs );
		$template = str_replace( '.php', '', $template );
		$template = $template . '.php';
		foreach ( $dirs as $dir ) {
			if ( file_exists( $dir . $template ) ) {
				return $dir . $template;
			}
		}

		return '';
	}
}
