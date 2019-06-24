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
	const _URL = 'https://schema.org/?.jsonld';

	/**
	 * The schema types tree.
	 *
	 * @var array $types The schema types tree.
	 */
	private $types;

	/**
	 * The schema data types allowed to become fields.
	 *
	 * @var array $data_types_allowed The schema data types allowed to become fields.
	 */
	private $data_types_allowed;

	/**
	 * WPPR_Schema_Model constructor.
	 *
	 * @access  public
	 *
	 * @param array $things The tree of things to parse.
	 */
	public function __construct( array $things ) {
		parent::__construct();

		$this->setup_types( $things );
	}

	/**
	 * Set up the types.
	 *
	 * @since   3.5.0
	 * @access  private
	 *
	 * @param array $things The tree of things to fetch.
	 */
	private function setup_types( array $things ) {
		// good for testing.
		// set_time_limit( 0 );
		$this->data_types_allowed   = apply_filters( 'wppr_schema_data_types_allowed', array( 'schema:Text', 'schema:URL', 'schema:Date', 'schema:Number', 'schema:Boolean', 'schema:DateTime', 'schema:Time', 'schema:Integer', 'schema:Float', 'schema:False', 'schema:True', 'schema:QuantitativeValue', 'schema:Distance' ), $things );
		$things     = apply_filters( 'wppr_schema_things', $things );
		$types      = get_transient( 'wppr_schema_types_' . md5( json_encode( $things ) ) );
		if ( $types ) {
			$this->types    = $types;
			return;
		}

		$types  = array();
		$categories = array();
		foreach ( $things as $parent => $type ) {
			foreach ( $type as $t ) {
				$categories[ $t ] = $parent;
			}
		}

		foreach ( $categories as $type => $parent ) {
			$subtypes   = array();

			$this->data_types_allowed_for_type = apply_filters( 'wppr_schema_data_types_allowed_for_' . $type, $this->data_types_allowed );

			// get the schema types under this type
			$subtypes   = $this->parse( array( $type ), '@id', array( 'rdfs:subClassOf > @id' => "schema:{$type}" ) );

			// get the schema type itself
			$subtypes   = array_merge( $subtypes, $this->parse( array( $type ), '@id', array( 'rdfs:subClassOf > @id' => "schema:{$parent}" ) ) );

			foreach ( $subtypes as $subtype ) {
				if ( 'schema:Thing' === $subtype ) {
					continue;
				}

				$subtype    = str_replace( 'schema:', '', $subtype );

				$attributes = $this->parse( array( $subtype ), null, array( '@type' => 'rdf:Property' ) );
				if ( ! $attributes ) {
					continue;
				}

				$fields = array();
				foreach ( $attributes as $attribute ) {
					$fields[ str_replace( 'schema:', '', $attribute['@id'] ) ]  = array(
						'desc'      => $attribute['rdfs:comment'],
						'label'     => $attribute['rdfs:label'],
					);
				}

				ksort( $fields );

				$types[ $subtype ]  = apply_filters( 'wppr_schema_fields_for_' . $type, $fields, $subtype );
			}
		}

		ksort( $types );

		$this->types    = $types;

		set_transient( 'wppr_schema_types_' . md5( json_encode( $things ) ), $types, YEAR_IN_SECONDS );

	}

	/**
	 * Parse the schema URL response.
	 *
	 * @param   array  $types  The types to fetch.
	 * @param   string $extract    Extract these values from each element.
	 * @param   array  $if         The conditions under which the element qualifies as a valid element.
	 *
	 * @return array
	 */
	private function parse( $types, $extract, $if ) {
		$elements   = array();

		foreach ( $types as $type ) {
			$url        = str_replace( '?', $type, self::_URL );
			$json       = json_decode( wp_remote_retrieve_body( wp_remote_get( $url ) ), true );
			$if         = apply_filters( 'wppr_schema_extract_if', $if, $type );

			if ( $json && isset( $json['@graph'] ) ) {
				foreach ( $json['@graph'] as $element ) {
					foreach ( $if as $key => $val ) {
						$extracted = $this->extract_if( $element, $extract, $key, $val );
						if ( $extracted !== null ) {
							$elements[] = $extracted;
						}
					}
				}
			}
		}
		return $elements;
	}

	/**
	 * Parse the schema URL response.
	 *
	 * @param   array  $element    The schema element.
	 * @param   string $extract    Extract these values from each element.
	 * @param   string $key        The key(s) that will fetch the $val.
	 * @param   string $val        The value that will qualify the element as a valid one.
	 *
	 * @return mixed
	 */
	private function extract_if( $element, $extract, $key, $val ) {
		$leaf       = $element;
		$extracted  = null;
		foreach ( array_map( 'trim', explode( '>', $key ) ) as $k ) {
			if ( isset( $leaf[ $k ] ) ) {
				$leaf   = $leaf[ $k ];
			}
		}
		if ( $val === $leaf ) {
			if ( is_null( $extract ) ) {
				$extracted  = $element;
			} else {
				$extracted = $element[ $extract ];
			}
		}

		// exclude anything that is a complex object and cannot resolve to a scalar field.
		if ( is_array( $extracted ) ) {
			$range_includes = $extracted['schema:rangeIncludes'];
			$data_types     = array_values( $range_includes );
			if ( count( $data_types ) > 1 ) {
				$data_types     = array_values( wp_list_pluck( $data_types, '@id' ) );
			}
			$common = array_intersect( $this->data_types_allowed_for_type, $data_types );
			if ( empty( $common ) ) {
				$extracted = null;
			}
		}

		return $extracted;
	}

	/**
	 * Getter function to return the types.
	 *
	 * @return array
	 */
	public function get_types() {
		return apply_filters( 'wppr_schema_types', $this->types );
	}

}
