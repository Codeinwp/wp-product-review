<div id="wppr-comment-rating-stars">
<?php
	$options      = $this->review->get_options();
	$option_names = wp_list_pluck( $options, 'name' );

	foreach ( $option_names as $k => $name ) {
?>
	<div class="wppr-comment-form-meta">
		<label for="wppr-star-option-<?php echo esc_attr( $k ); ?>"><?php echo esc_html( $name ); ?></label>
		<div class="wppr-comment-rating-star <?php echo ( is_rtl() ? 'rtl' : '' ); ?> ">
<?php
	$type   = 'half';
	for ( $x = 10; $x > 0; $x-- ) {
		$value = round( $x / 2, 2 );
		$type = $type === 'full' ? 'half' : 'full';
?>
				<input type="radio" id="star<?php echo $x; ?><?php echo esc_attr( $k ); ?>" name="wppr-slider-option-<?php echo esc_attr( $k ); ?>" value="<?php echo esc_attr( $value ); ?>" />
				<label class="<?php echo esc_attr( $type ); ?>" for="star<?php echo esc_attr( $x ); ?><?php echo esc_attr( $k ); ?>"></label>
<?php
		}
?>
		</div>
		<div class="wppr-clear"></div>
	</div>
<?php
	}
?>
</div>
