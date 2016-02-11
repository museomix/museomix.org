<?php
// Set up Away Mode Admin
require_once( 'class-itsec-away-mode-admin.php' );
$itsec_away_mode_admin = new ITSEC_Away_Mode_Admin();
$itsec_away_mode_admin->run( ITSEC_Core::get_instance() );

// Set up Away Mode Frontend
require_once( 'class-itsec-away-mode.php' );
$itsec_away_mode = new ITSEC_Away_Mode();
$itsec_away_mode->run();
