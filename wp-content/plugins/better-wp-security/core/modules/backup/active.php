<?php
// Set up Backup Admin
require_once( 'class-itsec-backup-admin.php' );
$itsec_backup_admin = new ITSEC_Backup_Admin();
$itsec_backup_admin->run( ITSEC_Core::get_instance() );

// Set up Backup Frontend
require_once( 'class-itsec-backup.php' );
$itsec_backup = new ITSEC_Backup();
$itsec_backup->run( ITSEC_Core::get_instance() );
