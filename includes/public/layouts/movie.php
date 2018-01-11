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
<div class="wppr-template wppr-template-1">

	<?php
	// Review info.
	$review_id = $review_object->get_ID();
	$review_pros = $review_object->get_pros();
	$review_cons = $review_object->get_cons();
	$review_rating = $review_object->get_rating();
	?>

	<div id="wppr-review-<?php echo $review_id; ?>" class="wppr-review-container">
		<div class="wppr-review-stars">
			<div class="wppr-review-stars-grade" style="color: <?php echo esc_attr( $review_object->wppr_get_option( 'cwppos_rating_weak' ) ); ?>;">
				<span class="wppr-review-full-stars" style="color: <?php echo esc_attr( $review_object->wppr_get_option( 'cwppos_rating_weak' ) ); ?>; width:<?php echo esc_html( intval( $review_rating ) ); ?>%;"></span>
			</div>
			<span class="wppr-review-stars-author"><?php echo get_the_author() . __( '\'s rating','wp-product-review' ); ?></span>
		</div><!-- end .wppr-review-stars -->
		<div class="wppr-review-grade">
			<div class="wppr-review-grade-number">
				<span>
				<?php
					// Display rating number.
					echo esc_html( round( $review_object->get_rating(), 0 ) / 10 );
					?>
					</span>
			</div>
			<div class="wppr-review-grade-options">
				<?php
				foreach ( $review_object->get_options() as $option ) {
					$review_option_rating = $option['value'];
					?>
				<div class="wppr-review-grade-option">
					<div class="wppr-review-grade-option-header">
						<span><?php echo esc_html( apply_filters( 'wppr_option_name_html', $option['name'] ) ); ?></span>
						<span><?php echo esc_html( number_format( ( $review_option_rating / 10 ), 1 ) ); ?></span>
					</div>
					<div class="wppr-review-grade-option-rating" style="background: <?php echo esc_attr( $review_object->wppr_get_option( 'cwppos_rating_default' ) ); ?>;">
						<span style="background: <?php echo esc_attr( $review_object->wppr_get_option( 'cwppos_rating_chart_default' ) ); ?>;width:<?php echo esc_attr( $review_option_rating ); ?>%;"></span>
					</div>
				</div><!-- end .wppr-review-grade-option -->
				<?php } ?>
			</div><!-- end .wppr-review-grade-options -->
		</div><!-- end .wppr-review-grade -->
		<?php
		// Pros & Cons section.
		if ( ! empty( $review_pros ) || ! empty( $review_cons ) ) {
		?>
		<div class="wppr-review-pros-cons<?php echo ( $review_pros && $review_cons ) ? '' : ' wppr-review-one-column'; ?>">
			<?php
			// Pros.
			if ( ! empty( $review_pros ) ) {
			?>
			<div class="pros">
				<h3>
					<?php echo esc_html( apply_filters( 'wppr_review_pros_text', $review_object->wppr_get_option( 'cwppos_pros_text' ) ) ); ?>
				</h3>
				<ul>
					<?php foreach ( $review_pros as $pro ) { ?>
						<li><?php echo esc_html( $pro ); ?></li>
					<?php } ?>
				</ul>
			</div>
			<?php
			}
			// Cons.
			if ( ! empty( $review_cons ) ) {
			?>
			<div class="cons">
				<h3>
					<?php echo esc_html( apply_filters( 'wppr_review_pros_text', $review_object->wppr_get_option( 'cwppos_cons_text' ) ) ); ?>
				</h3>
				<ul>
					<?php foreach ( $review_cons as $con ) { ?>
						<li><?php echo esc_html( $con ); ?></li>
					<?php } ?>
				</ul>
			</div>
			<?php } ?>
		</div><!-- end .wppr-review-pros-cons -->
		<?php } ?>
	</div><!-- end .wppr-review-container -->
</div>
