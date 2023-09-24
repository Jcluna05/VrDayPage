<?php

if ( ! defined( 'ABSPATH' ) ) exit;

require_once( 'class-image-resize.php' );

class Slick_Menu_Image {

	 /**
	 * Get size information for all currently-registered image sizes.
	 *
	 * @global $_wp_additional_image_sizes
	 * @uses   get_intermediate_image_sizes()
	 * @return array $sizes Data for all currently-registered image sizes.
	 */
	public static function get_image_sizes() {
		global $_wp_additional_image_sizes;
	
		$sizes = array();
	
		foreach ( get_intermediate_image_sizes() as $_size ) {
			if ( in_array( $_size, array('thumbnail', 'medium', 'medium_large', 'large') ) ) {
				$sizes[ $_size ]['width']  = get_option( "{$_size}_size_w" );
				$sizes[ $_size ]['height'] = get_option( "{$_size}_size_h" );
				$sizes[ $_size ]['crop']   = (bool) get_option( "{$_size}_crop" );
			} elseif ( isset( $_wp_additional_image_sizes[ $_size ] ) ) {
				$sizes[ $_size ] = array(
					'width'  => $_wp_additional_image_sizes[ $_size ]['width'],
					'height' => $_wp_additional_image_sizes[ $_size ]['height'],
					'crop'   => $_wp_additional_image_sizes[ $_size ]['crop'],
				);
			}
		}
	
		return $sizes;
	}

	public static function get_image_sizes_options() {
	
	    global $_wp_additional_image_sizes;
	
	    $sizes = self::get_image_sizes();
	
		$image_sizes = array();
				
		foreach($sizes as $key => $size) {
		
			$size_string = ucfirst($key);
			
			if(!empty($size['width'])) {
				$size_string.' - '.$size['width'];
			}
			
			if(!empty($size['height'])) {
				$size_string .= ' x '.$size['height'];
			}	
			
			if(!empty($size['crop'])) {
				$size_string .= ' Cropped';
			}
			
			$image_sizes[$key] = $size_string;
		}
	
		$image_sizes['custom'] = esc_html__('Custom Size', 'slick-menu');
		
	    return $image_sizes;
	}

	
	/**
	 * Get size information for a specific image size.
	 *
	 * @uses   get_image_sizes()
	 * @param  string $size The image size for which to retrieve data.
	 * @return bool|array $size Size data about an image size or false if the size doesn't exist.
	 */
	public static function get_image_size( $size ) {
		$sizes = get_image_sizes();
	
		if ( isset( $sizes[ $size ] ) ) {
			return $sizes[ $size ];
		}
	
		return false;
	}
	
	/**
	 * Get the width of a specific image size.
	 *
	 * @uses   get_image_size()
	 * @param  string $size The image size for which to retrieve data.
	 * @return bool|string $size Width of an image size or false if the size doesn't exist.
	 */
	public static function get_image_width( $size ) {
		if ( ! $size = get_image_size( $size ) ) {
			return false;
		}
	
		if ( isset( $size['width'] ) ) {
			return $size['width'];
		}
	
		return false;
	}
	
	/**
	 * Get the height of a specific image size.
	 *
	 * @uses   get_image_size()
	 * @param  string $size The image size for which to retrieve data.
	 * @return bool|string $size Height of an image size or false if the size doesn't exist.
	 */
	public static function get_image_height( $size ) {
		if ( ! $size = get_image_size( $size ) ) {
			return false;
		}
	
		if ( isset( $size['height'] ) ) {
			return $size['height'];
		}
	
		return false;
	}

	public static function get($id, $size, $crop = false) {
		
		if(is_array($size)) {
			$image = wp_get_attachment_image_src($id, 'full');
			$resized = self::resize($image[0], $size[0], $size[1], $crop);
		}else{
			$resized = wp_get_attachment_image_src($id, $size);
		}
		
		return $resized;
	}
	
	
	/**
	 * Get the height of a specific image size.
	 *
	 * @uses   get_image_size()
	 * @param  string $size The image size for which to retrieve data.
	 * @return bool|string $size Height of an image size or false if the size doesn't exist.
	 */
	public static function resize( $url, $width = null, $height = null, $crop = null, $single = false, $upscale = true ) {
	    $resizer = Slick_Menu_Image_Resize::getInstance();
	    $resized = $resizer->process( $url, $width, $height, $crop, $single, $upscale );
	
	    if(empty($resized)) {
	        $resized = array(
		        $url,
		        $width,
		        $height
	        );
	    }
	    
	    return $resized;
	}

}
