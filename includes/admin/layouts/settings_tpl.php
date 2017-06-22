<?php
/**
 *  Settings layout in the admin dashboard.
 *
 * @package
 * @subpackage
 * @copyright   Copyright (c) 2017, Marius Cristea
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       3.0.0
 */

$global_settings = WPPR_Global_Settings::instance();
$sections = $global_settings->get_sections();
$fields = $global_settings->get_fields();

?>
<div id="wppr-admin">
	<?php do_action( 'wppr_admin_page_before' ); ?>

	<?php include WPPR_PATH . '/includes/admin/layouts/header_part.php'; ?>

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
				foreach ( $fields[ $section_key ] as $name => $field ) {
				    $field['name'] = $name;
				    $field['value'] = WPPR_Options::instance()->get_var( $name );
					$this->add_element( $field );
				}
				?>
			</div>

		<?php endforeach; ?>

		<div id="info_bar">
			<span class="spinner" ></span>
			<button  type="button" class="button-primary cwp_save"><?php _e( 'Save All Changes','cwppos' ); ?></button>
			<span class="spinner spinner-reset" ></span>
			<button   type="button" class="button submit-button reset-button cwp_reset"><?php _e( 'Options Reset','cwppos' ); ?></button>
		</div><!--.info_bar-->
	</form>
	<?php do_action( 'wppr_admin_page_after' ); ?>
</div>
