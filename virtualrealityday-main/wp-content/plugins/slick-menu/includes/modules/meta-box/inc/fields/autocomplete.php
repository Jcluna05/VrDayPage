<?php

/**
 * Autocomplete field class.
 */
class SM_RWMB_Autocomplete_Field extends SM_RWMB_Multiple_Values_Field
{
	/**
	 * Enqueue scripts and styles.
	 */
	static function admin_enqueue_scripts()
	{
		wp_enqueue_style( 'sm-rwmb-autocomplete', SM_RWMB_CSS_URL . 'autocomplete.css', array( 'wp-admin' ), SM_RWMB_VER );
		wp_enqueue_script( 'sm-rwmb-autocomplete', SM_RWMB_JS_URL . 'autocomplete.js', array( 'jquery-ui-autocomplete' ), SM_RWMB_VER, true );

		/**
		 * Prevent loading localized string twice.
		 * @link https://github.com/rilwis/meta-box/issues/850
		 */
		$wp_scripts = wp_scripts();
		if ( ! $wp_scripts->get_data( 'sm-rwmb-autocomplete', 'data' ) )
		{
			wp_localize_script( 'sm-rwmb-autocomplete', 'SM_RWMB_Autocomplete', array( 'delete' => __( 'Delete', 'meta-box' ) ) );
		}
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
		if ( ! is_array( $meta ) )
			$meta = array( $meta );

		$field   = apply_filters( 'sm_rwmb_autocomplete_field', $field, $meta );
		$options = $field['options'];
		
		if ( ! is_string( $field['options'] ) )
		{
			$options = array();
			foreach ( (array) $field['options'] as $value => $label )
			{
				$options[] = array(
					'value' => $value,
					'label' => $label,
				);
			}
			$options = wp_json_encode( $options );
		}
		
		$placeholder = !empty($field['placeholder']) ? $field['placeholder'] : '';
		

		// Input field that triggers autocomplete.
		// This field doesn't store field values, so it doesn't have "name" attribute.
		// The value(s) of the field is store in hidden input(s). See below.
		$html = sprintf(
			'<input type="text" class="sm-rwmb-autocomplete-search" size="%s" placeholder="%s">
			<input type="hidden" name="%s" id="%s" class="sm-rwmb-autocomplete" data-options="%s" disabled>',
			$field['size'],
			$placeholder,
			$field['field_name'],
			$field['id'],
			esc_attr( $options )
		);

		$html .= '<div class="sm-rwmb-autocomplete-results">';

		// Each value is displayed with label and 'Delete' option
		// The hidden input has to have ".sm-rwmb-*" class to make clone work
		$tpl = '
			<div class="sm-rwmb-autocomplete-result">
				<div class="label">%s</div>
				<div class="actions"><span>%s</span></div>
				<input type="hidden" class="sm-rwmb-autocomplete-value" name="%s" value="%s">
			</div>
		';

		if ( is_array( $field['options'] ) )
		{
			foreach ( $field['options'] as $value => $label )
			{
				if ( in_array( $value, $meta ) )
				{
					$html .= sprintf(
						$tpl,
						$label,
						__( 'Delete', 'meta-box' ),
						$field['field_name'],
						$value
					);
				}
			}
		}
		else
		{
			foreach ( $meta as $value )
			{
				if ( empty( $value ) )
					continue;
					
				$label = apply_filters( 'sm_rwmb_autocomplete_result_label', $value, $field );
		
				$html .= sprintf(
					$tpl,
					$label,
					__( 'Delete', 'meta-box' ),
					$field['field_name'],
					$value
				);
			}
		}

		$html .= '</div>'; // .sm-rwmb-autocomplete-results

		return $html;
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
		$field = wp_parse_args( $field, array(
			'size' => 30
		) );
		return $field;
	}
}
