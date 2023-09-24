<?php
/**
 * Plugin Name: MB Admin Columns
 * Plugin URI: https://metabox.io/plugins/sm-mb-admin-columns/
 * Description: Show custom fields in the post list table.
 * Version: 1.0.0
 * Author: Rilwis
 * Author URI: http://www.deluxeblogtips.com
 * License: GPL2+
 * Text Domain: sm-mb-admin-columns
 * Domain Path: /lang/
 */

// Prevent loading this file directly.
defined( 'ABSPATH' ) || exit;

/**
 * Plugin main class.
 */
class SM_MB_Admin_Columns
{
	/**
	 * Add hooks.
	 */
	public function __construct()
	{
		add_action( 'admin_init', array( $this, 'load' ) );
	}

	/**
	 * Load plugin files after Meta Box is loaded.
	 */
	public function load()
	{
		if ( !class_exists('SM_RWMB_Core') )
		{
			return;
		}

		$this->constants();
		$this->load_files();
		$this->init();
	}

	/**
	 * Define plugin constants.
	 */
	public function constants()
	{
		define( 'SM_MB_ADMIN_COLUMNS_DIR', trailingslashit( plugin_dir_path( __FILE__ ) ) );
		define( 'SM_MB_ADMIN_COLUMNS_URL', trailingslashit( plugins_url( '', __FILE__ ) ) );
	}

	/**
	 * Load plugin files.
	 */
	public function load_files()
	{
		require SM_MB_ADMIN_COLUMNS_DIR . 'inc/post.php';
	}

	/**
	 * Initialize.
	 */
	public function init()
	{
		$meta_boxes = SM_RWMB_Core::get_meta_boxes();
		foreach ( $meta_boxes as $meta_box )
		{
			$fields = &$meta_box['fields'];
			foreach ( $fields as $k => $field )
			{
				if ( ! isset( $field['admin_columns'] ) )
				{
					unset( $fields[$k] );
				}
			}
			if ( empty( $fields ) )
			{
				continue;
			}

			$meta_box = SM_RW_Meta_Box::normalize( $meta_box );
			foreach ( $meta_box['post_types'] as $post_type )
			{
				new SM_MB_Admin_Columns_Post( $post_type, $fields );
			}
		}
	}
}

new SM_MB_Admin_Columns;
