<?php
	class cwpposSanitizer{
			
		public function sanitize_hex($hex,$default=''){
				 
				if ( $this->validate_hex( $hex ) ) {
							return $hex;
				}
				return $default;
		
		}
		public function sanitize_textarea($input,$default=''){ 
					$output = esc_html(esc_attr(wp_kses_data($input)));
				 
					return $output;
		
		}
		public function sanitize_html($input,$default=''){ 
					$output = wp_kses_data($input);
					return $output;
		
		}
		public function sanitize_number($input,$default = 0){
			$number = floatval($input); 
			if(empty($number)) return $default;
			return $number;
		}
		public function sanitize_imageurl($input,$default = ''){
 
			$filetype = wp_check_filetype($input);
			if ( $filetype["ext"] ) {
				return $input;
			}
			$filetype = wp_check_filetype($default);
			if ( $filetype["ext"] ) {
				return $default;
			} 
			return '';
		}
		public function sanitize_background($input,$default = array("bgcolor"=>"","bgimage"=>"","bgrepeat"=>"no-repeat","bgposition"=>"center center","bgattachment"=>"scroll")){
				$render = new cwpposRenderView();
				$repeat = $render->get_bg_repeat();
				$repeat = array_keys($repeat);
				$position = $render->get_bg_position();
				$position = array_keys($position);
				$att = $render->get_bg_attachment();
				$att = array_keys($att);
				$input['bgcolor'] = apply_filters("pos_sanitize_color",$input['bgcolor'],$default['bgcolor']);
				$input['bgimage'] = apply_filters("pos_sanitize_url",$input['bgimage'],$default['bgimage']);
				if(!in_array($input['bgrepeat'],$repeat))
					$input['bgrepeat'] = $default['bgrepeat'];
				if(!in_array($input['bgposition'],$position))
					$input['bgposition'] = $default['bgposition'];
				if(!in_array($input['bgattachment'],$repeat))
					$input['bgattachment'] = $default['bgattachment'];
				return $input;
		}
		public function sanitize_typography($input,$default = array("color"=>"","size"=>12,"style"=>"normal","font"=>"arial")){  
				$render = new cwpposRenderView();
				$fonts = $render->get_fonts();
				$fonts = array_keys($fonts);
				$styles = $render->get_font_styles();
				$styles = array_keys($styles);
				$sizes = $render->get_font_sizes();
				$input['color'] = apply_filters("cwppos_sanitize_color",$input['color'],$default['color']); 
				if(!in_array($input['size'],$sizes))
					$input['size'] = $default['size'];
				if(!in_array($input['style'],$styles))
					$input['style'] = $default['style'];
				if(!in_array($input['font'],$fonts))
					$input['font'] = $default['font'];
				return $input;
		}
		public function get_config_option($name){
				$structure = cwpposConfig::$structure;
				foreach($structure as $k=>$fields){
								 
								if($fields['type'] == 'tab'){ 
									 
									
									foreach ($fields['options'] as $r=>$field){ 
			  
											if($field['type'] == 'group'){
			 
												foreach($field['options'] as $m=>$gfield){
													if($gfield["type"]!='title' && $gfield["id"] == $name) 
														return $gfield['options'];
												}
												 
											}else{
												if($field["type"]!='title' && $field['id'] == $name) 
														return $field['options'];
											}
											
										} 
									
									}
									 
				} 
				
		}

		public function sanitize_change_icon($input, $name, $default= array()) {
			return wp_kses_data($input);
		}

		public function sanitize_array($input,$name, $default = array()){
		 
				$options = $this->get_config_option($name);
				if ( is_array( $input ) ) { 
					foreach( $input as $key => $value ) {
							
						 	$output[$key] = sanitize_text_field ($value) ; 
					}
					$kop = array_keys($options);
					$dif = array_diff($output,$kop); 
					 
					if(empty($dif))
						return $output;
					return $default;
				}
				return $default;
		}
		public function sanitize_enum($input,$name, $default = ''){ 
				$options = $this->get_config_option($name);
 
				if(in_array($input,array_keys($options)))
					return apply_filters("cwppos_sanitize_textarea",$input);
				return $default;
		}
		public function validate_hex($hex){ 
				$hex = trim( $hex ); 
				if ( 0 === strpos( $hex, '#' ) ) {
					$hex = substr( $hex, 1 );
				}
				elseif ( 0 === strpos( $hex, '%23' ) ) {
					$hex = substr( $hex, 3 );
				} 
				if ( 0 === preg_match( '/^[0-9a-fA-F]{6}$/', $hex ) ) {
					return false;
				}
				else {
					return true;
				}
		}
	}
?>