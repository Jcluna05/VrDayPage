<?php

/**
 * Slick Menu Icons
 *
 * @package SM_Icons
 * @version 0.10.1
 * 
 *
 *
 * Plugin name: Slick Menu Icons
 * Plugin URI:  http://xplodedthemes.com/
 * Description: Spice up your multi push menus with pretty icons, easily.
 * Version:     0.10.1
 * Author:      Georges Haddad
 * Author URI:  http://xplodedthemes.com/
 * License:     GPLv2
 * Text Domain: slick-menu-icons
 * Domain Path: /languages
 */


/**
 * Main plugin class
 */
final class SM_Icons {

	const version = '0.10.1';

	/**
	 * Holds plugin data
	 *
	 * @access protected
	 * @since  0.1.0
	 * @var    array
	 */
	protected static $data;


	/**
	 * Get plugin data
	 *
	 * @since  0.1.0
	 * @since  0.9.0  Return NULL if $name is not set in $data.
	 * @param  string $name
	 *
	 * @return mixed
	 */
	public static function get( $name = null ) {
		if ( is_null( $name ) ) {
			return self::$data;
		}

		if ( isset( self::$data[ $name ] ) ) {
			return self::$data[ $name ];
		}

		return null;
	}


	/**
	 * Load plugin
	 *
	 * 1. Load translation
	 * 2. Set plugin data (directory and URL paths)
	 * 3. Attach plugin initialization at slick_menu_icon_picker_init hook
	 *
	 * @since   0.1.0
	 * @wp_hook action plugins_loaded
	 * @link    http://codex.wordpress.org/Plugin_API/Action_Reference/plugins_loaded
	 */
	public static function _load() {
		load_plugin_textdomain( 'slick-menu-icons', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );

		self::$data = array(
			'dir'   => plugin_dir_path( __FILE__ ),
			'url'   => plugin_dir_url( __FILE__ ),
			'types' => array(),
		);

		// Load SM Icon Picker.
		if ( ! class_exists( 'SM_Icon_Picker' ) ) {
			$ip_file = self::$data['dir'] . 'includes/library/slick-menu-icon-picker/slick-menu-icon-picker.php';

			if ( file_exists( $ip_file ) ) {
				require_once $ip_file;
			} else {
				add_action( 'admin_notices', array( __CLASS__, '_notice_missing_slick_menu_icon_picker' ) );
				return;
			}
		}
		SM_Icon_Picker::instance();

		require_once self::$data['dir'] . 'includes/library/compat.php';
		require_once self::$data['dir'] . 'includes/library/functions.php';
		require_once self::$data['dir'] . 'includes/meta.php';

		add_action( 'slick_menu_icon_picker_init', array( __CLASS__, '_init' ), 9 );
	}


	/**
	 * Initialize
	 *
	 * 1. Get registered types from SM Icon Picker
	 * 2. Load settings
	 * 3. Load front-end functionalities
	 *
	 * @since   0.1.0
	 * @since   0.9.0  Hook into `slick_menu_icon_picker_init`.
	 * @wp_hook action slick_menu_icon_picker_init
	 * @link    http://codex.wordpress.org/Plugin_API/Action_Reference
	 */
	public static function _init() {
		/**
		 * Allow themes/plugins to add/remove icon types
		 *
		 * @since 0.1.0
		 * @param array $types Icon types
		 */
		self::$data['types'] = apply_filters(
			'slick_menu_icons_types',
			SM_Icon_Picker_Types_Registry::instance()->types
		);

		// Nothing to do if there are no icon types registered.
		if ( empty( self::$data['types'] ) ) {
			if ( WP_DEBUG ) {
				trigger_error( esc_html__( 'Slick Menu Icons: No registered icon types found.', 'slick-menu-icons' ) );
			}

			return;
		}

		// Load settings.
		require_once self::$data['dir'] . 'includes/settings.php';
		SM_Icons_Settings::init();

		// Load front-end functionalities.
		if ( ! is_admin() || defined('DOING_AJAX') ) {
			require_once self::$data['dir'] . '/includes/front.php';
			SM_Icons_Front_End::init();
		}

		do_action( 'slick_menu_icons_loaded' );
	}


	/**
	 * Display notice about missing SM Icon Picker
	 *
	 * @since   0.9.1
	 * @wp_hook action admin_notice
	 */
	public static function _notice_missing_slick_menu_icon_picker() {
		?>
		<div class="error">
			<p><?php esc_html_e( 'Looks like Slick Menu Icons was installed via Composer. Please activate SM Icon Picker first.', 'slick-menu-icons' ); ?></p>
		</div>
		<?php
	}
}
add_action( 'plugins_loaded', array( 'SM_Icons', '_load' ) );
