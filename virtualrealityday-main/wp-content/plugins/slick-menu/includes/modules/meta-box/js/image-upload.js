window.rwmb = window.rwmb || {};

jQuery( function ( $ )
{
	'use strict';


	var SM_MB_Field = {};
	 
	SM_MB_Field.init = function() {

		var self = this;
		
		self.initEvents();

	};
	
	SM_MB_Field.initEvents = function() {	
			
		var views = rwmb.views = rwmb.views || {},
			ImageField = views.ImageField,
			ImageUploadField,
			UploadButton = views.UploadButton;
	
		ImageUploadField = views.ImageUploadField = ImageField.extend( {
			createAddButton: function ()
			{
				this.addButton = new UploadButton( { collection: this.collection, props: this.props } );
			}
		} );
	
		/**
		 * Initialize fields
		 * @return void
		 */
		function init()
		{
			new ImageUploadField( { input: this, el: $( this ).siblings( 'div.sm-rwmb-media-view' ) } );
			console.log('win');
		}
		
		
		$(document).on('sm_mb_init_events', function(e, data) {
			
			$(data.target).find( ':input.sm-rwmb-image_upload, :input.sm-rwmb-plupload_image' ).each( init );
		});	
		
		$( ':input.sm-rwmb-image_upload, :input.sm-rwmb-plupload_image' ).each( init );
		$( document ).on( 'clone', '.sm-rwmb-input :input.sm-rwmb-image_upload, .sm-rwmb-input :input.sm-rwmb-plupload_image', init );
				
	};
	
	SM_MB_Field.init();	
			
} );
