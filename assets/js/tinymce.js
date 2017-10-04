/**
 * Plugin Name: WP Product Review
 * Author: Themeisle
 *
 * @package wp-product-review
 */
/* global tinymce */
/* jshint unused:false */
(function($) {
	tinymce.PluginManager.add('wppr_mce_button', function( editor, url ) {
		editor.addButton( 'wppr_mce_button', {
            type: 'menubutton',
			title: editor.getLang( 'wppr_tinymce_plugin.plugin_label' ),
			label: editor.getLang( 'wppr_tinymce_plugin.plugin_label' ),
			icon: 'wppr-icon',
            menu: renderMenu( editor )
		});
	});

    function renderMenu( editor ) {
        var menu    = [];
        var items = JSON.parse( editor.getLang( 'wppr_tinymce_plugin.buttons' ) );
        $.each(items, function(i, j){
            menu.push({
                'text': j.text,
                'onclick': function(){
                    editor.windowManager.open( {
					    title: editor.getLang( 'wppr_tinymce_plugin.plugin_title' ),
					    url: editor.getLang( 'wppr_tinymce_plugin.popup_url' ) + '&amp;action=wppr_get_tinymce_form&amp;type=' + j.type,
                        width: $( window ).width() * 0.7,
                        height: ($( window ).height() - 36 - 50) * 0.7,
                        inline: 1,
                        id: 'wppr-insert-dialog',
                        buttons: getButtons( editor ),
                        }, {
                            editor: editor,
                            jquery: $,
                            wp: wp,
                    });
                }
            });
        });
        return menu;
    }

    function getButtons( editor ) {
        var buttons = [];

        if(editor.getLang( 'wppr_tinymce_plugin.ispro' ) !== true ){
            buttons.push({
                            text: editor.getLang( 'wppr_tinymce_plugin.pro_button' ),
                            id: 'wppr-button-pro',
                            onclick: function( e ) {
                                openProLink( e, editor );
                            }
            });
        }

        buttons.push({
                            text: editor.getLang( 'wppr_tinymce_plugin.cancel_button' ),
                            id: 'wppr-button-cancel',
                            onclick: 'close'
        });
        buttons.push({
                            text: editor.getLang( 'wppr_tinymce_plugin.insert_button' ),
                            id: 'wppr-button-insert',
                            class: 'insert',
                            onclick: function( e ) {
                                insertShortcode( e, editor, j.shortcode );
                            },
                            disabled: editor.getLang( 'wppr_tinymce_plugin.ispro' ) !== true
        });

        return buttons;
    }

    function getFormValues(content) {
		var form = $( '#wppr_shortcode_form', content );
        return form.serialize().replace(/&/g, ' ');
    }

	function insertShortcode( e, editor, shortcode ) {
		var frame = $( e.currentTarget ).find( 'iframe' ).get( 0 );
		var content = frame.contentDocument;

		editor.insertContent('[' + shortcode + ' ' + getFormValues(content) + ']');
		editor.windowManager.close();
	}

	function openProLink( e , editor ) {
		window.open( editor.getLang( 'wppr_tinymce_plugin.pro_url' ), '_blank' );
	}
})(jQuery);
