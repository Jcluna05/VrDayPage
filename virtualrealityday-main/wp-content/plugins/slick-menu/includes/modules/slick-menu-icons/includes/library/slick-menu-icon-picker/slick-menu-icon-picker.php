<?php

/**
 * SM Icon Picker
 *
 * @package SM_Icon_Picker
 * @version 0.4.0
 * 
 *
 *
 * Plugin Name: SM Icon Picker
 * Plugin URI:  http://xplodedthemes.com/
 * Description: Pick an icon of your choice.
 * Version:     0.4.0
 * Author:      XplodedThemes
 * Author URI:  http://xplodedthemes.com/
 * License:     GPLv2
 * Text Domain: slick-menu-icon-picker
 * Domain Path: /languages
 */

final class SM_Icon_Picker {

	const version = '0.4.1';

	/**
	 * SM_Icon_Picker singleton
	 *
	 * @static
	 * @since  0.1.0
	 * @access protected
	 * @var    SM_Icon_Picker
	 */
	protected static $instance;

	/**
	 * Plugin directory path
	 *
	 * @access protected
	 * @since  0.1.0
	 * @var    array
	 */
	protected $dir;

	/**
	 * Plugin directory url path
	 *
	 * @since  0.1.0
	 * @access protected
	 * @var    array
	 */
	protected $url;

	/**
	 * Icon types registry
	 *
	 * @since  0.1.0
	 * @access protected
	 * @var    SM_Icon_Picker_Types_Registry
	 */
	protected $registry;

	/**
	 * Loader
	 *
	 * @since  0.1.0
	 * @access protected
	 * @var    SM_Icon_Picker_Loader
	 */
	protected $loader;

	/**
	 * Whether the functionality is loaded on admin
	 *
	 * @since  0.1.0
	 * @access protected
	 * @var    bool
	 */
	protected $is_admin_loaded = false;

	/**
	 * Default icon types
	 *
	 * @since  0.1.0
	 * @access protected
	 * @var    array
	 */
	protected $default_types = array(
		'dashicons'        => 'Dashicons',
		'elusive'          => 'Elusive',
		'fa'               => 'Font_Awesome',
		'foundation-icons' => 'Foundation',
		'genericon'        => 'Genericons',
		'image'            => 'Image',
		'svg'              => 'Svg',
	);


	/**
	 * Setter magic
	 *
	 * @since  0.1.0
	 * @param  string $name Property name.
	 * @return bool
	 */
	public function __isset( $name ) {
		return isset( $this->$name );
	}


	/**
	 * Getter magic
	 *
	 * @since  0.1.0
	 * @param  string $name Property name.
	 * @return mixed  NULL if attribute doesn't exist.
	 */
	public function __get( $name ) {
		if ( isset( $this->$name ) ) {
			return $this->$name;
		}

		return null;
	}


	/**
	 * Get instance
	 *
	 * @since  0.1.0
	 * @param  array $args Arguments {@see SM_Icon_Picker::__construct()}.
	 * @return SM_Icon_Picker
	 */
	public static function instance( $args = array() ) {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self( $args );
		}

		return self::$instance;
	}


	/**
	 * Constructor
	 *
	 * @since  0.1.0
	 * @access protected
	 * @param  array $args {
	 *     Optional. Arguments to override class property defaults.
	 *
	 *     @type string $dir Plugin directory path (without trailing slash).
	 *     @type string $url Plugin directory url path (without trailing slash).
	 * }
	 * @return SM_Icon_Picker
	 */
	protected function __construct( $args = array() ) {
		$defaults = array(
			'dir' => untrailingslashit( plugin_dir_path( __FILE__ ) ),
			'url' => untrailingslashit( plugin_dir_url( __FILE__ ) ),
		);

		$args = wp_parse_args( $args, $defaults );
		$keys = array_keys( get_object_vars( $this ) );

		// Disallow.
		unset( $args['registry'] );
		unset( $args['loader'] );
		unset( $args['is_admin_loaded'] );

		foreach ( $keys as $key ) {
			if ( isset( $args[ $key ] ) ) {
				$this->$key = $args[ $key ];
			}
		}

		$locale = apply_filters( 'plugin_locale', get_locale(), 'slick-menu-icon-picker' );

		load_textdomain( 'slick-menu-icon-picker', WP_LANG_DIR . "/slick-menu-icon-picker/slick-menu-icon-picker-{$locale}.mo" );
		load_plugin_textdomain( 'slick-menu-icon-picker', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );

		add_action( 'wp_loaded', array( $this, 'init' ) );
	}


	/**
	 * Register icon types
	 *
	 * @since   0.1.0
	 * @wp_hook action wp_loaded
	 * @return  void
	 */
	public function init() {
		// Initialize icon types registry.
		$this->init_registry();

		// Initialize loader.
		$this->init_loader();

		// Initialize field.
		$this->init_field();

		/**
		 * Fires when SM Icon Picker is ready
		 *
		 * @since 0.1.0
		 * @param SM_Icon_Picker $this SM_Icon_Picker instance.
		 */
		do_action( 'slick_menu_icon_picker_init', $this );
	}


	/**
	 * Initialize icon types registry
	 *
	 * @since  0.1.0
	 * @access protected
	 * @return void
	 */
	protected function init_registry() {
		require_once "{$this->dir}/includes/registry.php";
		$this->registry = SM_Icon_Picker_Types_Registry::instance();

		$this->register_default_types();

		/**
		 * Fires when SM Icon Picker's Registry is ready and the default types are registered.
		 *
		 * @since 0.1.0
		 * @param SM_Icon_Picker $this SM_Icon_Picker instance.
		 */
		do_action( 'slick_menu_icon_picker_types_registry_ready', $this );
	}


	/**
	 * Register default icon types
	 *
	 * @since  0.1.0
	 * @access protected
	 */
	protected function register_default_types() {
		require_once "{$this->dir}/includes/fontpack.php";
		SM_Icon_Picker_Fontpack::instance();

		/**
		 * Allow themes/plugins to disable one or more default types
		 *
		 * @since 0.1.0
		 * @param array $default_types Default icon types.
		 */
		$default_types = array_filter( (array) apply_filters( 'slick_menu_icon_picker_default_types', $this->default_types ) );

		/**
		 * Validate filtered default types
		 */
		$default_types = array_intersect( $this->default_types, $default_types );

		if ( empty( $default_types ) ) {
			return;
		}

		foreach ( $default_types as $filename => $class_suffix ) {
			$class_name = "SM_Icon_Picker_Type_{$class_suffix}";

			require_once "{$this->dir}/includes/types/{$filename}.php";
			$this->registry->add( new $class_name() );
		}
	}


	/**
	 * Initialize loader
	 *
	 * @since  0.1.0
	 * @access protected
	 * @return void
	 */
	protected function init_loader() {
		require_once "{$this->dir}/includes/loader.php";
		$this->loader = SM_Icon_Picker_Loader::instance();

		/**
		 * Fires when SM Icon Picker's Registry is ready and the default types are registered.
		 *
		 * @since 0.1.0
		 * @param SM_Icon_Picker $this SM_Icon_Picker instance.
		 */
		do_action( 'slick_menu_icon_picker_loader_ready', $this );
	}


	/**
	 * Initialize field functionalities
	 *
	 * @since  0.2.0
	 * @access protected
	 */
	protected function init_field() {
		require_once "{$this->dir}/includes/fields/base.php";
		require_once "{$this->dir}/includes/fields/mb.php";
		
		add_filter( 'cmb_field_types', array( $this, 'register_cmb_field' ) );
	}


	/**
	 * Register the field for Custom Meta Boxes
	 *
	 * @since   0.2.0
	 * @wp_hook filter  cmb_field_types
	 * @link    https://github.com/humanmade/Custom-Meta-Boxes/ Custom Meta Boxes
	 *
	 * @param   array  $field_types Available CMB field types.
	 *
	 * @return array
	 */
	public function register_cmb_field( $field_types ) {
		require_once "{$this->dir}/includes/fields/cmb.php";

		$field_types['icon'] = 'SM_Icon_Picker_Field_Cmb';

		return $field_types;
	}


	/**
	 * Load icon picker functionality on an admin page
	 *
	 * @since  0.1.0
	 * @return void
	 */
	public function load() {
		if ( true === $this->is_admin_loaded ) {
			return;
		}

		$this->loader->load();
		$this->is_admin_loaded = true;
	}
}
add_action( 'plugins_loaded', array( 'SM_Icon_Picker', 'instance' ), 7 );