/* global jQuery, google */

jQuery( function ( $ )
{
	'use strict';

	/**
	 * Refresh Google maps, make sure they're fully loaded
	 * The problem is Google maps won't fully display when it's in hidden div (accordion)
	 * We need to find all maps and send the 'resize' command to force them to refresh
	 *
	 * @see https://developers.google.com/maps/documentation/javascript/reference
	 *      ('resize' Event)
	 *
	 * @return void
	 */
	 
	 
	var SM_MB_Field = {};
	 
	SM_MB_Field.init = function() {

		var self = this;
		
		self.initEvents();
	};
	
	SM_MB_Field.initEvents = function() {
		 
		function refreshMap(_target)
		{
			if ( typeof google !== 'object' || typeof google.maps !== 'object' )
				return;
	
			$(_target).find('.sm-rwmb-map-field' ).each( function ()
			{
				var controller = $( this ).data( 'mapController' );
	
				if ( typeof controller !== 'undefined' && typeof controller.map !== 'undefined' )
				{
					google.maps.event.trigger( controller.map, 'resize' );
				}
			} );
		}
		
		function update(target) {
			
			// Set active accordion based on visible pane to better works with Meta Box Conditional Logic
			if ( ! $(target).find( '.sm-rwmb-accordion-active').is( 'visible' ) )
			{
				// Find the active pane
				var activePane = $(target).find('.sm-rwmb-accordion-panel[style*="block"]' ).index();
		
				if (activePane >= 0 )
				{
					$(target).find( '.sm-rwmb-accordion-title' ).removeClass( 'sm-rwmb-accordion-active' );
		
					$(target).find( '.sm-rwmb-accordion-title' ).eq( activePane ).addClass( 'sm-rwmb-accordion-active' );
				}
			}	
			
			$(target).find( '.sm-rwmb-accordion-active a' ).trigger( 'click' );
			$(target).find( '.sm-rwmb-accordions-no-wrapper' ).each(function() {
				$(this).closest( '.postbox' ).addClass( 'sm-rwmb-accordions-no-controls' );		
			});						
			
		}

		$(document).on( 'click', '.sm-rwmb-accordion-title a', function ( e )
		{
			e.preventDefault();
	
			var $li = $( this ).parent(),
				panel = $li.data( 'panel' ),
				$wrapper = $li.closest( '.sm-rwmb-accordions' ),
				$panel = $wrapper.find( '.sm-rwmb-accordion-panel-' + panel );
	
			$li.siblings().removeClass( 'sm-rwmb-accordion-active' );
			
			if(!$li.hasClass('sm-rwmb-accordion-active' )) {
				$li.addClass( 'sm-rwmb-accordion-active' );
				refreshMap($panel);
			}else{
				$li.removeClass( 'sm-rwmb-accordion-active' );
			}
			
		});
		
		$(document).on('sm_mb_init_events', function(e, data) {
			
			update(data.target);
		});	
		
		update('body');

	} 
	
	SM_MB_Field.init();
	
} );
