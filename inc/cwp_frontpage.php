<?php
	function cwp_pac_before_content($content) {
			 
			global $post; 
			$return_string  = '<section id="review-statistics" class="article-section">
                            
                        <div class="review-wrap-up hreview clearfix">
                            <div class="review-top clearfix">
                                <h2 class="item">'.get_post_meta($post->ID, "cwp_rev_product_name", true).'</h2>
                            </div><!-- end .review-top -->
                            <div class="review-wu-left">
                                <div class="rev-wu-image">';
 
                                    $product_image = get_post_meta($post->ID, "cwp_rev_product_image", true);
                                    if(!empty($product_image)) {  
										$return_string .= '<img src="'.$product_image.'" alt="'. get_post_meta($post->ID, "cwp_rev_product_name", true).'" class="photo photo-wrapup"/>';
									 
									} else { 
                                        $return_string .= "<p class='no-featured-image'>".__("No image added.", "cwp")."</p>";
                                    } 
                                    
                                    for($i=1; $i<6; $i++) {
                                        ${"option".$i."_grade"} = get_post_meta($post->ID, "option_".$i."_grade", true); 
                                    }
                                    
                                    for($i=1; $i<6; $i++) {
                                        ${"option".$i."_content"} = get_post_meta($post->ID, "option_".$i."_content", true);
                                        if(empty(${"option".$i."_content"})) { ${"option".$i."_content"} = __("Default Feature ".$i, "cwp"); }
                                    } 
               $return_string .= '</div><!-- end .rev-wu-image -->
                            <div class="review-wu-grade">
                                <div class="chart">
                                    <div class="percentage" data-percent="';
								
                                        $overall_score = "";
                                        $iter = 0;
                                        if(!empty($option1_grade)) { $overall_score += $option1_grade; $iter++; } 
                                        if(!empty($option2_grade)) { $overall_score += $option2_grade; $iter++; }
                                        if(!empty($option3_grade)) { $overall_score += $option3_grade; $iter++; }
                                        if(!empty($option4_grade)) { $overall_score += $option4_grade; $iter++; }
                                        if(!empty($option5_grade)) { $overall_score += $option5_grade; $iter++; }										if($iter == 0){											$overall_score = 0;										}else{ 
											$overall_score = $overall_score / $iter;										}
                                  $return_string .= $overall_score.'"><span class="rating"></span></div>
                                </div><!-- end .chart -->
                            </div><!-- end .review-wu-grade -->
                            <div class="review-wu-bars">';
                                 if (!empty($option1_content) && !empty($option1_grade) &&  strtoupper($option1_content) != 'DEFAULT FEATURE 1') {  
                                    $return_string .= '<div class="rev-option" data-value='.$option1_grade.'>
                                        <div class="clearfix">
                                            <h3>'. $option1_content.'</h3>
                                            <span>'.$option1_grade.'/10</span>
                                        </div>
                                        <ul class="clearfix"></ul>
                                    </div>';
                                 }
                                 
                                 if (!empty($option2_content) && !empty($option2_grade) && strtoupper($option2_content) != 'DEFAULT FEATURE 2') {  
                                    $return_string .= '<div class="rev-option" data-value='.$option2_grade.'>
                                        <div class="clearfix">
                                            <h3>'. $option2_content.'</h3>
                                            <span>'.$option2_grade.'/10</span>
                                        </div>
                                        <ul class="clearfix"></ul>
                                    </div>';
                                 } 
                                 
                                 if (!empty($option3_content) && !empty($option3_grade)&& strtoupper($option3_content) != 'DEFAULT FEATURE 3') {  
                                    $return_string .= '<div class="rev-option" data-value='.$option3_grade.'>
                                        <div class="clearfix">
                                            <h3>'. $option3_content.'</h3>
                                            <span>'.$option3_grade.'/10</span>
                                        </div>
                                        <ul class="clearfix"></ul>
                                    </div>';
                                }
                                 
                                 if (!empty($option4_content) && !empty($option4_grade) && strtoupper($option4_content) != 'DEFAULT FEATURE 4') {  
                                    $return_string .= '<div class="rev-option" data-value='.$option4_grade.'>
                                        <div class="clearfix">
                                            <h3>'. $option4_content.'</h3>
                                            <span>'.$option4_grade.'/10</span>
                                        </div>
                                        <ul class="clearfix"></ul>
                                    </div>'; 
                                 }
                                 if (!empty($option5_content) && !empty($option5_grade) && strtoupper($option5_content) != 'DEFAULT FEATURE 5') {  
                                    $return_string .= '<div class="rev-option" data-value='.$option5_grade.'>
                                        <div class="clearfix">
                                            <h3>'. $option5_content.'</h3>
                                            <span>'.$option5_grade.'/10</span>
                                        </div>
                                        <ul class="clearfix"></ul>
                                    </div>';
								}
                             $return_string .='    
                            </div><!-- end .review-wu-bars -->
                        </div><!-- end .review-wu-left -->
                        <div class="review-wu-right">
                            <div class="pros">';
                                
                                            
                                            
                                            for($i=1; $i<6; $i++) {
                                                ${"pro_option_".$i} = get_post_meta($post->ID, "cwp_option_".$i."_pro", true); 
												if(empty(${"pro_option_".$i})  ) { 
													${"pro_option_".$i} = "" ; 
												} 
                                            }
                                            for($i=1; $i<6; $i++) {
                                                ${"cons_option_".$i} = get_post_meta($post->ID, "cwp_option_".$i."_cons", true); if(empty(${"cons_option_".$i})) { ${"cons_option_".$i} = ""; } 
                                            }
                                 
                               $return_string .=  '<h2>'.__(cwppos("cwppos_pros_text"), "cwp").'</h2>
                                <ul>';
									
									for($i=1;$i<=5;$i++){
										if(!empty(${"pro_option_".$i})) { 
											$return_string .=  '   <li>- '.${"pro_option_".$i}.'</li>';
										}  
                                    }
                           	$return_string .= '     </ul>
                            </div><!-- end .pros -->
                            <div class="cons">';
                              	$return_string .=' <h2>'.__(cwppos("cwppos_cons_text"), "cwp").'</h2>  <ul>';
								
								for($i=1;$i<=5;$i++){
										if(!empty(${"cons_option_".$i})) { 
											$return_string .=  '   <li>- '.${"cons_option_".$i}.'</li>';
										}  
								} 
						$return_string .='
                                </ul>
                            </div><!-- end .pros -->
                        </div><!-- end .review-wu-right -->
                        </div><!-- end .review-wrap-up -->
                    </section><!-- end #review-statistics -->';
					 
                            $affiliate_text = get_post_meta($post->ID, "cwp_product_affiliate_text", true); 
                            $affiliate_link = get_post_meta($post->ID, "cwp_product_affiliate_link", true); 
                            if(!empty($affiliate_text) && !empty($affiliate_link)) {
                  
                       
                                $return_string .= '<div class="affiliate-button">
                                    <a href="'.$affiliate_link.'" rel="nofollow" target="_blank"><span>'. $affiliate_text.'</span> </a> 
                                </div><!-- end .affiliate-button -->';
							}
							
					$cwp_review_stored_meta = get_post_meta( $post->ID );
					if(@$cwp_review_stored_meta['cwp_meta_box_check'][0]  == 'Yes' && (is_single() || is_page()) ) {
						if(cwppos("cwppos_show_reviewbox") == 'yes') 
							return $content.$return_string;
						
						if(cwppos("cwppos_show_reviewbox") == 'no') 
							return $return_string.$content;
						
						return $content.$return_string;
						
					}
					else 
						return $content;
	}
	add_filter('the_content', 'cwp_pac_before_content');
?>