<?php

if ( ! defined( 'ABSPATH' ) ) exit;

class Slick_Menu_Output {

 	/**
	 * The single instance of Slick_Menu_Output.
	 * @var 	object
	 * @access  private
	 * @since 	1.0.0
	 */
	private static $_instance = null;

	/**
	 * The main plugin object.
	 * @var 	object
	 * @access  public
	 * @since 	1.0.0
	 */
	public $parent = null;
	public $fragments = array();
 	
	public function __construct ( $parent ) {

		$this->parent = &$parent;
		$this->parent->output = &$this;
		
		add_action( 'wp_footer', array($this, 'append_menu_to_body' ));
		add_action( 'template_redirect', array( $this, 'ajax_build_menu' ), PHP_INT_MAX );
		add_filter( 'slick_menu_dynamic_fragment', array($this, 'process_shortcode') );
	}	

	function append_menu_to_body() {
		
		$slikmenus = $this->parent->get_menus();
		
		foreach($slikmenus as $menu) {
			
			$this->build_menu($menu->term_id);
		}
		
		$this->build_triggers();
		
		wp_localize_script( $this->parent->plugin_slug('frontend'), 'SM_VARS', array(
			'ajaxurl' => admin_url('admin-ajax.php'),
			'sm_ajaxurl' => slick_menu_get_ajax_link('build_menu'),
			'assets_url' => $this->parent->assets_url,
			'options' => $this->parent->global_options,
			'filterMenuOptions' => $this->parent->filterMenuOptions,
			'settings' =>  $this->parent->get_settings(),
			'debug' => $this->parent->debug,
			'sm_debug' => $this->parent->sm_debug
		));
		
		wp_enqueue_script( $this->parent->plugin_slug('frontend') );

	}

    function menu_excluded_from_page($menu_id) {

        $excluded_pages = $this->parent->get_menu_option('exclude-pages', $menu_id);

        if(empty($excluded_pages)) {
            return false;
        }

        $current_page_id = slick_menu_get_post_id();

        return in_array($current_page_id, $excluded_pages);
    }

    function ajax_build_menu() {
		
		$sm_ajax = slick_menu_is_ajax_action('build_menu');
		
		if(!$sm_ajax) {
			return false;
		}

		$menu_id = intval($_REQUEST['id']);
		$force = (bool) !empty($_REQUEST['force']) ? $_REQUEST['force'] : '0';

		$menu = "";
		$options = array();
		
		if(is_nav_menu($menu_id)) {
			
        	$menu = $this->build_menu($menu_id, true, $force);

        	if(!empty($menu)) {
                $options = $this->parent->get_all_menu_options($menu_id);

                $options["menu-trigger-selector"] = $this->parent->get_trigger_selector($menu_id);
                $options["real-mobile-breakpoint"] = $this->parent->get_menu_mobile_breakpoint($menu_id);
            }
		}
			
        $return = array( 
        	'error' => '', 
        	'menu' => $menu,
        	'menu_id' => $menu_id,
        	'options' => $options
        );
	  
	    die(json_encode($return));
	}

	function build_menu($menu_id, $return = false, $force = false) {

		if(!empty($menu_id) && !$this->menu_excluded_from_page($menu_id)) {

            $item_id = null;
            $options = $this->parent->get_all_menu_options($menu_id);
            $settings = $this->parent->get_settings();

            $ajax = (bool)$this->parent->get_menu_option('menu-ajax', $menu_id);
            $alwaysVisible = (bool)$this->parent->get_menu_option('menu-always-visible', $menu_id);
            $menuPosition = $this->parent->get_menu_option('menu-position', $menu_id);
            $levelAnimation = $this->parent->get_menu_option('level-animation-type', $menu_id);
            $menuAnimation = $this->parent->get_menu_option('menu-animation-type', $menu_id);

            // Classes Prefix
					
			$prefix = 'sm-';

			$classes = array();
			$level_classes = array('sm-main-level', 'sm-level');
			$header_classes = array('sm-header', 'sm-level-component');
			$title_classes = array('sm-title', 'sm-level-component');
			$title_overlap_classes = array('sm-title-overlap');
			$title_wrap_classes = array('sm-title-wrap');
			$subtitle_classes = array('sm-subtitle', 'sm-level-component');
			$subtitle_wrap_classes = array('sm-subtitle-wrap');
			$description_classes = array('sm-description', 'sm-level-component');
			$description_wrap_classes = array('sm-description-wrap');			
			$close_classes = array('sm-close', 'sm-level-component');
			$nav_classes = array('sm-nav-list');
			$footer_classes = array('sm-footer', 'sm-level-component');
			$footer_text_classes = array('sm-footer-text');

			// Menu Classes
			$classes[] = $prefix.'menu';
			$classes[] = $prefix.'menu-'.$menu_id;
			$classes[] = $prefix.$menuPosition;
			$classes[] = $prefix.$levelAnimation;

			
			if($alwaysVisible) {
				$classes[] = $prefix.'always-visible';
			}else{
				$classes[] = $menuAnimation;
			}
						

			if($alwaysVisible || in_array($menuAnimation, slick_menu_data_get_effects('push', true))) {
				$classes[] = 'sm-push';
			}

			if($ajax && !defined( 'DOING_AJAX' )) {
				
				$classes[] = $prefix.'ajax';
				$classes = slick_menu_class_string($classes);

				$menu = '<script type="text/template" id="sm-menu-'.esc_attr($menu_id).'" class="'.esc_attr($classes).'" data-id="'.esc_attr($menu_id).'"></script>';
				
			}else{
				
				$cache_key = apply_filters('slick_menu_output_cache_key', 'menu-output-'.$menu_id, $menu_id);
				
				$menu = $this->parent->pcache->get($cache_key);
	
				if ( false === $menu || $force) {

					// Get Nav Menu / Options
					
					$nav_menu = wp_get_nav_menu_object( $menu_id );
					
					$level_data['level'] = 1;
					
					// Add Menu Classes
					$classes[] = $prefix.'navmenu-'.$nav_menu->slug;
					

					// WRAPPER BG	
					$wrapper_bgimage = !empty($options['wrapper-bg']) ? $options['wrapper-bg'] : false;
					$wrapper_bgpattern = !empty($options['wrapper-pattern']) ? $options['wrapper-pattern'] : false;
					$wrapper_bgoverlay = !empty($options['wrapper-overlay']) ? $options['wrapper-overlay'] : false;
					$wrapper_bgvideo = !empty($options['wrapper-video']) ? $options['wrapper-video'] : false;
					$wrapper_bgfilter = !empty($options['wrapper-filter']) ? $options['wrapper-filter'] : false;
					
					if(empty($wrapper_bgimage['image_url']) && empty($wrapper_bgimage['color'])) {
						$wrapper_bgimage = !empty($settings['wrapper-bg']) ? $settings['wrapper-bg'] : false;
					}
		
					if(!empty($wrapper_bgimage['image_url']) || !empty($wrapper_bgimage['color'])) {
						
						$level_classes[] = 'sm-has-wrapper-bg';
					
						$level_data['wrapper-image'] = set_url_scheme(slick_menu_array_value($wrapper_bgimage, 'image_url'));
						$level_data['wrapper-color'] = slick_menu_array_value($wrapper_bgimage, 'color');
						$level_data['wrapper-repeat'] = slick_menu_array_value($wrapper_bgimage, 'repeat');
						$level_data['wrapper-size'] = slick_menu_array_value($wrapper_bgimage, 'size');
						$level_data['wrapper-position'] = slick_menu_array_value($wrapper_bgimage, 'position');
					}
					
					
					if(empty($wrapper_bgpattern['pattern'])) {
						$wrapper_bgpattern = !empty($settings['wrapper-pattern']) ? $settings['wrapper-pattern'] : false;
					}
					if(!empty($wrapper_bgpattern['pattern'])) {
						
						$level_classes[] = 'sm-has-wrapper-bg';
						
						$level_data['pattern-image'] = slick_menu_array_value($wrapper_bgpattern, 'pattern');
						$level_data['pattern-opacity'] = slick_menu_array_value($wrapper_bgpattern, 'opacity');
							
					}
					
					
					if(empty($wrapper_bgoverlay)) {
						$wrapper_bgoverlay = !empty($settings['wrapper-overlay']) ? $settings['wrapper-overlay'] : false;
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
					
					
					if(empty($wrapper_bgfilter)) {
						$wrapper_bgfilter = !empty($settings['wrapper-filter']) ? $settings['wrapper-filter'] : false;
					}
					if(!empty($wrapper_bgfilter)) {
						
						$level_classes[] = 'sm-has-wrapper-bg';
						
						$level_data['wrapper-filter'] = $wrapper_bgfilter;
					}
					
					// CONTENT FILTER
					$level_data['content-filter'] = !empty($options['content-filter']) ? $options['content-filter'] : '';
							
					
					// GENERAL
					$width = str_replace('%', 'perc', $this->parent->get_menu_option('level-width', $menu_id));
					$level_data['width'] = $width;
					
					
					$level_scroll_disabled = (bool)$this->parent->get_menu_option('level-disabled-scroll', $menu_id );
					if($level_scroll_disabled) {
						$level_classes[] = 'sm-no-scroll';
					}
					
					$level_show_scrollbar = (bool)$this->parent->get_menu_option('level-show-scrollbar', $menu_id );
					if($level_show_scrollbar) {
						$level_classes[] = 'sm-show-scrollbar';
					}
					
					$level_scroll_to_current = (bool)$this->parent->get_menu_option('level-scroll-to-current', $menu_id );
					$level_data['scroll-to-current'] = $level_scroll_to_current;
				
					
					$level_mobile_centered = (bool)$this->parent->get_menu_option('level-mobile-centered', $menu_id );
					if(!empty($level_mobile_centered)) {
						$level_classes[] = 'sm-mobile-centered';
					}
	

					// HEADER
					
					$header_stick_top = (bool)$this->parent->get_menu_option('level-header-stick-top', $menu_id );
					
					if($header_stick_top) {
						$header_over_content = (bool)$this->parent->get_menu_option('level-header-over-content', $menu_id );
						if($header_over_content) {
							$header_classes[] = 'sm-header-over-content';
						}
					}
	
					
					// LOGO
					$logo_use_avatar = $this->parent->get_menu_option('logo-use-avatar', $menu_id );
					
					if ( $logo_use_avatar ) {
						
						if(is_user_logged_in()) {
				            $current_user = wp_get_current_user();
				
					        if ( ($current_user instanceof WP_User) ) {
						        
						        $logo_width = $this->parent->get_menu_option('logo-width', $menu_id );
						        $logo_width = intval($logo_width);
								$logo_width = !empty($logo_width) ? $logo_width : 90;
			        
					            $logo_avatar = get_avatar( $current_user->user_email, $logo_width );
					            
								$logo_url = get_the_author_meta('user_url', $current_user->ID);
								
								if(empty($logo_url)) {
						            $logo_url = esc_url( get_author_posts_url( $current_user->ID, $current_user->user_nicename ) );
						        }
					        }
				        }
				        
			        }else{
				        
				        $logo = $this->parent->get_menu_option('logo', $menu_id );
						$logo_url = $this->parent->get_menu_option('logo-url', $menu_id );
			        }
			        
					
					// BG VIDEO
					$bgvideo = $this->parent->get_menu_option('level-video', $menu_id );
					if(!empty($bgvideo['id'])) {
						$level_classes[] = 'sm-has-level-video';
					}
					
					
					// CLOSE LINK
					$close_link_hidden = $this->parent->get_menu_option('close-link-hidden', $menu_id );
					
					if(empty($close_link_hidden)) {
						
						$close_link_icon = SM_Icons_Front_End::get_icon( 
							$this->parent->mb_field_id('close-link-icon'), 
							$menu_id
						);
						
						if(!empty($options['close-link-position'])) {
							$close_link_position = $options['close-link-position'];
							$close_classes[] = $prefix.'position-'.$close_link_position;
						}
						
						$close_animation_data = '';
						if(!empty($options['close-link-animation'])) {							
							$close_animation_class = $options['close-link-animation'];
							if(!empty($close_animation_class)) {
								$close_animation_data = ' class="sm-animated" data-animation="sm-'.esc_attr($close_animation_class).'"';
							}
						}			
					}
					
		
					// GLOBAL TITLE ICON
					
					$show_title_icon =  (bool)$this->parent->get_menu_option('title-show-icon', $menu_id );
					
					$global_icon = SM_Icons_Front_End::get_icon( 
						$this->parent->mb_field_id('title-main-icon'), 
						$menu_id 
					);
				

					
					// TITLE

					$title_hidden = (bool)$this->parent->get_menu_option('title-hidden', $menu_id );
					if($title_hidden) {
						$title_classes[] = 'sm-title-hidden';
					}
					
					$title_stick_top = $this->parent->get_menu_option('title-stick-top', $menu_id);
					if(!empty($title_stick_top)) {
						$title_classes[] = 'sm-title-top';
					}
					
					$title_fullwidth = $this->parent->get_menu_option('title-fullwidth', $menu_id);
					if(!empty($title_fullwidth)) {
						$title_classes[] = 'sm-fullwidth';
					}
					
					$title_animation_data = '';
					if(!empty($options['title-animation'])) {
						$title_animation_data = ' data-animation="sm-'.esc_attr($options['title-animation']).'"';
						$title_wrap_classes[] = 'sm-animated';
					}
					
					$title_override = $this->parent->get_menu_option('title-override', $menu_id );
					
							
					
					// SUB TITLE
					$subtitle_enabled = (bool)$this->parent->get_menu_option('subtitle-enabled', $menu_id );
					
					if($subtitle_enabled) {
						$subtitle = $this->parent->get_menu_option('subtitle-text', $menu_id );
						if(!empty($subtitle)) {
							$subtitle_animation_data = '';
							if(!empty($options['subtitle-animation'])) {
								$subtitle_animation_data = ' data-animation="sm-'.esc_attr($options['subtitle-animation']).'"';
								$subtitle_wrap_classes[] = 'sm-animated';
							}
						}
					}
					
					// DESCRIPTION
					$description_enabled = (bool)$this->parent->get_menu_option('description-enabled', $menu_id );
					
					if($description_enabled) {
						
						$description_from_page = (bool)$this->parent->get_menu_option('description-from-page', $menu_id );
						if($description_from_page) {
							$description_page = intval($this->parent->get_menu_option('description-page', $menu_id ));
							if(!empty($description_page)) {
								$description = get_post_field('post_content', $description_page);
							}
						}else{
							$description = $this->parent->get_menu_option('description-text', $menu_id );
						}
						
						if(!empty($description)) {
							
							$description = $this->get_dynamic_fragment('description', $description, $menu_id);
							
							$description_animation_data = '';
							$description_animation_class = $this->parent->get_menu_option('description-animation', $menu_id);
							
							if(!empty($description_animation_class)) {
								$description_animation_data = ' data-animation="sm-'.esc_attr($description_animation_class).'"';
								$description_wrap_classes[] = 'sm-animated';
							}
						}
					}
					
					
					// NAV ITEMS
					$nav_hidden = $this->parent->get_menu_option('menu-items-hidden', $menu_id);
					if(!empty($nav_hidden)) {
						$nav_classes[] = 'sm-nav-items-hidden';
					}
					
					$nav_align = $this->parent->get_menu_option('level-menu-align', $menu_id);
				    
				    if(!empty($nav_align)) {
						$nav_classes[] = 'sm-nav-align-'.$nav_align;
				    }	
				    						    
				    $nav_column_align = $this->parent->get_menu_option('level-menu-column-align', $menu_id);
				    
				    if(!empty($nav_column_align)) {
						$nav_classes[] = 'sm-col-align-'.$nav_column_align;
				    }

					
					// FOOTER
					$footer_text = '';
					if(!empty($options['footer-text'])) {
						$footer_text = $options['footer-text'];
					}
					
					$footer_animation_data = '';
					$footer_animation_class = $this->parent->get_menu_option('footer-animation', $menu_id);
					
					if(!empty($footer_animation_class)) {
						$footer_animation_data = ' data-animation="sm-'.esc_attr($footer_animation_class).'"';
						$footer_text_classes[] = 'sm-animated';
					}
		
					$footer_stick_bottom = (bool)$this->parent->get_menu_option('level-footer-stick-bottom', $menu_id );
					
					if($footer_stick_bottom) {
						$footer_over_content = (bool)$this->parent->get_menu_option('level-footer-over-content', $menu_id );
						if($footer_over_content) {
							$footer_classes[] = 'sm-footer-over-content';
						}
					}
					


					// FILTER CLASSES
					$classes = apply_filters('slick_menu_classes', $classes, $menu_id);
					$level_classes = apply_filters('slick_menu_level_classes', $level_classes, $menu_id);
					
					// PROCESS CLASSES
					
					$classes = slick_menu_class_string($classes);
					$level_classes = slick_menu_class_string($level_classes);
					$header_classes = slick_menu_class_string($header_classes);
					$title_classes = slick_menu_class_string($title_classes);
					$title_overlap_classes = slick_menu_class_string($title_overlap_classes);
					$title_wrap_classes = slick_menu_class_string($title_wrap_classes);
					$subtitle_classes = slick_menu_class_string($subtitle_classes);
					$subtitle_wrap_classes = slick_menu_class_string($subtitle_wrap_classes);
					$description_classes = slick_menu_class_string($description_classes);
					$description_wrap_classes = slick_menu_class_string($description_wrap_classes);
					$close_classes = slick_menu_class_string($close_classes);
					$nav_classes = slick_menu_class_string($nav_classes);
					$footer_classes = slick_menu_class_string($footer_classes);
					$footer_text_classes = slick_menu_class_string($footer_text_classes);

					// FILTER DATA
					$level_data = apply_filters('slick_menu_level_data', $level_data, $menu_id);
										
					// PROCESS DATA
					$level_data = slick_menu_data_string($level_data);
		
					// EXPORT LOCAL VARS
					$vars = get_defined_vars();
					
					
					// SM LEVEL
					$output =  '<div class="'.esc_attr($level_classes).'" '.$level_data.'>';
			
						// SM LEVEL OVERLAP BORDER
						$output .= slick_menu_include_part('overlap-border', $vars);
						
					 
						// SM LEVEL BG VIDEO
						$output .= slick_menu_include_part('video-bg', $vars);
						

						// SM LEVEL INNER
						$output .= '	<div class="sm-level-inner">';


							// SM CLOSE LINK
							$output .= slick_menu_include_part('close', $vars);
							
							
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
									
							
									// SM TITLE
									$output .= slick_menu_include_part('title', $vars);
									
									// SM DESCRIPTION
									$output .= slick_menu_include_part('description', $vars);
		
									
									// SM NAV
									$output .= slick_menu_include_part('nav-begin', $vars);
									$output .= '%3$s';
									$output .= slick_menu_include_part('nav-end', $vars);
									
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
					
					
					// Nav Menu
									
					$args = array();
					$args['menu'] = $menu_id;
					$args['sm_menu'] = true;
					$args['sm_options'] = $options;
					$args['items_wrap'] = $output;
					$args['container'] = false;
					$args['echo'] = false;	
					$args['walker'] = new Slick_Menu_Walker();
					$args['fallback_cb'] = '__return_false';
					
					add_filter( 'wp_nav_menu_args', array($this, 'filter_menu_args' ), PHP_INT_MAX);
	
					$menu = wp_nav_menu($args);	
					if(empty($menu)) {
						$menu = $output;
					}
					$menu = '<nav id="sm-menu-'.esc_attr($menu_id).'" class="'.esc_attr($classes).'" data-id="'.esc_attr($menu_id).'">'.$menu.'</nav>';
	
					$this->parent->pcache->set($cache_key, $menu);
					
					if(!$this->parent->sm_debug) {
						$this->save_dynamic_fragments($menu_id);
					}
				
				}
				
				if(!$this->parent->sm_debug) {
					$this->process_dynamic_fragments($menu_id, $menu);
				}	
			}

			$options["menu-trigger-selector"] = $this->parent->get_trigger_selector($menu_id);
			$options["real-mobile-breakpoint"] = $this->parent->get_menu_mobile_breakpoint($menu_id);
			
			$this->parent->global_options[$menu_id] = $options;
			
			if($return) {
				return $menu;
			}
			
			echo $menu;
			
		}else {

            return null;
        }
	}
	
	public function filter_menu_args($args) {
		
		if( true === $args['sm_menu'] ) {
			
			if(empty($args["walker"]) || (!empty($args["walker"]) && !($args["walker"] instanceOf Slick_Menu_Walker) ) ) {
				$args['walker'] = new Slick_Menu_Walker();
			}	
		}	
		
		return $args;
	}

	public function build_triggers() {
		
		$this->build_trigger('hamburger-top-left', 'left');
		$this->build_trigger('hamburger-top-right', 'right');
	}
	
	public function build_trigger($trigger_id, $position) {

		$trigger_html = $this->parent->pcache->get('hamburger-trigger-'.$trigger_id);
		
		if ( false === $trigger_html ) {
						
			$trigger = $this->parent->get_setting($trigger_id);
		
			if(empty($trigger["menu"]) || empty($trigger["hamburger"])) {
				return false;
			}
			
			$menu_id = $trigger['menu'];

			if($this->menu_excluded_from_page($menu_id)) {
			    return false;
            }

			if($this->parent->lang->enabled()) {
				$menu_id = $this->parent->lang->get_translated_term_id($menu_id, 'nav_menu');
			}
			
			$reverse = !empty($trigger['reverse']) ? true : false;
			$hamburger = $trigger['hamburger'];
			$visibility= !empty($trigger['visibility']) ? 'sm-'.$trigger['visibility'] : '';
			$hamburger = $reverse ? $hamburger.'-r' : $hamburger;
			$position = 'sm-position-'.$position;
			$trigger_class = $this->parent->get_trigger_class($menu_id);
		
			$template = '<div class="sm-hamburger %s %s %s %s" data-id="%s">
		        <div class="sm-hamburger-box">
		          <div class="sm-hamburger-inner"></div>
		        </div>
		      </div>';


			$trigger_html = sprintf( 
				$template, 
				esc_attr( $hamburger ),  
				esc_attr( $position ), 
				esc_attr( $trigger_class ),
				esc_attr( $visibility ),
				esc_attr( $menu_id )
			);
			
			$this->parent->pcache->set('hamburger-trigger-'.$trigger_id, $trigger_html);
		}	
		
		echo $trigger_html;
	}
		
	public function get_dynamic_fragment($id, $content, $menu_id, $item_id = null) {
		
		if($this->parent->sm_debug) {
			return $content;
		}
		
		$fragment_id = strtoupper($id).'_'.$menu_id;
		if(!empty($item_id)) {
			$fragment_id .= '_'.$item_id;
		}
		
		$placeholder = '<!-- SM_FRAGMENT_'.$fragment_id.' -->';
	
		$this->fragments[$menu_id][] = array(
			'id' => $fragment_id,
			'content' => $content
		);
	
		return $placeholder;
	}
	
	public function save_dynamic_fragments($menu_id) {
		
		if(!empty($this->fragments[$menu_id])) {
			$this->parent->pcache->set('menu-fragments-'.$menu_id, $this->fragments[$menu_id]);
		}
	}
	
	public function get_dynamic_fragments($menu_id) {
		
		return $this->parent->pcache->get('menu-fragments-'.$menu_id);
	}
	
	public function process_dynamic_fragments($menu_id, &$menu) {
		
		$fragments = $this->get_dynamic_fragments($menu_id);
	
		if(empty($fragments)) {
			return false;
		}
		
		$find = array();	
		$replace = array();
		
		foreach($fragments as $fragment) {
		
			$fragment_id = $fragment['id'];
			$content = $fragment['content'];
			
			$content = apply_filters('slick_menu_dynamic_fragment', $content, $menu_id);
					
			$find[] = '/\<\!\-\- SM_FRAGMENT_'.$fragment_id.' \-\-\>/';
			$replace[] = $content;
		}

		$menu = preg_replace($find, $replace, $menu);
	}
	
	public function has_any_shortcode(&$content) {
		
		preg_match('/\[.*?\]/', $content, $match);
		return !empty($match);
	}


	function process_shortcode($content) {
		
		if($this->has_any_shortcode($content)) {
		
			/** @var \WP_Embed $wp_embed */
			global $wp_embed;
			
			if(!empty($wp_embed)) {

				// [embed] shortcode
				$wp_embed->post_ID = 1;
				$content = $wp_embed->run_shortcode( $content );
			}

			$content = trim( do_shortcode( shortcode_unautop( $content ) ) );
			
		}
		return $content;
	}


	/**
	 * Slick_Menu_Output Instance
	 *
	 * Ensures only one instance of Slick_Menu_Output is loaded or can be loaded.
	 *
	 * @since 1.0.0
	 * @static
	 * @see Slick_Menu()
	 * @return Slick_Menu_Output instance
	 */
	public static function instance ( $parent ) {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self( $parent );
		}
		return self::$_instance;
	} // End instance()

	/**
	 * Cloning is forbidden.
	 *
	 * @since 1.0.0
	 */
	public function __clone () {
		_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?' ), $this->parent->plugin_version() );
	} // End __clone()

	/**
	 * Unserializing instances of this class is forbidden.
	 *
	 * @since 1.0.0
	 */
	public function __wakeup () {
		_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?' ), $this->parent->plugin_version() );
	} // End __wakeup()	 	
}
