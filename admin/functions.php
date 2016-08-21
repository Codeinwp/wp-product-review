<?php
    global $ABTESTING_PLUGIN_SLUG;
    $ABTESTING_PLUGIN_SLUG  = "wppr";

    add_filter($ABTESTING_PLUGIN_SLUG . "_upsell_config", "wppr_upsell_config");

    function wppr_upsell_config($config)
    {
        return array (
            "icons" => array("<b>html string 1</b>", "<b>html</b> string 2</b>", "html string 3</b>"),
            "preloader" => array("<b>html string 1</b>", "<b>html string 2</b>", "<b>html string 3</b>") 
        );
    }

    require(  plugin_dir_path (   __FILE__ ) . 'inc/config.php' ); 
    require(  plugin_dir_path (   __FILE__) . 'inc/adminOptionsValidator.php' );
    require(  plugin_dir_path (   __FILE__ ) . 'inc/adminSanitizer.php' );
    require(  plugin_dir_path (   __FILE__ ) . 'inc/render.php' ); 
    require(  plugin_dir_path ( __FILE__ ) . 'inc/loader.php' );  
