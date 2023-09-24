<?php
/**
 * This class defines new "icon" field type for Meta Box class
 * 
 */
if ( class_exists( 'SM_RWMB_Field' ) )
{
	class SM_RWMB_Icon_Field extends SM_RWMB_Field
	{
	
		/**
		 * Enqueue scripts and styles.
		 */
		static function admin_enqueue_scripts()
		{

			wp_enqueue_style( 'sm-rwmb-fontawesome', SM_RWMB_EXTEND_CSS_URL . 'font-awesome.min.css', array(), SM_RWMB_EXTEND_VER );

			wp_enqueue_style( 'sm-rwmb-fontawesome-iconpicker', SM_RWMB_EXTEND_CSS_URL . 'fontawesome-iconpicker.min.css', array(), SM_RWMB_EXTEND_VER );
			wp_enqueue_script( 'sm-rwmb-fontawesome-iconpicker', SM_RWMB_EXTEND_JS_URL . 'fontawesome-iconpicker.min.js', array(), SM_RWMB_EXTEND_VER, true );

			wp_enqueue_script( 'sm-rwmb-field-icon', SM_RWMB_EXTEND_JS_URL . 'icon.js', array( 'jquery' ), SM_RWMB_EXTEND_VER, true );

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
				'<input data-placement="bottomRight" class="form-control icp icp-auto sm-rwmb-icon" name="%s" id="%s" value="%s" type="text" /><span class="input-group-addon"></span>',
				$field['field_name'],
				$field['id'],
				$meta
			);
		}
	}
}