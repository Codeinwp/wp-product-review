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
	$review_object         = new WPPR_Review_Model( $review['ID'] );
	$product_title_display = ( $instance['post_type'] == true ) ? $review_object->get_name() : get_the_title( $review['ID'] );
	$product_image         = $review_object->get_small_thumbnail();

	if ( strlen( $product_title_display ) > $title_length ) {
		$product_title_display = substr( $product_title_display, 0, $title_length ) . '...';
	}
	?>
	<li class="cwp-popular-review">

	<?php
	$wppr_image = false;
	if ( $instance['show_image'] == true && ! empty( $product_image ) ) {
		?>
		<div class="cwp_rev_image wppr-col">
			<img src="<?php echo $product_image; ?>"
			 alt="<?php echo $review_object->get_name(); ?>">
		</div>
		<?php
		$wppr_image = true;
	}
	?>

	<div class="wppr-post-title wppr-col<?php echo ( $wppr_image ) ? '' : ' wppr-no-image'; ?>">
		<a href="<?php echo get_the_permalink( $review['ID'] ); ?>" class="wppr-col" title="<?php echo $review_object->get_name(); ?>">
			<?php echo $product_title_display; ?>
		</a>
	</div>

	<?php
	$review_score = $review_object->get_rating();
	$rating = round( $review_score );
	$rating_10  = round( $review_score, 0 ) / 10;

	$review_class = $review_object->get_rating_class();
	if ( ! empty( $review_score ) ) {
		?>
		<?php wppr_layout_get_rating( $review_object, 'donut', 'style1-widget', 'review-grade-widget wppr-col' ); ?>

	<?php } ?>
	</li>
		<?php
	endforeach;
?>
</ul>
