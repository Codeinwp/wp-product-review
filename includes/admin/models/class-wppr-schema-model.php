<?php
/**
 * Model responsible for the review types supported in WPPR.
 *
 * @package     WPPR
 * @subpackage  Models
 * @copyright   Copyright (c) 2017, Marius Cristea
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       3.5.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class WPPR_Review
 *
 * @since 3.0
 */
class WPPR_Schema_Model extends WPPR_Model_Abstract {

	/**
	 * The schema URL.
	 *
	 * @var string _URL The schema URL.
	 */
	const _URL = 'https://schema.org/?';

	/**
	 * WPPR_Schema_Model constructor.
	 *
	 * @access  public
	 */
	public function __construct() {
		parent::__construct();
	}

	/**
	 * Checks if the PHP version is supported by this package.
	 */
	private static function is_version_supported() {
		return version_compare( phpversion(), '7.0', '>=' );
	}

	/**
	 * Gets all the schema types that support review-type data.
	 *
	 * To disable the (year-long) cache, use `wppr_schema_disable_cache`.
	 */
	public static function get_types() {
		$types = null;

		if ( ! self::is_version_supported() ) {
			return $types;
		}

		if ( ! ( WPPR_CACHE_DISABLED || apply_filters( 'wppr_schema_disable_cache', false ) ) ) {
			$types = get_transient( 'wppr_schema_types' );
		}
		if ( $types ) {
			return $types;
		}

		include WPPR_PATH . '/vendor/autoload.php';
		$methods = self::get_uninherited_class_methods( new Spatie\SchemaOrg\Schema() );
		$types = array();
		foreach ( $methods as $method ) {
			$type = ucwords( $method );
			if ( self::can_type_include_review( $type ) ) {
				$types[ $type ] = $type;
			}
		}

		set_transient( 'wppr_schema_types', $types, YEAR_IN_SECONDS );
		return $types;
	}

	/**
	 * Gets all the fields for the specified schema type.
	 */
	public static function get_fields_for_type( $type ) {
		$fields = array();

		if ( ! self::is_version_supported() ) {
			return $fields;
		}

		include WPPR_PATH . '/vendor/autoload.php';
		$class = '\Spatie\SchemaOrg\\' . ucwords( $type );
		$class = new $class();
		$methods = self::get_uninherited_class_methods( $class );

		foreach ( $methods as $method ) {
			if ( is_callable( array( $class, $method ) ) ) {
				$fields[] = $method;
			}
		}
		return $fields;
	}

	/**
	 * Gets the URL for the specified schema type.
	 */
	public static function get_schema_url( $type ) {
		return str_replace( '?', ucwords( $type ), self::_URL );
	}

	/**
	 * Ensures that the methods returned are only defined in the specified class
	 * and are not inherited methods.
	 */
	private static function get_uninherited_class_methods( $class ) {
		$array1 = get_class_methods( $class );
		// phpcs:ignore WordPress.CodeAnalysis.AssignmentInCondition.Found
		if ( $parent_class = get_parent_class( $class ) ) {
			$array2 = get_class_methods( $parent_class );
			$array3 = array_diff( $array1, $array2 );
		} else {
			$array3 = $array1;
		}
		return( $array3 );
	}

	/**
	 * Only specific schema types support Offers and Review.
	 * There are the only types that will be shown in the Review Type dropdown.
	 */
	private static function can_type_include_review( $type ) {
		$supported = array(
			'Book', 'Course', 'Diet', 'Event', 'Game', 'HowTo', 'LocalBusiness', 'Movie', 'Painting', 'Product', 'Recipe', 'SoftwareApplication',
			'CreativeWorkSeason', 'CreativeWorkSeries', 'Episode', 'MediaObject', 'MusicPlaylist', 'MusicRecording', 'Organization',
		);
		$supported = apply_filters( 'wppr_schema_include_review_details', $supported, $type );

		return in_array( $type, $supported, true );
	}


}
