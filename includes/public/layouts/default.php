<?php
/**
 *  WPPR front page layout.
 *
 * @package     WPPR
 * @subpackage  Layouts
 * @global      WPPR_Review_Model $review_object The inherited review object.
 * @copyright   Copyright (c) 2017, Bogdan Preda
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       3.0.0
 */

$price_raw = $review_object->get_price_raw();

$pros = $review_object->get_pros();
$cons = $review_object->get_cons();

?>
<div id="wppr-review-<?php echo $review_object->get_ID(); ?>"
	 class="wppr-template wppr-template-default <?php echo is_rtl() ? 'rtl' : ''; ?> wppr-review-container <?php echo( empty( $pros ) ? 'wppr-review-no-pros' : '' ); ?> <?php echo( empty( $cons ) ? 'wppr-review-no-cons' : '' ); ?>">
	<section id="review-statistics" class="article-section">
		<div class="review-wrap-up  cwpr_clearfix">
			<div class="cwpr-review-top cwpr_clearfix">
				<h2 class="cwp-item"><?php echo esc_html( $review_object->get_name() ); ?></h2>
				<span class="cwp-item-price cwp-item"><?php echo esc_html( empty( $price_raw ) ? '' : $price_raw ); ?></span>
			</div><!-- end .cwpr-review-top -->
			<div class="review-wu-content cwpr_clearfix">
				<div class="review-wu-left">
					<div class="review-wu-left-top">
						<div class="rev-wu-image">
							<?php wppr_layout_get_image( $review_object, 'wppr-default-img', 'photo photo-wrapup wppr-product-image' ); ?>
						</div>

						<?php wppr_layout_get_rating( $review_object, 'donut', 'default', 'review-wu-grade' ); ?>
					</div><!-- end .review-wu-left-top -->

					<?php wppr_layout_get_options_ratings( $review_object, 'dashes' ); ?>

				</div><!-- end .review-wu-left -->

				<div class="review-wu-right">
					<?php wppr_layout_get_pros( $review_object, '', 'h2', '' ); ?>
					<?php wppr_layout_get_cons( $review_object, '', 'h2', '' ); ?>
				</div><!-- end .review-wu-right -->

			</div><!-- end .review-wu-content -->
		</div><!-- end .review-wrap-up -->
	</section>

	<?php wppr_layout_get_affiliate_buttons( $review_object ); ?>
</div>
