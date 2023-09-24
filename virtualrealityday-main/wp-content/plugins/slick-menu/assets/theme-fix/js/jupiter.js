;( function( window, $ ) {
		
	'use strict';
			
	$( document ).ready( function () {

		if(wp.customize && window.top.SM_MB_NAV_MENU.init) {
			window.top.SM_MB_NAV_MENU.init();
		}
		
	});	
	
} )( window, jQuery );			