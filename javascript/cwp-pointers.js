(function ($, cwpp){

    $(document).ready( function() {

        $("#cwp_product_affiliate_link").on("keyup", function(e){
            if($(this).val().indexOf("amazon") != -1){
                $("#cwp_product_affiliate_link_upsell").show();
                wptuts_open_pointer(0);
            }
        });
        
        function wptuts_open_pointer(i) {
            pointer = cwpp.pointers.pointers[i];
            options = $.extend( pointer.options, {
            });
 
            $(pointer.target).pointer( options ).pointer('open');
        }
    });
})(jQuery, cwpp)