<?php
class WPPR_Core {

	public function register_options() {
		$validator = new WPPR_Options_Validator_Helper();
		$options_name = $validator->retrive_options_name();
		var_dump( $options_name );
		$option = get_option( $options_name );
		$defaults = $validator->validate_defaults();
		$options = array_merge( $defaults,is_array( $option ) ? $option : array() );

		var_dump( $defaults );
		var_dump( $options );

		if ( ! is_array( $option ) ) {
			add_option( $options_name, $options, '', 'no' );
		} else { update_option( $options_name, $options ); }
		if ( function_exists( 'register_setting' ) ) {
			register_setting( $options_name, $options_name,  array( $validator, 'validate' ) );
		}
	}
}
