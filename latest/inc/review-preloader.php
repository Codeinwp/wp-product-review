<?php
require_once( '../../../../wp-load.php' ); 

				$args = array(
					'offset'           => 0,
					'post_type'        => array('any'),
					'post__not_in' => get_option('sticky_posts'),
					'meta_query'             => array(
											array(
												'key'       => 'cwp_meta_box_check',
												'value'     => 'Yes',
											),
										),	
				);

				$cwp_query = new WP_Query($args);
				while ($cwp_query->have_posts()) : $cwp_query->the_post();
				$post_id = $post->ID;
				$preloaded_info = array(); 
				$preloaded_info[$post_id] = array();

				?>
				<li class="cwp_preloaded_item cwpr_clearfix">
					<header>

						<h3 class="cwp_p_title"><?php the_title(); ?></h3>
						<button class="preload" title="Preload all details">&curarr;</button>
					</header>
					<?php 

						for ($i=1; $i <=cwppos("cwppos_option_nr"); $i++) { 
							$preloaded_info[$post_id]["option".$i] = array(
								"content" => get_post_meta($post->ID, "option_" . $i ."_content", true),
								"grade" => get_post_meta($post->ID, "option_" . $i ."_grade", true),
								"pro" 	=> get_post_meta($post->ID, "cwp_option_". $i ."_pro", true),							
								"cons" 	=> get_post_meta($post->ID, "cwp_option_". $i ."_cons", true),
								);
						}
				?>

				<div class="cwp_pitem_info post_<?php echo $post_id; ?>">
					<ul class="cwp_pitem_options_content">
						<h4><?php _e("Options", "cwppos"); ?></h4>
						<?php
							for ($i=1; $i <= cwppos("cwppos_option_nr"); $i++) { 
								$pinfo_temp = $preloaded_info[$post_id]["option". $i]['content'];
								if (!empty($pinfo_temp)) {
									echo "<li>" . $pinfo_temp. "</li>";
								} else {
									echo "<li>-</li>";
								}
							}
						?>
					</ul><!-- end .cwp_pitem_options_content -->

					<ul class="cwp_pitem_options_pros">
						<h4><?php _e("Pros", "cwppos"); ?></h4>
						<?php
							for ($i=1; $i <=cwppos("cwppos_option_nr"); $i++) { 
								$pinfo_temp = $preloaded_info[$post_id]["option". $i]['pro'];
								if (!empty($pinfo_temp)) {
									echo "<li>" . $pinfo_temp. "</li>";
								} else {
									echo "<li>-</li>";
								}
							}
						?>
					</ul><!-- end .cwp_pitem_options_pros -->

					<ul class="cwp_pitem_options_cons">
						<h4><?php _e("Cons", "cwppos"); ?></h4>
						<?php
							for ($i=1; $i <=cwppos("cwppos_option_nr"); $i++) { 
								$pinfo_temp = $preloaded_info[$post_id]["option". $i]['cons'];
								if (!empty($pinfo_temp)) {
									echo "<li>" . $pinfo_temp. "</li>";
								} else {
									echo "<li>-</li>";
								}
							}
						?>
					</ul><!-- end .cwp_pitem_options_cons -->
				</div><!-- end .cwp_pitem_info -->
				</li><!-- end .cwp_preloaded_item -->
				<?php endwhile; wp_reset_postdata(); ?>