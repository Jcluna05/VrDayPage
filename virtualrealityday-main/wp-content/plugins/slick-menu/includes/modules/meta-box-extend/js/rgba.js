jQuery( document ).ready(function( $ ) {

	var SM_MB_Field = {};
	 
	SM_MB_Field.init = function() {

		var self = this;
		
		self.initEvents();
	
	};
	
	SM_MB_Field.initEvents = function() {	
		
		function rgb2hex(rgb){
			rgb = rgb.match(/^rgba?[\s+]?\([\s+]?(\d+)[\s+]?,[\s+]?(\d+)[\s+]?,[\s+]?(\d+)[\s+]?/i);
			return (rgb && rgb.length === 4) ? "#" +
			("0" + parseInt(rgb[1],10).toString(16)).slice(-2) +
			("0" + parseInt(rgb[2],10).toString(16)).slice(-2) +
			("0" + parseInt(rgb[3],10).toString(16)).slice(-2) : '';
		}

		function update() {
			
			var $this = $(this);
			var $parent = $this.closest('.sm-rwmb-input');
			
			$this.not('loaded').addClass('loaded').minicolors({
				opacity: true,
				format: 'rgb',
				swatches: [],
				change: function(value, opacity) {
				
					var color = rgb2hex(value);
			        $parent.find('.minicolors-opacity-slider').css('background-color', color);
			    }
			});
		}

		
		$(document).on('sm_mb_init_events', function(e, data) {
	
			$( data.target ).find( 'input.sm-rwmb-rgba' ).each( update );

		});	
		
		$( 'input.sm-rwmb-rgba' ).each( update );
		$( document ).on( 'clone', 'input.sm-rwmb-rgba', update );
		
	};
	
	SM_MB_Field.init();	
});

