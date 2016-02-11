<?php
// Set up Brute Force Admin
require_once( 'class-itsec-four-oh-four-admin.php' );
$itsec_404_detection_admin = new ITSEC_Four_Oh_Four_Admin();
$itsec_404_detection_admin->run( ITSEC_Core::get_instance() );

// Set up Brute Force Frontend
require_once( 'class-itsec-four-oh-four.php' );
$itsec_404_detection = new ITSEC_Four_Oh_Four();
$itsec_404_detection->run();
