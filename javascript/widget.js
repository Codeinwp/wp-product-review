(function($, w){

    $(document).ready(function(){
        toggleRatingType();
        $("#" + w.layout).on("change", function(e){
            toggleRatingType();
        });
    });

    function toggleRatingType(){
        if($("#" + w.layout).val() == "default.php"){
            $("#" + w.rating).hide();
        }else{
            $("#" + w.rating).show();
        }
    }

})(jQuery, cwpw);
