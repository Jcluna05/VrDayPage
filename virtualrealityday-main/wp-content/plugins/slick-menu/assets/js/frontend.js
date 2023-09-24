;( function( window, $ ) {
		
	'use strict';
	
	var SM = {};

	// IGNORE ALL ERRORS
	$(window).on('error', function(e){
		$('html').addClass('sm-ready');
	}); 
	
	$(window).on('load', function(e){
		document.documentElement.className = document.documentElement.className.replace("no-js","js");
		$('html').addClass('sm-ready');
	}); 
					
	SM.ready = false;	
	SM.menus = $('.sm-menu');
	SM.hamburgers = $('.sm-hamburger');
	SM.wrapper = $('#sm-wrapper');
	SM.wrapperBg1 = $('#sm-wrapper-bg-1');
	SM.wrapperBg2 = $('#sm-wrapper-bg-2');
	SM.wrapperLoading = $('#sm-wrapper-loading');
	SM.wrapperInner = $('#sm-wrapper-loading');
	SM.pusher = $('#sm-pusher');
	SM.content = $('#sm-content');
	SM.contentInner = $('#sm-content-inner');
	SM.triggerSelectors = [];
	SM.alwaysVisibleId;
	SM.mobileBreakpoint = 0;
	SM.alwaysVisibleMobileBreakpoint = 0;
	SM.admin_bar_height = 0;
	SM.in_customizer = typeof(parent.window.wp) !== 'undefined' && typeof(parent.window.wp.customize) !== 'undefined';

	SM.loaderTemplate = '<div class="sm-loading-overlay">'+
							'<div class="sm-loading">' +
	       					'	<div class="sm-loading-child"></div>'+
		   					'	<div class="sm-loading-child"></div>'+
		   					'	<div class="sm-loading-child"></div>'+
		   					'</div>'+	
		   				'</div>';

	SM.preInit = function() {
	
		SM.touch = Modernizr.touchevents;
		SM.support = Modernizr.csstransforms3d;

		SM.init();
	};
				   						
	SM.init = function() {

		var self = this;
            		
		self.container = $(SM.setting('menu-container', 'body'));
		
		if(self.menus.length > 0) {
			
			if(self.container.length === 0) {
				self.container = $('body');
			}
	
			if(self.wrapper.length === 0) {
				self.wrapper = $('<div class="sm-wrapper" id="sm-wrapper"></div>');
				self.wrapper.prependTo(self.container);
			}	
			
			if(self.wrapperInner.length === 0) {
				self.wrapperInner = $('<div class="sm-wrapper-inner" id="sm-wrapper-inner"></div>');
				self.wrapperInner.prependTo(self.wrapper);
			}
			
			if(self.pusher.length === 0) {
				self.pusher = $('<div class="sm-pusher" id="sm-pusher"></div>');
				self.pusher.appendTo(self.wrapperInner);
			}	
			
			if(self.content.length === 0) {
				self.content = $('<div class="sm-content" id="sm-content"></div>');
				self.content.appendTo(self.pusher);
			}
			
			if(self.contentInner.length === 0) {
				self.contentInner = $('<div class="sm-content-inner" id="sm-content-inner"></div>');
				self.contentInner.appendTo(self.content);
			}
						
			if(self.wrapperBg2.length === 0) {
				self.wrapperBg2 = $('<div class="sm-wrapper-bg sm-wrapper-bg-2" id="sm-wrapper-bg-2"></div>');
				self.wrapperBg2.prependTo(self.wrapper);
				
				$('<div class="sm-wrapper-pattern"></div>').prependTo(self.wrapperBg2);
				$('<div class="sm-wrapper-overlay"></div>').prependTo(self.wrapperBg2);
			}
			
			if(self.wrapperBg1.length === 0) {
				self.wrapperBg1 = $('<div class="sm-wrapper-bg sm-wrapper-bg-1" id="sm-wrapper-bg-1"></div>');
				self.wrapperBg1.prependTo(self.wrapper);
				
				$('<div class="sm-wrapper-pattern"></div>').prependTo(self.wrapperBg1);
				$('<div class="sm-wrapper-overlay"></div>').prependTo(self.wrapperBg1);
			}
			

			if(self.hamburgers.length) {
				
				if(!self.support) {
					
					self.hamburgers.removeClass (function (index, css) {
						
					    return (css.match (/(^|\s)sm-hamburger--\S+/g) || []).join(' ');
					    
					}).addClass('sm-hamburger--boring');	
				}	
				
				self.hamburgers.appendTo(self.wrapper);
			}


			if(self.wrapperLoading.length === 0) {
				
				self.wrapperLoading = $('<div class="sm-wrapper-loading" id="sm-wrapper-loading">'+self.loaderTemplate+'</div>');
				self.wrapperLoading.appendTo(self.wrapper);
			}
			
						
			self.container.addClass('sm-body');
			self.container.attr('data-level', 'menu-item-main');

			self.replayAutoplayVideos();
						
			self.initMenus();
			self.onMenusInit();
			self.initScrollToSection();
		}
	};
	
	SM.resetMobileBreakpoint = function() {
		
		$('html').removeData('sm-bp-mobile');
		$('html').removeData('sm-av-bp-mobile');
		
		$('html').removeClass('sm-bp-mobile');
		$('html').removeClass('sm-av-bp-mobile');
		
		$('html').removeClass('sm-always-visible-enabled');
	};
	
	SM.updateMobileBreakpoint = function() {
		
		var self = this;
		self.resetMobileBreakpoint();
		
		var openMenuID = SlickMenu.getOpen();
		if(!openMenuID) {
			openMenuID = SlickMenu.getAlwaysVisible();
		}
	
		self.mobileBreakpoint = "";
		
		if(openMenuID) {
			
			var mobileBreakpoint = SlickMenu.getOption(openMenuID, 'mobile-breakpoint');
			if(mobileBreakpoint && mobileBreakpoint !== "") {
				self.mobileBreakpoint = mobileBreakpoint;
			}else{
				self.mobileBreakpoint = "";
			}
		}
		
		if(self.mobileBreakpoint === "") {
			self.mobileBreakpoint = SM.setting('mobile-breakpoint');
		}
		
		self.mobileBreakpoint = parseInt(self.mobileBreakpoint);
				
		self.alwaysVisibleId = SlickMenu.getAlwaysVisible();	

		if(self.alwaysVisibleId) {
			$('html').addClass('sm-always-visible-enabled');
			var menuWidth = SlickMenu.getOption(self.alwaysVisibleId, 'level-width');
			self.alwaysVisibleMobileBreakpoint = (parseInt(menuWidth) + self.mobileBreakpoint);
		}else{
			$('html').removeClass('sm-always-visible-enabled');
		}

		$(window).trigger('resize');
	};
	
	SM.replayAutoplayVideos = function() {
		
		setTimeout(function() {
	    	$('video[autoplay]').each(function() {
	        	$(this).get(0).play();
	    	});
	    }, 50);
	};
	
	SM.initMenus = function() {
		
		var self = this;
		
		var settings = SM.settings();
		
		self.menus.each(function(i) {
			
			var $this = $(this);
			
			var el = $this.get(0);
			
			var id = $this.data('id');
			var options = SM.options(id);

			var triggerSelector = SM.option(id, 'menu-trigger-selector');
			var customTriggerSelector = SM.option(id, 'menu-trigger-custom-selector', '').trim();
						
			if(customTriggerSelector.length > 0) {
				triggerSelector += ',' + customTriggerSelector;
			}
			
			if(!self.ready) {
					
				if(!$this.parent().is(self.container)) {
					var tmp = $this.detach();
					tmp.appendTo(self.container);
				}
	
				self.container.children().each(function() {
					
					if(
						(!$(this).is('script') || $(this).hasClass('sm-menu')) && 
						!$(this).is('style') && 					
						!$(this).hasClass('sm-wrapper') &&
						!$(this).hasClass('sm-pusher') && 
						!$(this).hasClass('sm-content') && 
						$(this).attr('id') !== 'wpadminbar'
					) {
						var child = $(this).detach();
						
						if(child.hasClass('sm-menu')) {
							if(child.hasClass('sm-always-visible')) {
								child.prependTo(self.content);
							}else{
								if(child.hasClass('sm-push') && !self.support) {
									child.prependTo(self.pusher);
								}else{
									child.prependTo(self.wrapperInner);
								}
							}
						}else{
							child.appendTo(self.contentInner);
						}	
					}
				});
				
				if($('#wpadminbar').length > 0 && !$('#wpadminbar').parent().is($('body')) ) {
					$('#wpadminbar').appendTo('body');
				}
			}

			var menuTriggers = document.querySelectorAll(triggerSelector);

			if(!$(el).data('sm-created')) {
				SlickMenu.create(el, menuTriggers, options, settings, function(elem) {
					
					self.bindMenuEvents( elem );
					$(elem).data('sm-created', true);
					
				});
			}

		});	

	};
	
	SM.onMenusInit = function() {
		
		var self = this;
		
		var onResize = function() {
			
			self.updateAdminBarHeight();
			
			if(self.isMobileBreakpoint()) {
				
				if($('html').data('sm-bp-mobile') !== true) {
					$('html').data('sm-bp-mobile', true);
					$('html').addClass('sm-bp-mobile');
				}
				
			}else{
				
				if($('html').data('sm-bp-mobile') !== false) {
					$('html').data('sm-bp-mobile', false);
					$('html').removeClass('sm-bp-mobile');
				}
			}
			
			if(self.isTabletBreakpoint()) {
				
				if($('html').data('sm-bp-tablet') !== true) {
					$('html').data('sm-bp-tablet', true);
					$('html').addClass('sm-bp-tablet');
				}
				
			}else{
				
				if($('html').data('sm-bp-tablet') !== false) {
					$('html').data('sm-bp-tablet', false);
					$('html').removeClass('sm-bp-tablet');
				}
			}	
			
			if(self.alwaysVisibleId) {
				
				if(self.isAlwaysVisibleMobileBreakpoint()) {
					
					if($('html').data('sm-av-bp-mobile') !== true) {
						$('html').data('sm-av-bp-mobile', true);
						$('html').addClass('sm-av-bp-mobile');
					}
					
				}else{
					
					if($('html').data('sm-av-bp-mobile') !== false) {
						$('html').data('sm-av-bp-mobile', false);
						$('html').removeClass('sm-av-bp-mobile');
					}
				}
				
				if(self.isAlwaysVisibleTabletBreakpoint()) {
					
					if($('html').data('sm-av-bp-tablet') !== true) {
						$('html').data('sm-av-bp-tablet', true);
						$('html').addClass('sm-av-bp-tablet');
					}
					
				}else{
					
					if($('html').data('sm-av-bp-tablet') !== false) {
						$('html').data('sm-av-bp-tablet', false);
						$('html').removeClass('sm-av-bp-tablet');
					}
				}
				
			}else if($('html').data('sm-av-bp-mobile')) {
				
				$('html').removeData('sm-av-bp-mobile');
				$('html').removeClass('sm-av-bp-mobile');
			}
			
		};

		SM.deBouncer($,'smartresize', 'resize', 5);
		
		$(window).smartresize(onResize);

		if(!SM.Device.isMobile()) {
			
			$(window).on('scroll', function() {
				
				self.updateContentScroll();
				
			}).trigger('scroll');
		
		}
		
		self.updateMobileBreakpoint();
		
		self.ready = true;
		iNoBounce.disable();

		$(window).trigger('resize');
							
		setTimeout(function() {
		
			if(SM.setting('onloaded-fadein', "0") === "0") {
				
				setTimeout(function() {
					$('html').addClass('sm-ready');
					$(document).trigger('sm-ready');
				},4);
				
			}else{
				$(document).trigger('sm-ready');
			}
		
		},1);
	};
	
	SM.updateAdminBarHeight = function() {
		
		this.admin_bar_height = 0;
		
		if($('#wpadminbar').length > 0) {
			
			this.admin_bar_height = $('#wpadminbar').height();
		}

	};
	
	SM.updateContentScroll = function() {

		var scrollTop = $(window).scrollTop() - this.admin_bar_height;

		if(scrollTop > 0) {
			this.content.data('scrolltop', scrollTop);
		}
	};


	SM.bindMenuEvents = function(menu) {
	
		var self = this;
		
		self.removeEventListeners(menu);

/*
		self.addEventListener(menu, "sm-opening", function(e) { 
			
			console.log(e.type); 

		});	
*/
			
		self.addEventListener(menu, "sm-open", function(e) { 
			
			//console.log(e.type);
			self.updateMobileBreakpoint();

		});

		self.addEventListener(menu, "sm-level-open", function(e) { 
	
			if(!$(e.detail.level).data('scroll-to-current')) {
				return false;
			}
			
		    var currentMenuItem = $(e.detail.level).find('.current-menu-item');
		    
		    if(currentMenuItem.length === 0) {
		        return false;
		    } 
		
		    $(e.detail.level).find('> .sm-level-inner').scrollTop(0);
		   
		    var top = currentMenuItem.position().top - ($(e.detail.menu).height() / 2);
		    $(e.detail.level).find('> .sm-level-inner').scrollTop(top);
		});

		self.addEventListener(menu, "sm-level-opening sm-level-active", function(e) { 
			
			//console.log(e.type);

			if(!self.support || (SM.Device.isMobile() && e.type === 'sm-level-active')) {
				return false
			}

			// Animate components on opening level	
			
			var totalDelay = 0;
			if(e.type === 'sm-level-active') {
				totalDelay = 500;
			}
			var inner_prefix = '> .sm-level-inner';
			var inner_valign_prefix = inner_prefix + '> .sm-level-body > .sm-level-align';
			
			var selectors = [
				inner_prefix + '> .sm-header .sm-logo .sm-animated',
				inner_valign_prefix + '> .sm-header .sm-logo .sm-animated',
				inner_valign_prefix + '> .sm-title .sm-animated',
				inner_valign_prefix + '> .sm-description .sm-animated',
				inner_valign_prefix + '> .sm-nav-list > li > a.sm-animated',
				inner_prefix + '> .sm-footer .sm-animated',
				inner_valign_prefix + '> .sm-footer .sm-animated'
			];
			
			if(e.detail.levelDepth === 1) {
				
				selectors.push(inner_prefix + '> .sm-close .sm-animated');
				selectors.push(inner_prefix + '> .sm-header .sm-search.sm-animated');
				selectors.push(inner_valign_prefix + '> .sm-header .sm-search.sm-animated');
			}
				
			$(e.detail.level)
			.find(selectors.join(", "))
			.each(function(i) {

				var $this = $(this);
				var animation = $this.data('animation');
				var speed = $this.data('speed');
				var delay = ((i + 2) / 20);
				totalDelay += delay;
				
				$this.css('animation-delay', delay+'s');

				$this.addClass(animation);
				
				$this.one('webkitAnimationEnd oanimationend msAnimationEnd animationend', function (e) {
		   
		           $(this).removeClass(animation);
		           $(this).css('animation-delay', '');
		        });
		  
			});

		});

		self.addEventListener(menu, "sm-level-opened sm-level-active", function(e) { 
			
			//console.log(e.type); 

			Timers.setTimeout(function() {
				bodyScrollLock.clearAllBodyScrollLocks();
				bodyScrollLock.disableBodyScroll(e.detail.menu.querySelector('.sm-level-open:not(.sm-level-overlay) > .sm-level-inner'));
			}, 10);

			// Load video on opening level
			var timeout = 300;
			if(e.type === 'sm-level-active') {
				timeout = 600;
			}

			Timers.setTimeout(function() {
				
				SM.video.loadWrapperVideo(e.detail);
				
				$(e.detail.level).find('>.sm-level-bgvideo').each(function() {
					
					self.video.load($(this), false);
				});
				
			}, timeout);

		});

		self.addEventListener(menu, "sm-level-closing sm-level-inactive", function(e) {

			//console.log(e.type);
			var forcePause = (e.type === 'sm-level-closing' && e.detail.levelDepth <= 1) ? true : false;
		
			SM.video.pauseWrapperVideo(e.detail, forcePause);
				
			// Pause video on closing level
			$(e.detail.level).find('>.sm-level-bgvideo').each(function() {
				
				self.video.pause($(this), false, forcePause);
			});	
		});


		/*
		self.addEventListener(menu, "sm-level-close", function(e) {

			console.log(e.type);

		});


		self.addEventListener(menu, "sm-closing", function(e) {

			console.log(e.type);

		});
		*/

		self.addEventListener(menu, "sm-close", function(e) { 
			
			//console.log(e.type); 
			self.updateMobileBreakpoint();
			bodyScrollLock.clearAllBodyScrollLocks();

		});



		self.addEventListener(menu, "sm-loaded sm-reloaded", function(e) {
			
			//console.log(e.type); 
			self.inlineSvgImages(e.detail.menu);
			self.updateMobileBreakpoint();
		});
		
		self.addEventListener(menu, "sm-reloading", function(e) { 
			
			//console.log(e.type); 
			self.video.destroy(false);
			self.video.destroy(true);
		});
/*

		self.addEventListener(menu, "sm-resize", function(e) { 
			
			console.log(e.type); 
		});
		
		self.addEventListener(menu, "sm-resize-start", function(e) { 
			
			console.log(e.type); 
		});
		
		self.addEventListener(menu, "sm-resize-stop", function(e) { 
			
			console.log(e.type); 
		});	
*/	
	};
	
	SM.inlineSvgImages = function(menu) {
	
		$(menu).find('img._svg').each(function(){
		    var $img = $(this);
		    var imgID = $img.attr('id');
		    var imgClass = $img.attr('class');
		    var imgURL = $img.attr('src');
		
		    $.get(imgURL, function(data) {
		        // Get the SVG tag, ignore the rest
		        var $svg = $(data).find('svg');
		
		        // Add replaced image's ID to the new SVG
		        if(typeof imgID !== 'undefined') {
		            $svg = $svg.attr('id', imgID);
		        }
		        // Add replaced image's classes to the new SVG
		        if(typeof imgClass !== 'undefined') {
		            $svg = $svg.attr('class', imgClass+' replaced-svg');
		        }
		
		        // Remove any invalid XML tags as per http://validator.w3.org
		        $svg = $svg.removeAttr('xmlns:a');
		
		        // Check if the viewport is set, if the viewport is not set the SVG wont't scale.
		        if(!$svg.attr('viewBox') && $svg.attr('height') && $svg.attr('width')) {
		            $svg.attr('viewBox', '0 0 ' + $svg.attr('height') + ' ' + $svg.attr('width'))
		        }
		
		        // Replace image with new SVG
		        $img.replaceWith($svg);
		
		    }, 'xml');
		
		});
		
	};

	
	SM.initScrollToSection = function() {
		
		var is_scrolling = false;

		var scrollToTargetEvent = function(ev){
			
			var anchor = $(this);
			var menu_id = anchor.closest('.sm-menu').data('id');
			var target = anchor.attr('href');

			if(target && target.search('#') !== -1) {
				
				if(is_scrolling) {
					return false;
				}	
			
				if(target.charAt(0) !== '#') {
	
					target = target.split('#');
					target = '#'+target[1];
				}
	
				if($(target).length > 0) {
					
					ev.preventDefault();

					SlickMenu.close(menu_id, function() {
						scrollToTarget(target);
					});
	
				}
			  	
			}else if(target === location.href) {
				
				var current_url = location.href;
				target = current_url.split('#');
			
				if(target.length > 1) {
				
					target = '#'+target[1];
					
					if($(target).length > 0) {
							
						ev.preventDefault();
							
						SlickMenu.close(menu_id, function() {
							scrollToTarget(target);
						});
					
					}
		
				}
			}	
		};
		
        var getOffsetTop = function(target) {
	        
	        var scrolltop = $(target).offset().top;

	        if(Modernizr.touchevents) {
		        return scrolltop;
	        }
	        	        
	  		var sticky_elements = [
		  		'#wpadminbar',				// WP Admin Bar height
		  		'.sticky > .top-bar', 		// Foundation Sticky Navbar height
		  		'.navbar.navbar-fixed-top'	// Bootstrap Sticky Navbar height
	  		];

	  		for(var i = 0 ; i < sticky_elements.length ; i++) {
		  		
		  		var el = $(sticky_elements[i]);
		  		
		  		if(el.length > 0 && el.is(':visible') && (el.css('position') === 'fixed' || el.css('position') === 'absolute')) {
					scrolltop = scrolltop - el.outerHeight(true); 
				}
			}
			
			return scrolltop;
        };
	
		var scrollToTarget = function(target) {
			
			if(is_scrolling || $(target).length === 0) {
				return false;
			}	
		  	
	  		is_scrolling = true;
	
	  		var scrolltop = getOffsetTop(target);
						
			if($(window).scrollTop() === scrolltop) {
				is_scrolling = false;
				return false;
			}
			
			if(!SM.Device.isMobile()) {
					
				TweenLite.to(window, 0.8, {
					
					scrollTo:{y:scrolltop}, 
					ease:Sine.easeOut, 
					onComplete: function() {
	
						var scrolltop2 = getOffsetTop(target);
				
						if(scrolltop !== scrolltop2) {
						
							TweenLite.to(window, 0.6, {
								scrollTo:{y:scrolltop2}, 
								ease:Sine.easeOut, 
								onComplete: function() {
									is_scrolling = false;
								}
							});
							
						}else{
							is_scrolling = false;
						}
					}
				});
			
			}else{
				
				$('html, body').stop().animate({scrollTop: scrolltop}, 800, 'swing', function() {
					
					var scrolltop2 = getOffsetTop(target);
				
					if(scrolltop !== scrolltop2) {
						
						$('html, body').stop().animate({scrollTop: scrolltop2}, 400, 'swing', function() {
							is_scrolling = false;
						});
						
					}else{
						is_scrolling = false;
					}
				});
			}
		};
		
				
		// Iterate over all nav links, setting the "selected" class as-appropriate.

		$(document).off('vclick', '.sm-menu a[href*="#"]:not([href="#"])', scrollToTargetEvent)
		$(document).on('vclick', '.sm-menu a[href*="#"]:not([href="#"])', scrollToTargetEvent)
		
		$(window).on('load', function() {
			
			var current_url = location.href;
			var target = current_url.split('#');
			
			if(target.length > 1) {
				target = '#'+target[1];
				scrollToTarget(target);
				is_scrolling = false;	
			}

		});

	};
	
	
	SM.deBouncer = function($,cf,of, interval){
	    // deBouncer by hnldesign.nl
	    // based on code by Paul Irish and the original debouncing function from John Hann
	    // http://unscriptable.com/index.php/2009/03/20/debouncing-javascript-methods/
	    var debounce = function (func, threshold, execAsap) {
	        var timeout;
	        return function debounced () {
	            var obj = this, args = arguments;
	            function delayed () {
	                if (!execAsap)
	                    func.apply(obj, args);
	                timeout = null;
	            }
	            if (timeout)
	                clearTimeout(timeout);
	            else if (execAsap)
	                func.apply(obj, args);
	            timeout = setTimeout(delayed, threshold || interval);
	        };
	    };
	    jQuery.fn[cf] = function(fn){  return fn ? this.bind(of, debounce(fn)) : this.trigger(cf); };
	};

	
	SM.eventHandlers = {}; // somewhere global

	SM.addEventListener = function(node, evt, handler, capture) {
		
		var events = evt.split(' ');
		for (var i=0, iLen=events.length; i<iLen; i++) {
			evt = events[i];

		    if(!(node in this.eventHandlers)) {
		        // _eventHandlers stores references to nodes
		        this.eventHandlers[node] = {};
		    }
		    if(!(evt in this.eventHandlers[node])) {
		        // each entry contains another entry for each event type
		        this.eventHandlers[node][evt] = [];
		    }
		    // capture reference
		    this.eventHandlers[node][evt].push([handler, capture]);
		    node.addEventListener(evt, handler, capture);
	    		    
		}

	};

	SM.removeEventListeners = function(node) {

	    if(node in this.eventHandlers) {
	        var handlers = this.eventHandlers[node];
	        
	        for (var evt in handlers) {
		
	            var eventHandlers = handlers[evt];
	            for(var i = eventHandlers.length; i--;) {
	                var handler = eventHandlers[i];
	                node.removeEventListener(evt, handler[0], handler[1]);
	            }
	        }
	    }
		   
	};


	SM.disableEvents = function (el, type, selector) {
	
	    var events = $(el).data('events');
	    events = $.extend( true, {}, events );
	    
	    if(typeof(events[type]) !== 'undefined') {
	        events = events[type];
	
			if(typeof(selector) !== 'undefined') {
				
				for (var evt in events) {
					if(typeof(events[evt].selector) !== 'undefined' && events[evt].selector === selector) {
						$(el).off(type, selector, events[evt].handler, events[evt].data);
				    }
				}  
				
			}else{
				
				for (var evt in events) {
				    $(el).off(type, events[evt].handler, events[evt].data);
				} 
			}
	         
			
			$(el).data('sm_clone_'+type+'_events', events);    
	    }
	    
	    return [];
	};
	
	SM.enableEvents = function (el, type, selector) {
	
		var events = $(el).data('sm_clone_'+type+'_events');

		if(typeof(selector) !== 'undefined') {
			
		    for (var evt in events) {
				if(typeof(events[evt].selector) !== 'undefined' && events[evt].selector === selector) {
					$(el).on(type, selector, events[evt].handler, events[evt].data);
				}	
			}
			
		}else{
			
			for (var evt in events) {
				$(el).on(type, events[evt].handler, events[evt].data);
			}
		}
		
		$(el).removeData('sm_clone_'+type+'_events');
	};
	
	SM.replaceEventsSelector = function(el, type, origSelector, newSelector) {
		
	    var events = $(el).data('events');
	    events = $.extend( true, {}, events );
	    
	    if(typeof(events[type]) !== 'undefined') {
	        events = events[type];
	
			for (var evt in events) {
			
				if(typeof(events[evt].selector) !== 'undefined' && events[evt].selector === origSelector) {
					$(el).off(type, origSelector, events[evt].handler, events[evt].data);
					$(el).on(type, newSelector, events[evt].handler, events[evt].data);
				}	
			} 

	    }
	}

	SM.options = function(id) {

		return SM_VARS.options[id];
	};
	
	SM.settings = function() {

		return SM_VARS.settings;
	};
	
	SM.option = function(id, key, std) {
	
		var value = SM_VARS.options[id][key];
		if((typeof(value) === 'undefined' || value === '') && typeof(std) !== 'undefined') {
			return std;
		}
		
		return value;
	};

	
	SM.setting = function(key, std) {
		
		var value = SM_VARS.settings[key];
		if((typeof(value) === 'undefined' || value === '') && typeof(std) !== 'undefined') {
			return std;
		}
		
		return value;
	};
	
	SM.isMobileBreakpoint = function() {
		
		var winWidth = $(window).width();

		if(winWidth <= this.mobileBreakpoint) {
			return true;
		}
		
		return false;
	};	

	SM.isAlwaysVisibleMobileBreakpoint = function() {
		
		var winWidth = $(window).width();

		if(winWidth <= this.alwaysVisibleMobileBreakpoint) {
			return true;
		}
		
		return false;
	};		

	SM.isTabletBreakpoint = function() {
		
		var winWidth = $(window).width();

		if(!SM.isMobileBreakpoint() && (winWidth <= (this.mobileBreakpoint * 3))) {
			return true;
		}
		
		return false;
	};	

	SM.isAlwaysVisibleTabletBreakpoint = function() {
		
		var winWidth = $(window).width();
		
		if(!SM.isAlwaysVisibleMobileBreakpoint() && (winWidth <= (this.alwaysVisibleMobileBreakpoint * 3))) {
			return true;
		}
		
		return false;
	};	
		
	// SM VIDEO ----------------------------------------------------------
			
	SM.video = {};
	SM.video.videos = [];
	SM.video.wrapperVideos = [];
	SM.video.loader = null;
	SM.video.load = function($videoBg, isWrapper) {
	
		var self = this;
		var player;
		var $level = $videoBg.closest('.sm-level'); 
		var spinner = $videoBg.data('spinner') ? true : false;
		var loader;
		var hideLoading = function() {
			
			if(spinner) {
					
				if(!isWrapper) {
					
					if(self.loader) {	  		
						self.loader.fadeOut("medium", function() {
							self.loader.remove();	
						});
					}	
						
				}else{
					
					SM.wrapperLoading.removeClass('is-loading sm-overlay');
				}	
			}
		}
		
		$videoBg.data('pause', false);	
		
		if(!$videoBg.data('loading') && !$videoBg.data('loaded')) {
			
			$videoBg.data('loading', true);		

			var videoId = $videoBg.data('id');
			var quality = $videoBg.data('quality');
			var mute = $videoBg.data('audio') ? false : true;
			var repeat = $videoBg.data('repeat') ? true : false;
			var mobile = $videoBg.data('mobile') ? true : false;
			var start = $videoBg.data('start') ? parseInt($videoBg.data('start')) : 0;
			var end = $videoBg.data('end') ? parseInt($videoBg.data('end')) : null;
			var controls = 0;
			
			if(start === end) {
				end = null;
			};
			
			if((SM.Device.mobile && !mobile)) {
				return false;	
			}
		
			if(spinner) {
				
				if(!isWrapper) {
					
					self.loader = $(SM.loaderTemplate);
				
				   	self.loader.appendTo($level);
				   	Timers.setTimeout(function(){
					   	self.loader.addClass('fadein');
				   	}, 2);
			   	
			   	}else{
				   	
				   	SM.wrapperLoading.addClass('is-loading sm-overlay');
			   	}
			   	
			}
			
			if(SM.Device.mobile) {
				
				controls = 1;
				start = null;
				end = null;
				
				if(isWrapper) {
					var $videoLevel = $videoBg.data('level');
					$videoBg.insertAfter($videoLevel.find('.sm-level-inner > .sm-level-body > .sm-level-align > .sm-title'));
				}else{
					$videoBg.insertAfter($level.find('.sm-level-inner > .sm-level-body > .sm-level-align > .sm-title'));
				}	
			}
			
			$videoBg.YTPlayer({
			  	videoId: videoId,
			  	fitToBackground: true,
			  	mute: mute,
			  	repeat: repeat,
			  	pauseOnScroll: false,
		        start: start,
		        playerVars: {
		          modestbranding: 0,
		          autoplay: 0,
		          controls: controls,
		          showinfo: 0,
		          branding: 0,
		          rel: 0,
		          autohide: 0,
		          end: end
		        },
		        
			  	callback: function(e, player) {
			  		
			  		
				  	self.videos.push({bg: $videoBg, player: player});
				  	
			  		if(isWrapper) {
			  			self.wrapperVideos.push({bg: $videoBg, player: player});
			  		}

		  			if(!SM.Device.mobile) {
			  			
			  			self.rePosition($videoBg, true);
			  			player.setPlaybackQuality(quality);	
						
			  		}else{
				  		
				  		$videoBg.fitVids();
				  		
				  		Timers.setTimeout(function() {
						  	
						  	$videoBg.addClass('loaded');	
					  		$videoBg.addClass('sm-video-loaded');
					  		
					  		hideLoading();
	
						}, 10);
			  		}
			  		
			  		$videoBg.data('loading', false);
					$videoBg.data('loaded', true);

		  		},
		  		
		  		onStateChange: function(e, player){
				  			
		  			if($videoBg.data('pause') === true) {
						$videoBg.data('loading', false);
						self.pause($videoBg, isWrapper);
				  		return false;
			  		}
	
					if (e.data == YT.PlayerState.PLAYING && !$videoBg.hasClass('sm-video-loaded')) {
						
						Timers.setTimeout(function() {
						  	
						  	$videoBg.addClass('loaded');	
					  		$videoBg.addClass('sm-video-loaded');
					  		
					  		hideLoading();
	
						}, 10);
					}
	
					if (e.data == YT.PlayerState.BUFFERING && quality) {
						    
						player.setPlaybackQuality(quality);
				    }
				    
				    if (e.data == YT.PlayerState.ENDED) {
						    
						if(repeat){    
							player.playVideo();
						}
				    }
				}
			});
		
		}else if(!$videoBg.data('loading') && $videoBg.data('loaded')) {
			
			player = $videoBg.data('ytPlayer') ? $videoBg.data('ytPlayer').player : null;
			
			if(player && typeof(player.playVideo) !== 'undefined') {
				
				self.rePosition($videoBg, true);
				player.playVideo();
				$videoBg.addClass('sm-video-loaded');
				
				hideLoading();
			}
				
		}else if($videoBg.data('loading') && !$videoBg.data('loaded')){
			
			player = $videoBg.data('ytPlayer') ? $videoBg.data('ytPlayer').player : null;
			
			if(player) {
				
				$videoBg.data('loading', false);
				$videoBg.data('loaded', true);
				
				self.load($videoBg, isWrapper);
			}
		}			
	};
	
	SM.video.pause = function($videoBg, isWrapper, force) {
		
		var self = this;

		force = typeof(force) !== 'undefined' ? force : false;
				
		var nopause = $videoBg.data('nopause') ? true : false;

		if(nopause && !force) {
			return false;
		}
		
		$videoBg.data('pause', true);
		$videoBg.removeClass('sm-video-loaded');
		
		if(isWrapper) {
			SM.wrapperLoading.removeClass('is-loading sm-overlay');
		}
		
		var count = 0;
		var pauseInterval = setInterval(function() {
			
			if(count > 25) {
				
				clearInterval(pauseInterval);
				
				if($videoBg.data('pause') !== true ) {
					return false;
				}
				
				if($videoBg.data('loaded') === true) {
			
					var player = $videoBg.data('ytPlayer') ? $videoBg.data('ytPlayer').player : null;
					player.pauseVideo();
				
				}
			}	
			count++;
				
		}, 5);
		
	};

	
	SM.video.rePosition = function($videoBg, resizeEvent) {

		var self = this;
		var $menu = $videoBg.closest('.sm-menu');
		var $videoFrame = $videoBg.find('iframe');
		var menuWidth = $videoBg.width(); 
		var frameWidth = $videoFrame.width();
		var scale = $videoBg.data('scale') ? parseFloat($videoBg.data('scale')) : 1;
		var position = (frameWidth / 2) - (menuWidth / 2);
		
		$videoFrame.css({
			'left': -(position)+'px',
			'right': 'inherit',
			'transform': 'scale('+scale+')'
		});

		if(!resizeEvent) {
			return false;
		}
		
		var rePosition = function() {
			self.rePosition($videoBg, false)
		};

	};

	SM.video.destroy = function(isWrapper) {
		
		var self = this;
		var video;
		
		if(!isWrapper) {

			video = self.videos.pop();	
		
		}else{
			
			video = self.wrapperVideos.pop();
		}
		
		if(!video)
			return false;
			
		var bg = video.bg;
		var player = video.player;
			
		bg.removeClass('loaded sm-video-loaded');
			
		if(player) {
		
			player.stopVideo();
			player.destroy();
		}
		
		bg.empty();
		bg.removeData();
		
		
		if(!isWrapper) {
			
			if(self.videos.length > 0) {
				
				self.destroy(isWrapper);
			}
			
		}else{
			
			if(self.wrapperVideos.length > 0) {
				
				self.destroy(isWrapper);
			}
		}	
	};
	
	SM.video.getWrapperVideo = function(data) {

		var video_id = $(data.level).data('video-id');
		var $wrapperVideo = null;
		
		if(video_id && video_id !== '') {
			
			var quality = $(data.level).data('video-quality');
			var opacity = $(data.level).data('video-opacity');
			var scale = $(data.level).data('video-scale');
			var start = $(data.level).data('video-start');
			var end = $(data.level).data('video-end');
			var nopause = $(data.level).data('video-nopause');
			var repeat = $(data.level).data('video-repeat');
			var audio = $(data.level).data('video-audio');
			var spinner = $(data.level).data('video-spinner');
			var mobile = $(data.level).data('video-mobile');

								
			var id = data.menu_id+'-'+data.levelDepth+'-'+video_id;
			
			$wrapperVideo = $('.sm-wrapper-video[data-uniqueid="'+id+'"]');
			if($wrapperVideo.length === 0) {
				$wrapperVideo = $('<div class="sm-wrapper-video" data-uniqueid="'+id+'" data-id="'+video_id+'" data-quality="'+quality+'" data-opacity="'+opacity+'" data-scale="'+scale+'" data-start="'+start+'" data-end="'+end+'" data-nopause="'+nopause+'" data-repeat="'+repeat+'" data-audio="'+audio+'" data-spinner="'+spinner+'" data-mobile="'+mobile+'"></div>');
				
				$wrapperVideo.data('level', $(data.level));
				$wrapperVideo.insertBefore(SM.wrapperInner);
			}

		}

		return $wrapperVideo;	
	};
	
	SM.video.loadWrapperVideo = function(data) {

		var $wrapperVideo = this.getWrapperVideo(data);
		if($wrapperVideo) {
			this.load($wrapperVideo, true);	
		}
	};
	
	SM.video.pauseWrapperVideo = function(data, force) {

		var $wrapperVideo = this.getWrapperVideo(data);
		if($wrapperVideo) {
			this.pause($wrapperVideo, true, force);
		}		
	};


	SM.Device = {
		mobile: null,
		pixelRatio: 1,
        init: function () {
	        
	        var self = this;
	        
            self.mobile = self.isMobile();
            
            if(this.mobile){
	            
	            this.setPixelRatio();
	            
	            $('html').addClass("sm-mobile");
	            $(document).trigger('sm-device-ready');
	            
            }else{
	            
	            $(document).trigger('sm-device-ready');
            }

        },
        isMobile: function() {
	        
	        if(this.mobile !== null) {
		        return this.mobile;
	        }
	        
			var check = false;
			(function(a){
				if(/(android|ipad|playbook|silk|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge |maemo|midp|mmp|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows (ce|phone)|xda|xiino/i.test(a)||/1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|yas\-|your|zeto|zte\-/i.test(a.substr(0,4))) {
					check = true;
				}
			})
			(navigator.userAgent||navigator.vendor||window.opera);
			return check;
		},
		setPixelRatio: function() {
			
			this.pixelRatio = 1;
		    // To account for zoom, change to use deviceXDPI instead of systemXDPI
		    if (window.screen.systemXDPI !== undefined && window.screen.logicalXDPI !== undefined && window.screen.systemXDPI > window.screen.logicalXDPI) {
		        // Only allow for values > 1
		        this.pixelRatio = window.screen.systemXDPI / window.screen.logicalXDPI;
		    }
		    else if (window.devicePixelRatio !== undefined) {
		        this.pixelRatio = window.devicePixelRatio;
		    }
		}
	};
			
	SM.Browser = {
		name: null,
		version: null,
        init: function () {
	        
            this.name = this.searchString(this.dataBrowser) || "Other";
            this.version = this.searchVersion(navigator.userAgent) || this.searchVersion(navigator.appVersion) || "Unknown";
            
            $('html').addClass('sm-browser-'+this.name);
            $('html').data('browser', {
	            'name': this.name,
	            'version': this.version
	        });
        },
        searchString: function (data) {
            for (var i = 0; i < data.length; i++) {
                var dataString = data[i].string;
                this.versionSearchString = data[i].subString;

                if (dataString.indexOf(data[i].subString) !== -1) {
                    return data[i].identity;
                }
            }
        },
        searchVersion: function (dataString) {
            var index = dataString.indexOf(this.versionSearchString);
            if (index === -1) {
                return;
            }

            var rv = dataString.indexOf("rv:");
            if (this.versionSearchString === "Trident" && rv !== -1) {
                return parseFloat(dataString.substring(rv + 3));
            } else {
                return parseFloat(dataString.substring(index + this.versionSearchString.length + 1));
            }
        },

        dataBrowser: [
            {string: navigator.userAgent, subString: "Edge", identity: "ms-edge"},
            {string: navigator.userAgent, subString: "MSIE", identity: "explorer"},
            {string: navigator.userAgent, subString: "Trident", identity: "explorer"},
            {string: navigator.userAgent, subString: "Firefox", identity: "firefox"},
            {string: navigator.userAgent, subString: "Opera", identity: "opera"},  
            {string: navigator.userAgent, subString: "OPR", identity: "opera"},  
            {string: navigator.userAgent, subString: "Chrome", identity: "chrome"}, 
            {string: navigator.userAgent, subString: "Safari", identity: "safari"}       
        ]
    };
    
    $(document).on('sm-device-ready', function() {
	    SM.preInit();	
	});
	
	$(document).ready( function () {
		SM.Browser.init();
		SM.Device.init();
	});

	window.SM = SM;
		
} )( window, jQuery );	