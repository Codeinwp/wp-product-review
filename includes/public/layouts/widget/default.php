<?php
echo '<ul>';
$post_type = true;
$show_image = true;
while ( $cwp_products_loop->have_posts() ) : $cwp_products_loop->the_post();
	$post_id = get_the_ID();
	$review_model = new WPPR_Review_Model( $post_id );
	$review = $review_model->get_review_data();
	$product_image = $review['image']['thumb'];
	$product_title = ( $post_type == true ) ? $review['name']  :  get_the_title();
	$product_title_display  = $product_title;
	if ( strlen( $product_title_display ) > self::RESTRICT_TITLE_CHARS ) {
		$product_title_display  = substr( $product_title_display, 0, self::RESTRICT_TITLE_CHARS ) . '...';
	}
	?>
	<li class="cwp-popular-review cwp_top_posts_widget_<?php the_ID();
	if ( $show_image == true && ! empty( $product_image ) ) { echo ' wppr-cols-3';
	} else { echo ' wppr-cols-2'; } ?>">
		<?php
		if ( $show_image == true && ! empty( $product_image ) ) {
			?>
			<img class="cwp_rev_image wppr-col" src="<?php echo $product_image;?>" alt="<?php echo $product_title; ?>">
		<?php } ?>
		<a href="<?php the_permalink(); ?>" class="wppr-col" title="<?php echo $product_title; ?>">
			<?php echo $product_title_display; ?>
		</a>
		<?php
		$review_score = $review['rating'];
		if ( ! empty( $review_score ) ) { ?>
			<div class="review-grade-widget wppr-col">
				<div class="cwp-review-chart absolute">
					<div class="cwp-review-percentage" data-percent="<?php echo $review_score; ?>"><span></span></div>
				</div><!-- end .chart -->
			</div>
		<?php } ?>
	</li><!-- end .popular-review -->
<?php endwhile; ?>
<?php wp_reset_postdata(); // reset the query
echo '</ul>';
