jQuery( document ).ready(function( $ ) {

	var SM_MB_Field = {};
	 
	SM_MB_Field.init = function() {

		var self = this;
		
		self.initEvents();

	};
	
	SM_MB_Field.initEvents = function() {	
		
		function update() {
			
			$(this).not('loaded').addClass('loaded').iconpicker({placement:'bottomLeft'});
		}
		

		$(document).on('sm_mb_init_events', function(e, data) {
			
			$(data.target).find(':input.sm-rwmb-icon' ).each( update );
		});	
		
		$( ':input.sm-rwmb-icon' ).each( update );
		$( document ).on( 'clone', 'input.sm-rwmb-icon', update );
		
	};
	
	SM_MB_Field.init();	
});

