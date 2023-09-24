;( function( $ ) {
		
	'use strict';
	
	var SM_DEBUG = {};
	
	// SET HTML CLASSES ----------------------------------------------------------
		
	SM_DEBUG = {
		
		init: function() {
			
			$('.slick-menu-debug-box').each(function() {
				$(this).appendTo('#wpcontent');
			});
		}

	};
	
	$(document).ready(function() {
		
		SM_DEBUG.init();
	});

} )( jQuery );	