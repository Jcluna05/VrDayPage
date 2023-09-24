<?php
/**
 * Rgba field class.
 */
 
if ( class_exists( 'SM_RWMB_Field' ) )
{
	class SM_RWMB_Rgba_Field extends SM_RWMB_Field
	{
		/**
		 * Enqueue scripts and styles
		 */
		static function admin_enqueue_scripts()
		{
				
			wp_enqueue_style( 'sm-rwmb-minicolors', SM_RWMB_EXTEND_JS_URL . 'minicolors/jquery.minicolors.min.css', array(), SM_RWMB_EXTEND_VER );
			wp_enqueue_script( 'sm-rwmb-minicolors', SM_RWMB_EXTEND_JS_URL. 'minicolors/jquery.minicolors.min.js', array( 'jquery' ), time() );
			
			wp_enqueue_script( 'sm-rwmb-field-rgba', SM_RWMB_EXTEND_JS_URL . 'rgba.js', array( 'jquery' ), SM_RWMB_EXTEND_VER, true );
			
			parent::admin_enqueue_scripts();
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
			return sprintf(
				'<input class="sm-rwmb-rgba" name="%s" id="%s" value="%s" type="text" />',
				$field['field_name'],
				$field['id'],
				$meta
			);
		}
			
	}
}