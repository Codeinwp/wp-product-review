jQuery(document).ready(function(){
	
	jQuery('input:radio[name="cwp_meta_box_check"]').change(function(){

    if(jQuery(this).is(':checked')){
       jQuery(".review-settings-notice").show();
       jQuery(".review-settings-group").show();
       jQuery(".isReviewYes").show();
       
    }
});


})