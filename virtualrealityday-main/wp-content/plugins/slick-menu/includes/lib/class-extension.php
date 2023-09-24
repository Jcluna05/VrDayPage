<?php

if ( ! defined( 'ABSPATH' ) ) exit;

class Slick_Menu_Extension {


	/**
	 * Slick Menu class object
	 * @var     object
	 * @access  public
	 * @since   1.0.0
	 */
	public $slickmenu = null;
		
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
	 *
	 * @var string A string prefix for html element attributes 
	 */
	public $_attr_prefix;

	/**
	 * The main plugin file.
	 * @var     string
	 * @access  public
	 * @since   1.0.0
	 */
	public $file;

	/**
	 * The main plugin directory.
	 * @var     string
	 * @access  public
	 * @since   1.0.0
	 */
	public $dir;

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
	 * Plugin dependencies.
	 * @var     array
	 * @access  public
	 * @since   1.0.0
	 */
	public $dependencies = array();


	/**
	 * Sets up variables
	 */
	public function __construct( $args = array() ) {
		
		$this->slickmenu = Slick_Menu();
		
		$default_token = $this->slickmenu->plugin_slug().'-extension';
		
		$defaults = array(
			'file' => '',
			'version' => '1.0.0',
			'token' => $default_token,
			'attr_prefix' => $default_token,
			'name' => 'Slick Menu Extension',
			'deps' => array()
		);
		
		$args = array_merge($defaults, $args);
		
		$this->_version = $args['version'];
		$this->_token = $args['token'];
		$this->_attr_prefix = $args['attr_prefix'];
		$this->_name = $args['name'];

		// Load plugin environment variables
		$this->file = $args['file'];
		$this->dir = dirname( $this->file );
		$this->url = esc_url( trailingslashit( plugins_url( '', $this->file )));
		$this->assets_dir = trailingslashit( $this->dir ) . 'assets';
		$this->assets_url = esc_url( trailingslashit( plugins_url( '/assets', $this->file ) ) );

		if(!empty($args['deps'])) {
			foreach($args['deps'] as $dep) {
				$this->setDependency($dep);	
			}	
		}	

		/**
		 * Filters the prefix used in class/id attributes in html display. 
		 * 
		 * @since 0.1.0
		 * 
		 * @param string $default_prefix The default prefix: 'slick-menu'
		 */

		$this->_attr_prefix = apply_filters($this->_token.'_attribute_prefix', $this->_attr_prefix);
		
		$this->preInit();
	}
	
	function preInit() {
		
		if($this->dependenciesLoaded()) {

			$this->init();
		}
	}

	/**
	 * Hooks, filters and registers everything appropriately
	 */
	public function init() {

		register_activation_hook( $this->file, array( $this, 'install' ) );
		
		// initialise translations
		$this->localise();

		$this->slickmenu->register_extension($this);
	}
	
	
	/**
	 * Localise the plugin
	 */
	public function localise(){
        load_plugin_textdomain(
			$this->_token, false, plugin_dir_path(__FILE__) . '/lang'
		);
	}
	
	function setDependency($dep) {
		
		$this->dependencies[] = $dep;
	}
	
	function dependenciesLoaded() {
		
		$failed = 0;
		foreach($this->dependencies as $key => $dep) {
			
			$function = $dep['function'];
			$var = $dep['var'];
			$name = $dep['name'];
			$url = $dep['url'];
			
			if(function_exists($function)) {
				
				$this->$var = $function();
				
			}else{
				$failed++;
				$this->dependencies[$key]['failed'] = true;
			}
			
		}
		
		if(!empty($failed)) {
			add_action( 'admin_notices', array($this, 'dependencyError'));
			return false;
		}
		
		return true;

	}
	
	
	function dependencyError() {

		foreach($this->dependencies as $key => $dep) {
			if(!empty($dep['failed'])) {
			
				?>
			    <div class="error notice">
			        <p><?php echo sprintf(esc_html( '%s is an add-on and requires %s plugin to be installed and active', 'slick-menu' ), '<strong>'.$this->_name.'</strong>', '<a target="_blank" href="'.$dep["url"].'"><strong>'.$dep["name"].'</strong></a>') ?></p>
			    </div>
			    <?php
		    	
			}
		}
	}

	public function plugin_name() {
		
		return $this->_name;
	}

	public function plugin_version() {
		
		return $this->_version;
	}
		
	public function plugin_url($section = '', $params = array()) {
		
		$url = admin_url('admin.php?page='.$this->plugin_slug($section));

		if(!empty($params)) {
			$url = add_query_arg( $params, $url );
		}
		
		return $url;
	}

	public function plugin_slug($section = '') {
		
		return $this->_token.(!empty($section) ? '-'.$section : '');
	}

	public function plugin_file() {
		
		return $this->file;
	}


	/**
	 * Cloning is forbidden.
	 *
	 * @since 1.0.0
	 */
	public function __clone () {
		_doing_it_wrong( __FUNCTION__, esc_html__( 'Cheatin&#8217; huh?', 'slick-menu' ), $this->_version );
	} // End __clone ()

	/**
	 * Unserializing instances of this class is forbidden.
	 *
	 * @since 1.0.0
	 */
	public function __wakeup () {
		_doing_it_wrong( __FUNCTION__, esc_html__( 'Cheatin&#8217; huh?', 'slick-menu' ), $this->_version );
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
		update_option( $this->_token . '_version', $this->_version );
	} // End _log_version_number ()	
}	