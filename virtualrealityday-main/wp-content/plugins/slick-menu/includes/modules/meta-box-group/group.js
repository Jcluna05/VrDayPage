jQuery( function ( $ )
{
	'use strict';

	var SM_MB_Field = {};
	 
	SM_MB_Field.init = function() {

		var self = this;
		
		self.initEvents('#wpbody');
		
		$(document).on('sm_mb_init_events', function(e, data) {
			
			self.initEvents(data.target);
		});
		
	};
	
	SM_MB_Field.initEvents = function(target) {	
	
		var $wrapper = $( target );


		// CUSTOM BY GH 
		
		function onGroupToggle(e) {
			
			e.preventDefault();
			
			var $toggle = $(this);
			var $group = $toggle.closest('.sm-rwmb-group-wrapper');
			
			if($group.hasClass('sm-rwmb-group-visible')) {
				$group.removeClass('sm-rwmb-group-visible');
				$toggle.find('i').addClass('dashicons-plus').removeClass('dashicons-minus');
			}else{
				$group.addClass('sm-rwmb-group-visible');
				$toggle.find('i').addClass('dashicons-minus').removeClass('dashicons-plus');
			}
		}
		
		$wrapper.find('.sm-rwmb-group-toggle').each(function() {
			
			$(this).closest('.sm-rwmb-group-wrapper').addClass('sm-rwmb-group-has-toggle');
			$(this).on('click', onGroupToggle);
		});
		
		// END CUSTOM
			
	
		/**
		 * Functions to handle input's name.
		 */
		var input = {
			updateGroupIndex: function ()
			{
				var that = this,
					$clones = $( this ).parents( '.sm-rwmb-group-clone' ),
					totalLevel = $clones.length;
				$clones.each( function ( i, clone )
				{
					var index = parseInt( $( clone ).parent().data( 'next-index' ) ) - 1,
						level = totalLevel - i;
					input.replaceName.call( that, level, index );
	
					// Stop each() loop immediately when reach the new clone group.
					if ( $( clone ).data( 'clone-group-new' ) )
					{
						return false;
					}
				} );
			},
			updateIndex     : function ()
			{
				var $this = $( this );
	
				// Update index only for sub fields in a group
				if ( !$this.closest( '.sm-rwmb-group-clone' ).length )
				{
					return;
				}
	
				// Do not update index if field is not cloned
				if ( !$this.closest( '.sm-rwmb-input' ).children( '.sm-rwmb-clone' ).length )
				{
					return;
				}
	
				var index = parseInt( $this.closest( '.sm-rwmb-input' ).data( 'next-index' ) ) - 1,
					level = $this.parents( '.sm-rwmb-clone' ).length;
				input.replaceName.call( this, level, index );
	
				// Stop propagation.
				return false;
			},
			// Replace the level-nth [\d] with new index
			replaceName     : function ( level, index )
			{
				var $input = $( this ),
					name = $input.attr( 'name' );
				if ( !name )
				{
					return;
				}
	
				var regex = new RegExp( '((?:\\[\\d+\\].*?){' + ( level - 1 ) + '}.*?)(\\[\\d+\\])' ),
					newValue = '$1' + '[' + index + ']';
	
				name = name.replace( regex, newValue );
				$input.attr( 'name', name );
			}
		};
	
		$wrapper.on( 'clone', ':input[class|="rwmb"]', input.updateIndex );
	
		/**
		 * When clone a group:
		 * 1) Remove sub fields' clones and keep only their first clone
		 * 2) Reset sub fields' [data-next-index] to 1
		 * 3) Set [name] for sub fields (which is done when 'clone' event is fired
		 * 4) Repeat steps 1)-3) for sub groups
		 */
		$wrapper.on( 'clone_instance', '.sm-rwmb-clone', function ()
		{
			if ( !$( this ).hasClass( 'sm-rwmb-group-clone' ) )
			{
				return false;
			}
	
			$( this )
				// Add new [data-clone-group-new] to detect which group is cloned. This data is used to update sub inputs' group index
				.data( 'clone-group-new', true )
				// Remove clones, and keep only their first clone. Reset [data-next-index] to 1
				.find( '.sm-rwmb-input' ).each( function ()
				{
					$( this ).data( 'next-index', 1 ).children( '.sm-rwmb-clone:gt(0)' ).remove();
				} ).end()
				// Update [group index] for inputs
				.find( ':input[class|="rwmb"]' ).each( function ()
				{
					input.updateGroupIndex.call( this );
				} );
	
			// Stop propagation to not trigger the same event on parent's clone.
			return false;
		} );
		
	};
	
	SM_MB_Field.init();	
} );
