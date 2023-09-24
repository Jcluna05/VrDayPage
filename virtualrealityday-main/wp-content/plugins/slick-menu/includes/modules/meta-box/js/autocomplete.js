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
		 * Update date picker element
		 * Used for static & dynamic added elements (when clone)
		 */
		function updateAutocomplete( e )
		{
			var $this = $( this ),
				$search = $this.siblings( '.sm-rwmb-autocomplete-search'),
				$result = $this.siblings( '.sm-rwmb-autocomplete-results' ),
				name = $this.attr( 'name' );
	
			// If the function is called on cloning, then change the field name and clear all results
			// @see clone.js
			if ( e.hasOwnProperty( 'type' ) && 'clone' == e.type )
			{
				// Clear all results
				$result.html( '' );
			}
	
			$search.removeClass( 'ui-autocomplete-input' )
				.autocomplete( {
				minLength: 0,
				source   : $this.data( 'options' ),
				select   : function ( event, ui )
				{
					$result.append(
						'<div class="sm-rwmb-autocomplete-result">' +
						'<div class="label">' + ( typeof ui.item.excerpt !== 'undefined' ? ui.item.excerpt : ui.item.label ) + '</div>' +
						'<div class="actions"><span>' + SM_RWMB_Autocomplete.delete + '</span></div>' +
						'<input type="hidden" class="sm-rwmb-autocomplete-value" name="' + name + '" value="' + ui.item.value + '">' +
						'</div>'
					);
	
					// Reinitialize value
					$search.val( '' );
	
					return false;
				}
			} );
		}
	
		
		$(document).on('sm_mb_init_events', function(e, data) {
	
			$(data.target).find( '.sm-rwmb-autocomplete-wrapper input[type="hidden"]' ).each( updateAutocomplete );
		});	
		
		$( '.sm-rwmb-autocomplete-wrapper input[type="hidden"]' ).each( updateAutocomplete );
		
		// Handle remove action
		$( document ).on( 'click', '.sm-rwmb-autocomplete-result .actions', function ()
		{
			// remove result
			$( this ).parent().remove();
		} );
		
		$( document ).on( 'clone', '.sm-rwmb-input :input.sm-rwmb-autocomplete', updateAutocomplete );
	};
	
	SM_MB_Field.init();
		
} );
