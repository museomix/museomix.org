<?php
defined( 'SECUPRESS_VERSION' ) or die( 'Cheatin&#8217; uh?' );

/** --------------------------------------------------------------------------------------------- */
/** REMOVE DEFAULT WORDPRESS REDIRECTIONS TO LOGIN AND ADMIN AREAS ============================== */
/** --------------------------------------------------------------------------------------------- */

/**
 * WordPress redirects some URLs (`wp-admin`, `dashboard`, `admin`) to the administration area,
 * and some others (`wp-login.php`, `login`) to the login page.
 * We don't want that, so we remove the hook.
 *
 * @since 1.0
 * @author Grégory Viguier
 */
remove_action( 'template_redirect', 'wp_redirect_admin_locations', 1000 );


add_filter( 'rewrite_rules_array', 'secupress_move_login_remove_rewrite_rules' );
/**
 * Filter the full set of generated rewrite rules.
 *
 * @since 1.0
 * @author Grégory Viguier
 *
 * @param (array) $rules The compiled array of rewrite rules.
 *
 * @return (array)
 */
function secupress_move_login_remove_rewrite_rules( $rules ) {
	if ( ! is_multisite() ) {
		unset( $rules['.*wp-register.php$'] );
	}
	return $rules;
}


/** --------------------------------------------------------------------------------------------- */
/** DENY ACCESS TO THE LOGIN FORM =============================================================== */
/** --------------------------------------------------------------------------------------------- */

add_action( 'login_init', 'secupress_move_login_maybe_deny_login_page', 0 );
add_action( 'secure_auth_redirect', 'secupress_move_login_maybe_deny_login_page', 0 );
/**
 * When displaying the login page, if the URL does not matches those in our settings, deny access.
 * Does nothing if the user is logged in.
 *
 * @since 1.0
 * @author Grégory Viguier
 */
function secupress_move_login_maybe_deny_login_page() {
	// If the user is logged in, do nothing, let WP redirect this user to the administration area.
	if ( is_user_logged_in() ) {
		return;
	}

	$uri    = secupress_get_current_url( 'uri' );
	$subdir = secupress_get_wp_directory();
	$slugs  = secupress_move_login_get_slugs();

	if ( $subdir ) {
		foreach ( $slugs as $action => $slug ) {
			$slugs[ $action ] = $subdir . $slug;
		}
	}
	/**
	 * If you want to display the login form somewhere outside wp-login.php, add your URIs here.
	 *
	 * @since 1.0
	 * @author Grégory Viguier
	 *
	 * @param (array)  $new_slugs An array of action => URIs (WP directory + slugs).
	 * @param (string) $uri       The current URI.
	 * @param (string) $subdir    WP directory.
	 * @param (array)  $slugs     URIs already in use.
	 */
	$new_slugs = apply_filters( 'sfml_slugs_not_to_kill', array(), $uri, $subdir, $slugs );
	$slugs     = is_array( $new_slugs ) && ! empty( $new_slugs ) ? array_merge( $new_slugs, $slugs ) : $slugs;
	$slugs     = array_flip( $slugs );

	if ( isset( $slugs[ $uri ] ) ) {
		// Display the login page.
		if ( ! defined( 'DONOTCACHEPAGE' ) ) {
			// Tell cache plugins not to cache the login page.
			define( 'DONOTCACHEPAGE', true );
		}
		return;
	}

	// You shall not pass!
	secupress_move_login_deny_login_access();
}


/**
 * Perform the action set for the login page: die or redirect.
 *
 * @since 1.0
 * @author Grégory Viguier
 *
 * @since 1.3.1 Only redirect choice left
 * @author Julio Potier
 */
function secupress_move_login_deny_login_access() {
	/**
	 * If you want to trigger a custom action (redirect, message, die...), add it here.
	 * Don't forget to exit/die.
	 *
	 * @since 1.0
	 * @author Grégory Viguier
	 */
	do_action( 'secupress.plugin.move-login.deny_login_access' );

	$do = secupress_get_module_option( 'move-login_login-access', '404', 'users-login' );

	if ( 'redir_404' === $do || 'redir_home' === $do ) {
		$do = '404';
	}

	$redirect_url = home_url( $do );
	/**
	 * Filter the 404 page URL.
	 *
	 * @since 1.0
	 * @author Grégory Viguier
	 *
	 * @param (string) $redirect An URL that leads to a 404 response.
	 */
	$redirect     = apply_filters( 'sfml_404_error_page', $redirect_url );
	$redirect     = apply_filters( 'secupress.plugin.move-login.login_redirect_location', $redirect_url );
	remove_filter( 'wp_redirect', 'secupress_move_login_maybe_deny_login_redirect', 1 );
	wp_redirect( esc_url_raw( user_trailingslashit( $redirect ) ) );
	die();
}


/** --------------------------------------------------------------------------------------------- */
/** DO NOT REDIRECT TO THE NEW LOGIN PAGE ======================================================= */
/** --------------------------------------------------------------------------------------------- */

add_filter( 'wp_redirect', 'secupress_move_login_maybe_deny_login_redirect', 1 );
/**
 * Filters the redirect location.
 * When a logged out user is being redirected to the new login page, deny access.
 * Does nothing if the user is logged in.
 *
 * @since 1.3
 * @author Grégory Viguier
 *
 * @param (string) $location The path to redirect to.
 *
 * @return (string)
 */
function secupress_move_login_maybe_deny_login_redirect( $location ) {
	global $pagenow;

	if ( 'wp-login.php' === $pagenow ) {
		return $location;
	}

	if ( is_user_logged_in() ) {
		return $location;
	}

	if ( wp_get_referer() === $location ) {
		return $location;
	}

	$slugs  = secupress_move_login_get_slugs();
	$wp_dir = secupress_get_wp_directory();
	if ( secupress_is_subfolder_install() ) {
		$base  = wp_parse_url( trailingslashit( secupress_get_main_url() ) );
		$base  = ltrim( $base['path'], '/' );
		$base .= $wp_dir ? '[_0-9a-zA-Z-]+/' : '([_0-9a-zA-Z-]+/)?';
	} else {
		$base  = wp_parse_url( trailingslashit( get_option( 'home' ) ) );
		$base  = ltrim( $base['path'], '/' );
		$base .= $wp_dir ? ltrim( $wp_dir, '/' ) : '';
	}

	$regex  = '^' . $base . '(' . implode( '|', $slugs ) . ')$';
	$parsed = wp_parse_url( $location );
	$parsed = ! empty( $parsed['path'] ) ? $parsed['path'] : '';
	$parsed = trim( $parsed, '/' );
	$parsed = explode( '/', $parsed );
	$parsed = end( $parsed );
	if ( ! preg_match( "@{$regex}@", $parsed ) ) {
		return $location;
	}
	$redirect = false;
	/**
	 * If you want to trigger a custom action (redirect, message, die...), add it here.
	 *
	 * @since 1.3
	 * @author Grégory Viguier
	 *
	 * @param (string|bool) $redirect A custom URL to redirect to. Default is false.
	 */
	$redirect = apply_filters( 'secupress.plugin.move-login.login_redirect_location', $redirect );

	if ( $redirect ) {
		return esc_url_raw( $redirect );
	}

	do_action_deprecated( 'secupress.plugin.move-login.deny_login_redirect', array(), '1.3', 'secupress.plugin.move-login.login_redirect_location' );

	$do = secupress_get_module_option( 'move-login_login-access', '404', 'users-login' );

	if ( 'redir_404' === $do || 'redir_home' === $do ) {
		$do = '404';
	}

	$redirect_url = home_url( $do );
	/**
	 * Filter the 404 page URL.
	 *
	 * @since 1.0
	 * @since 1.3.1 Only redirect choice left
	 * @author Grégory Viguier
	 * @author Julio Potier
	 *
	 * @param (string) $redirect An URL that leads to a 404 response.
	 */
	$redirect = apply_filters( 'sfml_404_error_page', $redirect_url );
	$redirect = apply_filters( 'secupress.plugin.move-login.login_redirect_location', $redirect_url );

	wp_redirect( esc_url_raw( user_trailingslashit( $redirect ) ) );
	die();
}

add_action( 'template_redirect', 'secupress_fallback_slug_redirect' );
/**
 * Will include the wp-loing.php file/template if the URL triggers the new slug
 *
 * @since 1.3.1
 * @author Julio Potier
 **/
function secupress_fallback_slug_redirect() {
	if ( ! is_404() || ! isset( $_SERVER['REQUEST_URI'] ) ) {
		return;
	}
	$slugs  = secupress_move_login_get_slugs();
	$wp_dir = secupress_get_wp_directory();

	if ( secupress_is_subfolder_install() ) {
		$base  = wp_parse_url( trailingslashit( secupress_get_main_url() ) );
		$base  = ltrim( $base['path'], '/' );
		$base .= $wp_dir ? '[_0-9a-zA-Z-]+/' : '([_0-9a-zA-Z-]+/)?';
	} else {
		$base  = wp_parse_url( trailingslashit( get_option( 'home' ) ) );
		$base  = ltrim( $base['path'], '/' );
		$base .= $wp_dir ? ltrim( $wp_dir, '/' ) : '';
	}

	$regex  = '^' . $base . '(' . implode( '|', $slugs ) . ')$';
	$parsed = wp_parse_url( $_SERVER['REQUEST_URI'] );
	$parsed = ! empty( $parsed['path'] ) ? $parsed['path'] : '';
	$parsed = trim( $parsed, '/' );
	if ( preg_match( "@{$regex}@", $parsed ) ) {
		$slugs  = array_flip( secupress_move_login_get_slugs() );
		$parsed = explode( '/', $parsed );
		$parsed = end( $parsed );
		if ( ! isset( $_REQUEST['action'] ) && isset( $slugs[ $parsed ] ) ) {
			$_REQUEST['action'] = $slugs[ $parsed ];
		}
		require( ABSPATH . 'wp-login.php' );
		die();
	}
}
