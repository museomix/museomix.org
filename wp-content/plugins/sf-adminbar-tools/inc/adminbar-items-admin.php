<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( 'Cheatin\' uh?' );
}

/*------------------------------------------------------------------------------------------------*/
/* !Add admin nodes ============================================================================= */
/*------------------------------------------------------------------------------------------------*/

add_action( 'sfabt_add_nodes_inside', 'sfabt_add_admin_nodes_inside' );

function sfabt_add_admin_nodes_inside( $wp_admin_bar ) {
	global $hook_suffix, $pagenow, $typenow, $taxnow, $plugin_page, $page_hook, $current_screen;

	// !ITEM LEVEL 1: Current screen menu --------------------------------------------------------------------------------------------------------------------------------------------------------------------------
	$wp_admin_bar->add_node( array(
		'parent' => 'sfabt-main',
		'id'     => 'sfabt-screen',
		'title'  => __( 'Current screen', 'sf-adminbar-tools' ),
	) );

	// !ITEM LEVEL 2: Admin init hooks menu ------------------------------------------------------------------------------------------------------------------------------------------------------------------------
	$wp_admin_bar->add_node( array(
		'parent' => 'sfabt-screen',
		'id'     => 'sfabt-load-hook',
		'title'  => __( 'Hooks before headers', 'sf-adminbar-tools' ),
	) );

	// !ITEMS LEVEL 3: Admin init hooks items ----------------------------------------------------------------------------------------------------------------------------------------------------------------------
	$meta_before = array(
		'class' => 'has-intel sfabt-before-headers',
	);
	$meta_after  = array(
		'class' => 'has-intel sfabt-after-headers',
	);

	$actions     = array(
		'muplugins_loaded',
		'plugins_loaded',
		'setup_theme',
		'after_setup_theme',
		array( 'auth_cookie_valid', array( '$cookie_elements' => 'array', '$user' => 'WP_User object' ) ),
		'set_current_user',
		'init',
		'widgets_init',
		'wp_loaded',
		array( 'auth_redirect', array( '$user_id' => 'int' ) ),
		array( ( is_network_admin() ? 'network_' : ( is_user_admin() ? 'user_' : '' ) ) . 'admin_menu', array( '\'\'' => 'empty string' ) ),
		'admin_init',
		'admin_bar_init',
		'add_admin_bar_menus',
		array( 'current_screen', array( '$current_screen' => 'WP_Screen object' ) ),
	);

	foreach ( $actions as $action ) {
		$wp_admin_bar->add_node( array(
			'parent' => 'sfabt-load-hook',
			'id'     => 'sfabt-' . ( is_array( $action ) ? $action[0] : $action ),
			'title'  => sfabt_get_action_num( $action ),
			'meta'   => $meta_before,
		) );
	}

	if ( ! empty( $plugin_page ) ) {

		if ( $page_hook ) {

			$wp_admin_bar->add_node( array(
				'parent' => 'sfabt-load-hook',
				'id'     => 'sfabt-load-hook-plugin-beforeh',
				'title'  => sfabt_get_action_num( 'load-' . $page_hook ),
				'meta'   => $meta_before,
			) );

		} else {

			$wp_admin_bar->add_node( array(
				'parent' => 'sfabt-load-hook',
				'id'     => 'sfabt-load-hook-plugin-page',
				'title'  => sfabt_get_action_num( 'load-' . $plugin_page ),
				'meta'   => $meta_before,
			) );

		}

	} elseif ( ! isset( $_GET['import'] ) ) {

		$wp_admin_bar->add_node( array(
			'parent' => 'sfabt-load-hook',
			'id'     => 'sfabt-load-hook-pagenow',
			'title'  => sfabt_get_action_num( 'load-' . $pagenow ),
			'meta'   => $meta_before,
		) );

		// Some old "load-*" hooks exist for backward compatibility.
		$old = false;

		if ( 'page' === $typenow ) {

			if ( 'post-new.php' === $pagenow ) {
				$old = 'page-new';
			} elseif ( 'post.php' === $pagenow ) {
				$old = 'page';
			}

		} elseif ( 'edit-tags.php' === $pagenow ) {

			if ( 'category' === $taxnow ) {
				$old = 'categories';
			} elseif ( 'link_category' === $taxnow ) {
				$old = 'edit-link-categories';
			}

		} elseif ( 'term.php' === $pagenow ) {

			$old = 'edit-tags';

		}

		if ( $old ) {
			$wp_admin_bar->add_node( array(
				'parent' => 'sfabt-load-hook',
				'id'     => 'sfabt-load-hook-pagenow-old',
				'title'  => sfabt_get_action_num( "load-$old.php" ),
				'meta'   => $meta_before,
			) );
		}

	}

	// !ITEM LEVEL 3: Admin init hook for $_REQUEST['action'] ------------------------------------------------------------------------------------------------------------------------------------------------------
	if ( empty( $plugin_page ) && ! isset( $_GET['import'] ) && ! empty( $_REQUEST['action'] ) ) {

		$wp_admin_bar->add_node( array(
			'parent' => 'sfabt-load-hook',
			'id'     => 'sfabt-load-hook-action',
			'title'  => sfabt_get_action_num( 'admin_action_' . esc_attr( $_REQUEST['action'] ) ),
			'meta'   => $meta_before,
		) );

	}

	// !ITEM LEVEL 2: Admin head hooks menu --------------------------------------------------------------------------------------------------------------------------------------------------------------------------
	$wp_admin_bar->add_node( array(
		'parent' => 'sfabt-screen',
		'id'     => 'sfabt-head-hook',
		'title'  => __( 'Hooks after headers', 'sf-adminbar-tools' ),
	) );

	// !ITEMS LEVEL 3: Admin head hooks items ------------------------------------------------------------------------------------------------------------------------------------------------------------------------
	$meta = array( 'class' => 'has-intel' );

	$wp_admin_bar->add_node( array(
		'parent' => 'sfabt-head-hook',
		'id'     => 'sfabt-hook-enq-styles',
		'title'  => sfabt_get_action_num( 'admin_enqueue_scripts', array( '$hook_suffix' => $hook_suffix ) ),
		'meta'   => $meta,
	) );

	if ( $hook_suffix ) {
		$actions = array( 'admin_print_styles-' . $hook_suffix, 'admin_print_styles', 'admin_print_scripts-' . $hook_suffix, 'admin_print_scripts', 'wp_print_scripts', 'admin_head-' . $hook_suffix );
	} else {
		$actions = array( 'admin_print_styles', 'admin_print_scripts', 'wp_print_scripts' );
	}

	$actions = array_merge( $actions, array(
		'admin_head',
		'adminmenu',
		'in_admin_header',
		array( 'admin_bar_menu', array( '&$wp_admin_bar' => get_class( $GLOBALS['wp_admin_bar'] ) . ' object' ) ),
		( is_network_admin() ? 'network_' : ( is_user_admin() ? 'user_' : '' ) ) . 'admin_notices',
		'all_admin_notices',
	) );

	if ( ! empty( $plugin_page ) && $page_hook ) {
		$actions[] = $page_hook;
	}

	foreach ( $actions as $action ) {
		$wp_admin_bar->add_node( array(
			'parent' => 'sfabt-head-hook',
			'id'     => 'sfabt-' . ( is_array( $action ) ? $action[0] : $action ),
			'title'  => sfabt_get_action_num( $action ),
			'meta'   => $meta,
		) );
	}

	// !ITEM LEVEL 2: Admin footer hooks menu --------------------------------------------------------------------------------------------------------------------------------------------------------------------------
	$wp_admin_bar->add_node( array(
		'parent' => 'sfabt-screen',
		'id'     => 'sfabt-foot-hook',
		'title'  => __( 'Hooks in footer', 'sf-adminbar-tools' ),
	) );

	// !ITEMS LEVEL 3: Admin footer hooks items ------------------------------------------------------------------------------------------------------------------------------------------------------------------------
	$actions = array(
		'in_admin_footer',
		array( 'admin_footer', array( '\'\'' => 'empty string' ) ),
		'admin_print_footer_scripts',
		'admin_footer-' . $hook_suffix,
		'shutdown',
	);

	foreach ( $actions as $action ) {
		$wp_admin_bar->add_node( array(
			'parent' => 'sfabt-foot-hook',
			'id'     => 'sfabt-' . ( is_array( $action ) ? $action[0] : $action ),
			'title'  => sfabt_get_action_num( $action ),
			'meta'   => $meta,
		) );
	}

	// !ITEM LEVEL 2: $...now menu ----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
	if ( $pagenow || $typenow || $taxnow ) {

		$wp_admin_bar->add_node( array(
			'parent' => 'sfabt-screen',
			'id'     => 'sfabt-now',
			'title'  => '$...now',
		) );

		// !ITEMS LEVEL 3: $...now items ----------------------------------------------------------------------------------------------------------------------------------------------------------------------------
		if ( $pagenow ) {
			$wp_admin_bar->add_node( array(
				'parent' => 'sfabt-now',
				'id'     => 'sfabt-now-page',
				'title'  => sprintf( __( '%1$s: %2$s', 'sf-adminbar-tools' ), '$pagenow', $pagenow ),
			) );
		}

		if ( $typenow ) {
			$wp_admin_bar->add_node( array(
				'parent' => 'sfabt-now',
				'id'     => 'sfabt-now-type',
				'title'  => sprintf( __( '%1$s: %2$s', 'sf-adminbar-tools' ), '$typenow', $typenow ),
			) );
		}

		if ( $taxnow ) {
			$wp_admin_bar->add_node( array(
				'parent' => 'sfabt-now',
				'id'     => 'sfabt-now-tax',
				'title'  => sprintf( __( '%1$s: %2$s', 'sf-adminbar-tools' ), '$taxnow', $taxnow ),
			) );
		}

	}

	// !ITEMS LEVEL 2: Current screen id, base, etc -----------------------------------------------------------------------------------------------------------------------------------------------------------------
	if ( is_object( $current_screen ) && ! empty( $current_screen ) ) {

		$wp_admin_bar->add_node( array(
			'parent' => 'sfabt-screen',
			'id'     => 'sfabt-screenid',
			'title'  => sprintf( __( '%1$s: %2$s', 'sf-adminbar-tools' ), 'id', $current_screen->id ),
		) );

		$wp_admin_bar->add_node( array(
			'parent' => 'sfabt-screen',
			'id'     => 'sfabt-screenbase',
			'title'  => sprintf( __( '%1$s: %2$s', 'sf-adminbar-tools' ), 'base', $current_screen->base ),
		) );

		if ( $current_screen->parent_base ) {
			$wp_admin_bar->add_node( array(
				'parent' => 'sfabt-screen',
				'id'     => 'sfabt-screenpbase',
				'title'  => sprintf( __( '%1$s: %2$s', 'sf-adminbar-tools' ), 'parent_base', $current_screen->parent_base ),
			) );
		}

		if ( $current_screen->parent_file ) {
			$wp_admin_bar->add_node( array(
				'parent' => 'sfabt-screen',
				'id'     => 'sfabt-screenpfile',
				'title'  => sprintf( __( '%1$s: %2$s', 'sf-adminbar-tools' ), 'parent_file', $current_screen->parent_file ),
			) );
		}

	}

	// !ITEM LEVEL 2: WPML hidden page -----------------------------------------------------------------------------------------------------------------------------------------------------------------------------
	if ( class_exists( 'SitePress' ) ) {
		$wp_admin_bar->add_node( array(
			'parent' => 'sfabt-screen',
			'id'     => 'sfabt-wpml',
			'title'  => 'WPML - ' . __( 'Troubleshooting', 'sitepress' ),
			'href'   => admin_url( 'admin.php?page=sitepress-multilingual-cms/menu/troubleshooting.php' ),
		) );
	}
}


/*------------------------------------------------------------------------------------------------*/
/* !Utilities =================================================================================== */
/*------------------------------------------------------------------------------------------------*/

// !Get the number of times an action has been ran (ok, I'm not really sure this sentence is in english).

function sfabt_get_action_num( $hook, $params = false ) {
	global $wp_actions, $wp_filter;
	/**
	 * WP 4.7 introduced the `WP_Hook` class.
	 * This static var will tell if `$wp_filter` holds the old hooks (an array of arrays) or the new ones (an array of `WP_Hook` objects).
	 * We need to do this test only once, the result will be the same for all hooks.
	 */
	static $hook_is_array;

	if ( is_array( $hook ) ) {
		$params = $hook[1];
		$hook   = $hook[0];
	}

	if ( isset( $wp_actions[ $hook ] ) ) {
		if ( empty( $wp_filter[ $hook ] ) ) {
			$num = 0;
		} elseif ( ! isset( $hook_is_array ) ) {
			$hook_is_array = is_array( $wp_filter[ $hook ] );
		}

		if ( ! isset( $num ) ) {
			if ( $hook_is_array ) {
				$num = array_sum( array_map( 'count', $wp_filter[ $hook ] ) );
			} else {
				$num = array_sum( array_map( 'count', $wp_filter[ $hook ]->callbacks ) );
			}
		}
	} else {
		$num = '&times;';
	}

	$params_span = '';
	$data_attr   = '';

	if ( is_array( $params ) && $nbr_params = count( $params ) ) {
		$params_txt = '';
		foreach ( $params as $param => $val ) {
			$params_txt .= sprintf( __( '%1$s (%2$s)', 'sf-adminbar-tools' ), $param, $val ) . ', ';
		}
		$data_attr   = $nbr_params > 1 ? ' data-nbrparams="' . $nbr_params . '"' : '';
		$params_span = ' <span class="action-indic" title="' . sprintf( _n( 'Parameter: %s', 'Parameters: %s', $nbr_params, 'sf-adminbar-tools' ), trim( $params_txt, ', ' ) ) . '"><span class="action-count">P</span></span>';
	}

	return '<span class="action-indic"><span class="action-count">' . $num . '</span></span>' . $params_span . ' <input class="no-adminbar-style" type="text" style="min-width:' . strlen( $hook ) . 'ch" readonly="readonly" autocomplete="off" value="' . $hook . '"' . $data_attr . '/>';
}
