<div id="wppr-slider-comment">
<?php
	foreach ( $option_names as $k => $value ) {
?>
	<div class="wppr-comment-form-meta">
		<label for="wppr-slider-option-<?php echo $k;?>"><?php echo $value;?></label>
		<input type="text" id="wppr-slider-option-<?php echo $k;?>" class="meta_option_input" value="" name="wppr-slider-option-<?php echo $k;?>" readonly="readonly">
        <div class="wppr-comment-meta-slider"></div>
        <div class="cwpr_clearfix"></div>
	</div>
<?php
	}
?>
	<div class="cwpr_clearfix"></div>
</div>