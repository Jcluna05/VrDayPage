/* global jQuery, google */

jQuery( function ( $ )
{
	'use strict';

	/**
	 * Refresh Google maps, make sure they're fully loaded
	 * The problem is Google maps won't fully display when it's in hidden div (tab)
	 * We need to find all maps and send the 'resize' command to force them to refresh
	 *
	 * @see https://developers.google.com/maps/documentation/javascript/reference
	 *      ('resize' Event)
	 *
	 * @return void
	 */
	function refreshMap()
	{
		if ( typeof google !== 'object' || typeof google.maps !== 'object' )
			return;

		$( '.sm-rwmb-map-field' ).each( function ()
		{
			var controller = $( this ).data( 'mapController' );

			if ( typeof controller !== 'undefined' && typeof controller.map !== 'undefined' )
			{
				google.maps.event.trigger( controller.map, 'resize' );
			}
		} );
	}

	$( '.sm-rwmb-tab-nav' ).on( 'click', 'a', function ( e )
	{
		e.preventDefault();

		var $li = $( this ).parent(),
			panel = $li.data( 'panel' ),
			$wrapper = $li.closest( '.sm-rwmb-tabs' ),
			$panel = $wrapper.find( '.sm-rwmb-tab-panel-' + panel );

		$li.addClass( 'sm-rwmb-tab-active' ).siblings().removeClass( 'sm-rwmb-tab-active' );
		$panel.show().siblings().hide();

		refreshMap();
	} );

	// Set active tab based on visible pane to better works with Meta Box Conditional Logic
	if ( ! $( '.sm-rwmb-tab-active').is( 'visible' ) )
	{
		// Find the active pane
		var activePane = $( '.sm-rwmb-tab-panel[style*="block"]' ).index();

		if (activePane >= 0 )
		{
			$( '.sm-rwmb-tab-nav li' ).removeClass( 'sm-rwmb-tab-active' );

			$( '.sm-rwmb-tab-nav li' ).eq( activePane ).addClass( 'sm-rwmb-tab-active' );
		}
	}

	$( '.sm-rwmb-tab-active a' ).trigger( 'click' );

	// Remove wrapper
	$( '.sm-rwmb-tabs-no-wrapper' ).closest( '.postbox' ).addClass( 'sm-rwmb-tabs-no-controls' );
} );
