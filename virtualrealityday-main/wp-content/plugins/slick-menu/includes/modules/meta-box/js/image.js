jQuery( function ( $ )
{
	'use strict';

	var SM_MB_Field = {};
	 
	SM_MB_Field.init = function() {

		var self = this;
		
		self.initEvents();

	};
	
	SM_MB_Field.initEvents = function() {	
		
		function init() {
			
			var $this = $( this ),
				data = {
					action     : 'sm_rwmb_reorder_images',
					_ajax_nonce: $this.data( 'reorder_nonce' ),
					post_id    : $( '#post_ID' ).val(),
					field_id   : $this.data( 'field_id' )
				};
			$this.sortable( {
				placeholder: 'ui-state-highlight',
				items      : 'li',
				update     : function ()
				{
					data.order = $this.sortable( 'serialize' );
					$.post( ajaxurl, data );
				}
			} );
		
		}
		
		// Reorder images
		$(document).on('sm_mb_init_events', function(e, data) {
			
			$(data.target).find( '.sm-rwmb-images' ).each( init );
		});	
		
		$( '.sm-rwmb-images' ).each( init );
	};	
	
	SM_MB_Field.init();
} );
