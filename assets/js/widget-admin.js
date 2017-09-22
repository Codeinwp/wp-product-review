/* jshint ignore:start */
(function ($, w) {
	$(document).ready(function () {
		var widget_selector = $('.widget');
		if (widget_selector.length > 0) {
			widget_selector.each(function () {
				var id = $(this).attr("id")
				for(name in wppr_widget.names){
					if (id.indexOf(wppr_widget.names[name]) !== -1) {
						toggleCustomFields(true, id);
					}
				}

			});
		} else {
			toggleCustomFields(true, "wpcontent");
		}
	});

	function toggleCustomFields(deflt, widgetID) {
		var val = getWidgetStyle(widgetID);
		if (val === "default.php") {
			$("#" + widgetID).find(".wppr-customField").hide();
		} else {
			$("#" + widgetID).find(".wppr-customField").show();
		}

		addListeners(widgetID);
	}

	$(document).on('widget-updated widget-added', function (e, w) {
		toggleCustomFields(true, w[0]["id"]);
	});

	function addListeners(widgetID) {
		var widget = $("#" + widgetID);
		widget.find("input.wppr-stylestyle").on("click", function (e) {
			toggleCustomFields(false, widgetID);
		});
		widget.find("label.wppr-stylestyle").hover(function (e) {
			var img = $("#" + $(this).attr("for") + "img");
			img.show();
			img.css('position', 'absolute');
			img.css('width', '100%');
		}, function (e) {
			$("#" + $(this).attr("for") + "img").hide();
		});
	}

	function getWidgetStyle(id) {
		var name = $("#" + id).find("input:radio.wppr-stylestyle").attr("name");
		return $("input:radio[name='" + name + "']:checked").val();
	}

})(jQuery, wppr_widget);
