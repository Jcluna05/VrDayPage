;( function( $ ) {
		
	'use strict';

	var SM_SETTINGS = {};

	
	// SET HTML CLASSES ----------------------------------------------------------
		
	SM_SETTINGS = {
		
		init: function() {
		
			var self = this;
			self.styleLink = $('link#slick-menu-dynamic-hamburgers-css');
	        self.styleUrl = self.styleLink.attr('href');
			self.refreshingCSS = false;
			
			setTimeout(function() {
			
				self.construct();
				self.initEvents();
			
			}, 10);
			
		},
		
		construct: function() {
			
			var self = this;

			var $form = $('#poststuff');
			var $nav = $('.nav-tab-wrapper');
			
			var $logo = $('<a href="'+SM_VARS.settings_url+'" class="nav-tab sm-logo"><img src="'+SM_VARS.assets_url + 'images/logo-inverted.png"></a>');
			var $version = $('<span class="sm-version">'+SM_VARS.plugin_name+' - '+SM_VARS.plugin_version+'</span>');
			
			if(!$nav.find('a.nav-tab-active').length) {
				$nav.find('a').first().trigger('click');
			}	
			
			$nav.prepend($logo);
			$logo.on('click', function(e) {
				 e.preventDefault();
				 e.stopPropagation();
				 
				 $nav.find('a').first().next().trigger('click');
		 	});
			
			$nav.find('a').wrapInner('<span>');
			
			$('.sm-rwmb-hamburger-group').each(function() {
				
				var $input = $(this).find(' > .sm-rwmb-input');
				
				var $wrapper = $('<div class="sm-rwmb-hamburger-wrapper"></div>');
				var $col1 = $('<div class="sm-rwmb-hamburger-options"></div>');
				var $col2 = $('<div class="sm-rwmb-hamburger-preview"></div>');

				$input.children().each(function() {
					
					if(!$(this).hasClass('sm-rwmb-select-wrapper')) {
	
						if($(this).hasClass('sm-rwmb-hamburger-select-wrapper')) {
							$(this).appendTo($col2);
						}else{
							$(this).appendTo($col1);
						}
					}
				});	
				
								
				$col1.appendTo($wrapper);
				$col2.appendTo($wrapper);
				
				$wrapper.appendTo($input)
				
			});
			
			$form.find('.submit').append($version);
		},

		initEvents: function() {
			
			var self = this;
			
			/* Hamburger Dynamic CSS refresh */
			
			var onChange = function() { 
					
				self.refreshHamburgerCSS(true);
			};
			
			var onHamburgerActive = function(target, group, wrapper, position) {
				
				if(!$(group).hasClass('hamburger-selected')) {
					
					$(group).addClass('hamburger-selected');
					
					$(wrapper).find('>.sm-rwmb-label label').html('Hamburger Preview');
				}	
				
				$(target).find('.sm-hamburger').addClass('sm-position-'+position);

			};
			
			var onHamburgerInactive = function(target, group, wrapper, position) {
				
				if($(group).hasClass('hamburger-selected')) {
				
					$(group).removeClass('hamburger-selected');
					
					$(wrapper).find('>.sm-rwmb-label label').html('Select Hamburger');
				}

				$(target).find('.sm-hamburger').removeClass (function (index, css) {
				    return (css.match (/(^|\s)sm-position-\S+/g) || []).join(' ');
				});
			};
			
			var onResize = function(group) {
				
				var winWidth = $(window).width();
				var options = group.find('.sm-rwmb-hamburger-options');
				var preview = group.find('.sm-rwmb-hamburger-preview');
				var isMobile = $(group).data('is-mobile');
				var firstTime = isMobile === undefined;
				
				if(winWidth < 1090 && (!isMobile || firstTime)) {
					
					$(group).data('is-mobile', true);
					preview.insertBefore(options); 
					
				}else if(winWidth >= 1090 && (isMobile || firstTime)) {
					
					$(group).removeData('is-mobile', false);
					options.insertBefore(preview); 
				}
			};
			
						
			var positions = {
				left: '.slick-menu-hamburger-top-left',
				right: '.slick-menu-hamburger-top-right'
			};
			
			$.each(positions, function(position, selector) {
				
				var group = $(selector);
				
				group.find('.sm-rwmb-slider').on('slidechange', onChange);
				group.find('input').on('change', onChange); 
				
				group.find('.sm-rwmb-hamburger-select').on('sm_rwmb_hamburger_active', function(e, wrapper) {
					
					onHamburgerActive(e.target, group, wrapper, position);
				});
				
				group.find('.sm-rwmb-hamburger-select').on('sm_rwmb_hamburger_inactive', function(e, wrapper) {
					
					onHamburgerInactive(e.target, group, wrapper, position);
				});
		
				$(window).on('resize', function(e) {
					onResize(group);
				});
			
			});
			
			$(document).on('change', '.sm-rwmb-select-wrapper select', onChange);

			$(window).trigger('resize');
			
			$(window).on('load', function() {
				
				$('.sm-rwmb-hamburger-preview').theiaStickySidebar({
			      	additionalMarginTop: 50
			    });
			    
			    self.refreshHamburgerCSS();
			})				
					
		},
		
		
	    refreshHamburgerCSS: function(nocache, callback) {
	        
	        var self = this;
	        
	        if(self.refreshingCSS) {
		        return false;
	        }
	        
	        var $form = $('#poststuff');
	        var formData = $form.serialize();
	        var $inlineStyle = $('#slick-menu-inline-hamburgers-css');
	        var nocache = (nocache === true) ? true : false;
	        
	        var url = self.styleUrl;
	        if(nocache) {
		        url = self.updateQueryParam(self.styleUrl, 'nocache', 'true');
		    }    

			if($inlineStyle.length === 0) {
				
				 $inlineStyle = $('<style id="slick-menu-inline-hamburgers-css"></style>');
				 $inlineStyle.appendTo('body');	
			}
			
			self.refreshingCSS = true;
			
	      	$.ajax({
	            url         : url, // the url where we want to POST
	            type        : 'POST', // define the type of HTTP verb we want to use (POST for our form)
	            data        : formData, // our data object
	            dataType    : 'html', // what type of data do we expect back from the server
  			})
            // using the done promise callback
            .done(function(data) {
				
				$inlineStyle.html(data);	
				
				if(self.styleLink.length) {
					self.styleLink.remove();
				}
				
				self.refreshingCSS = false;

			    if(typeof(callback) !== 'undefined') {
					callback(data); 
				}
					
            });	         
        },

            
		updateQueryParam: function (uri, key, value) {
			
			if (!uri) {
				return false;
			} 
			var re = new RegExp("([?&])" + key + "=.*?(&|$)", "i");
			var separator = uri.indexOf('?') !== -1 ? "&" : "?";
			if (uri.match(re)) {
				return uri.replace(re, '$1' + key + "=" + value + '$2');
			}
			else {
				return uri + separator + key + "=" + value;
			}
		}
						
	};
	
	$(document).ready(function() {
		
		SM_SETTINGS.init();	
	});
	
} )( jQuery );	