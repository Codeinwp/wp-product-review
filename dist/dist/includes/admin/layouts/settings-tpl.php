<?php
/**
 *  Settings layout in the admin dashboard.
 *
 * @package     WPPR
 * @subpackage  Layouts
 * @copyright   Copyright (c) 2017, Marius Cristea
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       3.0.0
 */

$global_settings = WPPR_Global_Settings::instance();
$sections        = $global_settings->get_sections();
$fields          = $global_settings->get_fields();

?>
<div id="wppr-admin">
	<?php do_action( 'wppr_admin_page_before' ); ?>

	<?php include WPPR_PATH . '/includes/admin/layouts/header-part.php'; ?>

	<div id="wppr_top_tabs" class="clearfix">
		<ul id="tabs_menu" role="menu">
			<?php foreach ( $sections as $section_key => $section_name ) : ?>
				<li class="wppr-nav-tab" id="wppr-nav-tab-<?php echo $section_key; ?>"
					data-tab="wppr-tab-<?php echo $section_key; ?>">
					<a href="#wppr-tab-<?php echo $section_key; ?>" title="<?php esc_attr( $section_name ); ?>">
						<?php echo esc_html( $section_name ); ?>
					</a>
				</li>
			<?php endforeach; ?>
		</ul>

	</div>
	<form id="wppr-settings" method="post" action="#" enctype="multipart/form-data">

		<?php foreach ( $sections as $section_key => $section_name ) : ?>
			<div id="wppr-tab-<?php echo $section_key; ?>" class="wppr-tab-content">
				<?php
				if ( ! shortcode_exists( 'P_REVIEW' ) ) {
					?>
				<label class="wppr-upsell-label"> You can use the shortcode <b>[P_REVIEW]</b> to show a review you
				already made or
				<b>[wpr_landing]</b> to display a comparison table of them. The shortcodes are available on the
				<a
						target="_blank" href="<?php echo WPPR_UPSELL_LINK; ?>">Pro Bundle</a><br/><br/></label>
					<?php
				} else {
					do_action( 'wppr_settings_section_upsell', $section_key );
				}
				foreach ( $fields[ $section_key ] as $name => $field ) {
					$field['title'] = $field['name'];
					$field['name']  = $name;
					$field['value'] = $model->wppr_get_option( $name );
					$this->add_element( $field );
				}
				?>
			</div>

		<?php endforeach; ?>

		<div id="info_bar">
			<button type="button"
					class="button-primary cwp_save"><?php _e( 'Save All Changes', 'wp-product-review' ); ?></button>
			<span class="spinner"></span>
		</div><!--.info_bar-->
		<?php wp_nonce_field( 'wppr_save_global_settings', 'wppr_nonce_settings', false ); ?>
	</form>
	<?php do_action( 'wppr_admin_page_after' ); ?>
</div>
