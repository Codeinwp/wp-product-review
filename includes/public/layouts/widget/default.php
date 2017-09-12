<?php
/**
 * Default Widget Layout for front end.
 *
 * @package     WPPR
 * @subpackage  Layouts
 * @copyright   Copyright (c) 2017, Bogdan Preda
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       3.0.0
 */

?>
<ul>
<?php
foreach ( $results as $review ) :
	$review_object = new WPPR_Review_Model( $review['ID'] );
	$product_title_display = ( $instance['post_type'] == true ) ? $review_object->get_name() : get_the_title( $review['ID'] );
	$product_image = $review_object->get_small_thumbnail();

	if ( strlen( $product_title_display ) > $title_length ) {
		$product_title_display = substr( $product_title_display, 0, $title_length ) . '...';
	}
	?>
	<li class="cwp-popular-review cwp_top_posts_widget_
	<?php
	if ( $instance['show_image'] == true && ! empty( $product_image ) ) {
		echo ' wppr-cols-3';
	} else {
		echo ' wppr-cols-2';
	}
	?>
	">
	<?php
	if ( $instance['show_image'] == true && ! empty( $product_image ) ) {
		?>
		<img class="cwp_rev_image wppr-col" src="<?php echo $product_image; ?>"
			 alt="<?php echo $review_object->get_name(); ?>">
			<?php } ?>
			<a href="<?php echo get_the_permalink( $review['ID'] ); ?>" class="wppr-col"
		   title="<?php echo $review_object->get_name(); ?>">
			<?php echo $product_title_display; ?>
			</a>
			<?php
			$review_score = $review_object->get_rating();
			if ( ! empty( $review_score ) ) {
				?>
				<div class="review-grade-widget wppr-col">
					<div class="cwp-review-chart absolute">
						<div class="cwp-review-percentage" data-percent="<?php echo $review_score; ?>"><span></span>
						</div>
					</div><!-- end .chart -->
				</div>
			<?php } ?>
		</li>
		<?php
	endforeach;
	?>
</ul>
