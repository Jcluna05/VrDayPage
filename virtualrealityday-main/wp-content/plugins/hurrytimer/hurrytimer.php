<?php
// removeIf(pro)

/**
 * The plugin bootstrap file
 *
 * @link              http://hurrytimer.com
 * @since             1.0.0
 * @package           Hurrytimer
 *
 * @wordpress-plugin
 * Plugin Name:       HurryTimer
 * Plugin URI:        https://hurrytimer.com
 * Description:       A Scarcity and Urgency Countdown Timer for WordPress & WooCommerce with recurring and evergreen mode.
 * Version:           2.3.1.1
 * Author:            Nabil Lemsieh
 * Author URI:        https://hurrytimer.com
 * License:           GPLv3
 * License URI:       http://www.gnu.org/licenses/gpl.html
 * Text Domain:       hurrytimer
 * Domain Path:       /languages
 */


// endRemoveIf(pro)

// If this file is called directly, abort.

if ( !defined( 'WPINC' ) ) {
    die;
}

define( 'HURRYT_VERSION', '2.3.1.1' );
define( 'HURRYT_DIR', plugin_dir_path( __FILE__ ) );
define( 'HURRYT_URL', plugin_dir_url( __FILE__ ) );
define( 'HURRYT_BASENAME', plugin_basename( __FILE__ ) );
define( 'HURRYT_POST_TYPE', 'hurrytimer_countdown' );

require_once __DIR__ . '/vendor/autoload.php';

register_activation_hook( __FILE__, [ Hurrytimer\Installer::get_instance(), 'activate' ] );

add_action( 'plugins_loaded', function () {
    ( new \Hurrytimer\Bootstrap() )->run();
    do_action( 'hurrytimer_init' );
} );


