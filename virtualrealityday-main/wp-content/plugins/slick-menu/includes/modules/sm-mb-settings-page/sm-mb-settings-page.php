<?php
/**
 * Plugin Name: MB Settings Page
 * Plugin URI: https://metabox.io/plugins/sm-mb-settings-page/
 * Description: Add-on for meta box plugin which helps you create settings pages easily.
 * Version: 1.1.0
 * Author: Rilwis
 * Author URI: http://www.deluxeblogtips.com
 * License: GPL2+
 * Text Domain: sm-mb-settings-page
 * Domain Path: /lang/
 */

// Prevent loading this file directly.
defined( 'ABSPATH' ) || exit;

add_action( 'plugins_loaded', 'sm_mb_settings_page_load' );

/**
 * Load plugin files after Meta Box is loaded
 */
function sm_mb_settings_page_load()
{
	if ( ! class_exists( 'SM_RW_Meta_Box' ) )
	{
		return;
	}

	define( 'SM_MB_SETTINGS_PAGE_URL', plugin_dir_url( __FILE__ ) );
	define( 'SM_MB_SETTINGS_PAGE_DIR', plugin_dir_path( __FILE__ ) );

	require_once SM_MB_SETTINGS_PAGE_DIR . 'inc/settings-page.php';
	require_once SM_MB_SETTINGS_PAGE_DIR . 'inc/settings-page-meta-box.php';
	require_once SM_MB_SETTINGS_PAGE_DIR . 'inc/init.php';
}
