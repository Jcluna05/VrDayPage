<?php

/**
 * Menu item metadata
 *
 * @package SM_Icons
 * 
 */
final class SM_Icons_Meta {

	/**
	 * Default meta value
	 *
	 * @since  0.9.0
	 * @access protected
	 * @var    array
	 */
	protected static $defaults = array(
		'type' => '',
		'icon' => '',
		'url'  => '',
		'position'  => ''
	);
	

	/**
	 * Get menu item meta value
	 *
	 * @since  0.3.0
	 * @since  0.9.0  Add $defaults parameter.
	 * @param  int    $key       Meta Key.
	 * @param  int    $id       Menu item ID.
	 * @param  array  $defaults Optional. Default value.
	 * @return array
	 */
	public static function get( $key = null, $id = null, $child_key = null, $child_id = null, $defaults = array() ) {
		$defaults = wp_parse_args( $defaults, self::$defaults );
		
		$value = array();
		
		if(is_null($child_key) && is_null($child_id)) {
			
			$value = get_term_meta($id, $key, true);
			
		}else{	
		
			$value = get_post_meta($child_id, $child_key, true);
			
			if((empty($value) || empty($value['icon']) || empty($value['icon'])) && !empty($key)) {
				
				$value = get_term_meta($id, $key, true);
			}
		}
		
		$value    = wp_parse_args( (array) $value, $defaults );

		// Backward-compatibility.
		if ( empty( $value['icon'] ) &&
			! empty( $value['type'] ) &&
			! empty( $value[ "{$value['type']}-icon" ] )
		) {
			$value['icon'] = $value[ "{$value['type']}-icon" ];
		}

		if ( ! empty( $value['width'] ) ) {
			$value['svg_width'] = $value['width'];
		}
		unset( $value['width'] );

		if ( isset( $value['position'] ) && ! in_array( $value['position'], array( 'before', 'after', 'above', 'below' ) ) ) {
			$value['position'] = $defaults['position'];
		}

		if ( isset( $value['size'] ) && ! isset( $value['font_size'] ) ) {
			$value['font_size'] = $value['size'];
			unset( $value['size'] );
		}

		// The values below will NOT be saved
		if ( ! empty( $value['icon'] ) && in_array( $value['type'], array( 'image', 'svg' ) ) ) {
			$value['url'] = wp_get_attachment_image_url( $value['icon'], 'thumbnail', false );
		}

		return $value;
	}


	/**
	 * Update menu item metadata
	 *
	 * @since 0.9.0
	 *
	 * @param  int  $key   Meta Key.
	 * @param int   $id    Menu item ID.
	 * @param mixed $value Metadata value.
	 *
	 * @return void
	 */
	public static function update( $key, $id, $value ) {

		/**
		 * Allow plugins/themes to filter the values
		 *
		 * @since 0.8.0
		 * @param  int  $key   Meta Key.
		 * @param array $value Metadata value.
		 * @param int   $id    Menu item ID.
		 */
		$value = apply_filters( 'slick_menu_icons_item_meta_values', $value, $key, $id );
		
		// Don't bother saving if `type` or `icon` is not set.
		if ( empty( $value['type'] ) || empty( $value['icon'] ) ) {
			$value = false;
		}

		// Update
		if ( ! empty( $value ) ) {
			update_post_meta( $id, $key, $value );
		} else {
			delete_post_meta( $id, $key );
		}
	}
}
