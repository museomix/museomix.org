<?php
defined( 'ABSPATH' ) or die( 'Cheatin&#8217; uh?' );

/** --------------------------------------------------------------------------------------------- */
/** MODULE OPTIONS ============================================================================== */
/** --------------------------------------------------------------------------------------------- */

/**
 * Update a SecuPress module option.
 *
 * @since 1.0
 *
 * @param (string) $option  The option name.
 * @param (mixed)  $value   The new value.
 * @param (string) $module  The module slug (see array keys from `modules.php`). Default is the current module.
 */
function secupress_update_module_option( $option, $value, $module = false ) {
	if ( ! $module ) {
		$module = secupress_get_current_module();
	}

	$options = get_site_option( "secupress_{$module}_settings" );
	$options = is_array( $options ) ? $options : array();
	$options[ $option ] = $value;

	update_site_option( "secupress_{$module}_settings", $options );
}


/**
 * Update a SecuPress module options.
 *
 * @since 1.0
 *
 * @param (array)  $values The new values. Keys not provided are not removed, previous values are kept.
 * @param (string) $module The module slug (see array keys from `modules.php`). Default is the current module.
 */
function secupress_update_module_options( $values, $module = false ) {
	if ( ! $values || ! is_array( $values ) ) {
		return null;
	}

	if ( ! $module ) {
		$module = secupress_get_current_module();
	}

	$options = get_site_option( "secupress_{$module}_settings" );
	$options = is_array( $options ) ? $options : array();
	$options = array_merge( $options, $values );

	update_site_option( "secupress_{$module}_settings", $options );
}


/**
 * Get the current module.
 *
 * @since 1.0
 *
 * @return (string).
 */
function secupress_get_current_module() {
	if ( ! class_exists( 'SecuPress_Settings' ) ) {
		secupress_require_class( 'settings' );
	}
	if ( ! class_exists( 'SecuPress_Settings_Modules' ) ) {
		secupress_require_class( 'settings', 'modules' );
	}

	return SecuPress_Settings_Modules::get_instance()->get_current_module();
}
