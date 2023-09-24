<?php

/**
 * Misc. helper functions
 *
 * 
 */


if ( ! function_exists( 'slick_menu_helper_get_array_value_deep' ) ) {
	/**
	 * Get value of a multidimensional array
	 *
	 * @since  0.1.0
	 * @param  array $array Haystack
	 * @param  array $keys  Needles
	 * @return mixed
	 */
	function slick_menu_helper_get_array_value_deep( Array $array, Array $keys ) {
		if ( empty( $array ) || empty( $keys ) ) {
			return $array;
		}

		foreach ( $keys as $idx => $key ) {
			unset( $keys[ $idx ] );

			if ( ! isset( $array[ $key ] ) ) {
				return null;
			}

			if ( ! empty( $keys ) ) {
				$array = $array[ $key ];
			}
		}

		if ( ! isset( $array[ $key ] ) ) {
			return null;
		}

		return $array[ $key ];
	}
}


if ( ! function_exists( 'slick_menu_helper_validate' ) ) {
	/**
	 * Validate settings values
	 *
	 * @param  array $values Settings values
	 * @return array
	 */
	function slick_menu_helper_validate( $values, $sanitize_cb = 'wp_kses_data' ) {
		foreach ( $values as $key => $value ) {
			if ( is_array( $value ) ) {
				$values[ $key ] = slick_menu_helper_validate( $value );
			} else {
				$values[ $key ] = call_user_func_array(
					$sanitize_cb,
					array( $value )
				);
			}
		}

		return $values;
	}
}


if ( ! function_exists( 'slick_menu_helper_get_image_sizes' ) ) {
	/**
	 * Get image sizes
	 *
	 * @since  0.9.0
	 * @access protected
	 * @return array
	 */
	function slick_menu_helper_get_image_sizes() {
		$_sizes = array(
			'thumbnail' => __( 'Thumbnail', 'slick-menu-icons' ),
			'medium'    => __( 'Medium', 'slick-menu-icons' ),
			'large'     => __( 'Large', 'slick-menu-icons' ),
			'full'      => __( 'Full Size', 'slick-menu-icons' ),
		);

		$_sizes = apply_filters( 'image_size_names_choose', $_sizes );

		$sizes = array();
		foreach ( $_sizes as $value => $label ) {
			$sizes[] = array(
				'value' => $value,
				'label' => $label,
			);
		}

		return $sizes;
	}
}


if ( ! function_exists( 'slick_menu_helper_get_script_suffix' ) ) {
	/**
	 * Get script & style suffix
	 *
	 * When SM_SCRIPT_DEBUG is defined true, this will return '.min'.
	 *
	 * @return string
	 */
	function slick_menu_helper_get_script_suffix() {
		return ( defined( 'SM_SCRIPT_DEBUG' ) && SM_SCRIPT_DEBUG ) ? '' : '.min';
	}
}
