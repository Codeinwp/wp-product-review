<?php
class WPPR_Options_Validator_Helper extends WPPR_Options_Abstract {

	public function __construct() {
		parent::__construct( WPPR_Global_Settings::instance() );
	}

	public function validate_defaults() {
		$ninput = array();
		$defaults = $this->get_default_options();
		foreach ( $defaults as $k => $i ) {
			switch ( $i['type'] ) {
				case 'textarea':
				case 'editor':

				case 'input_text':
					$ninput[ $k ] = apply_filters( 'cwppos_sanitize_textarea',$i['default'] );
					break;
				case 'textarea_html':
					$ninput[ $k ] = apply_filters( 'cwppos_sanitize_textarea_html',$i['default'] );
					break;

				case 'radio':
				case 'select':
					$ninput[ $k ] = apply_filters( 'cwppos_sanitize_enum',$i['default'],$k );

					break;
				case 'color':

					$ninput[ $k ] = apply_filters( 'cwppos_sanitize_color',$i['default'] );
					break;
				case 'image':

					$ninput[ $k ] = apply_filters( 'cwppos_sanitize_url',$i['default'] );
					break;
				case 'background':

					$ninput[ $k ] = apply_filters( 'cwppos_sanitize_background',$i['default'] );
					break;
				case 'typography':

					$ninput[ $k ] = apply_filters( 'cwppos_sanitize_typography',$i['default'] );
					break;
				case 'input_number':

					$ninput[ $k ] = apply_filters( 'cwppos_sanitize_number',$i['default'] );
					break;
				case 'checkbox':
				case 'multiselect':
					$ninput[ $k ] = apply_filters( 'cwppos_sanitize_array',$i['default'],$k );
					break;

				case 'change_icon':
					$ninput[ $k ] = apply_filters( 'cwppos_sanitize_change_icon',$i['default'],$k );
					break;

			}// End switch().
		}// End foreach().
		return $ninput;
	}

	public function validate( $input ) {

		$ninput = array();

		$options = $this->get_options_data();

		foreach ( $input as $k => $i ) {
			switch ( $options[ $k ]['type'] ) {
				case 'textarea':
				case 'editor':

				case 'input_text':
					$ninput[ $k ] = apply_filters( 'cwppos_sanitize_textarea',$i,$options[ $k ]['default'] );

					break;
				case 'textarea_html':
					$ninput[ $k ] = apply_filters( 'cwppos_sanitize_textarea_html',$i,$options[ $k ]['default'] );
					break;
				case 'radio':
				case 'select':
					$ninput[ $k ] = apply_filters( 'cwppos_sanitize_enum',$i,$k,$options[ $k ]['default'] );

					break;
				case 'color':
					$ninput[ $k ] = apply_filters( 'cwppos_sanitize_color',$i,$options[ $k ]['default'] );
					break;
				case 'image':

					$ninput[ $k ] = apply_filters( 'cwppos_sanitize_url',$i,$options[ $k ]['default'] );
					break;
				case 'background':

					$ninput[ $k ] = apply_filters( 'cwppos_sanitize_background',$i,$options[ $k ]['default'] );
					break;
				case 'typography':

					$ninput[ $k ] = apply_filters( 'cwppos_sanitize_typography',$i,$options[ $k ]['default'] );
					break;
				case 'input_number':

					$ninput[ $k ] = apply_filters( 'cwppos_sanitize_number',$i,$options[ $k ]['default'] );
					break;
				case 'checkbox':
				case 'multiselect':
					$ninput[ $k ] = apply_filters( 'cwppos_sanitize_array',$i,$k,$options[ $k ]['default'] );
					break;

				case 'change_icon':
					$ninput[ $k ] = apply_filters( 'cwppos_sanitize_change_icon',$i,$k,$options[ $k ]['default'] );
					break;

			}// End switch().
		}// End foreach().
		return $ninput;
	}
}
