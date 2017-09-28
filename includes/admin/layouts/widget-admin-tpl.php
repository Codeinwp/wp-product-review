<?php
/**
 *  Widget layout for the admin dashboard.
 *
 * @package     WPPR
 * @subpackage  Layouts
 * @copyright   Copyright (c) 2017, Bogdan Preda
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       3.0.0
 */

// @codingStandardsIgnoreStart
?>
<p>
	<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:', 'wp-product-review' ); ?></label>
	<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $instance['title'] ); ?>" />
</p>
<p>
	<label for="<?php echo $this->get_field_id( 'no_items' ); ?>"><?php _e( 'Number of posts to show:', 'wp-product-review' ); ?></label>
	<input id="<?php echo $this->get_field_id( 'no_items' ); ?>" name="<?php echo $this->get_field_name( 'no_items' ); ?>" size="3" type="text" value="<?php echo esc_attr( $instance['no_items'] ); ?>" />
</p>
<p>
	<?php $cwp_tp_selected_categ = esc_attr( $instance['cwp_tp_category'] ); ?>
	<label for="<?php echo $this->get_field_id( 'cwp_tp_category' ); ?>"><?php _e( 'Category:', 'wp-product-review' ); ?></label>
	<select id="<?php echo $this->get_field_id( 'cwp_tp_category' ); ?>" name="<?php echo $this->get_field_name( 'cwp_tp_category' ); ?>">
		<?php echo '<option>All</option>'; ?>
		<?php foreach ( $instance['cwp_tp_all_categories'] as $categ_slug => $categ_name ) :  ?>
			<?php if ( $categ_slug == $cwp_tp_selected_categ ) {
				echo '<option selected>' . $categ_slug . '</option>';
} elseif ( $categ_slug == '' ) {
	echo '<option>There are no categs</select>';
} else {
	echo '<option>' . $categ_slug . '</option>';
} ?>
		<?php endforeach; ?>
	</select>
</p>
<?php
	if ( 'cwp_top_products_widget' === $this->id_base ) {
	$timespan	= $instance['cwp_timespan'];
	$min		= 0;
	$max		= 0;
	if ( ! empty( $timespan ) && false !== $timespan ) {
		$min_max	= explode( ',', $timespan );
		$min		= reset( $min_max );
		$max		= end( $min_max );
		}
?>
<p>
<label for="<?php echo $this->get_field_id( 'cwp_timespan' ); ?>"><?php _e( 'Timespan (weeks):', 'wp-product-review' ); ?></label>
<div data-wppr-value="<?php echo $timespan; ?>" data-wppr-min="-52" data-wppr-max="0" class="wppr-timespan wppr-range-slider" data-wppr-desc="<?php echo $this->get_field_id( 'cwp_timespan_desc' ); ?>"></div>
<div class="wppr-timespan-desc" id="<?php echo $this->get_field_id( 'cwp_timespan_desc' ); ?>">
<input type="hidden" id="<?php echo $this->get_field_id( 'cwp_timespan' ); ?>" name="<?php echo $this->get_field_name( 'cwp_timespan' ); ?>" value="<?php echo $timespan; ?>">
<?php echo sprintf(__( 'Posts published between %s%d%s and %s%d%s week(s) ago', 'wp-product-review' ), '<span class="wppr-range-min">', abs($min), '</span>', '<span class="wppr-range-max">', abs($max), '</span>');?>
</div>
</p>
<?php
	}
?>
<p>
	<?php $cwp_tp_layout = esc_attr( $instance['cwp_tp_layout'] ); ?>
	<label for="<?php echo $this->get_field_id( 'cwp_tp_layout' ); ?>"><?php _e( 'Layout:', 'wp-product-review' ); ?></label>
	<?php
	$layouts            = array();
	$customLayoutFiles  = glob( WPPR_PATH . '/includes/public/layouts/widget/*.php' );
	foreach ( $customLayoutFiles as $file ) {
		$layouts[ basename( $file ) ] = ucwords( basename( $file, '.php' ) );
	}
	foreach ( $layouts as $key => $val ) :
		$extra      = '';
		if ( $key == $cwp_tp_layout ) { $extra = 'checked'; }
		$styleName  = strtolower( str_replace( ' ', '', $val ) );
		$id         = $this->get_field_id( $styleName );
		?>
		<br>
		<input type="radio" name="<?php echo $this->get_field_name( 'cwp_tp_layout' ); ?>" value="<?php echo $key;?>" id="<?php echo $id . 'style'?>" <?php echo $extra;?> class="wppr-stylestyle"><label for="<?php echo $id . 'style';?>" class="wppr-stylestyle"><?php echo $val;?></label>
		<span class="wppr-styleimg" id="<?php echo $id . 'style'?>img">
			<img src="<?php echo WPPR_URL . '/assets/img/' . $styleName . '.png';?>">
		</span>
		<?php
	endforeach;
	?>
</p>
<p class="wppr-customField" style="display: none">
	<?php $cwp_tp_buynow = esc_attr( $instance['cwp_tp_buynow'] ); ?>
	<label for="<?php echo $this->get_field_id( 'cwp_tp_buynow' ); ?>"><?php _e( 'Buy Now text:', 'wp-product-review' ); ?></label>
	<input id="<?php echo $this->get_field_id( 'cwp_tp_buynow' ); ?>" name="<?php echo $this->get_field_name( 'cwp_tp_buynow' ); ?>" class="widefat" type="text" value="<?php echo $cwp_tp_buynow; ?>" />
</p>
<p class="wppr-customField" style="display: none">
	<?php $cwp_tp_readreview = esc_attr( $instance['cwp_tp_readreview'] ); ?>
	<label for="<?php echo $this->get_field_id( 'cwp_tp_readreview' ); ?>"><?php _e( 'Read Review text:', 'wp-product-review' ); ?></label>
	<input id="<?php echo $this->get_field_id( 'cwp_tp_readreview' ); ?>" name="<?php echo $this->get_field_name( 'cwp_tp_readreview' ); ?>" class="widefat" type="text" value="<?php echo $cwp_tp_readreview; ?>" />
</p>
<p>
	<label for="<?php echo $this->get_field_id( 'title_type' ); ?>"><?php _e( 'Display Product Titles :', 'wp-product-review' ); ?></label>
	<input  id="<?php echo $this->get_field_id( 'title_type' ); ?>" name="<?php echo $this->get_field_name( 'title_type' ); ?>"  type="checkbox" <?php checked( $instance['title_type'] ); ?>  />
</p>
<p>
	<label for="<?php echo $this->get_field_id( 'show_image' ); ?>"><?php _e( 'Display Product Image :', 'wp-product-review' ); ?></label>
	<input  id="<?php echo $this->get_field_id( 'show_image' ); ?>" name="<?php echo $this->get_field_name( 'show_image' ); ?>"  type="checkbox" <?php checked( $instance['show_image'] ); ?>  />
</p>
<?php // @codingStandardsIgnoreEnd ?>
