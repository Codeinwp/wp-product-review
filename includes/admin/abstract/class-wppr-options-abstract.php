<?php
class WPPR_Options_Abstract {

    private $fields_array;
    private $options_name;

    public function __construct( WPPR_Global_Settings $gs ) {
        $this->fields_array = $gs->get_filtered_fields();
        $this->options_name = $gs->get_options_name();
    }

    public function retrive_options_name() {
        return $this->options_name;
    }

    protected function wppr_get_config_defaults() {
        $structure = $this->fields_array;
        $defaults = array();
        foreach ( $structure as $k => $fields ) {

            if ( $fields['type'] == 'tab' ) {

                foreach ( $fields['options'] as $r => $field ) {

                    if ( $field['type'] == 'group' ) {

                        foreach ( $field['options'] as $m => $gfield ) {
                            if ( $gfield['type'] != 'title' ) {
                                $defaults[ $gfield['id'] ] = $gfield['default']; }
                        }
                    } else {
                        if ( $field['type'] != 'title' ) {
                            $defaults[ $field['id'] ] = $field['default']; }
                    }
                }
            }
        }
        return $defaults;
    }

    public function get_default_options() {
        $structure = $this->fields_array;
        $options = $this->wppr_get_config_defaults();
        $data = array();
        foreach ( $structure as $k => $fields ) {

            if ( $fields['type'] == 'tab' ) {

                foreach ( $fields['options'] as $r => $field ) {

                    if ( $field['type'] == 'group' ) {

                        foreach ( $field['options'] as $m => $gfield ) {
                            if ( $gfield['type'] != 'title' ) {
                                $data[ $gfield['id'] ] = array(
                                    'default' => $options[ $gfield['id'] ],
                                    'type' => $gfield['type'],
                                ); }
                        }
                    } else {
                        if ( $field['type'] != 'title' ) {

                            $data[ $field['id'] ] = array(
                                'default' => $options[ $field['id'] ],
                                'type' => $field['type'],
                            ); }
                    }
                }
            }
        }
        return $data;
    }

    public function get_options_data( $options = 0 ) {
        $structure = $this->fields_array;
        $defaults = $this->wppr_get_config_defaults();
        $option = get_option( $this->options_name );
        $options = array_merge( $defaults,is_array( $option ) ? $option : array() );

        $data = array();
        foreach ( $structure as $k => $fields ) {

            if ( $fields['type'] == 'tab' ) {

                foreach ( $fields['options'] as $r => $field ) {

                    if ( $field['type'] == 'group' ) {

                        foreach ( $field['options'] as $m => $gfield ) {
                            if ( $gfield['type'] != 'title' ) {
                                $data[ $gfield['id'] ] = array(
                                    'default' => $options[ $gfield['id'] ],
                                    'type' => $gfield['type'],
                                ); }
                        }
                    } else {
                        if ( $field['type'] != 'title' ) {

                            $data[ $field['id'] ] = array(
                                'default' => $options[ $field['id'] ],
                                'type' => $field['type'],
                            ); }
                    }
                }
            }
        }
        return $data;
    }

}