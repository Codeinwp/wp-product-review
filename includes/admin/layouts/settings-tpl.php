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
				<li class="wppr-nav-tab" id="wppr-nav-tab-<?php echo esc_attr( $section_key ); ?>"
					data-tab="wppr-tab-<?php echo esc_attr( $section_key ); ?>">
					<a href="#wppr-tab-<?php echo esc_attr( $section_key ); ?>" title="<?php esc_attr( $section_name ); ?>">
						<?php echo esc_html( $section_name ); ?>
					</a>
				</li>
			<?php endforeach; ?>
		</ul>

	</div>
	<form id="wppr-settings" method="post" action="#" enctype="multipart/form-data">

		<?php foreach ( $sections as $section_key => $section_name ) : ?>
			<div id="wppr-tab-<?php echo esc_attr( $section_key ); ?>" class="wppr-tab-content">
				<div class="controls migration-notice">
					<?php _e( 'WP Product Review is not being maintained anymore. You can migrate your data to Otter\'s Review Block and keep most of the functionality and continue receiving updates.', 'wp-product-review' ); ?>
					<br/>
					<a href="https://docs.themeisle.com/article/1360-migrating-from-wp-product-review-to-otters-review-block"><?php _e( 'Learn more', 'wp-product-review' ); ?></a>

					<?php if ( ! defined( 'THEMEISLE_BLOCKS_VERSION' ) ) : ?>
						- <a href="<?php echo admin_url( 'plugin-install.php?tab=plugin-information&plugin=otter-blocks' ); ?>" target="_blank"><?php _e( 'Install', 'wp-product-review' ); ?></a>
					<?php endif; ?>
				</div>
				<hr>

				<?php
				if ( shortcode_exists( 'P_REVIEW' ) ) {
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
