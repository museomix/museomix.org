<?php
/**
 * Module Name: Move Login
 * Description: Change your login URL.
 * Main Module: users_login
 * Author: SecuPress
 * Version: 1.3.1
 */

defined( 'SECUPRESS_VERSION' ) or die( 'Cheatin&#8217; uh?' );

/** --------------------------------------------------------------------------------------------- */
/** INCLUDES ==================================================================================== */
/** --------------------------------------------------------------------------------------------- */

if ( is_admin() && ! function_exists( 'secupress_move_login_write_rules' ) ) {
	include( SECUPRESS_MODULES_PATH . 'users-login/plugins/inc/php/move-login/admin.php' );
}

// EMERGENCY BYPASS!
if ( ( ! defined( 'SFML_ALLOW_LOGIN_ACCESS' ) || ! SFML_ALLOW_LOGIN_ACCESS ) &&
	( ! defined( 'SECUPRESS_ALLOW_LOGIN_ACCESS' ) || ! SECUPRESS_ALLOW_LOGIN_ACCESS )
	) {
	include( SECUPRESS_MODULES_PATH . 'users-login/plugins/inc/php/move-login/deprecated.php' );
	include( SECUPRESS_MODULES_PATH . 'users-login/plugins/inc/php/move-login/url-filters.php' );
	include( SECUPRESS_MODULES_PATH . 'users-login/plugins/inc/php/move-login/redirections-and-dies.php' );
}


/** --------------------------------------------------------------------------------------------- */
/** TOOLS ======================================================================================= */
/** --------------------------------------------------------------------------------------------- */

/**
 * Get default slugs.
 *
 * @since 1.0
 * @author Grégory Viguier
 *
 * @return (array)
 */
function secupress_move_login_get_default_slugs() {
	$slugs = array(
		'login'        => 1,
	);

	/**
	 * Add additional slugs.
	 *
	 * @since 1.0
	 * @author Grégory Viguier
	 *
	 * @param (array) $new_slugs An array with slugs as keys.
	 */
	$new_slugs = apply_filters( 'sfml_additional_slugs', array() );

	if ( $new_slugs && is_array( $new_slugs ) ) {
		$slugs = array_merge( $slugs, $new_slugs );
	}

	$slugs = array_keys( $slugs );
	$slugs = array_combine( $slugs, $slugs );

	return $slugs;
}

/**
 * Get the slugs the user has set.
 *
 * @since 1.0
 * @author Grégory Viguier
 *
 * @return (array)
 */
function secupress_move_login_get_slugs() {
	$slugs = secupress_move_login_get_default_slugs();

	foreach ( $slugs as $action ) {
		$slugs[ $action ] = secupress_get_module_option( 'move-login_slug-' . $action, $action, 'users-login' );
		$slugs[ $action ] = sanitize_title( $slugs[ $action ], $action, 'display' );
	}
	return $slugs;
}
