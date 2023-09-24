<?php
/**
 * jQueryUI slider field class.
 */
class SM_RWMB_Slider_Field extends SM_RWMB_Field
{
	/**
	 * Enqueue scripts and styles
	 *
	 * @return void
	 */
	static function admin_enqueue_scripts()
	{
		if(!parent::is_edit_screen()) {
			return false;
		}
		
		$url = SM_RWMB_CSS_URL . 'jqueryui';
		wp_enqueue_style( 'jquery-ui-core', "{$url}/jquery.ui.core.css", array(), '1.8.17' );
		wp_enqueue_style( 'jquery-ui-theme', "{$url}/jquery.ui.theme.css", array(), '1.8.17' );
		wp_enqueue_style( 'jquery-ui-slider', "{$url}/jquery.ui.slider.css", array(), '1.8.17' );
		wp_enqueue_style( 'sm-rwmb-slider', SM_RWMB_CSS_URL . 'slider.css' );

		wp_enqueue_script( 'sm-rwmb-slider', SM_RWMB_JS_URL . 'slider.js', array( 'jquery-ui-slider', 'jquery-ui-widget', 'jquery-ui-mouse', 'jquery-ui-core' ), SM_RWMB_VER, true );
	}

	/**
	 * Get div HTML
	 *
	 * @param mixed $meta
	 * @param array $field
	 *
	 * @return string
	 */
	static function html( $meta, $field )
	{		
		return sprintf(
			'<div class="clearfix">
				<div class="sm-rwmb-slider" id="%s" data-options="%s"></div>
				<span class="sm-rwmb-slider-value-label">%s<span>%s</span>%s</span>
				<input type="hidden" name="%s" value="%s" class="sm-rwmb-slider-value">
			</div>',
			$field['id'], 
			esc_attr( wp_json_encode( $field['js_options'] ) ),
			$field['prefix'], 
			( isset($meta) ) ? $meta : $field['std'], 
			$field['suffix'],
			$field['field_name'], 
			( isset($meta) ) ? $meta : $field['std']
		);
	}

	/**
	 * Normalize parameters for field
	 *
	 * @param array $field
	 *
	 * @return array
	 */
	static function normalize( $field )
	{
		$field               = parent::normalize( $field );
		$field               = wp_parse_args( $field, array(
			'prefix'     => '',
			'suffix'     => '',
			'std'      	 => '',
			'js_options' => array(),
		) );
		
		$field['js_options'] = wp_parse_args( $field['js_options'], array(
			'range' => 'min', // range = 'min' will add a dark background to sliding part, better UI
			'value' => $field['std'],
		) );

		return $field;
	}
}
