<?php
/**
 * This class defines new "icon" field type for Meta Box class
 * 
 */
if ( class_exists( 'SM_RWMB_Field' ) )
{
	class SM_RWMB_Googlefonts_Field extends SM_RWMB_Field
	{

		/**
		 * Enqueue scripts and styles.
		 */
		static function admin_enqueue_scripts()
		{

			wp_enqueue_script( 'sm-rwmb-field-google-webfonts', '//ajax.googleapis.com/ajax/libs/webfont/1/webfont.js', array( 'jquery' ), SM_RWMB_EXTEND_VER, true );

			wp_enqueue_style( 'sm-rwmb-jquery-fontselect', SM_RWMB_EXTEND_CSS_URL . 'font-select.css', array(), SM_RWMB_EXTEND_VER );
			wp_enqueue_script( 'sm-rwmb-jquery-fontselect', SM_RWMB_EXTEND_JS_URL . 'jquery.font-select.js', array(), SM_RWMB_EXTEND_VER, true );
			
			wp_enqueue_script( 'sm-rwmb-field-googlefonts', SM_RWMB_EXTEND_JS_URL . 'googlefonts.js', array( 'jquery' ), SM_RWMB_EXTEND_VER, true );
	
			wp_localize_script( 'sm-rwmb-field-googlefonts', 'rwmb_googlefonts', array(
				'api_key' => Slick_Menu()->get_setting('google-fonts-api-key')
			));

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
			$api_key = Slick_Menu()->get_setting('google-fonts-api-key');
			
			if(empty($api_key)) {
				return sprintf(
					'<span style="color:red">Please configure your google font API Key within Slick Menu <a href="%s" target="_blank">Global Settings</a></span>',
					Slick_Menu()->plugin_url('settings')
				);
			}
			
			return sprintf(
				'<input class="form-control sm-rwmb-googlefonts sm-rwmb-input" name="%s" id="%s" value="%s" type="text" />',
				$field['field_name'],
				$field['id'],
				$meta
			);
		}
	}
}