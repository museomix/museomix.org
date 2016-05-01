<?php
/*
Plugin Name: WPML String Translation
Plugin URI: https://wpml.org/
Description: Adds theme and plugins localization capabilities to WPML | <a href="https://wpml.org">Documentation</a> | <a href="https://wpml.org/version/wpml-3-2/">WPML 3.2 release notes</a>
Author: OnTheGoSystems
Author URI: http://www.onthegosystems.com/
Version: 2.3.7
Plugin Slug: wpml-string-translation
*/

if ( defined( 'WPML_ST_VERSION' ) ) {
	return;
}

define( 'WPML_ST_VERSION', '2.3.7' );

// Do not uncomment the following line!
// If you need to use this constant, use it in the wp-config.php file
//define( 'WPML_PT_VERSION_DEV', '2.2.3-dev' );

define( 'WPML_ST_PATH', dirname( __FILE__ ) );

require_once 'embedded/wpml/commons/autoloader.php';
$wpml_auto_loader_instance = WPML_Auto_Loader::get_instance();
$wpml_auto_loader_instance->register( WPML_ST_PATH . '/' );

require WPML_ST_PATH . '/inc/wpml-dependencies-check/wpml-bundle-check.class.php';

function wpml_st_core_loaded() {
	global $wpdb;
	new WPML_ST_TM_Jobs( $wpdb );
}

function load_wpml_st_basics() {
	global $WPML_String_Translation, $wpdb, $wpml_st_string_factory, $sitepress;
	$wpml_st_string_factory = new WPML_ST_String_Factory( $wpdb );

	require WPML_ST_PATH . '/inc/functions-load.php';
	require WPML_ST_PATH . '/inc/wpml-string-translation.class.php';
	require WPML_ST_PATH . '/inc/constants.php';

	$WPML_String_Translation = new WPML_String_Translation( $sitepress,
		$wpml_st_string_factory );
	$WPML_String_Translation->set_basic_hooks();

	require WPML_ST_PATH . '/inc/package-translation/wpml-package-translation.php';

	add_action( 'wpml_loaded', 'wpml_st_setup_label_menu_hooks', 10, 0 );
	add_action( 'wpml_loaded', 'wpml_st_core_loaded', 10 );
}

function wpml_st_verify_wpml() {
	$verifier     = new WPML_ST_Verify_Dependencies();
	$wpml_version = defined( 'ICL_SITEPRESS_VERSION' ) ? ICL_SITEPRESS_VERSION : false;
	$verifier->verify_wpml( $wpml_version );
}

add_action( 'wpml_before_init', 'load_wpml_st_basics' );
add_action( 'init', 'wpml_st_verify_wpml' );
