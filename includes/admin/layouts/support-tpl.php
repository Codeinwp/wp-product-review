<div id="wppr-admin" class="wppr-settings">

	<?php include WPPR_PATH . '/includes/admin/layouts/header-part.php'; ?>

	<?php
	$active_tab  = isset( $_REQUEST['tab'] ) ? sanitize_text_field( $_REQUEST['tab'] ) : 'help';
	?>

	<h2 class="nav-tab-wrapper">
		<a href="<?php echo esc_url( admin_url( 'admin.php?page=wppr-support&tab=help' ) ); ?>"
		   class="nav-tab <?php echo $active_tab === 'help' ? 'nav-tab-active' : ''; ?>"><?php _e( 'Support', 'wp-product-review' ); ?></a>
	</h2>

	<div class="wppr-features-content">
		<div class="wppr-feature">
			<div class="wppr-feature-features">
					<?php
					switch ( $active_tab ) {
						case 'help':
								include WPPR_PATH . '/includes/admin/layouts/support-tab.php';
							break;
					}
					?>
				<div class="clear"></div>
			</div>
		</div>
	</div>

</div>
