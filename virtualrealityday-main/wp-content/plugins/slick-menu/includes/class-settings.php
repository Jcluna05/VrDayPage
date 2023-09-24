<?php

if ( ! defined( 'ABSPATH' ) ) exit;

class Slick_Menu_Settings {

	/**
	 * The single instance of Slick_Menu_Settings.
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

	public $option_name = null;
	public $parent_menu = null;

	public function __construct ( $parent ) {
		
		$this->parent = &$parent;
		$this->parent->settings = &$this;
		
		$this->option_name = $this->parent->plugin_slug('settings');
		$this->parent_menu = $this->parent->plugin_slug();

		add_filter( 'sm_mb_settings_pages', array($this, 'options_page' ));
		add_filter( 'sm_rwmb_meta_boxes', array($this, 'options_metaboxes' ));
		add_filter( 'plugin_action_links_' . plugin_basename( $this->parent->plugin_file() ), array($this, 'action_link' ));

		if ( !empty( $_POST['submit'] ) )
		{
			$this->parent->pcache->flush();
		}
	}


	function action_link( $links ) {
	   $links[] = '<a href="'. esc_url( $this->parent->plugin_url('settings') ) .'">'.esc_html__('Global Settings', 'slick-menu').'</a>';
	   return $links;
	}
	
	function options_page( $settings_pages )
	{
		$settings_pages[] = array(
			'id'          => $this->option_name,
			'option_name' => $this->option_name,
			'menu_title'  => __( 'Global Settings', 'slick-menu' ),
			'page_title'  => __( 'Slick Menu Global Settings', 'slick-menu' ),
			'parent'      => $this->parent_menu,
			'context'    => 'advanced',
			'columns'	  => 1,			

			'style'		  => 'no-boxes',
			'tabs'        => array(
				'global' => __( 'Global Settings', 'slick-menu' ),
				'wrapper' => __( 'Wrapper Design', 'slick-menu' ),
				'hamburgers' => __( 'Hamburger Triggers', 'slick-menu' )
			)
		
		);
		return $settings_pages;
	}
	
	
	public function get_setting($key) {
		
		$settings = $this->get_settings();

		if ( isset( $settings[$key] ) )
		{
		    return $settings[$key];
		}
		
		return null;
	}

	public function get_settings() {
		
		$settings = $this->parent->pcache->get('global-settings');
		
		if ( false === $settings ) {
			
			$options = get_option( $this->option_name );
			$settings = array();
			
			if(!empty($options)) {
				foreach($options as $key => $val) {
					
					$key = str_replace($this->parent->_metabox_field_prefix, "", $key);
					$settings[$key] = $val;
					
					if(!empty($settings[$key]['image']) && !empty($settings[$key]['image'][0])) {
						$settings[$key]['image_url'] = wp_get_attachment_url($settings[$key]['image'][0]);
					}
				}

				$this->parent->pcache->set('global-settings', $settings);
			}
			
		}	

		return $settings;
	}
	
	
	/**
	 * Initialise settings
	 * @return void
	 */
	public function options_metaboxes ($meta_boxes) {

		$meta_boxes[] = array(
			'id'             => $this->parent->mb_id('settings-global'),
			'title'          => __( 'Global Settings', 'slick-menu' ),
			'settings_pages' => $this->option_name,
			'tab'			 => 'global',
			'fields'         => $this->parent->include_metabox_fields('settings', 'global')
		);

		$meta_boxes[] = array(
			'id'             => $this->parent->mb_id('settings-wrapper'),
			'title'          => __( 'Wrapper Design', 'slick-menu' ),
			'settings_pages' => $this->option_name,
			'tab'			 => 'wrapper',
			'fields'         => $this->parent->include_metabox_fields('settings', 'wrapper')
		);
		
		$meta_boxes[] = array(
			'id'             => $this->parent->mb_id('settings-hamburgers-heading'),
			'title'          => __( 'You can create multiple slick menus, however you can only create 2 hamburger triggers (Top Left / Top Right) corners.', 'slick-menu' ),
			'settings_pages' => $this->option_name,
			'tab'			 => 'hamburgers',
			'tab_wrapper' => true,
			'fields'         => array(
				
				array(
					'id' 			=> "heading",
					'name'			=> esc_html__( 'Fixed Hamburger Triggers', 'slick-menu' ),
					'desc'			=> esc_html__( 'You can create multiple slick menus, however you can only create 2 hamburger triggers (Top Left / Top Right) corners.', 'slick-menu' ),
					'type'			=> 'heading',
					'tab'  => 'top-left'
				),
			)
		);
		
		$meta_boxes[] = array(
			'id'             => $this->parent->mb_id('settings-hamburgers'),
			'title'          => __( 'Hamburger Triggers', 'slick-menu' ),
			'settings_pages' => $this->option_name,
			'tab'			 => 'hamburgers',
			'tabs'      => array(
				'top-left' => array(
					'label' => __( 'Top Left Trigger', 'rwmb' )
				),
				'top-right'  => array(
					'label' => __( 'Top Right Trigger', 'rwmb' )
				)
			),
			'tab_style' => 'left',
			'tab_wrapper' => true,
			'fields'         => $this->parent->include_metabox_fields('settings', 'hamburgers')
		);
				
		return $meta_boxes;
	
	}



	/**
	 * Main Slick_Menu_Settings Instance
	 *
	 * Ensures only one instance of Slick_Menu_Settings is loaded or can be loaded.
	 *
	 * @since 1.0.0
	 * @static
	 * @see Slick_Menu()
	 * @return Main Slick_Menu_Settings instance
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
