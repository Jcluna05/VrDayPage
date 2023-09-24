;( function( $ ) {
		
	'use strict';
		
	$.fn.mbNavMenu = function (options, vars) {
			
		var panel = {};

		panel.parent = SM_MB_NAV_MENU;
		panel.trigger = $(this);
		panel.settings = options;
		panel.vars = vars;
		panel.id = 'sm-mb-nav-menu-'+((panel.settings.item_id !== null) ? panel.settings.item_id : 'global-'+panel.settings.menu_id);
		panel.classes = 'sm-mb-nav-menu-edit sm-mb-nav-menu'+((panel.settings.item_id !== null) ? panel.settings.item_id : '-global');
        panel.exists = $('#'+panel.id).length !== 0;
        panel.selector = '.sm-mb-nav-menu-edit';
        panel.has_changes = false;
        panel.load_errors = 0;
        panel.save_errors = 0;
						
		if(!panel.exists) {
			
			panel.el = $("<div />");
			panel.el.attr('id', panel.id);	
			panel.el.addClass(panel.classes);
			panel.el.data('reload', false);
			panel.el.data('has-loaded', false);
			panel.el.data('images', {});
			
		}else{
			panel.el = $('#'+panel.id);
		}

	    panel.init = function () {
		
			$.ajaxSetup({async:true});
			panel.container = wp.customize ? panel.trigger.closest('.control-section-nav_menu') : $('body');
			
			var winHeight = $(window).height();
			
			panel.close();
			
			if(!panel.exists) {
				
				var customizerBackButton = '';
				if(wp.customize) {
					customizerBackButton += '<button class="customize-section-back" tabindex="0">';
					customizerBackButton += '	<span class="screen-reader-text">Back</span>';
					customizerBackButton += '</button>';
				}
				
				panel.el.html('<div class="sm-mb-nav-menu-header">'+customizerBackButton+'<span class="sm-mb-nav-menu-title"></span> <span class="sm-mb-nav-menu-type"></span></div><div class="sm-mb-nav-menu-content"></div>');
				panel.el.appendTo(panel.container);
				
				panel.load(function(data) {
					
					var load_only = typeof(panel.settings.load_only) !== 'undefined' ? panel.settings.load_only : false;
					
					panel.el.find('.sm-mb-nav-menu-content').html($(data));
			
					panel.initEvents();
					panel.fixImageFields(); 
				    panel.loaded(load_only);
				});
				
			}else if(panel.el.data('reload') === true) {
				
				panel.load(function(data) {
					
					panel.el.find('.sm-mb-nav-menu-content').html($(data));
				    
				    panel.initEvents();
					panel.reloadImageFields();
				    panel.loaded();	
				});
					
			}else{
				
				panel.fixImageFields();
				panel.loaded();
			}	

        };
        
        
        panel.initEvents = function() {
	      	
	      	var hasChanges = function() {
		      	
		      	panel.has_changes = true; 
		        panel.el.addClass('sm-mb-unsaved');
		        $(this).closest('.sm-rwmb-field').addClass('sm-mb-unsaved');
		        
		        $(panel.el).trigger('sm_mb_nav_menu_has_changes', [$(this)]);
		        $(document).trigger('sm_mb_nav_menu_has_changes', [$(this), panel]);
	      	};
	      	
	      	$('form .sm-rwmb-slider', panel.el).on('slidechange', hasChanges);
	      	$('form input', panel.el).on('change', hasChanges);
		   	
		   	
		   	// Hide customizer preview button for always visible menus
		   	
		   	if(wp.customize) {
			   	
			   	var togglePreviewButton = function() {
					
					var field = panel.el.find('[name="'+panel.field_id('menu-always-visible')+'"]:checked');
		
					if(field.val() === "1") {
						panel.el.find('.sm-mb-nav-menu-preview').hide();
						
						$('[name="slick-menu-menu-always-visible"][value="1"]:checked').not(field).each(function() {
							$(this).closest('ul').find('[name="slick-menu-menu-always-visible"][value="0"]').trigger('click');
							$(this).closest('form').find('.sm-mb-nav-menu-submit').data('silent', true).trigger('click');
							$(this).closest('form').find('.sm-mb-nav-menu-preview').show();
							panel.el.find('.sm-mb-nav-menu-submit').trigger('click');
						});	

					}else{
						panel.el.find('.sm-mb-nav-menu-preview').show();
					}
			   	};
			   	
			   	$(panel.el).on('sm_mb_nav_menu_saved', function() {
					togglePreviewButton();
			   	});	
			   	
			   	togglePreviewButton();	
			   	
			   	$(document).trigger('sm_mb_panel_rendered', [panel]);
		   	}				

	
			// Fix submit bar, reinsert it within form. Caused by tinymce somehow.
			//$(panel.el).find('.sm-mb-nav-menu-content .submit').appendTo($(panel.el).find('#sm-mb-nav-menu-metaboxes'));
			
			
			// Switch animation list based on level animation type
			var $levelAnimationField = panel.el.find('[name="slick-menu-level-animation-type"]:checked');
			if($levelAnimationField.length) {
				var $animationsField = panel.el.find('#slick-menu-menu-animation-type');
				var animationType = $animationsField.val();
				$levelAnimationField.trigger('change');
				$animationsField.val(animationType);
			}


								
			// Toggle Clone Sort
			
			panel.el.find('.sm-rwmb-group-wrapper .sm-rwmb-sort-clone:first-child').each(function() {
				
				var $field = $(this).closest('.sm-rwmb-group-wrapper');
				var $label = $field.find('>.sm-rwmb-label');
				var $input = $field.find('>.sm-rwmb-input');
			
				var $sort = $label.find('.sm-rwmb-sort');
				
				if($sort.length === 0) {
					$sort = $('<a class="sm-rwmb-sort hide-if-no-js" href="#"><i class="dashicons dashicons-sort"></i>sort</a>');
					$label.append($sort);
				}
					
				$sort.off('click');
				$sort.on('click', function() {
					if($input.hasClass('sm-rwmb-sorting')) {
						$sort.removeClass('sm-rwmb-sorting');
						$input.removeClass('sm-rwmb-sorting');
					}else{
						$sort.addClass('sm-rwmb-sorting');
						$input.addClass('sm-rwmb-sorting');
					}	
				});

			});

			
			// Animation Field Preview
			
			var onAnimateChange = function() {
				
		        var $this = $(this);
		        var $wrap = $this.closest('.sm-rwmb-input');
		        var $preview = $wrap.find('.sm-mb-animation-preview');
		        var value = $this.val();
		        if(value === '') {
			        value = 'no-animation';
		        }else{
			        value = 'sm-'+value;
		        }
		        
		        if($preview.length === 0) {
		            $this.after('<span class="sm-mb-animation-preview sm-animated '+value+'" data-animation="'+value+'"></span>');
		        }else{
		            var animation = $preview.data('animation');
		            $preview.removeClass(animation);
		            $preview.addClass(value);
		            $preview.data('animation', value);
		        }				
			};
			
			panel.el.on('change', '.sm-mb-animate-field select', onAnimateChange).trigger('change');
			
						
			var onTransformsChange = function(el, sliderFields) {
				
				if($(el).is(':checked')) {
		            sliderFields.each(function() {
			            if($(this).attr('data-val') && $(this).attr('data-val') !== '') {
		                	$(this).val($(this).attr('data-val'));
		                }
		            });
		        }else{
		            sliderFields.each(function() {
			            if($(this).val() !== '') {
		                	$(this).attr('data-val', $(this).val()).val('');
		                }
		            });
		        }
			}
			setTimeout(function() {
				
				panel.el.find('.sm-rwmb-group-wrapper > .sm-rwmb-input[data-id*="-transforms"]').find('#enabled').each(function() {
					var wrapper = $(this).closest('.sm-rwmb-group-wrapper');
				    var sliderFields = wrapper.find('.sm-rwmb-slider-value');
				    $(this).on('change', function() {
				        
				        onTransformsChange(this, sliderFields);
				        
				    });
					
					if(!$(this).is(':checked')) {
						 onTransformsChange(this, sliderFields);
					}
				});
				
			},200);


			$('div.ipf').trigger('ipf:update');

			$(document).trigger('sm_mb_init_events', {target: panel.el});
			
			$(window).trigger('resize');

        },

        
        panel.load = function(callback) {
	        	        
	      	$.ajax({
			    url: ajaxurl,
			    type: 'post',
			    dataType: 'html',
			    data: { 
				    action: 'sm_mb_nav_menu_load', 
				    wpnonce: panel.vars.editor_load_nonce, 
				    menu_id: panel.settings.menu_id,
				    item_id: panel.settings.item_id,
				    depth: panel.settings.depth,
				    customizer: wp.customize ? 1 : 0
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
	            
	            panel.load_errors++;
	            
	            if(panel.load_errors < 3) {
		            
	            	panel.load(callback);
	            	
	            }else{
		            
		           location.reload(); 
	            }
			}); 
        };
        
        panel.loaded = function(load_only) {
	        
	        load_only = (typeof(load_only) !== 'undefined' && load_only) ? true : false;
	        
	        panel.el.find('.sm-mb-nav-menu-header .sm-mb-nav-menu-title').html(panel.settings.title);
			panel.el.find('.sm-mb-nav-menu-header .sm-mb-nav-menu-type').html(panel.settings.type);
			
			if(!load_only || panel.el.data('has-loaded')) {
	
				panel.el.addClass('loaded');
				$('body').removeClass('sm-mb-nav-menu-inactive');
				$('body').addClass('sm-mb-nav-menu-active');
				
				if(wp.customize) {
					$('.wp-full-overlay-sidebar-content').scrollTop(0);
				}	
					                
			}else{
				
				if(wp.customize && !panel.parent.SlickMenu.isOpen(panel.settings.menu_id)) {
			    
					panel.parent.SlickMenu.open(panel.settings.menu_id);
				}
			}
			
			panel.trigger.removeClass('loading').addClass('loaded').closest('.menu-item').addClass('sm-mb-active');
				
			panel.mp_enabled = Boolean(parseInt(panel.el.find('input[name="mp_enabled"]').val()));
			

		    if(panel.el.find('.sm-mb-metabox-holder').length === 1) {
		    	panel.el.find('.sm-mb-metabox-holder').first().addClass('sm-mb-active');
		    }	
		    			
			if(wp.customize) {
				
		    	$('.wp-customizer.sm-mb-nav-menu-active #menu-to-edit').scrollTop(0);
		    }
		    
		    panel.el.data('reload', false);
		    panel.el.data('has-loaded', true);

        };
 
        panel.save = function(callback) {
	        
	        var $form = panel.el.find('form');
	        var formData = $form.serialize();
	        
/*
	        if(typeof(tinyMCE) !== 'undefined') {
	        	tinyMCE.triggerSave();
	        }
*/

	      	$.ajax({
	            url         : ajaxurl, // the url where we want to POST
	            type        : 'POST', // define the type of HTTP verb we want to use (POST for our form)
	            data        : formData, // our data object
	            dataType    : 'json' // what type of data do we expect back from the server
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
	            
	            panel.save_errors++;
	            
	            if(panel.save_errors < 3) {
		            
	            	panel.save(callback);
	            	
	            }else{
		            
		           location.reload(); 
	            }
			})       
        };
 
 		
		panel.resetField = function(target, std, std_type) {
			
			var field;
		
			var _resetField = function(field, std) {

				if(field.hasClass('sm-rwmb-media-input')) {
					field.parent().find('.sm-rwmb-remove-media').trigger('click');
				}
				
				if(field.is(':checkbox')) {
					
					if(std === 1) {
						field.attr('checked', true);
					}else{
						field.attr('checked', false);
					}
					
				}else if(field.is(':radio')) {
					
					if(std !== '') {
						field.filter('[value="'+std+'"]').attr('checked', true);	
					}else{
						field.attr('checked', false);
					}	
					
				}else{
					
					field.val(std);

					if(field.hasClass('ipf-icon')) {
						
						var iconWrap = field.closest('.ipf');
						iconWrap.trigger('ipf:update');
						
					}else if(field.hasClass('sm-rwmb-rgba')) {
						
						field.trigger('keyup');
						
					}else if(field.hasClass('sm-rwmb-slider-value')) {
						
						field.parent().find('.sm-rwmb-slider').slider( "value", std );
						
					}else if(field.hasClass('sm-rwmb-autocomplete')) {
						
						field.parent().find('.sm-rwmb-autocomplete-result .actions').each(function() {
							
							$(this).trigger('click');
							
						});
					}
			
				}
				
				field.trigger('change');
				
				field.closest('.sm-rwmb-field').removeClass('slick-menu-highlight').addClass('slick-menu-highlight');
						
			}

			// if default value is object
			if(std_type === 'object') {
			
				var count = 0;
				var _target;
				for (var k in std){
					
					_target = panel.el.find('input[name="'+target+'['+k+']"]');
					panel.resetField(_target, std[k], 'string');
					count++;
				}
				
				if(count == 0) {
					panel.resetField(false, '', 'string');
				}
				
			// if default value is string	
			}else{
				
				if(target !== false) {
					
					if(typeof(target) === 'object') {
						field = target;
					}else{
						field = panel.el.find('input[name="'+target+'"]');
						
						if(field.length === 0) {
							field = panel.el.find('#'+target);
						}
					}	
					_resetField(field, std);
				}
			}	
			
		};

        panel.showLoading = function() {
	        
	        panel.el.find('form').addClass('loading');
			panel.el.find('form + .sm-mb-nav-menu-loading').addClass('sm-mb-visible');
        };
        
        panel.hideLoading = function() {
	        
	        panel.el.find('form').removeClass('loading');
			panel.el.find('form + .sm-mb-nav-menu-loading').removeClass('sm-mb-visible');
        };
        		              	
		panel.close = function() {
			$(panel.selector).find('.sm-mb-metabox-wrap.sm-mb-active').removeClass('sm-mb-active').find('.sm-mb-metabox-holder.sm-mb-active').removeClass('sm-mb-active');
			$(panel.selector).removeClass('loaded');
			$('.menu-item').removeClass('sm-mb-active');
			$('body').addClass('sm-mb-nav-menu-inactive');
			$('body').removeClass('sm-mb-nav-menu-active');
		};
		
		
		// TMP UGLY FIX, image fields not appearing if other field exists with the same value.
		// Fix by copying the other field over.
		
		panel.fixImageFields = function() {
						
			panel.el.find('.sm-rwmb-image_advanced').each(function() {
				
				var $input = $(this);
				var $field = $input.closest('.sm-rwmb-input');
				var val = $input.val();
				
				var $existing_input = $('.sm-mb-nav-menu-edit').not(panel.el).find('.sm-rwmb-image_advanced[value="'+val+'"]').first();
				if($existing_input.length > 0) {
					var $existing_field = $existing_input.closest('.sm-rwmb-input');
					$field.replaceWith($existing_field);
				}
				
			});
		};

		panel.saveImageFields = function() {
			
			var $input, $field, image_id;
			
			var images = panel.el.data('images');
			 
			if(images) {
				panel.el.find('.sm-rwmb-image_advanced').each(function() {
					
					$input = $(this);
					$field = $input.closest('.sm-rwmb-field');
					image_id = $input.attr('name');
					
					if(typeof(images[image_id]) !== 'undefined') {
						
						images[image_id] = $field.detach();
					}	
					
				});
				
				panel.el.data('images', images);
			}
		};
		
		panel.reloadImageFields = function() {
			
			var $input, $field, image_id;
			
			var images = panel.el.data('images');
			
			if(images) {
				panel.el.find('.sm-rwmb-image_advanced').each(function() {
					
					$input = $(this);
					$field = $input.closest('.sm-rwmb-field');
					
					image_id = $input.attr('name');
					
					if(typeof(images[image_id]) !== 'undefined') {
	
						$field.replaceWith($(images[image_id]));
					}	
					
				});
			}
		};
		
		panel.field_id = function(id) {
			
			return SM_VARS.field_prefix + id;
		};
		
						
		if(options === 'close') {
			
			panel.close();

		}else{
			
			panel.init();
		}
		
		return panel;

    };

} )( jQuery );	
	
