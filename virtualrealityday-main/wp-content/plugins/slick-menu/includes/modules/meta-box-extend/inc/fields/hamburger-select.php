<?php

/**
 * Image select field class which uses images as radio options.
 */
 
 if ( class_exists( 'SM_RWMB_Field' ) )
{
	class SM_RWMB_Hamburger_Select_Field extends SM_RWMB_Field
	{
		
		/**
		 * Enqueue scripts and styles
		 */
		static function admin_enqueue_scripts()
		{
			
			wp_enqueue_style( 'sm-rwmb-hamburgers', SM_RWMB_EXTEND_CSS_URL . 'hamburgers.min.css', array(), SM_RWMB_EXTEND_VER );

			wp_enqueue_style( 'sm-rwmb-image-select', SM_RWMB_CSS_URL . 'image-select.css', array(), SM_RWMB_VER );
			wp_enqueue_script( 'sm-rwmb-image-select', SM_RWMB_JS_URL . 'image-select.js', array( 'jquery' ), SM_RWMB_VER, true );
					
			wp_enqueue_style( 'sm-rwmb-hamburgers-select', SM_RWMB_EXTEND_CSS_URL . 'hamburger-select.min.css', array('sm-rwmb-image-select'), SM_RWMB_EXTEND_VER );
			wp_enqueue_script( 'sm-rwmb-hamburger-select', SM_RWMB_EXTEND_JS_URL . 'hamburger-select.js', array( 'jquery' ), SM_RWMB_EXTEND_VER, true );

		}
	
		/**
		 * Get field HTML
		 *
		 * @param mixed $meta
		 * @param array $field
		 * @return string
		 */
		static function html( $meta, $field )
		{
			$html = array();
			
			
			$options = array(
						
				'sm-hamburger--3dx' => '3DX',
				'sm-hamburger--3dy' => '3DY',
				'sm-hamburger--arrow' => 'Arrow',
				'sm-hamburger--arrowalt' => 'Arrow Alt',
				'sm-hamburger--collapse' => 'Collapse',
				'sm-hamburger--elastic' => 'Elastic',
				'sm-hamburger--emphatic' => 'Emphatic',
				'sm-hamburger--slider' => 'Slider',
				'sm-hamburger--spin' => 'Spin',
				'sm-hamburger--spring' => 'Spring',
				'sm-hamburger--stand' => 'Stand',
				'sm-hamburger--squeeze' => 'Squeeze',
				'sm-hamburger--vortex' => 'Vortex',
				'sm-hamburger--boring' => 'Boring',
				''	=>	''
			);
	
			$tpl  = '<label class="sm-rwmb-image-select sm-rwmb-hamburger-select%s">
		      <div class="name">%s</div>
		      <div class="sm-hamburger %s" data-regular="%s" data-reverse="%s-r">
		        <div class="sm-hamburger-box">
		          <div class="sm-hamburger-inner"></div>
		        </div>
		      </div>
		      <img src="data:image/gif;base64,R0lGODlhAQABAAD/ACwAAAAAAQABAAACADs=" style="opacity:0;visibility: hidden;">
		      <input type="%s" class="sm-rwmb-image_select hidden" name="%s" value="%s"%s>
		    </label>';
	    
			$meta = (array) $meta;
			foreach ( $options as $value => $hamburger )
			{				
				$html[] = sprintf(
					$tpl,
					(!empty($value) ? '' : ' sm-rwmb-hamburger-empty'),
					(!empty($hamburger) ? $hamburger : ''),
					$value,
					$value,
					$value,
					$field['multiple'] ? 'checkbox' : 'radio',
					$field['field_name'],
					$value,
					checked( in_array( $value, $meta ), true, false )
				);
			}
	
			return implode( ' ', $html );
		}
	
		/**
		 * Normalize parameters for field
		 *
		 * @param array $field
		 * @return array
		 */
		static function normalize( $field )
		{
			$field = parent::normalize( $field );
			$field['field_name'] .= $field['multiple'] ? '[]' : '';
	
			return $field;
		}
	
		/**
		 * Format a single value for the helper functions.
		 * @param array  $field Field parameter
		 * @param string $value The value
		 * @return string
		 */
		static function format_single_value( $field, $value )
		{
			$tpl = '<div class="sm-hamburger %s">
		        <div class="sm-hamburger-box">
		          <div class="sm-hamburger-inner"></div>
		        </div>
		      </div>';
		      
			return sprintf( $tpl, esc_attr( $field['options'][$value] ) );
		}
	}
}