jQuery(document).ready(function(){

    if (ispro.value ==true) jQuery("#cwp_nav li:last").hide();
    
    var meta_image_frame;

    
    jQuery('#cwp_rev_product_image-button').click(function(e){

        
        e.preventDefault();

        
        if ( meta_image_frame ) {
            wp.media.frame.open();
            return;
        }
        var mtitle="Add a product image to the review";
        var mbutton = "Attach the image ";
        
        meta_image_frame = wp.media.frames.meta_image_frame = wp.media({
            title: mtitle,
            button: { text:  mbutton },
            library: { type: 'image' }
        });

        
        meta_image_frame.on('select', function(){

            
            var media_attachment = meta_image_frame.state().get('selection').first().toJSON();

            
            jQuery('#cwp_rev_product_image').val(media_attachment.url);
        });

        
        wp.media.frame.open();
    });

    jQuery('input:radio[name="cwp_meta_box_check"]').change(function(){
        var value = jQuery(this).val();
        if(value === "Yes"){

            jQuery("#cwp_review_meta_box .product-review-meta-No").show();
            jQuery("#cwp_review_meta_box .product-review-meta-Yes").show();
        }else{
            jQuery("#cwp_review_meta_box .product-review-meta-Yes").hide();
            jQuery("#cwp_review_meta_box .product-review-meta-No").hide();
        }
    });





});