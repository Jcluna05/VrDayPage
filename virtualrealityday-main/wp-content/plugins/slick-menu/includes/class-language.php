<?php

if ( ! defined( 'ABSPATH' ) ) exit;

class Slick_Menu_Language {

 	/**
	 * The single instance of Slick_Menu_Language.
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
	
 	public $type = null;

	public function __construct ( $parent ) {

		$this->parent = &$parent;
		$this->parent->lang = &$this;
		
		if(defined('ICL_LANGUAGE_CODE') && ICL_LANGUAGE_CODE !== 'all') {
			global $sitepress;
			
			if(!empty($sitepress)) {
				$this->type = 'wpml';
			}
		}

	}
	
	public function enabled() {
		
		return !empty($this->type);
	}
	
	public function is_type($type) {
		
		return $this->type === $type;
	}	

 	public function get_default_language() {
 		
 		if(!$this->enabled()) {
	 		return false;
 		}
 		
		if($this->is_type('wpml')) {
			
			global $sitepress;
			return $sitepress->get_default_language();
			
		}
		
		return false;	 	
 	}

 	public function get_current_language() {
 		
 		if(!$this->enabled()) {
	 		return false;
 		}
 		
		if($this->is_type('wpml')) {
			
			return ICL_LANGUAGE_CODE;
			
		}
		
		return false;
 	}
 	
 	public function get_translated_term_id($term_id, $taxonomy) {
	 	
	 	if(!$this->enabled()) {
	 		return $term_id;
 		}
 
		$default_language = $this->get_default_language();
		$current_language = $this->get_current_language();
		
		if(is_admin() && !defined( 'DOING_AJAX' )) {
			$lang = $default_language;
		}else{
			$lang = $current_language;
		}
			
	 	if($this->is_type('wpml')) {
            
            return apply_filters('wpml_object_id', $term_id, $taxonomy, true, $lang);
		
		}
		
		return $term_id;
 	}

	/**
	 * Slick_Menu_Language Instance
	 *
	 * Ensures only one instance of Slick_Menu_Language is loaded or can be loaded.
	 *
	 * @since 1.0.0
	 * @static
	 * @see Slick_Menu()
	 * @return Slick_Menu_Language instance
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
