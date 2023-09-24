<?php
/**
 * Load plugin's files with check for installing it as a standalone plugin or
 * a module of a theme / plugin. If standalone plugin is already installed, it
 * will take higher priority.
 * @package Meta Box
 */

/**
 * Plugin loader class.
 * @package Meta Box
 */
class SM_RWMB_Extend_Loader
{
	/**
	 * Class constructor.
	 */
	public function __construct()
	{
		$this->constants();
		$this->init();
	}

	/**
	 * Define plugin constants.
	 */
	public function constants()
	{
		// Script version, used to add version for scripts and styles
		define( 'SM_RWMB_EXTEND_VER', '1.0.0' );

		list( $path, $url ) = self::get_path();

		// Plugin URLs, for fast enqueuing scripts and styles
		define( 'SM_RWMB_EXTEND_URL', $url );
		define( 'SM_RWMB_EXTEND_JS_URL', trailingslashit( SM_RWMB_EXTEND_URL . 'js' ) );
		define( 'SM_RWMB_EXTEND_CSS_URL', trailingslashit( SM_RWMB_EXTEND_URL . 'css' ) );

		// Plugin paths, for including files
		define( 'SM_RWMB_EXTEND_DIR', $path );
		define( 'SM_RWMB_EXTEND_INC_DIR', trailingslashit( SM_RWMB_EXTEND_DIR . 'inc' ) );
		define( 'SM_RWMB_EXTEND_FIELDS_DIR', trailingslashit( SM_RWMB_EXTEND_INC_DIR . 'fields' ) );
	}

	/**
	 * Get plugin base path and URL.
	 * The method is static and can be used in extensions.
	 * @link http://www.deluxeblogtips.com/2013/07/get-url-of-php-file-in-wordpress.html
	 * @param string $base Base folder path
	 * @return array Path and URL.
	 */
	public static function get_path( $base = '' )
	{
		// Plugin base path
		$path        = $base ? $base : dirname( dirname( __FILE__ ) );
		$path        = wp_normalize_path( untrailingslashit( $path ) );
		$content_dir = wp_normalize_path( untrailingslashit( WP_CONTENT_DIR ) );

		// Default URL
		$url = plugins_url( '', $path . '/' . basename( $path ) . '.php' );

		// Installed as a plugin?
		if ( 0 === strpos( $path, wp_normalize_path( WP_PLUGIN_DIR ) ) || 0 === strpos( $path, wp_normalize_path( WPMU_PLUGIN_DIR ) ) )
		{
			// Do nothing
		}
		// Included into themes
		elseif ( 0 === strpos( $path, $content_dir ) )
		{
			// Get plugin base URL
			$content_url = untrailingslashit( dirname( dirname( get_stylesheet_directory_uri() ) ) );
			$url         = str_replace( $content_dir, $content_url, $path );
		}

		$path = trailingslashit( $path );
		$url  = trailingslashit( $url );

		return array( $path, $url );
	}

	/**
	 * Initialize plugin.
	 */
	public function init()
	{
		require_once(SM_RWMB_EXTEND_INC_DIR.'core.php');
		require_once(SM_RWMB_EXTEND_INC_DIR.'groups.php');
		
		// Plugin core
		new SM_RWMB_EXTEND_Core;

	}
}
