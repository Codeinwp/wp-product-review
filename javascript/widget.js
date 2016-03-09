(function($, w){

    $(document).ready(function(){
        toggleCustomFields(true, null);
        $("input.stylestyle").on("click", function(e){
            toggleCustomFields(false, $(this).val());
        });

        $("label.stylestyle").hover(function(e){
            $("#" + $(this).attr("for") + "img").show();
        }, function(e){
            $("#" + $(this).attr("for") + "img").hide();
        });
    });

    function toggleCustomFields(deflt, val){
        if(val != null && val == "default.php"){
            $(".customField").hide();
            $(".defaultField").show();
        }else{
            if(!deflt) $("#" + w.ratingSelect).val("star");
            $(".customField").show();
            $(".defaultField").hide();
            $("#" + w.imageCheckbox).prop("checked", true);
        }
    }

})(jQuery, cwpw);
