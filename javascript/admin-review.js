jQuery(document).ready(function(){

    if (ispro.value ==true) jQuery("#cwp_nav li:last").hide();
     // Instantiates the variable that holds the media library frame.
    var meta_image_frame;
 
    // Runs when the image button is clicked.
    jQuery('#cwp_rev_product_image-button').click(function(e){
 
        // Prevents the default action from occuring.
        e.preventDefault();
 
        // If the frame already exists, re-open it.
        if ( meta_image_frame ) {
            wp.media.frame.open();
            return;
        }
        var mtitle="Add a product image to the review";
        var mbutton = "Attach the image ";
        // Sets up the media library frame
        meta_image_frame = wp.media.frames.meta_image_frame = wp.media({
            title: mtitle,
            button: { text:  mbutton },
            library: { type: 'image' }
        });
 
        // Runs when an image is selected.
        meta_image_frame.on('select', function(){
 
            // Grabs the attachment selection and creates a JSON representation of the model.
            var media_attachment = meta_image_frame.state().get('selection').first().toJSON();
            // Sends the attachment URL to our custom image input field.
            jQuery('#cwp_rev_product_image').val(media_attachment.url);
        });
 
        // Opens the media library frame.
        wp.media.frame.open();
    });

    jQuery('input:radio[name="cwp_meta_box_check"]').change(function(){



    if(jQuery(this).is(':checked')){

       jQuery(".review-settings-notice").show();

       jQuery(".review-settings-group").show();

       jQuery(".isReviewYes").show();

       

    }

});





})