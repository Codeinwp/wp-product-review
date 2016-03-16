(function($, w){

    $(document).ready(function(){
        $(".widget").each(function(){
            if($(this).attr("id").indexOf(w.widgetName.toLowerCase()) != -1){
                toggleCustomFields(true, $(this).attr("id"));
            }
        });
    });

    function toggleCustomFields(deflt, widgetID){
        var val = getWidgetStyle(widgetID);
        if(val == "default.php"){
            $("#" + widgetID).find(".customField").hide();
            $("#" + widgetID).find(".defaultField").show();
        }else{
            if(!deflt) $("#" + widgetID).find("#" + w.ratingSelect).val("star");
            $("#" + widgetID).find(".customField").show();
            $("#" + widgetID).find(".defaultField").hide();
            $("#" + widgetID).find("#" + w.imageCheckbox).prop("checked", true);
        }

        addListeners(widgetID);
    }

    $(document).on('widget-updated widget-added', function(e, w){
        toggleCustomFields(true, w[0]["id"]);
    });

    function addListeners(widgetID){
        $("#" + widgetID).find("input.stylestyle").on("click", function(e){
            toggleCustomFields(false, widgetID);
        });
        $("#" + widgetID).find("label.stylestyle").hover(function(e){
            $("#" + $(this).attr("for") + "img").show();
        }, function(e){
            $("#" + $(this).attr("for") + "img").hide();
        });
    }

    function getWidgetStyle(id){
        var name = $("#" + id).find("input:radio.stylestyle").attr("name");
        return $("input:radio[name='" + name + "']:checked").val();
    }

})(jQuery, cwpw_latest);
