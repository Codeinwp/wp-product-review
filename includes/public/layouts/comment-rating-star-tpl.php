<div id="wppr-comment-rating-stars">
<?php
	$options      = $this->review->get_options();
	$option_names = wp_list_pluck( $options, 'name' );

	foreach ( $option_names as $k => $name ) {
?>
	<div class="wppr-comment-form-meta">
		<label for="wppr-star-option-<?php echo $k; ?>"><?php echo $name; ?></label>
		<div class="wppr-comment-rating-star <?php echo ( is_rtl() ? 'rtl' : '' ); ?> ">
<?php
	$type   = 'half';
	for ( $x = 10; $x > 0; $x-- ) {
		$value = round( $x / 2, 2 );
		$type = $type === 'full' ? 'half' : 'full';
?>
				<input type="radio" id="star<?php echo $x; ?><?php echo $k; ?>" name="wppr-slider-option-<?php echo $k; ?>" value="<?php echo $value; ?>" />
				<label class="<?php echo $type; ?>" for="star<?php echo $x; ?><?php echo $k; ?>"></label>
<?php
		}
?>
		</div>
	</div>
<?php
	}
?>
</div>
