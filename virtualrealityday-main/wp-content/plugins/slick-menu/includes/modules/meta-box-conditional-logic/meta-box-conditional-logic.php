<?php
/*
Plugin Name: Meta Box Conditional Logic
Plugin URI: http://www.metabox.io/plugins/meta-box-conditional-logic
Description: Control the Visibility of Meta Boxes and Fields or even HTML elements with ease.
Version: 1.3
Author: Tan Nguyen
Author URI: https://www.binaty.org
License: GPL2+
*/

//Prevent loading this file directly
defined( 'ABSPATH' ) || exit;

//----------------------------------------------------------
//Define plugin URL for loading static files or doing AJAX
//------------------------------------------------------------
if ( ! defined( 'SM_MBC_URL' ) )
	define( 'SM_MBC_URL', plugin_dir_url( __FILE__ ) );

define( 'SM_MBC_JS_URL', trailingslashit( SM_MBC_URL . 'assets/js' ) );
// ------------------------------------------------------------
// Plugin paths, for including files
// ------------------------------------------------------------
if ( ! defined( 'SM_MBC_DIR' ) )
	define( 'SM_MBC_DIR', plugin_dir_path( __FILE__ ) );

define( 'SM_MBC_INC_DIR', trailingslashit( SM_MBC_DIR . 'inc' ) );

// Load the conditional logic and assets
include SM_MBC_INC_DIR . 'class-conditional-logic.php';

new SM_MB_Conditional_Logic;