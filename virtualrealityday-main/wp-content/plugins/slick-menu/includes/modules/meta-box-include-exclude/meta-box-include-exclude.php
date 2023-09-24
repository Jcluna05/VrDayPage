<?php
/**
 * Plugin Name: Meta Box Include Exclude
 * Plugin URI: https://metabox.io/plugins/meta-box-include-exclude/
 * Description: Easily show/hide meta boxes by ID, page template, taxonomy or custom defined function.
 * Version: 1.0.1
 * Author: Rilwis
 * Author URI: http://www.deluxeblogtips.com
 * License: GPL2+
*/

if ( defined( 'ABSPATH' ) && is_admin() )
{
	require plugin_dir_path( __FILE__ ) . 'class-sm-mb-include-exclude.php';
	add_filter( 'sm_rwmb_show', array( 'SM_MB_Include_Exclude', 'check' ), 10, 2 );
}
