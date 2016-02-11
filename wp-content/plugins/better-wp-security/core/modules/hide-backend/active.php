<?php
// Set up Hide Backend Admin
require_once( 'class-itsec-hide-backend-admin.php' );
$itsec_hide_backend_admin = new ITSEC_Hide_Backend_Admin();
$itsec_hide_backend_admin->run( ITSEC_Core::get_instance() );

// Set up Hide Backend Frontend
require_once( 'class-itsec-hide-backend.php' );
$itsec_hide_backend = new ITSEC_Hide_Backend();
$itsec_hide_backend->run();
