<?php

/**
 * Helper class for HTML fields.
 *
 * @package     WPPR
 * @subpackage  Helpers
 * @copyright   Copyright (c) 2017, Marius Cristea
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       3.0.0
 */
class WPPR_Html_Fields {

	/**
	 * The main instance var.
	 *
	 * @var WPPR_Html_Fields The one WPPR_Html_Fields istance.
	 * @since 3.0.0
	 */
	private static $instance;

	/**
	 * @return WPPR_Html_Fields The singleton instance.
	 */
	public static function init() {
		if ( ! isset( self::$instance ) && ! ( self::$instance instanceof WPPR_Html_Fields ) ) {
			self::$instance = new WPPR_Html_Fields;
			self::$instance->init();
		}

		return self::$instance;
	}

	/**
	 * Render a radio html element.
	 *
	 * @param array $args The field settings.
	 *
	 * @return string The radio html field string.
	 */
	public function radio( $args ) {
		$defaults = array(
			'name'     => null,
			'id'       => null,
			'current'  => null,
			'value'    => null,
			'class'    => 'wppr-radio',
			'disabled' => false,
		);
		$args     = wp_parse_args( $args, $defaults );
		$disabled = '';
		if ( ! empty( $args['options']['disabled'] ) ) {
			$disabled .= ' disabled="disabled"';
		}
		if ( is_null( $args['id'] ) ) {
			$args['id'] = $args['name'];
		}
		$class  = $this->validate_class( $args['class'] );
		$output = '<input type="radio" ' . $disabled . ' name="' . esc_attr( $args['name'] ) . '" id="' . esc_attr( $args['id'] ) . '" class="' . $class . '" ' . checked( $args['value'], $args['current'], false ) . ' value="' . esc_attr( $args['value'] ) . '" />';

		return apply_filters( 'wppr_field', $output, $args );
	}

	/**
	 * Validate a class string.
	 *
	 * @param string $class String to validate.
	 *
	 * @return string The validate string.
	 */
	private function validate_class( $class ) {
		return implode( ' ', array_map( 'sanitize_html_class', explode( ' ', str_replace( '  ', ' ', $class ) ) ) );
	}

	/**
	 * Render a text input string.
	 *
	 * @param array $args The settings of the input.
	 *
	 * @return string The html string.
	 */
	public function text( $args ) {
		$defaults = array(
			'name'        => null,
			'id'          => null,
			'value'       => null,
			'class'       => 'wppr-text',
			'placeholder' => '',
			'disabled'    => false,
		);
		$args     = wp_parse_args( $args, $defaults );
		$class    = $this->validate_class( $args['class'] );
		$disabled = '';
		if ( $args['disabled'] ) {
			$disabled = ' disabled="disabled"';
		}
		if ( is_null( $args['id'] ) ) {
			$args['id'] = $args['name'];
		}
		$output = '<input type="text" ' . $disabled . ' name="' . esc_attr( $args['name'] ) . '" id="' . esc_attr( $args['id'] ) . '" class="' . $class . '"   value="' . esc_attr( $args['value'] ) . '" placeholder="' . esc_attr( $args['placeholder'] ) . '"  />';

		return apply_filters( 'wppr_field', $output, $args );
	}

	/**
	 * Render a image field.
	 *
	 * @param array $args The settings of the input.
	 *
	 * @return string The html string.
	 */
	public function image( $args ) {
		$defaults = array(
			'name'        => null,
			'id'          => null,
			'value'       => null,
			'class'       => 'wppr-image',
			'placeholder' => '',
			'action'      => __( 'Choose image', 'wp-product-review' ),
			'disabled'    => false,
		);
		$args     = wp_parse_args( $args, $defaults );
		$class    = $this->validate_class( $args['class'] );
		$disabled = '';
		if ( $args['disabled'] ) {
			$disabled = ' disabled="disabled"';
		}
		if ( is_null( $args['id'] ) ) {
			$args['id'] = $args['name'];
		}
		$output = '<input type="text" ' . $disabled . ' name="' . esc_attr( $args['name'] ) . '" id="' . esc_attr( $args['id'] ) . '" class="' . $class . '"   value="' . esc_attr( $args['value'] ) . '" placeholder="' . esc_attr( $args['placeholder'] ) . '"  />';
		$output .= '<input type="button" id="' . esc_attr( $args['id'] ) . '-button" class="wppr-image-button button"  value="' . esc_attr( $args['action'] ) . '"/>';

		return apply_filters( 'wppr_field', $output, $args );
	}
}
