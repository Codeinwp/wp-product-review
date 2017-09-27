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
	<label for="<?php echo $this->get_field_id( 'cwp_tp_post_types' ); ?>"><?php _e( 'Post Types:', 'wp-product-review' ); ?></label>
	<select id="<?php echo $this->get_field_id( 'cwp_tp_post_types' ); ?>" name="<?php echo $this->get_field_name( 'cwp_tp_post_types' ); ?>[]" class="wppr-chosen wppr-post-types" data-wppr-cat-combo="<?php echo $this->get_field_id( 'cwp_tp_category' ); ?>" multiple>
	<?php
		foreach( get_post_types( '', 'objects' ) as $post_type ) {
	?>
	<option value="<?php echo $post_type->name;?>" <?php echo in_array($post_type->name, $instance['cwp_tp_post_types']) ? 'selected' : '';?>><?php echo $post_type->label;?></option>
	<?php
		}
	?>
	</select>
</p>

<p>
	<?php $all_cats = isset( $instance['cwp_tp_all_categories'] ) ? $instance['cwp_tp_all_categories'] : ''; ?>
	<label for="<?php echo $this->get_field_id( 'cwp_tp_category' ); ?>"><?php _e( 'Category:', 'wp-product-review' ); ?></label>
	<select id="<?php echo $this->get_field_id( 'cwp_tp_category' ); ?>" name="<?php echo $this->get_field_name( 'cwp_tp_category' ); ?>" class="wppr-chosen wppr-cats">
		<option>All</option>
		<?php 
			if ( $all_cats ) {
			foreach ( $all_cats as $post_type => $cats ) {
		?>
		<optgroup label='<?php echo $post_type;?>'>
		<?php
				foreach ( $cats as $k => $v ) {
		?>
<option value="<?php echo $k;?>" <?php selected($k, $instance['cwp_tp_category'])?>><?php echo $v;?></option>
		<?php
				}
		?>
		</optgroup>
		<?php
		}
			}
		?>
	</select>
	<div class="spinner wppr-cat-spinner"></div>
</p>
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
