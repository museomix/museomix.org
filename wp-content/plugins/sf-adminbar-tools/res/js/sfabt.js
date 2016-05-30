if ( ! ( window.console && window.console.log ) ) {
	(function() {
		var noop    = function consoleNoop() {},
			methods = ["assert", "clear", "count", "debug", "dir", "dirxml", "error", "exception", "group", "groupCollapsed", "groupEnd", "info", "log", "markTimeline", "profile", "profileEnd", "markTimeline", "table", "time", "timeEnd", "timeStamp", "trace", "warn"],
			length  = methods.length,
			console = window.console = {};
		while ( length-- ) {
			console[ methods[ length ] ] = noop;
		}
	}());
}

// !Console.log only if debug is active.
function abtlog( code ) {
	if ( window.sfabt.debug ) {
		console.log( code );
	}
}
// Shorthand to tell if a modifier key is pressed.
function sfabtHasModifierKey( e ) {
	return e.altKey || e.ctrlKey || e.metaKey || e.shiftKey;
}
// Shorthand to tell if the pressed key is Space or Enter.
function sfabtIsSpaceOrEnterKey( e ) {
	return ( e.which === 13 || e.which === 32 ) && ! sfabtHasModifierKey( e );
}
// Shorthand to tell if the pressed key is Escape.
function sfabtIsEscapeKey( e ) {
	return e.which === 27 && ! sfabtHasModifierKey( e );
}

// !Document ready
jQuery(document).ready(function($){

	// !Call $wp_query with ajax
	$("body")
		// Open the $wp_query lightbox
		.on( "click keydown", ".sfabt-get-var-button", function sfabtOpenLightboxEVH( e ) {
			if ( e.type === "keydown" && ! sfabtIsSpaceOrEnterKey( e ) ) {
				return;
			}

			var $this = $(this), $buttons, $preCode, globalVar, pageUrl, sep;

			e.preventDefault();

			if ( $this.attr( "disabled" ) ) {
				return;
			}

			$buttons  = $( ".sfabt-get-var-button" ).attr( {"disabled": "disabled", "aria-disabled": "true"} );
			$preCode  = $( "#sfabt-code" );
			globalVar = $this.parent().parent().attr("id");
			pageUrl   = window.location.href;
			sep       = ( pageUrl.indexOf("?") !== -1 ) ? "&" : "?";

			if ( globalVar === "sfabt-pre" ) {
				globalVar = $this.text().replace( "$", "" );
			}
			else {
				globalVar = globalVar.replace( "wp-admin-bar-sfabt-var-", "" );
			}

			if ( pageUrl.indexOf("#") !== -1 ) {
				pageUrl = pageUrl.split("#")[0];
			}

			$.get( pageUrl + sep + "_wpnonce=" + window.sfabt.queryNonce + "&sfabt-var=" + globalVar )
				.always( function sfabtGetLightboxContentAlways( data, status, jqXHR ) {
					var html = "",
						dataType = typeof data;

					if ( dataType === "object" && data.readyState === 4 ) {
						html = data.responseText;
					}
					else if ( dataType !== "object" && jqXHR.readyState === 4 ) {
						html = data;
					}

					if ( ! $preCode.length ) {
						$preCode = $( '<div id="sfabt-pre-wrap" tabindex="0" aria-label="' + window.sfabt.closeModal + '"><pre id="sfabt-pre"><h2 id="sfabt-title"><button class="sf-no-button sfabt-get-var-button" title="' + window.sfabt.clickToReload + '"><span>$' + globalVar + '</span><span class="sfabt-spin" title="' + window.sfabt.loading + '"></span></button></h2><code id="sfabt-code"></code></pre></div>' ).appendTo( $("body") ).find( "#sfabt-code" );
					}

					$buttons.removeAttr( "disabled aria-disabled" );
					$preCode.html( html ).prev( "#sfabt-title" ).children( ".sfabt-get-var-button" ).focus();
				} );
		} )
		// Close the $wp_query lightbox
		.on( "click keydown", "#sfabt-pre-wrap", function sfabtCloseLightboxEVH( e ) {
			if ( e.type === "keydown" && sfabtIsEscapeKey( e ) ) {
				$(this).remove();
				e.preventDefault();
			}
			else if ( e.target === this ) {
				if ( e.type === "keydown" && ! sfabtIsSpaceOrEnterKey( e ) ) {
					return;
				}
				$(this).remove();
				e.preventDefault();
			}
		} );

	// !Action input fields (admin)
	$( "#wp-admin-bar-sfabt-tools" ).on( "click focus", "input.no-adminbar-style", function sfabtEnlargeYourHookEVH( e ) {
		var $this      = $(this),
			nbr_params = $this.data( "nbrparams" ),
			newValue   = "add_action( '" + this.defaultValue + "', ''" + ( nbr_params !== "undefined" && Number( nbr_params ) > 1 ? ", 10, " + Number( nbr_params ) : "" ) + " );",
			newWidth   = newValue.length;

		$this.val( newValue ).css( "width", newWidth + "ch" );
		this.select();
	} )
	.on( "blur", "input.no-adminbar-style", function sfabtReduceYourHookEVH( e ) {
		$(this).val( this.defaultValue ).css( "width", "" );
	} );

});