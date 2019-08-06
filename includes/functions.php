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
			<i class="dashicons dashicons-star-filled wppr-dashicons"></i>
						<?php
					}
					break;
				case 'half':
					if ( $value ) {
						?>
			<i class="dashicons dashicons-star-half wppr-dashicons"></i>
						<?php
					}
					break;
				case 'empty':
					for ( $i = 0; $i < $value; $i++ ) {
						?>
			<i class="dashicons dashicons-star-empty wppr-dashicons"></i>
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

if ( ! function_exists( 'wppr_default_get_image' ) ) {

	/**
	 * Display the imaage for the default template.
	 */
	function wppr_default_get_image( $review_object ) {
		$links                     = $review_object->get_links();
		$links                     = array_filter( $links );
		$image_link                = reset( $links );
		$lightbox                   = '';
		if ( $review_object->get_click() === 'image' ) {
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
	 * Display the rating of the given type.
	 */
	function wppr_layout_get_rating( $review_object, $type, $template, $div_classes = '', $include_author = false ) {
		$review_rating = $review_object->get_rating();
		$rating     = round( $review_rating );

		$scale      = $review_object->wppr_get_option( 'wppr_use_5_rating_scale' );
		if ( empty( $scale ) ) {
			$scale  = 10;
		}
		// for scale of 10 divide by 10 and for scale of 5 divide by 20.
		$scale      = 10 * ( 10 / $scale );
		$rating_10  = round( $review_rating, 0 ) / $scale;

		$div_class1 = $div_class2 = '';
		if ( is_array( $div_classes ) ) {
			$div_class1 = array_shift( $div_classes );
			if ( is_array( $div_classes ) ) {
				$div_class2 = array_shift( $div_classes );
			}
		} else {
			$div_class1 = $div_classes;
		}

		switch ( $type ) {
			case 'donut':
				$class_bar = $class_fill = '';
				$style_bar = apply_filters( 'wppr_rating_circle_bar_styles', '', $rating );
				$style_fill = apply_filters( 'wppr_rating_circle_fill_styles', '', $rating );
				if ( function_exists( 'is_amp_endpoint' ) && is_amp_endpoint() ) {
					$class_bar = md5( $style_bar ) . '-donut';
					$class_fill = md5( $style_fill ) . '-donut';
					$style_bar = $style_fill = '';
					add_filter(
						'wppr_global_style', function( $css, $review ) {
						$review_rating = $review->get_rating();
						$rating     = round( $review_rating );
						$style_bar = apply_filters( 'wppr_rating_circle_bar_styles', '', $rating );
						$style_fill = apply_filters( 'wppr_rating_circle_fill_styles', '', $rating );
						$class_bar = md5( $style_bar ) . '-donut';
						$class_fill = md5( $style_fill ) . '-donut';
						$css = $css . ".$class_bar { $style_bar } .$class_fill { $style_fill }";
						return $css;
						}, 10, 2
					);
				}
		?>
		<div class="<?php echo $div_class1; ?>">
			<div class="review-wu-grade-content <?php echo $div_class2; ?>">
				<div class="wppr-c100 wppr-p<?php echo esc_attr( $rating ) . ' ' . esc_attr( $review_object->get_rating_class() ); ?>">
					<span><?php echo esc_html( $rating_10 ); ?></span>
					<div class="wppr-slice">
						<div class="wppr-bar <?php echo $class_bar; ?>" style="<?php echo $style_bar; ?>"></div>
						<div class="wppr-fill <?php echo $class_fill; ?>" style="<?php echo $style_fill; ?>"></div>
					</div>
					<div class="wppr-slice-center"></div>
				</div>
			</div>
		</div>
		<?php
				break;

			case 'number':
		?>
				<span class="wppr-review-rating-grade wppr-p<?php echo esc_attr( $rating ) . ' ' . $review_object->get_rating_class(); ?>">
					<?php
					echo esc_html( $rating_10 );
					?>
				</span>
		<?php
				break;

			case 'stars':
				wppr_display_rating_stars( $template, $review_object, $include_author );
				break;
		}
	}
}


if ( ! function_exists( 'wppr_layout_get_image' ) ) {
	/**
	 * Display the image for the review.
	 */
	function wppr_layout_get_image( $review_object, $class_a = '', $class_img = '' ) {
		$src                = $review_object->get_small_thumbnail();
		if ( empty( $src ) ) {
			return;
		}

		$links              = $review_object->get_links();
		$links              = array_filter( $links );
		$image_link         = reset( $links );
		$lightbox           = '';
		if ( $review_object->get_click() === 'image' ) {
			$lightbox       = 'data-lightbox="' . esc_url( $src ) . '"';
			$image_link     = $review_object->get_image();
		}
		?>
		<a title="<?php echo $review_object->get_name(); ?>" class="<?php echo $class_a; ?>" href="<?php echo esc_url( $image_link ); ?>" <?php echo $lightbox; ?> rel="nofollow" target="_blank">
			<img
				src="<?php echo esc_attr( $src ); ?>"
				alt="<?php echo esc_attr( $review_object->get_image_alt() ); ?>"
				class="<?php echo $class_img; ?>"/>
		</a>
		<?php
	}
}

if ( ! function_exists( 'wppr_layout_get_pros' ) ) {
	/**
	 * Display the pros for the review.
	 */
	function wppr_layout_get_pros( $review_object, $class_div = '', $heading_type, $class_heading = '' ) {
		$pros = $review_object->get_pros();
		wppr_layout_get_proscons( $review_object, $pros, 'pro', $class_div, $heading_type, $class_heading );
	}
}

if ( ! function_exists( 'wppr_layout_get_cons' ) ) {
	/**
	 * Display the cons for the review.
	 */
	function wppr_layout_get_cons( $review_object, $class_div = '', $heading_type, $class_heading = '' ) {
		$cons = $review_object->get_cons();
		wppr_layout_get_proscons( $review_object, $cons, 'con', $class_div, $heading_type, $class_heading );
	}
}

if ( ! function_exists( 'wppr_layout_get_proscons' ) ) {
	/**
	 * Display the pros/cons for the review.
	 */
	function wppr_layout_get_proscons( $review_object, $pro_cons, $type, $class_div, $heading_type, $class_heading = '' ) {
		if ( empty( $pro_cons ) ) {
			return;
		}
	?>
		<div class="<?php echo $class_div; ?> <?php echo $type; ?>s">
			<<?php echo $heading_type; ?> class="<?php echo $class_heading; ?>">
				<?php echo esc_html( apply_filters( "wppr_review_{$type}s_text", $review_object->wppr_get_option( "cwppos_{$type}s_text" ) ) ); ?>
			</<?php echo $heading_type; ?>>
			<ul>
				<?php
				foreach ( $pro_cons as $text ) {
				?>
					<li><?php echo esc_html( $text ); ?></li>
				<?php
				}
				?>
			</ul>
		</div>
	<?php
	}
}

if ( ! function_exists( 'wppr_layout_get_user_rating' ) ) {
	/**
	 * Display the user rating/score/votes.
	 */
	function wppr_layout_get_user_rating( $review_object ) {
		if ( $review_object->wppr_get_option( 'cwppos_show_userreview' ) !== 'yes' ) {
			return;
		}
		$comments_rating = $review_object->get_comments_rating();
		$number_comments = count( $review_object->get_comments_options() );
		?>
		<span class="wppr-review-rating-users wppr-p<?php echo esc_attr( round( $comments_rating ) ) . ' ' . $review_object->get_rating_class( $comments_rating ); ?>">
			<span dir="<?php echo is_rtl() ? 'rtl' : ''; ?>">
				<?php echo sprintf( __( 'Users score: %1$d with %2$d votes', 'wp-product-review' ), $comments_rating, $number_comments ); ?>
			</span>
		</span>
	<?php
	}
}

if ( ! function_exists( 'wppr_layout_get_options_ratings' ) ) {
	/**
	 * Display the option ratings.
	 */
	function wppr_layout_get_options_ratings( $review_object, $type ) {
		$scale      = $review_object->wppr_get_option( 'wppr_use_5_rating_scale' );
		if ( empty( $scale ) ) {
			$scale  = 10;
		}
		$display    = round( $scale );

		// for scale of 10 divide by 10 and for scale of 5 divide by 20.
		$scale      = 10 * ( 10 / $scale );

		switch ( $type ) {
			case 'dashes':
	?>
		<div class="review-wu-bars">
			<?php
			foreach ( $review_object->get_options() as $option ) {
					$class_ul   = $review_object->get_rating_class( $option['value'] ) . apply_filters( 'wppr_option_custom_icon', '' );
					?>
			<div class="rev-option" data-value="<?php echo $option['value']; ?>">
			<div class="cwpr_clearfix">
				<span>
					<h3><?php echo esc_html( apply_filters( 'wppr_option_name_html', $option['name'] ) ); ?></h3>
				</span>
				<span><?php echo esc_html( number_format( ( $option['value'] / $scale ), 1 ) ); ?>/<?php echo $display; ?></span>
			</div>
			<ul class="cwpr_clearfix <?php echo $class_ul; ?>">
					<?php
					$rating     = round( $option['value'] / $scale );
					$start_from = is_rtl() ? ( $display + 1 - $rating ) : 1;
					$stop_at    = is_rtl() ? $display : $rating;
					for ( $i = 1; $i <= $display; $i ++ ) {
						?>
					<li <?php echo $i >= $start_from && $i <= $stop_at ? ' class="colored"' : ''; ?>></li>
					<?php
						}
					?>
					</ul>
				</div>
					<?php
			}
			?>
		</div>
	<?php
				break;
			case 'bars':
	?>
				<div class="wppr-review-grade-options <?php echo is_rtl() ? 'rtl' : ''; ?>">
	<?php
	foreach ( $review_object->get_options() as $option ) {
					$review_option_rating = $option['value'];
							?>
<div class="wppr-review-grade-option">
<div class="wppr-review-grade-option-header">
	<span><?php echo esc_html( apply_filters( 'wppr_option_name_html', $option['name'] ) ); ?></span>
	<span><?php echo esc_html( number_format( ( $review_option_rating / $scale ), 1 ) ); ?></span>
</div>
<div class="wppr-review-grade-option-rating wppr-default <?php echo $review_object->get_rating_class( $review_option_rating ); ?> <?php echo is_rtl() ? 'rtl' : ''; ?>">
	<span class="<?php echo $review_object->get_rating_class( $review_option_rating ); ?>" style="
							<?php
							/**
							 * Adds min-width for amp support.
							 */
							 echo 'width:' . esc_attr( is_rtl() ? ( 100 - $review_option_rating ) : $review_option_rating ) . '%; ';
							 echo esc_attr( apply_filters( 'wppr_review_option_rating_css', '', $review_option_rating ) );
							?>
	"></span>
</div>
</div><!-- end .wppr-review-grade-option -->
							<?php
	}
	?>
			</div><!-- end .wppr-review-grade-options -->
	<?php
				break;
			case 'stars':
	?>
			<div class="wppr-review-options">
	<?php
	foreach ( $review_object->get_options() as $option ) {
					$review_option_rating = $option['value'];
							?>
<div class="wppr-review-option">
<div class="wppr-review-option-header">
	<span><?php echo esc_html( apply_filters( 'wppr_option_name_html', $option['name'] ) ); ?></span>
</div>
<ul class="wppr-review-option-rating <?php echo apply_filters( 'wppr_option_custom_icon', '' ); ?>">
							<?php
								$rating     = round( $option['value'] / $scale );
								$start_from = is_rtl() ? ( $display + 1 - $rating ) : 1;
								$stop_at    = is_rtl() ? $display : $rating;
							for ( $i = 1; $i <= $display; $i ++ ) {
				?>
<li class="<?php echo $i >= $start_from && $i <= $stop_at ? $review_object->get_rating_class( $option['value'] ) : ' wppr-default'; ?>"></li>
	<?php
										}
							?>
</ul>
</div><!-- end .wppr-review-option -->
							<?php
	}
				?>
			</div><!-- end .wppr-review-options -->
	<?php
				break;
		}
	}
}

if ( ! function_exists( 'wppr_layout_get_affiliate_buttons' ) ) {
	/**
	 * Display the affiliate buttons.
	 */
	function wppr_layout_get_affiliate_buttons( $review_object ) {
		$links  = $review_object->get_links();
		$links  = array_filter( $links );

		$multiple_affiliates_class = 'affiliate-button';

		if ( count( $links ) > 1 ) {
			$multiple_affiliates_class = 'affiliate-button2 affiliate-button';
		}

		foreach ( $links as $title => $link ) {
			if ( empty( $title ) || empty( $link ) ) {
				continue;
			}
			?>
				<div class="<?php echo esc_attr( $multiple_affiliates_class ); ?>">
					<a href="<?php echo esc_url( $link ); ?>" rel="nofollow" target="_blank">
						<span><?php echo esc_html( $title ); ?></span>
					</a>
				</div><!-- end .affiliate-button -->
			<?php
		}
	}
}

if ( ! function_exists( 'wppr_schema_add_director_to_movie' ) ) {
	add_filter( 'wppr_schema_data_types_allowed_for_Movie', 'wppr_schema_add_director_to_movie', 10, 1 );

	/**
	 * Movie has to have the Director field.
	 */
	function wppr_schema_add_director_to_movie( $types ) {
		$types[] = 'schema:Person';
		return $types;
	}
}

if ( ! function_exists( 'wppr_schema_remove_fields_from_movie' ) ) {
	add_filter( 'wppr_schema_fields_for_Movie', 'wppr_schema_remove_fields_from_movie', 10, 2 );

	/**
	 * The property materialExtent is not recognised by Google for an object of type Movie.
	 */
	function wppr_schema_remove_fields_from_movie( $fields, $subtype ) {
		unset( $fields['materialExtent'] );
		return $fields;
	}
}

if ( ! function_exists( 'wppr_schema_data_types_allowed_brand' ) ) {
	add_filter( 'wppr_schema_data_types_allowed_for_Product', 'wppr_schema_data_types_allowed_brand', 10, 1 );
	add_filter( 'wppr_schema_data_types_allowed_for_IndividualProduct', 'wppr_schema_data_types_allowed_brand', 10, 1 );
	add_filter( 'wppr_schema_data_types_allowed_for_ProductModel', 'wppr_schema_data_types_allowed_brand', 10, 1 );
	add_filter( 'wppr_schema_data_types_allowed_for_SomeProducts', 'wppr_schema_data_types_allowed_brand', 10, 1 );
	add_filter( 'wppr_schema_data_types_allowed_for_Vehicle', 'wppr_schema_data_types_allowed_brand', 10, 1 );

	/**
	 * IndividualProduct, Product, ProductModel, SomeProducts, Vehicle need the Brand field.
	 */
	function wppr_schema_data_types_allowed_brand( $types ) {
		$types[] = 'schema:Brand';
		return $types;
	}
}


if ( function_exists( 'register_block_type' ) ) {
	WPPR_Gutenberg::get_instance();
}
