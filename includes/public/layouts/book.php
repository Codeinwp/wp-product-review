<?php
/**
 * WPPR Template 2.
 *
 * @package     WPPR
 * @subpackage  Layouts
 * @copyright   Copyright (c) 2017, Bogdan Popa
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       3.0.0
 */
?>
<div class="wppr-template wppr-template-2">
	<?php
	// Review info.
	$review_id = $review_object->get_ID();
	$review_pros = $review_object->get_pros();
	$review_cons = $review_object->get_cons();
	$review_rating = $review_object->get_rating();
	?>

	<div id="wppr-review-<?php echo $review_id; ?>" class="wppr-review-container">
		<div class="wppr-review-head<?php echo ( $review_pros && $review_cons ) ? ' wppr-review-with-pros-cons' : ''; ?>">
			<div class="wppr-review-rating">
				<span style="background: <?php echo esc_attr( $review_object->wppr_get_option( 'cwppos_rating_weak' ) ); ?>;">
					<?php
					// Display rating number.
					echo esc_html( round( $review_object->get_rating(), 0 ) / 10 );
					?>
				</span>
			</div>
			<?php
			// Pros & Cons section.
			if ( ! empty( $review_pros ) || ! empty( $review_cons ) ) {
				// Pros.
				if ( ! empty( $review_pros ) ) {
					?>
					<div class="wppr-review-pros">
						<h3 style="color: <?php echo esc_attr( $review_object->wppr_get_option( 'cwppos_rating_weak' ) ); ?>;">
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
					<div class="wppr-review-cons">
						<h3 style="color: <?php echo esc_attr( $review_object->wppr_get_option( 'cwppos_rating_weak' ) ); ?>;">
							<?php echo esc_html( apply_filters( 'wppr_review_pros_text', $review_object->wppr_get_option( 'cwppos_cons_text' ) ) ); ?>
						</h3>
						<ul>
							<?php foreach ( $review_cons as $con ) { ?>
								<li><?php echo esc_html( $con ); ?></li>
							<?php } ?>
						</ul>
					</div>
			<?php
				}
			}
			?>
		</div><!-- end .wppr-review-head -->
		<div class="wppr-review-options">
			<?php
			foreach ( $review_object->get_options() as $option ) {
				$review_option_rating = $option['value'];
				?>
				<div class="wppr-review-option">
					<div class="wppr-review-option-header">
						<span><?php echo esc_html( apply_filters( 'wppr_option_name_html', $option['name'] ) ); ?></span>
						<span><?php echo esc_html( number_format( ( $review_option_rating / 10 ), 1 ) ); ?></span>
					</div>
					<ul class="wppr-review-option-rating">
						<?php for ( $i = 1; $i <= 10; $i++ ) { ?>
							<li
								<?php if ( $i <= round( $option['value'] / 10 ) ) { ?>
									style="color: <?php echo esc_attr( $review_object->wppr_get_option( 'cwppos_rating_chart_default' ) ); ?>;">
								<?php } else { ?>
									style="color: <?php echo esc_attr( $review_object->wppr_get_option( 'cwppos_rating_default' ) ); ?>;">
								<?php } ?>
							</li>
						<?php } ?>
					</ul>
				</div><!-- end .wppr-review-option -->
			<?php } ?>
		</div><!-- end .wppr-review-options -->

	</div><!-- end .wppr-review-container -->
</div>
