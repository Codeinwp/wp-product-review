<?php
if (!class_exists('CWP_PR_PRO_Core')){
	class CWP_PR_PRO_Core {

		// All fields
			

		public function __construct() {
			// Get all fields
			//global $cwp_top_fields;

			// Set all authentication settings
			$this->loadAllHooks();

		}

		public function addLocalization() {
 
 			load_plugin_textdomain(CWP_TEXTDOMAIN, false, dirname(ROPPLUGINBASENAME).'/languages/');
 		}

 		public function preload_js() {

			// Register & enqueue the preload js script.
			wp_register_script("cwp-review-preload", plugins_url( 'cwp-review-preload.php', __FILE__ ), false, "1.0", "all");
			wp_enqueue_script("cwp-review-preload");
	  
	
 		}

 		public function loadAllHooks() 
		{
			//add_action( 'plugins_loaded', array($this, 'addLocalization') );
			add_action( 'admin_enqueue_scripts',  array($this, "preload_js"));
		}



 	}
 }
 	?>