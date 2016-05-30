<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( 'Cheatin\' uh?' );
}

/*------------------------------------------------------------------------------------------------*/
/* !I18N ======================================================================================== */
/*------------------------------------------------------------------------------------------------*/

add_action( 'init', 'sfabt_lang_init' );

function sfabt_lang_init() {
	load_plugin_textdomain( 'sf-adminbar-tools', false, basename( dirname( SFABT_FILE ) ) . '/languages/' );
}


/*------------------------------------------------------------------------------------------------*/
/* !SETTINGS ==================================================================================== */
/*------------------------------------------------------------------------------------------------*/

// !Register setting: no undefined function anymore.

if ( ! function_exists( 'sf_register_setting' ) ) :
	function sf_register_setting( $option_group, $option_name, $sanitize_callback = '' ) {
		global $new_whitelist_options;

		if ( function_exists( 'register_setting' ) ) {
			register_setting( $option_group, $option_name, $sanitize_callback );
			return;
		}

		$new_whitelist_options = isset( $new_whitelist_options ) && is_array( $new_whitelist_options ) ? $new_whitelist_options : array();
		$new_whitelist_options[ $option_group ] = isset( $new_whitelist_options[ $option_group ] ) && is_array( $new_whitelist_options[ $option_group ] ) ? $new_whitelist_options[ $option_group ] : array();
		$new_whitelist_options[ $option_group ][] = $option_name;

		if ( $sanitize_callback ) {
			add_filter( "sanitize_option_{$option_name}", $sanitize_callback );
		}
	}
endif;


sf_register_setting( 'sf-abt-settings', '_sf_abt', 'sfabt_sanitize_options' );


/*------------------------------------------------------------------------------------------------*/
/* !INTERNAL UTILITIES ========================================================================== */
/*------------------------------------------------------------------------------------------------*/

if ( ! function_exists( 'doing_ajax' ) ) :
	function doing_ajax() {
		return defined( 'DOING_AJAX' ) && DOING_AJAX && is_admin();
	}
endif;


if ( ! function_exists( 'is_frontend' ) ) :
	function is_frontend() {
		return ! defined( 'XMLRPC_REQUEST' ) && ! defined( 'DOING_CRON' ) && ! is_admin();
	}
endif;


if ( ! function_exists( 'get_main_blog_id' ) ) :
	function get_main_blog_id() {
		static $blog_id;

		if ( ! isset( $blog_id ) ) {
			if ( ! is_multisite() ) {
				$blog_id = 1;
			} elseif ( ! empty( $GLOBALS['current_site']->blog_id ) ) {
				$blog_id = absint( $GLOBALS['current_site']->blog_id );
			} elseif ( defined( 'BLOG_ID_CURRENT_SITE' ) ) {
				$blog_id = absint( BLOG_ID_CURRENT_SITE );
			} elseif ( defined( 'BLOGID_CURRENT_SITE' ) ) {
				// deprecated.
				$blog_id = absint( BLOGID_CURRENT_SITE );
			}
			$blog_id = ! empty( $blog_id ) ? $blog_id : 1;
		}

		return $blog_id;
	}
endif;


// !Never trigger "Fatal error: Call to undefined function is_plugin_active_for_network()" ever again!

if ( ! function_exists( 'sf_is_plugin_active_for_network' ) ) :
	function sf_is_plugin_active_for_network( $plugin ) {
		if ( function_exists( 'is_plugin_active_for_network' ) ) {
			return is_plugin_active_for_network( $plugin );
		}

		if ( ! is_multisite() ) {
			return false;
		}

		$plugins = get_site_option( 'active_sitewide_plugins' );
		if ( isset( $plugins[ $plugin ] ) ) {
			return true;
		}

		return false;
	}
endif;


// `is_admin_bar_showing()`-like, without the `is_embed()` test since WP 4.4.0.

function sfabt_is_admin_bar_showing() {
	global $show_admin_bar, $pagenow;

	// For all these types of requests, we never want an admin bar.
	if ( defined( 'XMLRPC_REQUEST' ) || defined( 'DOING_AJAX' ) || defined( 'IFRAME_REQUEST' ) ) {
		return false;
	}

	// Integrated into the admin.
	if ( is_admin() ) {
		return true;
	}

	if ( ! isset( $show_admin_bar ) ) {
		if ( ! is_user_logged_in() || 'wp-login.php' === $pagenow ) {
			$show_admin_bar = false;
		} else {
			$show_admin_bar = _get_admin_bar_pref();
		}
	}

	/**
	 * Filter whether to show the admin bar.
	 *
	 * Returning false to this hook is the recommended way to hide the admin bar.
	 * The user's display preference is used for logged in users.
	 *
	 * @since 3.1.0
	 *
	 * @param bool $show_admin_bar Whether the admin bar should be shown. Default false.
	 */
	$show_admin_bar = apply_filters( 'show_admin_bar', $show_admin_bar );

	return $show_admin_bar;
}


/*------------------------------------------------------------------------------------------------*/
/* !CACHE ======================================================================================= */
/*------------------------------------------------------------------------------------------------*/

// !Allow to clear the settings cache.

function sfabt_clear_cache() {
	add_filter( 'sfabt_clear_cache', '__return_true' );
}


function sfabt_is_cache_cleared( $key ) {
	$caches = array();

	if ( ! isset( $caches[ $key ] ) ) {
		$caches[ $key ] = false;
	}

	if ( apply_filters( 'sfabt_clear_cache', false ) ) {	// "true" means the cache NEEDS to be cleared.
		remove_all_filters( 'sfabt_clear_cache' );
		$caches = array_map( '__return_true', $caches );
	}

	$val = $caches[ $key ];
	$caches[ $key ] = false;
	return $val;
}


/*------------------------------------------------------------------------------------------------*/
/* !SETTINGS ==================================================================================== */
/*------------------------------------------------------------------------------------------------*/

// !Get plugin settings.

function sfabt_get_options( $name = false ) {
	global $blog_id;
	static $options = null;

	if ( ! is_array( $options ) || sfabt_is_cache_cleared( 'options' ) ) {

		if ( sf_is_plugin_active_for_network( SFABT_PLUGIN_BASENAME ) ) {
			$main_blog_id = get_main_blog_id();

			if ( absint( $blog_id ) !== $main_blog_id ) {
				switch_to_blog( $main_blog_id );
				$options = get_option( '_sf_abt' );
				restore_current_blog();
			} else {
				$options = get_option( '_sf_abt' );
			}
		} else {
			$options = get_option( '_sf_abt' );
		}

		$options = is_array( $options ) ? $options : array();
		$options = sfabt_sanitize_options( $options );
	}

	if ( $name ) {
		return isset( $options[ $name ] ) ? $options[ $name ] : null;
	}

	return $options;
}


// !Update plugin options manually

function sfabt_update_options( $new_values ) {
	global $blog_id;

	if ( sf_is_plugin_active_for_network( SFABT_PLUGIN_BASENAME ) ) {
		$main_blog_id = get_main_blog_id();

		if ( absint( $blog_id ) !== $main_blog_id ) {
			switch_to_blog( $main_blog_id );
			update_option( '_sf_abt', $new_values );
			restore_current_blog();
		} else {
			update_option( '_sf_abt', $new_values );
		}
	} else {
		update_option( '_sf_abt', $new_values );
	}

	sfabt_clear_cache();
	return $new_values;
}


// !Sanitize options.

function sfabt_sanitize_options( $options = array() ) {
	$options_out = apply_filters( 'sfabt_sanitize_options', array(), $options );

	$options_out['coworkers'] = isset( $options['coworkers'] ) && is_array( $options['coworkers'] ) ? wp_parse_id_list( $options['coworkers'] ) : array();
	$options_out['coworkers'] = ! empty( $options_out['coworkers'] ) ? array_combine( $options_out['coworkers'], $options_out['coworkers'] ) : array();

	return $options_out;
}


/*------------------------------------------------------------------------------------------------*/
/* !COWORKERS LIST ============================================================================== */
/*------------------------------------------------------------------------------------------------*/

// !Shorthand to get the coworkers list.

function sfabt_get_coworkers() {
	$coworkers = sfabt_get_options( 'coworkers' );
	return is_array( $coworkers ) ? $coworkers : array();
}


// !Tell if a user can be added to the coworkers list.

function sfabt_is_eligible_user( $user_id ) {
	return $user_id && user_can( $user_id, SFABT_CAP );
}


// !Tell if a user is in the coworkers list.

function sfabt_is_coworker( $user_id = 0 ) {
	$user_id = $user_id ? $user_id : get_current_user_id();

	if ( ! $user_id ) {
		return false;
	}

	$user_id   = absint( $user_id );
	$coworkers = sfabt_get_coworkers();

	return ! empty( $coworkers[ $user_id ] ) && sfabt_is_eligible_user( $user_id ) ? $user_id : false;
}


// !Tell if the coworkers list still contains at least one administrator.

function sfabt_coworkers_have_admin() {
	static $have = null;

	if ( null === $have || sfabt_is_cache_cleared( 'coworkers_have_admin' ) ) {

		$have = false;
		$coworkers = sfabt_get_coworkers();

		if ( ! empty( $coworkers ) ) {
			foreach ( $coworkers as $user ) {
				if ( sfabt_is_eligible_user( $user ) ) {
					$have = true;
					break;
				}
			}
		}

	}

	return $have;
}
