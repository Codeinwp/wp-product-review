/* global jQuery */

(function($){

    $(document).ready(function(){
        onReady();
    });

    function onReady() {
        $('.wppr-review-type').accordion({
            heightStyle: 'content',
            collapsible: true,
            active: false,
            icons: false
        });
    }

})(jQuery);