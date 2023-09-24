<?php
/**
 * Plugin Name: Meta Box Extend
 * Description: Extension to MetaBox.io
 * Version: 1.0
 * Author: Georges Haddad
 * Author URI: http://www.xplodedthemes.com
 * License: GPL2+
 */

if ( defined( 'ABSPATH' ) && ! class_exists( 'SM_RWMB_EXTEND_Loader' ) )
{
	require plugin_dir_path( __FILE__ ) . 'inc/loader.php';
	new SM_RWMB_EXTEND_Loader;
}
