<?php
/**
 *  WPPR front page layout.
 *
 * @package     WPPR
 * @subpackage  Layouts
 * @global      WPPR_Review_Model $review_object The inherited review object.
 * @copyright   Copyright (c) 2017, Bogdan Preda
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       3.0.0
 */

$price_raw = $review_object->get_price_raw();

$lightbox = '';

$links                     = $review_object->get_links();
$multiple_affiliates_class = 'affiliate-button';
$links                     = array_filter( $links );
$image_link                = reset( $links );
if ( count( $links ) > 1 ) {
	$multiple_affiliates_class = 'affiliate-button2 affiliate-button';
}
if ( $review_object->get_click() == 'image' ) {
	$lightbox   = 'data-lightbox="' . esc_url( $review_object->get_small_thumbnail() ) . '"';
	$image_link = $review_object->get_image();
}

$pros = $review_object->get_pros();
$cons = $review_object->get_cons();

?>
<div id="wppr-review-<?php echo $review_object->get_ID(); ?>"
	 class="wppr-review-container <?php echo( empty( $pros ) ? 'wppr-review-no-pros' : '' ); ?> <?php echo( empty( $cons ) ? 'wppr-review-no-cons' : '' ); ?>">
	<section id="review-statistics" class="article-section">
		<div class="review-wrap-up  cwpr_clearfix">
			<div class="cwpr-review-top cwpr_clearfix">
				<span><h2 class="cwp-item"><?php echo esc_html( $review_object->get_name() ); ?></h2></span>
				<span class="cwp-item-price cwp-item"><span>
				<span><?php echo esc_html( empty( $price_raw ) ? '' : $price_raw ); ?></span>
			</div><!-- end .cwpr-review-top -->
			<div class="review-wu-content cwpr_clearfix">
				<div class="review-wu-left">
					<div class="review-wu-left-top">
						<div class="rev-wu-image">
							<a href="<?php echo esc_url( $image_link ); ?>" <?php echo $lightbox; ?> rel="nofollow"
							   target="_blank"><img
										src="<?php echo esc_attr( $review_object->get_small_thumbnail() ); ?>"
										alt="<?php echo esc_attr( $review_object->get_name() ); ?>"
										class="photo photo-wrapup wppr-product-image"/></a>
						</div><!-- end .rev-wu-image -->

						<div class="review-wu-grade">
							<div class="review-wu-grade-content">
								<div class="wppr-c100 wppr-p<?php echo esc_attr( round( $review_object->get_rating() ) ) . ' ' . $review_object->get_rating_class(); ?>">
									<span><?php echo esc_html( round( $review_object->get_rating(), 0 ) / 10 ); ?></span>
									<div class="wppr-slice">
										<div class="wppr-bar"></div>
										<div class="wppr-fill"></div>
									</div>
									<div class="wppr-slice-center"></div>
								</div>
							</div>
						</div><!-- end .review-wu-grade -->
					</div><!-- end .review-wu-left-top -->

					<div class="review-wu-bars">
						<?php
						foreach ( $review_object->get_options() as $option ) {

							?>
							<div class="rev-option" data-value="
							<?php echo $option['value']; ?>">
								<div class="cwpr_clearfix">
									<h3><?php echo esc_html( apply_filters( 'wppr_option_name_html', $option['name'] ) ); ?></h3>
									<span><?php echo esc_html( number_format( ( $option['value'] / 10 ), 1 ) ); ?>
										/10 </span>
								</div>
								<ul class="cwpr_clearfix <?php echo $review_object->get_rating_class( $option['value'] ) . apply_filters( 'wppr_option_custom_icon', '' ); ?>">
									<?php for ( $i = 1; $i <= 10; $i ++ ) { ?>
										<li
											<?php
											echo $i <= round( $option['value'] / 10 ) ? ' class="colored"' : '';
											?>
										></li>
									<?php } ?>
								</ul>
							</div>
						<?php } ?>
					</div><!-- end .review-wu-bars -->
				</div><!-- end .review-wu-left -->

				<?php if ( ! empty( $pros ) || ! empty( $cons ) ) : ?>
					<div class="review-wu-right">
						<?php if ( ! empty( $pros ) ) : ?>
							<div class="pros">
								<h2>
									<?php
									echo esc_html(
										apply_filters(
											'wppr_review_pros_text', $review_object->wppr_get_option(
												'cwppos_pros_text'
											)
										)
									);
									?>
								</h2>
								<ul>
									<?php
									foreach ( $pros as $pro ) {
										?>
										<li><?php echo esc_html( $pro ); ?></li>
										<?php
									}
									?>
								</ul>
							</div><!-- end .pros -->
						<?php
						endif;
if ( ! empty( $cons ) ) :
	?>
	<div class="cons">
<h2>
<?php
echo esc_html(
	apply_filters(
		'wppr_review_cons_text', $review_object->wppr_get_option(
			'cwppos_cons_text'
		)
	)
);
?>
</h2>
<ul>
<?php
foreach ( $cons as $con ) {
?>

<li><?php echo esc_html( $con ); ?></li>

			<?php } ?>
		</ul>
	</div>

<?php endif; ?>
					</div><!-- end .review-wu-right -->
				<?php endif; ?>
			</div><!-- end .review-wu-content -->
		</div><!-- end .review-wrap-up -->
	</section>
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
