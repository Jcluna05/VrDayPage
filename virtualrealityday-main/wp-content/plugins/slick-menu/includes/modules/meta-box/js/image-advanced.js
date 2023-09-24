window.rwmb = window.rwmb || {};

jQuery( function ( $ )
{
	'use strict';

	var views = rwmb.views = rwmb.views || {},
		MediaField = views.MediaField,
		MediaItem = views.MediaItem,
		MediaList = views.MediaList,
		ImageField, ImageList, ImageItem;

	ImageField = views.ImageField = MediaField.extend( {
		createList: function ()
		{
			this.list = new MediaList( { collection: this.collection, props: this.props, itemView: ImageItem } );
		}
	} );

	ImageItem = views.ImageItem = MediaItem.extend( {
		className: 'sm-rwmb-image-item',
		template : wp.template( 'sm-rwmb-image-item' )
	} );


	var SM_MB_Field = {};
	 
	SM_MB_Field.init = function() {

		var self = this;
		
		self.initEvents();

	};
	
	SM_MB_Field.initEvents = function() {	

		/**
		 * Initialize image fields
		 * @return void
		 */
		function initImageField()
		{
			if($(this).hasClass('loaded')) {
				return false;
			}
			$(this).addClass('loaded');

			new ImageField( { input: this, el: $( this ).siblings( 'div.sm-rwmb-media-view' ) } );
		}
		
		
		$(document).on('sm_mb_init_events', function(e, data) {
			
			$(data.target).find( 'input.sm-rwmb-image_advanced:not(.loaded)' ).each( initImageField );
		});
				
		$( 'input.sm-rwmb-image_advanced:not(.loaded)' ).each( initImageField );
		$( document ).on( 'clone', '.sm-rwmb-input input.sm-rwmb-image_advanced', initImageField )
		
	};
	
	SM_MB_Field.init();	

} );
