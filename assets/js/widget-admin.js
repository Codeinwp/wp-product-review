/* jshint ignore:start */
(function ($, w) {
	$(document).ready(function () {
        initAll();
	});

    function initAll() {
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


        $('.wppr-range-slider').each(function(){
            $(this).slider({
                range   : true,
                step    : 1,
                min     : parseInt($(this).attr('data-wppr-min')),
                max     : parseInt($(this).attr('data-wppr-max')),
                values  : JSON.parse('[' + $(this).attr('data-wppr-value') + ']'),
                slide   : function( event, ui ) {
                    var $desc = $('#' + $(this).attr('data-wppr-desc'));
                    $desc.find('input').val(ui.values[0] + ',' + ui.values[1]);
                    $desc.find('span.wppr-range-min').html(Math.abs(ui.values[0]));
                    $desc.find('span.wppr-range-max').html(Math.abs(ui.values[1]));
                }
            });
        });
	
        $(document).on('widget-updated widget-added', function (e, widget) {
            initEvents(widget);
        });

        initEvents(null);
    }

    function initEvents(widget) {
        if(widget){
            widget.find( '.chosen-container' ).remove();
            widget.find('select.wppr-chosen').chosen({
                width               : '100%',
                search_contains     : true
            });
            widget.find('.wppr-post-types').on('change', function(evt, params) {
                get_categories(params, $(this), $('#' + $(this).attr('data-wppr-cat-combo')));
            });
        }else{
            $('select.wppr-chosen').chosen({
                width               : '100%',
                search_contains     : true
            });
            $('.wppr-post-types').on('change', function(evt, params) {
                get_categories(params, $(this), $('#' + $(this).attr('data-wppr-cat-combo')));
            });
        }

    }

    function get_categories(params, types, categories){
        if(params.selected){
            $('.wppr-cat-spinner').css('visibility', 'visible').show();
            $.ajax({
                url     : ajaxurl,
                method  : 'post',
                data    : {
                    action  : 'get_categories',
                    nonce   : w.ajax.nonce,
                    type    : params.selected
                },
                success : function(data){
                    if(data.data.categories){
                        var $group = '<optgroup label="' + types.find('option[value="' + params.selected + '"]').text() + '">';
                        $.each(data.data.categories, function(slug, name){
                            $group += '<option value="' + slug + '">' + name + '</option>';
                        });
                        $group += '</optgroup>';
                        categories.append($group);
                        categories.trigger("chosen:updated");
                    }
                    $('.wppr-cat-spinner').css('visibility', 'hidden').hide();
                }
            });
        }else{
            categories.find('optgroup[label="' + types.find('option[value="' + params.deselected + '"]').text() + '"]').remove();
            categories.trigger("chosen:updated");
        }
    }


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
