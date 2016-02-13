<?php
/**
 * Admin View: Page - Addons
 */


function cwp_addons() {
?>

<div class="cwp_addons_wrap">
<div class="announcement clearfix" style="
    width: 99%;  
    background: no-repeat left -10px top -45px, #f16848;  
    margin-top: 20px;
    float: left;
    clear: both;
    margin-bottom: 10px;
">
		<h2 style="
    width: 75%;  
    float: left;  font-family: &quot;Helvetica Neue&quot;, HelveticaNeue, sans-serif;  color: #fff;  font-weight: 100;  font-size: 17px;  line-height: 1;  
    padding-left: 20px;
">Get the PRO addons bundle (including all existing add-on and future ones ) starting from $75!</h2>
		<a class="show-me" href="https://themeisle.com/plugins/wp-product-review-pro-add-on/?utm_source=addonsadmin&amp;utm_medium=announce&amp;utm_campaign=top" style="
    float: right;  background: #fff;  border-radius: 5px;  font-family: &quot;Helvetica Neue&quot;, HelveticaNeue, sans-serif;  color: #5c5c5c;  text-decoration: none;  text-transform: uppercase;  padding: 7px 15px;  margin-top: 9px;  margin-right: 20px;  -webkit-transition: all 0.3s ease-in-out;  -moz-transition: all 0.3s ease-in-out;  -o-transition: all 0.3s ease-in-out;  transition: all 0.3s ease-in-out;  line-height: 1;
">Show Me</a>
	</div>
	<div class="icon32 icon32-posts-product" id="icon-woocommerce"><br /></div>
	<h2>
		<?php esc_html_e( 'WP Product Review Add-ons/Extensions', 'cwppos' ); ?>
		<a href="https://themeisle.com/plugins/" class="add-new-h2"><?php esc_html_e( 'See all extensions', 'cwppos' ); ?></a>
	</h2><?php
	if ( false === ( $addons = get_transient( 'wppr_addons_data' ) ) ) {
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

		<ul class="products"><?php

		foreach ( $addons as $addon ) {

			echo '<li class="product">';

			echo '<a target="_blank" href="' . esc_url( $addon->link ) . '">';
			if ( ! empty( $addon->image ) ) {
				echo '<img src="' . esc_url($addon->image) . '"/>';
			} else {
				echo '<h3>' . $addon->title . '</h3>';
			}
			echo '<span class="price">' . $addon->price . '</span>';
			echo '<p>' . $addon->excerpt . '</p>';
			echo '<button class="cwp-cta">Get it now for '.$addon->price.'</button>';
			echo '</a>';
			echo '</li>';
		}
		?></ul>
	<?php else : ?>
		<p><?php printf( __( 'Our catalog of WP Product Review Extensions can be found on ThemeIsle.com here: <a href="%s">WP Product Review Extensions</a>', 'cwppos' ), 'https://themeisle.com/plugins/' ); ?></p>
	<?php endif; ?>
</div>
<?php } ?>