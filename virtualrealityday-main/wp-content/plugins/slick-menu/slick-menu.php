<?php
/*
 * Slick Menu
 *
 * @package WordPress
 * @author Georges Haddad
 * @since 1.0.0
 *
 * @wordpress-plugin
 * Plugin Name: Slick Menu Pro
 * Plugin URI: http://www.slickmenu.net
 * Description: Slick Menu is an Advanced Responsive Vertical Push Menu with multi-level functionality that allows endless nesting of navigation elements. Automatically integrates with your existing wordpress menus.
 * Version: 1.2.8
 * Author: XplodedThemes
 * Author URI: http://www.xplodedthemes.com
 * Text Domain: slick-menu
 * Domain Path: /lang/
 * License:     GPL-2.0+
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
 *
 * @fs_premium_only /includes/license
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
    die;
}

global $xt_slickmenu_plugin;

$market = 'envato';
$market = (strpos($market, 'XT_MARKET') !== false) ? 'freemius' : $market;
$market = (defined('XT_MARKET')) ? XT_MARKET : $market;

$xt_slickmenu_plugin = (object) array(
    'version'       => '1.2.8',
    'name'          => 'Slick Menu',
    'menu_name'     => 'Slick Menu',
    'icon'          => 'dashicons-menu',
    'slug'          => 'slick-menu',
    'premium_slug'  => 'slick-menu',
    'freemium_slug' => 'slick-menu-lite',
    'freemius_id'   => '3243',
    'market'        => $market,
    'markets'       => array(
        'envato' => array(
            'id' => 17723518,
            'buy_url' => 'https://codecanyon.net/item/slick-menu-responsive-wordpress-vertical-menu/17723518',
        )
    ),
    'license_section_slug' => 'slick-menu-license',
    'file'          => __FILE__
);

if ( function_exists( 'xt_slickmenu_fs' ) ) {

    xt_slickmenu_fs()->set_basename( true, __FILE__ );

} else {

    // Load sdk bootstrap file.
    require_once plugin_dir_path(__FILE__) . 'includes/class-bootstrap.php';

    /**
     * Freemius helper function for easy SDK access.
     *
     * @since    1.0.0
     */

    function xt_slickmenu_fs()
    {
        global $xt_slickmenu_plugin;

        return Slick_Menu_Bootstrap::boot($xt_slickmenu_plugin)->sdk;
    }

    // Init Freemius.
    xt_slickmenu_fs();


    if ( ! defined( 'SLICK_MENU_VERSION' ) ) {
        define('SLICK_MENU_VERSION', $xt_slickmenu_plugin->version);
    }


    // Setup Plugin

    /**
     * Returns the main instance of Slick_Menu to prevent the need to use globals.
     *
     * @since  1.0.0
     * @return object Slick_Menu
     */
    function Slick_Menu($class = null) {

        global $xt_slickmenu_plugin;

        $instances = array();

        $instance = Slick_Menu::instance( $xt_slickmenu_plugin );

        // instantiate plugin's class
        $instances['migration'] = Slick_Menu_Migration::instance( $instance );
        $instances['lang'] = Slick_Menu_Language::instance( $instance );
        $instances['cache'] = Slick_Menu_Cache::instance( $instance );
        $instances['pcache'] = Slick_Menu_PCache::instance( $instance );
        $instances['nav'] = Slick_Menu_Nav::instance( $instance );
        $instances['output'] = Slick_Menu_Output::instance( $instance );
        $instances['styles'] = Slick_Menu_Styles_Output::instance( $instance );
        $instances['settings'] = Slick_Menu_Settings::instance( $instance );

        // Load API for generic admin functions
        $instances['extensions'] = Slick_Menu_Extensions::instance( $instance, !is_admin() );
        $instances['admin'] = Slick_Menu_Admin::instance($instance);
        
        if(!empty($class) && !empty($instances[$class])) {
            return $instances[$class];
        }

        return $instance;
    }

    add_action('plugins_loaded', 'Slick_Menu', 9999);
}

