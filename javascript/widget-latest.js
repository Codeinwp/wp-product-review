(function($, w){

    $(document).ready(function(){
        if($(".widget").length > 0){
            $(".widget").each(function(){
                if($(this).attr("id").indexOf(w.widgetName.toLowerCase()) != -1){
                    toggleCustomFields(true, $(this).attr("id"));
                }
            });
        }else{
            toggleCustomFields(true, "wpcontent");
        }
    });

    function toggleCustomFields(deflt, widgetID){
        var val = getWidgetStyle(widgetID);
        if(val == "default.php"){
            $("#" + widgetID).find(".wppr-customField").hide();
        }else{
            $("#" + widgetID).find(".wppr-customField").show();
            if(!deflt) $("#" + widgetID).find("#" + w.imageCheckbox).prop("checked", true);
        }

        addListeners(widgetID);
    }

    $(document).on('widget-updated widget-added', function(e, w){
        toggleCustomFields(true, w[0]["id"]);
    });

    function addListeners(widgetID){
        $("#" + widgetID).find("input.wppr-stylestyle").on("click", function(e){
            toggleCustomFields(false, widgetID);
        });
        $("#" + widgetID).find("label.wppr-stylestyle").hover(function(e){
            $("#" + $(this).attr("for") + "img").show();
        }, function(e){
            $("#" + $(this).attr("for") + "img").hide();
        });
    }

    function getWidgetStyle(id){
        var name = $("#" + id).find("input:radio.wppr-stylestyle").attr("name");
        return $("input:radio[name='" + name + "']:checked").val();
    }

})(jQuery, cwpw_latest);
