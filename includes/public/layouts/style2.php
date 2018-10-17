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
	// Review info.
	$links             = $review_object->get_links();
	$review_id         = $review_object->get_ID();
	$review_pros       = $review_object->get_pros();
	$review_cons       = $review_object->get_cons();
	$review_rating     = $review_object->get_rating();
	$review_image      = $review_object->get_small_thumbnail();
	$review_image_link = reset( $links );

	$rating_10      = round( $review_rating, 0 ) / 10;

	$multiple_affiliates_class = 'affiliate-button';

	if ( count( $links ) > 1 ) {
		$multiple_affiliates_class = 'affiliate-button2 affiliate-button';
	}
	if ( $review_object->get_click() == 'image' ) {
		$lightbox          = 'data-lightbox="' . esc_url( $review_object->get_small_thumbnail() ) . '"';
		$review_image_link = $review_object->get_image();
	}
	?>

	<div id="wppr-review-<?php echo $review_id; ?>" class="wppr-review-container">
		<h2 class="wppr-review-name"><?php echo esc_html( $review_object->get_name() ); ?></h2>
		<div class="wppr-review-head<?php echo ( $review_pros && $review_cons ) ? ' wppr-review-with-pros-cons' : ''; ?><?php echo ( $review_image ) ? ' wppr-review-with-image' : ''; ?>">
			<div class="wppr-review-rating <?php echo is_rtl() ? 'rtl' : ''; ?>">
				<span class="wppr-review-rating-grade wppr-p<?php echo esc_attr( round( $review_rating ) ) . ' ' . $review_object->get_rating_class(); ?>">
					<?php
					// Display rating number.
					echo esc_html( $rating_10 );
					?>
				</span>
				<?php
				// Review image.
				if ( ! empty( $review_image ) ) {
					?>
					<a href="<?php echo esc_url( $review_image_link ); ?>" <?php echo $lightbox; ?>
					   class="wppr-review-product-image wppr-default-img" rel="nofollow" target="_blank"><img
								src="<?php echo esc_attr( $review_image ); ?>"
								alt="<?php echo esc_attr( $review_object->get_image_alt() ); ?>" class="wppr-product-image"/></a>
				<?php } ?>
				<div class="clearfix"></div>
				<?php
				if ( $review_object->wppr_get_option( 'cwppos_show_userreview' ) === 'yes' ) {
					$comments_rating = $review_object->get_comments_rating();
					$number_comments = count( $review_object->get_comments_options() );
					?>
					<span class="wppr-review-rating-users wppr-p<?php echo esc_attr( round( $comments_rating ) ) . ' ' . $review_object->get_rating_class( $comments_rating ); ?>">
					<span dir="<?php echo is_rtl() ? 'rtl' : ''; ?>">
						<?php echo sprintf( __( 'Users score: %1$d with %2$d votes', 'wp-product-review' ), $comments_rating, $number_comments ); ?>
					</span>
				</span>
				<?php } ?>
			</div>
			<?php
			// Pros & Cons section.
			if ( ! empty( $review_pros ) || ! empty( $review_cons ) ) {
				// Pros.
				if ( ! empty( $review_pros ) ) {
					?>
					<div class="wppr-review-pros">
						<h3 class="wppr-review-pros-name">
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
						<h3 class="wppr-review-cons-name">
							<?php echo esc_html( apply_filters( 'wppr_review_cons_text', $review_object->wppr_get_option( 'cwppos_cons_text' ) ) ); ?>
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
					</div>
					<ul class="wppr-review-option-rating <?php echo apply_filters( 'wppr_option_custom_icon', '' ); ?>">
						<?php
							$rating     = round( $option['value'] / 10 );
							$start_from = is_rtl() ? ( 11 - $rating ) : 1;
							$stop_at    = is_rtl() ? 10 : $rating;
						for ( $i = 1; $i <= 10; $i ++ ) {
							?>
						<li class="
							<?php
							echo $i >= $start_from && $i <= $stop_at ? $review_object->get_rating_class( $option['value'] ) : ' wppr-default';
							?>
						">

						</li>
						<?php } ?>
					</ul>
				</div><!-- end .wppr-review-option -->
			<?php } ?>
		</div><!-- end .wppr-review-options -->
		<div class="clearfix"></div>
	</div><!-- end .wppr-review-container -->
	<?php
	foreach ( $links as $title => $link ) {
		if ( ! empty( $title ) && ! empty( $link ) ) {
			?>
			<div class="<?php echo esc_attr( $multiple_affiliates_class ); ?>">
				<a href="<?php echo esc_url( $link ); ?>" rel="nofollow"
				   target="_blank"><span><?php echo esc_html( $title ); ?></span> </a>
			</div><!-- end .affiliate-button -->
			<?php
		}
	}
	?>
</div>
