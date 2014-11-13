<?php
require_once( '../../../../wp-load.php' ); 
header("Content-type: text/javascript", true);
 ?>

jQuery(document).ready(function(){
		function cwpPreloadOptions(item) {
			for (var i = 1; i <= <?php echo cwppos("cwppos_option_nr");?>; i++) {
				var preloadListItem = jQuery(item).parent().parent().children(".cwp_pitem_info").children(".cwp_pitem_options_content").children("li:eq("+(i-1)+")").text();
				if(preloadListItem != "-") { jQuery("input#option_" + i + "_content").val(preloadListItem); }
			};
		}

		function cwpPreloadCons(item)
		{
			for (var i = 1; i <= <?php echo cwppos("cwppos_option_nr");?>; i++) {
				var preloadListItem = jQuery(item).parent().parent().children(".cwp_pitem_info").children(".cwp_pitem_options_pros").children("li:eq("+(i-1)+")").text();
				if(preloadListItem != "-") { jQuery("input#cwp_option_" + i + "_pro").val(preloadListItem); }
			};
		}

		function cwpPreloadPros(item)
		{
			for (var i = 1; i <= <?php echo cwppos("cwppos_option_nr");?>; i++) {
				var preloadListItem = jQuery(item).parent().parent().children(".cwp_pitem_info").children(".cwp_pitem_options_cons").children("li:eq("+(i-1)+")").text();
				if(preloadListItem != "-") { jQuery("input#cwp_option_" + i + "_cons").val(preloadListItem); }
			};
		}
	jQuery(".preload_info").click(function(e){
		e.preventDefault();

		var cwpThemeUrl = '<?php echo plugins_url( '', __FILE__ ); ?>';
		var ajaxLoad = "<img class='ajax_load_icon' src='" + cwpThemeUrl +"/images/ajaxload.gif' alt='Loading...'/>";
		var loadUrl = cwpThemeUrl + "/review-preloader.php";

		jQuery("body #wpwrap").append("<div class='preload_result'><div class='preload_inner'><header><h2>Preload Info</h2><div class='preload_close'></div></header><div class='preloader_body'><ul class='preload_list'></ul></div></div></div>");
		jQuery(".preload_result").fadeIn();

		jQuery(".preload_close").bind("click", function(){
			jQuery(".preload_result").fadeOut();
		});

		jQuery(".preload_list").html(ajaxLoad).load(loadUrl);


		jQuery(".preload_list .cwp_p_title").live("click", function(){
			jQuery(this).parent().parent().children(".cwp_pitem_info").slideToggle();
		});

		jQuery(".preload_list li button.preload").live("click", function(){
			cwpPreloadOptions(this);
			cwpPreloadCons(this);
			cwpPreloadPros(this);
			jQuery(".preload_result").fadeOut();
		});

		jQuery(".preload_list .cwp_pitem_options_content li").live("click",function(){
			var plIndex = jQuery(this).index();
			var preloadListItem = jQuery(this).text();
			if(preloadListItem != "-") { jQuery("input#option_" + plIndex + "_content").val(preloadListItem); }
		});

		jQuery(".preload_list .cwp_pitem_options_pros li").live("click",function(){
			var plIndex = jQuery(this).index();
			var preloadListItem = jQuery(this).text();
			if(preloadListItem != "-") { jQuery("input#cwp_option_" + plIndex + "_pro").val(preloadListItem); }
		});

		jQuery(".preload_list .cwp_pitem_options_cons li").live("click",function(){
			var plIndex = jQuery(this).index();
			var preloadListItem = jQuery(this).text();
			if(preloadListItem != "-") { jQuery("input#cwp_option_" + plIndex + "_cons").val(preloadListItem); }
		});
		
	 });
});
