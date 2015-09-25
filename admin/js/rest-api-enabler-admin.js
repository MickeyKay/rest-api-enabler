(function( $ ) {
	'use strict';

	$( document ).ready( function() {

		$( '.rest-api-enabler-settings input[type="checkbox"]' ).each( function() {
			raeToggleInput( $( this ) );
		});

		$( '.rest-api-enabler-settings input[type="checkbox"]' ).on( 'click', function() {
			raeToggleInput( $( this ) );
		});

	});

	function raeToggleInput( $input ) {

		var $rest_base_input = $input.parents( 'td' ).find( '.rae-rest-base' );

		if ( $input.is( ':checked' ) ) {
			$rest_base_input.removeClass( 'rae-hidden-opacity' );
		} else {
			$rest_base_input.addClass( 'rae-hidden-opacity' );
		}

	}

})( jQuery );
