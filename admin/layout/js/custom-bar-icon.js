		jQuery("#cwp_select_bar_icon").click(function(a) {
		    a.preventDefault();
		    a.stopPropagation();
		    var b = ["&#xf096","&#xf08a","&#xf11b","&#xf06d","&#xf1b2","&#xf005","&#xf155","&#xf1db","&#xf006","&#xf0c8","&#xf02d","&#xf135","&#xf013","&#xf0fa",];
		    if (0 != jQuery("#bar-icon-elements").length){
		    	jQuery("#bar-icon-elements").addClass("active").show(); }  else  { insertIconContainer(b); }
			});

		function insertIconContainer(a) {
		    jQuery("#cwp_form").append("<div id='bar-icon-elements' class='active'></div>");
		    a.forEach(function(a) {
		        iC = a.substring(1);
		        var b = "<i id='" + iC + "' class='fa icon fa-fw'>" + a + "</i>";
		        jQuery("#bar-icon-elements").append(b);
		    });
		    jQuery("#bar-icon-elements").append("<span class='closeModal'><i class='fa fa-times'></i></span>");
		    jQuery("#bar-icon-elements .closeModal").click(function(x) {
			    jQuery("#bar-icon-elements").removeClass("active").hide();
			    return;
		    });
		}

		function r() {
			jQuery(".current_bar_icon").html("").text("* Currently set to the default styling.");
			jQuery("input[type='hidden']#cwp_bar_icon_field").val("");
		}

		jQuery(".useDefault").click(function(a) {
			a.preventDefault();
			a.stopPropagation();
			r();
		});

		function d() {
		    jQuery("#bar-icon-elements").removeClass("active").hide();
		    return;
		}

		jQuery("#cwp_form").on("click", "#bar-icon-elements i.icon", function(a) {
		    a.preventDefault();
		    var b = jQuery(this).attr("id");
		    var uD = "<a href='#' class='useDefault'>Use Default Styling</a>";
		    jQuery("#bar-icon-elements i.active").removeClass("active");
		    jQuery(this).addClass("active");
		    jQuery(".current_bar_icon").text("").append("<i class='fa icon fa-fw'>&" + b + "</i>" + uD);
		    jQuery("input[type='hidden']#cwp_bar_icon_field").val(b);
		    d();
		});