<?php
// Set up Database Prefix Admin
require_once( 'class-itsec-database-prefix-admin.php' );
$itsec_database_prefix_admin = new ITSEC_Database_Prefix_Admin();
$itsec_database_prefix_admin->run( ITSEC_Core::get_instance() );
