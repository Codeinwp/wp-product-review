<?php 
/**
 * CWP - Top Producs Widget 
 */



class cwp_top_products_widget extends WP_Widget {



function __construct() {

parent::__construct(

'cwp_top_products_widget', 

__('CWP Top Products Widget', 'cwppos'), 



// Widget description

array( 'description' => __( 'This widget displays the top products based on their rating.', 'cwppos' ), ) 

);



}



	// Creating widget front-end

	// This is where the action happens

	public function widget( $args, $instance ) {

		if ( isset( $instance[ 'title' ]) ) 

			$title = apply_filters( 'widget_title', $instance['title'] );

		if ( isset( $instance[ 'no_items' ]) ) 

			$no_items = apply_filters( 'widget_content', $instance['no_items'] );

		if ( isset( $instance[ 'cwp_tp_category' ]) ) 

			$cwp_tp_category = apply_filters( 'widget_content', $instance['cwp_tp_category'] );

		if ( isset( $instance[ 'title_type' ]) ) 

			$post_type = apply_filters( 'widget_content', $instance['title_type'] );

		if ( isset( $instance[ 'show_image' ]) ) 

			$show_image = apply_filters( 'widget_content', $instance['show_image'] );



		// before and after widget arguments are defined by themes

		//echo "<div id='cwp_top_products_widget'>";

		echo $args['before_widget'];

		if ( ! empty( $title ) )

		echo $args['before_title'] . $title . $args['after_title'];


		if ( $cwp_tp_category=="All") $cwp_tp_category="";
	// Loop to get the most popular posts, ordered by the author's final grade.

		$query_args = array(

			'posts_per_page'=> $no_items, // limit it to the specified no of posts
			'post_type'	=>	"any",
			'post__not_in' => get_option('sticky_posts'),
			'category_name' => $cwp_tp_category, // limit it to the specified category
			'meta_key' => 'option_overall_score',

				'meta_query'             => array(

		array(

			'key'       => 'cwp_meta_box_check',

			'value'     => 'Yes',


		),

		),	
		'orderby'	=> 'meta_value_num',
		'order'		=> 'DESC'

		);



		$cwp_top_products_loop = new WP_Query( $query_args );  

		echo "<ul>";

		while($cwp_top_products_loop->have_posts()) : $cwp_top_products_loop->the_post(); ?>



		<li class="cwp-popular-review cwp_top_posts_widget_<?php the_ID(); ?>">
		<?php 
		$product_image = get_post_meta($cwp_top_products_loop->post->ID, "cwp_rev_product_image", true);
		if ($show_image==true&&!empty($product_image)) {
		?>
		
		<img class="cwp_rev_image" src="<?php echo $product_image;?>"\>
		<?php } ?>
		<a href="<?php the_permalink(); ?>">

		<?php if ($post_type==true) { $titlep = get_post_meta($cwp_top_products_loop->post->ID, "cwp_rev_product_name", true);echo $titlep; } else the_title(); ?>

		</a>





			<?php
           
            for($i=1; $i<6; $i++) {
                ${"option".$i."_content"} = get_post_meta($cwp_top_products_loop->post->ID, "option_".$i."_content", true);
                //if(empty(${"option".$i."_content"})) { ${"option".$i."_content"} = __("Default Feature ".$i, "cwppos"); }
            }
            $review_score = cwppos_calc_overall_rating($cwp_top_products_loop->post->ID);
			$review_score = $review_score['overall'];

			if(!empty($review_score)) { ?>

			<div class="review-grade-widget">

				<div class="cwp-review-chart">

				<div class="cwp-review-percentage" data-percent="<?php echo $review_score; ?>"><span></span></div>

				</div><!-- end .chart -->

			</div>

			<?php } ?>

		</li><!-- end .popular-review -->



		<?php endwhile; ?>

		<?php wp_reset_postdata(); // reset the query 



		echo "</ul>";

		echo $args['after_widget'];

		//echo "</div>"; // end #cwp_top_products_widget

	}



	// Widget Backend 

	public function form( $instance ) {

		if ( isset( $instance[ 'title' ] ) ) {

			$title = $instance[ 'title' ];
		}
		else {
			$title = __( 'Top Products', 'cwppos' );
		}
		if ( isset( $instance[ 'no_items' ]) ) {
			$no_items = $instance[ 'no_items' ];
		}
		else {
			$no_items = __( '10', 'cwppos');
		}
		if ( isset( $instance[ 'cwp_tp_category' ]) ) {
			$cwp_tp_category = $instance[ 'cwp_tp_category' ];}
		else {
			$cwp_tp_category = "Select Category";
		}
		if ( isset( $instance[ 'title_type' ]) ) {
			$title_type = $instance[ 'title_type' ];

		}
		else {
			$title_type = false;
		}

		if ( isset( $instance[ 'show_image' ]) ) {
			$show_image = $instance[ 'show_image' ];

		}
		else {
			$show_image = false;
		}
		



		$cwp_tp_categ_array = get_categories('hide_empty=0');

		foreach ($cwp_tp_categ_array as $categs) {

			$cwp_tp_all_categories[$categs->slug] = $categs->name;

		}



	// Widget admin form

	?>

	<p>

	<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:', "cwppos" ); ?></label> 

	<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />

	</p>



	<p>

	<label for="<?php echo $this->get_field_id( 'no_items' ); ?>"><?php _e( 'Number of posts to show:', "cwppos" ); ?></label> 

	<input id="<?php echo $this->get_field_id( 'no_items' ); ?>" name="<?php echo $this->get_field_name( 'no_items' ); ?>" size="3" type="text" value="<?php echo esc_attr( $no_items ); ?>" />

	</p>



	<p>

	<?php $cwp_tp_selected_categ = esc_attr( $cwp_tp_category ); ?>

	<label for="<?php echo $this->get_field_id( 'cwp_tp_category' ); ?>"><?php _e( 'Category:', "cwppos" ); ?></label> 

	<select id="<?php echo $this->get_field_id( 'cwp_tp_category' ); ?>" name="<?php echo $this->get_field_name( 'cwp_tp_category' ); ?>">
	<?php echo "<option>All</option>"; ?>

	<?php foreach ($cwp_tp_all_categories as $categ_slug => $categ_name): ?>

			<?php if($categ_slug == $cwp_tp_selected_categ) {

				echo "<option selected>".$categ_slug."</option>";

			} elseif($categ_slug == "") {

				echo "<option>There are no categs</select>";

			} else { 

				echo "<option>".$categ_slug."</option>";

			} ?>

	<?php endforeach; ?>

	</select>

	</p>
	<p>

	<label for="<?php echo $this->get_field_id( 'title_type' ); ?>"><?php _e( 'Display Product Titles :', "cwppos" ); ?></label> 

	<input  id="<?php echo $this->get_field_id( 'title_type' ); ?>" name="<?php echo $this->get_field_name( 'title_type' ); ?>"  type="checkbox" <?php checked( $title_type ); ?>  />
	</p>

	<p>

	<label for="<?php echo $this->get_field_id( 'show_image' ); ?>"><?php _e( 'Display Product Image :', "cwppos" ); ?></label> 

	<input  id="<?php echo $this->get_field_id( 'show_image' ); ?>" name="<?php echo $this->get_field_name( 'show_image' ); ?>"  type="checkbox" <?php checked( $show_image ); ?>  />
	</p>


	<?php }

	

	// Updating widget replacing old instances with new

	public function update( $new_instance, $old_instance ) {

		$instance = $old_instance;

		$instance['title'] = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';

		$instance['no_items'] = ( ! empty( $new_instance['no_items'] ) ) ? strip_tags( $new_instance['no_items'] ) : '';

		$instance['cwp_tp_category'] = ( ! empty( $new_instance['cwp_tp_category'] ) ) ? strip_tags( $new_instance['cwp_tp_category'] ) : '';

		$instance['title_type'] = (bool) $new_instance['title_type'] ;
		$instance['show_image'] = (bool) $new_instance['show_image'] ;
		
		return $instance;

	}



} // end Class cwp_top_products_widget





// Register and load the widget

function cwp_load_top_products_widget() {

	register_widget( 'cwp_top_products_widget' );

}

add_action( 'widgets_init', 'cwp_load_top_products_widget' );