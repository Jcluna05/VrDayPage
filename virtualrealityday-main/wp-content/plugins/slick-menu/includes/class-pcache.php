<?php

if ( ! defined( 'ABSPATH' ) ) exit;

class Slick_Menu_PCache {

 	/**
	 * The single instance of Slick_Menu_PCache.
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
		$this->parent->pcache = &$this;
		
		$this->pcache_flush_prefix = 'smcache';
		$this->pcache_key = 'smcache';

		if($this->parent->lang->enabled()) {
			$this->pcache_key .= '-'.$this->parent->lang->get_current_language();
		}

		if(get_current_user_id() !== 0) {
			$this->pcache_key .= '-'.get_current_user_id();
		}

		if($this->parent->print_stats && !defined('DOING_AJAX')) {

			add_action('shutdown', array($this, 'stats'));

		}

		add_action( 'sm_rwmb_after_save_post',  array($this, 'flush' ), 10, 1 );
		add_action('wp_update_nav_menu', array($this, 'flush' ), 10, 1 );
		add_action('slickmenu_welcome_sections', array( &$this,'add_section'), 1);

        add_action('slick_menu_rebuild_cache_event', array($this, 'rebuild_cache' ));
	}

	public function add_section($sections) {

        $sections[] = array(
            'id' => 'flush-cache',
            'title' => esc_html__('Flush Cache', 'slick-menu'),
            'show_menu' => true,
            'hide_tab' => true,
            'content' => array(
                'type' => 'function',
                'function' => array($this, 'flush_cache_page'),
            )
        );

        return $sections;
	}

	public function flush_cache_page() {

		$this->flush();
		?>

		<div style="display:table; width:100%;height:90vh;font-size:40px;line-height:1.3;color:#181818;font-weight: 200;text-align:center;">
			<div style="display:table-cell; vertical-align: middle;">
				<?php echo sprintf(esc_html__('%sSlick Menu%s Cache flushed Successfully', 'slick-menu'), '<strong>', '</strong>'); ?><br>
				<?php echo esc_html__('Redirecting, please wait...', 'slick-menu'); ?>
				<meta http-equiv="refresh" content="2; url=<?php echo $this->parent->plugin_url();?>">
			</div>
		</div>

		<?php
	}

    public function get_global_cache() {

	    $key = $this->pcache_key;

        if($this->parent->sm_debug)
            return false;

        $value = get_option($key);

        return $value;
    }

    public function set_global_cache($val) {

        $key = $this->pcache_key;

        if($this->parent->sm_debug)
            return false;

        return update_option($key, $val);
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

        $value = wp_cache_get( $this->pcache_key.'-'.$id );

        if($value === false) {

            $cache = $this->get_global_cache();

            if(isset($cache[$id])) {
                $value = $cache[$id];
                wp_cache_set( $this->pcache_key.'-'.$id, $value );

            }
        }

	 	return $value;
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

	public function flush() {

		global $wpdb;
		
		$key_search = $this->pcache_flush_prefix;
		
		$query = $wpdb->prepare( "DELETE FROM `$wpdb->options` WHERE `option_name` LIKE (%s)", $key_search.'%' );

		$wpdb->query($query);	
		
		$this->flush_w3_total_cache();
		$this->flush_wp_super_cache();

        wp_schedule_single_event( strtotime('+1 second'), 'slick_menu_rebuild_cache_event');
	}
	
	function flush_w3_total_cache(){
		
		//clear W3TC page cache
		if(function_exists('w3tc_pgcache_flush'))
			w3tc_pgcache_flush();
	
		//clear W3TC database cache (comment out if not using)
		if(function_exists('w3tc_dbcache_flush'))
			w3tc_dbcache_flush();
	
		//clear W3TC object cache
		if(function_exists('w3tc_objectcache_flush'))
			w3tc_objectcache_flush();
	
		//clear W3TC minify cache
		if(function_exists('w3tc_minify_flush'))
			w3tc_minify_flush();
	}
	
	function flush_wp_super_cache() {
		
		if(function_exists('wp_cache_clear_cache')) {
			
			global $wpdb;
			wp_cache_clear_cache( $wpdb->blogid );
		}
	}

	function rebuild_cache() {

	    wp_remote_get(home_url());
    }

	function stats() {
		
		echo '<div class="slick-menu-debug-box slick-menu-debug-cache-stats" style="margin: 20px 0;">';
		echo '<label>Persistent Cache Stats</label><br>';
		echo '<strong>cached_objects:</strong><br><pre>'.print_r(array_diff_key($this->tries, $this->missed), true).'</pre>';
		echo '<br><strong>uncached_objects:</strong><br><pre>'.print_r($this->missed, true).'</pre>';
		echo '</div>';
	}


	/**
	 * Slick_Menu_PCache Instance
	 *
	 * Ensures only one instance of Slick_Menu_PCache is loaded or can be loaded.
	 *
	 * @since 1.0.0
	 * @static
	 * @see Slick_Menu()
	 * @return Slick_Menu_PCache instance
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
