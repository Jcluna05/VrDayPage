<?php

if ( ! defined( 'ABSPATH' ) ) exit;

require_once "lib/scss/scss.inc.php";

class Slick_Menu_Styles_Output {

 	/**
	 * The single instance of Slick_Menu_Styles_Output.
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
		
	protected $global_fonts = array();
	protected $global_fonts_imports = '';
	protected $global_css = '';
	protected $minify = false;

	public function __construct ( $parent ) {

		$this->parent = &$parent;
		$this->parent->styles = &$this;
		$this->use_cache = empty($_GET['nocache']) ? true : false;
		$this->minify = (defined( 'SM_SCRIPT_DEBUG' ) && SM_SCRIPT_DEBUG) ? false : true;
	}			

	public function renderGlobal($return = false, $die = true) {
		
		if(!$this->parent->debug) {
			ob_start();
		}	
		
		$this->resetVars();
		
		// No cache, so regenerate the styles and save the transient
		 
		if ( !$this->use_cache || false === ( $dynamicStyles = $this->parent->pcache->get('global-styles') ) ) {
			
			// GLOBAL STYLES
			
			$this->set_global_styles();
			$this->set_hamburger_styles();

			$dynamicStyles = $this->getStyles();
			
		    $this->parent->pcache->set('global-styles', $dynamicStyles);
	    }

		if(!$this->parent->debug) {
			ob_end_clean();
		}

		if($return) {
			return $dynamicStyles;
		}

		echo $dynamicStyles;
		
		if($die) {
			die();
		}
	}

	public function renderHamburgers($return = false, $die = true) {
		
		if(!$this->parent->debug) {
			ob_start();
		}	
		
		$this->resetVars();
		
		// No cache, so regenerate the styles and save the transient
		 
		if ( !$this->use_cache || false === ( $dynamicStyles = $this->parent->pcache->get('hamburger-styles') ) ) {
			
			// GLOBAL STYLES
			
			$this->set_hamburger_styles();

			$dynamicStyles = $this->getStyles();
			
		    $this->parent->pcache->set('hamburger-styles', $dynamicStyles);
	    }

		if(!$this->parent->debug) {
			ob_end_clean();
		}

		if($return) {
			return $dynamicStyles;
		}

		echo $dynamicStyles;
		
		if($die) {
			die();
		}
	}
			
	public function renderAll($return = false, $die = true) {
		
		if(!$this->parent->debug) {
			ob_start();
		}	
		
		$this->resetVars();
		
		// No cache, so regenerate the styles and save the transient
		 
		if ( !$this->use_cache || false === ( $dynamicStyles = $this->parent->pcache->get('styles') ) ) {
			
			// GLOBAL STYLES
			
			$this->set_global_styles();
			$this->set_hamburger_styles();
			
			
			// INDIVIDUAL MENU STYLES
			
			$menus = $this->parent->get_menus();
	
			foreach($menus as $menu) {
			
				$this->set_menu_styles($menu->term_id);
			}
				
			$dynamicStyles = $this->getStyles(true);

		    $this->parent->pcache->set('styles', $dynamicStyles);
	    }

		if(!$this->parent->debug) {
			ob_end_clean();
		}

		if($return) {
			return $dynamicStyles;
		}

		echo $dynamicStyles;
		
		if($die) {
			die();
		}
	}
	
	public function render($menu_id, $return = false, $die = false) {
		
		if(!$this->parent->debug) {
			ob_start();
		}	
		
		$this->resetVars();
		
		// No cache, so regenerate the styles and save the transient
		 
		if ( !$this->use_cache || false === ( $dynamicStyles = $this->parent->pcache->get('styles-'.$menu_id) ) ) {
			
			$this->set_menu_styles($menu_id);
			
			$dynamicStyles = $this->getStyles();
			
		    $this->parent->pcache->set('styles-'.$menu_id, $dynamicStyles);
	    }
	
		if(!$this->parent->debug) {
			ob_end_clean();
		}
	
		if($return) {
			return $dynamicStyles;
		}
		
		echo $dynamicStyles;
		
		if($die) {
			die();
		}
	}
	
	
	public function set_global_styles() {
		
		$styles = new Slick_Menu_Styles('settings');
		
                		
		// WRAPPER BG		
/*
		$styles->setBackground("{bselector} .sm-wrapper-bg-2", "wrapper-bg");
		$styles->setPattern("{bselector} .sm-wrapper-bg-2 .sm-wrapper-pattern", "wrapper-pattern");
		$styles->setOverlay("{bselector} .sm-wrapper-bg-2 .sm-wrapper-overlay", "wrapper-overlay");
*/
		
		// CONTENT BG
		$styles->setBackground("{cselector}", "content-bg");
	
		do_action('slick_menu_global_styles_output', $styles);
		
		$this->global_css .= $styles->render(true, false);
		$this->global_fonts = $styles->getCurrentFonts();
		$this->global_fonts_imports .= $styles->renderGoogleFonts(true);
	}
	
	
	public function set_hamburger_styles() {
		
		$styles = new Slick_Menu_Styles('settings');
		
		$scss = slickmenu_scss_compiler();
		$scss->setImportPaths($this->parent->assets_dir."/vendors/hamburgers/scss");

		// Compile Hamburger Base
		$compile = array();
		$compile[] = '@import "mixins"';
		$compile[] = '@import "base"';
		$compile = implode(";", $compile);
		$hamburger_base_css = $scss->compile($compile);
			
				
		$active = 0;
		$hamburger_dynamic_css = '';
		foreach(array('left', 'right') as $position) {
			
			$compile = array();
			$compile[] = '@import "mixins"';

			$option_key = "hamburger-top-$position";
			if(!empty($_POST)) {
				
				$option_key = $prefix = $this->parent->mb_field_id($option_key);
				if(!empty($_POST[$option_key])) {
					$value = $_POST[$option_key];
				}
				
			}else{
				$value = $styles->get_option($option_key);
			}	

			if(empty($value['menu']) || empty($value['hamburger'])) {
				continue;
			}

			
			$menu = $value['menu'];
			$hamburger = str_replace('sm-hamburger--', '', $value['hamburger']);
			$reverse = !empty($value['reverse']) ? true : false;
			if($reverse) {
				$hamburger = $hamburger.'-r';
			}

			$compile[] = '$hamburger-position	: '.$position.' !default';
			
			
			$label = !empty($value['label']) ? true : false;
			if(!empty($label)) {
				$label = $label ? 'true' : 'false';
				$compile[] = '$hamburger-label: '.$label.' !default';
			}
			if(!empty($value['label-text'])) {
				$label_text = $value['label-text'];
				$compile[] = '$hamburger-label-text: "'.$label_text.'" !default';
			}
			if(isset($value['label-position'])) {
				$label_position = $styles->format($value['label-position'], "unit");
				$compile[] = '$hamburger-label-position: '.$label_position.' !default';
			}
			if(isset($value['label-distance'])) {
				$label_distance = $styles->format($value['label-distance'], "unit");
				$compile[] = '$hamburger-label-distance: '.$label_distance.' !default';
			}
			if(isset($value['label-size'])) {
				$label_size = $styles->format($value['label-size'], "unit");
				$compile[] = '$hamburger-label-size: '.$label_size.' !default';
			}
						
			if(isset($value['margin-x'])) {
				$margin_x = $styles->format($value['margin-x'], "unit");
				$compile[] = '$hamburger-margin-x: '.$margin_x.' !default';
			}
			
			if(isset($value['margin-y'])) {
				$margin_y = $styles->format($value['margin-y'], "unit");
				$compile[] = '$hamburger-margin-y: '.$margin_y.' !default';
			}
			
			if(isset($value['hover-opacity'])) {
				$hover_opacity = floatval($value['hover-opacity']);
				$compile[] = '$hamburger-hover-opacity: '.$hover_opacity.' !default';
			}
			
			if(isset($value['bar-width'])) {
				$bar_width = $styles->format($value['bar-width'], "unit");
				$compile[] = '$hamburger-layer-width: '.$bar_width.' !default';
			}
			
			if(isset($value['bar-height'])) {
				$bar_height = $styles->format($value['bar-height'], "unit");
				$compile[] = '$hamburger-layer-height: '.$bar_height.' !default';
			}
			
			if(isset($value['bar-spacing'])) {
				$bar_spacing = $styles->format($value['bar-spacing'], "unit");
				$compile[] = '$hamburger-layer-spacing: '.$bar_spacing.' !default';
			}
			
			if(isset($value['bar-radius'])) {
				$bar_radius = $styles->format($value['bar-radius'], "unit");
				$compile[] = '$hamburger-layer-border-radius: '.$bar_radius.' !default';
			}	
					
			if(!empty($value['color'])) {
				$color = $value['color'];
				$compile[] = '$hamburger-layer-color: '.$color.' !default';
			}
			
			if(!empty($value['hover-color'])) {
				$hover_color = $value['hover-color'];
				$compile[] = '$hamburger-layer-hover-color: '.$hover_color.' !default';
			}
			
			if(!empty($value['active-color'])) {
				$active_color = $value['active-color'];
				$compile[] = '$hamburger-layer-active-color : '.$active_color.' !default';
			}

			$compile[] = '@import "vars"';
			$compile[] = '@import "dynamic"';
			$compile[] = '@import "types/boring"';
			$compile[] = '@import "types/'.$hamburger.'"';
			
			$compile = implode(";", $compile);
			$hamburger_dynamic_css .= $scss->compile($compile);
			
			if($this->minify) {
				$styles->minify($hamburger_base_css);
				$styles->minify($hamburger_dynamic_css);
			}

			$active++;
		}

		if($active > 0) {
			$this->global_css .= $hamburger_base_css;
			$this->global_css .= $hamburger_dynamic_css;
		}
		
		
		do_action('slick_menu_hamburger_styles_output', $styles);
		
		$this->global_css .= $styles->render(true, false);
		$this->global_fonts = $styles->getCurrentFonts();
		$this->global_fonts_imports .= $styles->renderGoogleFonts(true);		
	}
	
	public function set_menu_styles($menu_id) {
	
		$styles = new Slick_Menu_Styles('options', $menu_id, null, $this->global_fonts);
		
		$breakpoint = $this->parent->get_menu_mobile_breakpoint($menu_id, true);
		$styles->setMediaBreakpoint('mobile', $breakpoint);

		$menuPosition = $styles->get_option('menu-position');
		$alwaysVisible = $styles->get_option('menu-always-visible');
		$menuAnimation = $styles->get_option('menu-animation-type');
		$levelAnimation = $styles->get_option('level-animation-type');
		$levelWidth = $styles->get_option('level-width', 'level-width');
		$mobileCentered = $styles->get_option('level-mobile-centered');

		
		// MENU SETTINGS
		if(!empty($alwaysVisible)) {

	    	$styles->setUseOptions(false);
	    	
			$styles->set("===html.sm-always-visible:not(.sm-av-bp-mobile) .sm-body.sm-menu-$menu_id .sm-hamburger[data-id='$menu_id']", "display", "none");
			
			//$styles->set("{bmselector} .rev_slider_wrapper.fullwidthbanner-container,{bmselector} .rev_slider_wrapper.fullscreen-container", "width", "calc(100% - $levelWidth)", null, true);
			
			if($menuPosition == 'left') {
				$styles->set("{bmselector} .sm-content-inner", "margin-left", $levelWidth);
				//$styles->set("{bmselector} .rev_slider_wrapper.fullwidthbanner-container,{bmselector} .rev_slider_wrapper.fullscreen-container", "margin-left", $levelWidth, null, true);
			}else{
				$styles->set("{bmselector} .sm-content-inner", "margin-right", $levelWidth);
				//$styles->set("{bmselector} .rev_slider_wrapper.fullwidthbanner-container,{bmselector} .rev_slider_wrapper.fullscreen-container", "margin-right", $levelWidth, null, true);
			}
			
			// RESET INNER CONTENT ON MOBILE BREAKPOINT
			$styles->setMedia('mobile');
			
			$styles->set("{bavmselector} {selector}", "z-index", "10000");
			$styles->set("{bavmselector} .sm-content-inner", "margin", "inherit", null, true);
			//$styles->set("{bmselector} .rev_slider_wrapper.fullwidthbanner-container,{bmselector} .rev_slider_wrapper.fullscreen-container", "margin", "inherit", null, true);

			$styles->resetMedia();
			//
			
			$styles->setUseOptions(true);	

		}


		if($levelAnimation == 'cover') {

			$styles->setBoxShadow("{bamselector} {selector} .sm-level:not(.sm-level-overlay)", "menu-shadow");
			
		}else if($levelAnimation == 'overlap') {
				
			$styles->setBoxShadow("{bamselector} {selector} .sm-main-level:not(.sm-level-overlay)", "menu-shadow");
			$styles->setBoxShadow("{bamselector} {selector} .sm-level-overlay > .sm-level-overlap", "menu-shadow");
		}
		
			
		// WRAPPER BACKGROUND
		
		$styles->setBackground("{bamselector} .sm-wrapper-bg-2", "wrapper-bg");
		$styles->setPattern("{bamselector} .sm-wrapper-bg-2 .sm-wrapper-pattern", "wrapper-pattern");
		$styles->setOverlay("{bamselector} .sm-wrapper-bg-2 .sm-wrapper-overlay", "wrapper-overlay");

		$wrapperVideo = $styles->get_option('wrapper-video');
		
		if(!empty($wrapperVideo['id'])) {
			$styles->set("{bamselector} .sm-wrapper-video.sm-video-loaded", "opacity", "wrapper-video::opacity");
		}
				
		// CONTENT BG
		
		$styles->setBackground("{bamselector} {cselector}", "content-bg");
		$styles->setBoxShadow("{bamselector} {pselector}", "content-shadow");


		// MENU ANIMATION SETTINGS
		
		if(empty($alwaysVisible)) {
			
			$openDuration = intval($styles->get_option('menu-open-duration'));
			$closeDuration = intval($styles->get_option('menu-close-duration'));
			
			$styles->set("{selector},{bamselector} {pselector}, {bamselector} {cselector}", "transition-timing-function", "menu-open-easing", null, false, true);
			$styles->set("{selector}.sm-menu-closing, {bamselector} {pselector}.sm-pusher-closing, {bamselector} {pselector}.sm-pusher-closing {cselector}", "transition-timing-function", "menu-close-easing", null, false, true);
			
			$styles->set("{selector}, {bamselector} {pselector}, {bamselector} {pselector} {cselector}", "transition-duration", "menu-open-duration", "ms", false, true);
			$styles->set("{selector}.sm-menu-closing, {bamselector} {pselector}.sm-pusher-closing, {bamselector} {pselector}.sm-pusher-closing {cselector}", "transition-duration", "menu-close-duration", "ms", false, true);
			
			
			$styles->setUseOptions(false);
			if($menuAnimation == 'sm-effect-13') {
				
				$styles->set("{selector}:not(.sm-menu-closing)}", "transition-duration", ($openDuration * 0.4), "ms", true, true);
				$styles->set("{selector}:not(.sm-menu-closing)}", "transition-delay", ($openDuration * 0.6), "ms", true, true);
				
			}else if($menuAnimation == 'sm-effect-14') {
				
				$styles->set("{selector}:not(.sm-menu-closing)", "transition-duration", ($openDuration * 0.7), "ms", true, true);
				$styles->set("{selector}:not(.sm-menu-closing)", "transition-delay", ($openDuration * 0.3), "ms", true, true);
			}	
			$styles->setUseOptions(true);
		}


		// LEVEL GENERAL
		
		$styles->set("", "width", "level-width", "level-width");
		$styles->set(".sm-level", "width", "level-width", "level-width");
		$styles->set(".sm-level-align", "padding", "level-padding");
		$styles->set(".sm-level-align", "vertical-align", "level-valign");
		$styles->set(".sm-nav-list", "padding", "level-menu-padding");
		$styles->set(".sm-nav-list", "max-width", "level-menu-width", "unit");
				
		
		// CLOSE LINK
	
		$styles->set(".sm-close > span","padding", "close-link-margin");
		$closePadding = intval($styles->get_option("close-link-padding"));

		$styles->set(".sm-close ._mi","color", "close-link-color");
		$styles->set(".sm-close ._mi._svg","fill", "close-link-color");
		$styles->set(".sm-close ._mi._svg *","fill", "close-link-color");

		$styles->set(".sm-close:hover ._mi", "color", "close-link-hover-color");
		$styles->set(".sm-close:hover ._mi._svg", "fill", "close-link-hover-color");
		$styles->set(".sm-close:hover ._mi._svg *", "fill", "close-link-hover-color");
		
		$styles->set(".sm-close > span a:before", "background-color", "close-link-bg-color");
		$styles->set(".sm-close > span:hover a:after", "background-color", "close-link-hover-bg-color");
		
		$styles->set(".sm-close > span a", 'font-size', 'close-link-icon-size');
		$styles->set(".sm-close > span a ._mi","font-size", "close-link-icon-size");
		$styles->set(".sm-close > span a ._mi","width", "close-link-icon-size");		
		$styles->set(".sm-close > span a ._mi","height", "close-link-icon-size");
				
		$styles->set(".sm-close > span a", 'width', 'close-link-icon-size', array(array('styles', 'calc'), array('+', $closePadding)));
		$styles->set(".sm-close > span a", 'height', 'close-link-icon-size', array(array('styles', 'calc'), array('+', $closePadding)));
		$styles->set(".sm-close > span a", 'line-height', 'close-link-icon-size', array(array('styles', 'calc'), array('+', $closePadding)));
		
		$styles->setBorder(".sm-close > span a:before", "close-link-border");
		$styles->setBorder(".sm-close > span a:after", "close-link-border");	

			
		// LEVEL HEADER
		
		$styles->set(".sm-header", "padding", "header-padding");
		$styles->set(".sm-header", "margin", "header-margin");
		$styles->setBackground(".sm-header", "header-bg");
		$styles->setPattern(".sm-header:before", "header-pattern");
		$styles->setOverlay(".sm-header:after", "header-overlay");
						
		// LEVEL LOGO
		
		$styles->set(".sm-header .sm-logo", "padding", "logo-padding");
		$styles->set(".sm-header .sm-logo", "text-align", "logo-align");
		$styles->set(".sm-header .sm-logo img", "max-width", "logo-width");
		$styles->set(".sm-header .sm-logo img", "max-height", "logo-height");
		$styles->setBorder(".sm-header .sm-logo img", "logo-border");		
				
		
		// LEVEL BACKGROUND
		
		$menu_bg = $styles->get_option('level-bg');
		
		if(!empty($menu_bg['apply-sublevels'])) {
			$styles->setBackground("{selector} .sm-level, {selector} .sm-level", "level-bg");
		}else{
			$styles->setBackground("{selector} .sm-level.sm-main-level, {selector} .sm-level.sm-main-level", "level-bg");
		}

		
		$menu_pattern = $styles->get_option('level-pattern');
		
		if(!empty($menu_pattern['apply-sublevels'])) {
			$styles->setPattern("{selector} .sm-level:before, {selector} .sm-level:before", "level-pattern");
		}else{
			$styles->setPattern("{selector} .sm-level.sm-main-level:before, {selector} .sm-level.sm-main-level:before", "level-pattern");	
		}
		
		
		$menu_overlay = $styles->get_option('level-overlay');
		
		if(!empty($menu_overlay['apply-sublevels'])) {
			$styles->setOverlay("{selector} .sm-level:after, {selector} .sm-level:after", "level-overlay");
		}else{
			$styles->setOverlay("{selector} .sm-level.sm-main-level:after, {selector} .sm-level.sm-main-level:after", "level-overlay");	
		}
		
					
		$video = $styles->get_option('level-video');
		
		if(!empty($video['id'])) {
			$styles->set(".sm-level.sm-main-level.sm-level-opened > .sm-level-bgvideo.sm-video-loaded", "opacity", "level-video::opacity");
		}
		
		
		if($levelAnimation == 'overlap') {
			$styles->set(".sm-level .sm-level-overlap", "background-color", "menu-overlap-bg-color");
		}
		

		// LEVEL SEARCH
	
		$styles->set(".sm-search", "background-color", "search-bg-color");
		$styles->set(".sm-search .sm-search-form .sm-search-field", "font-size", "search-font-size", "unit");
		$styles->set(".sm-search .sm-search-form .sm-search-field", "text-align", "search-text-align");
		$styles->setFont(".sm-search .sm-search-form .sm-search-field", "search-font-family");
		$styles->set(".sm-search .sm-search-form .sm-search-field", "color", "search-font-color");
		$styles->set(".sm-search .sm-search-form .sm-search-field", "height", "search-height", "unit");
		$styles->set(".sm-search .sm-search-form .sm-search-field", "padding-left", "search-hpadding", "unit");
		$styles->set(".sm-search .sm-search-form .sm-search-field", "padding-right", "search-hpadding", "unit");
		$styles->set(".sm-search", "width", "search-width", "unit");
		$styles->set(".sm-search", "margin", "search-margin");
		
		$styles->setBorder(".sm-search", "search-border");

		$searchIconPosition = $styles->get_option('search-icon-position');
		$styles->setUseOptions(false);
		if($searchIconPosition == 'left') {
			$styles->set(".sm-search .sm-search-form .sm-search-submit", "left", "15px");
		}else{
			$styles->set(".sm-search .sm-search-form .sm-search-submit", "right", "15px");
		}
		$styles->setUseOptions(true);
		$styles->set(".sm-search .sm-search-form .sm-search-submit", "font-size", "search-icon-size", "unit");
		$styles->set(".sm-search .sm-search-form .sm-search-submit", "width", "search-icon-size", "unit");
		$styles->set(".sm-search .sm-search-form .sm-search-submit", "margin-top", "search-icon-size", "calc(-{value} / 2)");
		$styles->set(".sm-search .sm-search-form .sm-search-submit", "color", "search-icon-color");
		
				
		// LEVEL TITLE
	
		
		$styles->set(".sm-title", "font-size", "title-font-size", "unit-responsive");
		$styles->set(".sm-title", "text-transform", "title-text-transform");
		$styles->set(".sm-title", "text-align", "title-position");
		$styles->setFont(".sm-title", "title-font-family");
		$styles->set(".sm-title", "color", "title-font-color");
		
		$styles->set(".sm-title", "margin", "title-margin");
		$styles->set(".sm-title .sm-title-wrap", "background-color", "title-bg-color");
		$styles->set(".sm-title .sm-title-wrap", "padding", "title-padding");
		$styles->setBorder(".sm-title .sm-title-wrap", "title-border");
		$styles->set(".sm-title  .sm-title-wrap", "text-align", "title-text-align");

		$styles->set(".sm-level-overlay .sm-title-overlap .sm-title-wrap ._mi", "color", "menu-overlap-icon-color");
		$styles->set(".sm-level-overlay .sm-title-overlap .sm-title-wrap ._mi._svg", "color", "menu-overlap-icon-color");
		$styles->set(".sm-level-overlay .sm-title-overlap .sm-title-wrap ._mi._svg *", "color", "menu-overlap-icon-color");
		$styles->set(".sm-level-overlay .sm-title-overlap .sm-title-wrap span", "color", "menu-overlap-title-color");
		$styles->setFont(".sm-level-overlay .sm-title-overlap", "menu-overlap-font-family");

		$styles->set(".sm-title ._mi", "color", "title-icon-color");
		$styles->set(".sm-title ._mi._svg", "color", "title-icon-color");
		$styles->set(".sm-title ._mi._svg *", "color", "title-icon-color");
		$styles->set(".sm-title ._mi", "font-size", "title-icon-size", "unit-responsive");
		$styles->set(".sm-title ._mi", "width", "title-icon-size", "unit-responsive");



		// LEVEL SUB TITLE
	
		$styles->set(".sm-subtitle", "font-size", "subtitle-font-size", "unit-responsive");
		$styles->set(".sm-subtitle", "text-transform", "subtitle-text-transform");
		$styles->set(".sm-subtitle", "text-align", "subtitle-text-align");
		$styles->setFont(".sm-subtitle", "subtitle-font-family");
		$styles->set(".sm-subtitle", "color", "subtitle-font-color");
		
		$styles->set(".sm-subtitle", "margin", "subtitle-margin");
		$styles->set(".sm-subtitle .sm-subtitle-wrap", "background-color", "subtitle-bg-color");
		$styles->set(".sm-subtitle .sm-subtitle-wrap", "padding", "subtitle-padding");
		$styles->setBorder(".sm-subtitle .sm-subtitle-wrap", "subtitle-border");


		// LEVEL DESCRIPTION
	
		$styles->set(".sm-description", "font-size", "description-font-size", "unit-responsive");
		$styles->set(".sm-description", "line-height", "description-line-height", "unit-responsive");
		$styles->set(".sm-description", "text-transform", "description-text-transform");
		$styles->set(".sm-description", "text-align", "description-text-align");
		$styles->setFont(".sm-description", "description-font-family");
		$styles->set(".sm-description", "color", "description-font-color");
		
		$styles->set(".sm-description", "max-width", "description-width", "unit");
		$styles->set(".sm-description", "margin", "description-margin");
		$styles->set(".sm-description .sm-description-wrap", "background-color", "description-bg-color");
		$styles->set(".sm-description .sm-description-wrap", "padding", "description-padding");
		$styles->setBorder(".sm-description .sm-description-wrap", "description-border");
		
		
		// BACK LINK
		
		$styles->set(".sm-back","text-align", "back-link-position");
		$styles->setFont(".sm-back a", "back-link-font-family");
		$styles->set(".sm-back a", "font-size", "back-link-font-size", "unit");
		$styles->set(".sm-back a", "line-height", "back-link-font-size", "unit");

		$styles->set(".sm-back a","padding", "back-link-padding");
		$styles->set(".sm-back a","margin", "back-link-margin");
		$styles->set(".sm-back a ._mi","font-size", "back-link-icon-size", "unit");
		$backPosition = $styles->get_option('back-link-position');
		$backIconPosition = $styles->get_option('back-link-icon-position');
		$backIconSize = $styles->get_option('back-link-icon-size');
		
		if(!empty($backPosition) && !empty($backIconPosition) && ($backIconPosition == 'before' || $backIconPosition == 'after')){
				
			$backIconPosition = $backIconPosition == 'before' ? 'left' : 'right';
				
			if($backPosition == 'center'){
				$styles->set(".sm-back a ._mi","margin-$backIconPosition", "back-link-icon-size", "-{value}");
			}
			
			if(!empty($mobileCentered)) {
				$styles->setMedia('mobile');
				$styles->set(".sm-back a ._mi","margin-$backIconPosition", "back-link-icon-size", "-{value}");
				$styles->resetMedia();
			}
		}	
		
		$styles->set(".sm-back a","color", "back-link-color");
		$styles->set(".sm-back ._mi","color", "back-link-color");
		$styles->set(".sm-back ._mi._svg","fill", "back-link-color");
		$styles->set(".sm-back ._mi._svg *","fill", "back-link-color");

		$styles->set(".sm-back:hover a", "color", "back-link-hover-color");
		$styles->set(".sm-back:hover ._mi", "color", "back-link-hover-color");
		$styles->set(".sm-back:hover ._mi._svg", "fill", "back-link-hover-color");
		$styles->set(".sm-back:hover ._mi._svg *", "fill", "back-link-hover-color");

		// MENU ITEMS
		
		
		$styles->set(".sm-nav-list li > a .sm-title-wrap", "height", "menu-items-height", "level-height");

		$styles->set(".sm-nav-list li > a .sm-title-wrap", "background-color", "menu-items-bg-color");
		$styles->set(".sm-nav-list li > a", "font-size", "menu-items-font-size", "unit-responsive");
		$styles->set(".sm-nav-list li > a", "line-height", "menu-items-line-height", "unit-responsive");
		$styles->set(".sm-nav-list li > a", "text-transform", "menu-items-text-transform");
		$styles->set(".sm-nav-list li > a", "text-align", "menu-items-text-align");
		$styles->set(".sm-nav-list li", "vertical-align", "menu-items-vertical-align");
		$styles->setFont(".sm-nav-list li > a", "menu-items-font-family");
		$styles->set(".sm-nav-list li > a", "color", "menu-items-font-color");
		$styles->set(".sm-nav-list li > a .sm-title-wrap", "padding", "menu-items-padding");
		$styles->set(".sm-nav-list li", "padding", "menu-items-margin", null, true);

		$styles->setBorder(".sm-nav-list li > a .sm-title-wrap", "menu-items-border");
	
		$styles->set(".sm-nav-list li.current-menu-item > a .sm-title-wrap", "background-color", "menu-items-active-bg-color");
		$styles->set(".sm-nav-list li.current-menu-item > a", "color", "menu-items-active-font-color");
		
		$styles->set(".sm-nav-list li > a:hover .sm-title-wrap:before", "background-color", "menu-items-hover-bg-color", null, true);
		$styles->set(".sm-nav-list li > a:hover", "color", "menu-items-hover-font-color", null, true);
	
		$styles->set(".sm-nav-list li > a .sm-icon", "font-size", "menu-items-icon-size", "unit-responsive");
		$styles->set(".sm-nav-list li > a .sm-icon", "line-height", "menu-items-icon-line-height", "unit-responsive");
		$styles->set(".sm-nav-list li > a .sm-icon", "width", "menu-items-icon-width");
		$styles->set(".sm-nav-list li > a .sm-icon", "text-align", "menu-items-icon-halign");
		$styles->set(".sm-nav-list li > a .sm-icon", "vertical-align", "menu-items-icon-valign");
		$styles->set(".sm-nav-list li > a .sm-icon", "color", "menu-items-icon-color");
		$styles->set(".sm-nav-list li > a .sm-icon._svg", "fill", "menu-items-icon-color");
		$styles->set(".sm-nav-list li > a .sm-icon._svg *", "fill", "menu-items-icon-color");

		$styles->set(".sm-nav-list li > a:hover .sm-icon", "color", "menu-items-icon-hover-color");
		$styles->set(".sm-nav-list li > a:hover .sm-icon._svg", "fill", "menu-items-icon-hover-color");
		$styles->set(".sm-nav-list li > a:hover .sm-icon._svg *", "fill", "menu-items-icon-hover-color");
	
		$arrowsPosition = $styles->get_option('menu-items-arrow-position');
		if(!empty($arrowsPosition)) {
			$styles->set(".sm-nav-list li.menu-item-has-children.sm-fullwidth a .sm-title-wrap .sm-title-inner-wrap span", 'margin-'.$arrowsPosition, "menu-items-arrow-size", array(array('styles', 'calc'), array('/', -2)));
		}

		$styles->set(".sm-nav-list li.menu-item-has-children > a .sm-arrow", "font-size", "menu-items-arrow-size", "unit-responsive");	
		$styles->set(".sm-nav-list li.menu-item-has-children > a .sm-arrow", "left", "menu-items-arrow-hoffset", "unit");
		$styles->set(".sm-nav-list li.menu-item-has-children > a .sm-arrow", "margin-top", "menu-items-arrow-voffset", "unit");
		$styles->set(".sm-nav-list li.menu-item-has-children > a .sm-arrow", "color", "menu-items-arrow-color");
		$styles->set(".sm-nav-list li.menu-item-has-children > a .sm-arrow._svg", "fill", "menu-items-arrow-color");
		$styles->set(".sm-nav-list li.menu-item-has-children > a .sm-arrow._svg *", "fill", "menu-items-arrow-color");

		$styles->set(".sm-nav-list li.menu-item-has-children:hover a .sm-arrow", "color", "menu-items-hover-arrow-color");
		$styles->set(".sm-nav-list li.menu-item-has-children:hover a .sm-arrow._svg", "fill", "menu-items-hover-arrow-color");
		$styles->set(".sm-nav-list li.menu-item-has-children:hover a .sm-arrow._svg *", "fill", "menu-items-hover-arrow-color");

		$styles->set(".sm-nav-list li > a .sm-title-wrap .sm-thumb-behind", "background-repeat", "menu-items-thumb-bg-repeat");
		$styles->set(".sm-nav-list li > a .sm-title-wrap .sm-thumb-behind", "background-size", "menu-items-thumb-bg-size");
		$styles->set(".sm-nav-list li > a .sm-title-wrap .sm-thumb-behind", "background-position", "menu-items-thumb-bg-position");
		
		$styles->setOverlay(".sm-nav-list li > a .sm-title-wrap .sm-thumb-behind:after", "menu-items-thumb-bg-overlay");
		$styles->setOverlay(".sm-nav-list li > a:hover .sm-title-wrap .sm-thumb-behind:after", "menu-items-thumb-hover-bg-overlay");
				
		$styles->set(".sm-nav-list li > a .sm-title-wrap .sm-thumb:not(.sm-thumb-behind)", "margin", "menu-items-thumb-margin");
		
		// Item Shadow
		$styles->setBoxShadow(".sm-nav-list li > a .sm-title-wrap", "menu-items-shadow");
		
		// Item Hover Shadow
		$styles->setBoxShadow(".sm-nav-list li > a:hover .sm-title-wrap", "menu-items-hover-shadow");
		
		
		$globalItemsTextAlign = $styles->get_option('menu-items-text-align');
		
		// Item Inactive Transforms
		$inactive_transformsEnabled = (bool)$styles->get_option("menu-items-inactive-transforms::enabled");	
		if($inactive_transformsEnabled) {
			$styles->setPerspective("{inactive_level_selector} .sm-nav-list li > a", "menu-items-inactive-transforms::perspective"); 
			$styles->setPerspectiveOrigin("{inactive_level_selector} .sm-nav-list li > a", "menu-items-inactive-transforms::perspective-origin");
			$styles->setTransform("{inactive_level_selector} .sm-nav-list li > a .sm-title-wrap:not(.sm-has-thumb-behind), {inactive_level_selector} .sm-nav-list li > a .sm-title-wrap.sm-has-thumb-behind .sm-thumb-behind", "menu-items-inactive-transforms", false, $globalItemsTextAlign, $mobileCentered);
		}
		
		// Item Transforms
		$transformsEnabled = (bool)$styles->get_option("menu-items-transforms::enabled");
		$transitionDuration = $styles->get_option("menu-items-transforms::duration");	
		if($transformsEnabled) {
			
			$styles->setTransitionDelay("{opening_level_selector} .sm-nav-list li > a .sm-title-wrap.sm-title-wrap:not(.sm-has-thumb-behind), {opening_level_selector} .sm-nav-list li > a .sm-title-wrap.sm-title-wrap.sm-has-thumb-behind .sm-thumb-behind", "menu-items-transforms::delay");
			$styles->setTransitionDuration("{opening_level_selector} .sm-nav-list li > a .sm-title-wrap.sm-title-wrap:not(.sm-has-thumb-behind), {opening_level_selector} .sm-nav-list li > a .sm-title-wrap.sm-title-wrap.sm-has-thumb-behind .sm-thumb-behind", "menu-items-transforms::duration");
			
			$styles->setPerspective("{opening_level_selector} .sm-nav-list li > a", "menu-items-transforms::perspective"); 
			$styles->setPerspectiveOrigin("{opening_level_selector} .sm-nav-list li > a", "menu-items-transforms::perspective-origin");
			$styles->setTransform("{opening_level_selector} .sm-nav-list li > a .sm-title-wrap:not(.sm-has-thumb-behind), {opening_level_selector} .sm-nav-list li > a .sm-title-wrap.sm-has-thumb-behind .sm-thumb-behind", "menu-items-transforms", false, $globalItemsTextAlign, $mobileCentered);
		}
		
		// Item Hover Transforms
		$hover_transformsEnabled = (bool)$styles->get_option("menu-items-hover-transforms::enabled");	
		if($hover_transformsEnabled) {
			
			$styles->setTransitionDelay(".sm-no-touchevents {opened_level_selector} .sm-nav-list li > a .sm-title-wrap:not(.sm-has-thumb-behind), .sm-no-touchevents {opened_level_selector} .sm-nav-list li > a .sm-title-wrap.sm-has-thumb-behind .sm-thumb-behind", "menu-items-hover-transforms::delay"); 
			$styles->setTransitionDuration(".sm-no-touchevents {opened_level_selector} .sm-nav-list li > a .sm-title-wrap:not(.sm-has-thumb-behind), .sm-no-touchevents {opened_level_selector} .sm-nav-list li > a .sm-title-wrap.sm-has-thumb-behind .sm-thumb-behind", "menu-items-hover-transforms::duration"); 
			
			$styles->setPerspective(".sm-no-touchevents {opened_level_selector} .sm-nav-list li > a:hover", "menu-items-hover-transforms::perspective"); 
			$styles->setPerspectiveOrigin(".sm-no-touchevents {opened_level_selector} .sm-nav-list li > a:hover", "menu-items-hover-transforms::perspective-origin"); 
			$styles->setTransform(".sm-no-touchevents {opened_level_selector} .sm-nav-list li > a:hover .sm-title-wrap:not(.sm-has-thumb-behind), .sm-no-touchevents {opened_level_selector} .sm-nav-list li > a:hover .sm-title-wrap.sm-has-thumb-behind .sm-thumb-behind", "menu-items-hover-transforms", false, $globalItemsTextAlign, $mobileCentered);
		}
		
	
		// MENU FOOTER
		$styles->set(".sm-footer", "min-height", "footer-min-height");
		$styles->set(".sm-footer", "padding", "footer-padding");
		$styles->set(".sm-footer", "margin", "footer-margin");
		$styles->setBackground(".sm-footer", "footer-bg");
		$styles->setPattern(".sm-footer:before", "footer-pattern");
		$styles->setOverlay(".sm-footer:after", "footer-overlay");
		$styles->set(".sm-footer .sm-footer-text", "padding", "footer-text-padding");
		$styles->setFont(".sm-footer .sm-footer-text", "footer-text-font-family");
		$styles->set(".sm-footer .sm-footer-text", "font-size", "footer-text-font-size", "unit-responsive");	
		$styles->set(".sm-footer .sm-footer-text", "color", "footer-text-color");
		$styles->set(".sm-footer .sm-footer-text", "text-align", "footer-text-align");		
	
		
		
		// FALLBACK for browsers that don't support 3D transforms (and no JS fallback)

		$styles->set("===html.no-csstransforms3d.sm-menu-active:not(.sm-always-visible) .sm-body.sm-menu-$menu_id .sm-pusher.sm-left", "padding-left", "level-width", "unit");
		$styles->set("===html.no-csstransforms3d.sm-menu-active:not(.sm-always-visible) .sm-body.sm-menu-$menu_id .sm-pusher.sm-right", "padding-right", "level-width", "unit");

		
		// END
		

		do_action('slick_menu_styles', $styles, $menu_id);
		
		
		$this->global_css .= $styles->render(true, false);
		$this->global_fonts = $styles->getCurrentFonts();
		$this->global_fonts_imports .= $styles->renderGoogleFonts(true);	
	
		$menu_items = wp_get_nav_menu_items( $menu_id, array(
			'orderby' => 'menu_order'	
		));

	    foreach( $menu_items as $item ) {
		
			$item_styles = new Slick_Menu_Styles('options', $menu_id, $item->ID, $this->global_fonts);
			$item_styles->setMediaBreakpoint('mobile', $breakpoint);
			
			$itemMobileCentered = $item_styles->get_option('level-mobile-centered');
	
			// MENU ITEM Settings
		
			$item_styles->set("> a .sm-title-wrap", "height", "menu-item-height", "level-height");

			$item_styles->set("> a .sm-title-wrap", "background-color", "menu-item-bg-color");
			$item_styles->set("> a", "font-size", "menu-item-font-size", "unit-responsive");
			$item_styles->set("> a", "line-height", "menu-item-line-height", "unit-responsive");
		
			$item_styles->set("> a", "text-transform", "menu-item-text-transform");
			$item_styles->set("> a", "text-align", "menu-item-text-align");
			$item_styles->set("", "vertical-align", "menu-item-vertical-align");
			$item_styles->setFont("> a", "menu-item-font-family");
			$item_styles->set("> a", "color", "menu-item-font-color");
			$item_styles->set("> a .sm-title-wrap", "padding", "menu-item-padding");
			$item_styles->set("", "padding", "menu-item-margin", null, true);
			
			$item_styles->setBorder("> a .sm-title-wrap", "menu-item-border");
	
			$item_styles->set("&.current-menu-item > a .sm-title-wrap", "background-color", "menu-item-active-bg-color");
			$item_styles->set("&.current-menu-item > a", "color", "menu-item-active-font-color");
			
			$item_styles->set("> a:hover .sm-title-wrap:before", "background-color", "menu-item-hover-bg-color", null, true);
			$item_styles->set("> a:hover", "color", "menu-item-hover-font-color", null, true);
		
			$item_styles->set("> a .sm-icon", "font-size", "menu-item-icon-size", "unit-responsive");
			$item_styles->set("> a .sm-icon", "line-height", "menu-item-icon-line-height", "unit-responsive");
			$item_styles->set("> a .sm-icon", "width", "menu-item-icon-width", "unit-responsive");

			$item_styles->set("> a .sm-icon", "text-align", "menu-item-icon-halign");
			$item_styles->set("> a .sm-icon", "vertical-align", "menu-item-icon-valign");
			$item_styles->set("> a .sm-icon", "color", "menu-item-icon-color");
			$item_styles->set("> a .sm-icon._svg", "fill", "menu-item-icon-color");
			$item_styles->set("> a .sm-icon._svg *", "fill", "menu-item-icon-color");

			$item_styles->set("&:hover a .sm-icon", "color", "menu-item-icon-hover-color");
			$item_styles->set("&:hover a .sm-icon._svg", "fill", "menu-item-icon-hover-color");
			$item_styles->set("&:hover a .sm-icon._svg *", "fill", "menu-item-icon-hover-color");

			$arrowsPosition = $item_styles->get_option('menu-item-arrow-position');
			if(!empty($arrowsPosition)) {
				$item_styles->set("&.menu-item-has-children.sm-fullwidth a .sm-title-wrap .sm-title-inner-wrap span", 'margin-'.$arrowsPosition, "menu-item-arrow-size", array(array('styles', 'calc'), array('/', -2)));
			}

			$item_styles->set("&.menu-item-has-children a .sm-arrow", "font-size", "menu-item-arrow-size", "unit-responsive");
		
			$item_styles->set("&.menu-item-has-children a .sm-arrow", "left", "menu-item-arrow-hoffset", "unit-skip-zero");
			$item_styles->set("&.menu-item-has-children a .sm-arrow", "margin-top", "menu-item-arrow-voffset", "unit-skip-zero");
			$item_styles->set("&.menu-item-has-children a .sm-arrow", "color", "menu-item-arrow-color");
			$item_styles->set("&.menu-item-has-children a .sm-arrow._svg", "fill", "menu-item-arrow-color");
			$item_styles->set("&.menu-item-has-children a .sm-arrow._svg *", "fill", "menu-item-arrow-color");

			$item_styles->set("&.menu-item-has-children:hover a .sm-arrow", "color", "menu-item-hover-arrow-color");
			$item_styles->set("&.menu-item-has-children:hover a .sm-arrow._svg", "fill", "menu-item-hover-arrow-color");
			$item_styles->set("&.menu-item-has-children:hover a .sm-arrow._svg *", "fill", "menu-item-hover-arrow-color");

			if($item_styles->get_option("menu-item-thumb-position") === 'behind') {
					
				$menu_item_thumb = Slick_Menu()->get_menu_item_option('menu-item-thumb', $item->ID);

				if(!empty($menu_item_thumb)) {
					
					$item_styles->set("> a .sm-title-wrap .sm-thumb-behind", "background-repeat", "menu-item-thumb-bg-repeat");
					$item_styles->set("> a .sm-title-wrap .sm-thumb-behind", "background-size", "menu-item-thumb-bg-size");
					$item_styles->set("> a .sm-title-wrap .sm-thumb-behind", "background-position", "menu-item-thumb-bg-position");
					
					$item_styles->setOverlay("> a .sm-title-wrap .sm-thumb-behind:after", "menu-item-thumb-bg-overlay");
					$item_styles->setOverlay("> a:hover .sm-title-wrap .sm-thumb-behind:after", "menu-item-thumb-hover-bg-overlay");

				}
			}
			
			$item_styles->set("> a .sm-title-wrap .sm-thumb:not(.sm-thumb-behind)", "margin", "menu-item-thumb-margin");


			// Item Shadow
			$item_styles->setBoxShadow("> a .sm-title-wrap", "menu-item-shadow");
			
			//Item Hover Shadow
			$item_styles->setBoxShadow("> a:hover .sm-title-wrap", "menu-item-hover-shadow");
		
			
			$textAlign = $item_styles->get_option('menu-item-text-align');

	
			// Item Inactive Transforms

			$inactive_transformsEnabled = (bool)$item_styles->get_option("menu-item-inactive-transforms::enabled");	
			if($inactive_transformsEnabled) {
				$item_styles->setPerspective("{inactive_level_selector} a", "menu-item-inactive-transforms::perspective"); 
				$item_styles->setPerspectiveOrigin("{inactive_level_selector} a .sm-title-wrap", "menu-item-inactive-transforms::perspective-origin");
				$item_styles->setTransform("{inactive_level_selector} a .sm-title-wrap:not(.sm-has-thumb-behind), {inactive_level_selector} a .sm-title-wrap.sm-has-thumb-behind .sm-thumb-behind", "menu-item-inactive-transforms", false, $globalItemsTextAlign, $mobileCentered);
			}

			// Item Transforms
			$transformsEnabled = (bool)$item_styles->get_option("menu-item-transforms::enabled");	
			if($item->ID == 1342) {
				$t = $item_styles->get_option("menu-item-transforms");	
			}
			if($transformsEnabled) {
				
				$item_styles->setTransitionDelay("{opening_level_selector} a .sm-title-wrap:not(.sm-has-thumb-behind), {opening_level_selector} a .sm-title-wrap.sm-has-thumb-behind .sm-thumb-behind", "menu-item-transforms::delay"); 
				$item_styles->setTransitionDuration("{opening_level_selector} a .sm-title-wrap:not(.sm-has-thumb-behind), {opening_level_selector} a .sm-title-wrap.sm-has-thumb-behind .sm-thumb-behind", "menu-item-transforms::duration"); 

				$item_styles->setPerspective("{opening_level_selector} a", "menu-item-transforms::perspective"); 
				$item_styles->setPerspectiveOrigin("{opening_level_selector} a", "menu-item-transforms::perspective-origin"); 
				$item_styles->setTransform("{opening_level_selector} a .sm-title-wrap:not(.sm-has-thumb-behind), {opening_level_selector} a .sm-title-wrap.sm-has-thumb-behind .sm-thumb-behind", "menu-item-transforms", false, $textAlign, $itemMobileCentered); 
			}

			// Item Hover Transforms
			$hover_transformsEnabled = (bool)$item_styles->get_option("menu-item-hover-transforms::enabled");	
		
			if(empty($textAlign)) {
				$textAlign = $globalItemsTextAlign;
			}
			if($hover_transformsEnabled) {
				
				$item_styles->setTransitionDelay(".sm-no-touchevents {opened_level_selector} a .sm-title-wrap:not(.sm-has-thumb-behind), {opening_level_selector} a .sm-title-wrap.sm-has-thumb-behind .sm-thumb-behind", "menu-item-hover-transforms::delay");
				$item_styles->setTransitionDuration(".sm-no-touchevents {opened_level_selector} a .sm-title-wrap:not(.sm-has-thumb-behind), {opening_level_selector} a .sm-title-wrap.sm-has-thumb-behind .sm-thumb-behind", "menu-item-hover-transforms::duration");
				
				$item_styles->setPerspective(".sm-no-touchevents {opened_level_selector} a:hover", "menu-item-hover-transforms::perspective"); 
				$item_styles->setPerspectiveOrigin(".sm-no-touchevents {opened_level_selector} a:hover", "menu-item-hover-transforms::perspective-origin"); 
				$item_styles->setTransform(".sm-no-touchevents {opened_level_selector} a:hover .sm-title-wrap:not(.sm-has-thumb-behind), {opening_level_selector} a:hover .sm-title-wrap.sm-has-thumb-behind .sm-thumb-behind", "menu-item-hover-transforms", false, $textAlign, $itemMobileCentered); 
			}

		
			
			// WRAPPER BACKGROUND
		
			//$item_styles->setBackground("{bamiselector} .sm-wrapper-bg", "wrapper-bg");
			//$item_styles->setPattern("{bamiselector} .sm-wrapper-bg .sm-wrapper-pattern", "wrapper-pattern");
			//$item_styles->setOverlay("{bamiselector} .sm-wrapper-bg .sm-wrapper-overlay", "wrapper-overlay");
			
			$itemWrapperVideo = $item_styles->get_option('wrapper-video');
		
			if(!empty($itemWrapperVideo['id'])) {
				$item_styles->set("{bamiselector} .sm-wrapper-video.sm-video-loaded", "opacity", "wrapper-video::opacity");
			}
		
		
			
			// SUB LEVEL GENERAL
			$item_styles->set("html:not(.sm-bp-mobile) {selector} .sm-level", "width", "level-width", "level-width");
			$item_styles->set(".sm-level-align", "padding", "level-padding");
			$item_styles->set(".sm-level-align", "vertical-align", "level-valign");
			$item_styles->set(".sm-nav-list", "padding", "level-menu-padding");
			$item_styles->set(".sm-nav-list", "max-width", "level-menu-width", "unit");
				
			
			// LEVEL HEADER
		
			$item_styles->set(".sm-header", "padding", "header-padding");
			$item_styles->set(".sm-header", "margin", "header-margin");
			$item_styles->setBackground(".sm-header", "header-bg");
			$item_styles->setPattern(".sm-header:before", "header-pattern");
			$item_styles->setOverlay(".sm-header:after", "header-overlay");
					
			
			// LEVEL LOGO
			$item_styles->set(".sm-header .sm-logo", "padding", "logo-padding");
			$item_styles->set(".sm-header .sm-logo", "text-align", "logo-align");
			$item_styles->set(".sm-header .sm-logo img", "max-width", "logo-width");
			$item_styles->set(".sm-header .sm-logo img", "max-height", "logo-height");
			$item_styles->setBorder(".sm-header .sm-logo img", "logo-border");
		
		
			// SUB LEVEL - BACKGROUND
			$menu_bg = $item_styles->get_option('level-bg');
			
			if(!empty($menu_bg['apply-sublevels'])) {
				$item_styles->setBackground("{selector} .sm-level, {selector} .sm-level", "level-bg");
			}else{
				$item_styles->setBackground("{selector} > .sm-level, {selector} > .sm-level", "level-bg");
			}


			$menu_pattern = $item_styles->get_option('level-pattern');
			
			if(!empty($menu_pattern['apply-sublevels'])) {
				$item_styles->setPattern("{selector} .sm-level:before, {selector} .sm-level:before", "level-pattern");
			}else{
				$item_styles->setPattern("{selector} > .sm-level:before, {selector} > .sm-level:before", "level-pattern");
			}
			
			
			$menu_overlay = $item_styles->get_option('level-overlay');
			
			if(!empty($menu_overlay['apply-sublevels'])) {
				$item_styles->setOverlay("{selector} .sm-level:after, {selector} .sm-level:after", "level-overlay");
			}else{
				$item_styles->setOverlay("{selector} > .sm-level:after, {selector} > .sm-level:after", "level-overlay");
			}
			
									
			$video = $item_styles->get_option('level-video');
			
			if(!empty($video['id'])) {
				$item_styles->set("> .sm-level-opened > .sm-level-bgvideo.sm-video-loaded", "opacity", "level-video::opacity");
			}
				
			
			// SUB LEVEL - LEVEL TITLE
		
			$item_styles->set(".sm-title", "font-size", "title-font-size", "unit-responsive");		
			$item_styles->set(".sm-title", "text-transform", "title-text-transform");
			$item_styles->set(".sm-title", "text-align", "title-position", "unit");
			$item_styles->setFont(".sm-title", "title-font-family");
			$item_styles->set(".sm-title", "color", "title-font-color");
			
			$item_styles->set(".sm-title", "margin", "title-margin");
			$item_styles->set(".sm-title .sm-title-wrap", "background-color", "title-bg-color");
			$item_styles->set(".sm-title .sm-title-wrap", "padding", "title-padding");
			$item_styles->setBorder(".sm-title .sm-title-wrap", "title-border");
			$item_styles->set(".sm-title .sm-title-wrap", "text-align", "title-text-align", "unit");

			$item_styles->set(".sm-title ._mi", "color", "title-icon-color");
			$item_styles->set(".sm-title ._mi._svg", "fill", "title-icon-color");
			$item_styles->set(".sm-title ._mi._svg *", "fill", "title-icon-color");
			$item_styles->set(".sm-title ._mi", "font-size", "title-icon-size", "unit-responsive");
			$item_styles->set(".sm-title ._mi", "width", "title-icon-size", "unit-responsive");



			// SUB LEVEL - LEVEL SUB TITLE
		
			$item_styles->set(".sm-subtitle", "font-size", "subtitle-font-size", "unit-responsive");
			$item_styles->set(".sm-subtitle", "text-transform", "subtitle-text-transform");
			$item_styles->set(".sm-subtitle", "text-align", "subtitle-text-align", "unit");
			$item_styles->setFont(".sm-subtitle", "subtitle-font-family");
			$item_styles->set(".sm-subtitle", "color", "subtitle-font-color");
			
			$item_styles->set(".sm-subtitle", "margin", "subtitle-margin");
			$item_styles->set(".sm-subtitle .sm-subtitle-wrap", "background-color", "subtitle-bg-color");
			$item_styles->set(".sm-subtitle .sm-subtitle-wrap", "padding", "subtitle-padding");
			$item_styles->setBorder(".sm-subtitle .sm-subtitle-wrap", "subtitle-border");


			// SUB LEVEL - LEVEL DESCRIPTION
		
			$item_styles->set(".sm-description", "font-size", "description-font-size", "unit-responsive");
			$item_styles->set(".sm-description", "line-height", "description-line-height", "unit-responsive");
			$item_styles->set(".sm-description", "text-transform", "description-text-transform");
			$item_styles->set(".sm-description", "text-align", "description-text-align", "unit");
			$item_styles->setFont(".sm-description", "description-font-family");
			$item_styles->set(".sm-description", "color", "description-font-color");

			$item_styles->set(".sm-description", "max-width", "description-width", "unit");
			$item_styles->set(".sm-description", "margin", "description-margin");
			$item_styles->set(".sm-description .sm-description-wrap", "background-color", "description-bg-color");
			$item_styles->set(".sm-description .sm-description-wrap", "padding", "description-padding");
			$item_styles->setBorder(".sm-description .sm-description-wrap", "description-border");

			
			
			// SUB LEVEL - BACK LINK

			$item_styles->set(".sm-back","text-align", "back-link-position");
			$item_styles->setFont(".sm-back a", "back-link-font-family");
			$item_styles->set(".sm-back a", "font-size", "back-link-font-size", "unit");
			$item_styles->set(".sm-back a","line-height", "back-link-font-size", "unit");
			
			$item_styles->set(".sm-back a","padding", "back-link-padding");
			$item_styles->set(".sm-back a","margin", "back-link-margin");
			$item_styles->set(".sm-back a ._mi","font-size", "back-link-icon-size", "unit");
			$itemBackPosition = $item_styles->get_option('back-link-position');
			$itemBackIconPosition = $item_styles->get_option('back-link-icon-position');
			$itemBackIconSize = $item_styles->get_option('back-link-icon-size');
			
			if(empty($itemBackIconPosition)){
				$itemBackIconPosition = $backIconPosition;
			}
			if(empty($itemBackIconSize)){
				$itemBackIconSize = $backIconSize;
			}
			if(!empty($itemBackPosition) && !empty($itemBackIconPosition) && ($itemBackIconPosition == 'before' || $itemBackIconPosition == 'after')){
				
				$itemBackIconPosition = $itemBackIconPosition == 'before' ? 'left' : 'right';
				$itemBackIconPositionReverse = $itemBackIconPosition == 'before' ? 'right' : 'left';
				
				$item_styles->setUseOptions(false);
				if($itemBackPosition == 'center'){
					$item_styles->set(".sm-back a ._mi", "margin-$itemBackIconPosition", "-".$itemBackIconSize, "unit");
					$item_styles->set(".sm-back a ._mi", "margin-$itemBackIconPositionReverse", "inherit", "unit");
				}else{
					//$item_styles->set(".sm-back a ._mi", "margin-left", "inherit");
					//$item_styles->set(".sm-back a ._mi", "margin-right", "inherit");
					if(!empty($itemMobileCentered)) {
						$item_styles->setMedia('mobile');
						$item_styles->set(".sm-back a ._mi", "margin-$itemBackIconPosition", "-".$itemBackIconSize, "unit");
						$item_styles->set(".sm-back a ._mi", "margin-$itemBackIconPositionReverse", "inherit", "unit");
						$item_styles->resetMedia();
					}	
				}
				$item_styles->setUseOptions(true);	
			}
			
			$item_styles->set(".sm-back a","color", "back-link-color");
			$item_styles->set(".sm-back ._mi","color", "back-link-color");
			$item_styles->set(".sm-back ._mi._svg","fill", "back-link-color");
			$item_styles->set(".sm-back ._mi._svg *","fill", "back-link-color");

			$item_styles->set(".sm-back:hover a", "color", "back-link-hover-color");
			$item_styles->set(".sm-back:hover ._mi", "color", "back-link-hover-color");
			$item_styles->set(".sm-back:hover ._mi._svg", "fill", "back-link-hover-color");
			$item_styles->set(".sm-back:hover ._mi._svg *", "fill", "back-link-hover-color");

				
			// MENU FOOTER
			
			$item_styles->set(".sm-footer", "min-height", "footer-min-height");
			$item_styles->set(".sm-footer", "padding", "footer-padding");
			$item_styles->set(".sm-footer", "margin", "footer-margin");
			$item_styles->setBackground(".sm-footer", "footer-bg");
			$item_styles->setPattern(".sm-footer:before", "footer-pattern");
			$item_styles->setOverlay(".sm-footer:after", "footer-overlay");
			$item_styles->set(" .sm-footer .sm-footer-text", "background-color", "footer-text-color");
			$item_styles->set(" .sm-footer .sm-footer-text", "padding", "footer-text-padding");
			$item_styles->setFont(" .sm-footer .sm-footer-text", "footer-text-font-family");
			$item_styles->set(" .sm-footer .sm-footer-text", "font-size", "footer-text-font-size", "unit-responsive");
			$item_styles->set(" .sm-footer .sm-footer-text", "color", "footer-text-color");
			$item_styles->set(" .sm-footer .sm-footer-text", "text-align", "footer-text-align");
			
			do_action('slick_menu_item_styles', $item_styles, $item->ID);	
				
			// END	
					
			$this->global_css .= $item_styles->render(true, false);
			$this->global_fonts = $item_styles->getCurrentFonts();
			$this->global_fonts_imports .= $item_styles->renderGoogleFonts(true);
		
		}
	}	
	
	public function getStyles($excludeFonts = false) {
		
		$styles = $this->global_css;
		
		if(!$excludeFonts) {
			$styles = $this->mergeFontsImports($this->global_fonts_imports).$styles;
		}

		return $styles;
	}
	
	public function mergeFontsImports($imports) {
		
		if($this->minify) {
			$imports = str_replace(");@import url(//fonts.googleapis.com/css?family=", "|", $imports);
		}else{	
			$imports = str_replace(");\r\n@import url(//fonts.googleapis.com/css?family=", "|", $imports);
		}
		return $imports;
	}
	
	public function getFontsLink() {
		
		$fontsLink = false;

		if ( !$this->use_cache || false === ( $fontsLink = $this->parent->pcache->get('fonts-link') ) ) {
			
			$fontsImports = $this->mergeFontsImports($this->global_fonts_imports);
			preg_match('/\@import.url\((.+?)\)\;/', $fontsImports, $match);
			
			if(!empty($match[1])) {
				$fontsLink = set_url_scheme($match[1]);
				
				$this->parent->pcache->set('fonts-link', $fontsLink);
			}
		}
		
		return $fontsLink;	
	}
	
	public function resetVars() {
		
		$this->global_fonts = array();
		$this->global_fonts_imports = '';
		$this->global_css = '';
	}	
			

	/**
	 * Slick_Menu_Styles_Output Instance
	 *
	 * Ensures only one instance of Slick_Menu_Styles_Output is loaded or can be loaded.
	 *
	 * @since 1.0.0
	 * @static
	 * @see Slick_Menu()
	 * @return Slick_Menu_Styles_Output instance
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
	