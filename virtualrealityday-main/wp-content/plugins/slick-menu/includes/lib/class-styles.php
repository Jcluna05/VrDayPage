<?php

class Slick_Menu_Styles {

	protected $styles = array();
	protected $google_fonts = array();
	protected $google_fonts_weights = array();
	protected $existing_fonts = array();
	protected $menu_id ;
	protected $item_id;
	protected $item_parent_id;
	protected $type;
	protected $media = 'all';
	protected $medias = array(
		'mobile' => 'and (max-width: 479px)',
		'tablet' => 'and (max-width: 849px)'
	);
	
	protected $selector = '';
	protected $item_selector = '';
	protected $inactive_level_selector = '';
	protected $opening_level_selector = '';
	protected $opened_level_selector = '';
	
	// Body - Menu
	protected $bmselector = '';
	// Body - Menu - Item
	protected $bmiselector = '';
	
	// Body - Active - Menu
	protected $bamselector = '';
	// Body - Active - Menu - Item
	protected $bamiselector = '';
	
	// Body - Active - Always Visible - Menu
	protected $bavmselector = '';
	// Body - Active - Always Visible - Menu - Item
	protected $bavmiselector = '';
	
	protected $bselector = '.sm-body';
	protected $wselector = '#sm-wrapper';
	protected $pselector = '#sm-pusher';
	protected $cselector = '#sm-content';	
	protected $mode = 'options';
	protected $use_options = true;
	protected $custom_css = '';
	protected $minify;
	
	protected $new_line = "\r\n";
	protected $new_tab = "\t";
	protected $new_space = " ";
	  
	function __construct($mode = 'options', $menu_id = null, $item_id = null, $existing_fonts = array()) {
	
		$this->slickmenu = Slick_Menu();
		$this->existing_fonts = $existing_fonts;

		if(!empty($menu_id)) 
		{
			$this->menu_id = $menu_id;
			$this->bmselector = '.sm-body.sm-menu-'.$this->menu_id;
			$this->bamselector = 'html.sm-menu-active .sm-body.sm-menu-'.$this->menu_id;
			$this->bavmselector = 'html.sm-menu-active.sm-always-visible .sm-body.sm-menu-'.$this->menu_id;
			$this->selector = '#sm-menu-'.$this->menu_id;
			$this->type = 'menu';
			
			if(!empty($item_id)) {
				
				$this->type = 'menu-item';
				$this->item_id = $item_id;
				$this->item_parent_id = slick_menu_get_menu_item_parent_id($this->item_id);
				$this->bmiselector = $this->bmselector.'[data-level="menu-item-'.$this->item_id.'"]';
				$this->bamiselector = $this->bamselector.'[data-level="menu-item-'.$this->item_id.'"]';
				$this->bavmiselector = $this->bavmselector.'[data-level="menu-item-'.$this->item_id.'"]';
				$this->item_selector = 'li.menu-item-'.$this->item_id;	

				if(isset($this->item_parent_id)) {
				
					$this->inactive_level_selector = $this->selector.' li.menu-item-'.$this->item_parent_id.' .sm-level:not(.sm-level-open) '.$this->item_selector;
					$this->opening_level_selector = $this->selector.' li.menu-item-'.$this->item_parent_id.' .sm-level-open '.$this->item_selector;
					$this->opened_level_selector = $this->selector.' li.menu-item-'.$this->item_parent_id.' .sm-level-opened '.$this->item_selector;
				}
				
				$this->selector .= ' '.$this->item_selector;	
							
			}else{
				
				$this->inactive_level_selector = $this->selector.' .sm-level:not(.sm-level-open)';
				$this->opening_level_selector = $this->selector.' .sm-level-open';	
				$this->opened_level_selector = $this->selector.' .sm-level-opened';		
			}
		}
		
		$this->mode = $mode;
		$this->debug = defined( 'WP_DEBUG' ) && WP_DEBUG ? true : false;
		$this->minify = (defined( 'SM_SCRIPT_DEBUG' ) && SM_SCRIPT_DEBUG) ? false : true;
		
		if($this->minify) {
			$this->new_line = '';
			$this->new_tab = '';
			$this->new_space = '';
		}

	}  
	  
	// Public Methods

	public function isMode($mode) {
		return $this->mode == $mode;
	}

	public function setMedia($media) {
		$this->media = $media;
	}
	
	public function resetMedia() {
		$this->media = 'all';
	}

	public function setMediaBreakpoint($media, $breakpoint) {
		
		if(!empty($this->medias[$media])) {
			$this->medias[$media] = 'and (max-width: '.$breakpoint.')';
		}
	}
		
	public function setUseOptions($flag = true) {
		$this->use_options = $flag;
	}

	public function useOptions() {
		return $this->use_options;
	}
		  
	public function set($element, $attr, $option_key, $format = null, $important = false, $prefix = false) {
		
		$important = $this->important($important);
		
		$responsiveUnit = false;
		if($format == "unit-responsive") {	
			$responsiveUnit = true;
			$format = "unit";
		}	
		
		if($this->useOptions()) {

			$value = $this->get_option($option_key, $format);
			
		}else{

			$value = $this->format($option_key, $format);
		}
		
		if(strpos($value, "vw") !== false || strpos($value, "vh") !== false) {
			$responsiveUnit = false;
		}

		if($responsiveUnit) {
			$this->setMedia('mobile');
			$this->set($element, $attr, $option_key, array(array('styles', 'calc'), array('*', 0.2333, 'vw')));
			$this->resetMedia();
		}	

		if($attr == 'background-image' && is_array($value)) {
			$value = !empty($value[0]) && !empty($value[0]) ? wp_get_attachment_url($value[0]) : '';
		}
							
		if($value !== '') {
			
			if($prefix) {
				$value = $this->cssPrefix($attr, $value.$important);
			}else{
				$value = array($attr => $value.$important);
			}
			
			$styles[$this->media][$element] = $value;
			
			$this->mergeStyles($styles);
		
		}
	}

	public function setBackground($element, $option_key, $important = false) {
		
		if($this->useOptions()) {
		
			$value = $this->get_option($option_key);

		}else{
			
			$value = $option_key;
		}
		
		$image = !empty($value['image']) && !empty($value['image'][0]) ? wp_get_attachment_url($value['image'][0]) : '';
		$color = !empty($value['color']) ? $value['color'] : '';
		$repeat = !empty($value['repeat']) ? $value['repeat'] : '';
		$size = !empty($value['size']) ? $value['size'] : '';
		$position = !empty($value['position']) ? $value['position'] : '';
		
		$important = $this->important($important);
		
		$styles[$this->media][$element] = array(
			'background-image' => $image.$important,
			'background-color' => $color.$important,
			'background-repeat' => $repeat.$important,
			'background-size' => $size.$important,
			'background-position' => $position.$important
		);
				
		$this->mergeStyles($styles);
		
	}
	
	public function setPattern($element, $option_key, $format = null, $important = false) {

		if($this->useOptions()) {
		
			$value = $this->get_option($option_key, $format);

		}else{
			
			$value = $this->format($option_key, $format);
		}

		
		$pattern = !empty($value['pattern']) ? $value['pattern'] : '';
		$opacity = !empty($value['opacity']) ? $value['opacity'] : '';
	
		$styles = array();

		$important = $this->important($important);
			
		if(!empty($pattern) && $pattern !== '_none') {
			
			$styles[$this->media][$element] = array(
				'background-image' => $pattern.$important,
				'opacity' => $opacity.$important
			);
		}

		$this->mergeStyles($styles);
				    
	}
		
	public function setOverlay($element, $option_key, $important = false) {

		if($this->useOptions()) {
		
			$value = $this->get_option($option_key);

		}else{
			
			$value = $option_key;
		}
		
		$important = $this->important($important);

		$type = !empty($value['type']) ? $value['type'] : 'color';
		
		$styles = array();
		
		if($type === 'gradient') {
			
			$direction = !empty($value['direction']) ? $value['direction'] : '';
			$color_start = !empty($value['color_start']) ? $value['color_start'] : '';
			$color_end = !empty($value['color_end']) ? $value['color_end'] : '';
			
			if(!empty($direction) && !empty($color_start) && !empty($color_end)) {

				$gradient_string  = "-moz-linear-gradient({direction}, {color_start} 0%, {color_end} 100%);".$this->new_line;
				$gradient_string .= "background: -o-linear-gradient({direction}, {color_start} 0%, {color_end} 100%);".$this->new_line;
				$gradient_string .= "background: -webkit-linear-gradient({direction}, {color_start} 0%, {color_end} 100%);".$this->new_line;
				$gradient_string .= "background: -ms-linear-gradient({direction}, {color_start} 0%,{color_end} 100%);".$this->new_line;
				$gradient_string .= "background: linear-gradient({direction}, {color_start} 0%,{color_end} 100%)".$this->new_line;
			    
			    $gradient = str_replace(
			    	array(
				    	'{direction}',
				    	'{color_start}',
				    	'{color_end}'
			    	),
			    	array(
				    	$direction,
				    	$color_start,
				    	$color_end
			    	),
			    	$gradient_string
				);

				$styles[$this->media][$element] = array(
					'background' => $gradient.$important
				);
			
			}
			
		}else{

			$color = !empty($value['color']) ? $value['color'] : '';
			
			if(!empty($color)) {
				$styles[$this->media][$element] = array(
					'background' => $color.$important
				);
			}
						
		}
		
		$this->mergeStyles($styles);
				    
	}
	
	public function setTransform($element, $option_key, $important = false, $textAlign = null, $mobileCentered = null) {
		
		if($this->useOptions()) {
		
			$value = $this->get_option($option_key);

		}else{
			
			$value = $option_key;
		}
		
		if(!slick_menu_is_empty($value['enabled'])) {
		
			$important = $this->important($important);
			
			$duration = !empty($value['duration']) ? $value['duration'] : 400;
			$scaleX = !empty($value['scale-x']) ? $value['scale-x'] : 1;
			$scaleY = !empty($value['scale-y']) ? $value['scale-y'] : 1;
			$translateX = !empty($value['translate-x']) ? $value['translate-x'] : 0;
			$translateY = !empty($value['translate-y']) ? $value['translate-y'] : 0;
			$translateZ = !empty($value['translate-z']) ? $value['translate-z'] : 1;
			$rotate = !empty($value['rotate']) ? $value['rotate'] : 0;
			$rotateX = !empty($value['rotate-x']) ? $value['rotate-x'] : 0;
			$rotateY = !empty($value['rotate-y']) ? $value['rotate-y'] : 0;
			$rotateZ = !empty($value['rotate-z']) ? $value['rotate-z'] : 0;
			$skew = !empty($value['skew']) ? $value['skew'] : 0;
			
			$radius = !empty($value['radius']) ? $value['radius'] : 0;
		
			$duration = $this->format($duration, 'ms');
			$translateX = $this->format($translateX, 'unit');
			$translateY = $this->format($translateY, 'unit');
			$translateZ = $this->format($translateZ, 'unit');
			$rotate = $this->format($rotate, 'deg');
			$rotateX = $this->format($rotateX, 'deg');
			$rotateY = $this->format($rotateY, 'deg');
			$rotateZ = $this->format($rotateZ, 'deg');
			$skew = $this->format($skew, 'deg');
			$radius = $this->format($radius, 'unit');

			$transform = "scaleX($scaleX) scaleY($scaleY) translateX($translateX) translateY($translateY) translateZ($translateZ) rotate($rotate) rotateX($rotateX) rotateY($rotateY) rotateZ($rotateZ) skew($skew)";
			$transform_mobile = "inherit";
			
			$styles[$this->media][$element] = array_merge(
				$this->cssPrefix('transform', $transform.$important),	
				array(
					'border-radius'	=> $radius.$important
				)
			);	
		
			if($textAlign == 'center' || !empty($mobileCentered)) {
				$styles['mobile'][$element] = $this->cssPrefix('transform', $transform_mobile.$important);	
			}
			
			$this->mergeStyles($styles);
		
		}
	}


	public function setPerspective($element, $option_key, $important = false) {
		
		if($this->useOptions()) {
		
			$value = $this->get_option($option_key);

		}else{
			
			$value = $option_key;
		}

		if(!empty($value)) {
		
			$important = $this->important($important);
					
			$perspective = $this->format($value, 'vw');
						
			$styles[$this->media][$element] = $this->cssPrefixLight('perspective', $perspective.$important);
					
			$this->mergeStyles($styles);
		
		}
	}

	public function setPerspectiveOrigin($element, $option_key, $important = false) {
		
		if($this->useOptions()) {
		
			$value = $this->get_option($option_key);

		}else{
			
			$value = $option_key;
		}

		if(!empty($value)) {
		
			$important = $this->important($important);
						
			$styles[$this->media][$element] = $this->cssPrefixLight('perspective-origin', $value.$important);
					
			$this->mergeStyles($styles);
		}
	}
		
	public function setTransition($element, $option_key, $merge = array(), $important = false) {
		
		if($this->useOptions()) {
		
			$value = $this->get_option($option_key);

		}else{
			
			$value = $option_key;
		}
		
		if(!empty($merge)) {
			$value = array_merge_recursive($value, $merge);
		}
		
		if(!slick_menu_is_empty($value['duration']) || !slick_menu_is_empty($value['delay'])) {
		
			$important = $this->important($important);
			
			$property = !empty($value['property']) ? $value['property'] : 'all';
			$duration = !empty($value['duration']) ? $value['duration'] : 400;
			$easing = !empty($value['easing']) ? $value['easing'] : 'ease';
			$delay = !empty($value['delay']) ? $value['delay'] : 0;
		
			$duration = $this->format($duration, 'ms');
			$delay = $this->format($delay, 'ms');

			$transition = "$property $duration $easing $delay";
						
			$styles[$this->media][$element] = $this->transitionPrefix('transition', $transition.$important);
					
			$this->mergeStyles($styles);
		
		}
	}
	
	public function setTransitionDelay($element, $option_key, $important = false) {
		
		if($this->useOptions()) {
		
			$value = $this->get_option($option_key);

		}else{
			
			$value = $option_key;
		}

		if(!slick_menu_is_empty($value)) {
		
			$important = $this->important($important);
					
			$delay = $this->format($value, 'ms');
						
			$styles[$this->media][$element] = $this->cssPrefixLight('transition-delay', $delay.$important);
					
			$this->mergeStyles($styles);
		
		}
	}
			
	public function setTransitionDuration($element, $option_key, $important = false) {
		
		if($this->useOptions()) {
		
			$value = $this->get_option($option_key);

		}else{
			
			$value = $option_key;
		}

		if(!slick_menu_is_empty($value)) {
		
			$important = $this->important($important);
					
			$duration = $this->format($value, 'ms');
						
			$styles[$this->media][$element] = $this->cssPrefixLight('transition-duration', $duration.$important);
					
			$this->mergeStyles($styles);
		
		}
	}
	
	public function setBoxShadow($element, $option_key, $important = false) {
		
		if($this->useOptions()) {
		
			$value = $this->get_option($option_key);

		}else{
			
			$value = $option_key;
		}
		
		$color = !empty($value['color']) ? $value['color'] : '';
		$hshadow = !empty($value['hshadow']) ? $value['hshadow'] : '0';
		$vshadow = !empty($value['vshadow']) ? $value['vshadow'] : '0';
		$blur = !empty($value['blur']) ? $value['blur'] : '0';
		$spread = !empty($value['spread']) ? $value['spread'] : '0';
		
		if(!empty($color)) {
		
			$important = $this->important($important);
			
			$hshadow = $this->format($hshadow, 'unit');
			$vshadow = $this->format($vshadow, 'unit');
			$blur = $this->format($blur, 'unit');
			$spread = $this->format($spread, 'unit');
					
			$box_shadow = "$hshadow $vshadow $blur $spread $color$important";
	
			$styles[$this->media][$element] = $this->cssPrefix('box-shadow', $box_shadow);
					
			$this->mergeStyles($styles);
		}
	}

  	public function setBorder($element, $option_key, $important = false) {
		
		if($this->useOptions()) {
		
			$value = $this->get_option($option_key);

		}else{
			
			$value = $option_key;
		}
				
		$top_width = isset($value['top-width']) ? $value['top-width'] : '0';
		$right_width = isset($value['right-width']) ? $value['right-width'] : '0';
		$bottom_width = isset($value['bottom-width']) ? $value['bottom-width'] : '0';
		$left_width = isset($value['left-width']) ? $value['left-width'] : '0';
		$radius = isset($value['radius']) ? $value['radius'] : '0';
		$style = isset($value['style']) ? $value['style'] : 'none';
		$color = isset($value['color']) ? $value['color'] : '';

		$empty = (empty($top_width) && empty($right_width) && empty($bottom_width) && empty($left_width)) || (empty($style) || empty($color));
		
		if(!empty($radius)) {
			$empty = false;
		}
		
		if(!$empty) {
			
			$important = $this->important($important);

			$top_width = $this->format($top_width, 'unit');
			$right_width = $this->format($right_width, 'unit');
			$bottom_width = $this->format($bottom_width, 'unit');
			$left_width = $this->format($left_width, 'unit');
			$radius = $this->format($radius, 'unit');
			
			$styles[$this->media][$element] = array(
				'border-top-width' => $top_width.$important,
				'border-right-width' => $right_width.$important,
				'border-bottom-width' => $bottom_width.$important,
				'border-left-width' => $left_width.$important,
				'border-radius' => $radius.$important,
				'border-style' => $style.$important,
				'border-color' => $color.$important
			);
					
			$this->mergeStyles($styles);
			
		}
		
	}
	  		
	public function setFont($element, $option_key) {
		
		if($this->useOptions()) {
		
			$font = $this->get_option($option_key);
		
			$font_data = explode(":", $font);
			
			$font_family = '';
			if(!empty($font_data[0])) {
				$font_family = "'".urldecode($font_data[0])."'";
			}
			
			$font_weight = "";
			if(!empty($font_data[1])) {
				$font_weight = $font_data[1];
			}
		
			
			$fonts = array();
			if(!empty($font) && is_string($font)) {
	
				$fonts[md5($font)] = $font;
			}
			
			$this->mergeFonts($fonts);
		
			$styles[$this->media][$element] = array(
				'font-family' => $font_family,
				'font-weight' => $font_weight
			);

		}else{
			
			$styles[$this->media][$element] = $option_key;
		}
		
		$this->mergeStyles($styles);
		
	}
	
	public function cssPrefix($attr, $value) {
		
		return array(
			"-webkit-$attr" => $value,
		    "-o-$attr" => $value,
		    "-moz-$attr" => $value,
		    "-ms-$attr" => $value,
		    "$attr" => $value
	    );
	}

	public function cssPrefixLight($attr, $value) {
		
		return array(
			"-webkit-$attr" => $value,
		    "$attr" => $value
	    );
	}
		
	public function transitionPrefix($attr, $value) {
		
		return array(
			"/* 1 */-webkit-$attr" => $this->propertyPrefix("webkit", $value),
		    "/* 2 */$attr" => $this->propertyPrefix("webkit", $value),
		    "/* 3 */-o-$attr" => $this->propertyPrefix("o", $value),
		    "/* 4 */-moz-$attr" => $value.', '.$this->propertyPrefix("moz", $value),
		    "/* 5 */$attr" => $value,
		    "/* 6 */$attr" => $value.', '.$this->propertyPrefix("webkit", $value),
	    );
	}
	
	public function propertyPrefix($prefix, $value) {
		
		$prefixable = array(
			'transform',
			'background-clip',
			'background-size'	
		);
		
		foreach($prefixable as $property) {
			
			if(strpos($value, $property) !== false) {
				$value = str_replace($property, "-$prefix-".$property, $value);
			}
		}
		
		return $value;

	}
	
	public function setCustomCss($css) {
		
		$this->custom_css .= $css;
	}
	
	function important($flag = false) {
		
		$important = '';
		if($flag) {
			$important = '!important';
		}
		
		return $important;
	}
	
	function hex2rgb($hex) {
	
	   $hex = str_replace("#", "", $hex);
	
	   if(strlen($hex) == 3) {
	      $r = hexdec(substr($hex,0,1).substr($hex,0,1));
	      $g = hexdec(substr($hex,1,1).substr($hex,1,1));
	      $b = hexdec(substr($hex,2,1).substr($hex,2,1));
	   } else {
	      $r = hexdec(substr($hex,0,2));
	      $g = hexdec(substr($hex,2,2));
	      $b = hexdec(substr($hex,4,2));
	   }
	   $rgb = array($r, $g, $b);

	   return $rgb; // returns an array with the rgb values
	}
		
	public function get_option($key, $format = null, $checkParents = true) {
		
		$value = '';
		$subKey = false;
		
		if(strpos($key, "::") !== false) {
			$keyData = explode("::", $key);
			$key = $keyData[0];
			$subKey = $keyData[1];
		}
		
		if($this->isMode('options')) {

			if($this->type == 'menu') {
				
				$value = $this->slickmenu->get_menu_option($key, $this->menu_id);
				
			}else{

				if($checkParents) {
					
					$menu_key = str_replace(
						array("menu-item-", "submenu-items-"), 
						"menu-items-", 
						$key
					);
					$item_key = str_replace(
						array("menu-items-", "submenu-items-"), 
						"menu-item-", 
						$key
					);
					$item_parent_key = str_replace(
						array("menu-items-", "menu-item-"), 
						"submenu-items-", 
						$key
					);
					
					$value = $this->slickmenu->get_menu_option(
						$menu_key, 
						$this->menu_id,
						$item_key, 
						$this->item_id,
						$item_parent_key, 
						$this->item_parent_id
					);
				
				}else{
					
					$value = $this->slickmenu->get_menu_item_option($key, $this->item_id);
				}
			}
			
		}else if($this->isMode('settings')) {
			
			$value = $this->slickmenu->get_setting($key);
		}
		
		if(!empty($subKey)) {
			if(isset($value[$subKey])) {
				$value = $value[$subKey];
			}else{
				$value = "";
			}	
		}
		
		if(!slick_menu_is_empty($value)) {
		
			$value = $this->format($value, $format);

		}
			
		return $value;			
	}

	public function get_top_item_option($key, $format = null) {
		
		$value = '';
		$subKey = false;
		
		if(strpos($key, "::") !== false) {
			$keyData = explode("::", $key);
			$key = $keyData[0];
			$subKey = $keyData[1];
		}
		
		if($this->isMode('options')) {

			if($this->type == 'menu') {
				$value = $this->slickmenu->get_menu_option($key, $this->menu_id);
			}else{
				$value = $this->slickmenu->get_menu_item_option($key, $this->item_id);
			}
			
		}else if($this->isMode('settings')) {
			
			$value = $this->slickmenu->get_setting($key);
		}
		
		if(!empty($subKey)) {
			if(isset($value[$subKey])) {
				$value = $value[$subKey];
			}else{
				$value = "";
			}	
		}
		
		if(!slick_menu_is_empty($value)) {
		
			$value = $this->format($value, $format);

		}
			
		return $value;			
	}
	
	public function format($value, $format = null) {
	
		if(!empty($format)) {
			
			if(is_array($format)) {
				
				$func = $format[0];
				
				if(is_array($func) && $func[0] === 'styles') {
					$func[0] = $this;
				}
				
				$args = array();
				$args[] = $value;
				
				if(isset($format[1])) {
					$args = array_merge($args, $format[1]);
				}
				
				if(is_callable($func)) {
					$value = call_user_func_array($func, $args);
				}
				
			}else{
				
				if(strpos($format, '{value}') !== false) {
					
					$value = str_replace("{value}", $value, $format);
					
				}else if($format == 'unit') {
					
					if(is_numeric($value) !== false) {
						$value = $value.'px';
					}
					
				}else if($format == 'unit-skip-zero') {
					
					if(is_numeric($value) !== false) {
						if(intval($value) == 0){
							$value = "";
						}else{	
							$value = $value.'px';
						}
					}
						
				}else if($format == 'level-width') {
					
					if(is_numeric($value) !== false) {
						$value = $value.'px';
					}
				
				}else if($format == 'level-height') {
					
					if(is_numeric($value) !== false) {
						$value = $value.'px';
					}
	
				}else if($format == 'perc') {
					
					$value = intval($value).'%';
					
				}else if($format == 'ms') {
					
					$value = intval($value).'ms';
					
				}else if($format == 'deg') {
					
					$value = intval($value).'deg';
					
				}else if($format == 'vw') {
					
					$value = intval($value).'vw';
					
				}else if($format == 'vh') {
					
					$value = intval($value).'vh';
				}
				
			}
		}
			
		return $value;			
	}
	
	
	function calc($value, $operator, $modifier, $unit = 'px') {
		
		switch($operator) {
			case '+':
				return (intval($value) + floatval($modifier)) . $unit;
			case '-':	
				return (intval($value) - floatval($modifier)) . $unit;
			case '*':	
				return (intval($value) * floatval($modifier)) . $unit;		
			case '/':	
				return (intval($value) / floatval($modifier)) . $unit;	
		}
	}
	
					
	public function render($return = false, $includeFonts = true) {

		$this->mergeFontsWithExisting();
		
		if($includeFonts) {
			$this->renderGoogleFonts();
		}	

		$css = "";
		
		foreach($this->styles as $media => $elements) {

			foreach($elements as $elem => $styles) {
			
				$element_css = "";
				
				$media_tab_space = "";
				
				if($media != "all") {
					$media_tab_space = $this->new_tab;
					$media_query = $this->medias[$media];
					$element_css .= "@media only screen ".($media_query)." {".$this->new_line.$media_tab_space;
				}
	
				if(preg_match("/(\{.+?\})/", $elem) === 1 || substr($elem, 0,3) === '===') {
					
					$elem = str_replace(
						array(
							'===',
							'{selector}', 
							'{inactive_level_selector}',
							'{opening_level_selector}',
							'{opened_level_selector}',
							'{bmselector}',
							'{bmiselector}',
							'{bamselector}',
							'{bamiselector}',
							'{bavmselector}',
							'{bavmiselector}',
							'{bselector}',
							'{wselector}',
							'{pselector}',
							'{cselector}',
						),
						array(
							'',
							$this->selector,
							$this->inactive_level_selector,
							$this->opening_level_selector,
							$this->opened_level_selector,
							$this->bmselector,
							$this->bmiselector,
							$this->bamselector,
							$this->bamiselector,
							$this->bavmselector,
							$this->bavmiselector,
							$this->bselector,
							$this->wselector,
							$this->pselector,
							$this->cselector,
						), 
						$elem
					);
					
					$element_css .= $elem.$this->new_space."{";
				
				}else{
					
					$element_css .= $this->selector;
					if(!empty($elem) && $elem[0] !== "&") {
						$element_css .= ' ';
					}else{
						$elem = ltrim($elem, '&');
					}
					
					$element_css .= $elem.$this->new_space."{";
				}
			
				
				$element_css .= $this->new_line;
				
				$count = 0;
				foreach($styles as $attr => $value) {
				
					if($value && !empty($value) && $value != '' && !is_array($value)) {
					
						if($attr == 'background-image') {
							$value = 'url('.$value.')';
						}	
				
						$element_css .= $this->new_tab.$media_tab_space.$attr.":".$this->new_space.$value.";".$this->new_line;
						
						$count++;
					}
				}
				$element_css .= $media_tab_space."}".$this->new_line;
				
				if($media != "all") {
					$element_css .= "}".$this->new_line;
				}
				
				if($count > 0)
					$css .= $element_css;
					
			}
		}
				
		$css .= $this->custom_css;
		
		if($this->minify) {
			$this->minify($css);
		}else{
			$this->removeComments($css);
		}

		if($return)
			return $css;
		
		echo($css);
		
	}
	
    public function minify(&$buffer) {
	    
	    // Remove comments
		$buffer = preg_replace('!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $buffer);
		
		// Remove space after colons
		$buffer = str_replace(': ', ':', $buffer);
		
		// Remove whitespace
		$buffer = str_replace(array("\r\n", "\r", "\n", "\t"), '', $buffer);
		
		// Collapse adjacent spaces into a single space
		$buffer = preg_replace("/ {2,}/", ' ',$buffer);
		
		// Remove spaces that might still be left where we know they aren't needed
		$buffer = str_replace(array('} '), '}', $buffer);
		$buffer = str_replace(array('{ '), '{', $buffer);
		$buffer = str_replace(array('; '), ';', $buffer);
		$buffer = str_replace(array(', '), ',', $buffer);
		$buffer = str_replace(array(' }'), '}', $buffer);
		$buffer = str_replace(array(' {'), '{', $buffer);
		$buffer = str_replace(array(' ;'), ';', $buffer);
		$buffer = str_replace(array(' ,'), ',', $buffer);

    }
    
    public function removeComments(&$buffer) {
	    
	    // Remove comments
		$buffer = preg_replace('!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $buffer);
	}	
	
	public function getCurrentFonts() {
		
		return array_merge(
			$this->google_fonts,
			$this->existing_fonts
		);
		
	}


	public function renderGoogleFonts($return = false) {

		if(empty($this->google_fonts))
			return false;
			
		$import = '';	
		$fonts = '';
		
		if(!empty($this->google_fonts)) {
			
			$total = count($this->google_fonts);
			$i = 0;
			foreach($this->google_fonts as $font) {
				
				$fonts .= $font;
				if($i < ($total - 1)) {
					$fonts .= "|";
				}
				
				$i++;
			}
			
			$fonts = urlencode($fonts);
			$import = "@import url(//fonts.googleapis.com/css?family=".$fonts.");".$this->new_line;
		}
		
		if($return)
			return $import;
		
		echo $import;
	}
	
		
	
	// Protected Methods

	protected function mergeStyles($styles) {
		
		$this->styles = array_merge_recursive(
			$this->styles,
			$styles
		);
	}

	protected function mergeFonts($fonts) {
		
		$this->google_fonts = array_merge(
			$this->google_fonts,
			$fonts
		);
		
	} 
	
	protected function mergeFontsWithExisting() {

		$this->google_fonts = array_diff(
			$this->google_fonts,
			$this->existing_fonts
		);
		
	} 
	 
}
