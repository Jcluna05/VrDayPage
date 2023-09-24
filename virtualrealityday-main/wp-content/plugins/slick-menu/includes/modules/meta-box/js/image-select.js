jQuery( function ( $ )
{
	'use strict';

	var SM_MB_Field = {};
	 
	SM_MB_Field.init = function() {

		var self = this;
		
		self.initEvents();
	};
	
	SM_MB_Field.initEvents = function() {	
		
		$( document ).on( 'change', '.sm-rwmb-image-select input', function ()
		{
			var $this = $( this ),
				type = $this.attr( 'type' ),
				selected = $this.is( ':checked' ),
				$parent = $this.parent(),
				$others = $parent.siblings();
			if ( selected )
			{
				$parent.addClass( 'sm-rwmb-active' );
				if ( type === 'radio' )
				{
					$others.removeClass( 'sm-rwmb-active' );
				}
			}
			else
			{
				$parent.removeClass( 'sm-rwmb-active' );
			}
		} );

		$(document).on('sm_mb_init_events', function(e, data) {
			
			$(data.target).find( '.sm-rwmb-image-select input' ).trigger( 'change' );
		});	

		$( '.sm-rwmb-image-select input' ).trigger( 'change' );

	};
	
	SM_MB_Field.init();
		
} );
