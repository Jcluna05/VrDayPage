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
		 * Turn select field into beautiful dropdown with select2 library
		 * This function is called when document ready and when clone button is clicked (to update the new cloned field)
		 *
		 * @return void
		 */
		function update()
		{
			var $this = $( this ),
				options = $this.data( 'options' );
			$this.siblings( '.select2-container' ).remove();
			$this.show().select2( options );
	
			rwmbSelect.bindEvents( $this );
		}
	
		$(document).on('sm_mb_init_events', function(e, data) {
			
			$(data.target).find( ':input.sm-rwmb-select_advanced' ).each( update );
		});	
		
		$( ':input.sm-rwmb-select_advanced' ).each( update );
		$( document ).on( 'clone', '.sm-rwmb-input input.sm-rwmb-select_advanced', update );
		
	};
	
	SM_MB_Field.init();	
} );
