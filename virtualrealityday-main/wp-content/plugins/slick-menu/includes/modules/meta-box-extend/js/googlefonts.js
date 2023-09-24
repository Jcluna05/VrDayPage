jQuery( document ).ready(function( $ ) {

	var SM_MB_Field = {};
	 
	SM_MB_Field.init = function() {

		var self = this;
		
		self.initEvents();

	};
	
	SM_MB_Field.initEvents = function() {	
		
		function update() {
    
			$(this).not('.loaded').addClass('loaded').fontSelect({api_key: rwmb_googlefonts.api_key});
		}
	
		$(document).on('sm_mb_init_events', function(e, data) {
			
			$(data.target).find( ':input.sm-rwmb-googlefonts' ).each( update );
		});	
			
		$( ':input.sm-rwmb-googlefonts' ).each( update );
		$( document ).on( 'clone', '.sm-rwmb-input input.sm-rwmb-googlefonts', update );

	};
	
	SM_MB_Field.init();	
	 
});

