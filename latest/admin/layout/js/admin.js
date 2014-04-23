
jQuery("document").ready(function() {
 

	jQuery("#cwp_container").fadeIn(200);
	
    var _custom_media = true,

      _orig_send_attachment = wp.media.editor.send.attachment;



  jQuery('.clear-image').click(function(e) {
    var button =   jQuery(this);
	 var id = button.attr('id').replace('_buttonclear', '');
		
          jQuery("#"+id).val(''); 
		  jQuery("#"+id+"_image").attr("src",'').hide();
	return false;
  });

  jQuery('.selector-image').click(function(e) {

    var send_attachment_bkp = wp.media.editor.send.attachment;

    var button =   jQuery(this);

    var id = button.attr('id').replace('_button', '');

    _custom_media = true;

	 wp.media.frames.file_frame = wp.media({
            title: "Select Image",
            button: {
                text: 'Select Image'
            },
            multiple: false
        }); 
    wp.media.editor.send.attachment = function(props, attachment){

      if ( _custom_media ) {

          jQuery("#"+id).val(attachment.url); 
		  jQuery("#"+id+"_image").attr("src",attachment.url).show();

		jQuery("#"+id+"-gradientcolor").hide();
		jQuery("#"+id+"-gradientselector").hide();
      } else {

        return _orig_send_attachment.apply( this, [props, attachment] );

      };

    }
	
    wp.media.editor.open(button);
	setInterval(function(){
		if(jQuery('a.media-button-insert').text() != 'Use this Image' )
			jQuery('a.media-button-insert').text('Use this Image');
	},100)
    return false;

  });
	
   jQuery(".subo-color-picker").wpColorPicker({  change: function(event, ui){
		var color = ui.color.toCSS();
		console.log(color);
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

});