<?php
/**
 * Admin View: Page - Addons
 */


function cwp_addons() {
?>

<div class="cwp_addons_wrap">
	<div class="icon32 icon32-posts-product" id="icon-woocommerce"><br /></div>
	<h2>
		<?php _e( 'WP Product Review Add-ons/Extensions', 'cwppos' ); ?>
		<a href="https://themeisle.com/plugins/" class="add-new-h2"><?php _e( 'Browse all plugins', 'cwppos' ); ?></a>
		<a href="https://themeisle.com/allthemes/" class="add-new-h2"><?php _e( 'Browse themes', 'cwppos' ); ?></a>
	</h2>
	<?php 
	if ( false === ( $addons = get_transient( 'wpprx_addons_data' ) ) ) {
			$addons_json = wp_remote_get( 'https://themeisle-vertigostudio.netdna-ssl.com/wp-content/uploads/wppr-addons.json', array( 'user-agent' => 'WPPR Addons Page' ) );
			if ( ! is_wp_error( $addons_json ) ) {
				$addons = json_decode( wp_remote_retrieve_body( $addons_json ) );
				if ( $addons ) {
					set_transient( 'wppr_addons_data', $addons, 604800 );
				}
			}
	}
	//print_r($addons);
	/* $addons[0] = array(
    "title" => "Custom Icon",
    "excerpt" => "This add-on add a custom icon functionality to your review box, so you can add things like star icon, $ icon and much more.",
    "link" =>"https://themeisle.com/plugins/WPPR-custom-icon/",
    "price" =>"$19"
	);*/

	if ( $addons ) :  ?>

		<ul class="products">
		<?php

			foreach ( $addons as $addon ) {

				echo '<li class="product">';
				echo '<a href="' . $addon->link . '">';
				if ( ! empty( $addon->image ) ) {
					echo '<img src="' . $addon->image . '"/>';
				} else {
					echo '<h3>' . $addon->title . '</h3>';
				}
				echo '<span class="price">' . $addon->price . '</span>';
				echo '<p>' . $addon->excerpt . '</p>';
				echo '<button class="cwp-cta">Get it now for '.$addon->price.'</button>';
				echo '</a>';
				echo '</li>';
			}
		?>
		</ul>
	<?php else : ?>
		<p><?php printf( __( 'Our catalog of WP Product Review Extensions can be found on ThemeIsle.com here: <a href="%s">WP Product Review Extensions</a>', 'cwppos' ), 'https://themeisle.com/plugins/' ); ?></p>
	<?php endif; ?>
</div>
<?php } ?>