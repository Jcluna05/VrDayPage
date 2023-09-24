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
		 * Object stores all necessary methods for select All/None actions
		 * Assign to global variable so we can access to this object from select advanced field
		 */
		var select = window.rwmbSelect = {
			/**
			 * Select all/none for select tag
			 *
			 * @param $input jQuery selector for input wrapper
			 *
			 * @return void
			 */
			selectAllNone: function ( $input )
			{
				var $element = $input.find( 'select' );
	
				$input.on( 'click', '.sm-rwmb-select-all-none a', function ( e )
				{
					e.preventDefault();
					if ( 'all' == $( this ).data( 'type' ) )
					{
						var selected = [];
						$element.find( 'option' ).each( function ( i, e )
						{
							var $value = $( e ).attr( 'value' );
	
							if ( $value != '' )
							{
								selected.push( $value );
							}
						} );
						$element.val( selected ).trigger( 'change' );
					}
					else
					{
						$element.val( '' );
					}
				} );
			},
	
			/**
			 * Add event listener for select all/none links when click
			 *
			 * @param $el jQuery element
			 *
			 * @return void
			 */
			bindEvents: function ( $el )
			{
				var $input = $el.closest( '.sm-rwmb-input' ),
					$clone = $input.find( '.sm-rwmb-clone' );
	
				if ( $clone.length )
				{
					$clone.each( function ()
					{
						select.selectAllNone( $( this ) );
					} );
				}
				else
				{
					select.selectAllNone( $input );
				}
			}
		};
	
		/**
		 * Update select field when clicking clone button
		 *
		 * @return void
		 */
		function update()
		{
			select.bindEvents( $( this ) );
		}
	
		$(document).on('sm_mb_init_events', function(e, data) {
			
			$(data.target).find( ':input.sm-rwmb-select' ).each( update );
		});	
		
		// Run for select field
		$( ':input.sm-rwmb-select' ).each( update );
		$( document ).on( 'clone', '.sm-rwmb-input :input.sm-rwmb-select', update );
		
	};
	
	SM_MB_Field.init();		
} );
