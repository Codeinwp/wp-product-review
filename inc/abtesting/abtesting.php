<?php

class TIABTesting {

	private $config;
	private $slug;
	private $version;

	public function __construct( $slug, $version ) {
		$this->version  = str_replace( '.', '_', $version );
		$this->loadHooks( $slug );
	}

	private function loadHooks( $slug ) {
		$this->slug     = $slug;
		$this->config   = apply_filters( $this->slug . '_upsell_config', array() );

		foreach ( $this->config as $section => $values ) {
			add_filter( $this->slug . '_' . $section . '_upsell_text', array( $this, 'getUpsellText' ), 10, 2 );
		}
	}

	public function getUpsellText( $default = '', $escapeHTML = false ) {
		$filter     = current_filter();
		if ( strpos( $filter, $this->slug ) !== false ) {
			$section    = str_replace( array( $this->slug . '_', '_upsell_text' ), '', $filter );
			if ( ! empty( $section ) ) {
				if ( array_key_exists( $section, $this->config ) ) {
					// check if a value has already been saved against this slug, version, section
					$savedVal   = get_option( $this->slug . '_' . $this->version . '_' . $section, '' );
					if ( ! empty( $savedVal ) ) { return $savedVal; }
					$values     = $this->config[ $section ];
					$html       = $values[ rand( 0, count( $values ) - 1 ) ];
					$html       = $escapeHTML ? esc_html( $html ) : $html;
					update_option( $this->slug . '_' . $this->version . '_' . $section, $html );
					return $html;
				}
			}
		}
		return $default;
	}

	public static function writeDebug( $msg ) {
		@mkdir( dirname( __FILE__ ) . '/tmp' );
		file_put_contents( dirname( __FILE__ ) . '/tmp/log.log', date( 'F j, Y H:i:s', current_time( 'timestamp' ) ) . ' - ' . $msg . "\n", FILE_APPEND );
	}

}
