jQuery( document ).ready(function( $ ) {

	var SM_MB_Field = {};
	 
	SM_MB_Field.init = function() {

		var self = this;
		
		self.initEvents();

	};
	
	SM_MB_Field.initEvents = function() {	
	
		var animateHamburger = function(e) {
			var burger = $(this);
			var parent = burger.parent();
			
			if(parent.hasClass('sm-rwmb-active') && e.type !== 'click') {
				return false;
			}
			
			if(!burger.hasClass('is-active')) {
				burger.addClass('is-active');	
			}else{
				burger.removeClass('is-active');	
			}	
		};


		var onReverseToggle = function() {
			
			var $this = $(this);
			
			var $group = $this.closest('.sm-rwmb-group-wrapper');
			var hamburger = $group.find('.sm-rwmb-hamburger-select.sm-rwmb-active:not(.sm-rwmb-hamburger-empty) .sm-hamburger');
			var reverse = $this.is(':checked');
			var regular_class = hamburger.data('regular');
			var reverse_class = hamburger.data('reverse');

			if(reverse) {
				hamburger.removeClass(regular_class);
				hamburger.addClass(reverse_class);
			}else{
				hamburger.removeClass(reverse_class);
				hamburger.addClass(regular_class);
			}			
		};
	

		var activateWrapper = function(e) {
			
			var $this = $(this);

			setTimeout(function() {
				
				var $all = $this.find('.sm-rwmb-hamburger-select');
				var $active = $this.find('.sm-rwmb-hamburger-select.sm-rwmb-active:not(.sm-rwmb-hamburger-empty)');
				
			    if($active.length) {
					
					if(!$this.hasClass('sm-rwmb-active')) {
						
						var $group = $this.closest('.sm-rwmb-group-wrapper');
						
						var name = $active.find('.name').html();
						
			        	$this.addClass('sm-rwmb-active');
			        	$this.append('<span class="sm-rwmb-hamburger-remove">'+name+' <a href="#">Remove</a></span>');
						$active.trigger('sm_rwmb_hamburger_active', [$this]);
						
						$this.find('.sm-rwmb-hamburger-remove a').off('click', deactivateHamburger);
						$this.find('.sm-rwmb-hamburger-remove a').on('click', deactivateHamburger);
						
						$group.find('input#reverse').off('change', onReverseToggle);
						$group.find('input#reverse').on('change', onReverseToggle).trigger('change');

			        }
			
			    }else{
			
			        $this.removeClass('sm-rwmb-active');
			        $this.find('.sm-rwmb-hamburger-remove').remove(); 
			        $all.trigger('sm_rwmb_hamburger_inactive', [$this]);
			    }
			    
		    }, 20);
		};
		
		var deactivateHamburger = function() {
			
			$(this).closest('.sm-rwmb-hamburger-select-wrapper').find('.sm-rwmb-hamburger-empty').trigger('click');
		};


		$(document).on('sm_mb_init_events', function(e, data) {
			
			$(data.target).find('.sm-rwmb-hamburger-select-wrapper').trigger('click');
		});	
		
		$(document).on('click mouseenter mouseleave', '.sm-rwmb-hamburger-select .sm-hamburger', animateHamburger);		
		$(document).on('click', '.sm-rwmb-hamburger-select-wrapper', activateWrapper);
		$('.sm-rwmb-hamburger-select-wrapper').trigger('click');		
	};
	
	SM_MB_Field.init();	
	 
});
