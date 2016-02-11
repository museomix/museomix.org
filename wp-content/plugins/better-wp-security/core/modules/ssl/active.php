<?php
// Set up SSL Admin
require_once( 'class-itsec-ssl-admin.php' );
$itsec_ssl_admin = new ITSEC_SSL_Admin();
$itsec_ssl_admin->run( ITSEC_Core::get_instance() );

// Only include the front end if SSL is turned on
if ( $itsec_ssl_admin->enabled() ) {
	// Set up SSL Frontend
	require_once( 'class-itsec-ssl.php' );
	$itsec_ssl = new ITSEC_SSL();
	$itsec_ssl->run();
}