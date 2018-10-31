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
		$stars          = array( 'full' => intval( $rating_5 ), 'half' => $rating_5 > intval( $rating_5 ), 'empty' => ( $rating_5 > intval( $rating_5 ) ? 4 : 5 ) - intval( $rating_5 ) );
		foreach ( $stars as $key => $value ) {
			switch ( $key ) {
				case 'full':
					for ( $i = 0; $i < $value; $i++ ) {
						?>
			<i class="fa fa-star"></i>
						<?php
					}
					break;
				case 'half':
					if ( $value ) {
						?>
			<i class="fa fa-star-half-o"></i>
						<?php
					}
					break;
				case 'empty':
					for ( $i = 0; $i < $value; $i++ ) {
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
		$review_rating = $review_object->get_rating();
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


if ( ! function_exists( 'wppr_default_get_image' ) ) {

	/**
	 * Display the imaage for the default template.
	 */
	function wppr_default_get_image( $review_object ) {
		$links                     = $review_object->get_links();
		$links                     = array_filter( $links );
		$image_link                = reset( $links );
		$lightbox                   = '';
		if ( $review_object->get_click() == 'image' ) {
			$lightbox   = 'data-lightbox="' . esc_url( $review_object->get_small_thumbnail() ) . '"';
			$image_link = $review_object->get_image();
		}
		?>
		<div class="rev-wu-image">
			<a class="wppr-default-img" href="<?php echo esc_url( $image_link ); ?>" <?php echo $lightbox; ?> rel="nofollow" target="_blank">
				<img
					src="<?php echo esc_attr( $review_object->get_small_thumbnail() ); ?>"
					alt="<?php echo esc_attr( $review_object->get_name() ); ?>"
					class="photo photo-wrapup wppr-product-image"/>
			</a>
		</div><!-- end .rev-wu-image -->
		<?php
	}
}

if ( ! function_exists( 'wppr_default_get_rating' ) ) {

	/**
	 * Display the rating for the default template.
	 */
	function wppr_default_get_rating( $review_object ) {
		$rating     = round( $review_object->get_rating() );
		$rating_10  = round( $review_object->get_rating(), 0 ) / 10;
		?>
		<div class="review-wu-grade">
			<div class="review-wu-grade-content">
				<div class="wppr-c100 wppr-p<?php echo esc_attr( $rating ) . ' ' . $review_object->get_rating_class(); ?>">
					<span><?php echo esc_html( $rating_10 ); ?></span>
					<div class="wppr-slice">
						<div class="wppr-bar" style="<?php echo apply_filters( 'wppr_rating_circle_bar_styles', '', $rating ); ?>"></div>
						<div class="wppr-fill" style="<?php echo apply_filters( 'wppr_rating_circle_fill_styles', '', $rating ); ?>"></div>
					</div>
					<div class="wppr-slice-center"></div>
				</div>
			</div>
		</div><!-- end .review-wu-grade -->
		<?php
	}
}

if ( function_exists( 'register_block_type' ) ) {
	WPPR_Gutenberg::get_instance();
}
