<?php

/**
 * 'icon' field type for Custom Meta Boxes
 *
 * @link    https://github.com/humanmade/Custom-Meta-Boxes/wiki/Adding-your-own-field-types CMB Wiki
 * @version 0.1.0
 * @since   SM_Icon_Picker 0.2.0
 */
class SM_Icon_Picker_Field_Cmb extends CSM_MB_Field {
	/**
	 * Constructor
	 *
	 * @since 0.1.0
	 * @see   {CSM_MB_Field::__construct()}
	 */
	public function __construct( $name, $title, array $values, $args = array() ) {
		parent::__construct( $name, $title, $values, $args );
		SM_Icon_Picker::instance()->load();
	}


	/**
	 * Display the field
	 *
	 * @since 0.1.0
	 * @see   {CSM_MB_Field::html()}
	 */
	public function html() {
		slick_menu_icon_picker_field( array(
			'id'    => $this->id,
			'name'  => $this->get_the_name_attr(),
			'value' => $this->get_value(),
		) );
	}
}
