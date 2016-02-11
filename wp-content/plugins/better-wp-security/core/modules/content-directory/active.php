<?php
// Set up Content Directory Admin
require_once( 'class-itsec-content-directory-admin.php' );
$itsec_content_directory_admin = new ITSEC_Content_Directory_Admin();
$itsec_content_directory_admin->run( ITSEC_Core::get_instance() );
