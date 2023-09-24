;( function( $ ) {
		
	'use strict';
	
	var SM_ADMIN = {};

	
	// SET HTML CLASSES ----------------------------------------------------------
		
	SM_ADMIN.global = {
		
		init: function() {
			
			this.initLoading();
		},
		
		initLoading: function() {
			
			$('.slick-menu-debug-box').each(function() {
				$(this).appendTo('#wpcontent');
			});
			$('html').addClass('sm-admin-loaded');
		},
	};

	// METABOX OPTIONS ----------------------------------------------------------
	
	SM_ADMIN.metabox = {
		
		init: function() {
			
			var self = this;
			
		    self.initEvents('body');	
		    
		    $(document).on('sm_mb_init_events', function(e, data) {
	
			    self.initEvents(data.target);
		    });	
		},
		
		initEvents: function(target) {
		
			var self = this;
			
			$(target).find('.sm-rwmb-tabs').each(function(i) {
				
				var $tabs = $(this);
				var cookie_key = 'sm_rwmb_tabs_'+i+'_active_tab';
				
				$tabs.find( '.sm-rwmb-tab-nav' ).on( 'mouseup', 'a', function ( e ) {
					
					var $li = $( this ).parent(),
					panel = $li.data( 'panel' );
					
					SM_ADMIN.cookie.set(cookie_key, panel);
				});
				
				if(SM_ADMIN.cookie.exists(cookie_key)) {
					
					var panel = SM_ADMIN.cookie.get(cookie_key);
					var tab = $tabs.find('li[data-panel="'+panel+'"] a');
			
					setTimeout(function() {
						tab.trigger('click');
						tab.trigger('mouseup');
					}, 1);
					
				}
			});

		}
	};	


	
	// SM POPUP ----------------------------------------------------------

	SM_ADMIN.popup = {
			
		init: function() {
				
			$('.sm-popup').each(function() {
				
				var first_time = true;
				$(this).on('click', function(e){
					e.preventDefault();
					
					var $button = $(this);
					var target = $button.data('target');
					
					$(target).fadeIn();
					
					if(first_time) {
									
						$(target).find('.sm-popup-inner').on('click', function(e) {
							e.stopPropagation();
						});
							
						$(window).on('click', function() {
							$(target).fadeOut();
						});

						first_time = false;
					}
					e.stopPropagation();
					
				});
			});
		}
	};		

	
	// COOKIES FUNCTIONS ----------------------------------------------------------

	SM_ADMIN.cookie = {

		get: function(cname) {
		    var name = cname + "=";
		    var ca = document.cookie.split(';');
		    for(var i = 0; i < ca.length; i++) {
		        var c = ca[i];
		        while (c.charAt(0) === ' ') {
		            c = c.substring(1);
		        }
		        if (c.indexOf(name) === 0) {
		            return c.substring(name.length, c.length);
		        }
		    }
		    return "";
		},		
		set: function(cname, cvalue, exdays) {
		    var d = new Date();
		    d.setTime(d.getTime() + (exdays*24*60*60*1000));
		    var expires = "expires="+d.toUTCString();
		    document.cookie = cname + "=" + cvalue + "; " + expires;
		},
		exists: function(cname) {
		    var value = this.get(cname);
		    if (value !== "") {
		        return true;
		    } else {
		        return false;
		    }
		}
	
	};
	
	$(document).ready(function() {

		SM_ADMIN.global.init();	
		SM_ADMIN.metabox.init();	
		SM_ADMIN.popup.init();
	});

})( jQuery );	