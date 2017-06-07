/* jshint ignore:start */
(function ($, cwpp){

    $(document).ready( function() {

        var timeout;

        $("#cwp_product_affiliate_link").on("keyup", function(e){
            if($(this).val().indexOf("amazon") != -1){
                $("#wppr_product_affiliate_link_upsell").show();
                timeout = setTimeout(function(){wppr_open_pointer(0);}, 1000);
            }
        });
        
        function wppr_open_pointer(i) {
            pointer = cwpp.pointers.pointers[i];
            options = $.extend( pointer.options, {
                close: function() {
                    $.post( ajaxurl, {
                        pointer: pointer.pointer_id,
                        action: 'wppr-dismiss-amazon-link'
                    });
                }
            });
 
            $(pointer.target).pointer( options ).pointer('open');
            clearTimeout(timeout);
        }
    });
})(jQuery, cwpp)