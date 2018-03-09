/* global jQuery */
/* global wppr */

(function($, wppr){

    $(document).ready(function(){
        onReady();
    });

    $(window).load(function(){
        onLoad();
    });

    function onReady() {
        // check the is review radio button.
        $('#wppr-review-yes').attr('checked', 'checked');
        // hide the radio button settings.
        $('p.wppr-active').hide();
        // auto show the review settings.
        $('.wppr-review-editor').show();
        // hide the product name row.
        $('#wppr-editor-product-name').parent().hide();
        // change the title placeholder.
        $('#title-prompt-text').html(wppr.i10n.title_placeholder);
    }

    function onLoad() {
    }

})(jQuery, wppr);