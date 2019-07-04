<?php
		$options      = $this->review->get_options();
		$option_names = wp_list_pluck( $options, 'name' );
		$sliders      = array();

		foreach ( $option_names as $k => $value ) {
	$sliders[] =
'<div class="wppr-comment-form-meta ' . ( is_rtl() ? 'rtl' : '' ) . '">
            <label for="wppr-slider-option-' . $k . '">' . $value . '</label>
            <input type="text" id="wppr-slider-option-' . $k . '" class="meta_option_input" value="" name="wppr-slider-option-' . $k . '" readonly="readonly">
            <div class="wppr-comment-meta-slider"></div>
            <div class="cwpr_clearfix"></div>
		</div>';
		}

		$scale      = $this->review->wppr_get_option( 'wppr_use_5_rating_scale' );
		if ( empty( $scale ) ) {
	$scale  = 10;
		}

		echo '<input type="hidden" name="wppr-scale" value="' . $scale . '">';
		echo '<div id="wppr-slider-comment">' . implode( '', $sliders ) . '<div class="cwpr_clearfix"></div></div>';


