jQuery( function( $ )
{
	'use strict';


	var SM_MB_Field = {};
	 
	SM_MB_Field.init = function() {

		var self = this;
		
		self.initEvents();
	
	};
	
	SM_MB_Field.initEvents = function() {	
		
		
		function sm_rwmb_update_slider()
		{
			var $input = $( this ),
				$slider = $input.siblings( '.sm-rwmb-slider' ),
				$valueLabel = $slider.siblings( '.sm-rwmb-slider-value-label' ).find( 'span' ),
				value = $input.val(),
				options = $slider.data( 'options' );
	
			$slider.html( '' );
	
			if ( !value )
			{
				value = 0;
				$input.val( 0 );
				$valueLabel.text( '0' );
			}
			else
			{
				$valueLabel.text( value );
			}
	
			// Assign field value and callback function when slide
			options.value = value;
			options.slide = function( event, ui )
			{
				$input.val( ui.value );
				$valueLabel.text( ui.value );
			};
			options.change = function( event, ui ) {
				$valueLabel.text( ui.value );
			};
	
			$slider.slider( options );
		}
	
		
		$(document).on('sm_mb_init_events', function(e, data) {
			
			$(data.target).find( ':input.sm-rwmb-slider-value' ).each( sm_rwmb_update_slider );
		});	
		
		$( ':input.sm-rwmb-slider-value' ).each( sm_rwmb_update_slider );
		$( document ).on( 'clone', '.sm-rwmb-input :input.sm-rwmb-slider-value', sm_rwmb_update_slider );
			
	};
	
	SM_MB_Field.init();	

} );
