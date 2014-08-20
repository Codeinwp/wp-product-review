
jQuery("document").ready(function() {
 

	jQuery("#cwp_container").fadeIn(200);
	
    var _custom_media = true;


  jQuery('.clear-image').click(function(e) {
    var button =   jQuery(this);
	 var id = button.attr('id').replace('_buttonclear', '');
		
          jQuery("#"+id).val(''); 
		  jQuery("#"+id+"_image").attr("src",'').hide();
	return false;
  });

  
	
   jQuery(".subo-color-picker").wpColorPicker({  change: function(event, ui){
		var color = ui.color.toCSS();
		
		var id = jQuery(this).attr('id').replace('_color_selector', '');
	 
		 
		jQuery("#"+id+"_color").val(color); 
   }} );
      
  jQuery('.add_media').on('click', function(){

    _custom_media = false;

  });
  jQuery(".tab-section:first").show();
  jQuery("#cwp_nav li a").live("click",function(){
	var show = jQuery(this).attr("href");  
	jQuery(".tab-section:visible").fadeOut(200,function(){
		jQuery( show).fadeIn(300);
 
	});
	return false;
  });

  jQuery(".group-in-tab .group-name").live("click",function(){ 
	jQuery(".active-tab").slideUp(200);
	var cnt = jQuery(this).parent().find(".group-content");
	cnt.addClass("active-tab");
	
  if(cnt.is(":hidden"))
		cnt.slideDown(1000);
	else
		cnt.slideUp(200);
	return false;
  });


  jQuery(".cwp_save").live("click",function(){
				var b =  jQuery("#cwp_form").serialize();
				jQuery(".cwp_save").addClass("button-primary-disabled");
				jQuery(".cwp_reset").addClass("button-disabled");
				jQuery(".spinner").show();
                jQuery.post( 'options.php', b ).error( 
                    function() {
						jQuery(".spinner").hide();
						jQuery(".cwp_reset").removeClass("button-disabled");
						jQuery(".cwp_save").removeClass("button-primary-disabled");
                    }).success( function() { 
						jQuery(".spinner").hide();
						jQuery(".cwp_reset").removeClass("button-disabled");
						jQuery(".cwp_save").removeClass("button-primary-disabled"); 
                    });
					
  });

  jQuery(".cwp_reset").live("click",function(){		
				jQuery(".cwp_reset").addClass("button-disabled");
				jQuery(".cwp_save").addClass("button-primary-disabled");
				jQuery(".spinner ").show();
			  jQuery.post( ajaxurl,{action:"cwp_load_defaults"} ).error( 
                    function() {
						jQuery(".spinner").hide();
						jQuery(".cwp_reset").removeClass("button-disabled");
						jQuery(".cwp_save").removeClass("button-primary-disabled");
						locatino.reload();
                    }).success( function() {
					
						jQuery(".spinner").hide();
						jQuery(".cwp_reset").removeClass("button-disabled"); 
						jQuery(".cwp_save").removeClass("button-primary-disabled"); 
						location.reload();
                    });
					
  });
 
  jQuery(".cwp_tipsy ").tipsy();

		jQuery("#cwp_select_bar_icon").click(function(a) {
		    a.preventDefault();
		    a.stopPropagation();
		    //var b = [ "&#xf042", "&#xf170", "&#xf037", "&#xf039", "&#xf036", "&#xf038", "&#xf0f9", "&#xf13d", "&#xf17b", "&#xf103", "&#xf100", "&#xf101", "&#xf102", "&#xf107", "&#xf104", "&#xf105", "&#xf106", "&#xf179", "&#xf187", "&#xf0ab", "&#xf0a8", "&#xf01a", "&#xf190", "&#xf18e", "&#xf01b", "&#xf0a9", "&#xf0aa", "&#xf063", "&#xf060", "&#xf061", "&#xf062", "&#xf047", "&#xf0b2", "&#xf07e", "&#xf07d", "&#xf069", "&#xf1b9", "&#xf04a", "&#xf05e", "&#xf19c", "&#xf080", "&#xf02a", "&#xf0c9", "&#xf0fc", "&#xf1b4", "&#xf1b5", "&#xf0f3", "&#xf0a2", "&#xf171", "&#xf172", "&#xf15a", "&#xf032", "&#xf0e7", "&#xf1e2", "&#xf02d", "&#xf02e", "&#xf097", "&#xf0b1", "&#xf15a", "&#xf188", "&#xf1ad", "&#xf0f7", "&#xf0a1", "&#xf140", "&#xf1ba", "&#xf073", "&#xf133", "&#xf030", "&#xf083", "&#xf1b9", "&#xf0d7", "&#xf0d9", "&#xf0da", "&#xf150", "&#xf191", "&#xf152", "&#xf151", "&#xf0d8", "&#xf0a3", "&#xf0c1", "&#xf127", "&#xf00c", "&#xf058", "&#xf05d", "&#xf14a", "&#xf046", "&#xf13a", "&#xf137", "&#xf138", "&#xf139", "&#xf078", "&#xf053", "&#xf054", "&#xf077", "&#xf1ae", "&#xf111", "&#xf10c", "&#xf1ce", "&#xf1db", "&#xf0ea", "&#xf017", "&#xf0c2", "&#xf0ed", "&#xf0ee", "&#xf157", "&#xf121", "&#xf126", "&#xf1cb", "&#xf0f4", "&#xf013", "&#xf085", "&#xf0db", "&#xf075", "&#xf0e5", "&#xf086", "&#xf0e6", "&#xf14e", "&#xf066", "&#xf0c5", "&#xf09d", "&#xf125", "&#xf05b", "&#xf13c", "&#xf1b2", "&#xf1b3", "&#xf0c4", "&#xf0f5", "&#xf0e4", "&#xf1c0", "&#xf03b", "&#xf1a5", "&#xf108", "&#xf1bd", "&#xf1a6", "&#xf155", "&#xf192", "&#xf019", "&#xf17d", "&#xf16b", "&#xf1a9", "&#xf044", "&#xf052", "&#xf141", "&#xf142", "&#xf1d1", "&#xf0e0", "&#xf003", "&#xf199", "&#xf12d", "&#xf153", "&#xf153", "&#xf0ec", "&#xf12a", "&#xf06a", "&#xf071", "&#xf065", "&#xf08e", "&#xf14c", "&#xf06e", "&#xf070", "&#xf09a", "&#xf082", "&#xf049", "&#xf050", "&#xf1ac", "&#xf182", "&#xf0fb", "&#xf15b", "&#xf1c6", "&#xf1c7", "&#xf1c9", "&#xf1c3", "&#xf1c5", "&#xf1c8", "&#xf016", "&#xf1c1", "&#xf1c5", "&#xf1c5", "&#xf1c4", "&#xf1c7", "&#xf15c", "&#xf0f6", "&#xf1c8", "&#xf1c2", "&#xf1c6", "&#xf0c5", "&#xf008", "&#xf0b0", "&#xf06d", "&#xf134", "&#xf024", "&#xf11e", "&#xf11d", "&#xf0e7", "&#xf0c3", "&#xf16e", "&#xf0c7", "&#xf07b", "&#xf114", "&#xf07c", "&#xf115", "&#xf031", "&#xf04e", "&#xf180", "&#xf119", "&#xf11b", "&#xf0e3", "&#xf154", "&#xf1d1", "&#xf013", "&#xf085", "&#xf06b", "&#xf1d3", "&#xf1d2", "&#xf09b", "&#xf113", "&#xf092", "&#xf184", "&#xf000", "&#xf0ac", "&#xf1a0", "&#xf0d5", "&#xf0d4", "&#xf19d", "&#xf0c0", "&#xf0fd", "&#xf1d4", "&#xf0a7", "&#xf0a5", "&#xf0a4", "&#xf0a6", "&#xf0a0", "&#xf1dc", "&#xf025", "&#xf004", "&#xf08a", "&#xf1da", "&#xf015", "&#xf0f8", "&#xf13b", "&#xf03e", "&#xf01c", "&#xf03c", "&#xf129", "&#xf05a", "&#xf156", "&#xf16d", "&#xf19c", "&#xf033", "&#xf1aa", "&#xf157", "&#xf1cc", "&#xf084", "&#xf11c", "&#xf159", "&#xf1ab", "&#xf109", "&#xf06c", "&#xf0e3", "&#xf094", "&#xf149", "&#xf148", "&#xf1cd", "&#xf1cd", "&#xf1cd", "&#xf0eb", "&#xf0c1", "&#xf0e1", "&#xf08c", "&#xf17c", "&#xf03a", "&#xf022", "&#xf0cb", "&#xf0ca", "&#xf124", "&#xf023", "&#xf175", "&#xf177", "&#xf178", "&#xf176", "&#xf0d0", "&#xf076", "&#xf064", "&#xf112", "&#xf122", "&#xf183", "&#xf041", "&#xf136", "&#xf0fa", "&#xf11a", "&#xf130", "&#xf131", "&#xf068", "&#xf056", "&#xf146", "&#xf147", "&#xf10b", "&#xf10b", "&#xf0d6", "&#xf186", "&#xf19d", "&#xf001", "&#xf0c9", "&#xf19b", "&#xf03b", "&#xf18c", "&#xf1d8", "&#xf1d9", "&#xf0c6", "&#xf1dd", "&#xf0ea", "&#xf04c", "&#xf1b0", "&#xf040", "&#xf14b", "&#xf044", "&#xf095", "&#xf098", "&#xf03e", "&#xf03e", "&#xf1a7", "&#xf1a8", "&#xf1a7", "&#xf0d2", "&#xf0d3", "&#xf072", "&#xf04b", "&#xf144", "&#xf01d", "&#xf067", "&#xf055", "&#xf0fe", "&#xf196", "&#xf011", "&#xf02f", "&#xf12e", "&#xf1d6", "&#xf029", "&#xf128", "&#xf059", "&#xf10d", "&#xf10e", "&#xf1d0", "&#xf074", "&#xf1d0", "&#xf1b8", "&#xf1a1", "&#xf1a2", "&#xf021", "&#xf18b", "&#xf0c9", "&#xf01e", "&#xf112", "&#xf122", "&#xf079", "&#xf157", "&#xf018", "&#xf135", "&#xf0e2", "&#xf01e", "&#xf158", "&#xf09e", "&#xf143", "&#xf158", "&#xf158", "&#xf156", "&#xf0c7", "&#xf0c4", "&#xf002", "&#xf010", "&#xf00e", "&#xf1d8", "&#xf1d9", "&#xf064", "&#xf1e0", "&#xf1e1", "&#xf14d", "&#xf045", "&#xf132", "&#xf07a", "&#xf090", "&#xf08b", "&#xf012", "&#xf0e8", "&#xf17e", "&#xf198", "&#xf1de", "&#xf118", "&#xf0dc", "&#xf15d", "&#xf15e", "&#xf160", "&#xf161", "&#xf0de", "&#xf0dd", "&#xf0dd", "&#xf162", "&#xf163", "&#xf0de", "&#xf1be", "&#xf197", "&#xf110", "&#xf1b1", "&#xf1bc", "&#xf0c8", "&#xf096", "&#xf18d", "&#xf16c", "&#xf005", "&#xf089", "&#xf123", "&#xf123", "&#xf123", "&#xf006", "&#xf1b6", "&#xf1b7", "&#xf048", "&#xf051", "&#xf0f1", "&#xf04d", "&#xf0cc", "&#xf1a4", "&#xf1a3", "&#xf12c", "&#xf0f2", "&#xf185", "&#xf12b", "&#xf1cd", "&#xf0ce", "&#xf10a", "&#xf0e4", "&#xf02b", "&#xf02c", "&#xf0ae", "&#xf1ba", "&#xf1d5", "&#xf120", "&#xf034", "&#xf035", "&#xf00a", "&#xf009", "&#xf00b", "&#xf08d", "&#xf165", "&#xf088", "&#xf087", "&#xf164", "&#xf145", "&#xf00d", "&#xf057", "&#xf05c", "&#xf043", "&#xf150", "&#xf191", "&#xf152", "&#xf151", "&#xf014", "&#xf1bb", "&#xf181", "&#xf091", "&#xf0d1", "&#xf195", "&#xf173", "&#xf174", "&#xf195", "&#xf099", "&#xf081", "&#xf0e9", "&#xf0cd", "&#xf0e2", "&#xf19c", "&#xf127", "&#xf09c", "&#xf13e", "&#xf0dc", "&#xf093", "&#xf155", "&#xf007", "&#xf0f0", "&#xf0c0", "&#xf03d", "&#xf194", "&#xf1ca", "&#xf189", "&#xf027", "&#xf026", "&#xf028", "&#xf071", "&#xf1d7", "&#xf18a", "&#xf1d7", "&#xf193", "&#xf17a", "&#xf159", "&#xf19a", "&#xf0ad", "&#xf168", "&#xf169", "&#xf19e", "&#xf157", "&#xf167", "&#xf16a", "&#xf166" ];
		    var b = [
		    	"&#xf005",
		    	"&#xf155"
		    ];
		    if (0 != jQuery("#bar-icon-elements").length) jQuery("#bar-icon-elements").addClass("active").show(); else insertIconContainer(b);
		});

		function insertIconContainer(a) {
		    jQuery("#cwp_container").append("<div id='bar-icon-elements' class='active'></div>");
		    a.forEach(function(a) {
		        iC = a.substring(1);
		        var b = "<i id='" + iC + "' class='fa icon fa-fw'>" + a + "</i>";
		        jQuery("#bar-icon-elements").append(b);
		    });
		    jQuery("#bar-icon-elements").append("<span class='closeModal'><i class='fa fa-times'></i></span>");
		    jQuery("#bar-icon-elements .closeModal").click(function(a) {
		        a.preventDefault();
		        d();
		        return;
		    });
		}

		function d() {
		    jQuery("#bar-icon-elements").removeClass("active").hide();
		    return;
		}

		function r() {
			jQuery(".current_bar_icon").html("").text("* Currently set to the default styling.");
			jQuery("input[type='hidden']#cwp_bar_icon_field").val("");
		}

		jQuery("#cwp_container").on("click", "#bar-icon-elements i.icon", function(a) {
		    a.preventDefault();
		    var b = jQuery(this).attr("id");
		    var uD = "<a href='#' class='useDefault'>Use Default Styling</a>";
		    jQuery("#bar-icon-elements i.active").removeClass("active");
		    jQuery(this).addClass("active");
		    jQuery(".current_bar_icon").text("").append("<i class='fa fa-fw'>&" + b + "</i>" + uD);
		    jQuery("input[type='hidden']#cwp_bar_icon_field").val(b);
		    d();
		});

		jQuery(".review-settings-group").on("click", ".useDefault", function(a){
			a.preventDefault();
			a.stopPropagation();
			r();
		});

});