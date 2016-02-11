<?php
// Set up IP Check Admin
require_once( 'class-itsec-ipcheck-admin.php' );
$itsec_ip_check_admin = new ITSEC_IPCheck_Admin();
$itsec_ip_check_admin->run( ITSEC_Core::get_instance() );

// Set up IP Check Frontend
require_once( 'class-itsec-ipcheck.php' );
$itsec_ip_check = new ITSEC_IPCheck( ITSEC_Core::get_instance() );
$itsec_ip_check->run();
