<?php
/**
 * Control the include, exclude condition for meta boxes
 *
 * @link       https://metabox.io/plugins/meta-box-include-exclude/
 * @package    Meta Box
 * @subpackage Meta Box Include Exclude
 */

/**
 * Control the include, exclude condition for meta boxes
 *
 * @link       https://metabox.io/plugins/meta-box-include-exclude/
 * @package    Meta Box
 * @subpackage Meta Box Include Exclude
 */
 
if(class_exists('SM_MB_Include_Exclude')) {
	 
	class SM_MB_Nav_Menu_Include_Exclude extends SM_MB_Include_Exclude
	{
	
	
		/**
		 * Check if meta box is displayed or not
		 *
		 * @param bool  $show
		 * @param array $meta_box
		 * @return bool
		 */
		public static function check( $show, $meta_box )
		{
	
			$post_id       = isset( $_GET['post'] ) ? $_GET['post'] : ( isset( $_POST['post_ID'] ) ? $_POST['post_ID'] : false );
			self::$post_id = (int) $post_id;
	
			if ( isset( $meta_box['include'] ) )
				$show = self::maybe_exclude_include( 'include', $meta_box );
	
			if ( isset( $meta_box['exclude'] ) )
				$show = ! self::maybe_exclude_include( 'exclude', $meta_box );
	
			return $show;
		}
	
	}
}
