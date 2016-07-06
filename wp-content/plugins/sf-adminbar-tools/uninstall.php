<?php
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit();
}


delete_option( 'sf-abt-open-files' );
delete_option( '_sf_abt' );
delete_metadata( 'user', 0, 'sf-abt-coworking', null, true );
delete_metadata( 'user', 0, 'sf-abt-no-autosave', null, true );
delete_metadata( 'user', 0, 'sf-abt-no-cowork-refresh', null, true );
