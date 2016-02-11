<?php
// Set up Content Directory Admin
require_once( 'class-itsec-salts-admin.php' );
$itsec_salts_admin = new ITSEC_Salts_Admin();
$itsec_salts_admin->run( ITSEC_Core::get_instance() );
