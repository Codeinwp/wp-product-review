<?php 

/**

 * CWP - Top Producs Widget 

 */



class cwp_top_products_widget extends WP_Widget {



function __construct() {

parent::__construct(

'cwp_top_products_widget', 

__('WP Product Review Top Products Widget', 'cwppos'), 



// Widget description

array( 'description' => __( 'This widget displays the top products based on their rating.', 'cwppos' ), ) 

);



}



	// Creating widget front-end

	// This is where the action happens

	public function widget( $args, $instance ) {

		$title = apply_filters( 'widget_title', $instance['title'] );

		$no_items = apply_filters( 'widget_content', $instance['no_items'] );

		$cwp_tp_category = apply_filters( 'widget_content', $instance['cwp_tp_category'] );



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

		<a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>





			<?php
            for($i=1; $i<6; $i++) {
                ${"option".$i."_grade"} = get_post_meta($cwp_top_products_loop->post->ID, "option_".$i."_grade", true);
                //if(empty(${"option".$i."_grade"})) { ${"option".$i."_grade"} = "10"; }
            }
            
            for($i=1; $i<6; $i++) {
                ${"option".$i."_content"} = get_post_meta($cwp_top_products_loop->post->ID, "option_".$i."_content", true);
                //if(empty(${"option".$i."_content"})) { ${"option".$i."_content"} = __("Default Feature ".$i, "cwppos"); }
            }
            $overall_score = "";
            $iter = "";
            if(!empty($option1_grade)) { $overall_score += $option1_grade; $iter++; }
            if(!empty($option2_grade)) { $overall_score += $option2_grade; $iter++; }
            if(!empty($option3_grade)) { $overall_score += $option3_grade; $iter++; }
            if(!empty($option4_grade)) { $overall_score += $option4_grade; $iter++; }
            if(!empty($option5_grade)) { $overall_score += $option5_grade; $iter++; }
            $overall_score = $overall_score / $iter;
        	

			$review_score = $overall_score;
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

			$no_items = $instance[ 'no_items' ];

			$cwp_tp_category = $instance[ 'cwp_tp_category' ];

		}

		else {

			$title = __( 'Top Products', 'cwppos' );

			$no_items = __( '10', 'cwppos');

			$cwp_tp_category = "Select Category";

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



	<?php }

	

	// Updating widget replacing old instances with new

	public function update( $new_instance, $old_instance ) {

		$instance = array();

		$instance['title'] = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';

		$instance['no_items'] = ( ! empty( $new_instance['no_items'] ) ) ? strip_tags( $new_instance['no_items'] ) : '';

		$instance['cwp_tp_category'] = ( ! empty( $new_instance['cwp_tp_category'] ) ) ? strip_tags( $new_instance['cwp_tp_category'] ) : '';



		return $instance;

	}



} // end Class cwp_top_products_widget





// Register and load the widget

function cwp_load_top_products_widget() {

	register_widget( 'cwp_top_products_widget' );

}

add_action( 'widgets_init', 'cwp_load_top_products_widget' );