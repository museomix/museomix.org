<?php

/*
 * Plugin Name: iThemes Security
 * Plugin URI: https://ithemes.com/security
 * Description: Protect your WordPress site by hiding vital areas of your site, protecting access to important files, preventing brute-force login attempts, detecting attack attempts and more.
 * Author: iThemes
 * Author URI: https://ithemes.com
 * Version: 5.1.1
 * Text Domain: better-wp-security
 * Network: True
 * License: GPLv2
 */


$itsec_dir = dirname( __FILE__ );

$locale = apply_filters( 'plugin_locale', get_locale(), 'better-wp-security' );
load_textdomain( 'better-wp-security', WP_LANG_DIR . "/plugins/better-wp-security/better-wp-security-$locale.mo" );
load_plugin_textdomain( 'better-wp-security' );

if ( is_admin() ) {
	require( "$itsec_dir/lib/icon-fonts/load.php" );
	require( "$itsec_dir/lib/one-version/index.php" );
}

require( "$itsec_dir/core/class-itsec-core.php" );
new ITSEC_Core( __FILE__, __( 'iThemes Security', 'better-wp-security' ) );
