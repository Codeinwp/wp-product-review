<?php
/**
 * WPPR Template 1.
 *
 * @package     WPPR
 * @subpackage  Layouts
 * @copyright   Copyright (c) 2017, Bogdan Popa
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       3.0.0
 */
?>
<div class="wppr-template wppr-template-1 <?php echo is_rtl() ? 'rtl' : ''; ?>">

	<?php
	$review_id     = $review_object->get_ID();
	$review_pros   = $review_object->get_pros();
	$review_cons   = $review_object->get_cons();
	?>

	<div id="wppr-review-<?php echo $review_id; ?>" class="wppr-review-container">

		<h2 class="wppr-review-name"><?php echo esc_html( $review_object->get_name() ); ?></h2>

	<?php wppr_layout_get_rating( $review_object, 'stars', 'style1', false ); ?>

		<div class="wppr-review-grade">
			<div class="wppr-review-grade-number">
				<?php wppr_layout_get_rating( $review_object, 'number', 'style1' ); ?>
			</div>
			<div class="wppr-review-product-image">
				<?php wppr_layout_get_image( $review_object, 'wppr-default-img', 'wppr-product-image' ); ?>
			</div>

			<?php wppr_layout_get_options_ratings( $review_object, 'bars' ); ?>

		</div><!-- end .wppr-review-grade -->

		<div class="wppr-review-pros-cons<?php echo ( $review_pros && $review_cons ) ? '' : ' wppr-review-one-column'; ?>">
			<?php wppr_layout_get_pros( $review_object, '', 'h3', 'wppr-review-pros-name' ); ?>
			<?php wppr_layout_get_cons( $review_object, '', 'h3', 'wppr-review-cons-name' ); ?>
		</div><!-- end .wppr-review-pros-cons -->

	</div><!-- end .wppr-review-container -->
	<?php wppr_layout_get_affiliate_buttons( $review_object ); ?>

</div>
