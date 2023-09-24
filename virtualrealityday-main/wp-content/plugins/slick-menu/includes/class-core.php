<?php

if ( ! defined( 'ABSPATH' ) ) exit;

class Slick_Menu {

	/**
	 * The single instance of Slick_Menu.
	 * @var 	object
	 * @access  private
	 * @since 	1.0.0
	 */
	private static $_instance = null;

	/**
	 * Migration class object
	 * @var     object
	 * @access  public
	 * @since   1.0.0
	 */
	public $migration = null;

	/**
	 * Admin class object
	 * @var     object
	 * @access  public
	 * @since   1.0.0
	 */
	public $admin = null;

	/**
	 * Settings class object
	 * @var     object
	 * @access  public
	 * @since   1.0.0
	 */
	public $settings = null;

	/**
	 * Nav class object
	 * @var     object
	 * @access  public
	 * @since   1.0.0
	 */
	public $nav = null;

	/**
	 * Demo class object
	 * @var     object
	 * @access  public
	 * @since   1.0.0
	 */
	public $demo = null;
		
	/**
	 * Cache class object
	 * @var     object
	 * @access  public
	 * @since   1.0.0
	 */
	public $cache = null;

	/**
	 * PCache class object
	 * @var     object
	 * @access  public
	 * @since   1.0.0
	 */
	public $pcache = null;

	/**
	 * Menu Output class object
	 * @var     object
	 * @access  public
	 * @since   1.0.0
	 */
	public $output = null;
	
	/**
	 * Styles Output class object
	 * @var     object
	 * @access  public
	 * @since   1.0.0
	 */
	public $styles = null;

	/**
	 * The version number.
	 * @var     string
	 * @access  public
	 * @since   1.0.0
	 */
	public $_version;

	/**
	 * The token.
	 * @var     string
	 * @access  public
	 * @since   1.0.0
	 */
	public $_token;

    /**
     * The plugin url.
     * @var     string
     * @access  public
     * @since   1.0.0
     */
    public $url;

	/**
	 * The plugin assets directory.
	 * @var     string
	 * @access  public
	 * @since   1.0.0
	 */
	public $assets_dir;

	/**
	 * The plugin assets URL.
	 * @var     string
	 * @access  public
	 * @since   1.0.0
	 */
	public $assets_url;

	/**
	 * Suffix for Javascripts.
	 * @var     string
	 * @access  public
	 * @since   1.0.0
	 */
	public $script_suffix;

    /**
     * Suffix for rtl styles.
     * @var     string
     * @access  public
     * @since   1.0.0
     */
    public $rtl_suffix;

	/**
	 * Global Menus
	 * @var     array
	 * @access  public
	 * @since   1.0.0
	 */
	public $global_options;

	/**
	 * Active Extensions
	 * @var     array of Active Extensions (reference)
	 * @access  public
	 * @since   1.0.0
	 */
	public $active_extensions;
	
	/**
	 * Constructor function.
	 * @access  public
	 * @since   1.0.0
	 * @return  void
	 */
	public function __construct ( $plugin ) {

        $this->plugin = $plugin;

        $this->_token = $this->plugin_slug();

		$this->_metabox_prefix = $this->_token.'-metabox-';
		$this->_metabox_field_prefix = $this->_token.'-';
		
		// Load plugin environment variables
        $this->url = esc_url( trailingslashit( plugins_url( '', $this->plugin_file() )));
        $this->assets_dir = trailingslashit( $this->plugin_dir() ) . 'assets';
		$this->assets_url = esc_url( trailingslashit( plugins_url( '/assets', $this->plugin_file() ) ) );

		$this->script_suffix = defined( 'SM_SCRIPT_DEBUG' ) && SM_SCRIPT_DEBUG ? '' : '.min';
		$this->debug = defined('WP_DEBUG') && WP_DEBUG;
		$this->sm_debug = defined('SM_DEBUG') && SM_DEBUG;
		$this->print_stats = defined('SM_PRINT_STATS') && SM_PRINT_STATS;
		
		$this->filterMenuOptions = array();

		$this->set_elapsed_time();
		
		register_activation_hook( $this->plugin_file(), array( $this, 'install' ) );

		$this->actions();
		$this->filters();

				
	} // End __construct ()

    /**
     * The plugin info
     *
     * @since     1.0.0
     * @return    object    The plugin info.
     */
    public function plugin() {

        return $this->plugin;
    }

    /**
     * The name of the plugin
     *
     * @since     1.0.0
     * @return    string    The name of the plugin.
     */
    public function plugin_name() {

        return $this->plugin()->name;
    }

    /**
     * The menu name of the plugin
     *
     * @since     1.0.0
     * @return    string    The menu name of the plugin.
     */
    public function plugin_menu_name() {

        return $this->plugin()->menu_name;
    }

    /**
     * The ID of the plugin used to uniquely identify it within the context of
     * WordPress and to define internationalization functionality.
     *
     * @since     1.0.0
     * @return    string    The name of the plugin.
     */
    public function plugin_slug($section = '') {

        return $this->plugin()->slug.(!empty($section) ? '-'.$section : '');
    }

    /**
     * The dashicon of the plugin
     *
     * @since     1.0.0
     * @return    string    The name of the plugin.
     */
    public function plugin_icon() {

        return $this->plugin()->icon;
    }

    /**
     * Retrieve the version number of the plugin.
     *
     * @since     1.0.0
     * @return    string    The version number of the plugin.
     */
    public function plugin_version() {

        return $this->plugin()->version;
    }

    /**
     * Retrieve the plugin marketplace
     *
     * @since     1.0.0
     * @return    string    The plugin marketplace.
     */
    public function plugin_market() {

        return $this->plugin()->market;
    }

    /**
     * The plugin file
     *
     * @since     1.0.0
     * @return    string    The plugin file.
     */
    public function plugin_file() {

        return $this->plugin()->file;
    }

    /**
     * The plugin directory
     *
     * @since     1.0.0
     * @return    string    The plugin directory.
     */
    public function plugin_dir() {

        return dirname( $this->plugin_file() );
    }

    /**
     * The plugin path
     *
     * @since     1.0.0
     * @return    string    The plugin path.
     */
    public function plugin_path($dir = null, $file = null) {
		
		$path = plugin_dir_path( $this->plugin_file() );
		
		if(!empty($dir)) {
			$path .= $dir."/";
		}
		
		if(!empty($file)) {
			$path .= $file;
		}
		
		return $path;
	}
		
	public function plugin_url($section = '', $params = array()) {
		
		$url = admin_url('admin.php?page='.$this->plugin_slug($section));

		if(!empty($params)) {
			$url = add_query_arg( $params, $url );
		}
		
		return $url;
	}

    public function backend() {

        return Slick_Menu('admin');
    }

    /**
     * Get Freemius Function
     *
     * @since     1.0.0
     * @return    Object
     */
    public function fs() {

        return xt_slickmenu_fs();
    }

    public function register_extension(&$instance) {
		
		$this->active_extensions[$instance->_token] = $instance;
	}

	public function extensions_exists($extension_id) {
		
		return !empty($this->active_extensions[$extension_id]);
	}
	
	public function extension($extension_id) {
		
		return $this->active_extensions[$extension_id];
	}
	
	public function get_active_extensions() {
		
		return $this->active_extensions;
	}

	public function actions() {
		
		add_action( 'plugins_loaded', array( $this, 'load_localisation' ), 0 );
		add_action( 'init', array( $this, 'check_ajax_requests' ), 20 );
        add_action( 'init', array( $this, 'set_rtl_suffix' ), 1 );
        add_action( 'admin_init', array( $this, 'set_rtl_suffix' ), 1 );

		// Load frontend JS & CSS

        add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_styles' ), 10 );
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ), 20 );
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_theme_fixes' ), 30 );
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_dynamic_styles' ), PHP_INT_MAX );
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_dynamic_styles' ), PHP_INT_MAX );

		add_action(	'sm_mb_nav_menu_saved', array($this, 'check_always_visible_on_save'), 10, 4);

		// Load admin JS & CSS
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts' ), 10, 1 );
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_styles' ), 10, 1 );

		add_action('wp_footer', array($this, 'show_elapsed_time'));
		add_action('in_admin_footer', array($this, 'show_elapsed_time'));
		
		add_action('in_admin_header', array($this, 'print_admin_loading'));

	}

	public function check_ajax_requests() {
		
		if(slick_menu_is_ajax_request() && !defined( 'DOING_AJAX' )) {
			
			define( 'DOING_AJAX', true);
		}
		
		if(slick_menu_is_ajax_action('dynamic_styles')) {
	    	require_once $this->assets_dir . '/css/dynamic-css.php';
			exit;
	    }
	}
	
	public function filters() {

		add_filter( 'body_class', array($this, 'body_class'), 10, 1 );
		add_filter( 'admin_body_class', array($this, 'admin_body_class'), 10, 1 );
	}

    function write_changelog($plugin = null) {


    }

    public function plugin_page_hook($section = '') {
		
		$hook = $this->plugin_slug();
		if(empty($section)) {
			$hook = 'toplevel_page_'.$hook;
		}else{
			$hook .= '_page_'.$this->plugin_slug($section);
		}
		return $hook;
	}


	/**
	 * Load plugin localisation
	 * @access  public
	 * @since   1.0.0
	 * @return  void
	 */
	public function load_localisation () {
		load_plugin_textdomain( 'slick-menu', false, dirname( plugin_basename( $this->plugin_file() ) ) . '/lang/' );
	} // End load_localisation ()

    /**
     * Set RTL Suffix
     * @access  public
     * @since   1.0.0
     * @return void
     */
    public function set_rtl_suffix() {

        $this->rtl_suffix = is_rtl() ? '-rtl' : '';
    }

	/**
	 * Load frontend CSS.
	 * @access  public
	 * @since   1.0.0
	 * @return void
	 */
	public function enqueue_styles () {

        wp_enqueue_script( 'wpb_composer_front_js' );
		wp_enqueue_style( 'js_composer_front' );
		wp_enqueue_style( 'js_composer_custom_css' );
    
		wp_register_style( $this->plugin_slug('animate'), esc_url( $this->assets_url ) . 'vendors/animate/animate.css', array(), $this->plugin_version() );
		wp_enqueue_style( $this->plugin_slug('animate') );

		wp_register_style( $this->plugin_slug('slickmenu'), esc_url( $this->assets_url ) . 'css/slickmenu' . $this->script_suffix . '.css', array(), $this->plugin_version() );
		wp_enqueue_style( $this->plugin_slug('slickmenu') );

			
		// DEBUG STYLES		
		if($this->sm_debug) {		
			wp_register_style( $this->plugin_slug('debug'.$this->rtl_suffix), esc_url( $this->assets_url ) . 'css/debug' . $this->rtl_suffix.$this->script_suffix . '.css', array(), $this->plugin_version() );
			wp_enqueue_style( $this->plugin_slug('debug'.$this->rtl_suffix) );
		}
		
	} // End enqueue_styles ()

	
	/**
	 * Load frontend Dynamic CSS.
	 * @access  public
	 * @since   1.0.0
	 * @return void
	 */
	public function enqueue_dynamic_styles () {

		if(is_customize_preview()) {
			
			wp_enqueue_style($this->plugin_slug('dynamic-global'), slick_menu_get_ajax_link('dynamic_styles', array('global' => 1)), array(),  $this->plugin_version() );
			
			$menus = $this->get_menus();
			foreach($menus as $menu) {
				wp_enqueue_style($this->plugin_slug('dynamic-'.$menu->term_id), slick_menu_get_ajax_link('dynamic_styles', array('menu_id' => $menu->term_id)), array(),  $this->plugin_version() );
			}
			
		}else{
			
			$inline_dynamic_styles = (bool)$this->get_setting('internal-dynamic-styles');
			
			$dynamic_styles = $this->styles->renderAll(true, false);
			
			$fonts_link = $this->styles->getFontsLink();
			if(!empty($fonts_link)) {
				wp_enqueue_style($this->plugin_slug('fonts'), $fonts_link, array(), $this->plugin_version() );
			}
			
			if($inline_dynamic_styles) {
				
				wp_add_inline_style( 
					$this->plugin_slug('slickmenu'), 
					$dynamic_styles
				);
			
			}else{
				
				wp_enqueue_style($this->plugin_slug('dynamic'), slick_menu_get_ajax_link('dynamic_styles'), array(),  $this->plugin_version() );
			}
		}
    
	} // End enqueue_styles ()
	

	/**
	 * Load frontend Javascript.
	 * @access  public
	 * @since   1.0.0
	 * @return  void
	 */
	public function enqueue_scripts () {
	
		wp_register_script( $this->plugin_slug('modernizr'), esc_url( $this->assets_url ) . 'vendors/modernizr/modernizr.js', array( 'jquery' ), $this->plugin_version(), true );
		wp_enqueue_script( $this->plugin_slug('modernizr') );
		
		wp_register_script( $this->plugin_slug('tween-max'), esc_url( $this->assets_url ) . 'vendors/greensock/TweenMax.min.js', array(), $this->plugin_version(), true );
		wp_enqueue_script( $this->plugin_slug('tween-max') );

		wp_register_script( $this->plugin_slug('gsap-scrollto'), esc_url( $this->assets_url ) . 'vendors/greensock/ScrollToPlugin.min.js', array( $this->plugin_slug('tween-max') ), $this->plugin_version(), true );
		wp_enqueue_script( $this->plugin_slug('gsap-scrollto') );

        wp_register_script( $this->plugin_slug('body-scroll-lock'), esc_url( $this->assets_url ) . 'vendors/body-scroll-lock/body-scroll-lock' . $this->script_suffix . '.js', array(), $this->plugin_version(), true );
        wp_enqueue_script( $this->plugin_slug('body-scroll-lock') );

		wp_register_script( $this->plugin_slug('utils'), esc_url( $this->assets_url ) . 'js/utils' . $this->script_suffix . '.js', array( 'jquery' ), $this->plugin_version(), true );
		wp_enqueue_script( $this->plugin_slug('utils') );

		wp_register_script( $this->plugin_slug('slickmenu'), esc_url( $this->assets_url ) . 'js/slickmenu' . $this->script_suffix . '.js', array( 'jquery', $this->plugin_slug('utils') ), $this->plugin_version(), true );
		wp_enqueue_script( $this->plugin_slug('slickmenu') );
		
		wp_register_script( $this->plugin_slug('frontend'), esc_url( $this->assets_url ) . 'js/frontend' . $this->script_suffix . '.js', array( 'jquery', $this->plugin_slug('utils') ), $this->plugin_version(), true );

	
	} // End enqueue_scripts ()


	/**
	 * Load frontend Theme Fixes.
	 * @access  public
	 * @since   1.0.0
	 * @return void
	 */
	public function enqueue_theme_fixes () {
		
		$theme_name = get_template();
		
		$theme_fixes = array(
			'bridge' => array('css', 'js'),
			'kinetika' => array('css', 'js'),
			'jupiter' => array('css', 'js'),
			'betheme' => array('js'),
			'berg' => array('css')
		);
	
		if(!empty($theme_fixes[$theme_name])) {
			
			foreach($theme_fixes[$theme_name] as $type) {
				
				if($type == 'css') {
				
					wp_register_style( $this->plugin_slug('slickmenu'.$theme_name.$this->rtl_suffix), esc_url( $this->assets_url ) . 'theme-fix/css/' . $theme_name . $this->rtl_suffix.$this->script_suffix . '.css', $this->plugin_slug('slickmenu'), $this->plugin_version() );
					wp_enqueue_style( $this->plugin_slug('slickmenu'.$theme_name.$this->rtl_suffix) );
					
				}else{
				
					wp_register_script( $this->plugin_slug('slickmenu'.$theme_name), esc_url( $this->assets_url ) . 'theme-fix/js/' . $theme_name . $this->script_suffix . '.js', array( $this->plugin_slug('slickmenu') ), $this->plugin_version(), true );
					wp_enqueue_script( $this->plugin_slug('slickmenu'.$theme_name) );
				}
			
			}
	
		}

	} // End enqueue_theme_fixes ()
	
	
	
	/**
	 * Load admin CSS.
	 * @access  public
	 * @since   1.0.0
	 * @return  void
	 */
	public function admin_enqueue_styles ( $hook = '' ) {

		wp_register_style( $this->plugin_slug('animate'), esc_url( $this->assets_url ) . 'vendors/animate/animate.css', array(), $this->plugin_version() );
		wp_enqueue_style( $this->plugin_slug('animate') );
						
		wp_register_style( $this->plugin_slug('admin'.$this->rtl_suffix), esc_url( $this->assets_url ) . 'css/admin' . $this->rtl_suffix.$this->script_suffix . '.css', array(), $this->plugin_version() );
		wp_enqueue_style( $this->plugin_slug('admin'.$this->rtl_suffix) );
		
		
		// SETTINGS PAGE
		if($hook == $this->plugin_page_hook('settings')) {
			wp_register_style( $this->plugin_slug('settings'), esc_url( $this->assets_url ) . 'css/settings' . $this->rtl_suffix.$this->script_suffix . '.css', array(), $this->plugin_version() );
			wp_enqueue_style( $this->plugin_slug('settings') );			
		}
		
		// DEBUG STYLES
		if($this->sm_debug || $this->print_stats) {		
			wp_register_style( $this->plugin_slug('debug'.$this->rtl_suffix), esc_url( $this->assets_url ) . 'css/debug' . $this->rtl_suffix.$this->script_suffix . '.css', array(), $this->plugin_version() );
			wp_enqueue_style( $this->plugin_slug('debug'.$this->rtl_suffix) );
		}
		
	} // End admin_enqueue_styles ()

	
	/**
	 * Load admin Dynamic CSS.
	 * @access  public
	 * @since   1.0.0
	 * @return void
	 */
	public function admin_enqueue_dynamic_styles ($hook) {

		if($hook == $this->plugin_page_hook('settings')) {
			
			wp_enqueue_style($this->plugin_slug('dynamic-hamburgers'), slick_menu_get_ajax_link('dynamic_styles', array('hamburgers' => 1)), array(),  $this->plugin_version() );
		}
    
	} // End admin_enqueue_dynamic_styles ()
	
	
	
	/**
	 * Load admin Javascript.
	 * @access  public
	 * @since   1.0.0
	 * @return  void
	 */
	public function admin_enqueue_scripts ( $hook = '' ) {

		$vars = array(
			'ajaxurl' => admin_url('admin-ajax.php'),
			'assets_url' => $this->assets_url,
			'customizer' => is_customize_preview(),
			'metabox_prefix' => $this->_metabox_prefix,
			'field_prefix' => $this->_metabox_field_prefix,
			'settings_url' => $this->plugin_url('settings'),
			'plugin_name' => $this->plugin_name(),
			'plugin_version' => $this->plugin_version(),
			'debug' => $this->debug,
			'sm_debug' => $this->sm_debug,
			'lang' => array(
				'reset_all_confirm' => esc_html__("Are you sure you would like to reset all menu settings to default values ?", 'slick-menu'),
				'reset_group_confirm' => esc_html__("Are you sure you would like to reset all {group_name} settings to default values ?", 'slick-menu'),
				'reset_confirm' => esc_html__("Are you sure you would like to reset this option to the default value ?", 'slick-menu')
			)	
		);

		// DEBUG SCRIPTS
		if($this->sm_debug || $this->print_stats) {		
			wp_register_script( $this->plugin_slug('debug'), esc_url( $this->assets_url ) . 'js/debug' . $this->script_suffix . '.js',  array( 'jquery' ), $this->plugin_version() );
			wp_enqueue_script( $this->plugin_slug('debug') );
		}
			
		// ADMIN GENERAL	
		wp_register_script( $this->plugin_slug('admin'), esc_url( $this->assets_url ) . 'js/admin' . $this->script_suffix . '.js', array( 'jquery' ), $this->plugin_version() );
		wp_localize_script( $this->plugin_slug('admin'), 'SM_VARS', $vars);
		wp_enqueue_script( $this->plugin_slug('admin') );

		
		// SETTINGS PAGE
		if($hook == $this->plugin_page_hook('settings')) {

			wp_register_script( $this->plugin_slug('stickykit'), esc_url( $this->assets_url ) . 'vendors/stickykit/jquery.stickykit.min.js', array( 'jquery' ), $this->plugin_version() );
			wp_enqueue_script( $this->plugin_slug('stickykit') );
			
			wp_register_script( $this->plugin_slug('settings'), esc_url( $this->assets_url ) . 'js/settings' . $this->script_suffix . '.js', array( 'jquery' ), $this->plugin_version() );
			wp_enqueue_script( $this->plugin_slug('settings') );		
		}

				
	} // End admin_enqueue_scripts ()


	function get_menus($args = array()) {
		
		$cache_key = 'sm-menus-'.md5(maybe_serialize($args));

		$menus = $this->pcache->get($cache_key);
		
		if ( false === $menus ) {

			$defaults = array(
				'taxonomy' => 'nav_menu',
				'hide_empty' => false ,
				'meta_key' => $this->mb_field_id('enabled'),
				'meta_value' => 'yes',
				'order' => 'asc',
				'orderby' => 'id',
				'suppress_filters' => 0
			);
			
			$args = array_merge($defaults, $args);
			
			if($this->lang->enabled()) {
				
				add_filter('get_terms', array($this, 'get_terms_filter'), 10, 3); 
			}

			$menus = get_terms($args);
			
			if($this->lang->enabled()) {
				
				remove_filter('get_terms', array($this, 'get_terms_filter'), 10, 3); 
			}

			$this->pcache->set($cache_key, $menus);
		} 
	
		return $menus;
	}
	
	function get_terms_filter($terms, $taxonomies, $args) {
		
		if(!$this->lang->enabled()) {
			return $terms;
		}
	
		$uniques = array();
        foreach($terms as $idx => $term)
        {	
	        if(empty($term->term_id)) {
		        continue;
	        }
			
			$term_trans_id = $this->lang->get_translated_term_id($term->term_id, $term->taxonomy);
			
            if ($term_trans_id != $term->term_id) {
                unset($terms[$idx]);
            }
            
            if(!is_admin() || defined( 'DOING_AJAX' )) {
	            if(isset($uniques[$term_trans_id]))   {
		             unset($terms[$idx]);
	            }else{
		            $uniques[$term_trans_id] = $term;
	            }  
            }
        }
	    
	    return $terms;
	}

	function get_menu_url($menu_id) {
				
		return esc_url(admin_url('/nav-menus.php?action=edit&menu='.absint( $menu_id )));
	}
	
	function get_menu_customizer_url($menu_id) {
		
		return esc_url(admin_url('customize.php?autofocus[panel]=nav_menus&menu='.absint( $menu_id ).'&return='.urlencode('/nav-menus.php?action=edit&menu='.absint( $menu_id ))));
	}
	
	function get_always_visible_menu() {
		
		$menus = $this->get_menus(array(
			'hide_empty' => false ,
			'meta_key' => null,
			'meta_value' => null,
			'meta_query' => array(
				array(
					'key'     => $this->mb_field_id('enabled'),
					'value'   => 'yes',
					'compare' => '=',
				),
				array(
					'key'     => $this->mb_field_id('menu-always-visible'),
					'value'   => '1',
					'compare' => '=',
				),
			)
		));
		
		if(!empty($menus)) {
			return array_shift($menus);
		}
		
		return null;
	}
	
	
	function delete_menu($menu_id) {
			
		$menu_exists = wp_get_nav_menu_object($menu_id);
		
		// If it exists, let's delete it.
		if( !empty($menu_exists)) {
			
		    wp_delete_nav_menu($menu_id);
		    $this->pcache->flush();
		    
		    return true;
		}
		
		return false;
	}
	
	function get_menus_dropdown_options() {
	
		$options = $this->pcache->get('sm-menus-options');
		
		if ( false === $options ) {
			
			$menus = $this->get_menus();
	
			foreach ( $menus as $menu ) {
				
				$position = $this->get_menu_option('menu-position', $menu->term_id);
				$options[$menu->term_id] = $menu->name.' - '.ucfirst($position);
			}
			
			if(!empty($options)) {
				
				ksort($options);
				
				$this->pcache->set('sm-menus-options', $options);
			}
		} 
		
		return $options;
	}
	
	function check_always_visible_on_save($menu_id, $item_id, $target_item, $data) {
		
		if(!$target_item) {
			
			$alwaysVisibleKey = $this->mb_field_id('menu-always-visible');
			
			if(!slick_menu_is_empty($data[$alwaysVisibleKey])) {
				
				$alwaysVisible = (bool)$data[$alwaysVisibleKey];
			
				if($alwaysVisible) {
					
					$menus = $this->get_menus(array(
						'exclude' => $menu_id
					));
					
					$this->update_menu_option('menu-always-visible', "1", $menu_id);	
					
					if(!empty($menus)) {
						foreach($menus as $menu) {
							$this->delete_menu_option('menu-always-visible', $menu->term_id);	
						}
						$this->pcache->flush();
					}
				}
			}
		}
	}

	public function do_output_action($tag, $menu_id, $item_id = null) {
		
		ob_start();
		
		$tag = 'slick_menu_'.$tag;
		
		do_action($tag, $menu_id, $item_id);
		
		return ob_get_clean();
	}

	public function apply_filters($tag, $content, $menu_id, $item_id = null) {
		
		$tag = 'slick_menu_'.$tag;
		
		$content = apply_filters($tag, $content, $menu_id, $item_id);
		
		return $content;
	}
		
	public function get_trigger_selector($menu_id = null, $valueOnly = true) {
		
		return $this->get_trigger_class($menu_id, $valueOnly, true);
	}	

	public function get_trigger_class($menu_id = null, $valueOnly = true, $selector = false) {

		$class = "";
		
		if(empty($menu_id) && !empty($_REQUEST['menu_id'])) {
			$menu_id = intval($_REQUEST['menu_id']);	
		}
		
		if(!empty($menu_id)) {
			
			$class = 'sm-trigger-'.$menu_id;
			
			if($selector) {
				$class = '.'.$class;
			}

			if(empty($valueOnly)) {
				
				$wrap = '<div class="slick-menu-trigger-wrap">';
				$wrap .= $class;
				$wrap .= '</div>';	
				$class = $wrap;
			}	
		}
				
		return $class;
	}
	
	public function get_api_function($menu_id = null, $function = 'toggle', $valueOnly = true) {

		if(empty($menu_id) && !empty($_REQUEST['menu_id'])) {
			$menu_id = intval($_REQUEST['menu_id']);	
		}
				
		if(!empty($menu_id)) {
				
			$function = 'SlickMenu.'.$function.'('.$menu_id.')';
				
			if(empty($valueOnly)) {
				
				$wrap = '<div class="slick-menu-trigger-wrap">';
				$wrap .= $function;
				$wrap .= '</div>';	
				$function = $wrap;
			}				

		}
				
		return $function;
	}
	
	public function get_menu_option_choice_std_label($field_key) {
		
		$cache_key = 'menu-option-choice-std-label-'.$field_key;
		
		$label = $this->cache->get($cache_key);
		
		if( false === $label) {
			
			$label = "";
			$fields = $this->get_metabox_fields('menu');
			
		    foreach ( $fields as $field ) {
			    
			    if($this->mb_field_id($field_key) == $field["id"]) {
				    
				    if(isset($field['std']) && !is_array($field['std'])) {
				    	$value = $field['std'];
				    	
					    if(isset($field['options'][$value])) {
					    	$label = $field['options'][$value];	
					    	break;
					    }
					}    
				}    
		    }
		    
		    $this->cache->set($cache_key, $label);
		}    
		
		return $label;
	}

	public function get_menu_option_choice_label($field_key, $menu_id) {
		
		$cache_key = 'menu-option-choice-label-'.$field_key.'-'.$menu_id;

		$label = $this->cache->get($cache_key);
		
		if( false === $label) {
			
			$label = "";	
			$fields = $this->get_metabox_fields('menu');
			
		    foreach ( $fields as $field ) {
			    
			    if($this->mb_field_id($field_key) == $field["id"]) {
				    
				    $value = $this->get_menu_option($field_key, $menu_id);
				    
				    if(isset($field['options'][$value])) {
				    	$label = $field['options'][$value];	
				    	break;
				    }
				}    
		    }
		    
		    $this->cache->set($cache_key, $label);
		}    
		
		return $label;
	}
	
	public function get_menu_option_choices($field_key) {
		
		$cache_key = 'menu-option-choices-'.$field_key;
		
		$choices = $this->cache->get($cache_key);
		
		if( false === $choices) {
			
			$choices = array();
			$fields = $this->get_metabox_fields('menu');
	
		    foreach ( $fields as $field ) {
			    
			    if($this->mb_field_id($field_key) == $field["id"] && !empty($field['options'])) {
				    
				    $choices = $field['options'];
				    break;
				}    
		    }
		    
		    $this->cache->set($cache_key, $choices);
		}
		
		return $choices;
	}

	
	public function get_metabox_fields($metabox_id, $returnUniqueKeysOnly = false) {
		
		$cache_key = 'metabox-fields-'.md5(maybe_serialize(func_get_args()));
		
		$fields = $this->cache->get($cache_key);
		
		if ( false === $fields ) {
			
			$fields = $this->include_metabox_fields($metabox_id);
			
			if($returnUniqueKeysOnly) {
				$_fields = array();
				foreach($fields as $field) {
					if(isset($field['id'])) {
						$_fields[$field['id']] = $field;
					}
				}
				$fields = $_fields;
			}
			
			$this->cache->set($cache_key, $fields);
		}
		
		return $fields;
	}

	public function include_metabox_fields($metabox_id, $id = null, $params = array()) {

        $cache_key = 'metabox-fields-'.md5(maybe_serialize(func_get_args()));
		
		$fields = $this->cache->get($cache_key);
		
		if ( false === $fields ) {
			
			extract($params);
			
			$prefix = $this->mb_field_id();
			$fields = array();
	
			if(!empty($id)) {
				
				$fields = include( $this->plugin_dir().'/includes/metaboxes/'.$metabox_id.'/'.$id.'.php' );
				
				$this->cache->set($cache_key, $fields);
				
				$fields = apply_filters('slick_menu_metabox_section_fields', $fields, $metabox_id, $id, $prefix);
				
			}else{
	
				$files = glob( $this->plugin_dir().'/includes/metaboxes/'.$metabox_id.'/*.php');
			
				foreach($files as $file) {
					$fields = array_merge_recursive($fields, include( $file ));
				}	
				
				$this->cache->set($cache_key, $fields);
				
				$fields = apply_filters('slick_menu_metabox_fields', $fields, $metabox_id, $prefix);
			}
	
		}
		
		return $fields;	
	}
	
    public function get_menu_items_fields($id, $exclude = array(), $replace_key = array(), $replace_label = array(), $inserts = array()) {
	    		
        $cache_key = 'menu-items-fields-'.md5(maybe_serialize(func_get_args()));

        $item_fields = $this->cache->get($cache_key);
		
		if ( false === $item_fields ) {
			
			$item_fields = array();
			$fields = $this->include_metabox_fields('menu', $id);
			
			// INSERT FIRST
			if(!empty($inserts)) {
				foreach($inserts as $insert) {
					
					$insert_fields = !empty($insert['fields']) ? $insert['fields'] : array();
					$insert_type = !empty($insert['type']) ? $insert['type'] : 'first';
					$insert_key = !empty($insert['key']) ? $insert['key'] : null;
					
					if(!empty($insert_fields) && $insert_type == 'first') {
						foreach($insert_fields as $_field) {
							$this->set_field_inherit_option($_field);
							$item_fields[] = $_field;
						}
					}
				}
			}
							
			foreach($fields as $i => $field) {
				
				if(empty($field['id'])) {
					continue;	
				}
				
				$key = $field['id'];
				
				if(!is_array($exclude) || !in_array($key, $exclude)) {
					
					$exclude_std_removal = array('custom-html', 'slider');
					$field_type = $field['type'];
					$field_remove_std = !in_array($field_type, $exclude_std_removal);
	
					$new_field = $field;
					
					if(!empty($replace_key)) {
						
						$new_field['id'] = str_replace($replace_key[0], $replace_key[1], $new_field['id']);
						
						if(!empty($new_field['visible'][0])) {
							$new_field['visible'][0] = str_replace($replace_key[0], $replace_key[1], $new_field['visible'][0]);
						}	
						if(!empty($new_field['hidden'][0])) {
							$new_field['hidden'][0] = str_replace($replace_key[0], $replace_key[1], $new_field['hidden'][0]);
						}
						
						if(!empty($new_field['fields'])) {
							
							foreach($new_field['fields'] as $subkey => $subfield) {
								
								$subfield_type = $subfield['type'];
								$subfield_remove_std = !in_array($subfield_type, $exclude_std_removal);
								
								$new_field['fields'][$subkey]['id'] = str_replace($replace_key[0], $replace_key[1], $new_field['fields'][$subkey]['id']);
						
								if(!empty($new_field['fields'][$subkey]['visible'][0])) {
									$new_field['fields'][$subkey]['visible'][0] = str_replace($replace_key[0], $replace_key[1], $new_field['fields'][$subkey]['visible'][0]);
								}	
								if(!empty($new_field['fields'][$subkey]['hidden'][0])) {
									$new_field['fields'][$subkey]['hidden'][0] = str_replace($replace_key[0], $replace_key[1], $new_field['fields'][$subkey]['hidden'][0]);
								}
								
								if($subfield_remove_std && isset($new_field['fields'][$subkey]["std"])) {
									unset($new_field['fields'][$subkey]["std"]);
								}
						
								$this->set_field_inherit_option($new_field['fields'][$subkey]);
						
							}
						}
					}
					
					if(!empty($replace_label)) {
						$new_label = str_replace($replace_label[0], $replace_label[1], $new_field['name']);
						$new_field['name'] = esc_html__("$new_label", 'slick-menu');
					}
					
					if($field_remove_std && isset($new_field["std"])) {
						unset($new_field["std"]);
					}
					
					$this->set_field_inherit_option($new_field);
					
					// INSERT BEFORE KEY
					if(!empty($inserts)) {
						foreach($inserts as $insert) {
							
							$insert_fields = !empty($insert['fields']) ? $insert['fields'] : array();
							$insert_type = !empty($insert['type']) ? $insert['type'] : 'first';
							$insert_key = !empty($insert['key']) ? $insert['key'] : null;
							
							if(!empty($insert_fields) && !empty($insert_key) && $insert_key == $key && $insert_type == 'before') {
								foreach($insert_fields as $_field) {
									$this->set_field_inherit_option($_field);
									$item_fields[] = $_field;
								}
							}
						}
					}
					
					// INSERT AT KEY (REPLACE)
					$replaced_field = false;
					if(!empty($inserts)) {
						foreach($inserts as $insert) {
							
							$insert_fields = !empty($insert['fields']) ? $insert['fields'] : array();
							$insert_type = !empty($insert['type']) ? $insert['type'] : 'first';
							$insert_key = !empty($insert['key']) ? $insert['key'] : null;
							
							if(!empty($insert_fields) && !empty($insert_key) && $insert_key == $key && $insert_type == 'replace') {
								foreach($insert_fields as $_field) {
									$this->set_field_inherit_option($_field);
									$item_fields[] = $_field;
								}
								$replaced_field = true;
							}
						
						}
					}
					
					if(!$replaced_field) {
						$item_fields[] = $new_field;
					}
	
					// INSERT AFTER KEY
					if(!empty($inserts)) {
						foreach($inserts as $insert) {
							
							$insert_fields = !empty($insert['fields']) ? $insert['fields'] : array();
							$insert_type = !empty($insert['type']) ? $insert['type'] : 'first';
							$insert_key = !empty($insert['key']) ? $insert['key'] : null;
							
							if(!empty($insert_fields) && !empty($insert_key) && $insert_key == $key && $insert_type == 'after') {
								foreach($insert_fields as $_field) {
									$this->set_field_inherit_option($_field);
									$item_fields[] = $_field;
								}
							}
						
						}
					}

				}
			}
			
			// INSERT LAST
			if(!empty($inserts)) {
				foreach($inserts as $insert) {
					
					$insert_fields = !empty($insert['fields']) ? $insert['fields'] : array();
					$insert_type = !empty($insert['type']) ? $insert['type'] : 'first';
					$insert_key = !empty($insert['key']) ? $insert['key'] : null;
					
					if(!empty($insert_fields) && $insert_type == 'last') {
						foreach($insert_fields as $_field) {
							$this->set_field_inherit_option($_field);
							$item_fields[] = $_field;
						}
					}
				
				}
			}
		
			$this->cache->set($cache_key, $item_fields);	
		}	
		
		return $item_fields;
	}
	
	public function set_field_inherit_option(&$field) {
		
		if(!empty($field['options'])) {
			
			$inherit_option = array('inherit' => esc_html__('Inherit Parent Settings', 'slick-menu'));
			
			if(isset($field['options'][0]['text'])) {
				
				$inherit_option = array(
					array(
					  	"text" => esc_html__("", "slick-menu"),
					    "type" => "",
					  	"children" => array(
				            array(
				                "id" => "",
				                "text" => "Inherit Parent or Main Settings"
				            )
				        )   
				    )
				);
			}
			
			$field['options'] = array_merge($inherit_option, $field['options']);
			$field["std"] = 'inherit';
			
			if($field['type'] == 'radio' || $field['type'] == 'checkbox') {
				$field["inline"] = false;
			}
		}					
	} 
	
	public function mb_id($id = '') {
		
		return $this->_metabox_prefix.$id; 
	}
	
	public function mb_field_id($key = '') {
		
		return $this->_metabox_field_prefix.$key; 
	}

	public function mb_field_key($id = '') {
		
		return str_replace($this->_metabox_field_prefix, "", $id);
	}

		
	public function used_as_slickmenu($menu_id) {
		
		$enabled = $this->cache->get( 'enabed-'.$menu_id );
		
		if ( false === $enabled ) {
			
			$enabled = $this->get_menu_option('enabled', $menu_id);
			
			$this->cache->set( 'enabed-'.$menu_id, $enabled );
		}	

		return ($enabled === "yes") ? true : false; 
	}
	
	
	public function get_menu_mobile_breakpoint($menu_id, $inPixels = false) {
		
		$breakpoint = $this->get_menu_option('mobile-breakpoint', $menu_id);
	
		if(slick_menu_is_empty($breakpoint)) {
			$breakpoint = $this->get_setting('mobile-breakpoint');
		}

		$alwaysVisible = (bool)$this->get_menu_option('menu-always-visible', $menu_id);

		if(!empty($alwaysVisible)) {
			$levelWidth = $this->get_menu_option('level-width', $menu_id);
			$breakpoint = (intval($breakpoint) + intval($levelWidth));
		}
	
		$breakpoint = intval($breakpoint);
		
		if($inPixels) {
			$breakpoint .= 'px';
		}

		return $breakpoint;
	}
	
				
	public function get_setting($key) {
		
		return $this->settings->get_setting($key);
	}
	
	public function get_settings() {
		
		return $this->settings->get_settings();
	}
				
	public function get_menu_option($mkey, $parent_id, $mchild_key = null, $child_id = null, $mchild_parent_key = null, $child_parent_id = null) {
		
        $cache_key = 'menu-option-'.md5(maybe_serialize(func_get_args()));

        $child_parent_id = intval($child_parent_id);
		
		if($child_parent_id === 0) {
			$child_parent_id = $parent_id;
			$mchild_parent_key = $mkey;
		}
		
		$key = $this->mb_field_id($mkey);
		$child_key = $this->mb_field_id($mchild_key);
		$child_parent_key = $this->mb_field_id($mchild_parent_key);
				
		$value = $this->cache->get($cache_key);
		
		if($value === false) {
				
			if(is_null($child_id)) {
				
				$value = get_term_meta($parent_id, $key, true);
				
			}else{	
				
				$value = get_post_meta($child_id, $child_key, true);

				if(slick_menu_is_empty($value) && !is_null($child_parent_id)) {
					
					$value = get_post_meta($child_parent_id, $child_parent_key, true);

					if(slick_menu_is_empty($value))	{
						
						while($child_parent_id !== 0 && $child_parent_id !== false){
							
							$value = get_post_meta($child_parent_id, $child_parent_key, true);

							if(slick_menu_is_empty($value))	{
								$child_parent_id = slick_menu_get_menu_item_parent_id($child_parent_id);
							}else{
								break;
							}	
						}	
					}
				}	

				if(slick_menu_is_empty($value)) {
					
					$value = get_term_meta($parent_id, $key, true);
				}
			}
			
			if(!empty($value['image']) && !empty($value['image'][0])) {
				$value['image_url'] = wp_get_attachment_url($value['image'][0]);
			}
		
			$this->cache->set($cache_key, $value);
		
		}	
		
		$value = apply_filters('slick_menu_get_menu_option', $value, $mkey, $parent_id, $mchild_key, $child_id, $mchild_parent_key, $child_parent_id );

		$value = $this->get_filtered_menu_option($value, $parent_id, $mkey);
		return $value;
	
	}
		
	public function get_filtered_menu_option($value, $object_id, $meta_key) {
	
		if( !empty($this->filterMenuOptions) && !empty($this->filterMenuOptions[$object_id][$meta_key])) {
			$value = $this->filterMenuOptions[$object_id][$meta_key];
		}
		
		return $value;
		
	}
	
	public function get_menu_item_option($mkey, $item_id) {
		
		$cache_key = 'menu-option-'.md5(maybe_serialize(func_get_args()));

		$value = $this->cache->get($cache_key);
		
		if($value === false) {
			
			$key = $this->mb_field_id($mkey);
			
			$value = get_post_meta($item_id, $key, true);
			
			if(!empty($value['image']) && !empty($value['image'][0])) {
				$value['image_url'] = wp_get_attachment_url($value['image'][0]);
			}
			
			$this->cache->set($cache_key, $value);
		}
		
		$value = apply_filters('slick_menu_get_menu_item_option', $value, $mkey, $item_id);

		return $value;
	
	}

	public function get_all_menu_options($menu_id) {

        $cache_key = 'menu-options-'.$menu_id;

		$options = $this->pcache->get($cache_key);
		
		if( false === $options) {
			
			$metadata = get_metadata('term', $menu_id);
			$options = array();
			
			foreach ( $metadata as $id => $meta ) {
			    $key = $this->mb_field_key($id);
			    if(is_array($meta) && count($meta) > 0) {
				    $meta = $meta[0];
			    }
			    $options[$key] = maybe_unserialize($meta);
			    
				if(!empty($options[$key]['image']) && !empty($options[$key]['image'][0])) {
					$options[$key]['image_url'] = wp_get_attachment_url($options[$key]['image'][0]);
				}
			}
    
		    $this->pcache->set($cache_key, $options);
		    
		} 
		
		$options = $this->get_all_filtered_menu_options($options, $menu_id);
		
		return $options;
	}

	public function get_all_menu_item_options($item_id) {
		
		$options = $this->pcache->get('menu-item-options-'.$item_id);
		
		if( false === $options) {

			$metadata = get_metadata('post', $item_id);
			$options = array();
			
			foreach ( $metadata as $id => $meta ) {
			    $key = $this->mb_field_key($id);
			    if(is_array($meta) && count($meta) > 0) {
				    $meta = $meta[0];
			    }
			    $options[$key] = maybe_unserialize($meta);
			}
	    
		    $this->pcache->set('menu-item-options-'.$item_id, $options);
		    
		} 
		
		return $options;
	}

	public function get_global_menus_options() {

		$global_options = $this->pcache->get('sm-menus-global-options');
		
		if ( false === $global_options ) {
			
			$global_options = array();
			
			$menus = $this->get_menus();
		
			foreach($menus as $menu) {
				
				$options = $this->get_all_menu_options($menu->term_id);
	
				$global_options[$menu->term_id] = $options;
			}
			
			$this->pcache->set('sm-menus-global-options', $global_options);
		}
		
		$this->get_filtered_global_menus_options($global_options);
		
		return $global_options;

	}

	
	public function get_all_filtered_menu_options($options, $object_id) {
	
		if( !empty($this->filterMenuOptions) && !empty($this->filterMenuOptions[$object_id])) {
			
			$options = array_merge($options, $this->filterMenuOptions[$object_id]);
		}
		
		return $options;
		
	}
			
	public function get_filtered_global_menus_options($global_options) {
	
		if( !empty($this->filterMenuOptions)) {
			
			foreach($global_options as $menu_id => $options) {
				if(!empty($this->filterMenuOptions[$menu_id])) {
					$global_options[$menu_id] = array_merge($global_options[$menu_id], $this->filterMenuOptions[$menu_id]);
				}
			}
		}
		
		return $global_options;
	}
		
	
	public function update_menu_option($mkey, $value, $menu_id) {
		
		$key = $this->mb_field_id($mkey);

		update_term_meta($menu_id, $key, $value);
	}
	
	public function update_menu_item_option($mkey, $value, $item_id) {

		$key = $this->mb_field_id($mkey);

		update_post_meta($item_id, $key, $value);
	}
	
	public function update_all_menu_options($menu_id, $options) {

	    foreach ( $options as $id => $value ) {
		    
		   	$this->update_menu_option( $id, $value, $menu_id);
	    }
	}	
	
	public function update_all_menu_item_options($item_id, $options) {
			
	    foreach ( $options as $id => $value ) {
		    
		   	$this->update_menu_item_option( $id, $value, $item_id);
	    }
	}
	
	public function delete_menu_option($mkey, $menu_id) {
		
		$key = $this->mb_field_id($mkey);

		delete_term_meta($menu_id, $key);
	}
	
	public function delete_menu_item_option($mkey, $item_id) {

		$key = $this->mb_field_id($mkey);

		delete_post_meta($item_id, $key);
	}
	
	public function reset_all_menu_options($menu_id) {
	
		$fields = $this->get_metabox_fields('menu');
	
	    foreach ( $fields as $field ) {
		    
			$key = $this->mb_field_key($field['id']);
			
		    if(!isset($field['std'])) {
				$field['std'] = '';
			}
		    
		    $this->update_menu_option($key, $field['std'], $menu_id);
	    }
	    
	    $this->update_menu_option('saved', '1', $menu_id);

	}
	
	public function reset_all_menu_items_options($menu_id) {

		$item_fields = $this->get_metabox_fields('menu-item');
		$menu_items = get_objects_in_term( $menu_id, 'nav_menu' );
		
	    foreach ( (array) $menu_items as $key => $menu_item ) {
	        
	        foreach ( $item_fields as $field ) {
		       		   
		       	$key = $this->mb_field_key($field['id']);
			    $this->delete_menu_item_option($key, $menu_item);
		     
		    }
	    }
	}
	
	function move_menu_option($key1, $key2, $menuItems = false) {
		
		$menus = $this->get_menus();
		foreach($menus as $menu) {
			
			$value = $this->get_menu_option($key1, $menu->term_id);
			$this->update_menu_option($key2, $value, $menu->term_id);
			
			if($menuItems) {
				
				$menu_items = wp_get_nav_menu_items( $menu->term_id, array(
					'orderby' => 'menu_order'	
				));
		
			    foreach( $menu_items as $item ) {
				    
					$item_value = $this->get_menu_item_option($key1, $item->ID);
					$this->update_menu_item_option($key2, $item_value, $item->ID);
				}
			}
		}
	}

	public function menu_metas_saved($menu_id) {
		
		// check one of the fields, if value exists then metas have been saved at least once
		$saved = $this->get_menu_option('saved', $menu_id);

		if(empty($saved)) {
			return false;
		}
		
		return true;
	}
	
	function body_class( $classes ) {
				
		if($this->sm_debug) {
			$classes[] = 'slick-menu-debug';
		}

		if(trim($this->get_setting('menu-container')) === 'body') {

		    $classes[] = 'slick-menu-in-body';
        }

		return $classes;
	}
	
	function admin_body_class( $classes ) {
				
		if($this->sm_debug) {
			$classes .= ' slick-menu-debug';
		}
		
		return $classes;
	}


	// Script end
	
	public function set_elapsed_time() {

		if($this->print_stats) {
			$this->elapsed_time = microtime(true);
		}   
	}
	
	public function show_elapsed_time() {

		if($this->print_stats) {
			
			$this->elapsed_time = (microtime(true)) - ($this->elapsed_time);
			echo '<div class="slick-menu-debug-box slick-menu-debug-elapsed-time"><strong>'.$this->plugin_name().'</strong> plugin used <strong>' . $this->elapsed_time .' secs</strong> for its computations</div>';
		}   
	}
	
	public function print_admin_loading() {
		
		?>
		<div id="sm-admin-loading">
	        <div id="sm-admin-loading-center">
	            <div id="sm-admin-loading-absolute">
	                <div class="object" id="object_four"></div>
	                <div class="object" id="object_three"></div>
	                <div class="object" id="object_two"></div>
	                <div class="object" id="object_one"></div>
	            </div>
	        </div>
	    </div>
    	<?php
	}

    /**
     * Check if we're on the right edit screen.
     *
     * @param WP_Screen $screen Screen object. Optional. Use current screen object by default.
     * @return bool
     */
    public function is_edit_screen( $screen = null )
    {
        if(!function_exists('get_current_screen') ) {
            return false;
        }

        if ( ! ( $screen instanceof WP_Screen ) )
        {
            $screen = get_current_screen();
        }

        $is_customizer = is_customize_preview();

        if((!empty($screen) && in_array($screen->id, array('nav-menus', 'customize', 'slick-menu_page_slick-menu-settings'))) || !empty($is_customizer)) {
            return true;
        }

        return false;
    }


	/**
	 * Main Slick_Menu Instance
	 *
	 * Ensures only one instance of Slick_Menu is loaded or can be loaded.
	 *
	 * @since 1.0.0
	 * @static
	 * @see Slick_Menu()
	 * @return Main Slick_Menu instance
	 */
	public static function instance ( $plugin ) {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self( $plugin );
			do_action('slick_menu_loaded');
		}
		return self::$_instance;
	} // End instance ()

	/**
	 * Cloning is forbidden.
	 *
	 * @since 1.0.0
	 */
	public function __clone () {
		_doing_it_wrong( __FUNCTION__, esc_html__( 'Cheatin&#8217; huh?' ), $this->plugin_version() );
	} // End __clone ()

	/**
	 * Unserializing instances of this class is forbidden.
	 *
	 * @since 1.0.0
	 */
	public function __wakeup () {
		_doing_it_wrong( __FUNCTION__, esc_html__( 'Cheatin&#8217; huh?' ), $this->plugin_version() );
	} // End __wakeup ()

	/**
	 * Installation. Runs on activation.
	 * @access  public
	 * @since   1.0.0
	 * @return  void
	 */
	public function install () {
		$this->_log_version_number();
	} // End install ()

	/**
	 * Log the plugin version number.
	 * @access  public
	 * @since   1.0.0
	 * @return  void
	 */
	private function _log_version_number () {
		update_option( $this->plugin_slug('version'), $this->plugin_version() );
	} // End _log_version_number ()

}
