;( function( window, $ ) {
		
	'use strict';
			
	$( document ).ready( function () {

		$('.animated.animation-action').each(function() {
		    if(!$(this).isOnScreen()) {
		        $(this).removeClass('animation-action').addClass('animation-standby'); 
		    }
		});
	});	
	
} )( window, jQuery );			
