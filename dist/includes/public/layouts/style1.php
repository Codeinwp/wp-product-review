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
	// Review info.
	$review_id     = $review_object->get_ID();
	$review_pros   = $review_object->get_pros();
	$review_cons   = $review_object->get_cons();
	$review_rating = $review_object->get_rating();
	$rating_10      = round( $review_rating, 0 ) / 10;

	$links                     = $review_object->get_links();
	$multiple_affiliates_class = 'affiliate-button';

	if ( count( $links ) > 1 ) {
		$multiple_affiliates_class = 'affiliate-button2 affiliate-button';
	}
	$review_image      = $review_object->get_small_thumbnail();
	$review_image_link = reset( $links );

	if ( $review_object->get_click() == 'image' ) {
		$lightbox          = 'data-lightbox="' . esc_url( $review_object->get_small_thumbnail() ) . '"';
		$review_image_link = $review_object->get_image();
	}
	?>

	<div id="wppr-review-<?php echo $review_id; ?>" class="wppr-review-container">

		<h2 class="wppr-review-name"><?php echo esc_html( $review_object->get_name() ); ?></h2>

	<?php
		wppr_display_rating_stars( 'style1', $review_object, false );
	?>

		<div class="wppr-review-grade">
			<div class="wppr-review-grade-number">
				<span class=" <?php echo $review_object->get_rating_class(); ?> ">
				<?php
				// Display rating number.
				echo esc_html( $rating_10 );
				?>
					</span>
			</div>
			<?php
			// Review image.
			if ( ! empty( $review_image ) ) {
				?>
			<div class="wppr-review-product-image">
				<a href="<?php echo esc_url( $review_image_link ); ?>" <?php echo $lightbox; ?>
					 rel="nofollow" target="_blank" class="wppr-default-img"><img
							src="<?php echo esc_attr( $review_image ); ?>"
							alt="<?php echo esc_attr( $review_object->get_image_alt() ); ?>" class="wppr-product-image"/></a>
			</div>
			<?php } ?>
			<div class="wppr-review-grade-options <?php echo is_rtl() ? 'rtl' : ''; ?>">
				<?php
				foreach ( $review_object->get_options() as $option ) {
					$review_option_rating = $option['value'];
					?>
					<div class="wppr-review-grade-option">
						<div class="wppr-review-grade-option-header">
							<span><?php echo esc_html( apply_filters( 'wppr_option_name_html', $option['name'] ) ); ?></span>
							<span><?php echo esc_html( number_format( ( $review_option_rating / 10 ), 1 ) ); ?></span>
						</div>
						<div class="wppr-review-grade-option-rating wppr-default <?php echo $review_object->get_rating_class( $review_option_rating ); ?> <?php echo is_rtl() ? 'rtl' : ''; ?>">
							<span class="<?php echo $review_object->get_rating_class( $review_option_rating ); ?>" style="
							<?php
							/**
							 * Adds min-width for amp support.
							 */
							 echo 'width:' . esc_attr( is_rtl() ? ( 100 - $review_option_rating ) : $review_option_rating ) . '%; ';
							 echo esc_attr( apply_filters( 'wppr_review_option_rating_css', '', $review_option_rating ) );
							?>
							"></span>
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
					<div class="cons">
						<h3 class="wppr-review-cons-name">
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
