<?php

/**
 * Front end functionalities
 *
 * @package SM_Icons
 * 
 */
final class SM_Icons_Front_End {

	/**
	 * Icon types
	 *
	 * @since  0.9.0
	 * @access protected
	 * @var    array
	 */
	protected static $icon_types = array();

	/**
	 * Default icon style
	 *
	 * @since  0.9.0
	 * @access protected
	 * @var    array
	 */
	protected static $default_style = array(
		'font_size'      => array(
			'property' => 'font-size',
			'value'    => '1.2',
			'unit'     => 'em',
		),
		'vertical_align' => array(
			'property' => 'vertical-align',
			'value'    => 'middle',
			'unit'     => null,
		),
		'svg_width'      => array(
			'property' => 'width',
			'value'    => '1',
			'unit'     => 'em',
		),
	);

	/**
	 * Hidden label class
	 *
	 * @since  0.9.0
	 * @access protected
	 * @var    string
	 */
	public static $hidden_label_class = 'visuallyhidden';


	/**
	 * Add hooks for front-end functionalities
	 *
	 * @since 0.9.0
	 */
	public static function init() {
		$active_types = SM_Icons_Settings::get( 'global', 'icon_types' );

		if ( empty( $active_types ) ) {
			return;
		}

		foreach ( SM_Icons::get( 'types' ) as $type ) {
			if ( in_array( $type->id, $active_types ) ) {
				self::$icon_types[ $type->id ] = $type;
			}
		}

		/**
		 * Allow themes/plugins to override the hidden label class
		 *
		 * @since  0.8.0
		 * @param  string $hidden_label_class Hidden label class.
		 * @return string
		 */
		self::$hidden_label_class = apply_filters( 'slick_menu_icons_hidden_label_class', self::$hidden_label_class );

		/**
		 * Allow themes/plugins to override default inline style
		 *
		 * @since  0.9.0
		 * @param  array $default_style Default inline style.
		 * @return array
		 */
		self::$default_style = apply_filters( 'slick_menu_icons_default_style', self::$default_style );

		add_action( 'wp_enqueue_scripts', array( __CLASS__, '_enqueue_styles' ), 7 );
	}


	/**
	 * Get nav menu ID based on arguments passed to wp_nav_menu()
	 *
	 * @since  0.3.0
	 * @param  array $args wp_nav_menu() Arguments
	 * @return mixed Nav menu ID or FALSE on failure
	 */
	public static function get_nav_menu_id( $args ) {
		$args = (object) $args;
		$menu = wp_get_nav_menu_object( $args->menu );

		// Get the nav menu based on the theme_location
		if ( ! $menu
			&& $args->theme_location
			&& ( $locations = get_nav_menu_locations() )
			&& isset( $locations[ $args->theme_location ] )
		) {
			$menu = wp_get_nav_menu_object( $locations[ $args->theme_location ] );
		}

		// get the first menu that has items if we still can't find a menu
		if ( ! $menu && ! $args->theme_location ) {
			$menus = wp_get_nav_menus();
			foreach ( $menus as $menu_maybe ) {
				if ( $menu_items = wp_get_nav_menu_items( $menu_maybe->term_id, array( 'update_post_term_cache' => false ) ) ) {
					$menu = $menu_maybe;
					break;
				}
			}
		}

		if ( is_object( $menu ) && ! is_wp_error( $menu ) ) {
			return $menu->term_id;
		} else {
			return false;
		}
	}


	/**
	 * Enqueue stylesheets
	 *
	 * @since   0.1.0
	 * @wp_hook action wp_enqueue_scripts
	 * @link    http://codex.wordpress.org/Plugin_API/Action_Reference/wp_enqueue_scripts
	 */
	public static function _enqueue_styles() {
		foreach ( self::$icon_types as $type ) {
			if ( wp_style_is( $type->stylesheet_id, 'registered' ) ) {
				wp_enqueue_style( $type->stylesheet_id );
			}
		}

		/**
		 * Allow plugins/themes to override the extra stylesheet location
		 *
		 * @since 0.9.0
		 * @param string $extra_stylesheet_uri Extra stylesheet URI.
		 */
		$extra_stylesheet_uri = apply_filters(
			'slick_menu_icons_extra_stylesheet_uri',
			sprintf( '%scss/extra%s.css', SM_Icons::get( 'url' ), slick_menu_helper_get_script_suffix() )
		);

		wp_enqueue_style(
			'slick-menu-icons-extra',
			$extra_stylesheet_uri,
			false,
			SM_Icons::version
		);
	}

	/**
	 * Get icon
	 *
	 * @since  0.9.0
	 * @param  int    $key       Meta Key.
	 * @param  int    $id       Menu item ID.
	 * @return string
	 */
	public static function get_icon( $key = null, $id = null, $child_key = null, $child_id = null, $settings = array()) {
		
		if(!empty($key) && is_array($key)) {
			
			$meta = $key;
			
		}else{
			
			$meta = SM_Icons_Meta::get( $key, $id, $child_key, $child_id );
		}
		
		if(is_array($meta) && is_array($settings)) {
			$meta = array_merge($meta, $settings);
		}
		
		$icon = '';

		// Icon type is not set.
		if ( empty( $meta['type'] ) ) {
			return $icon;
		}

		// Icon is not set.
		if ( empty( $meta['icon'] ) ) {
			return $icon;
		}

		// Icon is not set.
		if ( empty( $meta['position'] ) ) {
			$meta['position'] = 'before';
		}
		
		// Icon type is not registered/enabled.
		if ( ! isset( self::$icon_types[ $meta['type'] ] ) ) {
			return $icon;
		}

		$type = self::$icon_types[ $meta['type'] ];

		$callbacks = array(
			array( $type, 'get_icon' ),
			array( __CLASS__, "get_{$type->id}_icon" ),
			array( __CLASS__, "get_{$type->template_id}_icon" ),
		);

		foreach ( $callbacks as $callback ) {
			if ( is_callable( $callback ) ) {
				$icon = call_user_func( $callback, $meta );
				break;
			}
		}

		return $icon;
	}


	/**
	 * Get icon style
	 *
	 * @since  0.9.0
	 * @param  array   $meta         Menu item meta value.
	 * @param  array   $keys         Style properties.
	 * @param  bool    $as_attribute Optional. Whether to output the style as HTML attribute or value only.
	 *                               Defaults to TRUE.
	 * @return string
	 */
	public static function get_icon_style( $meta, $keys, $as_attribute = true ) {
		$style_a = array();
		$style_s = '';

		foreach ( $keys as $key ) {
			if ( ! isset( self::$default_style[ $key ] ) ) {
				continue;
			}

			$rule = self::$default_style[ $key ];

			if ( ! isset( $meta[ $key ] ) || $meta[ $key ] === $rule['value'] ) {
				continue;
			}

			$value = $meta[ $key ];
			if ( ! empty( $rule['unit'] ) ) {
				$value .= $rule['unit'];
			}

			$style_a[ $rule['property'] ] = $value;
		}

		if ( empty( $style_a ) ) {
			return $style_s;
		}

		foreach ( $style_a as $key => $value ) {
			$style_s .= "{$key}:{$value};";
		}

		$style_s = esc_attr( $style_s );

		if ( $as_attribute  ) {
			$style_s = sprintf( ' style="%s"', $style_s );
		}

		return $style_s;
	}


	/**
	 * Get icon classes
	 *
	 * @since  0.9.0
	 * @param  array         $meta    Menu item meta value.
	 * @param  string        $output  Whether to output the classes as string or array. Defaults to string.
	 * @return string|array
	 */
	public static function get_icon_classes( $meta, $output = 'string' ) {
		$classes = array( '_mi' );

		if ( empty( $meta['hide_label'] ) && !empty($meta['position'])) {
			$classes[] = "_{$meta['position']}";
		}

		if ( 'string' === $output ) {
			$classes = implode( ' ', $classes );
		}

		return $classes;
	}


	/**
	 * Get font icon
	 *
	 * @since  0.9.0
	 * @param  array  $meta Menu item meta value.
	 * @return string
	 */
	public static function get_font_icon( $meta ) {
		$classes = sprintf( '%s %s %s', self::get_icon_classes( $meta ), $meta['type'], $meta['icon'] );
		$style   = self::get_icon_style( $meta, array( 'font_size', 'vertical_align' ) );

		return sprintf( '<i class="%s" aria-hidden="true"%s></i>', esc_attr( $classes ), $style );
	}


	/**
	 * Get image icon
	 *
	 * @since  0.9.0
	 * @param  array  $meta Menu item meta value.
	 * @return string
	 */
	public static function get_image_icon( $meta ) {
		$args = array(
			'class'       => sprintf( '%s _image', self::get_icon_classes( $meta ) ),
			'aria-hidden' => 'true',
		);

		$style = self::get_icon_style( $meta, array( 'vertical_align' ), false );
		if ( ! empty( $style ) ) {
			$args['style'] = $style;
		}

        $image_size = isset($meta['image_size']) ? $meta['image_size'] : 'thumbnail';
        return wp_get_attachment_image( $meta['icon'], $image_size, false, $args );
	}


	/**
	 * Get SVG icon
	 *
	 * @since  0.9.0
	 * @param  array  $meta Menu item meta value.
	 * @return string
	 */
	public static function get_svg_icon( $meta ) {
		$classes = sprintf( '%s _svg', self::get_icon_classes( $meta ) );
		$style   = self::get_icon_style( $meta, array( 'svg_width', 'vertical_align' ) );

		return sprintf(
			'<img src="%s" class="%s" aria-hidden="true"%s />',
			esc_url( wp_get_attachment_url( $meta['icon'] ) ),
			esc_attr( $classes ),
			$style
		);
	}
}
