<?php
/**
 * Icon fonts handler
 *
 * @package SM_Icons
 * 
 */

require_once dirname( __FILE__ ) . '/type.php';

/**
 * Generic handler for icon fonts
 *
 */
abstract class SM_Icons_Type_Fonts extends SM_Icons_Type {
	/**
	 * Constructor
	 *
	 * @since 0.9.0
	 */
	public function __construct() {
		_deprecated_function( __CLASS__, '0.9.0', 'SM_Icon_Picker_Type_Font' );
	}
}
