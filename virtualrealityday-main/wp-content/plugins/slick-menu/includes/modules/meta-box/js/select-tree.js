jQuery( function( $ )
{
	'use strict';


	var SM_MB_Field = {};
	 
	SM_MB_Field.init = function() {

		var self = this;
		
		self.initEvents();

	};
	
	SM_MB_Field.initEvents = function() {	
		
		function update()
		{
			var $this = $( this ),
				val = $this.val(),
				$selected = $this.siblings( "[data-parent-id='" + val + "']" ),
				$notSelected = $this.parent().find( '.sm-rwmb-select-tree' ).not( $selected );
	
			$selected.removeClass( 'hidden' );
			$notSelected
				.addClass( 'hidden' )
				.find( 'select' )
				.prop( 'selectedIndex', 0 );
		}

		$( document ).on( 'change', '.sm-rwmb-input .sm-rwmb-select-tree select', update )
		$( document ).on( 'clone', '.sm-rwmb-input .sm-rwmb-select-tree select', update );
			
	};
	
	SM_MB_Field.init();			
} );
