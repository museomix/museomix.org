<?php
// Set up Admin User Admin
require_once( 'class-itsec-admin-user-admin.php' );
$itsec_admin_user_admin = new ITSEC_Admin_User_Admin();
$itsec_admin_user_admin->run( ITSEC_Core::get_instance() );
