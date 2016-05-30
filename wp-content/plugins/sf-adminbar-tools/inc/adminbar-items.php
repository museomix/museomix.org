<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( 'Cheatin\' uh?' );
}

/*------------------------------------------------------------------------------------------------*/
/* !WORDPRESS SEO PLUGIN ======================================================================== */
/*------------------------------------------------------------------------------------------------*/

// Remove admin bar items.

add_action( 'init', 'sfabt_maybe_remove_wpseo_adminbar_stuff' );

function sfabt_maybe_remove_wpseo_adminbar_stuff() {
	if ( ! defined( 'WPSEO_VERSION' ) || ! get_user_meta( get_current_user_id(), 'sf-abt-no-wpseo', true ) ) {
		return;
	}

	remove_action( 'admin_bar_menu', 'wpseo_admin_bar_menu', 95 );
	remove_action( 'wp_enqueue_scripts', 'wpseo_admin_bar_css' );
}


/*------------------------------------------------------------------------------------------------*/
/* !THE ADMINBAR ITEMS ========================================================================== */
/*------------------------------------------------------------------------------------------------*/

add_action( 'add_admin_bar_menus', 'sfabt_admin_bar_menus' );

function sfabt_admin_bar_menus() {
	add_action( 'admin_bar_menu', 'sfabt_tools', 0 );
}


function sfabt_tools( $wp_admin_bar ) {

	do_action( 'sfabt_add_nodes_before', $wp_admin_bar );

	// !GROUP LEVEL 0: The main group --------------------------------------------------------------------------------------------------------------------------------------------------------------------
	$wp_admin_bar->add_group( array(
		'id'     => 'sfabt-tools',
		'meta'   => array(
			'class' => 'ab-top-secondary',
		),
	) );

	// !ITEM LEVEL 0: The main item (requests and page load time) ----------------------------------------------------------------------------------------------------------------------------------------
	$queries	= number_format_i18n( (int) get_num_queries() );
	$timer_stop	= timer_stop();

	$wp_admin_bar->add_node( array(
		'parent' => 'sfabt-tools',
		'id'     => 'sfabt-main',
		'title'  => '<span class="sfabt-stats">' . sprintf( __( '%1$s q. - %2$s s', 'sf-adminbar-tools' ), $queries, $timer_stop ) . '</span>',
	) );

	do_action( 'sfabt_add_nodes_inside', $wp_admin_bar );

	// !ITEM LEVEL 1: php mem + version ------------------------------------------------------------------------------------------------------------------------------------------------------------------
	$wp_admin_bar->add_node( array(
		'parent' => 'sfabt-main',
		'id'     => 'sfabt-php',
		'title'  => sprintf( __( '%1$s / %2$s MB (%3$s)', 'sf-adminbar-tools' ), size_format( memory_get_usage(), 2 ), number_format_i18n( absint( WP_MEMORY_LIMIT ) ), PHP_VERSION ),
		'meta'   => array(
			'title' => __( 'Memory used / Memory max (php version)', 'sf-adminbar-tools' ),
		),
	) );

	// !ITEM LEVEL 1: debug ------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
	$wp_admin_bar->add_node( array(
		'parent' => 'sfabt-main',
		'id'     => 'sfabt-debug',
		'title'  => WP_DEBUG ? __( 'WP_DEBUG is enabled', 'sf-adminbar-tools' ) : __( 'WP_DEBUG is disabled', 'sf-adminbar-tools' ),
		'meta'   => array(
			'title' => sprintf( __( 'Error reporting level is %s', 'sf-adminbar-tools' ), sfabt_error2string( error_reporting() ) ),
		),
	) );

	do_action( 'sfabt_add_nodes_after', $wp_admin_bar );

}


/*------------------------------------------------------------------------------------------------*/
/* !CSS and JS ================================================================================== */
/*------------------------------------------------------------------------------------------------*/

add_action( 'admin_enqueue_scripts', 'sfabt_css_and_js', 999 );
add_action( 'wp_print_styles', 'sfabt_css_and_js', 999 );

function sfabt_css_and_js( $hook_suffix ) {
	$min = SFABT_DEBUG ? '' : '.min';

	wp_enqueue_style( 'sfabt', SFABT_PLUGIN_URL . 'res/css/sfabt' . $min . '.css', false, SFABT_VERSION, 'screen' );

	$loc = array(
		'debug'         => ( '' === $min ),
		'queryNonce'    => wp_create_nonce( 'sfabt_get-var' ),
		'closeModal'    => __( 'Close this modal window', 'sf-adminbar-tools' ),
		'clickToReload' => __( 'Click to reload the value', 'sf-adminbar-tools' ),
		'loading'       => __( 'Loading...', 'sf-adminbar-tools' ),
	);
	wp_enqueue_script( 'sfabt', SFABT_PLUGIN_URL . 'res/js/sfabt' . $min . '.js', array( 'jquery' ), SFABT_VERSION, true );
	wp_localize_script( 'sfabt', 'sfabt', $loc );
}


/*------------------------------------------------------------------------------------------------*/
/* !Utilities =================================================================================== */
/*------------------------------------------------------------------------------------------------*/

// !Returns the error level as a string

function sfabt_error2string( $value ) {
	$level_names = array(
		E_ERROR           => 'E_ERROR',
		E_WARNING         => 'E_WARNING',
		E_PARSE           => 'E_PARSE',
		E_NOTICE          => 'E_NOTICE',
		E_CORE_ERROR      => 'E_CORE_ERROR',
		E_CORE_WARNING    => 'E_CORE_WARNING',
		E_COMPILE_ERROR   => 'E_COMPILE_ERROR',
		E_COMPILE_WARNING => 'E_COMPILE_WARNING',
		E_USER_ERROR      => 'E_USER_ERROR',
		E_USER_WARNING    => 'E_USER_WARNING',
		E_USER_NOTICE     => 'E_USER_NOTICE',
	);
	if ( defined( 'E_STRICT' ) ) {
		$level_names[ E_STRICT ] = 'E_STRICT';
	}

	$levels = array();
	if ( ( $value & E_ALL ) === E_ALL ) {
		$levels[] = 'E_ALL';
		$value &= ~ E_ALL;
	}
	foreach ( $level_names as $level => $name ) {
		if ( ( $value & $level ) === $level ) {
			$levels[] = $name;
		}
	}

	return implode( ' | ',$levels );
}
