<?php
// Set up Tweaks Admin
require_once( 'class-itsec-tweaks-admin.php' );
$itsec_tweaks_admin = new ITSEC_Tweaks_Admin();
$itsec_tweaks_admin->run( ITSEC_Core::get_instance() );

// Set up Tweaks Frontend
require_once( 'class-itsec-tweaks.php' );
$itsec_tweaks = new ITSEC_Tweaks();
$itsec_tweaks->run();
