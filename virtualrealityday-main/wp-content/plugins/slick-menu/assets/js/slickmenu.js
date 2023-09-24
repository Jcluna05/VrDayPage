/**
 * SlickMenu.js v1.0.0
 * http://www.xplodedthemes.com
 * 
 * Copyright 2016, XplodedThemes
 * http://www.xplodedthemes.com
 */
;( function( window, $ ) {
		
	'use strict';

	$.ajaxSetup({async:true});
	
	// Helpers ------

	if (typeof CustomEvent !== 'function') {
		
		var CustomEvent = function( eventType, params ) {
			params = params || { bubbles: false, cancelable: false, detail: undefined };
			var evt = document.createEvent( 'CustomEvent' );
			evt.initCustomEvent( eventType, params.bubbles, params.cancelable, params.detail );
			return evt;
		};
		
		CustomEvent.prototype = window.Event.prototype;
		window.CustomEvent = CustomEvent;
	}

	function extend( a, b ) {
		for( var key in b ) { 
			if( b.hasOwnProperty( key ) ) {
				a[key] = b[key];
			}
		}
		return a;
	}

	function hasParent( e, id ) {
		if (!e) {
			return false;
		}	
		var el = e.target||e.srcElement||e||false;
		while (el && el.id !== id) {
			el = el.parentNode||false;
		}
		return (el!==false);
	}
	
 	function hasParentClass( e, classname ) {
		if(e === document) {
			return false;
		}
		if( classie.has( e, classname ) ) {
			return true;
		}
		return e.parentNode && hasParentClass( e.parentNode, classname );
	}
	
	// returns the depth of the element "e" relative to element with id=id
	// for this calculation only parents with classname = waypoint are considered
	function getLevelDepth( e, id, waypoint, cnt ) {
		cnt = cnt || 0;
		if ( e.id.indexOf( id ) >= 0 ) {
			return cnt;
		}
		if( classie.has( e, waypoint ) ) {
			++cnt;
		}
		return e.parentNode && getLevelDepth( e.parentNode, id, waypoint, cnt );
	}


	// returns the closest element to 'e' that has "selector"

	function closest(el, selector) {
	    var matchesSelector = el.matches || el.webkitMatchesSelector || el.mozMatchesSelector || el.msMatchesSelector;
	
	    while (el) {
	        if (matchesSelector.call(el, selector)) {
	            break;
	        }
	        el = el.parentElement;
	    }
	    return el;
	}
	
	function getWindowScrollTop() {
	
		var supportPageOffset = window.pageYOffset !== undefined;
		var isCSS1Compat = ((document.compatMode || "") === "CSS1Compat");
		
		var scrollTop = supportPageOffset ? window.pageYOffset : isCSS1Compat ? document.documentElement.scrollTop : document.body.scrollTop;

		return scrollTop;
	}
	window.getWindowScrollTop = getWindowScrollTop;

	function setWindowScrollTop(scrollTop) {
		
		window.scrollTo( 0, scrollTop ); 
	}
	window.setWindowScrollTop = setWindowScrollTop;
	
	function getScrollTop(el) {
	
		var supportScrollTop = el.scrollTop !== undefined;
		
		if(supportScrollTop) {
			return el.scrollTop;
		}
		
		return 0;
	}
	window.getScrollTop = getScrollTop;
			
	function setScrollTop(el, scrollTop) {
	
		var supportScrollTop = el.scrollTop !== undefined;
		
		if(supportScrollTop) {
			el.scrollTop = scrollTop;
		}
	}
	window.setScrollTop = setScrollTop;

	
	function isNumeric(n) {
	  return !isNaN(parseFloat(n)) && isFinite(n);
	}
	
	function isDefined(variable) {
		
		return typeof(variable) !== 'undefined';
	}
	
	
	function getWindowWidth() {
		
		var width = window.innerWidth || document.documentElement.clientWidth || document.body.clientWidth;
		
		return width;
	}
		
	function getWindowHeight() {
		
		var height = window.innerHeight || document.documentElement.clientHeight || document.body.clientHeight;
		return height;
	}
	
	function transitionEnd() {
	    var i,
	        el = document.createElement('div'),
	        transitions = {
	            'transition':'transitionend',
	            'OTransition':'otransitionend',  // oTransitionEnd in very old Opera
	            'MozTransition':'transitionend',
	            'WebkitTransition':'webkitTransitionEnd'
	        };
	
	    for (i in transitions) {
	        if (transitions.hasOwnProperty(i) && el.style[i] !== undefined) {
	            return transitions[i];
	        }
	    }
	}

	function animationEnd() {
	    var i,
	        el = document.createElement('div'),
	        animations = {
	            'animation':'animationend',
	            'OAnimation':'oanimationend',  // oTransitionEnd in very old Opera
	            'MozAnimation':'animationend',
	            'WebkitAnimation':'webkitAnimationEnd'
	        };
	
	    for (i in animations) {
	        if (animations.hasOwnProperty(i) && el.style[i] !== undefined) {
	            return animations[i];
	        }
	    }
	}
	
	var preloadedImages = [];
	function preloadImage(src, callback) {

		var obj = new Image();

	    // call the notify_complete function when the image has loaded
	    if(typeof(callback) !== 'undefined') {
		    
		    obj.onload = function() {
			    preloadedImages.push(src);
				callback(src);    
			};
			
			obj.onerror = function() {
				callback(src); 
			};
		}
	
	    // load the image
	    obj.src = src;
	}

		
	// SlickMenu Implementation ------
	
	var SM_MENUS = {};

	var SlickMenu = function ( el, trigger, options, settings, callback ) {
	
		var self = this;
		
		this.el = el;
		this.id = el.id;
		this.menu_id = parseInt(this.el.getAttribute('data-id'));
		this.trigger = trigger;
		this.options = options;
		this.settings = settings;
		this.callback = callback;
		this.eventHandlers = {}; // somewhere global

		// Dom Elements
		this.html = document.querySelector( 'html' );
		// the container
		this.container = document.querySelector( '.sm-body' );
		// the wrapper
		this.wrapper = document.getElementById( 'sm-wrapper' );
		this.wrapperBg1 = document.getElementById( 'sm-wrapper-bg-1' );
		this.wrapperBg2 = document.getElementById( 'sm-wrapper-bg-2' );
		this.wrapperLoading = document.getElementById( 'sm-wrapper-loading' );
		this.wrapperInner = document.getElementById( 'sm-wrapper-inner' );
		// the moving pusher
		this.pusher = document.getElementById( 'sm-pusher' );
		// the content
		this.content = document.getElementById( 'sm-content' );		
		this.contentInner = document.getElementById( 'sm-content-inner' );	
						
		// Detect Mobile	
		this.touch = SM.touch;
		this.mobile = classie.hasClass(this.html, 'sm-mobile') ? true : false;
				
		// support 3d transforms
		this.support =  SM.support;
						
		this.disableItemClick = false;

		this.ajax_errors = 0;
		
		this.setVars = function() {

			// Reset Vars ----------
			
			this.cache = {};	 	
			this.created = false;
			this.open = false;
			this.openFirst = false;
			this.selectedLevelSet = false;
			this.isAjaxing = false;
			this.isReloading = false;
			
			// Menu Ajax Loading ----------
			
			this.ajax = this.option('menu-ajax') === '1' ? true : false;
			
			
			// Menu Always Visible ----------
			
			this.alwaysVisible = this.option('menu-always-visible') === '1' ? true : false;		
			
			
			// Level Widths / Overlaps ----------
			
			this.levelSpacing = 40;
			this.levelWidth = this.option('level-width', '300px');
			
			
			// Menu Position ----------
			
			this.position = this.option('menu-position');
			
			
			// Trigger Event Type ----------
			
			this.menuEventType = this.isTouch() ? 'vclick' : 'click';	
			this.levelEventType = this.isTouch() ? 'vclick' : 'click';
			
			
			// Animation Type ----------	
			
			this.setAnimationType();
			
			
			// Animation Speed  ----------
					
			if(this.support) {
				this.openAnimationSpeed = parseInt(this.option('menu-open-duration', 500) * 1.2);
				this.closeAnimationSpeed = parseInt(this.option('menu-close-duration', 500) * 1.2);
			}else{
				this.openAnimationSpeed = 0;
				this.closeAnimationSpeed = 0;	
			}
			
			
			// Mobile Breakpoint  ----------
			
			this.mobileBreakpoint = parseInt(this.option('real-mobile-breakpoint', 700));


			// Trigger Selectors  ----------
			
			this.triggerSelector = this.option('menu-trigger-selector');
			this.customTriggerSelector = this.option('menu-trigger-custom-selector', '');

			if(typeof (this.customTriggerSelector) === 'string' && this.customTriggerSelector.length > 0) {
				
				// Disable events on existing theme triggers
				$(this.customTriggerSelector).each(function() {

					SM.disableEvents(this, 'click');
					SM.disableEvents(this, 'vclick');
					SM.disableEvents(this, 'touchstart');
				});
				
				this.triggerSelector += ',' + this.customTriggerSelector.trim();
			}
			
			
			this.openCurrentLevel = this.option('menu-open-active-level') === '1' ? true : false;
			
			
			this.hideAlwaysVisibleMenu();
			this.closeHamburgerTrigger(true);
			this.animateReset();
	
		};
		
		this.setAnimationType = function() {
			
			var disablePush = false;
			var enablePush = false;
			var $parent = $(this.el).parent();
			
			this.type = this.option('level-animation-type', 'cover');
			this.pushType = classie.hasClass(this.el, 'sm-push') ? true : false;
			this.origAnimationType = this.option('menu-animation-type', 'sm-effect-1');
			this.mobileAnimationType = this.option('menu-mobile-animation-type', 'sm-effect-2');
	
			if(this.alwaysVisible) {
				
				this.animationType = 'sm-effect-2';
				disablePush = true;
	
			}else{
				
				if(!this.support) {
					
					this.animationType = 'sm-effect-2';
					disablePush = true;
					
				}else if(this.isMobile()) {
					
					this.animationType = this.mobileAnimationType;
				
				}else{
				
					this.animationType = this.origAnimationType;
				}
			}
			
			var switchAnimation = (this.origAnimationType !== this.animationType);
				
			if(switchAnimation || this.isMobile()) {
			
				if(switchAnimation) {
					classie.remove(this.el, this.origAnimationType);	
					classie.add(this.el, this.animationType);
				}
				
				if(disablePush) {
				
					this.pushType = false;
					classie.removeClass(this.el, 'sm-push');
					
				}else if(enablePush) {
				
					this.pushType = true;
					classie.addClass(this.el, 'sm-push');
				}	

			}

			if(this.alwaysVisible) {
					
				if(!$(self.el).parent().hasClass('sm-av-wrap'))	{	
					$(self.el).wrap('<div class="sm-av-wrap sm-'+self.position+'"></div>');
				}
	
				if(!$parent.parent().is($(this.content))) {
					$(this.el).parent().prependTo($(this.content));
				}

			}else{
				
				if($(self.el).parent().hasClass('sm-av-wrap'))	{	
					$(self.el).unwrap();
				}
				
				if(this.isMobile()) {
					
					if(!$parent.is($(this.wrapper))) {
				
						$(this.el).insertAfter($(this.wrapperInner));
					}	

				}else{
			
					if(this.pushType && !$parent.is($(this.pusher))) {
						
						$(this.el).prependTo($(this.pusher));
						
					}else if(!this.pushType && !$parent.is($(this.wrapperInner))) {
						
						$(this.el).prependTo($(this.wrapperInner));
					}
					
				}	
			}

		};
		
		this.preInit = function() {
			
			var self = this;

			self.setVars();
			
			SM_MENUS[self.id] = self;
			
			if(!self.alwaysVisible) {
			
				if(self.ajax) {
					
					$(self.triggerSelector).on(self.menuEventType, self.insertAjaxMenuEvent);
					
				}else{
	
					self.init();
				}
				
			}else{
				
				self.init();
			}
			
			
			self.initWrapperDataLevel();
			self.initAfterAjaxEvents();	
				
							
			if(self.alwaysVisible) {
			
				self.toggleMenu(function() {
		
					if(self.isMobileBreakpoint()) {
						self.hideAlwaysVisibleMenu();
					}else{
						self.showAlwaysVisibleMenu();
					}
					
				});
			}
			
		};
		
		this.insertAjaxMenuEvent = function(e) {
			
			e.preventDefault();
			e.stopImmediatePropagation();
			e.stopPropagation();
			
			self.insertAjaxMenu();
		}; 			
	
		this.init = function() {
			
			var self = this;
			
			self.removeEventListeners();
			self.setVars();

			// level depth
			self.level = 0;
			// previous level depth
			self.previousLevel = 0;
			self.levelEl;
			self.previousLevelEl;

			// Logo
			self.logo = self.el.querySelector('.sm-logo');
			// the sm-level elements
			self.levels = Array.prototype.slice.call( self.el.querySelectorAll( 'div.sm-level' ) );
			// the menu items
			self.menuItems = Array.prototype.slice.call( self.el.querySelectorAll( 'li' ) );
			// Main Level
			self.mainLevel = self.el.querySelector( '.sm-main-level');
			// if type === "cover" these will serve as hooks to move back to the previous level
			self.levelBack = Array.prototype.slice.call( self.el.querySelectorAll( '.sm-back a' ) );
			// Close Link
			self.closeLink = self.el.querySelector( '.sm-close');

			// Hamburger Triggers
			self.allHamburgers = Array.prototype.slice.call(document.querySelectorAll('.sm-hamburger'));
			self.hamburgersExists = self.allHamburgers.length > 0;
			
			self.menuHamburgers = Array.prototype.slice.call(document.querySelectorAll('.sm-hamburger[data-id="'+this.menu_id+'"]'));
			self.menuHasHamburgers = self.menuHamburgers.length > 0;
			if(self.levels.length === 0) {
				return false;
			}

			// initialize / bind the necessary events
			self.initEvents();
			self.created = true;

		};

		this.setting = function(key, std) {
		
			var value = this.settings[key];
			if((typeof(value) === 'undefined' || value === '') && typeof(std) !== 'undefined') {
				return std;
			}
			
			return value;
		};
		
		this.option = function(key, std) {
		
			var value = this.options[key];
			if((typeof(value) === 'undefined' || value === '') && typeof(std) !== 'undefined') {
				return std;
			}
			
			return value;
		};
		
		this.isTouch = function() {
			
			return this.touch;	
		};
		
		this.isMobile = function() {
			
			return this.mobile;
		};

		this.isMobileBreakpoint = function() {
			
			var winWidth = getWindowWidth();

			if(winWidth <= this.mobileBreakpoint) {
				return true;
			}
			
			return false;
		};
			
		this.initEvents = function() {
			
			var self = this;
			
			var toggleEvent = function( ev ) {
				
				ev.preventDefault();
				ev.stopImmediatePropagation();
				ev.stopPropagation();
				
				self.toggleMenuEvent(ev);
			};
			
			var goBack = function(ev, level) {
				
				if(typeof(level) === 'undefined') {

					var currentLevel = self.getCurrentLevel();
					level = self.getLevelDepth(closest( currentLevel, '.sm-level' ));
				}
			
				ev.preventDefault();
				ev.stopImmediatePropagation();
				ev.stopPropagation();
				
				self.previousLevel = self.level;
				self.previousLevelEl = self.levelEl;
				
				self.level = level - 1;
				self.levelEl = currentLevel;

				if(self.level === 0) {
					self.resetMenu(); 
				}else{
					self.closeMenu();
				}
			};
			
						
			var levelScrollEvent = function() {

				self.disableItemClick = true;

				clearTimeout($.data(this, 'scrollTimer'));

				$.data(this, 'scrollTimer', setTimeout(function() {
					self.disableItemClick = false;
				}, 300));

			   	var scroll = $(this).scrollTop();
			   	var scrolling = $(this).data("scrolling");
			   	var firstTime = scrolling === undefined;
			   	
			    if (scroll >= 50 && (!scrolling || firstTime)) {
				    
			        $(this).data("scrolling", true);
			        $(this).addClass("sm-scrolling");
			        
			        if(self.level === 1) {
			        	self.hideHamburgerTrigger();
			        }
			        
			    } else if(scroll < 50 && (scrolling || firstTime)) {
				    
			        $(this).data("scrolling", false);
			        $(this).removeClass("sm-scrolling");
			        
			        if(self.level === 1) {
			        	self.showHamburgerTrigger();
			        }
			    }
			};


			
			// Responsive Resize
			var noTransitionTimeout;
			var resizeEvent = function() {
				
				if( self.open || (self.alwaysVisible && !self.alwaysVisibleMenuHidden())) {
					
					Timers.clearTimeout(noTransitionTimeout);
					
					classie.add( self.pusher, 'sm-no-transition' );
					classie.add( self.el, 'sm-no-transition' );
				
					self.resizeCurrentLevel();
					self.animate();
				
					noTransitionTimeout = Timers.setTimeout(function() {
						
						classie.remove( self.pusher, 'sm-no-transition' );
						classie.remove( self.el, 'sm-no-transition' );
						
						self.fireEvent('sm-resize', {menu_id: self.menu_id});
						
					}, 100);
					
				}
				
				if(self.alwaysVisible) {
					if(!self.isMobileBreakpoint()) {
						
						self.showAlwaysVisibleMenu();
						$(self.el).data('av-opened-by-user', false);
						
					}else if(!self.alwaysVisibleMenuHidden() && $(self.el).data('av-opened-by-user') !== true){
						
						self.hideAlwaysVisibleMenu();
						$(self.el).data('av-opened-by-user', false);
					}
				}
			};


			// open (or close) the menu
			for(var i = 0; i < this.trigger.length ; i++) {
				
				$(this.trigger[i]).off( this.menuEventType, toggleEvent);
				$(this.trigger[i]).on( this.menuEventType, toggleEvent);	
			}

			// opening a sub level menu
			self.menuItems.forEach( function( el ) {
				// check if it has a sub level
				var subLevel = el.querySelector( 'div.sm-level' );
			
				$(el).find( '>a' ).on( self.levelEventType, function( ev ) {

					var $this = $(this);
					var level = self.getLevelDepth(closest( el, '.sm-level' ));

					if(!self.disableItemClick) {

						if (subLevel && self.level <= level) {

							ev.preventDefault();
							ev.stopImmediatePropagation();
							ev.stopPropagation();

							classie.add(closest(el, '.sm-level'), 'sm-level-overlay');
							self.openMenu(subLevel);

						} else if (!$this.hasClass('sm-trigger')) {

							var link = $this.attr('href');
							var target = $this.attr('target');
							var scroll_target = link;
							var will_scroll = false;

							if (target !== '_blank') {

								if (scroll_target && scroll_target.search('#') !== -1) {

									if (scroll_target.charAt(0) !== '#') {

										scroll_target = scroll_target.split('#');
										scroll_target = $('#' + scroll_target[1]);

										if (scroll_target.length === 0) {
											scroll_target = null;
										}
									}

									if (scroll_target && $(scroll_target).length > 0) {
										will_scroll = true;
									}

								}

								if (!will_scroll) {

									if (link) {
										ev.preventDefault();
										ev.stopImmediatePropagation();
										ev.stopPropagation();
									}

									if (link && link.length > 0 && link.charAt(0) !== '#') {

										location.href = link;

									} else {

										self.resetMenu(false);
									}
								}
							}
						}
					}else{
						ev.preventDefault();
						ev.stopImmediatePropagation();
						ev.stopPropagation();
					}
				});
				
			});
			

			self.levels.forEach( function( el ) {
				
				// overlap closing the sub levels :
				// by clicking on the visible part of the level element
			
				if(self.type === 'overlap') {
					var overlap_bar = el.querySelector('.sm-level-overlap');
					$(overlap_bar).on( self.levelEventType, function(ev) {
						goBack(ev, self.getLevelDepth(el) + 1);
					});
				}
				
				// Level Transition End
				el.addEventListener(transitionEnd(), function(ev) {
					ev.stopImmediatePropagation();
					ev.stopPropagation();
					
					if ((/transform/i.test(ev.propertyName))){
						if(typeof(ev.pseudoElement) !== 'undefined' && ev.pseudoElement !== "") {
							return false;
						}
	
						if(!classie.has( ev.target, 'sm-level' )) {
							return false;
						}
						
						if(classie.has( ev.target, 'sm-level-opened' )) {
							
							classie.remove( ev.target, 'sm-level-opened' );
							
						}else{
							
							classie.add( ev.target, 'sm-level-opened' );
							self.fireEvent('sm-level-opened', {
								menu_id: self.menu_id, 
								levelDepth: self.getLevelDepth(ev.target),
								level: ev.target
							});
		
						}
						if(classie.has(self.el, 'sm-resizing')) {
							classie.remove(self.el, 'sm-resizing');
							self.fireEvent('sm-resize-stop', {menu_id: self.menu_id});
						}
					}
				});
				
							
				// Main Level Scroll Event
				$(el).find('>.sm-level-inner').on('scroll', levelScrollEvent);
						
			});

			
			// Close Link Event
			$(self.closeLink).on( self.levelEventType, goBack);
			
			
			// by clicking on a specific element
			self.levelBack.forEach( function( el ) {
				$(el).on( self.levelEventType, goBack);
			});
							
				
			// the menu should close if clicking somewhere on the body (excluding clicks on the menu)
			if(self.menuEventType !== 'mousenenter' || self.isMobile()) {
				
				$(document).on( self.menuEventType, function(ev) {
					self.bodyClickFn(ev, self);	
				});
				
			}else{
				
				$(self.el).on( 'mouseleave', function(ev) {
					self.bodyClickFn(ev, self);	
				});	
			}	

			self.addEventListener('sm-loaded', function() {
				self.fireEvent('sm-resize', {menu_id: self.menu_id});
				
			});

			self.addEventListener('sm-open-first', function(e) {

				self.vAlign();
				self.setSelectedLevel();
			});
			
			self.addEventListener('sm-open sm-av-open', function(e) {
					
				self.openHamburgerTrigger();
			});

			self.addEventListener('sm-closing sm-av-close', function(e) {
				
				self.closeHamburgerTrigger();
				self.showHamburgerTrigger();
			});
			
			self.addEventListener('sm-level-opening sm-level-active', function(e) {
								
				if(e.detail.levelDepth > 1) {
					self.hideHamburgerTrigger();
				}else{
					self.showHamburgerTrigger();
					$(self.mainLevel).find('>.sm-level-inner').removeData('scrolling').trigger('scroll');
				}
			});
			
			window.addEventListener('resize', resizeEvent);
			resizeEvent();

			if(typeof(self.callback) !== 'undefined') {
				self.callback(self);
			}

			self.fireEvent('sm-loaded', {menu_id: self.menu_id});
		};

		this.closeHamburgerTrigger = function(notimeout) {
			
			var self = this;
			if(!self.hamburgersExists) {
				return false;
			}
			
			notimeout = typeof(notimeout) !== 'undefined' ? notimeout : false
			
			self.allHamburgers.forEach( function( el ) {
		
				if(parseInt(el.getAttribute('data-id')) === self.menu_id) {
					
					if(!notimeout) {
						
						Timers.setTimeout(function() {
							$(el).removeClass('is-active');	
						}, self.closeAnimationSpeed * 0.5 );
						
					}else{
						$(el).removeClass('is-active');	
					}
					
				}else{
					
					if(!notimeout) {
						
						Timers.setTimeout(function() {
							$(el).removeClass('sm-hidden');
						}, self.closeAnimationSpeed );
						
					}else{
						$(el).removeClass('sm-hidden');
					}	
				}	
			});
		};
		
		this.openHamburgerTrigger = function() {
			
			var self = this;
			if(!self.hamburgersExists) {
				return false;
			}
			
			self.allHamburgers.forEach( function( el ) {
		
				if(parseInt(el.getAttribute('data-id')) === self.menu_id) {
					$(el).addClass('is-active');	
				}else{
					if(!self.alwaysVisible) {
						$(el).addClass('sm-hidden');	
					}
				}	
			});
		};
		
		this.hideHamburgerTrigger = function() {
			
			var self = this;
			if(!self.menuHasHamburgers) {
				return false;
			}
			
			self.menuHamburgers.forEach( function( el ) {
		
				if(parseInt(el.getAttribute('data-id')) === self.menu_id) {
					$(el).addClass('sm-hidden');	
				}	
			});
		};
		
		this.showHamburgerTrigger = function() {
			
			var self = this;
			if(!self.menuHasHamburgers) {
				return false;
			}
			
			self.menuHamburgers.forEach( function( el ) {
		
				if(parseInt(el.getAttribute('data-id')) === self.menu_id) {
					$(el).removeClass('sm-hidden');	
				}	
			});
		};

		
		// the menu should close if clicking somewhere on the body
		
		this.bodyClickFn = function( ev, self ) {
			
			if(!self.open || self.alwaysVisible) {
				return false;
			}
			
			if(self.menuEventType === 'vclick' || self.menuEventType === 'click' || self.isMobile()) {
				
				if( !hasParent( ev.target, self.id ) ) {
					
					self.resetMenu(); 
					
					$(ev.target).off( self.menuEventType, function(ev) {
						self.bodyClickFn(ev, self);	
					});	
				}
				
			}else{

				self.resetMenu(); 
		
				$(ev.target).off( 'mouseleave', function(ev) {
					self.bodyClickFn(ev, self);	
				});
			}	
		};
		
		this.vAlign = function() {
			
			var self = this;
			var $level, $header, $logo, $search, $title, $back, $footer, $body, height;

			var alignLevel = function($level) {

				$logo = $level.find('> .sm-header .sm-logo');
				$search = $level.find('> .sm-header .sm-search');
				$title = $level.find('> .sm-level-body > .sm-level-align > .sm-title:visible');
				$back = $level.find('> .sm-back');
				$footer = $level.find('> .sm-footer');
				$body = $level.find('> .sm-level-body');
				
				height = 0;
				
				if($logo.length > 0) {
					
					height += $logo.outerHeight(true);
					$logo.find('img').on('load', function() {
						alignLevel($level);
						return false;
					});
				}
				
				if($search.length > 0) {
					height += $search.outerHeight(true);
				}

				if($back.length > 0) {
					height += $back.outerHeight(true);
				}

				if($footer.length > 0) {
					height += $footer.outerHeight(true);
				}
									
				if(height > 0) {
					$body.css({
						'height': 'calc(100% - '+height+'px)'
					});
				}
			};
			
			var alignLevelEvent = function() {
				
				alignLevel($(this));
			};
			
			$(self.el).find('.sm-level-inner').each(alignLevelEvent);    
		};

		this.animateReset = function() {
			
			var self = this;

			if(!self.alwaysVisible) {
			
				if(self.support) {
					self.setTransform( '', self.pusher );
					self.setTransform( '', self.el );
				}
				
				self.el.style.width = '';
			}

		};
		
		this.animate = function(closing) {
			
			var self = this,
			levelFactor,
			_pusherTranslateVal,
			_menuTranslateVal,
			pusherTranslateVal,
			menuTranslateVal;
			
			if(self.alwaysVisible) {
				
				if(!self.alwaysVisibleMenuHidden()) {
					self.setTransform( '', self.el );
				}
				return false;
			}
			
			var isMobileBreakpoint = self.isMobileBreakpoint();

			if(isMobileBreakpoint && self.type === 'overlap' && classie.has( self.el, 'sm-overlap')) {
				
				classie.remove( self.el, 'sm-overlap' );
				classie.add( self.el, 'sm-cover' );
				
			}else if(!isMobileBreakpoint && self.type === 'overlap' && classie.has( self.el, 'sm-cover')) {
				
				classie.remove( self.el, 'sm-cover' );
				classie.add( self.el, 'sm-overlap' );
			}
	
			var rotate = 0;
			var z = 0;

			levelFactor = (self.level - 1) * self.levelSpacing;

			_pusherTranslateVal = self.type === 'overlap' && !isMobileBreakpoint && self.level > 1 ? self.levelWidth + levelFactor : self.levelWidth;
			_menuTranslateVal = self.type === 'overlap'  && !isMobileBreakpoint ? levelFactor : 0;
	
			if(self.alwaysVisible) {
				_menuTranslateVal = _pusherTranslateVal - parseInt(self.option('level-width', '300px'));
			}	
			
			pusherTranslateVal = _pusherTranslateVal;
			menuTranslateVal = _menuTranslateVal;
				
			if(self.position === 'right') {
				pusherTranslateVal = -_pusherTranslateVal;
				menuTranslateVal = -_menuTranslateVal;
			}	
							
			var pusherEffects = [
				'sm-effect-2',
				'sm-effect-3',
				'sm-effect-4',
				'sm-effect-5',
				'sm-effect-7',
				'sm-effect-8',
				'sm-effect-10',
				'sm-effect-13',
				'sm-effect-14'
			];
			
			var pusherEffects2 = [
				'sm-effect-6'
			];
			
			var pusherEffects3 = [
				'sm-effect-9'
			];
			
			var pusherEffects4 = [
				'sm-effect-11'
			];	
			
			var menuEffects = [
				'sm-effect-1',
				'sm-effect-2',
				'sm-effect-4',
				'sm-effect-5',
				'sm-effect-9',
				'sm-effect-10',
				'sm-effect-11',
				'sm-effect-12',
				'sm-effect-13'
			];
			
			if( self.support ) {

				if( self.inArray(pusherEffects, self.animationType, 'pusherEffects') ) {
								
					self.setTransform( 'translate3d(' + (pusherTranslateVal) + 'px,0,0)', self.pusher );
	
				}else if( self.inArray(pusherEffects2, self.animationType, 'pusherEffects2') ) {	
					
					rotate = -15;
					if(self.position === 'right') {
						rotate = -rotate;
					}	
					self.setTransform( 'translate3d(' + (pusherTranslateVal) + 'px,0,0) rotateY('+rotate+'deg)', self.pusher );
					
				}else if( self.inArray(pusherEffects3, self.animationType, 'pusherEffects3') ) {	
					
					self.setTransform( 'translate3d(0,0,' + (-_pusherTranslateVal) + 'px) rotateY(0.00001deg)', self.pusher );
					
				}else if( self.inArray(pusherEffects4, self.animationType, 'pusherEffects4') ) {	
					
					rotate = -25;
					if(self.position === 'right') {
						rotate = -rotate;
					}
	
					z = '-1000px';
					if(isMobileBreakpoint) {
						z = '0px';	
					}
				
					self.setTransform( 'translate3d(' + (pusherTranslateVal) + 'px,0,'+z+') rotateY('+(rotate)+'deg)', self.pusher );
				}
				
	
				if( self.inArray(menuEffects, self.animationType, 'menuEffects') ) {	
					
					if(self.pushType) {
						self.setTransform( 'translate3d(' + (menuTranslateVal) + 'px,0,0)', self.el );
					}else{
						self.setTransform( 'translate3d(0,0,0)', self.el );
					}
				}
			
			}
			
		};


								
		this.openMenu = function( subLevel, callback) {
			
			var self = this;
			
			var open = self.open;

			var openMenuLevel = function() {

				if(!subLevel || (subLevel && classie.has(subLevel, 'sm-main-level'))) {
	
					if(self.alwaysVisible && self.open && !self.isReloading) {
	
						if(self.isMobileBreakpoint()) {
							self.showAlwaysVisibleMenu();	
							$(self.el).data('av-opened-by-user', true);
						}
					
						if(typeof(callback) !== 'undefined') {
							callback();
						}
						return false;
						
					}

				}

				if(self.alwaysVisible && self.isMobileBreakpoint()) {
					self.showAlwaysVisibleMenu();

					if(typeof(callback) !== 'undefined') {
						callback();
					}
					return false;
				}

                if(self.alwaysVisible) {
                    self.initAlwaysVisible();
                }else{
                    self.resetAlwaysVisible();
                }

				if(!self.created) {
	
					self.insertAjaxMenu(callback);
					return false;	
				}
				
				
				if( subLevel ) {
					self.resizeLevel(subLevel);
				}else{
					self.resizeLevel(self.mainLevel);
				}
			
				if(!open) {
					
					self.fireEvent('sm-opening', {menu_id: self.menu_id});
					
					if(!self.alwaysVisible) {
						iNoBounce.enable();
					}
					
					classie.remove( self.pusher, 'sm-pusher-closing' );
					classie.remove( self.el, 'sm-menu-closing' );
					
					classie.add( self.el, 'sm-triggered' );
					classie.add( self.container, 'sm-menu-'+self.menu_id );
					
					if(!self.alwaysVisible && self.overflowFixNeeded()) {
						
						var scrollTop = $('.sm-content').data('scrolltop');
						classie.add( self.html, 'sm-overflow-fix' );
						
						setScrollTop(self.content, scrollTop);
					}
									
					
					if(self.pushType) {
						classie.add( self.wrapper, 'sm-push' );
					}else{
						classie.remove( self.wrapper, 'sm-push' );
					}
	
					self.open = true;
					
					if(!self.openFirst) {
						self.openFirst = true;
						self.fireEvent('sm-open-first', {menu_id: self.menu_id});
					}
					
					self.fireEvent('sm-open', {menu_id: self.menu_id});
				}
		
				if( subLevel ) {
					self.fireEvent('sm-level-opening', {
						menu_id: self.menu_id,
						levelDepth: self.getLevelDepth(subLevel), 
						level: subLevel
					});
				}else{
					self.fireEvent('sm-level-opening', {
						menu_id: self.menu_id,
						levelDepth: self.getLevelDepth(self.mainLevel),
						level: self.mainLevel
					});
				}
	
				Timers.setTimeout(function() {
	
					// increment level depth
					self.previousLevel = self.level;
					self.previousLevelEl = self.levelEl;
					
					if(subLevel) {
						
						self.level = self.getLevelDepth(subLevel);
						self.levelEl = subLevel;
		
					}else{
						self.level++;
						self.levelEl = self.mainLevel;
					}	
		
					// move the main wrapper
					
					self.animate();
					
	
					// add class sm-menu-open to main wrapper if opening the first time
					if( self.level === 1 || subLevel) {
						
						if(!open) {
													
							classie.add( self.html, 'sm-menu-active' );
							classie.add( self.wrapper, self.animationType );
							classie.add( self.wrapper, 'sm-'+self.position );
							
							if(!self.alwaysVisible){
								classie.add( self.pusher, 'sm-'+self.position );
							}
							
							classie.add( self.wrapper, 'sm-menu-open' );
							classie.add( self.el, 'sm-active' );
	
						}
	
					}
			
					if( subLevel ) {

						self.setContentFilter(subLevel);
		
						subLevel.style.zIndex = (self.level + 1) * 2;
						
						classie.add( subLevel, 'sm-level-open' );
						
						self.fireEvent('sm-level-open', {
							menu_id: self.menu_id,
							levelDepth: self.getLevelDepth(subLevel), 
							level: subLevel
						});
						
						if(!self.support) {
							classie.add( subLevel, 'sm-level-opened' );
							self.fireEvent('sm-level-opened', {
								menu_id: self.menu_id, 
								levelDepth: self.getLevelDepth(subLevel),
								level: subLevel
							});

                            self.initCurrentLevel()
						}
		
												
						var level = subLevel.parentElement;
						var parentLevel = closest( level , '.sm-level' );
						
						while(parentLevel && parentLevel !== level) {
							
							classie.add( parentLevel, 'sm-level-overlay' );
							classie.add( parentLevel, 'sm-level-open' );
	
							self.fireEvent('sm-level-inactive', {
								menu_id: self.menu_id,
								levelDepth: self.getLevelDepth(parentLevel), 
								level: parentLevel
							});
							
							level = parentLevel.parentElement;
							parentLevel = closest( level, '.sm-level' );
							
						}
	
					}else{
						
						self.setContentFilter(self.mainLevel);
					
						classie.add( self.mainLevel, 'sm-level-open' );
						
						self.fireEvent('sm-level-open', {
							menu_id: self.menu_id,
							levelDepth: self.getLevelDepth(self.mainLevel), 
							level: self.mainLevel
						});
						
						classie.add( self.mainLevel, 'sm-level-opened' );
						self.fireEvent('sm-level-opened', {
							menu_id: self.menu_id,
							levelDepth: self.getLevelDepth(self.mainLevel), 
							level: self.mainLevel
						});

                        self.initCurrentLevel();
						
					}
					
				}, 3);
				
				if(typeof(callback) !== 'undefined') {
					callback();
				}

			};
			
			
			if( subLevel ) {
				self.setWrapperDataLevel(subLevel, openMenuLevel);
			}else{
				self.setWrapperDataLevel(self.mainLevel, openMenuLevel);
			}
	
		};
		
		this.initWrapperDataLevel = function() {
			
			var self = this;
			
			if(self.alwaysVisible || !self.wrapperBgUsed()) {
				return false;
			}
			
			self.resetWrapperDataLevel();
				
			var wrapperBg = self.option('wrapper-bg');
			if(wrapperBg.image_url) {
				preloadImage(wrapperBg.image_url);
			}
		};	

		this.setWrapperDataLevel = function(level, callback) {
		
			callback = typeof(callback) !== 'undefined' ? callback : function(){};
			
			var self = this;
			
			if(self.alwaysVisible || !self.wrapperBgUsed()) {
				callback();
				return false;
			}
			
			var level_li, 
			level_id, 
			current_level_id, 
			closing = true;
			
			if(self.level > self.previousLevel) {
				closing = false
			}

			if(closing) {
				$(self.container).addClass('sm-level-closing');
			}else{
				$(self.container).removeClass('sm-level-closing');
			}	

			var makeGradientStyle = function(){
			    var gradientString = '\
			        background: -moz-linear-gradient({direction}, {colour1} 0%, {colour2} 100%);\
			        background: -o-linear-gradient({direction}, {colour1} 0%, {colour2} 100%);\
			        background: -webkit-linear-gradient({direction}, {colour1} 0%, {colour2} 100%);\
			        background: -ms-linear-gradient({direction}, {colour1} 0%, {colour2} 100%);\
			        background: linear-gradient({direction}, {colour1} 0%, {colour2} 100%);\
			    ';
			    
			    var gradientString = 'linear-gradient({direction}, {colour1} 0%, {colour2} 100%)';
			    
			    return function(direction, colour1, colour2){
			        return gradientString
			        .replace(/\{direction\}/g, direction)
			        .replace(/\{colour1\}/g, colour1)
			        .replace(/\{colour2\}/g, colour2)
			    }
			}();
	
			var loadedImage = function(image, level_id) {
				
				image = typeof(image) !== 'undefined' ? image : '';
				
				$(self.container).attr('data-level', level_id);
				
				var bgColor = $(level).data('wrapper-color');
				
				var wrapperBg = {
					'background-color': $(level).data('wrapper-color'),
					'background-repeat': $(level).data('wrapper-repeat'),
					'background-size': $(level).data('wrapper-size'),
					'background-position': $(level).data('wrapper-position')
				};
				
				if(image && image !== '') {
					
					wrapperBg['background-image'] = 'url('+image+')';
					
				}else if(bgColor && bgColor !== ''){
					
					wrapperBg['background-image'] = 'inherit';
				}
				
				$(self.wrapperBg2).css(wrapperBg);
				$(self.wrapperBg2).data('css', wrapperBg);

				if(!$(self.wrapperBg1).attr('style')) {
					$(self.wrapperBg1).css(wrapperBg);
				}
				
				
				if($(level).data('pattern-image') && $(level).data('pattern-image') !== '') {
					
					var wrapperPattern = {
						'background-image': 'url('+$(level).data('pattern-image')+')',
						'opacity': $(level).data('pattern-opacity')
					};
					
					$(self.wrapperBg2).find('.sm-wrapper-pattern').css(wrapperPattern);
					$(self.wrapperBg2).data('pattern-css', wrapperPattern);
					
					if(!$(self.wrapperBg1).find('.sm-wrapper-pattern').attr('style')) {
						$(self.wrapperBg1).find('.sm-wrapper-pattern').css(wrapperBg);
					}
				
				}
				
				if($(level).data('overlay-type')) {
					
					var wrapperOverlay;
					
					if($(level).data('overlay-type') === 'gradient') {
						
						wrapperOverlay = {
							'background': makeGradientStyle(
								$(level).data('overlay-direction'),
								$(level).data('overlay-color-start'),
								$(level).data('overlay-color-end')
							)
						};
					
					}else if($(level).data('overlay-type') === 'color'){
						
						wrapperOverlay = {
							'background': $(level).data('overlay-color')
						};
						
					}
					
					if(wrapperOverlay) {
						$(self.wrapperBg2).find('.sm-wrapper-overlay').css(wrapperOverlay);
						$(self.wrapperBg2).data('overlay-css', wrapperOverlay);
						
						if(!$(self.wrapperBg1).find('.sm-wrapper-overlay').attr('style')) {
							$(self.wrapperBg1).find('.sm-wrapper-overlay').css(wrapperBg);
						}
					
					}
				}
				
				if($(level).data('wrapper-filter')) {
					
					var wrapperFilter = $(level).data('wrapper-filter');
					$(self.wrapperBg2).addClass(wrapperFilter);
					$(self.wrapperBg2).data('filter-class', wrapperFilter);
					
					if(!$(self.wrapperBg1).hasClass(wrapperFilter)) {
						$(self.wrapperBg1).addClass(wrapperFilter);
					}
				}
				
				callback();

			};
				
					
											
			if($(level).hasClass('sm-has-wrapper-bg')) {
			
				level_li = $(level).closest('li.menu-item');
				
				if(level_li && level_li.length > 0) {
					
					level_id = $(level_li).attr('id');
					
				}else{
					
					level_id = 'menu-item-main';
				}
				
				current_level_id  = $(self.container).attr('data-level');
				
				if(current_level_id !== level_id) {
					
					$(self.container).attr('data-level', '');
					
					var current_bg_image = $(self.wrapperBg2).css('background-image');
					var bg_image = $(level).data('wrapper-image');

					if(bg_image && bg_image !== current_bg_image) {
						
						if($(self.wrapperBg2).data('css')) {
							$(self.wrapperBg1).css($(self.wrapperBg2).data('css'));
						}
						
						if($(self.wrapperBg2).data('pattern-css')) {
							$(self.wrapperBg1).find('.sm-wrapper-pattern').css($(self.wrapperBg2).data('pattern-css'));
						}
						
						if($(self.wrapperBg2).data('overlay-css')) {
							$(self.wrapperBg1).find('.sm-wrapper-overlay').css($(self.wrapperBg2).data('overlay-css'));
						}
						
						if($(self.wrapperBg2).data('filter-class')) {
							$(self.wrapperBg1).addClass($(self.wrapperBg2).data('filter-class'));
						}
						
						if(bg_image && bg_image !== '') {
							
							if(!self.inArray(preloadedImages, bg_image, 'preloadedImages')) {
					
								self.showLoading();
								
								preloadImage(bg_image, function(image) {
									
									Timers.setTimeout(function() {
										loadedImage(image, level_id);
									}, 5);
									
									self.hideLoading();
								});
							
							}else{
							
								loadedImage(bg_image, level_id);
							}
							
						}else{
							callback();
						}	
					
					}else{
						loadedImage(null, level_id);
					}
				
				}else{
					callback();
				}

			}else{
				callback();
			}
		};


		this.resetWrapperDataLevel = function(timeout) {
			
			var self = this;
			
			if(self.alwaysVisible || !self.wrapperBgUsed()) {
				return false;
			}
			
			timeout = typeof(timeout) !== 'undefined' ? timeout : false;
			
			var resetWrapper = function() {
				
				$(self.container).attr('data-level', 'main-item-level');
				$(self.container).removeClass('sm-level-closing');
				
				$(self.wrapperBg1).removeAttr('style');
				$(self.wrapperBg1).find('.sm-wrapper-pattern').removeAttr('style');
				$(self.wrapperBg1).find('.sm-wrapper-overlay').removeAttr('style');
				
				$(self.wrapperBg1).removeClass (function (index, css) {
				    return (css.match (/(^|\s)sm-filter-\S+/g) || []).join(' ');
				});
				
				$(self.wrapperBg2).removeAttr('style');
				$(self.wrapperBg2).removeData('css');
				$(self.wrapperBg2).removeData('pattern-css');
				$(self.wrapperBg2).removeData('overlay-css');
				
				$(self.wrapperBg2).find('.sm-wrapper-pattern').removeAttr('style');
				$(self.wrapperBg2).find('.sm-wrapper-overlay').removeAttr('style');
				
				$(self.wrapperBg2).removeData('filter-class');
				$(self.wrapperBg2).removeClass (function (index, css) {
				    return (css.match (/(^|\s)sm-filter-\S+/g) || []).join(' ');
				});
			};
			
			if(timeout !== false) {
				Timers.setTimeout(resetWrapper, timeout);
			}else{
				resetWrapper();
			}
		};
		
		this.wrapperBgUsed = function() {

			var menuEffects = [
				'sm-effect-2',
				'sm-effect-3',
				'sm-effect-4',
				'sm-effect-5',
				'sm-effect-7',
				'sm-effect-9',
				'sm-effect-10',
				'sm-effect-11',
				'sm-effect-12',
				'sm-effect-13',
				'sm-effect-14'
			];
			
			return this.inArray(menuEffects, this.animationType, 'wrapperBgUsed');
		};
		
		this.overflowFixNeeded = function() {
			
			if(this.isMobile()) {
				return false;
			}

			return true;
		};
		
		this.setContentFilter = function(level) {
						
			var contentFilter = level.getAttribute('data-content-filter'); 							
			if(contentFilter !== '' && !classie.has(this.content, contentFilter) && !this.alwaysVisible) {
				classie.add(this.content, contentFilter);
			}
						
		};
		
		this.removeContentFilter = function() {
							
			$(this.content).removeClass (function (index, css) {
			    return (css.match (/(^|\s)sm-filter-\S+/g) || []).join(' ');
			});
		};

							
		// close the menu
		this.resetMenu = function(noTimeout, callback) {
			
			var self = this;

			if(!self.open) {
				return false;
			}
			
			classie.add( self.pusher, 'sm-pusher-closing' );
			classie.add( self.el, 'sm-menu-closing' );
			
			if(self.alwaysVisible) {
								
				if(self.isMobileBreakpoint()) {
					self.hideAlwaysVisibleMenu();
					self.animateReset();	
				}
				
				self.level = 0;
				
				if(typeof(callback) !== 'undefined') {
					callback();
				}
				
				return false;
			}
		
			while(self.level > 1) {
				self.level--;
				self.closeMenu(true);
			}

				
			noTimeout = (typeof(noTimeout) !== 'undefined') ? noTimeout : false;	
			 
			self.fireEvent('sm-closing', {menu_id: self.menu_id});
			
								
			self.level = 0;
			
			self.animateReset();
							
			// Remove Any filters
			self.removeContentFilter();
				
			
			// remove class sm-menu-open from main wrapper
			classie.remove( self.wrapper, 'sm-menu-open' );

			var resetMenu = function() {
				
				classie.remove( self.wrapper, self.animationType );	
				classie.remove( self.wrapper, 'sm-'+self.position );
				classie.remove( self.el, 'sm-active' );
				classie.remove( self.el, 'sm-triggered' );
				
				if(self.overflowFixNeeded()) {
					classie.remove( self.html, 'sm-overflow-fix' );
				}
						
				classie.remove( self.container, 'sm-menu-'+self.menu_id );
							
				self.toggleLevels(true);
				
				classie.remove( self.mainLevel, 'sm-level-opened' );
				
				if(!SM_PRIVATE_API.alwaysVisibleEnabled()) {
					classie.remove( self.html, 'sm-menu-active' );
				}
	
				classie.remove( self.wrapper, 'sm-level-closing' );
	
				self.resetWrapperDataLevel();
				self.reInitAlwaysVisible();
				
				if(!self.alwaysVisible){
					classie.remove( self.pusher, 'sm-'+self.position );
				
					if(self.overflowFixNeeded()) {
						
						if(!noTimeout) {
							setWindowScrollTop($(self.content).data('scrolltop'));
						}
					}	
				}

				if(typeof(callback) !== 'undefined') {
					callback();
				}

				iNoBounce.disable();

				self.open = false;
				self.fireEvent('sm-close', {menu_id: self.menu_id});
				
				$(window).resize();
			};
			
			if(noTimeout) {
				
				resetMenu();
				
			}else{
				
				Timers.setTimeout(resetMenu, self.closeAnimationSpeed);
			}	
			
		};
		
		this.resizeMenu = function (width) {
			
			var self = this;
			
			var styleWidth = width;
			var levelWidth = width;
			var winWidth = getWindowWidth();
			var containerWidth = $(self.container).width();

			if(isNumeric(width)) {
				
				styleWidth = width + 'px';
				
			}else if(width.search('perc') !== -1 || width.search('%') !== -1 || width.search('vw') !== -1){
				
				var perc = 0;
				
				if(width.search('perc') !== -1) {
					
					perc = parseInt(width.replace('perc', ''));
					styleWidth = styleWidth.replace('perc', '%');
					levelWidth = ((perc / 100) * winWidth);	
					
				}else if(width.search('vw') !== -1) {	
					
					perc = parseInt(width.replace('vw', ''));
					levelWidth = ((perc / 100) * winWidth);	
					
				}else{
					
					perc = parseInt(width.replace('%', ''));
					levelWidth = ((perc / 100) * containerWidth);	
				}	
				
			}
	
			levelWidth = parseInt(levelWidth);

			if(parseInt(self.levelWidth) !== levelWidth) {
				classie.add(self.el, 'sm-resizing');
				self.fireEvent('sm-resize-start', {menu_id: self.menu_id});
			}
			
			self.levelWidth = levelWidth;

		};

		this.resizeLevel = function(level) {
			
			var width = 0;
			
			if(typeof(level) === 'undefined') {
				return false;
			}
			
			
			if(this.isMobileBreakpoint()) {
				
				width = '100%';
				
			}else{	

				if(!self.isMobile()) {
					
					width = level.getAttribute('data-width');
					
				}else{
					
					width = self.option('level-width', '300px');
				}	
			}	

			this.resizeMenu(width);
		};
		
		this.resizeCurrentLevel = function() {
			
			var level = this.getCurrentLevel();
			if(level) {

				this.resizeLevel(level);
			}
		};
		
		this.getLevelDepth = function(level) {
			
			if(level) {
				return parseInt(level.getAttribute( 'data-level' ));
			}
			return 0;
		};
		
		this.getCurrentLevelDepth = function() {
			
			var level = this.getCurrentLevel();
			if(level) {

				return this.getLevelDepth(level);
			}
			
			return 0;
		};
	
		this.resetAlwaysVisible = function() {

            if(classie.has( this.html, 'sm-always-visible' )) {
                classie.remove(this.html, 'sm-always-visible');
            }
		};
		
		this.initAlwaysVisible = function() {

            if(!classie.has( this.html, 'sm-always-visible' )) {
                classie.add(this.html, 'sm-always-visible');
            }
		};
		
		this.reInitAlwaysVisible = function() {
 				
			var menu = SM_PRIVATE_API.getAlwaysVisible();
			if(menu && menu.id !== this.id) {
				this.initAlwaysVisible();
			}
		}
		
		this.alwaysVisibleMenuHidden = function() {
			
			return this.alwaysVisibleHidden;
		};

		this.showAlwaysVisibleMenu = function() {
			
			var self = this;

			if(self.isMobileBreakpoint()) {
				self.html.style.overflow = 'hidden';
			}else{
				self.html.style.overflow = '';
			}
			
			if(self.support) {	
				self.setTransform('translate3d(0,0,0)', self.el );
			}else{
				$(self.el).fadeIn();
			}
			
			self.alwaysVisibleHidden = false;
			
			self.fireEvent('sm-av-open', {menu_id: self.menu_id});
			
			if(!self.openFirst) {
				self.openFirst = true;
				self.fireEvent('sm-open-first', {menu_id: self.menu_id});
				
				setTimeout(function(){
					self.vAlign();
				},100);
			}
		};
		
		this.hideAlwaysVisibleMenu = function() {
			
			var self = this;
		
			self.html.style.overflow = '';
			
			if(self.support) {
				
				var translate = self.position === 'left' ? '-110%' : '110%';
				self.setTransform('translate3d('+translate+',0,0)', self.el );
			
			}else{
				
				$(self.el).fadeOut();
			}
			
			self.alwaysVisibleHidden = true;
			
			self.fireEvent('sm-av-close', {menu_id: self.menu_id});
		};
		
		
		// close sub menus
		this.closeMenu = function(resetting) {
	
			this.toggleLevels(resetting);
			this.animate(true);
		};
		
		// translate the el
		this.setTransform = function( val, el ) {
			el = el || this.pusher;
			el.style.WebkitTransform = val;
			el.style.MozTransform = val;
			el.style.transform = val;
		};
		
		// removes classes sm-level-open from closing levels
		this.toggleLevels = function(resetting) {
			
			var self = this;
			var resetMode = (typeof(resetting) !== 'undefined') ? resetting : false;
			
			// Remove Any filters
			self.removeContentFilter();
				
			for( var i = 0, len = this.levels.length; i < len; ++i ) {
				var levelEl = this.levels[i];
				var levelDepth = self.getLevelDepth(levelEl);
				
				if( levelDepth >= self.level + 1 ) {
					
					if(classie.has( levelEl, 'sm-level-open' )) {
						
						self.fireEvent('sm-level-closing', {
							menu_id: self.menu_id,
							levelDepth: levelDepth, 
							level: levelEl
						});
						
						classie.remove( levelEl, 'sm-level-overlay' );
						classie.remove( levelEl, 'sm-level-open' );
						
						if(!self.support) {
							classie.remove( levelEl, 'sm-level-opened' );
						}

						self.fireEvent('sm-level-close', {
							menu_id: self.menu_id,
							levelDepth: levelDepth, 
							level: levelEl
						});

					}
					
					levelEl.style.zIndex = 2;
					
				}else if( !resetMode && levelDepth === self.level ) {
					
					classie.remove( levelEl, 'sm-level-overlay' );
				}
			}
			
			var currentLevel = self.getCurrentLevel();

			if( currentLevel ) {

				self.fireEvent('sm-level-active', {
					menu_id: self.menu_id,
					levelDepth: self.getLevelDepth(currentLevel), 
					level: currentLevel
				});
				
				self.resizeLevel(currentLevel);
				self.setContentFilter(currentLevel);
				self.setWrapperDataLevel(currentLevel);
			}
		};

		this.toggleMenuEvent = function() {
			
			this.toggleMenu();
			
		};
				
		this.toggleMenu = function(callback) {
	
			var self = this;

							
			// If always visible menu (on desktop) and already open, abort. Continue if mobile or if reloading
			if(!self.isMobileBreakpoint() && self.alwaysVisible && self.open && self.created && !self.isReloading) {
				
				if(typeof(callback) !== 'undefined') {
					callback();
				}
							
				return false;	
			}

			if(!self.created) {
				
				self.insertAjaxMenu(callback);
				
			}else{
				

				if(self.busy) {
					return false;
				}
							
				self.busy = true;
				Timers.setTimeout(function() {
					self.busy = false;
				}, self.openAnimationSpeed );
					
				
				if( self.open ) {
					
					if(self.alwaysVisible) {
						
						if(!self.alwaysVisibleMenuHidden()) {
							
							self.resetMenu(false, callback);
							
						}else{
							
							var subLevel = self.getSelectedLevel();
							if(!self.openCurrentLevel) {
								subLevel = false;
							}
							self.openMenu(subLevel, callback);
						}
						
					}else{
						self.resetMenu(false, callback);
					}
					
				}else {
					
					var subLevel = self.getSelectedLevel();
					if(!self.openCurrentLevel) {
						subLevel = false;
					}
							
					var openMenu = SM_PRIVATE_API.getOpen();

					if(openMenu !== false) {
						
						openMenu.resetMenu(false, function() {
							
							self.openMenu(subLevel, callback);
						});
						
					}else{
						
						self.openMenu(subLevel, callback);
					}
					
				}
			}	
		};
		
		this.getSelectedLevel = function() {
			
			this.setSelectedLevel();
			
			var subLevel = null;
			var selectedItem = this.el.querySelector('.current-menu-item');
			
			if(selectedItem) {
				subLevel = closest(selectedItem, '.sm-level');
			}
			
			return subLevel;
		};
		
		this.getSubLevel = function(id) {
			
			var subLevel = null;
			var menuItem = this.el.querySelector('.menu-item-has-children.menu-item-'+id);
			
			if(menuItem) {
				subLevel = menuItem.querySelector('.sm-level');
			}
			
			return subLevel;
		};
		
		this.hasSelectedLevel = function() {
			
			var subLevel = null;
			var selectedItem = this.el.querySelector('.current-menu-item');
			
			if(selectedItem) {
				return true;
			}
			
			return false;
		};
		
		this.setSelectedLevel = function() {
			
			if(this.selectedLevelSet) {
				return false;
			}
			
		    var href = window.location.href;
		    if(
		    	SM.in_customizer && 
		    	typeof(parent.window.wp.customize.previewer) !== 'undefned'
		    ){
			    href = parent.window.wp.customize.previewer.previewUrl();
		    }
		    
			$(this.el).find('li.current-menu-item').each(function() {
				$(this).removeClass('current-menu-item');
			});
			    
		    var item_href = '#';
		    var menus_items = $(this.el).find('li.menu-item a').filter(function() {

			    item_href = $(this).attr('href');
			    return item_href == href && item_href !== '#';
			});
			
			menus_items.each(function() {
				$(this).closest('li').addClass('current-menu-item');
			});	
			
			this.selectedLevelSet = true;
		};
		
		this.getCurrentLevel = function() {
			
			var subLevel = null;
			var currentItem = this.el.querySelector('.sm-level-open:not(.sm-level-overlay)');
	
			if(currentItem) {
				subLevel = currentItem;
			}
			return subLevel;
		};

		this.initCurrentLevel = function() {

            var currentLevel = this.getCurrentLevel();

            if( currentLevel ) {

                this.fireEvent('sm-level-active', {
                    menu_id: this.menu_id,
                    levelDepth: this.getLevelDepth(currentLevel),
                    level: currentLevel
                });

                this.resizeLevel(currentLevel);
                this.setContentFilter(currentLevel);
                this.setWrapperDataLevel(currentLevel);
            }
        };

		this.insertAjaxMenu = function(callback, force) {
			
			var self = this;
			force = typeof(force) !== 'undefined' ? 1 : 0
			
			if(self.isAjaxing) {
				return false;
			}
			
			self.isAjaxing = true;
			self.showLoading();
			
			$.ajax({
			    url: SM_VARS.sm_ajaxurl,
			    type: 'get',
			    dataType: 'json',
			    data: { 
				    id: self.menu_id,
				    force: force
				}
			})
			// using the done promise callback
            .done(function(data) {
				
			    self.hideLoading();
			     
			    self.isAjaxing = false;
			    $(self.triggerSelector).off(self.menuEventType, self.insertAjaxMenuEvent);
			     
			    if(data.error === '') {
							     
					$(self.el).replaceWith($(data.menu));
					self.el = $('#'+self.id).get(0);
	
					SM_MENUS[self.id] = self;
					
					self.options = data.options;
					self.init();

					self.toggleMenu(function() {
				
						if(typeof(self.callback) !== 'undefined') {
							self.callback(self);
						}
										
						if(typeof(callback) !== 'undefined') {
							callback(data);
						}

					});
			    }
					
            })
			.fail(function() {
	            
	            self.isAjaxing = true;
	            self.ajax_errors++;
	            
	            if(self.ajax_errors < 3) {
		            
	            	self.insertAjaxMenu(callback, force);
	            	
	            }else{
		            
		            self.hideLoading();
	            }
			}); 
		
		};
		
		this.updateTriggers = function(triggers) {
			
			var self = this;
			var menuTriggers = Array.prototype.slice.call(document.querySelectorAll(triggers));
			var toggleEvent = function( ev ) {
				
				ev.preventDefault();
				ev.stopImmediatePropagation();
				ev.stopPropagation();
				
				self.toggleMenuEvent(ev);
			};
			
			for(var i = 0; i < menuTriggers.length ; i++) {
				
				$(menuTriggers[i]).on( self.menuEventType, toggleEvent);	
			}
		};
						


		/* After Ajax Loaded Menu, check if menu has mpm triggers and init trigger events */
		this.initAfterAjaxEvents = function() {
			
			var self = this;
			
			$(document).ajaxComplete(function(e, xhr) {
				
				if(xhr.responseJSON && xhr.responseJSON.menu_id) {
					
					var obj = SM_PRIVATE_API.get(xhr.responseJSON.menu_id);
								
					if($(obj.el).data('menu-triggers-ready')) {
						return false;
					}

					var updateTriggerEvent = function(e) {
			
						e.preventDefault();
						var triggerClass = $(this).attr("class");
						var id = parseInt(triggerClass.replace('sm-trigger-', ''));
						SM_API.toggle(id);
					};
					
					$(obj.el).find('[class*="sm-trigger-"]').on(self.levelEventType, updateTriggerEvent);
					
					$(obj.el).data('menu-triggers-ready', true);	
				}	
			});	
			
		};
	
	
		this.reloadMenu = function(callback, silent) {
			
			var self = this;
		
			if(self.isReloading) {
				return false;
			}
			
			silent = typeof(silent) !== 'undefined' && silent === true;
			
			self.fireEvent('sm-reloading', {menu_id: self.menu_id});
			classie.add(self.html, 'sm-reloading');
			classie.add(self.el, 'sm-reloading');
			
						
			var currentLevel = self.getCurrentLevel();
			if(currentLevel) {
				var currentLevelSelector = '#'+$(currentLevel).closest('li').attr('id')+' .sm-level';
			}
			
			var reload = function(callback, silent) {
				
				self.created = false;
				self.isReloading = true;
				
				self.insertAjaxMenu(function(data) {
					
					if(typeof(callback) !== 'undefined') {
						callback(data);
					}
					
					if(currentLevel && !$(currentLevel).hasClass('sm-main-level')) {
						
						Timers.setTimeout(function() {
							
							self.openMenu($(currentLevelSelector).get(0), function (){
								
								self.fireEvent('sm-reloaded', {menu_id: self.menu_id});
													
								classie.remove(self.html, 'sm-reloading');
								classie.remove(self.el, 'sm-reloading');
								
								self.isReloading = false;
								
								if(silent) {
									self.closeMenu();
								}
					
							});
							
						}, 1000);
						
					}else{

						self.fireEvent('sm-reloaded', {menu_id: self.menu_id});
											
						classie.remove(self.html, 'sm-reloading');
						classie.remove(self.el, 'sm-reloading');
					
						self.isReloading = false;
						
						if(silent) {
							self.closeMenu();
						}
		
					}

				}, true);
			};
			
			if(self.created && self.open) {
				
				self.toggleMenu(function() {
					
					reload(callback, silent);
				});
			
			}else{
			
				reload(callback, silent);
			}	
			
		};
		
		this.showLoading = function() {
			
			classie.add(this.wrapperLoading, 'is-loading');
		};
		
		this.hideLoading = function() {
			
			classie.remove(this.wrapperLoading, 'is-loading');
		};
		
		this.inArray = function(arr, value, key) {
			
			key = typeof(key) !== 'undefined' ? key : false;
			
			if(key) {
				if(typeof(this.cache[key]) !== 'undefined') {
					return this.cache[key];
				}
				this.cache[key] = arr.indexOf(value) !== -1;
				return this.cache[key];	
			}
			return arr.indexOf(value) !== -1;
		};

		this.addEventListener = function(evt, handler, capture) {
			
			var node = this.el;
			var events = evt.split(' ');
			for (var i=0, iLen=events.length; i<iLen; i++) {
				evt = events[i];
	
			    if(!(node in this.eventHandlers)) {
			        // eventHandlers stores references to nodes
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
	
		this.removeEventListeners = function() {
			
			var node = this.el;
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

		this.fireEvent = function(eventType, data) {
			
			data = data || {};
			
			var params = {
				bubbles: false,
				cancelable: false,
				detail: data
			};
			
			data.menu = this.el;
		
			var evt = new CustomEvent(eventType, params);
			this.el.dispatchEvent(evt);
			document.dispatchEvent(evt);
		};
		
		this.preInit();
	};



	// PUBLIC API ------
	
	var SM_PRIVATE_API = {
		
		create: function(el, trigger, options, settings, callback) {
			
			return new SlickMenu(el, trigger, options, settings, callback);	
		},
		
		get: function(id) {
			
			var obj = null;
			if(typeof(SM_MENUS['sm-menu-'+id]) !== 'undefined') {
				obj = SM_MENUS['sm-menu-'+id];
			}
			return obj;
		},
		
		getOpen: function() {
			
			var obj = false;
			var FoundException = {};

			try {
				
			    Object.keys(SM_MENUS).forEach(function (key) {
	
				   if(SM_MENUS[key].open && !SM_MENUS[key].alwaysVisible) {
					   obj = SM_MENUS[key];
					   throw FoundException;
				   }
				});
				
			} catch(e) {}

			return obj;
		},

		getAlwaysVisible: function() {
			
			var obj = false;
			var el = document.querySelector( '.sm-menu.sm-always-visible' );
			if(el) {
				if(typeof(SM_MENUS[el.id]) !== 'undefined') {
					obj = SM_MENUS[el.id];
				}
			}	
			
			return obj;
		},
		
		alwaysVisibleEnabled: function() {
			
			var el = document.querySelector( '.sm-menu.sm-always-visible' );
			
			if(el) {
				return true;
			}	
			
			return false;
		},
		
		getAll: function() {

			return SM_MENUS;
		}		
	};
	
	var SM_API = {
		
		create: function(el, trigger, options, settings, callback) {
			
			SM_PRIVATE_API.create(el, trigger, options, settings, function(obj) {
				callback(obj.el);
			});	
		},

		get: function(id) {
			
			var obj = SM_PRIVATE_API.get(id);
			if(obj) {
				return obj.el;
			}
			return null;
		},

		getOpen: function() {
			
			var obj = SM_PRIVATE_API.getOpen();
			if(obj) {
				return obj.menu_id;
			}
			return null;
		},

		getAlwaysVisible: function() {
			
			var obj = SM_PRIVATE_API.getAlwaysVisible();
			if(obj) {
				return obj.menu_id;
			}
			return null;
		},
		
		alwaysVisibleEnabled: function() {
			
			return SM_PRIVATE_API.alwaysVisibleEnabled();
		},
						
		toggle: function (id, callback) {
		
			var obj = SM_PRIVATE_API.get(id);
			
			if(obj) {
				obj.toggleMenu(callback);
			}
		},
		
		open: function (id, callback) {
			
			var obj = SM_PRIVATE_API.get(id);
			
			if(obj) {
				
				if(!obj.open || obj.alwaysVisible) {
					
					var subLevel = obj.getSelectedLevel();
								
					if(!obj.openCurrentLevel) {
						subLevel = false;
					}

					var openMenu = SM_PRIVATE_API.getOpen();
					
					if(openMenu !== false) {
						
						openMenu.resetMenu(false, function() {
							
							obj.openMenu(subLevel, callback);
						});
						
					}else{
			
						obj.openMenu(subLevel, callback);
					}
				}	
			}
		},	
		
		openSubLevel: function (id, sub_id, callback) {
			
			var self = this;
			var obj = SM_PRIVATE_API.get(id);
			
			if(obj) {
				
				var subLevel = obj.getSubLevel(sub_id);
				var currentLevel = obj.getCurrentLevel();
				
				if(subLevel === currentLevel) {
					return false;
				}
				
				if(subLevel) {
					if(obj.open) {
						
						var subLevelDepth = parseInt(subLevel.getAttribute('data-level'));
			
						if(subLevelDepth < obj.level) {
							
							while(obj.level > subLevelDepth) {
								obj.level--;
								obj.closeMenu();
							}
						
							self.openSubLevel(id, sub_id, callback);
							
						}else{
							
							obj.openMenu(subLevel, callback);
						}	
						
					}else{
								
						self.open(id, function() {
							
							obj.openMenu(subLevel, callback);
						});
					}
				}	
			}
		},	

		close: function (id, callback) {
	
			var obj = null;
			
			if(typeof(id) === 'undefined') {
				
				obj = SM_PRIVATE_API.getOpen();
		
			}else{
			
				obj = SM_PRIVATE_API.get(id);
			}	

			if(obj) {
				if(obj.open) {
					obj.resetMenu(false, callback);
				}	
			}
		},	
				
		isOpen: function (id) {
		
			var obj = SM_PRIVATE_API.get(id);
			
			if(obj) {
				if(!obj.alwaysVisible) {
					return obj.open;
				}else{
					return !obj.alwaysVisibleMenuHidden();
				}
			}
			
			return false;
		},
		
		isAlwaysVisible: function (id) {
		
			var obj = SM_PRIVATE_API.get(id);
			
			if(obj) {
				return obj.alwaysVisible;
			}
			
			return false;
		},

		isCreated: function (id) {
		
			var obj = SM_PRIVATE_API.get(id);
			
			if(obj) {
				return obj.created;
			}
			
			return false;
		},

		reload: function (id, callback, silent) {
		
			var obj = SM_PRIVATE_API.get(id);
			
			if(obj) {
				obj.reloadMenu(callback, silent);
			}
		},
				
		updateTriggers: function (id, triggers) {
		
			var obj = SM_PRIVATE_API.get(id);
			
			if(obj) {
				obj.updateTriggers(triggers);
			}
		},

		getCurrentLevel: function (id) {
		
			var obj = SM_PRIVATE_API.get(id);
			
			if(obj) {
				return obj.getCurrentLevel();
			}
			
			return null;
		},
				
		getCurrentLevelDepth: function (id) {
		
			var obj = SM_PRIVATE_API.get(id);
			
			if(obj) {
				return obj.getCurrentLevelDepth();
			}
			
			return 0;
		},
	
		getOption: function (id, key) {
		
			var obj = SM_PRIVATE_API.get(id);
			
			if(obj) {
				return obj.option(key);
			}
			
			return null;
		}
			
	};
	
	
	
	// add API to global namespace
	window.SlickMenu = SM_API;	
	

} )( window, jQuery );