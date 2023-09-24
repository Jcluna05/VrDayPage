<?php
/**
 * Plugin Name: MB Nav Menu
 * Plugin URI: http://www.xplodedthemes.com
 * Description: Add-on for meta box plugin which helps add metaboxes within nav menu items.
 * Version: 1.1.0
 * Author: Georges Haddad
 * Author URI: http://www.xplodedthemes.com
 * License: GPL2+
 * Text Domain: sm-mb-nav-menu
 * Domain Path: /lang/
 */

// Prevent loading this file directly.
defined( 'ABSPATH' ) || exit;

add_action( 'plugins_loaded', 'sm_mb_nav_menu_load' );

/**
 * Load plugin files after Meta Box is loaded
 */
function sm_mb_nav_menu_load()
{
	if ( ! class_exists( 'SM_RW_Meta_Box' ) )
	{
		return;
	}

	define( 'SM_MB_NAV_MENU_URL', plugin_dir_url( __FILE__ ) );
	define( 'SM_MB_NAV_MENU_DIR', plugin_dir_path( __FILE__ ) );

	require_once SM_MB_NAV_MENU_DIR . 'inc/nav-menu-include-exclude.php';
	require_once SM_MB_NAV_MENU_DIR . 'inc/nav-menu.php';
	require_once SM_MB_NAV_MENU_DIR . 'inc/nav-menu-meta-box.php';
	
	new SM_MB_Nav_Menu;
}
