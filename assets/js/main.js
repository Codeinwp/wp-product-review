/* jshint ignore:start */
/**
 * Main JavaScript File
 */

(function($, wppr_config){

    $(document).ready(function(){
        initAll();
    });

    function initAll(){
        $(".wppr-comment-meta-slider").each(function () {
            var min = 0;
            var max = 100;
            var step = wppr_config.scale / 2;
            if($(this).parent(".wppr-comment-form-meta").hasClass('rtl')){
                min = -100;
                max = 0;
            }
            var comm_meta_input = $(this).parent(".wppr-comment-form-meta").children("input");
            $(this).slider({
                min: min,
                max: max,
                value: 0,
                step: step,
                slide: function (event, ui) {
                    $(comm_meta_input).val(Math.abs(ui.value) / wppr_config.scale);
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
    }

})(jQuery, wppr_config);