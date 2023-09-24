<?php

if (!defined('ABSPATH'))
    exit;

class Slick_Menu_Walker extends Walker_Nav_Menu
{
	private $curMenu;
    private $curItem;
    private $curLevelItem = array();
    private $slickmenu;
    
    function __construct() {
	    
	    $this->slickmenu = Slick_Menu();
	    
		add_filter( 'nav_menu_link_attributes', array($this, 'link_attributes'), PHP_INT_MAX, 3 );
		add_filter( 'nav_menu_css_class', array($this, 'nav_menu_css_class'), PHP_INT_MAX, 4 );
		add_filter( 'slick_menu_item_title_inner_wrap_before', array($this, 'menu_item_thumb'), PHP_INT_MAX, 4 );
		add_filter( 'slick_menu_item_title_inner_wrap_after', array($this, 'menu_item_thumb'), PHP_INT_MAX, 4 );
		add_filter( 'slick_menu_item_title_before', array($this, 'menu_item_arrow'), PHP_INT_MAX, 4 );
		add_filter( 'slick_menu_item_title_after', array($this, 'menu_item_arrow'), PHP_INT_MAX, 4 );
		add_filter( 'slick_menu_item_title_class', array($this, 'menu_item_class'), PHP_INT_MAX, 2 );
    }

 	function menu_item_arrow($content, $item_id, $title, $target ) {

		if(empty($this->curMenu) || empty($this->curItem)) {
			return $content;
		}
			
		$menu_id = $this->curMenu;

		$item_parent_id = intval($this->curItem->menu_item_parent);
		
		$menu_item_arrow_hide = (bool)$this->slickmenu->get_menu_option(
			'menu-items-arrow-hide', 
			$menu_id,
			'menu-item-arrow-hide',
			$item_id,
			'submenu-items-arrow-hide',
			$item_parent_id
		);
		
		if(empty($menu_item_arrow_hide)) {
			
			$menu_item_arrow = SM_Icons_Front_End::get_icon(
				$this->slickmenu->mb_field_id('menu-items-arrow-icon'), 
				$menu_id,
				$this->slickmenu->mb_field_id('menu-item-arrow-icon'), 
				$item_id,
				$this->slickmenu->mb_field_id('submenu-items-arrow-icon'),	
				$item_parent_id	
			);
			
			$menu_item_arrow_position = $this->slickmenu->get_menu_option(
				'menu-items-arrow-position', 
				$menu_id,
				'menu-item-arrow-position',
				$item_id,
				'submenu-items-arrow-position',
				$item_parent_id
			);

			if(empty($menu_item_arrow_position)) {
				$menu_item_arrow_position = 'right';	
			}
			
			// Inject Arrow
			if(!empty($menu_item_arrow)) {
				
				$arrow_classes = array('sm-arrow');
				$arrow_classes[] = $menu_item_arrow_position == 'left' ? '_before' : '_after';
				$arrow_classes = slick_menu_class_string($arrow_classes);
			
				$menu_item_arrow = str_replace('_before', '', $menu_item_arrow);
				$menu_item_arrow = str_replace('class="', 'class="'.$arrow_classes.' ', $menu_item_arrow);
				
				if(
					($menu_item_arrow_position == 'left' && $target == "before") || 
					($menu_item_arrow_position == 'right' && $target == "after")
				) 
				{
					$content = $menu_item_arrow;					
				}	
			}
		}
		
	    return $content;
	}
	
	function menu_item_thumb($content, $item_id, $title, $target) {

		$menu_item_thumb = $this->slickmenu->get_menu_item_option('menu-item-thumb', $item_id);

		// Inject Image
		if(!empty($menu_item_thumb)) {
			
			if(empty($this->curMenu) || empty($this->curItem)) {
				return $content;
			}
			
			$menu_id = $this->curMenu;
			$item_parent_id = intval($this->curItem->menu_item_parent);
			
			$thumb_classes = array('sm-thumb');
			
			// Thumb Position
			$thumb_position = $this->slickmenu->get_menu_option('menu-items-thumb-position', $menu_id, 'menu-item-thumb-position', $item_id, 'submenu-items-thumb-position', $item_parent_id);
			if(empty($thumb_position)) {
				$thumb_position = 'above';
			}
			$thumb_classes[] = 'sm-thumb-'.$thumb_position;
	
			// Thumb Stretch
			$thumb_stretch = $this->slickmenu->get_menu_option('menu-items-thumb-stretch', $menu_id, 'menu-item-thumb-stretch', $item_id, 'submenu-items-thumb-stretch', $item_parent_id);
			if(!empty($thumb_stretch)) {
				$thumb_classes[] = 'sm-thumb-stretch';
			}
				
			// Thumb Size			
			$thumb_size = $this->slickmenu->get_menu_option('menu-items-thumb-size', $menu_id, 'menu-item-thumb-size', $item_id, 'submenu-items-thumb-size', $item_parent_id);
			if(empty($thumb_size)) {
				$thumb_size = 'medium';
			}
			
			// Thumb Custom Size
			$crop = false;
			if($thumb_size == 'custom') {
				$width = $this->slickmenu->get_menu_option('menu-items-thumb-width', $menu_id, 'menu-item-thumb-width', $item_id, 'submenu-items-thumb-width', $item_parent_id);
				$height = $this->slickmenu->get_menu_option('menu-items-thumb-height', $menu_id, 'menu-item-thumb-height', $item_id, 'submenu-items-thumb-height', $item_parent_id);
				$crop = (bool)$this->slickmenu->get_menu_option('menu-items-thumb-crop', $menu_id, 'menu-item-thumb-crop', $item_id, 'submenu-items-thumb-crop', $item_parent_id);
				$thumb_size = array($width, $height);
			}
			
			$thumb = Slick_Menu_Image::get($menu_item_thumb[0], $thumb_size, $crop);
			
			if(empty($thumb)) {
				return $content;
			}
			
			// Thumb Filters			
			$thumb_filter = $this->slickmenu->get_menu_option('menu-items-thumb-filter', $menu_id, 'menu-item-thumb-filter', $item_id, 'submenu-items-thumb-filter', $item_parent_id);
			$thumb_hover_filter = $this->slickmenu->get_menu_option('menu-items-thumb-hover-filter', $menu_id, 'menu-item-thumb-hover-filter', $item_id, 'submenu-items-thumb-hover-filter', $item_parent_id);
			
			if(!empty($thumb_filter)) {
				$thumb_classes[] = $thumb_filter;
			}
			if(!empty($thumb_hover_filter)) {
				$thumb_classes[] = 'sm-filter-hover';
			}	
					
			$thumb_classes = slick_menu_class_string($thumb_classes);
			
			if(
				($thumb_position == 'replace' && $target == 'before_inner_wrap') ||
				($thumb_position == 'above' && $target == 'before_inner_wrap') ||
				($thumb_position == 'below' && $target == 'after_inner_wrap') 
			){
				
				$menu_item_thumb = '<img class="'.$thumb_classes.'" src="'.set_url_scheme($thumb[0]).'" width="'.$thumb[1].'" height="'.$thumb[2].'">';
				$content = $menu_item_thumb;
				
			}else if($thumb_position == 'behind' && $target == 'before_inner_wrap'){
				
				$menu_item_thumb = '<div class="'.$thumb_classes.'" style="background-image:url('.set_url_scheme($thumb[0]).');"></div>';
				$content = $menu_item_thumb;
			}
		}
		
		return $content;		
	}
	
	function menu_item_class($classes, $item_id) {
	
		if(empty($item_id) || empty($this->curMenu) || empty($this->curItem)) {
			return $classes;
		}
	
		$menu_item_thumb = $this->slickmenu->get_menu_item_option('menu-item-thumb', $item_id);

		if(!empty($menu_item_thumb)) {
			
			$menu_id = $this->curMenu;
			$item_parent_id = intval($this->curItem->menu_item_parent);
			
			// Thumb Position
			$thumb_position = $this->slickmenu->get_menu_option('menu-items-thumb-position', $menu_id, 'menu-item-thumb-position', $item_id, 'submenu-items-thumb-position', $item_parent_id);
			if($thumb_position === 'behind') {	
				
				$classes[] = 'sm-has-thumb-behind';
				
			}else if($thumb_position === 'replace') {	
				
				$classes[] = 'sm-item-hide-title';
			}	
			
			// Thumb Filter on Hover			
			$thumb_hover_filter = $this->slickmenu->get_menu_option('menu-items-thumb-hover-filter', $menu_id, 'menu-item-thumb-hover-filter', $item_id, 'submenu-items-thumb-hover-filter', $item_parent_id);
			if(!empty($thumb_hover_filter)) {
				$classes[] = $thumb_hover_filter.'-hover';
			}	
		}
		
		return $classes;
	}
 
 	function link_attributes( $atts, $item, $args ) {

		if(!empty($args->sm_menu)) {
			
			$menu_id = $args->menu;
			if(is_object($menu_id) && !empty($menu_id->term_id)) {
				$menu_id = $menu_id->term_id;
			}
			$item_id = $item->ID;
			$item_parent_id = intval($item->menu_item_parent);
			
			$animation_class = $this->slickmenu->get_menu_option(
		    	'menu-items-animation', 
		    	$menu_id,
		    	'menu-item-animation', 
		    	$item_id,
		    	'submenu-items-animation',
		    	$item_parent_id
		    );

			$classes = array();
			
			if(!empty($atts['class'])) {
				$classes = explode(" ", $atts['class']);
			}	
	
			if(!empty($animation_class)) {
				
				$classes[] = 'sm-animated';
				
				$atts['data-animation'] = 'sm-'.esc_attr($animation_class);
			}
			
			$label_visibility = $this->slickmenu->get_menu_option(
		    	'menu-items-label-visibility', 
		    	$menu_id,
		    	'menu-item-label-visibility', 
		    	$item_id,
		    	'submenu-items-label-visibility',
		    	$item_parent_id
		    );
		    
			if(!empty($label_visibility)) {	
				if($label_visibility === 'show-on-hover') {
					
					$classes[] = 'sm-show-title-onhover';
					
				}else if($label_visibility === 'hide-on-hover') {
					
					$classes[] = 'sm-hide-title-onhover';
				}
			}
			
			$atts['class'] = slick_menu_class_string($classes);
		}
		
	    return $atts;
	}
	   
	function nav_menu_css_class($classes, $item, $args, $depth ) {
		
		if(!empty($args->sm_menu)) {
			
			$menu_id = $args->menu;
			if(is_object($menu_id) && !empty($menu_id->term_id)) {
				$menu_id = $menu_id->term_id;
			}
			$item_id = $item->ID;
			$item_parent_id = intval($item->menu_item_parent);
			
			$enable_empty_sublevel = $this->slickmenu->get_menu_item_option('level-show-empty', $item_id );
			if(!empty($enable_empty_sublevel)) {
			
				$classes[] = 'menu-item-has-children';
			}


			$menu_item_height = $this->slickmenu->get_menu_option(
				'menu-items-height', 
				$menu_id,
				'menu-item-height',
				$item_id,
				'submenu-items-height',
				$item_parent_id
			);
			
			if(!empty($menu_item_height)) {
				$menu_item_valign = $this->slickmenu->get_menu_option(
					'menu-items-vertical-align', 
					$menu_id,
					'menu-item-vertical-align',
					$item_id,
					'submenu-items-vertical-align',
					$item_parent_id
				);
				if(!empty($menu_item_valign)) {
					$classes[] = 'sm-valign-'.$menu_item_valign;
				}
			}
							
			$menu_item_fullwidth = $this->slickmenu->get_menu_option(
				'menu-items-fullwidth', 
				$menu_id,
				'menu-item-fullwidth',
				$item_id,
				'submenu-items-fullwidth',
				$item_parent_id
			);
			if(!empty($menu_item_fullwidth)) {
				$classes[] = 'sm-fullwidth';
			}
					
			$hover_animation_class = $this->slickmenu->get_menu_option(
		    	'menu-items-hover-animation', 
		    	$menu_id,
		    	'menu-item-hover-animation', 
		    	$item_id,
		    	'submenu-items-hover-animation',
		    	$item_parent_id
		    );
		    
		    if(!empty($hover_animation_class)) {
				$classes[] = $hover_animation_class;
		    }

		    $menu_item_column = $this->slickmenu->get_menu_option(
		    	'level-menu-columns', 
		    	$menu_id,
		    	'menu-item-column', 
		    	$item_id,
		    	'level-menu-columns',
		    	$item_parent_id
		    );
		    
		    if(!empty($menu_item_column)) {
				$classes[] = 'sm-col-'.$menu_item_column;
		    }
		}		
		
		return array_unique($classes);
	}   
	
    public function start_lvl(&$output, $depth = 0, $args = array())
    {
	    
		$prefix = 'sm-';
		$level_classes = array('sm-level');
		$header_classes = array('sm-header', 'sm-level-component');
		$title_classes = array('sm-title', 'sm-level-component');
		$title_overlap_classes = array('sm-title-overlap');
		$title_wrap_classes = array('sm-title-wrap');
		$subtitle_classes = array('sm-subtitle', 'sm-level-component');
		$subtitle_wrap_classes = array('sm-subtitle-wrap', 'sm-level-component');
		$description_classes = array('sm-description', 'sm-level-component');
		$description_wrap_classes = array('sm-description-wrap');
		$back_classes = array('sm-back', 'sm-level-component');
		$nav_classes = array('sm-nav-list');
		
		$level_data = array();
		
		$level_data['level'] = $depth + 2;
		
	    $item = $this->curItem;
	    $options = $args->sm_options;
	    $menu_id = $args->menu;
	    if(is_object($menu_id) && !empty($menu_id->term_id)) {
			$menu_id = $menu_id->term_id;
		}
	    $item_id = $item->ID;
	    $item_parent_id = intval($item->menu_item_parent);

		$this->curLevelItem[$depth] = $item;

		$levelAnimation = $this->slickmenu->get_menu_option('level-animation-type', $menu_id);


		// SUBLEVEL SETTINGS

		// CONTENT WRAPPER BG	
		$wrapper_bgimage = $this->slickmenu->get_menu_option('wrapper-bg', $menu_id, 'wrapper-bg', $item_id );
		$wrapper_bgpattern = $this->slickmenu->get_menu_option('wrapper-pattern', $menu_id, 'wrapper-pattern', $item_id );
		$wrapper_bgoverlay = $this->slickmenu->get_menu_option('wrapper-overlay', $menu_id, 'wrapper-overlay', $item_id );
		$wrapper_bgvideo = $this->slickmenu->get_menu_option('wrapper-video', $menu_id, 'wrapper-video', $item_id );
		$wrapper_bgfilter = $this->slickmenu->get_menu_option('wrapper-filter', $menu_id, 'wrapper-filter', $item_id );
		

		if(!empty($wrapper_bgimage['image_url']) || !empty($wrapper_bgimage['color'])) {
			
			$level_classes[] = 'sm-has-wrapper-bg';
			
			$level_data['wrapper-image'] = set_url_scheme(slick_menu_array_value($wrapper_bgimage, 'image_url'));
			$level_data['wrapper-color'] = slick_menu_array_value($wrapper_bgimage, 'color');
			$level_data['wrapper-repeat'] = slick_menu_array_value($wrapper_bgimage, 'repeat');
			$level_data['wrapper-size'] = slick_menu_array_value($wrapper_bgimage, 'size');
			$level_data['wrapper-position'] = slick_menu_array_value($wrapper_bgimage, 'position');
		}
		
		if(!empty($wrapper_bgpattern['pattern'])) {
			
			$level_classes[] = 'sm-has-wrapper-bg';
			
			$level_data['pattern-image'] = slick_menu_array_value($wrapper_bgpattern, 'pattern');
			$level_data['pattern-opacity'] = slick_menu_array_value($wrapper_bgpattern, 'opacity');
				
		}
		
		if(!empty($wrapper_bgoverlay)) {
			
			$level_classes[] = 'sm-has-wrapper-bg';
			$type = slick_menu_array_value($wrapper_bgoverlay, 'type');
			
			$level_data['overlay-type'] = $type;
			
			if($type == 'gradient') {
				$level_data['overlay-direction'] = slick_menu_array_value($wrapper_bgoverlay, 'direction');
				$level_data['overlay-color-start'] = slick_menu_array_value($wrapper_bgoverlay, 'color_start');
				$level_data['overlay-color-end'] = slick_menu_array_value($wrapper_bgoverlay, 'color_end');
			}else{
				$level_data['overlay-color'] = slick_menu_array_value($wrapper_bgoverlay, 'color');	
			}					
		}

		if(!empty($wrapper_bgvideo) && !empty($wrapper_bgvideo["id"])) {
			
			$level_classes[] = 'sm-has-wrapper-video';
			
			$level_data['video-id'] = slick_menu_array_value($wrapper_bgvideo, 'id');
			$level_data['video-quality'] = slick_menu_array_value($wrapper_bgvideo, 'quality');
			$level_data['video-opacity'] = slick_menu_array_value($wrapper_bgvideo, 'opacity');
			$level_data['video-scale'] = slick_menu_array_value($wrapper_bgvideo, 'scale');
			$level_data['video-start'] = slick_menu_array_value($wrapper_bgvideo, 'start');
			$level_data['video-end'] = slick_menu_array_value($wrapper_bgvideo, 'end');
			$level_data['video-nopause'] = slick_menu_array_value($wrapper_bgvideo, 'nopause');
			$level_data['video-repeat'] = slick_menu_array_value($wrapper_bgvideo, 'repeat');
			$level_data['video-audio'] = slick_menu_array_value($wrapper_bgvideo, 'audio');
			$level_data['video-spinner'] = slick_menu_array_value($wrapper_bgvideo, 'spinner');
			$level_data['video-mobile'] = slick_menu_array_value($wrapper_bgvideo, 'mobile');
		
		}
		
		if(!empty($wrapper_bgfilter)) {
			
			$level_classes[] = 'sm-has-wrapper-bg';
			
			$level_data['wrapper-filter'] = $wrapper_bgfilter;
		}
		
		// CONTENT FILTER
		$level_data['content-filter'] = $this->slickmenu->get_menu_option(
			'content-filter', 
			$menu_id, 
			'content-filter', 
			$item_id, 
			'content-filter',
			$item_parent_id
		);

		
		// GENERAL

		$width = str_replace('%', 'perc', $this->slickmenu->get_menu_option(
			'level-width', 
			$menu_id, 
			'level-width', 
			$item_id, 
			'level-width',
			$item_parent_id
		));
		
		$level_data['width'] = $width;
		
		$level_scroll_disabled = (bool)$this->slickmenu->get_menu_option('level-disabled-scroll', $menu_id, 'level-disabled-scroll', $item_id, 'level-disabled-scroll', $item_parent_id );
		if($level_scroll_disabled) {
			$level_classes[] = 'sm-no-scroll';
		}
		
		$level_show_scrollbar = (bool)$this->slickmenu->get_menu_option('level-show-scrollbar', $menu_id, 'level-show-scrollbar', $item_id, 'level-show-scrollbar', $item_parent_id );
		if($level_show_scrollbar) {
			$level_classes[] = 'sm-show-scrollbar';
		}
		
		$level_scroll_to_current = (bool)$this->slickmenu->get_menu_option('level-scroll-to-current', $menu_id, 'level-scroll-to-current', $item_id, 'level-scroll-to-current', $item_parent_id );
		$level_data['scroll-to-current'] = $level_scroll_to_current;

		$header_stick_top = (bool)$this->slickmenu->get_menu_option('level-header-stick-top', $menu_id, 'level-header-stick-top', $item_id, 'level-header-stick-top', $item_parent_id );
		
		if($header_stick_top) {
			$header_over_content = (bool)$this->slickmenu->get_menu_option('level-header-over-content', $menu_id, 'level-header-over-content', $item_id, 'level-header-over-content', $item_parent_id );
			if($header_over_content) {
				$header_classes[] = 'sm-header-over-content';
			}
		}
		

		
		$level_mobile_centered = (bool)$this->slickmenu->get_menu_option('level-mobile-centered', $menu_id, 'level-mobile-centered', $item_id, 'level-mobile-centered', $item_parent_id );
		if(!empty($level_mobile_centered)) {
			$level_classes[] = 'sm-mobile-centered';
		}
			

		// LOGO
		
		$logo_main_level = (bool)$this->slickmenu->get_menu_option('logo-main-level', $menu_id);
		$logo_hidden = $this->slickmenu->get_menu_option('logo-hidden', $menu_id, 'logo-hidden', $item_id );
		
		if($logo_main_level) {
			$logo_hidden = true;
		}
		
		$logo_use_avatar = $this->slickmenu->get_menu_option(
			'logo-use-avatar', 
			$menu_id,
			'logo-use-avatar', 
			$item_id, 
			'logo-use-avatar',
			$item_parent_id
		);
	
		
		if ( $logo_use_avatar ) {
			
            if(is_user_logged_in()) {
	            $current_user = wp_get_current_user();
	
		        if ( ($current_user instanceof WP_User) ) {
			        
			        $logo_width = $this->slickmenu->get_menu_option(
			        	'logo-width', 
			        	$menu_id,
			        	'logo-width', 
						$item_id, 
						'logo-width',
						$item_parent_id
			        );
			        
			        $logo_width = intval($logo_width);
			        $logo_width = !empty($logo_width) ? $logo_width : 90;
			        
		            $logo_avatar = get_avatar( $current_user->user_email, $logo_width);
		            
		            $logo_url = get_the_author_meta('user_url', $current_user->ID);
		            
		            if(empty($logo_url)) {
			            $logo_url = esc_url( get_author_posts_url( $current_user->ID, $current_user->user_nicename ) );
			        } 
		        }
	        }
	        
        }else{
	        	
			$logo = $this->slickmenu->get_menu_option(
				'logo', 
				$menu_id, 
				'logo', 
				$item_id, 
				'logo',
				$item_parent_id
			);
			
			$logo_url = $this->slickmenu->get_menu_option('logo-url', $menu_id, 'logo-url', $item_id );
        }

			
		// BG VIDEO		
		$bgvideo = $this->slickmenu->get_menu_item_option('level-video', $item_id );
		if(!empty($bgvideo['id'])) {
			$level_classes[] = 'sm-has-level-video';
		}

		
		// BACK LINK
		$back_link_hidden = $this->slickmenu->get_menu_option('back-link-hidden', $menu_id, 'back-link-hidden', $item_id );

		$back_link_position = $this->slickmenu->get_menu_option('back-link-vposition', $menu_id, 'back-link-vposition', $item_id );
		if(!empty($back_link_position)) {
			
			if($back_link_position == 'top') {
				
				$back_classes[] = 'sm-back-top';
				
			}else if($back_link_position == 'bottom') {
				
				$back_classes[] = 'sm-back-bottom';
			}
			
		}else{
			
			$back_link_position = "above";
		}
		
		$back_link_icon_position = $this->slickmenu->get_menu_option('back-link-icon-position', $menu_id, 'back-link-icon-position', $item_id );
		if(empty($back_link_icon_position)) {
			$back_link_icon_position = 'after';
		}
		
		$back_link_icon = SM_Icons_Front_End::get_icon( 
			$this->slickmenu->mb_field_id('back-link-icon'), 
			$menu_id,
			$this->slickmenu->mb_field_id('back-link-icon'), 
			$item_id,
			array(
				'position' => $back_link_icon_position
			) 
		);
		
		$back_link_icon_only = (bool)$this->slickmenu->get_menu_option('back-link-icon-only', $menu_id, 'back-link-icon-only', $item_id, 'back-link-icon-only', $item_parent_id );
		
	
		// TITLE			
		$title_hidden = (bool)$this->slickmenu->get_menu_option('title-hidden', $menu_id, 'title-hidden', $item_id );

		if($title_hidden) {
			$title_classes[] = 'sm-title-hidden';
		}

		$title_stick_top = $this->slickmenu->get_menu_option('title-stick-top', $menu_id, 'title-stick-top', $item_id );
		if(!empty($title_stick_top)) {
			$title_classes[] = 'sm-title-top';
		}
		
		$title_fullwidth = $this->slickmenu->get_menu_option('title-fullwidth', $menu_id, 'title-fullwidth', $item_id );
		if(!empty($title_fullwidth)) {
			$title_classes[] = 'sm-fullwidth';
		}
				
		$title_animation_data = '';
		$title_animation_class = $this->slickmenu->get_menu_option('title-animation', $menu_id, 'title-animation', $item_id);
		
		if(!empty($title_animation_class)) {
			$title_animation_data = ' data-animation="sm-'.esc_attr($title_animation_class).'"';
			$title_wrap_classes[] = 'sm-animated';
		}
		
		$title_override = $this->slickmenu->get_menu_option('title-override', $menu_id, 'title-override', $item_id );
		
		
		// SUB TITLE			
		$subtitle_enabled = (bool)$this->slickmenu->get_menu_item_option('subtitle-enabled', $item_id );
		
		if($subtitle_enabled) {
			
			$subtitle = $this->slickmenu->get_menu_item_option('subtitle-text', $item_id );
			if(!empty($subtitle)) {
							
				$subtitle_animation_data = '';
				$subtitle_animation_class = $this->slickmenu->get_menu_option('subtitle-animation', $menu_id, 'subtitle-animation', $item_id);
				
				if(!empty($subtitle_animation_class)) {
					$subtitle_animation_data = ' data-animation="sm-'.esc_attr($subtitle_animation_class).'"';
					$subtitle_wrap_classes[] = 'sm-animated';
				}
			}	
		}
		
		// DESCRIPTION			
		$description_enabled = (bool)$this->slickmenu->get_menu_item_option('description-enabled', $item_id );
		
		if($description_enabled) {
			
			$description_from_page = (bool)$this->slickmenu->get_menu_item_option('description-from-page', $item_id );

			if($description_from_page) {
				$description_page = intval($this->slickmenu->get_menu_item_option('description-page', $item_id ));
				if(!empty($description_page)) {
					$description = apply_filters('the_content', get_post_field('post_content', $description_page));
				}
			}else{
				$description = $this->slickmenu->get_menu_item_option('description-text', $item_id );
			}	
								
			if(!empty($description)) {
			
				$description = $this->slickmenu->output->get_dynamic_fragment('description', $description, $menu_id, $item_id);
						
				$description_animation_data = '';
				$description_animation_class = $this->slickmenu->get_menu_option('description-animation', $menu_id, 'description-animation', $item_id);
				
				if(!empty($description_animation_class)) {
					$description_animation_data = ' data-animation="sm-'.esc_attr($description_animation_class).'"';
					$description_wrap_classes[] = 'sm-animated';
				}
			}	
		}
		
					
		
		// NAV ITEMS
		$nav_hidden = $this->slickmenu->get_menu_option('menu-items-hidden', $menu_id, 'submenu-items-hidden', $item_id );
		if(!empty($nav_hidden)) {
			$nav_classes[] = 'sm-nav-items-hidden';
		}	
		
		$nav_align = $this->slickmenu->get_menu_option(
	    	'level-menu-align', 
	    	$menu_id,
	    	'level-menu-align', 
	    	$item_id
	    );
	    
	    if(!empty($nav_align)) {
			$nav_classes[] = 'sm-nav-align-'.$nav_align;
	    }
			    
	    $nav_column_align = $this->slickmenu->get_menu_option(
	    	'level-menu-column-align', 
	    	$menu_id,
	    	'level-menu-column-align', 
	    	$item_id
	    );
	    
	    if(!empty($nav_column_align)) {
			$nav_classes[] = 'sm-col-align-'.$nav_column_align;
	    }
		
		$show_title_icon = (bool)$this->slickmenu->get_menu_option('title-show-icon', $menu_id, 'title-show-icon', $item_id );
		$menu_item_icon = SM_Icons_Front_End::get_icon( 
			null,
			null,
			$this->slickmenu->mb_field_id('menu-item-icon'), 
			$item_id 
		);


		// FILTER CLASSES
		$level_classes = apply_filters('slick_menu_item_level_classes', $level_classes, $menu_id, $item_id);

		
		// PROCESS CLASSES
		$level_classes = slick_menu_class_string($level_classes);
		$header_classes = slick_menu_class_string($header_classes);
		$title_classes = slick_menu_class_string($title_classes);
		$title_overlap_classes = slick_menu_class_string($title_overlap_classes);
		$title_wrap_classes = slick_menu_class_string($title_wrap_classes);
		$subtitle_classes = slick_menu_class_string($subtitle_classes);
		$subtitle_wrap_classes = slick_menu_class_string($subtitle_wrap_classes);
		$description_classes = slick_menu_class_string($description_classes);
		$description_wrap_classes = slick_menu_class_string($description_wrap_classes);
		$back_classes = slick_menu_class_string($back_classes);
		$nav_classes = slick_menu_class_string($nav_classes);
		

		// FILTER DATA
		$level_data = apply_filters('slick_menu_item_level_data', $level_data, $menu_id, $item_id);
					
		// PROCESS DATA
		$level_data = slick_menu_data_string($level_data);
		
		
		// EXPORT LOCAL VARS
		$vars = get_defined_vars();
		
		//------ OUTPUT START ------- //
		

		// SM LEVEL
		$output .=  '<div class="'.esc_attr($level_classes).'" '.$level_data.'>';

			// SM LEVEL OVERLAP BORDER
			$output .= slick_menu_include_part('overlap-border', $vars);
			
		 
			// SM LEVEL BG VIDEO
			$output .= slick_menu_include_part('video-bg', $vars);
			

			// SM LEVEL INNER
			$output .= '	<div class="sm-level-inner">';

				
				// SM HEADER STICK TOP
				if($header_stick_top) {
					$output .= slick_menu_include_part('header', $vars);
				}
						
													
				// SM LEVEL BODY
				$output .= '	<div class="sm-level-body">';
				
					// SM LEVEL ALIGN
					$output .= '	<div class="sm-level-align">';
			
						// SM HEADER
						if(!$header_stick_top) {
							$output .= slick_menu_include_part('header', $vars);
						}
																		
						// SM OVERLAP TITLE
						$output .= slick_menu_include_part('title-overlap', $vars);
						
						// SM BACK
						if($back_link_position == 'above' || $back_link_position == 'top') {
							$output .= slick_menu_include_part('back', $vars);
						}
				
						// SM TITLE
						$output .= slick_menu_include_part('title', $vars);

						// SM DESCRIPTION
						$output .= slick_menu_include_part('description', $vars);
						
						// SM NAV BEGIN
						$output .= slick_menu_include_part('nav-begin', $vars);

							
    }

    public function start_el(&$output, $item, $depth = 0, $args = array(), $id = 0)
    {	
	    if(!empty($args->menu->term_id)) {
	    	$this->curMenu = $args->menu->term_id;
	    }else{
		    $this->curMenu = $args->menu;
	    }
	    $this->curItem = $item;
		
		parent::start_el($output, $item, $depth, $args, $id);
    }

    public function end_lvl(&$output, $depth = 0, $args = array())
    {
	    
	    $back_classes = array('sm-back', 'sm-level-component');
		$footer_classes = array('sm-footer', 'sm-level-component');
		$footer_text_classes = array('sm-footer-text');
		
	    $item = $this->curLevelItem[$depth];
	    $options = $args->sm_options;
	    $menu_id = $args->menu;
	    if(is_object($menu_id) && !empty($menu_id->term_id)) {
			$menu_id = $menu_id->term_id;
		}
	    $item_id = $item->ID;
	    $item_parent_id = intval($item->menu_item_parent);

		
		// BACK LINK
		$back_link_position = $this->slickmenu->get_menu_option('back-link-vposition', $menu_id, 'back-link-vposition', $item_id );
		if(!empty($back_link_position)) {
			
			if($back_link_position == 'top') {
				
				$back_classes[] = 'sm-back-top';
				
			}else if($back_link_position == 'bottom') {
				
				$back_classes[] = 'sm-back-bottom';
			}
			
		}else{
			
			$back_link_position = "above";
		}
		

		$back_link_icon_position = $this->slickmenu->get_menu_option('back-link-icon-position', $menu_id, 'back-link-icon-position', $item_id );
		if(empty($back_link_icon_position)) {
			$back_link_icon_position = 'after';
		}
		
		$back_link_icon = SM_Icons_Front_End::get_icon( 
			$this->slickmenu->mb_field_id('back-link-icon'), 
			$menu_id,
			$this->slickmenu->mb_field_id('back-link-icon'), 
			$item_id,
			array(
				'position' => $back_link_icon_position
			) 
		);
		
		$back_link_icon_only = (bool)$this->slickmenu->get_menu_option('back-link-icon-only', $menu_id, 'back-link-icon-only', $item_id, 'back-link-icon-only', $item_parent_id );
		
		// FOOTER
		$footer_text = $this->slickmenu->get_menu_item_option('footer-text', $item_id );
		$footer_stick_bottom = (bool)$this->slickmenu->get_menu_option('level-footer-stick-bottom', $menu_id, 'level-footer-stick-bottom', $item_id, 'level-footer-stick-bottom', $item_parent_id );
		
		$footer_animation_data = '';
		$footer_animation_class = $this->slickmenu->get_menu_option('footer-animation', $menu_id, 'footer-animation', $item_id);
		
		if(!empty($footer_animation_class)) {
			$footer_animation_data = ' data-animation="sm-'.esc_attr($footer_animation_class).'"';
			$footer_text_classes[] = 'sm-animated';
		}
							
		if($footer_stick_bottom) {
			$footer_over_content = (bool)$this->slickmenu->get_menu_option('level-footer-over-content', $menu_id, 'level-footer-over-content', $item_id, 'level-footer-over-content', $item_parent_id );
			if($footer_over_content) {
				$footer_classes[] = 'sm-footer-over-content';
			}
		}
		
	    // PROCESS CLASSES	
		
		$footer_classes = slick_menu_class_string($footer_classes);
		$footer_text_classes = slick_menu_class_string($footer_text_classes);
		$back_classes = slick_menu_class_string($back_classes);
		

		// EXPORT LOCAL VARS
		$vars = get_defined_vars();
		
		//------ OUTPUT START ------- //


						// SM NAV END
						$output .= slick_menu_include_part('nav-end', $vars);
						
						// SM BACK
						if($back_link_position == 'below' || $back_link_position == 'bottom') {
							$output .= slick_menu_include_part('back', $vars);
						}
						
						
						// SM FOOTER
						if(!$footer_stick_bottom) {
							$output .= slick_menu_include_part('footer', $vars);
						}	
						
					
					// END SM LEVEL ALIGN					
					$output .= '</div>';
				
				
				// END SM LEVEL BODY		
				$output .= '</div>';	
						
				
				// SM FOOTER STICK FOOTER
				if($footer_stick_bottom) {
					$output .= slick_menu_include_part('footer', $vars);
				}	

			// END SM LEVEL INNER	
			$output .= '</div>';
		
		
		// END SM LEVEL	
		$output .= '</div>';

	}


	/**
	 * Traverse elements to create list from elements.
	 *
	 * Display one element if the element doesn't have any children otherwise,
	 * display the element and its children. Will only traverse up to the max
	 * depth and no ignore elements under that depth. It is possible to set the
	 * max depth to include all depths, see walk() method.
	 *
	 * This method should not be called directly, use the walk() method instead.
	 *
	 * @since 2.5.0
	 *
	 * @param object $element           Data object.
	 * @param array  $children_elements List of elements to continue traversing.
	 * @param int    $max_depth         Max depth to traverse.
	 * @param int    $depth             Depth of current element.
	 * @param array  $args              An array of arguments.
	 * @param string $output            Passed by reference. Used to append additional content.
	 */
	public function display_element( $element, &$children_elements, $max_depth, $depth, $args, &$output ) {
		if ( ! $element ) {
			return;
		}

		$id_field = $this->db_fields['id'];
		$id = $element->$id_field;
		
		//display this element
		$this->has_children = ! empty( $children_elements[ $id ] );
		if ( isset( $args[0] ) && is_array( $args[0] ) ) {
			$args[0]['has_children'] = $this->has_children; // Backwards compatibility.
		}

		$cb_args = array_merge( array(&$output, $element, $depth), $args);
		call_user_func_array(array($this, 'start_el'), $cb_args);

		// descend only when the depth is right and there are childrens for this element
		if ( ($max_depth == 0 || $max_depth > $depth+1 )  ) {

			if(isset( $children_elements[$id])) {
				foreach ( $children_elements[ $id ] as $child ){
	
					if ( !isset($newlevel) ) {
						$newlevel = true;
						//start the child delimiter
						$cb_args = array_merge( array(&$output, $depth), $args);
						call_user_func_array(array($this, 'start_lvl'), $cb_args);
					}
					$this->display_element( $child, $children_elements, $max_depth, $depth + 1, $args, $output );
				}
				unset( $children_elements[ $id ] );
			}else{
				$enable_empty_sublevel = $this->slickmenu->get_menu_item_option('level-show-empty', $id );
				if(!empty($enable_empty_sublevel)) {
					if ( !isset($newlevel) ) {
						$newlevel = true;
						//start the child delimiter
						$cb_args = array_merge( array(&$output, $depth), $args);
						call_user_func_array(array($this, 'start_lvl'), $cb_args);
					}	
				}
			}	
		}

		if ( isset($newlevel) && $newlevel ){
			//end the child delimiter
			$cb_args = array_merge( array(&$output, $depth), $args);
			call_user_func_array(array($this, 'end_lvl'), $cb_args);
		}

		//end this element
		$cb_args = array_merge( array(&$output, $element, $depth), $args);
		call_user_func_array(array($this, 'end_el'), $cb_args);
	}	
}