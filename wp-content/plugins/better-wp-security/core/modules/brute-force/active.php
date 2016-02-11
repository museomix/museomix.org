<?php
// Set up Brute Force Admin
require_once( 'class-itsec-brute-force-admin.php' );
$itsec_brute_force_admin = new ITSEC_Brute_Force_Admin();
$itsec_brute_force_admin->run( ITSEC_Core::get_instance() );

// Set up Brute Force Frontend
require_once( 'class-itsec-brute-force.php' );
$itsec_brute_force = new ITSEC_Brute_Force();
$itsec_brute_force->run();
