jQuery( function( $ )
{
	'use strict';

	var SM_MB_Field = {};
	 
	SM_MB_Field.init = function() {

		var self = this;
		
		self.initEvents();
	};
	
	SM_MB_Field.initEvents = function() {	
		
		$( document ).ajaxSend( function( e, xhr, s )
		{
			if ( typeof s.data !== 'undefined' &&  -1 !== s.data.indexOf( 'action=autosave' ) )
			{
				$( '.sm-rwmb-meta-box').each( function()
				{
					var $meta_box = $( this );
					if ( $meta_box.data( 'autosave' ) === true )
					{
						s.data += '&' + $meta_box.find( ':input' ).serialize();
					}
				} );
			}
		} );
		
	};
	
	SM_MB_Field.init();	
} );
