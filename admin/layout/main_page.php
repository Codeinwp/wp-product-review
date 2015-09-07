<div id="cwp_container" style="display:none">
	<form id="cwp_form" method="post" action="#" enctype="multipart/form-data">
	<?php settings_fields( cwppos_config("menu_slug")); ?>
 
		<div id="header">
		
			<div class="logo ">
				<h2>

			    <img class="theme_options_logo" src="<?php echo plugins_url( 'img/logo.png' , __FILE__ ) . ''; ?>" alt="<?php echo cwppos_config('admin_page_header'); ?>"> 
				
				<?php if (!class_exists('CWP_PR_PRO_Core')) { ?><a href="https://themeisle.com/plugins/wp-product-review-pro-add-on/" class="read_docs button" target="_blank" style="color:red;text-decoration: none;"><?php _e("Buy the PRO Add-ons Bundle", "cwppos"); ?></a><?php } ?>
				
				<a href="https://themeisle.com/allthemes"  class="read_docs button" target="_blank" style=" text-decoration: none; "><?php _e("Recommended Review Themes", "cwppos"); ?></a>
				
				<a href="https://themeisle.com/forums/" target="_blank" class="read_docs button" style="text-decoration: none;"><?php _e("Contact us", "cwppos"); ?></a>

				</h2>
			</div>
		  
			<div class="clear"></div>
		
    		</div>

		<div id="info_bar">
		 
		 <span class="spinner" ></span>
						
			<button  type="button" class="button-primary cwp_save">
				<?php _e('Save All Changes','cwppos'); ?>			</button>
			
		 <span class="spinner spinner-reset" ></span>
			<button   type="button" class="button submit-button reset-button cwp_reset"><?php _e('Options Reset','cwppos'); ?></button>
		</div><!--.info_bar--> 	
		
		<div id="main">
		
			<div id="cwp_nav">
				<ul>
					<?php foreach ($tabs as $tab) { ?>
					
					
						<li  ><a  href="#tab-<?php echo $tab['id']; ?>"><?php echo $tab['name']; ?></a></li> 	
					
					<?php  } ?></ul>
			</div>

			<div id="content"> 	

					<?php foreach ($tabs as $tab) { ?>
						<div id="tab-<?php echo $tab['id']; ?>" class="tab-section">
							<h2><?php echo $tab['name']; ?></h2>
							
							<?php foreach($tab['elements'] as $element){ ?>
								<?php echo  $element['html']; ?>
							<?php } ?>
						
						</div> 
  
					
					<?php } ?>

				<div class="clear"></div></div>

			
		</div>
		
		<div class="save_bar"> 
		 <span class="spinner " ></span>
			<button  type="button" class="button-primary cwp_save">
				<?php _e('Save All Changes','cwppos'); ?>			</button>
			
		 <span class="spinner  spinner-reset" ></span>
			<button   type="button" class="button submit-button reset-button  cwp_reset"><?php _e('Options Reset','cwppos'); ?></button>
	 
			
		</div> 
 
	</form>
	
	<div style="clear:both;"></div>
</div>
