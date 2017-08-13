// CPAC Notices
jQuery( function( $ ) {
	$( document ).ready( function() {

		$( '.updated a.hide-install-addons-notice' ).click( function( e ) {
			e.preventDefault();

			var el = $( this ).parents( '.ac-message' );
			var el_close = el.find( '.hide-notice' );

			el_close.hide();
			el_close.after( '<div class="spinner right"></div>' );
			el.find( '.spinner' ).show();

			$.post( ajaxurl, {
				'action' : 'cpac_hide_install_addons_notice'
			}, function() {
				el.find( '.spinner' ).remove();
				el.slideUp();
			} );

			return false;
		} );
	} );

} );