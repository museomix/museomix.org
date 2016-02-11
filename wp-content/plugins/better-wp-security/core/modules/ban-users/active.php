<?php
// Set up Away Mode Admin
require_once( 'class-itsec-ban-users-admin.php' );
$itsec_ban_users_admin = new ITSEC_Ban_Users_Admin();
$itsec_ban_users_admin->run( ITSEC_Core::get_instance() );

// Set up Away Mode Frontend
require_once( 'class-itsec-ban-users.php' );
$itsec_ban_users = new ITSEC_Ban_Users();
$itsec_ban_users->run();
