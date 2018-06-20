<?php
/**
 * The file that contains global functions.
 *
 * @link       https://themeisle.com/
 *
 * @package    WPPR
 * @subpackage WPPR/includes
 */


if ( ! function_exists( 'wppr_display_rating' ) ) {
	/**
	 * Display the star/icon rating.
	 */
	function wppr_display_rating( $template, $review_object ) {
		$icon = apply_filters( 'wppr_option_custom_icon', '' );
		if ( empty( $icon ) ) {
			wppr_display_rating_stars( $template, $review_object, true );
		} else {
			wppr_display_rating_custom_icon( $template, $review_object );
		}
	}
}


if ( ! function_exists( 'wppr_display_rating_stars' ) ) {

	/**
	 * Display the star rating.
	 */
	function wppr_display_rating_stars( $template, $review_object, $include_author = true ) {
		$review_rating = $review_object->get_rating();
		$rating_10      = round( $review_rating, 0 ) / 10;
		$rating_5       = round( $review_rating / 20, PHP_ROUND_HALF_UP );
	?>
		<div class="wppr-review-stars <?php echo is_rtl() ? 'rtl' : ''; ?>" style="direction: <?php echo is_rtl() ? 'rtl' : ''; ?>">
			<div class="wppr-review-stars-grade <?php echo $review_object->get_rating_class(); ?>">
	<?php
		$stars          = array( 'full' => intval( $rating_5 ), 'half' => $rating_5 > intval( $rating_5 ), 'empty' => 4 - intval( $rating_5 ) );

	foreach ( $stars as $key => $value ) {
		switch ( $key ) {
			case 'full':
				for ( $i = 0; $i < intval( $rating_5 ); $i++ ) {
		?>
			<i class="fa fa-star"></i>
		<?php
				}
				break;
			case 'half':
				if ( $rating_5 > intval( $rating_5 ) ) {
		?>
			<i class="fa fa-star-half-o"></i>
		<?php
				}
				break;
			case 'empty':
				for ( $i = 0; $i < 4 - intval( $rating_5 ); $i++ ) {
		?>
			<i class="fa fa-star-o"></i>
		<?php
				}
				break;
		}
	}
		?>
		</div>
	<?php
	if ( $include_author ) {
	?>
	<span class="wppr-review-stars-author"><?php echo get_the_author() . __( '\'s rating', 'wp-product-review' ); ?></span>
	<?php
	}
	?>
	</div>
	<?php
	}
}

if ( ! function_exists( 'wppr_display_rating_custom_icon' ) ) {

	/**
	 * Display the custom icon rating.
	 */
	function wppr_display_rating_custom_icon( $template, $review_object ) {
	?>
		<div id="review-statistics">
			<div class="review-wu-bars">
				<ul class="cwpr_clearfix <?php echo ' ' . $review_object->get_rating_class( $review_rating ) . apply_filters( 'wppr_option_custom_icon', '' ); ?>">
	<?php
	for ( $i = 1; $i <= 5; $i ++ ) {
	?>
			<li <?php echo $i <= round( $review_rating / 20 ) ? ' class="colored"' : ''; ?>></li>
	<?php
	}
	?>
				</ul>
			</div>
		</div>
	<?php
	}
}

