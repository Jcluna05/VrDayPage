<?php
/**
 * Input list field.
 */
class SM_RWMB_Input_List_Field extends SM_RWMB_Choice_Field
{
	/**
	 * Enqueue scripts and styles
	 */
	public static function admin_enqueue_scripts()
	{
		wp_enqueue_style( 'sm-rwmb-input-list', SM_RWMB_CSS_URL . 'input-list.css', array(), SM_RWMB_VER );
		wp_enqueue_script( 'sm-rwmb-input-list', SM_RWMB_JS_URL . 'input-list.js', array(), SM_RWMB_VER, true );
	}

	/**
	 * Walk options
	 *
	 * @param mixed $meta
	 * @param array $field
	 * @param mixed $options
	 * @param mixed $db_fields
	 *
	 * @return string
	 */
	public static function walk( $field, $options, $db_fields, $meta )
	{
		$walker = new SM_RWMB_Input_List_Walker( $db_fields, $field, $meta );
		$output = sprintf( '<ul class="sm-rwmb-input-list %s %s">',
			$field['collapse'] ? 'collapse' : '',
		 	$field['inline']   ? 'inline'   : ''
		);
		$output .= $walker->walk( $options, $field['flatten'] ? - 1 : 0 );
		$output .= '</ul>';

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
		$field = $field['multiple'] ? SM_RWMB_Multiple_Values_Field::normalize( $field ) : $field;
		$field = SM_RWMB_Input_Field::normalize( $field );
		$field = parent::normalize( $field );
		$field = wp_parse_args( $field, array(
			'collapse' => true,
			'inline'   => null,
		) );

		$field['flatten'] = $field['multiple'] ? $field['flatten'] : true;
		$field['inline'] = ! $field['multiple'] && ! isset( $field['inline'] ) ? true : $field['inline'];

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
		$attributes           = SM_RWMB_Input_Field::get_attributes( $field, $value );
		$attributes['id']     = false;
		$attributes['type']   = $field['multiple'] ? 'checkbox' : 'radio';
		$attributes['value']  = $value;

		return $attributes;
	}
}
