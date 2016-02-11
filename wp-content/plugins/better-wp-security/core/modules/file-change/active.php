<?php
// Set up File Change Admin
require_once( 'class-itsec-file-change-admin.php' );
$itsec_file_change_admin = new ITSEC_File_Change_Admin();
$itsec_file_change_admin->run( ITSEC_Core::get_instance() );

// Set up File Change Frontend
require_once( 'class-itsec-file-change.php' );
$itsec_file_change = new ITSEC_File_Change();
$itsec_file_change->run();
