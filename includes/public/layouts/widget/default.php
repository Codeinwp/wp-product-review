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

echo '<ul>';
foreach ( $results as $review ) :
	$product_title_display = $review['cwp_rev_product_name'];
	$product_image = $review['cwp_rev_product_image'];

	if ( strlen( $product_title_display ) > self::RESTRICT_TITLE_CHARS ) {
		$product_title_display = substr( $product_title_display, 0, self::RESTRICT_TITLE_CHARS ) . '...';
	}
	?>
	<li class="cwp-popular-review cwp_top_posts_widget_
	<?php
	the_ID();
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
			<img class="cwp_rev_image wppr-col" src="<?php echo $product_image; ?>" alt="<?php echo $review['cwp_rev_product_name']; ?>">
		<?php } ?>
		<a href="<?php echo get_the_permalink( $review['ID'] ); ?>" class="wppr-col" title="<?php echo $review['cwp_rev_product_name']; ?>">
			<?php echo $product_title_display; ?>
		</a>
		<?php
		$review_score = $review['wppr_rating'];
		if ( ! empty( $review_score ) ) {
			?>
			<div class="review-grade-widget wppr-col">
				<div class="cwp-review-chart absolute">
					<div class="cwp-review-percentage" data-percent="<?php echo $review_score; ?>"><span></span></div>
				</div><!-- end .chart -->
			</div>
		<?php } ?>
	</li> 
	<?php
	endforeach;
?>
</ul>
