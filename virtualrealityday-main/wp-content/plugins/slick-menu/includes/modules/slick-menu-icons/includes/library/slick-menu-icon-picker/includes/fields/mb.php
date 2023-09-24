<?php
/**
 * This class defines new "icon" field type for Meta Box class
 * 
 */
if ( class_exists( 'SM_RWMB_Field' ) )
{
	class SM_RWMB_Icon_Picker_Field extends SM_RWMB_Field
	{

		/**
		 * add_actions
		 */
		static function add_actions() {
			
			parent::add_actions();
			
			SM_Icon_Picker::instance()->load();
		
		}

				
		/**
		 * Get field HTML
		 *
		 * @param mixed $meta
		 * @param array $field
		 *
		 * @return string
		 */
		static public function html( $meta, $field )
		{
			
			$args = array(
				'id'    => $field['id'],
				'name'  => $field['field_name'],
				'value' => $meta
			);

			return slick_menu_icon_picker_field($args, false);

		}
	}
}
