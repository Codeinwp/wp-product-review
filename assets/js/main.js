/* jshint ignore:start */
/**
 * Main JavaScript File
 */
jQuery(document).ready(function ($) {

	$(".wppr-comment-meta-slider").each(function () {
		var comm_meta_input = $(this).parent(".wppr-comment-form-meta").children("input");
		$(this).slider({
			min: 0,
			max: 100,
			value: 4,
			slide: function (event, ui) {
				$(comm_meta_input).val(ui.value / 10);
			}
		});
	});

});
