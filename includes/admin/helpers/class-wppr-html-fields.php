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

/**
 * Class WPPR_Html_Fields
 */
class WPPR_Html_Fields {

	/**
	 * Validate a class string.
	 *
	 * @since   3.0.0
	 * @access  private
	 * @param   string $class  Class name string to validate.
	 * @return string
	 */
	private function validate_class( $class ) {
		return implode( ' ', array_map( 'sanitize_html_class', explode( ' ', str_replace( '  ', ' ', $class ) ) ) );
	}

	/**
	 * Merges specific defaults with general ones.
	 *
	 * @since   3.0.0
	 * @access  private
	 * @param   array $specific_defaults  The specific defaults array.
	 * @return array
	 */
	private function define_defaults( $specific_defaults ) {
	    $general_defaults = array(
			'id'        => null,
	        'name'      => null,
			'value'     => null,
			'class'     => '',
			'default'   => '',
			'placeholder' => '',
			'disabled'    => false,
		);

	    return wp_parse_args( $specific_defaults, $general_defaults );
	}

	/**
	 * Render a radio html element.
	 *
	 * @since   3.0.0
	 * @access  public
	 * @param   array $args The field settings.
	 * @return string
	 */
	public function radio( $args ) {
		$defaults = $this->define_defaults( array(
			'class' => 'wppr-radio',
		) );
		$args     = wp_parse_args( $args, $defaults );
		$class  = $this->validate_class( $args['class'] );
		$disabled = '';
		if ( ! empty( $args['options']['disabled'] ) ) {
			$disabled .= ' disabled="disabled"';
		}
		if ( is_null( $args['id'] ) ) {
			$args['id'] = $args['name'];
		}
		$output = '<input type="radio" ' . $disabled . ' name="' . esc_attr( $args['name'] ) . '" id="' . esc_attr( $args['id'] ) . '" class="' . $class . '" ' . checked( $args['value'], $args['current'], false ) . ' value="' . esc_attr( $args['value'] ) . '" />';

		return apply_filters( 'wppr_field', $output, $args );
	}

	/**
	 * Render a text input string.
	 *
	 * @since   3.0.0
	 * @access  public
	 * @param   array $args The settings of the input.
	 * @return string
	 */
	public function text( $args ) {
		$defaults = $this->define_defaults( array(
			'class' => 'wppr-text',
		) );
		$args     = wp_parse_args( $args, $defaults );
		$class    = $this->validate_class( $args['class'] );
		$disabled = '';
		if ( $args['disabled'] ) {
			$disabled = ' disabled="disabled"';
		}
		if ( is_null( $args['id'] ) ) {
			$args['id'] = $args['name'];
		}
		if ( $args['value'] == null ) {
			$args['value'] = $args['default'];
		}
		$output = '<input type="text" ' . $disabled . ' name="' . esc_attr( $args['name'] ) . '" id="' . esc_attr( $args['id'] ) . '" class="' . $class . '"   value="' . esc_attr( $args['value'] ) . '" placeholder="' . esc_attr( $args['placeholder'] ) . '"  />';

		return apply_filters( 'wppr_field', $output, $args );
	}

	/**
	 * Render a image field.
	 *
	 * @since   3.0.0
	 * @access  public
	 * @param   array $args The settings of the input.
	 * @return string
	 */
	public function image( $args ) {
		$defaults = $this->define_defaults( array(
			'class' => 'wppr-image',
			'action' => __( 'Choose image', 'wp-product-review' ),
		) );
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

	/**
	 * Render a select input.
	 *
	 * @since   3.0.0
	 * @access  public
	 * @param   array $args The settings of the input.
	 * @return mixed
	 */
	public function select( $args ) {
		$defaults = $this->define_defaults( array(
			'class' => 'wppr-select',
		) );
		$args     = wp_parse_args( $args, $defaults );
		$class    = $this->validate_class( $args['class'] );
		if ( $args['value'] == null ) {
			$args['value'] = $args['default'];
		}
		$options = array();
		foreach ( $args['options'] as $ov => $op ) {
			$options[ esc_attr( $ov ) ] = esc_html( $op );
		}
		$output = '<select class="' . $class . '" name="' . esc_attr( $args['name'] ) . '" > ';
		foreach ( $options as $k => $v ) {
			$output .= "<option value='" . $k . "' " . ( ( isset( $args['value'] ) && $args['value'] == $k ) ? 'selected' : '') . '>' . $v . '</option>';
		}
		$output .= '</select>';

		return apply_filters( 'wppr_field', $output, $args );
	}

	/**
	 * Render a color picker.
	 *
	 * @since   3.0.0
	 * @access  public
	 * @param   array $args The settings of the input.
	 * @return mixed
	 */
	public function color( $args ) {
		$defaults = $this->define_defaults( array(
			'class' => 'wppr-color',
		) );
		$args     = wp_parse_args( $args, $defaults );
		$class    = $this->validate_class( $args['class'] );
		if ( $args['value'] == null ) {
			$args['value'] = $args['default'];
		}
		$output = '<input type="hidden" class="' . $class . '" id="' . esc_attr( $args['id'] ) . '_color" name="' . esc_attr( $args['name'] ) . '" value="' . esc_attr( $args['value'] ) . '"/></br>
				   <input type="text" name="" class="subo-color-picker" id="' . esc_attr( $args['id'] ) . '_color_selector" value="' . esc_attr( $args['value'] ) . '" /><br/>';

		return apply_filters( 'wppr_field', $output, $args );
	}
}