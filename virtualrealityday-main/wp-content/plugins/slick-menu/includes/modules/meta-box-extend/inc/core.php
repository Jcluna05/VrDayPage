<?php
	
if ( defined( 'ABSPATH' ) && ! class_exists( 'SM_RWMB_EXTEND_Core' ) )
{

	class SM_RWMB_EXTEND_Core {
		
		function __construct() {
			
			add_action( 'plugins_loaded', array($this, 'load_fields'), 1 );
		}
		
		function load_fields() {
			
			$fields = glob(SM_RWMB_EXTEND_FIELDS_DIR."*.php");
		
			foreach($fields as $field) {
		
				require_once $field;	
			}

		}

	}
	
	new SM_RWMB_EXTEND_Core;
}

