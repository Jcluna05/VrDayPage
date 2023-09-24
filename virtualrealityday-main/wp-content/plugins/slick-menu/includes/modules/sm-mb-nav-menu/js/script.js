;( function( window, $ ) {
		
	'use strict';
	
	var SM_MB_NAV_PANELS = {};
	
	var SM_MB_NAV_MENU = {
		
		_init: false,
		vars: SM_MB_NAV_MENU_VARS,
		ajax_errors: 0,
			
		customizer: {
			iframe: null,
			iframeDoc: null,
			iframeLoaded: false
		},
		
		init: function() {
			
			if(!this._init) {
				$.ajaxSetup({async:true});
				
				$('html').addClass('sm-wp-version-'+this.vars.wp_version);

				if(this.vars.wp_version > '4-6-1') {
					$('html').addClass('sm-wp-version-ge-4-7');
				}
				
			    this.initEvents();	
			    this._init = true;	
		    }
		},
		
		customizerFrameLoaded: function() {
			    
			var self = this; 
			
			self.customizer.iframe = $(wp.customize.previewer.container).find('iframe');	
			self.customizer.iframeDoc = $(self.customizer.iframe.contents());
			self.customizer.iframeLoaded = true;
			
			self.SlickMenu = self.customizer.iframe[0].contentWindow.SlickMenu;
			 
			// Hack - Make sure dashicons is loaded within the customizer preview frame
			var dashIconsId = 'dashicons-css';
			var $dashIconsParentLink = $('#'+dashIconsId);
			var $dashIconsLink = self.customizer.iframeDoc.find('head').find('#'+dashIconsId);

			if($dashIconsLink.length === 0 && $dashIconsParentLink.length > 0) {
				$dashIconsLink  = $dashIconsParentLink.clone();
				$dashIconsLink.appendTo(self.customizer.iframeDoc.find('head'));
			}
		
			self.customizer.iframeDoc.off("sm-loaded sm-reloaded");
			self.customizer.iframeDoc.on("sm-loaded sm-reloaded", function(e) {

				self.slickMenuReloaded(e.detail.menu_id);
			});
			
			self.openCustomizerMenuEditor();
		},
		
		getCustomizerVars: function() {
			
			return this.customizer;
			
		},
		
		slickMenuReloaded: function(menu_id) {
			
			var self = this; 
			
			self.refreshVars(function() {

				var menu = self.SlickMenu.get(menu_id);
				menu = self.customizer.iframeDoc.find('#'+$(menu).attr('id'));
			
				$(menu).find('.sm-nav-list li:not(.menu-item-has-children) > a:not([class*="sm-trigger-"]), .slick-menu-widgets-wrap a').off();
				$(menu).find('.sm-nav-list li:not(.menu-item-has-children) > a:not([class*="sm-trigger-"]), .slick-menu-widgets-wrap a').on('click', function(e) {
	
					var $this = $(this);
					
					var href = $this.attr('href');
					var is_scroll_to = false;
					
					if(href.charAt(0) === '#' && href.length > 1 && $(href, self.customizer.iframeDoc).length > 0) {
						is_scroll_to = true;
					}
				
					if(href !== '' && !is_scroll_to) {
							
						e.preventDefault();
						
						$(wp.customize.previewer.container).removeClass('iframe-ready');
						
						var url = $this.get(0).href;
						wp.customize.previewer.previewUrl(url);
						self.refreshVars();
						
					}
				});
			});
		},
		
		refreshVars: function(callback) {
			
			var self = this; 

			if(wp && typeof(wp) !== 'undefined' && typeof(wp.customize) !== 'undefined' && typeof(wp.customize.previewer) !== 'undefined') {
				var interval = setInterval(function() {
					if(wp.customize) {
						if($(wp.customize.previewer.container).hasClass('iframe-ready')) {
							 clearInterval(interval);
							 self.customizerFrameLoaded();	
							 
							 if(typeof(callback) !== 'undefined') {
							 	callback();
							 }
						}
					}else{
						clearInterval(interval);
					}
						 
				}, 10);	
			}	
		},
		
		openCustomizerMenuEditor: function() {
			
			var self = this;
			
			if(self.busy) {
				return false;
			}
			
			self.busy = true;
			
			var interval = setInterval(function() {
				
				var $metabox_div = $('#menu-to-edit').find('.sm-mb-nav-menu-metabox-div');
	
				if($metabox_div.length === 0) {
					
					var $li = $('#menu-to-edit .customize-control-nav_menu_name');

					if($li.length !== 0) {
						
						var $li_id = $li.attr('id');
				
						if($li_id.search('--') === -1) {
						
							self.busy = false;
							
							var $metabox_li = $('<li class="customize-control"><div class="sm-mb-nav-menu-loading sm-mb-visible"></div></li>').insertAfter($li);
							
							var menu_id = parseInt($li.attr('id').match(/\d+/)[0]);
								
							self.renderMetabox(menu_id, function(html) {
								
								$metabox_li.html($(html));
								
								self.initTrigger();
								self.initMetaboxEvents();
	
							});
							
						}
					}
				
				}else{
					
					self.busy = false;
					self.initTrigger();
				}
				
				if(!self.busy) {
					clearInterval(interval);
				}
			
			}, 300);
		},
		
		initEvents: function() {
		
			var self = this;

		    if(typeof(wp) !== 'undefined' && typeof(wp.customize) !== 'undefined') {	
				
				self.refreshVars(function() {

					$('.control-section-nav_menu h3').on('click', function() {
			
						self.openCustomizerMenuEditor();
	
					});
									
					setTimeout(function() {
					
						$(document).on('DOMNodeInserted', function(event) {
						            
						     if(
						     	event.relatedNode.id === 'menu-to-edit' && 
						     	event.target.nodeName === 'LI' && 
						     	$(event.target).hasClass('menu-item') &&
						     	!$(event.target).hasClass('sm-mb-nav-menu-loaded'))  
						     { 
						     	self.insertTrigger($(event.target));
						     }
						});
					
					}, 2000);
																	
					var onCustomizerSave = function() {
						
						setTimeout(function() {
							
							self.refreshVars(function() {
								
								var menu_id;
								
								if($('#menu-to-edit .sm-mb-nav-menu-preview').length > 0) {
									menu_id = $('#menu-to-edit .sm-mb-nav-menu-preview').data('id');
								}else{
									menu_id = self.SlickMenu.getOpen();
								}
									
								if(menu_id) {
									
									if(self.SlickMenu.isCreated(menu_id)) { 
										
										self.SlickMenu.reload(menu_id);
										
									}else{
										
										var el = self.customizer.iframeDoc.find('#sm-menu-'+menu_id);
						
										if(el.length === 0 || $('#menu-to-edit .menu-item').length === 0) {
											
											wp.customize.previewer.refresh();
											
										}else{
											
											self.SlickMenu.reload(menu_id);
										}	
						
									}
								}	
				                
			                });
		                
						}, 1000);
					};
					
					$('#save').off('click', onCustomizerSave);
					$('#save').on('click', onCustomizerSave);
					
					
						
					// Open current menu via query arg				
					var currentMenu = self.getQueryParam(location.href, 'menu');
				
					if(currentMenu) {
						var currentMenuLi = $("#accordion-section-nav_menu\\["+currentMenu+"\\] h3")
						
						if(currentMenuLi.length) {
							currentMenuLi.click();
						}
					}
				});
				
			}else{
				
				self.initMetaboxEvents();
				self.initTrigger();
		
			    $(document).ajaxComplete(function() {
				    setTimeout(function() {
						self.initTrigger();
					}, 1000);
			    });
			 
			}
			
			
			$(document).on('click', function(e) {

			    if($(e.target).parent().hasClass('sm-rwmb-label')) {
			
			        e.preventDefault();
			        e.stopPropagation();
			    }
			});
 
		},
		
		initMetaboxEvents: function() {

            var self = this;
            
            var enableEvent = function(e) {
				
                e.preventDefault();
				
				var $button = $(this);
				var $box = $(e.target).closest('#sm-mb-nav-menu-metabox-div');

				if($button.hasClass('loading')) {
					return false;
				}
				
				$box.html('<div class="sm-mb-nav-menu-loading sm-mb-visible"></div>');
				
				var id = $button.data('id')
				self.menuActivation(id, 1, function(html) {
					$button.removeClass('loading');
					
					var $content = $(html);
					
					if(typeof(wp.customize) === 'undefined') {
						$content.find('.sm-mb-preview-row').remove();
					}
					
					$box.replaceWith($content);
					
					self.initMetaboxEvents();
					
					if(wp.customize) {	
						wp.customize.previewer.refresh();
					}
				});

            };
            
            var disableEvent = function(e) {
				
                e.preventDefault();
				
				var $button = $(this);
				var $box = $(e.target).closest('#sm-mb-nav-menu-metabox-div');
				
				if($button.hasClass('loading')) {
					return false;
				}
				
				$box.html('<div class="sm-mb-nav-menu-loading sm-mb-visible"></div>');
				
				var id = $button.data('id')
				self.menuActivation(id, 0, function(html) {
					$button.removeClass('loading');
					
					$box.replaceWith($(html));
					
					self.initMetaboxEvents();
					
					if(wp.customize) {	
						wp.customize.previewer.refresh();
					}	
					
				});

            };
            
            var launchEvent = function(e) {

                e.preventDefault();
                
				if($(this).hasClass('loading')) {
					return false;
				}
		
				$(this).addClass('loading');
				
                var depth = 0;
                var type = '';
                var title = $(this).html();
                var id = $(this).data('id');

                var panel = $(this).mbNavMenu({
                    type: type,
                    menu_id: id,
                    item_id: null,
                    title: title,
                    depth: depth,
                    load_only: true
                }, self.vars);
                
                SM_MB_NAV_PANELS[panel.id] = panel;

            };
            
            var previewEvent = function(e) {
				
                e.preventDefault();
                
                var $this = $(this);
         
                self.refreshVars(function() {
	                
	                var menu_id = $this.data('id');
	        
	                if(self.SlickMenu.isCreated(menu_id)) {
		                
						self.SlickMenu.toggle(menu_id);
						
					}else{
						
						self.SlickMenu.reload(menu_id);
					}
		
                });

            };
            

            $('.nav-menus-php #sm-mb-nav-menu-metabox-div .sm-mb-nav-menu-enable, #menu-to-edit #sm-mb-nav-menu-metabox-div .sm-mb-nav-menu-enable').off('click', enableEvent);
            $('.nav-menus-php #sm-mb-nav-menu-metabox-div .sm-mb-nav-menu-enable, #menu-to-edit #sm-mb-nav-menu-metabox-div .sm-mb-nav-menu-enable').on('click', enableEvent);
            
            $('.nav-menus-php #sm-mb-nav-menu-metabox-div .sm-mb-nav-menu-disable, #menu-to-edit #sm-mb-nav-menu-metabox-div .sm-mb-nav-menu-disable').off('click', disableEvent);
            $('.nav-menus-php #sm-mb-nav-menu-metabox-div .sm-mb-nav-menu-disable, #menu-to-edit #sm-mb-nav-menu-metabox-div .sm-mb-nav-menu-disable').on('click', disableEvent);
            
			$('.nav-menus-php #sm-mb-nav-menu-metabox-div .sm-mb-nav-menu-launch, #menu-to-edit #sm-mb-nav-menu-metabox-div .sm-mb-nav-menu-launch').off('click', launchEvent);            			
			$('.nav-menus-php #sm-mb-nav-menu-metabox-div .sm-mb-nav-menu-launch, #menu-to-edit #sm-mb-nav-menu-metabox-div .sm-mb-nav-menu-launch').on('click', launchEvent);
			
			setTimeout(function() {
				$('.nav-menus-php #sm-mb-nav-menu-metabox-div .sm-mb-nav-menu-launch, #menu-to-edit #sm-mb-nav-menu-metabox-div .sm-mb-nav-menu-launch').trigger('click'); 
			}, 50);
			
			if(wp.customize) {	
				
				$('#menu-to-edit #sm-mb-nav-menu-metabox-div .sm-mb-nav-menu-preview').off('click');            			
				$('#menu-to-edit #sm-mb-nav-menu-metabox-div .sm-mb-nav-menu-preview').on('click', previewEvent); 	
						
			}else{
				
				$('.nav-menus-php #sm-mb-nav-menu-metabox-div').find('.sm-mb-preview-row').remove();
			}
			
			self.initPluginMetaboxes();           			
			
		},
        		
		initTrigger: function() {
			
			var self = this;

		    $('#menu-to-edit li.menu-item:not(.sm-mb-nav-menu-loaded)').each(function() {
		
		        var menu_item = $(this);
		        
		        self.insertTrigger(menu_item);
		    });

		},
		
		insertTrigger: function(menu_item) {
			
			var self = this;

			menu_item.addClass('sm-mb-nav-menu-loaded');
		        
	        var menu_id;
	        
	        if(wp.customize) {
		        
		        wp.customize.section.each(function(section) {
				    if(section.expanded()) {
				        menu_id = section.params.menu_id;
				    }
				});

	        }else{
		        menu_id = $('input#menu').val();
	        }
	        
	        var title = menu_item.find('.menu-item-title').text();
	        var type =  menu_item.find('.item-type').text();
	  
	        if ( ! title ) {
	            title = menu_item.find('.item-title').text();
	        }

	        var item_id = parseInt(menu_item.attr('id').match(/[0-9]+/)[0], 10);
			var buttonContent = '<span class="sm-spinner"><div class="bounce1"></div><div class="bounce2"></div><div class="bounce3"></div></span>' + self.vars.title;
							
	        var button = $("<span>").addClass("sm-mb-nav-menu-launch").html(buttonContent)
            .on('click', function(e) {
                e.preventDefault();
				if($(this).hasClass('loading')) {
					return false;
				}
				
				$(this).addClass('loading');
				
                var depth = menu_item.attr('class').match(/\menu-item-depth-(\d+)\b/)[1];

                var panel = $(this).mbNavMenu({
                    type: type,
                    menu_id: menu_id,
                    item_id: item_id,
                    title: title,
                    depth: depth,
                }, self.vars);
                
                SM_MB_NAV_PANELS[panel.id] = panel;
            });

			if(wp.customize) {
	        	$('.menu-item-bar', menu_item).append(button);
	        }else{
	        	$('.item-title', menu_item).append(button);
	        }	
		},
		
		renderMetabox: function(id, callback) {
			
			var self = this;
			
			$.ajax({
			    url: ajaxurl,
			    type: 'post',
			    dataType: 'html',
			    data: { 
				    action: 'sm_mb_nav_menu_metabox', 
				    wpnonce: SM_MB_NAV_MENU_VARS.metabox_nonce, 
				    id: id 
				}
			})
			// using the done promise callback
            .done(function(data) {
				
			    if(data === 'invalid nonce') {
			    	location.reload();
			    }else{ 
					callback(data); 
				}
					
            })
			.fail(function() {
	            
	            self.ajax_errors++;
	            
	            if(self.ajax_errors < 3) {
		            
	            	self.renderMetabox(id, callback);
	            	
	            }else{
		            
		           location.reload(); 
	            }
			}); 
		
		}, 
				
		menuActivation: function(id, flag, callback) {
		
			var self = this;
			
			$.ajax({
			    url: ajaxurl,
			    type: 'post',
			    dataType: 'html',
			    data: { 
				    action: 'sm_mb_nav_menu_activation', 
				    wpnonce: SM_MB_NAV_MENU_VARS.activation_nonce, 
				    flag: flag,
				    id: id 
				},
				statusCode: {
			        502: function () {
				        
						self.ajax_errors++;
	            
			            if(self.ajax_errors < 3) {
				            
			            	self.menuActivation(id, flag, callback);
			            	
			            }else{
				            
				           location.reload(); 
			            }
			        }
			    }
			})
			// using the done promise callback
            .done(function(data) {
				
			    if(data === 'invalid nonce') {
			    	location.reload();
			    }else{ 
				    $(document).trigger('sm_mb_metabox_rendered');
					callback(data); 
				}
					
            })
			.fail(function() {
	            
	            self.ajax_errors++;
	            
	            if(self.ajax_errors < 3) {
		            
	            	self.menuActivation(id, flag, callback);
	            	
	            }else{
		            
		           location.reload(); 
	            }
			}); 
		
		},
		
		initPluginMetaboxes: function() {
			
			if(!wp.customize) {	
				
				$('#menu-settings-column ul').find('li[class*="sm-metabox-"]').each(function() {
					$(this).insertAfter($('#slick-menu-metabox-nav-menu-global'));
				});
				
			}
				
			if($('#sm-mb-nav-menu-metabox-div .sm-mb-nav-menu-launch').length > 0) {
				$('body').addClass('slick-menu-active');
				if(!wp.customize) {	
					$('#menu-settings-column ul').find('li[class*="sm-metabox-"]').slideDown();
				}
			}else{
				$('body').removeClass('slick-menu-active');
				if(!wp.customize) {	
					$('#menu-settings-column ul').find('li[class*="sm-metabox-"]').slideUp();
				}
			}
					
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
		},
		
		getQueryParam: function (uri, key) {
			
		    if (!uri) {
				return false;
			} 
			
		    key = key.replace(/[\[\]]/g, "\\$&");
		    var regex = new RegExp("[?&]" + key + "(=([^&#]*)|&|#|$)"),
		        results = regex.exec(uri);
		    if (!results) return null;
		    if (!results[2]) return '';
		    return decodeURIComponent(results[2].replace(/\+/g, " "));
		}	
	};



	var SM_MB_NAV_PANEL = {
		
		_init: false,
		
	    classname: 'sm-mb-nav-menu-edit',
	    
		selector: function(suffix) {
	      	
	    	return '.' + this.classname + ' ' + suffix;
	    },
	    panelEl: function() {
		    return $('.' + this.classname);
	    },	
		activePanelEl: function() {
	      	
	      	return $('.' + this.classname + '.loaded');
	    },	
	    activePanel: function() {
		    
		    var panelEl = this.activePanelEl();
		    if(panelEl.length > 0) {
			    
		    	return SM_MB_NAV_PANELS[panelEl.attr('id')];
		    }
		    
		    return null;	
	    },
	    init: function() {
		    
		    if(!this._init) {
		    	this.initEvents();
		    	this._init = true;
		    }	
	    },
		initEvents: function() {
			
			var self = this;

	      	$(window).on('resize', function() {
		      	setTimeout(function() {
			      		
			      	if(self.activePanelEl().length > 0) {	
				      	self.activePanelEl().find('.sm-mb-metabox-holder.sm-mb-active .sm-mb-metabox-title').each(function() {
					      	
					      	var $this = $(this);
						    var $holder = $this.closest('.sm-mb-metabox-holder');
						    var width = $holder.outerWidth(true);
						    $this.css('width', (width + 1)+'px');
						    
				      	});
				    }	
		      	},2);
	      	});
    	
		   	$( document ).on( 'click', self.selector('.sm-mb-metabox-title'), function() {
				    
			    var $this = $(this);
			    var $wrap = $this.closest('.sm-mb-metabox-wrap');
			    var $holder = $this.closest('.sm-mb-metabox-holder');
			    var width = $holder.outerWidth(true);
			    
			    if($holder.hasClass('sm-mb-active')) {
				    $holder.removeClass('sm-mb-active');
				    $wrap.removeClass('sm-mb-active');
				    $this.css('width', '');
			    }else{
				    $holder.addClass('sm-mb-active');
				    $wrap.addClass('sm-mb-active');
				    $this.css('width', (width + 1)+'px');
				    $wrap.scrollTop(0);
			    }
		    });
		    
		   	$( document ).on( 'click', self.selector('.sm-mb-nav-menu-submit'), function(event) {
			 	
			 	event.preventDefault();
			 	
			 	var activePanel = self.activePanel();
			 	var activePanelEl = self.activePanelEl();
			 	
			 	if(activePanel === null) {
				 	return false;
			 	}
			 	
			 	var $button = $(this);
			 	var $form = $button.closest('form');
			 	var menu_id = $form.find('input[name="menu_id"]').val();
			 	var silent = $button.data('silent') === true;
			 	if($form.hasClass('submitting')) {
				 	return false;
			 	}
			 		 	
			 	$button.text($button.data('saving'));
		

			 	$form.addClass('submitting'); 
			 	
			 	activePanel.has_changes = false;
			 	activePanelEl.removeClass('sm-mb-unsaved');
			 	activePanelEl.find('sm-mb-unsaved').each(function() {
				 	$(this).removeClass('sm-mb-unsaved');
			 	});
			 	
			 	self.showLoading();
			 	
			 	if(wp.customize) {
			 		wp.customize.previewer.save();
			 	}
			 	
				activePanel.save(function(data) {
					
					setTimeout(function() {
					
		                $button.text ($button.data('saved')).blur();
		              
		                if(wp.customize) {
			                
			                if(!activePanel.mp_enabled) {
				                
				                wp.customize.previewer.refresh();
				                
			                }else{
								
								activePanel.parent.refreshVars(function() {
									
									// Reload Menu and apply new css changes for this particular menu
									var styleId = 'slick-menu-dynamic-'+menu_id+'-css';
									var $styleLink =  activePanel.parent.customizer.iframeDoc.find('head').find('#'+styleId);
									var styleUrl = $styleLink.attr('href');
									var closeAnimationSpeed = parseInt(activePanel.parent.SlickMenu.getOption(menu_id, 'menu-close-duration'));	
						
									setTimeout(function() {
										if(styleUrl && styleUrl.length > 0) {
											$styleLink.attr('href', activePanel.parent.updateQueryParam(styleUrl, 't', Date.now()));
										}
									}, closeAnimationSpeed);
																			
									activePanel.parent.SlickMenu.reload(menu_id, function() {
							
										activePanel.parent.slickMenuReloaded(menu_id);
										$button.removeData('silent');
										
									}, silent);

				                });
							}
						}
							
		                self.hideLoading();
		                
		                setTimeout(function() {
			                $form.removeClass('submitting'); 
			              	$button.text($button.data('submitting'));
		                }, 2000);
		               	
		               	activePanelEl.trigger('sm_mb_nav_menu_saved', [data]);
		               	$(document).trigger('sm_mb_nav_menu_saved', [activePanelEl, data]);
		               	 
					}, 1000);
				});
		
		    });

		    
		   	$( document ).on( 'click', self.selector('.sm-mb-nav-menu-preview'), function(e) {
				
				e.preventDefault();
				
				$('#menu-to-edit #sm-mb-nav-menu-metabox-div .sm-mb-nav-menu-preview').trigger('click');

			});	
				    
		   	$( document ).on( 'click', self.selector('.sm-mb-nav-menu-reset'), function(e) {
				
				e.preventDefault();
				
				var activePanel = self.activePanel();
			 	var activePanelEl = self.activePanelEl();
			 	
			 	if(activePanel === null) {
				 	return false;
			 	}
			 	
				var confirmResult = confirm(SM_VARS.lang.reset_all_confirm);
				if (confirmResult !== true) {
				    return false;
				} 

				var $mb_wrap = activePanelEl.find('form');
				var fields = [];
				
				self.showLoading();

				setTimeout(function() {
					$mb_wrap.find('.slick-menu-reset-field').each(function(i) {
	
						$(this).trigger('click');
		
					});
				},200);
				
				setTimeout(function() {
					
					activePanelEl.trigger('sm_mb_nav_menu_save');
					self.hideLoading();
					
				}, 1000);

			});
			
		   	$( document ).on( 'click', self.selector('.slick-menu-reset-group'), function(e) {
				
				e.preventDefault();

				var activePanel = self.activePanel();
			 	var activePanelEl = self.activePanelEl();
			 	
			 	if(activePanel === null) {
				 	return false;
			 	}
			 					
				var $group = $(this).closest('.sm-rwmb-group-wrapper');
				var group_name = $group.find(' > .sm-rwmb-label label').html();
				var confirm_msg = SM_VARS.lang.reset_group_confirm.replace('{group_name}', group_name);
				
				var confirmResult = confirm(confirm_msg);
				if (confirmResult !== true) {
				    return false;
				} 
				
				var fields = [];
				
				self.showLoading();

				setTimeout(function() {
					$group.find('.slick-menu-reset-field').each(function(i) {
	
						$(this).trigger('click');
		
					});
				},200);
				
				setTimeout(function() {
					
					activePanelEl.trigger('sm_mb_nav_menu_save');
					self.hideLoading();
					
				}, 1000);

			});
			
		   	$( document ).on( 'click', self.selector('.slick-menu-reset-field'), function(e) {
				
				e.preventDefault();

				var activePanel = self.activePanel();
			 	var activePanelEl = self.activePanelEl();
			 	
			 	if(activePanel === null) {
				 	return false;
			 	}
			 						
				var $this = $(this);	
				
				self.showLoading();
				
				var $mb_wrap =  $this.closest('form');

				var field_std = $this.data('std');
				var field_std_type = $this.data('type');
				var field_id = $this.data('id');
				var group = $this.closest('.sm-rwmb-group-wrapper');
				var in_group = group.length;
				var target_id = field_id;
				if(in_group) {
					target_id = group.find('>.sm-rwmb-input').data('id') + '['+target_id+']';
				}

				var target_item = $mb_wrap.find('input[name="target_item"]').val();
				var post_id;
				
				if(target_item === "1") {
					post_id = $mb_wrap.find('input[name="item_id"]').val();
				}else{
					post_id = $mb_wrap.find('input[name="menu_id"]').val();
				}

				field_id = field_id.replace(SM_VARS.field_prefix, '');

				activePanel.resetField(target_id, field_std, field_std_type);
				
				setTimeout(function(){
					activePanelEl.trigger('sm_mb_nav_menu_save');
					self.hideLoading();
				}, 500);
		
			});
				    
		   	$( document ).on( 'sm_mb_nav_menu_save', self.selector(''), function() {
			    $(this).find('.sm-mb-nav-menu-submit').trigger('click');
		    });

	    			
			if(wp.customize) {
				
		   		$( document ).on( 'click', '#menu-to-edit .customize-section-back', function() {
					$.fn.mbNavMenu('close');
				});
		   		$( document ).on( 'click', self.selector('.customize-section-back'), function(e) {
					e.preventDefault();
					$.fn.mbNavMenu('close');
				});
				
			}else{	
				
	            $(document).on('click', "#wpcontent", function(e) {
	             
	                if(!$(e.target).hasClass('sm-mb-nav-menu-launch')) {
		                $("#wpcontent").off('click');
	                	$.fn.mbNavMenu('close');
	                	
	                }
	            });	
            }
            
		   	$( document ).on( 'click', self.selector('form#sm-mb-nav-menu-metaboxes .submit .sm-mb-nav-menu-back'), function(e) {
			    e.preventDefault();
			    $.fn.mbNavMenu('close');
			});
			
		
			var clone_instance = function(e, nextIndex) {

				$(e.target).find('.ipf').each(function() {
					
					var iconWrap = $(this);
					var field = iconWrap.find('.ipf-icon');
					field.val('');
					
					iconWrap.trigger('ipf:update');
					
				});
				
				$(e.target).find('.sm-rwmb-rgba').trigger('clone');
			};			
			
			// Reset Icons on field clone
			$(document).on('clone_instance', self.selector('.sm-rwmb-clone'), clone_instance);
			
			
			var sm_metabox_rendered = function() {
				
				var activePanel = self.activePanel();
			 	var activePanelEl = self.activePanelEl();
			 	
				if(activePanel) {
					activePanel.saveImageFields();
					activePanelEl.data('reload', true);
				}	
			};
			
			$(document).on('sm_mb_metabox_rendered', sm_metabox_rendered);

        },
        
        showLoading: function() {
	        
	        this.activePanel().showLoading();

        },
        
        hideLoading: function() {
	        
	        this.activePanel().hideLoading();
        }
    
    };

	$(document).ready(function() {
		
		SM_MB_NAV_MENU.init();	
		SM_MB_NAV_PANEL.init();
	});

	setTimeout(function() {
		
		SM_MB_NAV_MENU.init();	
		SM_MB_NAV_PANEL.init();	
		
	}, 2000);
	
	window.SM_MB_NAV_MENU = SM_MB_NAV_MENU;
	window.SM_MB_NAV_PANEL = SM_MB_NAV_PANEL;
	window.SM_MB_NAV_PANELS = SM_MB_NAV_PANELS;
			
} )( window, jQuery );	
