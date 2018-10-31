<?php
/**
 * Style One Widget Layout for front end.
 *
 * @package     WPPR
 * @subpackage  Layouts
 * @copyright   Copyright (c) 2017, Bogdan Preda
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       3.0.0
 */

// @codingStandardsIgnoreStart
?>
<div class="wppr-prodlist">
	<?php
	foreach ( $results as $review ) :

		$review_object = new WPPR_Review_Model( $review['ID'] );
		$product_image = $review_object->get_small_thumbnail();
		$product_title = ( $instance['post_type'] == true ) ? $review_object->get_name() : get_the_title( $review['ID'] );
		$product_title_display = $product_title;
		if ( strlen( $product_title_display ) > $title_length ) {
			$product_title_display = substr( $product_title_display, 0, $title_length ) . '...';
		}
		$links          = $review_object->get_links();
		$affiliate_link = reset( $links );
		$review_link    = get_the_permalink( $review['ID'] );

		$showingImg = $instance['show_image'] == true && ! empty( $product_image );
		?>

		<div class="wppr-prodrow">
			<?php if ( $showingImg ) { ?>
				<div class="wppr-prodrowleft">
					<a href="<?php echo $review_link; ?>" class="wppr-col" title="<?php echo $product_title; ?>">
						<img class="cwp_rev_image wppr-col" src="<?php echo $product_image; ?>"
						     alt="<?php echo $product_title; ?>"/>
					</a>
				</div>
				<?php
			}
			?>
			<div class="wppr-prodrowright <?php echo $showingImg ? 'wppr-prodrowrightadjust' : '' ?>">
				<p><strong><?php echo $product_title_display; ?></strong></p>
				<?php
				$review_score = $review_object->get_rating();

				if ( ! empty( $review_score ) ) {
					if ( $instance['cwp_tp_rating_type'] == 'round' ) {
						?>
						<div class="review-grade-widget wppr-col">
							<div class="cwp-review-chart relative">
								<div class="cwp-review-percentage" data-percent="<?php echo $review_score; ?>">
									<span></span></div>
							</div><!-- end .chart -->
						</div>
						<div class="clear"></div>
						<?php
					} else {
						wppr_display_rating_stars( 'style1-widget', $review_object, false );
					}
				}
				?>
				<p class="wppr-style1-buttons">
					<?php
					$link = "<a href='{$affiliate_link}' rel='nofollow' target='_blank' class='wppr-bttn'>" . __( $instance['cwp_tp_buynow'], 'wp-product-review' ) . '</a>';
					if ( ! empty( $instance['cwp_tp_buynow'] ) ) {
						echo apply_filters( 'wppr_widget_style1_buynow_link', $link, $review['ID'], $affiliate_link, $instance['cwp_tp_buynow'] );
					}

					$link = "<a href='{$review_link}' rel='nofollow' target='_blank' class='wppr-bttn'>" . __( $instance['cwp_tp_readreview'], 'wp-product-review' ) . '</a>';
					if ( ! empty( $instance['cwp_tp_readreview'] ) ) {
						echo apply_filters( 'wppr_widget_style1_readreview_link', $link, $review['ID'], $review_link, $instance['cwp_tp_readreview'] );
					}
					?>
				</p>
			</div>
			<div class="clear"></div>
		</div>
	<?php endforeach; ?>
</div>
