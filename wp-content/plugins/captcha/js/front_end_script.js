( function( $ ) {
	$( document ).on( "click", '.cptch_reload_button, .wpcf7-submit', function() {
		cptch_reload( $( this ) );
	}).on( "touchstart", function( event ) {
		event = event || window.event;
		var item = $( event.target );
		if ( cptch_vars.enlarge == '1' ) {
			var element = $( event.target );
			if ( element.hasClass( 'cptch_img' ) ) {
				event.preventDefault();
				element.toggleClass( 'cptch_reduce' );
				$( '.cptch_img' ).not( element ).removeClass( 'cptch_reduce' );
			} else {
				$( '.cptch_img' ).removeClass( 'cptch_reduce' );
			}
		}
		if ( item.hasClass( 'cptch_reload_button' ) || item.attr( 'name' ) == 'ac_form_submit' )
			cptch_reload( item );
	}).ready( function() {
		var ajax_containers = $( '.cptch_ajax_wrap' );

		if ( ! ajax_containers.length )
			return;

		ajax_containers.each( function() {
			cptch_reload( $( this ), true );
		});
	});
})(jQuery);

/**
 * Reload captcha
 */
function cptch_reload( object, is_ajax_load ) {
	is_ajax_load = is_ajax_load || false;
	(function($) {
		var captcha = object.hasClass( '.cptch_reload_button' ) ? object.parent().parent( '.cptch_wrap' ) : object.closest( 'form' ).find( '.cptch_wrap' ),
			button  = captcha.find( '.cptch_reload_button' );
		if ( ! captcha.length || button.hasClass( 'cptch_active' ) )
			return false;
		button.addClass( 'cptch_active' );
		var captcha_block = captcha.parent(),
			input         = captcha.find( 'input:text' ),
			input_name    = is_ajax_load ? captcha.attr( 'data-cptch-input' ) : input.attr( 'name' ),
			input_class   = is_ajax_load ? captcha.attr( 'data-cptch-class' ) : input.attr( 'class' ).replace( /cptch_input/, '' ).replace( /^\s+|\s+$/g, '' ),
			form_slug     = is_ajax_load ? captcha.attr( 'data-cptch-form' ) : captcha_block.find( 'input[name="cptch_form"]' ).val();
		$.ajax({
			type: 'POST',
			url: cptch_vars.ajaxurl,
			data: {
				action:              'cptch_reload',
				cptch_nonce:       cptch_vars.nonce,
				cptch_input_name:  input_name,
				cptch_input_class: input_class,
				cptch_form_slug:   form_slug
			},
			success: function( result ) {
				captcha_block.find( '.cptch_to_remove' ).remove();
				if ( input_class === '' )
					captcha.replaceWith( result ); /* for default forms */
				else
					captcha_block.replaceWith( result ); /* for custom forms */
			},
			error : function ( xhr, ajaxOptions, thrownError ) {
				alert( xhr.status );
				alert( thrownError );
			}
		});
	})(jQuery);
}