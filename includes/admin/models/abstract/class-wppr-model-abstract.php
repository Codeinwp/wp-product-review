<?php

abstract class WPPR_Model_Abstract extends WPPR_Logger {

	private $namespace;
	private $options;

	public function __construct() {
		$this->namespace = WPPR_Global_Settings::instance()->get_options_name();
		$this->options = get_option( $this->namespace );
	}

	public function get_all_options() {
	    return $this->options;
	}

	public function get_var( $key ) {
		$this->log_notice( 'Getting value for ' . $key );
		if ( isset( $this->options[ $key ] ) ) {
			return $this->options[ $key ];
		}

		return false;
	}

	public function set_var( $key, $value = '' ) {
		$this->log_notice( 'Setting value for ' . $key . ' with ' . $value );
		if ( ! isset( $this->options[ $key ] ) ) {
			$this->options[ $key ] = '';
		}
		$this->options[ $key ] = apply_filters( 'wppr_pre_option' . $key, $value );

		return update_option( $this->namespace, $this->options );

	}

}
