<?php
/**
 * WPPR Template 2.
 *
 * @package     WPPR
 * @subpackage  Layouts
 * @copyright   Copyright (c) 2017, Bogdan Popa
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       3.0.0
 * @global      WPPR_Review_Model $review_object
 */
?>
<div class="wppr-template wppr-template-2 <?php echo is_rtl() ? 'rtl' : ''; ?>">
	<?php
	$review_id         = $review_object->get_ID();
	$review_pros       = $review_object->get_pros();
	$review_cons       = $review_object->get_cons();
	$review_image      = $review_object->get_small_thumbnail();
	?>

	<div id="wppr-review-<?php echo $review_id; ?>" class="wppr-review-container">
		<h2 class="wppr-review-name"><?php echo esc_html( $review_object->get_name() ); ?></h2>
		<div class="wppr-review-head<?php echo ( $review_pros && $review_cons ) ? ' wppr-review-with-pros-cons' : ''; ?><?php echo ( $review_image ) ? ' wppr-review-with-image' : ''; ?>">
			<div class="wppr-review-rating <?php echo is_rtl() ? 'rtl' : ''; ?>">
				<?php wppr_layout_get_rating( $review_object, 'number', 'style2' ); ?>
				<?php wppr_layout_get_image( $review_object, 'wppr-review-product-image wppr-default-img', 'wppr-product-image' ); ?>

				<div class="clearfix"></div>

				<?php wppr_layout_get_user_rating( $review_object ); ?>

			</div>

			<?php wppr_layout_get_pros( $review_object, 'wppr-review-pros', 'h3', 'wppr-review-pros-name' ); ?>
			<?php wppr_layout_get_cons( $review_object, 'wppr-review-pros', 'h3', 'wppr-review-cons-name' ); ?>

		</div><!-- end .wppr-review-head -->

		<?php wppr_layout_get_options_ratings( $review_object, 'stars' ); ?>

		<div class="clearfix"></div>
	</div><!-- end .wppr-review-container -->

	<?php wppr_layout_get_affiliate_buttons( $review_object ); ?>

</div>
