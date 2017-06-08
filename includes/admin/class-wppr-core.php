<?php
class WPPR_Core {

    public function __construct() {
    }

    function register_options() {
        $validator = new cwpposOptionsValidator();
        $option = get_option( cwppos_config( 'menu_slug' ) );
        $structure = cwpposConfig::$structure;
        $defaults = cwppos_get_config_defaults( $structure );
        $defaults = $validator->validate_defaults();
        $options = array_merge( $defaults,is_array( $option ) ? $option : array() );

        if ( ! is_array( $option ) ) {
            add_option( cwppos_config( 'menu_slug' ),$options,'','no' ); } else { 			update_option( cwppos_config( 'menu_slug' ),$options ); }
        if ( function_exists( 'register_setting' ) ) {
            register_setting( cwppos_config( 'menu_slug' ), cwppos_config( 'menu_slug' ),  array( $validator, 'validate' ) );
        }
    }

}