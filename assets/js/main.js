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
			value: 0,
			slide: function (event, ui) {
				$(comm_meta_input).val(ui.value / 10);
			}
		});
	});

	// Check if review image width is bigger than height.
    if ( $( '.wppr-template-2' ).length > 0 ) {
    	var reviewImage = $( '.wppr-review-product-image' );
    	if ( reviewImage.length > 0 ) {
    		if ( reviewImage.find('img').width() > reviewImage.find('img').height() ) {
                reviewImage.addClass('wppr-review-product-image-full');
			}
		}
    }
});
