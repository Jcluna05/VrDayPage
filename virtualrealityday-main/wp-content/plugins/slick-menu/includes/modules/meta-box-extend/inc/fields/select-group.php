<?php
/**
 * Select field class.
 */

if ( class_exists( 'SM_RWMB_Choice_Field' ) )
{
	class SM_RWMB_Select_Group_Field extends SM_RWMB_Choice_Field
	{
		/**
		 * Enqueue scripts and styles
		 */
		public static function admin_enqueue_scripts()
		{
			wp_enqueue_style( 'sm-rwmb-select', SM_RWMB_CSS_URL . 'select.css', array(), SM_RWMB_VER );
			wp_enqueue_script( 'sm-rwmb-select', SM_RWMB_JS_URL . 'select.js', array(), SM_RWMB_VER, true );
		}
	
		/**
		 * Walk options
		 *
		 * @param mixed $field
		 * @param array $options
		 * @param mixed $db_fields
		 * @param mixed $meta
		 *
		 * @return string
		 */
		public static function walk( $field, $options, $db_fields, $meta )
		{
			$attributes = self::call( 'get_attributes', $field, $meta );
			
			if(is_array($meta)) {
				$meta = $meta[0];
			}
	
			$output     = sprintf(
				'<select %s>',
				self::render_attributes( $attributes )
			);
			
			if ( false === $field['multiple'] )
			{
				$output .= $field['placeholder'] ? '<option value="">' . esc_html( $field['placeholder'] ) . '</option>' : '';
			}
			
			foreach ($field['options'] as $group)
			{
			      $output .= '<optgroup label="'.$group["text"].'">';
			      foreach ($group["children"] as $child) {
			          $output .= '<option value="' . $child["id"] . '"' . ($meta == $child["id"] ? ' selected' : '') . '>' . $child["text"] . '</option>';
			      }   
			      $output .= '</optgroup>';
			} 
			
			$output .= '</select>';
			$output .= self::get_select_all_html( $field );
			return $output;
		}
	
		/**
		 * Normalize parameters for field
		 *
		 * @param array $field
		 * @return array
		 */
		public static function normalize( $field )
		{
			$field = parent::normalize( $field );
			$field = $field['multiple'] ? SM_RWMB_Multiple_Values_Field::normalize( $field ) : $field;
			$field = wp_parse_args( $field, array(
				'size'            => $field['multiple'] ? 5 : 0,
				'select_all_none' => false,
			) );
	
			return $field;
		}
	
		/**
		 * Get the attributes for a field
		 *
		 * @param array $field
		 * @param mixed $value
		 *
		 * @return array
		 */
		public static function get_attributes( $field, $value = null )
		{
			$attributes = parent::get_attributes( $field, $value );
			$attributes = wp_parse_args( $attributes, array(
				'multiple' => $field['multiple'],
				'size'     => $field['size'],
			) );
	
			return $attributes;
		}
	
		/**
		 * Get html for select all|none for multiple select
		 *
		 * @param array $field
		 * @return string
		 */
		public static function get_select_all_html( $field )
		{
			if ( $field['multiple'] && $field['select_all_none'] )
			{
				return '<div class="sm-rwmb-select-all-none">' . esc_html__( 'Select', 'meta-box' ) . ': <a data-type="all" href="#">' . esc_html__( 'All', 'meta-box' ) . '</a> | <a data-type="none" href="#">' . esc_html__( 'None', 'meta-box' ) . '</a></div>';
			}
			return '';
		}
	}
}