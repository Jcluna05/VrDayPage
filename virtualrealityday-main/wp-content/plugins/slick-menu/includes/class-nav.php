<?php

if ( ! defined( 'ABSPATH' ) ) exit;
	
class Slick_Menu_Nav {

	/**
	 * The single instance of Slick_Menu_Nav.
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

	/**
	 * Registered Push Menus
	 * @var 	object
	 * @access  public
	 * @since 	1.0.0
	 */
	public $menus = array();
	
	/*--------------------------------------------*
	 * Constructor
	 *--------------------------------------------*/
	 
	/**
	 * Initializes the plugin by setting localization, filters, and administration functions.
	 */
	public function __construct( $parent ) {
		
		$this->parent = &$parent;
		$this->parent->nav = &$this;

		add_filter('sm_mb_nav_menus', array($this, 'nav_menu' ));
		add_filter('sm_rwmb_meta_boxes', array($this, 'register_metabox'));

        if ( ! is_admin() || defined('DOING_AJAX') ) {
            add_filter('nav_menu_item_title', array($this, 'item_title'), PHP_INT_MAX - 10, 4 );
        }
		add_filter('nav_menu_link_attributes', array($this, 'link_attributes'), PHP_INT_MAX - 10, 3 );
		
		add_action('customize_save_after', array($this, 'flush_cache'));
		add_action('sm_mb_nav_menu_saved', array($this, 'flush_cache'));
		add_action('wp_delete_nav_menu', array($this, 'flush_cache'));
		add_action('wp_update_nav_menu', array($this, 'flush_cache'));
		
		add_filter('sm_mb_nav_menu_enabled_key', array($this, 'menu_enabled_key'));
			        
	    add_action('sm_mb_nav_menu_activation', array($this, 'on_menu_activation'), 10, 2);

		
	} // end constructor
	
	function menu_enabled_key() {
		
		return 'slick-menu-enabled';
	}
	
	function on_menu_activation($menu_id, $enabled) {
		
		if($enabled) {
			
			if(!$this->parent->menu_metas_saved($menu_id)) {
				$this->parent->reset_all_menu_options($menu_id);
			}
		}	
		$this->parent->pcache->flush();
	}

	function nav_menu( $nav_menu )
	{
		$nav_menu = array(
			'id' => $this->parent->mb_id('nav-menu'),
			'title' => $this->parent->plugin_name()
		);
		return $nav_menu;
	}

	
	public function register_metabox($meta_boxes) {

		$menus = array();
		if(is_admin()) {
			$menus = $this->parent->get_menus_dropdown_options();
		}
		
		$types = array('menu', 'menu-item');


		$meta_boxes[] = array(
			'id'         => $this->parent->mb_id('menu-item-trigger'),
	        'title'      => esc_html__('Menu Item Settings', 'slick-menu'),
			'nav_menus'  => true,
	        'priority'	 => 'low',
	        'context'    => 'advanced',
	        'include' 	 => array(
				'custom' => array($this, 'not_push_menu')
			),
			'accordions'  => $this->get_accordions(array(
				'general',
				'icons'
			)),
	        'fields'     => array_merge_recursive(
	        	$this->parent->include_metabox_fields('menu-item', 'trigger', array('menus' => $menus)),
	        	$this->parent->include_metabox_fields('menu-item', 'item-icon')
	        )
		);	
		
		$meta_boxes[] = array(
			'id'         => $this->parent->mb_id('menu-item-item'),
	        'title'      => esc_html__('Menu Item Settings', 'slick-menu'),
			'nav_menus'  => true,
	        'priority'	 => 'low',
	        'context'    => 'advanced',
	        'include' 	 => array(
				'custom' => array($this, 'is_push_menu_item')
			),
			'accordions'  => $this->get_accordions(array(
				'general',
				'font',
				'colors',
				'spacing',
				'border',
				'icons',
				'arrows',
				'image',
				'shadow',
				'animations',
				'transforms'
			)),
	        'fields'     => array_merge_recursive(
	        	$this->parent->include_metabox_fields('menu-item', 'trigger', array('menus' => $menus)),
	        	$this->parent->include_metabox_fields('menu-item', 'item')
	        )	
		);
		

		foreach($types as $type) {
			
			$title_prefix = esc_html__('Level ', 'slick-menu').' ';
			
			if($type == 'menu-item') {
				$include_function = 'is_push_menu_item';
			}else{
				$include_function = 'is_push_menu_main';
			}

			if($type == 'menu') {

				$meta_boxes[] = array(
					'id'         => $this->parent->mb_id($type.'-general-settings'),
					'title'      => esc_html__('General Settings', 'slick-menu'),
					'nav_menus'  => true,
			        'priority'	 => 'low',
			        'context'    => 'advanced',
			        'include' 	 => array(
						'custom' => array($this, $include_function)
					),
			        'fields'     => $this->parent->include_metabox_fields($type, 'general')	
				);

			
				$meta_boxes[] = array(
					'id'         => $this->parent->mb_id($type.'-close-link'),
					'title'      => esc_html__('Close Link', 'slick-menu'),
					'nav_menus'  => true,
			        'priority'	 => 'low',
			        'context'    => 'advanced',
			        'include' 	 => array(
						'custom' => array($this, $include_function)
					),
					'accordions'  => $this->get_accordions(array(
						'general',
						'colors',
						'icons',
						'spacing',
						'border',
						'animations'
					)),
			        'fields'     => $this->parent->include_metabox_fields($type, 'close-link')	
				);

				$meta_boxes[] = array(
					'id'         => $this->parent->mb_id('search'),
			        'title'      => esc_html__('Level Search Box', 'slick-menu'),
					'nav_menus'  => true,
			        'priority'	 => 'low',
			        'context'    => 'advanced',
			        'include' 	 => array(
						'custom' => array($this, $include_function)
					),
					'accordions'  => $this->get_accordions(array(
						'general',
						'font',
						'colors',
						'icons',
						'spacing',
						'border',
						'animations'
					)),
			        'fields'     => $this->parent->include_metabox_fields($type, 'search')	
				);
			}
				
			$meta_boxes[] = array(
				'id'         => $this->parent->mb_id($type.'-level-general'),
		        'title'      => sprintf(esc_html__('%s General Settings', 'slick-menu'), $title_prefix),
				'nav_menus'  => true,
		        'priority'	 => 'low',
		        'context'    => 'advanced',
		        'include' 	 => array(
					'custom' => array($this, $include_function)
				),
				'accordions'  => $this->get_accordions(array(
					'general',
					'spacing'
				)),
		        'fields'     => $this->parent->include_metabox_fields($type, 'level-general')
			);
			
			$meta_boxes[] = array(
				'id'         => $this->parent->mb_id($type.'-level-header'),
		        'title'      => sprintf(esc_html__('%s Header', 'slick-menu'), $title_prefix),
				'nav_menus'  => true,
		        'priority'	 => 'low',
		        'context'    => 'advanced',
		        'include' => array(
					'custom' => array($this, $include_function)
				),
				'accordions'  => $this->get_accordions(array(
					'general',
					'bg-image',
					'bg-pattern',
					'bg-overlay',
					'spacing'
				)),
		        'fields'     => $this->parent->include_metabox_fields($type, 'level-header')
			);
									
			$meta_boxes[] = array(
				'id'         => $this->parent->mb_id($type.'-level-bg'),
		        'title'      => sprintf(esc_html__('%s Background', 'slick-menu'), $title_prefix),
				'nav_menus'  => true,
		        'priority'	 => 'low',
		        'context'    => 'advanced',
		        'include' 	 => array(
					'custom' => array($this, $include_function)
				),
				'accordions'  => $this->get_accordions(array(
					'bg-image',
					'bg-pattern',
					'bg-overlay',
					'bg-video'
				)),
		        'fields'     => $this->parent->include_metabox_fields($type, 'level-bg')
			);

			$meta_boxes[] = array(
				'id'         => $this->parent->mb_id($type.'-level-logo'),
		        'title'      => sprintf(esc_html__('%s Logo', 'slick-menu'), $title_prefix),
				'nav_menus'  => true,
		        'priority'	 => 'low',
		        'context'    => 'advanced',
		        'include' 	 => array(
					'custom' => array($this, $include_function)
				),
				'accordions'  => $this->get_accordions(array(
					'general',
					'spacing',
					'border',
					'animations'
				)),
		        'fields'     => $this->parent->include_metabox_fields($type, 'logo')	
			);

			
			$meta_boxes[] = array(
				'id'         => $this->parent->mb_id($type.'-level-back-link'),
		        'title'      => sprintf(esc_html__('%s Back Link', 'slick-menu'), $title_prefix),
				'nav_menus'  => true,
		        'priority'	 => 'low',
		        'context'    => 'advanced',
		        'include' => array(
					'custom' => array($this, $include_function)
				),
				'accordions'  => $this->get_accordions(array(
					'general',
					'font',
					'colors',
					'icons',
					'spacing'
				)),
		        'fields'     => $this->parent->include_metabox_fields($type, 'level-back-link')
			);
		

			$meta_boxes[] = array(
				'id'         => $this->parent->mb_id($type.'-level-title'),
		        'title'      => sprintf(esc_html__('%s Title', 'slick-menu'), $title_prefix),
				'nav_menus'  => true,
		        'priority'	 => 'low',
		        'context'    => 'advanced',
		        'include' 	 => array(
					'custom' => array($this, $include_function)
				),
				'accordions'  => $this->get_accordions(array(
					'general',
					'font',
					'colors',
					'icons',
					'spacing',
					'border',
					'animations'
				)),
		        'fields'     => $this->parent->include_metabox_fields($type, 'level-title')
			);
	
			$meta_boxes[] = array(
				'id'         => $this->parent->mb_id($type.'-level-subtitle'),
		        'title'      => sprintf(esc_html__('%s Sub Title', 'slick-menu'), $title_prefix),
				'nav_menus'  => true,
		        'priority'	 => 'low',
		        'context'    => 'advanced',
		        'include' 	 => array(
					'custom' => array($this, $include_function)
				),
				'accordions'  => $this->get_accordions(array(
					'general',
					'font',
					'colors',
					'spacing',
					'border',
					'animations'
				)),
		        'fields'     => $this->parent->include_metabox_fields($type, 'level-subtitle')
			);
			
			$meta_boxes[] = array(
				'id'         => $this->parent->mb_id($type.'-level-description'),
		        'title'      => sprintf(esc_html__('%s Description', 'slick-menu'), $title_prefix),
				'nav_menus'  => true,
		        'priority'	 => 'low',
		        'context'    => 'advanced',
		        'include' 	 => array(
					'custom' => array($this, $include_function)
				),
				'accordions'  => $this->get_accordions(array(
					'general',
					'font',
					'colors',
					'spacing',
					'border',
					'animations'
				)),
		        'fields'     => $this->parent->include_metabox_fields($type, 'level-description')
			);

			$meta_boxes[] = array(
				'id'         => $this->parent->mb_id($type.'-level-menu'),
		        'title'      => sprintf(esc_html__('%s Nav Menu', 'slick-menu'), $title_prefix),
				'nav_menus'  => true,
		        'priority'	 => 'low',
		        'context'    => 'advanced',
		        'class'		 => 'sm-mb-has-accordions',
		        'include' => array(
					'custom' => array($this, $include_function)
				),
				'accordions'  => $this->get_accordions(array(
					'general',
					'spacing'
				)),
		        'fields'     => $this->parent->include_metabox_fields($type, 'level-menu')
			);
			
			$meta_boxes[] = array(
				'id'         => $this->parent->mb_id($type.'-level-menu-items'),
		        'title'      => sprintf(esc_html__('%s Nav Menu Items', 'slick-menu'), $title_prefix),
				'nav_menus'  => true,
		        'priority'	 => 'low',
		        'context'    => 'advanced',
		        'class'		 => 'sm-mb-has-accordions',
		        'include' => array(
					'custom' => array($this, $include_function)
				),
				'accordions'  => $this->get_accordions(array(
					'general',
					'font',
					'colors',
					'spacing',
					'border',
					'icons',
					'arrows',
					'image',
					'shadow',
					'animations',
					'transforms'
				)),
		        'fields'     => $this->parent->include_metabox_fields($type, 'level-menu-items')
			);
			
			$meta_boxes[] = array(
				'id'         => $this->parent->mb_id($type.'-level-footer'),
		        'title'      => sprintf(esc_html__('%s Footer', 'slick-menu'), $title_prefix),
				'nav_menus'  => true,
		        'priority'	 => 'low',
		        'context'    => 'advanced',
		        'include' => array(
					'custom' => array($this, $include_function)
				),
				'accordions'  => $this->get_accordions(array(
					'general',
					'font',
					'bg-image',
					'bg-pattern',
					'bg-overlay',
					'colors',
					'spacing',
					'animations'
				)),
		        'fields'     => $this->parent->include_metabox_fields($type, 'level-footer')
			);
			

			$meta_boxes[] = array(
				'id'         => $this->parent->mb_id($type.'-wrapper'),
				'title'      => esc_html__('Site Wrapper Settings', 'slick-menu'),
				'nav_menus'  => true,
		        'priority'	 => 'low',
		        'context'    => 'advanced',
		        'include' 	 => array(
					'custom' => array($this, $include_function)
				),
				'accordions'  => $this->get_accordions(array(
					'bg-image',
					'bg-pattern',
					'bg-overlay',
					'bg-video',
					'filter'
				)),
		        'fields'     => $this->parent->include_metabox_fields($type, 'wrapper')	
			);
			

			$meta_boxes[] = array(
				'id'         => $this->parent->mb_id($type.'-content'),
				'title'      => esc_html__('Site Content Settings', 'slick-menu'),
				'nav_menus'  => true,
		        'priority'	 => 'low',
		        'context'    => 'advanced',
		        'include' 	 => array(
					'custom' => array($this, $include_function)
				),
				'accordions'  => $this->get_accordions(array(
					'bg-image',
					'shadow',
					'filter'
				)),
		        'fields'     => $this->parent->include_metabox_fields($type, 'content')	
			);
				
		}	

		$meta_boxes[] = array(
			'id'         => $this->parent->mb_id('menu-trigger'),
			'title'      => esc_html__('Trigger Info', 'slick-menu'),
			'nav_menus'  => true,
	        'priority'	 => 'low',
	        'context'    => 'advanced',
	        'include' 	 => array(
				'custom' => array($this, 'is_push_menu_main')
			),
	        'fields'     => $this->parent->include_metabox_fields('menu', 'trigger')
		);
				
		$meta_boxes = apply_filters('slick_menu_metaboxes', $meta_boxes, $this->parent->mb_field_id());
				
		return $meta_boxes;
		
	}
	
	public function prepend_metabox($metaboxes, $new_metabox) {
		
		$new_metaboxes = array();
		$new_metaboxes[] = $new_metabox;
		
		foreach($metaboxes as $key => $metabox) {

			$new_metaboxes[] = $metabox;
		}
		
		return $new_metaboxes;
	}
	
	public function append_metabox($metaboxes, $new_metabox) {
		
		$metaboxes[] = $new_metabox;
		return $metaboxes;
	}

	public function insert_metabox_after_id($metaboxes, $new_metabox, $after_id) {
		
		$new_metaboxes = array();
		
		foreach($metaboxes as $key => $metabox) {
			
			$new_metaboxes[] = $metabox;
			
			if($metabox['id'] == $this->parent->mb_id($after_id)) {
				$new_metaboxes[] = $new_metabox;
			}
		}
		
		return $new_metaboxes;
	}	

	public function insert_metabox_before_id($metaboxes, $new_metabox, $before_id) {
		
		$new_metaboxes = array();
		
		foreach($metaboxes as $key => $metabox) {
			
			if($metabox['id'] == $this->parent->mb_id($before_id)) {
				$new_metaboxes[] = $new_metabox;
			}
			
			$new_metaboxes[] = $metabox;
		}
		
		return $new_metaboxes;
	}
		
	public function not_push_menu($meta_box = null)
	{
		return !$this->is_push_menu($meta_box);
	}

	public function is_push_menu($meta_box = null)
	{
	
		if(!empty($_REQUEST["action"]) && $_REQUEST["action"] === 'sm_mb_nav_menu_load' && !empty($_REQUEST["menu_id"])) {
			
			$menu_id = intval($_REQUEST["menu_id"]);
			
			return $this->parent->used_as_slickmenu($menu_id);
		}
		
		return false;
	}	
		
	public function is_push_menu_main($meta_box = null)
	{
		if(!empty($_REQUEST["action"]) && $_REQUEST["action"] === 'sm_mb_nav_menu_load' && !empty($_REQUEST["menu_id"]) && empty($_REQUEST["item_id"])) {
			
			$menu_id = intval($_REQUEST["menu_id"]);
			
			return $this->parent->used_as_slickmenu($menu_id);
		}
		
		return false;
	}
	
	public function is_push_menu_item($meta_box = null)
	{
		if(!empty($_REQUEST["action"]) && $_REQUEST["action"] === 'sm_mb_nav_menu_load' && !empty($_REQUEST["menu_id"]) && !empty($_REQUEST["item_id"])) {
			
			$menu_id = intval($_REQUEST["menu_id"]);
			
			return $this->parent->used_as_slickmenu($menu_id);
		}
		
		return false;
	}

	public function is_push_menu_item_with_childs($meta_box = null)
	{
		return $this->is_push_menu_item($meta_box) && $this->has_childs($meta_box);
	}
		
	public function has_childs($meta_box = null)
	{
		
		if(!empty($_REQUEST["menu_id"]) && !empty($_REQUEST["item_id"])) {
			
			$menu_id = intval($_REQUEST["menu_id"]);
			$item_id = intval($_REQUEST["item_id"]);
			
			$cache_key = 'nav-menu-item-children-'.$item_id;
			
			$children = $this->parent->cache->get( $cache_key );
					
			if ( false === $children ) {
			
				$menu_items = wp_get_nav_menu_items($menu_id);	
				$children = slick_menu_get_menu_item_children($item_id, $menu_items, $depth = true);
				
				$this->parent->cache->set($cache_key, $children);
			}
			
			return count($children) > 0;
		}
		
		return false;
	}
	
	
	function get_accordions($ids = array()) {
		
		$accordions = array(
			'general' => array(
				'label' => __( 'General', 'slick-menu' ),
				'icon'  => 'dashicons-admin-generic',
			),
			'font' => array(
				'label' => __( 'Font & Style', 'slick-menu' ),
				'icon'  => 'dashicons-editor-bold',
			),
			'colors' => array(
				'label' => __( 'Colors', 'slick-menu' ),
				'icon'  => 'dashicons-admin-customizer',
			),
			'spacing'  => array(
				'label' => __( 'Spacing', 'slick-menu' ),
				'icon'  => 'dashicons-editor-contract',
			),
			'border'  => array(
				'label' => __( 'Border', 'slick-menu' ),
				'icon'  => 'dashicons-list-view',
			),
			'icons'  => array(
				'label' => __( 'Icon', 'slick-menu' ),
				'icon'  => 'dashicons-admin-site',
			),
			'arrows'  => array(
				'label' => __( 'Arrow', 'slick-menu' ),
				'icon'  => 'dashicons-editor-code',
			),
			'image' => array(
				'label' => __( 'Image', 'slick-menu' ),
				'icon'  => 'dashicons-format-image',
			),
			'bg-image' => array(
				'label' => __( 'Background Color / Image', 'slick-menu' ),
				'icon'  => 'dashicons-format-image',
			),
			'bg-pattern' => array(
				'label' => __( 'Background Pattern', 'slick-menu' ),
				'icon'  => 'dashicons-image-filter',
			),
			'bg-overlay' => array(
				'label' => __( 'Background Overlay', 'slick-menu' ),
				'icon'  => 'dashicons-format-gallery',
			),
			'bg-video' => array(
				'label' => __( 'Background Video', 'slick-menu' ),
				'icon'  => 'dashicons-video-alt3',
			),
			'filter' => array(
				'label' => __( 'Filter Effect', 'slick-menu' ),
				'icon'	=> 'dashicons-filter',
			),
			'shadow' => array(
				'label' => __( 'Box Shadow', 'slick-menu' ),
				'icon'	=> 'dashicons-admin-page',
			),
			'animations'  => array(
				'label' => __( 'Animations', 'slick-menu' ),
				'icon'  => 'dashicons-editor-expand',
			),	
			'transforms'  => array(
				'label' => __( 'Transforms', 'slick-menu' ),
				'icon'  => 'dashicons-admin-settings',
			),			
		);
		

		$accordions = apply_filters('slick_menu_nav_accordions', $accordions);
		
		$return = array();
		foreach($ids as $id) {
			
			if(!empty($accordions[$id])) {
				$return[$id] = $accordions[$id];
			}
		}
		
		return $return;
	}
	
	function get_accordion($id) {
		
		$accordions = $this->get_accordions(array($id));
		
		return $accordions[$id];
	}
	
 	function item_title( $title, $item, $args, $depth  ) {
 	
 		$title = $this->add_menu_item_icon($title, $item->ID);
 		
 		return $title;
 	}

	function add_menu_item_icon( $title, $id ) {
	
		if(strpos($title, 'sm-title-wrap') !== false) {
			return $title;
		}
		
		$before_title_wrap = apply_filters('slick_menu_item_title_wrap_before', '', $id, $title, 'before_wrap');
		$before_inner_title_wrap = apply_filters('slick_menu_item_title_inner_wrap_before', '', $id, $title, 'before_inner_wrap');
		$before_title = apply_filters('slick_menu_item_title_before', '', $id, $title, 'before');
		
		$after_title = apply_filters('slick_menu_item_title_after', '', $id, $title, 'after');
		$after_inner_title_wrap = apply_filters('slick_menu_item_title_inner_wrap_after', '', $id, $title, 'after_inner_wrap');
		$after_title_wrap = apply_filters('slick_menu_item_title_wrap_after', '', $id, $title, 'after_wrap');
		
		$title_wrap_class = apply_filters('slick_menu_item_title_class', array('sm-title-wrap'), $id, $title);
		$title_inner_wrap_class = apply_filters('slick_menu_item_title_inner_class', array('sm-title-inner-wrap'), $id, $title);
		$title_inner_wrap_span_class = apply_filters('slick_menu_item_title_inner_span_class', array(), $id, $title);

		if(strpos($after_title, 'sm-arrow') !== false) {
			$before_title = '';
		}
		
		$earlyTitleReturn = sprintf(
			'%s
			<span class="%s">
				%s
				<span class="%s">
					%s
					<span class="%s">%s</span>
					%s
				</span>
				%s
			</span>
			%s', 
			$before_title_wrap, 
			slick_menu_class_string($title_wrap_class),
			$before_inner_title_wrap, 
			slick_menu_class_string($title_inner_wrap_class),
			$before_title,
			slick_menu_class_string($title_inner_wrap_span_class),
			$title,
			$after_title,
			$after_inner_title_wrap,
			$after_title_wrap
		);
		
		$meta = SM_Icons_Meta::get( 
			null, 
			null,
			$this->parent->mb_field_id('menu-item-icon'),
			$id 
		);
	
		if(empty($meta["type"])) {
			return apply_filters( 'slick_menu_icons_item_title', $earlyTitleReturn, $id, $meta, $title );
		}	
			
		$meta["hide_label"] = (bool)$this->parent->get_menu_item_option('menu-item-hide-label', $id);
		$meta["position"] = $this->parent->get_menu_item_option('menu-item-icon-position', $id);
		
		if(empty($meta["position"])) {
			$meta["position"] = 'before';
		}

		$icon = SM_Icons_Front_End::get_icon($meta);

		if ( empty( $icon ) ) {
			return apply_filters( 'slick_menu_icons_item_title', $earlyTitleReturn, $id, $meta, $title );
		}

		$title_class   = ! empty( $meta["hide_label"] ) ? SM_Icons_Front_End::$hidden_label_class : '';
		$title_class .= ' '.slick_menu_class_string($title_inner_wrap_span_class);
		
		$title_wrapped = sprintf(
			'<span%s>%s</span>',
			( ! empty( $title_class ) ) ? sprintf( ' class="%s"', esc_attr( $title_class ) ) : '',
			$title
		);
		
		$icon = str_replace("_mi", "sm-icon _mi", $icon);

		if ( 'after' === $meta["position"] || 'below' === $meta["position"] ) {
			$title_with_icon = "{$title_wrapped}{$icon}";
		} else {
			$title_with_icon = "{$icon}{$title_wrapped}";
		}
		
		$title_with_icon = sprintf(
			'%s
			<span class="%s">
				%s
				<span class="%s">
					%s
					%s
					%s
				</span>
				%s
			</span>
			%s', 
			$before_title_wrap, 
			slick_menu_class_string($title_wrap_class),
			$before_inner_title_wrap, 
			slick_menu_class_string($title_inner_wrap_class),
			$before_title,
			$title_with_icon,
			$after_title,
			$after_inner_title_wrap,
			$after_title_wrap
		);
		
		/**
		 * Allow plugins/themes to override menu item markup
		 *
		 * @since 0.8.0
		 *
		 * @param string  $title_with_icon Menu item markup after the icon is added.
		 * @param integer $id              Menu item ID.
		 * @param array   $meta            Menu item metadata values.
		 * @param string  $title           Original menu item title.
		 *
		 * @return string
		 */
		$title_with_icon = apply_filters( 'slick_menu_icons_item_title', $title_with_icon, $id, $meta, $title );

		return $title_with_icon;
	}
		
	function link_attributes( $atts, $item, $args ) {
		
		$trigger_menu_key = $this->parent->mb_field_id("trigger-menu");
		$menu_id_key = $this->parent->mb_field_id("menu-id");
		
		if(!empty($item->{$trigger_menu_key}) && !empty($item->{$menu_id_key})) {
		
	    	$class = 'sm-trigger '.$this->parent->get_trigger_class($item->{$menu_id_key});
	    	if(empty($atts['class'])) {
		    	$atts['class'] = $class;
	    	}else{
		    	$atts['class'] .= ' '.$class;
	    	}
	    }
	 
	    return $atts;
	}

		
	function flush_cache() {
		
		$this->parent->pcache->flush();
	}


	/**
	 * Main Slick_Menu_Nav Instance
	 *
	 * Ensures only one instance of Slick_Menu_Nav is loaded or can be loaded.
	 *
	 * @since 1.0.0
	 * @static
	 * @see Slick_Menu()
	 * @return Main Slick_Menu_Nav instance
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


