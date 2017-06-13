<?php
class WPPR_Render_Helper {

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

	public function add_select( $args ) {
		$option_name = WPPR_Global_Settings::instance()->get_options_name();
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

		$options = array();
		foreach ( $args['options'] as $ov => $op ) {
			$options[ esc_attr( $ov ) ] = esc_html( $op );
		}

		$output = '
				<div class="controls ' . $class . '">
				<div class="explain">' . $args['name'] . '</div><p class="field_description">' . $args['description'] . '</p>';

		$output .= '<select class=" cwp_select" name="' . $option_name . '[' . $args['id'] . ']" > ';

		foreach ( $options as $k => $v ) {

			$output .= "<option value='" . $k . "' " . ( ( isset( $this->options[ 'cwppos_' . $args['id'] ] ) && $this->options[ 'cwppos_' . $args['id'] ] == $k ) ? 'selected' : '') . '>' . $v . '</option>';
		}

		$output .= '</select></div>';

		return apply_filters( 'wppr_field', $output, $args );
	}

	public function add_color( $args ) {
		$option_name = WPPR_Global_Settings::instance()->get_options_name();
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
		$output = '
				<div class="controls ' . $class . ' ">
				    <div class="explain">' . $args['name'] . '</div>
				    <p class="field_description">' . $args['description'] . '</p>
				    <input type="hidden" id="' . esc_attr( $args['id'] ) . '_color" name="' . $option_name . '[' . esc_attr( $args['id'] ) . ']" value="' . $this->options[ 'cwppos_' . esc_attr( $args['id'] ) ] . '"/></br>
				    <input type="text" name=""	class="subo-color-picker" id="' . esc_attr( $args['id'] ) . '_color_selector" value="' . $this->options[ 'cwppos_' . esc_attr( $args['id'] ) ] . '" /><br/>
				</div>';

		return apply_filters( 'wppr_field', $output, $args );
	}

	public function add_radio( $args ) {
		$defaults = array(
			'name'     => null,
			'description'     => null,
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
		$output = '<div class="controls ' . $class . '">
                        <div class="explain">' . $args['name'] . '</div>
                        <p class="field_description">' . $args['description'] . '</p>
                        <input type="radio" ' . $disabled . ' name="' . esc_attr( $args['name'] ) . '" id="' . esc_attr( $args['id'] ) . '" class="' . $class . '" ' . checked( $args['value'], $args['current'], false ) . ' value="' . esc_attr( $args['value'] ) . '" />
                   </div>     
                   ';

		return apply_filters( 'wppr_field', $output, $args );
	}

	public function add_text( $args ) {
		$option_name = WPPR_Global_Settings::instance()->get_options_name();
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
		$output = '
				<div class="controls ' . $class . '">
				    <div class="explain">' . $args['name'] . '</div>
				    <p class="field_description">' . $args['description'] . '</p>
				    <textarea class="cwp_textarea " placeholder="' . $args['name'] . '" name="' . $option_name . '[' . esc_attr( $args['id'] ) . ']"    >' . $this->options[ 'cwppos_' . esc_attr( $args['id'] ) ] . '</textarea>
				</div>';
		return apply_filters( 'wppr_field', $output, $args );
	}

	public function add_input_text( $args ) {
		$option_name = WPPR_Global_Settings::instance()->get_options_name();
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

		$output = '
				<div class="controls ' . $class . '">
				    <div class="explain">' . $args['name'] . '</div>
				    <p class="field_description">' . $args['description'] . '</p>
				    <input type="text" ' . $disabled . ' placeholder="' . $args['name'] . '" name="' . $option_name . '[' . esc_attr( $args['id'] ) . ']" id="' . esc_attr( $args['id'] ) . '" class="' . $class . '"   value="' . esc_attr( $args['value'] ) . '" />
				</div>';

		return apply_filters( 'wppr_field', $output, $args );
	}
}
