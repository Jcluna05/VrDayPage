;( function( window, $ ) {
		
	'use strict';
			
	$( document ).ready( function () {

		function ajaxSetActiveState(me){
			"use strict";
			
			$('.sm-menu li.current-menu-item').removeClass('current-menu-item');
			$('.sm-menu a.current').removeClass('current');
			
			me.addClass('current');
			me.parent().addClass('current-menu-item');
			
		}
			
		$(document).on('sm-loaded sm-reloaded', function(e) {
			
			$(e.detail.menu).find('li.menu-item-has-children > a, .sm-back a, .sm-close a').each(function() {
				
				$(this).addClass('no_link');
			});
		});		

		$(document).on('sm-opening', function() {
			
			var container = $(SM.setting('menu-container', 'body'));
			container.addClass('sm-body');
			
			SM.disableEvents(window, 'scroll');
			SM.disableEvents(window, 'resize');

		});
		
		$(document).on('sm-close', function() {
			
			SM.enableEvents(window, 'scroll');
			SM.enableEvents(window, 'resize');
			SM.updateContentScroll(1);

		});
		
		$(document).ajaxStart(function() {
			
			var menu = SlickMenu.getOpen();
			if(menu !== null) {
				SlickMenu.close(menu, function() {
					$('html,body').stop().animate({scrollTop: 0}, 700, 'swing', function() {
						SM.updateContentScroll(1);
					});
				});
			}
		});
		
		$(document).ajaxComplete(function( event, xhr, settings ) {

			var container = $(SM.setting('menu-container', 'body'));
			
			setTimeout(function() {
				
				container.addClass('sm-body');
				
			},1000);
			
			
			var me = $(".sm-menu a[href='"+settings.url+"']");
			ajaxSetActiveState(me);

		});
		
		var me = $(".sm-menu a[href='"+location.href+"']");
		ajaxSetActiveState(me);
		
	});	
	
} )( window, jQuery );			