<?php
/**
 * Header for admin pages.
 *
 * @package     WPPR
 * @subpackage  WPPR/Layouts
 * @copyright   Copyright (c) 2017, Marius Cristea
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       3.0.0
 */

?>
<div class="pro-features-header">
	<p class="logo">WP Product Review</p>
	<span class="slogan">by <a
			href="https://themeisle.com/">ThemeIsle</a></span>
	<div class="header-btns">
		<?php if ( ! class_exists( 'WPPR_Pro' ) ) { ?>
		<a target="_blank" href="<?php echo WPPR_UPSELL_LINK; ?>" class="buy-now"><span
				class="dashicons dashicons-cart"></span> More features</a>
		<?php } ?>
	</div>
	<div class="clear"></div>
</div>
