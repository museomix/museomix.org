<?php
// Set up Strong Passwords Admin
require_once( 'class-itsec-strong-passwords-admin.php' );
$itsec_strong_passwords_admin = new ITSEC_Strong_Passwords_Admin();
$itsec_strong_passwords_admin->run( ITSEC_Core::get_instance() );

// Set up Strong Passwords Frontend
require_once( 'class-itsec-strong-passwords.php' );
$itsec_strong_passwords = new ITSEC_Strong_Passwords();
$itsec_strong_passwords->run( ITSEC_Core::get_instance() );
