<?php
/*
 * Plugin Name: SF Admin bar tools
 * Plugin URI: https://www.screenfeed.fr/sf-abt/
 * Description: Adds some small interesting tools to the admin bar for developers.
 * Version: 3.0.4
 * Author: Grégory Viguier
 * Author URI: https://www.screenfeed.fr/greg/
 * License: GPLv3
 * License URI: https://www.screenfeed.fr/gpl-v3.txt
 * Text Domain: sf-adminbar-tools
 * Domain Path: /languages/
 */


if ( ! defined( 'ABSPATH' ) ) {
	die( 'Cheatin\' uh?' );
}

if ( version_compare( $GLOBALS['wp_version'], '3.1', '<' ) ) {
	return;
}


define( 'SFABT_VERSION',         '3.0.4' );
define( 'SFABT_FILE',            __FILE__ );
define( 'SFABT_PLUGIN_URL',      plugin_dir_url( SFABT_FILE ) );
define( 'SFABT_PLUGIN_DIR',      plugin_dir_path( SFABT_FILE ) );
define( 'SFABT_PLUGIN_BASENAME', plugin_basename( SFABT_FILE ) );

if ( ! defined( 'SFABT_CAP' ) ) {
	// Can be a role or a capability.
	define( 'SFABT_CAP',         'administrator' );
}
if ( ! defined( 'SFABT_DEBUG' ) ) {
	define( 'SFABT_DEBUG',       false );
}


/*------------------------------------------------------------------------------------------------*/
/* !INCLUDES ==================================================================================== */
/*------------------------------------------------------------------------------------------------*/

add_action( 'set_current_user', 'sfabt_includes', 1 );

function sfabt_includes() {
	global $pagenow;
	static $done = false;

	if ( $done ) {
		return;
	}

	$done    = true;
	$user_id = get_current_user_id();

	include( SFABT_PLUGIN_DIR . 'inc/general.php' );

	// Check we have at least one coworker. If not, add the current user if eligible.
	if ( sfabt_is_eligible_user( $user_id ) && ! sfabt_coworkers_have_admin() && ! sfabt_is_coworker() ) {
		$options = sfabt_get_options();
		$options['coworkers'][] = $user_id;
		sfabt_update_options( $options );
	}

	// Stop here for non-coworkers or if it's an ajjax call.
	if ( doing_ajax() || ! sfabt_is_coworker() ) {
		return;
	}

	$show = sfabt_is_admin_bar_showing();

	if ( $show ) {
		include( SFABT_PLUGIN_DIR . 'inc/adminbar-items.php' );
	}

	if ( is_admin() ) {
		include( SFABT_PLUGIN_DIR . 'inc/admin.php' );

		if ( $show ) {
			include( SFABT_PLUGIN_DIR . 'inc/adminbar-items-admin.php' );
		}

		if ( 'profile.php' === $pagenow && defined( 'IS_PROFILE_PAGE' ) && IS_PROFILE_PAGE ) {
			include( SFABT_PLUGIN_DIR . 'inc/profile.php' );
		}
	} elseif ( is_frontend() && $show ) {
		include( SFABT_PLUGIN_DIR . 'inc/adminbar-items-frontend.php' );
	}
}


/*------------------------------------------------------------------------------------------------*/
/* !TOOLS ======================================================================================= */
/*------------------------------------------------------------------------------------------------*/

/*
 * A better <code>print_r()</code>, wrapped in a styled <code>&lt;pre&gt;</code> tag.
 *
 * $var                     (mixed)    Variable to print out.
 * $display                 (bool|int) When false (default), print a "display: none" in the <code>&lt;pre&gt;</code> style. This way, you'll have to manually remove it (with firebug or any other tool), so you don't bother other users. But be aware that you can still break things.
 * $print_for_non_logged_in (bool|int) When false (default), nothing will be printed for logged out users.
 * ex:
 * pre_print_r($var)      : printed only for logged in users, but hidden for everybody (remove the display:hidden by yourself in the page).
 * pre_print_r($var, 1)   : printed only for logged in users, all logged in users can see the code.
 * pre_print_r($var, 0, 1): printed for everybody (logged out users too), but hidden for everybody (remove the display:hidden by yourself in the page).
 * pre_print_r($var, 1, 1): printed for everybody (logged out users too), all users can see the code.
 */

if ( ! function_exists( 'pre_print_r' ) ) :
	function pre_print_r( $var, $display = false, $print_for_non_logged_in = false ) {
		if ( ! $print_for_non_logged_in && ! ( function_exists( 'is_user_logged_in' ) && is_user_logged_in() ) ) {
			return;
		}

		echo '<pre style="background:rgb(34,34,34);line-height:19px;font-size:14px;color:#fff;text-shadow:none;font-family:monospace;padding:6px 10px;margin:2px;position:relative;z-index:10000;overflow:auto;' . ( (bool) $display ? '' : 'display:none;' ) . '">';
			if ( ( is_string( $var ) && '' === trim( $var ) ) || is_bool( $var ) || null === $var ) {
				var_dump( $var );
			} else {
				print_r( $var );
			}
			echo '<div style="clear:both"></div>';
		echo "</pre>\n";
	}
endif;
