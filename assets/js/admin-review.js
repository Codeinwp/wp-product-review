/* jshint ignore:start */
jQuery( document ).ready(function(){

	var meta_image_frame;

	jQuery( '#wppr-editor-image-button' ).click(function(e){

		e.preventDefault();

		if ( meta_image_frame ) {
			wp.media.frame.open();
			return;
		}
		var mtitle = "Add a product image to the review";
		var mbutton = "Attach the image ";

		meta_image_frame = wp.media.frames.meta_image_frame = wp.media({
			title: mtitle,
			button: { text:  mbutton },
			library: { type: 'image' }
		});

		meta_image_frame.on('select', function(){

			var media_attachment = meta_image_frame.state().get( 'selection' ).first().toJSON();

			jQuery( '#wppr-editor-image' ).val( media_attachment.url );
		});

		wp.media.frame.open();
	});

	jQuery( 'input:radio[name="wppr-review-status"]' ).change(function(){
		var value = jQuery( this ).val();
		if (value === "yes") {

			jQuery( "#wppr-meta-yes" ).show();
		} else {
            jQuery( "#wppr-meta-yes" ).hide();
		}
	});

	jQuery( '#cwp_add_button' ).click(function(e){
		e.preventDefault();
		jQuery( '.cwp_hide_button2' ).show();
		jQuery( this ).hide();
		return false;
	})

});
