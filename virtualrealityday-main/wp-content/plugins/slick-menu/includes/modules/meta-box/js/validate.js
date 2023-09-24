jQuery( function ( $ )
{
	'use strict';

	var SM_MB_Field = {};
	 
	SM_MB_Field.init = function() {

		var self = this;
		
		self.initEvents();

	};
	
	SM_MB_Field.initEvents = function() {	
		
		var $form = $( '#post' ),
			rules = {
				invalidHandler: function ()
				{
					// Re-enable the submit ( publish/update ) button and hide the ajax indicator
					$( '#publish' ).removeClass( 'button-primary-disabled' );
					$( '#ajax-loading' ).attr( 'style', '' );
					$form.siblings( '#message' ).remove();
					$form.before( '<div id="message" class="error"><p>' + rwmbValidate.summaryMessage + '</p></div>' );
				}
			};
	
		// Gather all validation rules
		$( '.sm-rwmb-validation-rules' ).each( function ()
		{
			var subRules = $( this ).data( 'rules' );
			jQuery.extend( true, rules, subRules );
	
			// Required field styling
			$.each( subRules, function ( k, v )
			{
				if ( v['required'] )
				{
					$( '#' + k ).parent().siblings( '.sm-rwmb-label' ).addClass( 'required' ).append( '<span>*</span>' );
				}
			} );
		} );
	
		// Execute
		$form.validate( rules );
		
	};
	
	SM_MB_Field.init();
		
} );
