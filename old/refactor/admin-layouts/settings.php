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

$sections = WPPR_Global_Settings::instance()->get_sections();

?>
<div id="wppr-admin">
	<?php do_action( 'wppr_admin_page_before' ); ?>
	
	<?php wppr_admin_template( 'header-layout' ); ?>
	
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
	<form id="wppr-settings">
		
		<?php foreach ( $sections as $section_key => $section_name ) : ?>
			<div id="wppr-tab-<?php echo $section_key; ?>" class="wppr-tab-content">
				<?php echo $section_name; ?>
			</div>
		
		<?php endforeach; ?>
	</form>
	<?php do_action( 'wppr_admin_page_after' ); ?>
</div>
