/* jshint ignore:start */
jQuery(document).ready(function ($) {
	jQuery(".subo-color-picker").wpColorPicker({
		change: function (event, ui) {
			var color = ui.color.toCSS();
			var id = jQuery(this).attr('id').replace('_color_selector', '');
			jQuery("#" + id + "_color").val(color);
		}
	});

	update_nav($("#wppr_top_tabs .wppr-nav-tab:first"));
	$("#wppr_top_tabs").on('click', '.wppr-nav-tab', function () {
		update_nav($(this));
		return false;
	})

	function update_nav(obj) {
		var tab = obj.attr('data-tab');
		var id = obj.find('a').attr('href');
		$('.wppr-nav-tab').removeClass('active');
		obj.addClass('active');
		$('.wppr-tab-content').hide();
		$(id).show();
	}
});

jQuery("document").ready(function () {
	jQuery('.cwp_save').on('click', function () {
		var $btn = jQuery(this);
		$btn.parent().find('.spinner').addClass('is-active');
		$btn.addClass('disabled');
		var form_data = jQuery('#wppr-settings').serializeArray()
		var data = {
			'action': 'update_options',
			'cwppos_options': form_data
		};
		// since 2.8 ajaxurl is always defined in the admin header and points to admin-ajax.php
		jQuery.post(ajaxurl, data, function (response) {
			$btn.parent().find('.spinner').removeClass('is-active');
			$btn.removeClass('disabled');
		});
	});
});
