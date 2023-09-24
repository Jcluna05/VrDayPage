jQuery( function ( $ )
{
	'use strict';


	var SM_MB_Field = {};
	 
	SM_MB_Field.init = function() {

		var self = this;
		
		self.initEvents();

	};
	
	SM_MB_Field.initEvents = function() {	
		
		/**
		 * Update color picker element
		 * Used for static & dynamic added elements (when clone)
		 */
		function update()
		{
			var $this = $( this ),
				$output = $this.siblings( '.sm-rwmb-output' );
	
		    $this.on( 'input propertychange change', function( e )
		    {
		      $output.html( $this.val() );
		    } );
	
		}
	
		$(document).on('sm_mb_init_events', function(e, data) {
			
			$(data.target).find( ':input.sm-rwmb-range' ).each( update );
		});	
		
		$( ':input.sm-rwmb-range' ).each( update );
		$( document ).on( 'clone', '.sm-rwmb-input input.sm-rwmb-range', update );
			
	};
	
	SM_MB_Field.init();	

} );
