/* jshint ignore:start */
(function($){
    
    $( document ).ready(function(){

        var meta_image_frame;

        $( '#wppr-editor-image-button' ).click(function(e){

            e.preventDefault();

            if ( meta_image_frame ) {
                wp.media.frame.open();
                return;
            }
            var mtitle = editor_vars.image_title ;
            var mbutton = editor_vars.image_button;

            meta_image_frame = wp.media.frames.meta_image_frame = wp.media({
                title: mtitle,
                button: { text:  mbutton },
                library: { type: 'image' }
            });

            meta_image_frame.on('select', function(){

                var media_attachment = meta_image_frame.state().get( 'selection' ).first().toJSON();

                $( '#wppr-editor-image' ).val( media_attachment.url );
            });

            wp.media.frame.open();
        });

        $( 'input:radio[name="wppr-review-status"]' ).change(function(){
            var value = $( this ).val();
            if (value === "yes") {

                $( "#wppr-meta-yes" ).show();
                $( "#wppr-meta-no" ).show();
            } else {
                $( "#wppr-meta-yes" ).hide();
                $( "#wppr-meta-no" ).hide();
            }
        });

        $( '#wppr-editor-new-link' ).click(function(e){
            e.preventDefault();
            $( '.hidden_fields' ).show();
            $( this ).hide();
            return false;
        });

        $type = $('#wppr-editor-review-type').val();
        if($type !== ''){
            var fields = $('#wppr-review-type-fields-template').attr('data-json');
            if(typeof fields !== 'undefined'){
                fields = JSON.parse(fields);
            }
            populate_schema(fields);
        }

        $('#wppr-editor-review-type').on('change', function(e){
            var $locker = $('.wppr-review-details-fields');
            $locker.lock();
            $type = $(this).val();
            $.ajax({
                url: ajaxurl,
                data: {
                    type: $type,
                    nonce: editor_vars.nonce,
                    action: 'get_schema_fields'
                },
                success: function(data){
                    $('.wppr-review-type-link a').attr('href', data.data.url);
                    populate_schema(data.data.fields);
                    $('.wppr-review-type').accordion({active: 0});
                },
                complete: function(){
                    $locker.unlock();
                }
            });
        });

        $('.wppr-review-type-link a').on('click', function(e){
            e.stopPropagation();
        });

    });

    function populate_schema(fields){
        var custom = $('#wppr-review-type-fields-template').attr('data-custom-fields');
        if(typeof custom !== 'undefined'){
           custom = JSON.parse(custom);
        }
        $template = $('#wppr-review-type-fields-template').html();
        $html = '';
        if(fields !== null){
            $.each(fields, function(index, name){
                $value = custom !== null ? custom[name] : '';
                if(typeof $value === 'undefined'){
                    $value = '';
                }
                $html += $template.replace(/#name#/g, name).replace(/#value#/g, $value);
            });
        }
        $('.wppr-review-type-fields').empty().append($html);
    }

})(jQuery);


(function ($) {
    $.fn.lock = function () {
        $(this).each(function () {
            var $this = $(this);
            var position = $this.css('position');

            if (!position) {
                position = 'static';
            }

            switch (position) {
                case 'absolute':
                case 'relative':
                    break;
                default:
                    $this.css('position', 'relative');
                    break;
            }
            $this.data('position', position);

            var width = $this.width(),
                height = $this.height();

            var locker = $('<div class="locker"></div>');
            locker.width(width).height(height);

            var loader = $('<div class="locker-loader"></div>');
            loader.width(width).height(height);

            locker.append(loader);
            $this.append(locker);
            $(window).resize(function () {
                $this.find('.locker,.locker-loader').width($this.width()).height($this.height());
            });
        });

        return $(this);
    };

    $.fn.unlock = function () {
        $(this).each(function () {
            $(this).find('.locker').remove();
            $(this).css('position', $(this).data('position'));
        });

        return $(this);
    };
})(jQuery);
