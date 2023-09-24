<?php

if ( ! defined( 'ABSPATH' ) ) exit;

class Slick_Menu_Cache {

 	/**
	 * The single instance of Slick_Menu_Cache.
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
	
 	public $tries = array();
 	public $missed = array();
 	public $repeated = array();
 	
	public function __construct ( $parent ) {

		$this->parent = &$parent;
		$this->parent->cache = &$this;
		
		$this->cache_key = 'smcache';
		
		if($this->parent->lang->enabled()) {
			$this->cache_key .= '-'.$this->parent->lang->get_current_language();
		}
	
		if(get_current_user_id() !== 0) {
			$this->cache_key .= '-'.get_current_user_id();
		}
			
		if($this->parent->print_stats && !defined('DOING_AJAX')) {
			
			add_action('shutdown', array($this, 'stats'));
		
		}

	}

    public function get_global_cache() {

        $key = $this->cache_key;

        if($this->parent->sm_debug)
            return false;

        return wp_cache_get( $key );
    }

    public function set_global_cache($val) {

        $key = $this->cache_key;

        if($this->parent->sm_debug)
            return false;

        return wp_cache_set($key, $val);
    }

 	public function set($id, $val) {

        $this->missed[$id] = true;

        if($this->parent->sm_debug)
            return false;

        $cache = $this->get_global_cache();

        $cache[$id] = $val;

        $this->set_global_cache($cache);
	 	
 	}
 	
 	public function get($id) {

        $this->tries[$id] = true;

        if($this->parent->sm_debug)
            return false;

        $cache = $this->get_global_cache();

        if(isset($cache[$id])) {
            return $cache[$id];
        }

        return false;
	 	
 	}

    public function delete($id) {

        if($this->parent->sm_debug)
            return false;

        $cache = $this->get_global_cache();

        if(isset($cache[$id])) {
            unset($cache[$id]);
            return $this->set_global_cache($cache);
        }

        return false;
    }

	function stats() {
		
		echo '<div class="slick-menu-debug-box slick-menu-debug-cache-stats" style="margin: 20px 0;">';
		echo '<label>Cache Stats</label><br>';
		echo '<strong>cached_objects:</strong><br><pre>'.print_r(array_diff_key($this->tries, $this->missed), true).'</pre>';
		echo '<br><strong>uncached_objects:</strong><br><pre>'.print_r($this->missed, true).'</pre>';
		echo '</div>';
	}


	/**
	 * Slick_Menu_Cache Instance
	 *
	 * Ensures only one instance of Slick_Menu_Cache is loaded or can be loaded.
	 *
	 * @since 1.0.0
	 * @static
	 * @see Slick_Menu()
	 * @return Slick_Menu_Cache instance
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
