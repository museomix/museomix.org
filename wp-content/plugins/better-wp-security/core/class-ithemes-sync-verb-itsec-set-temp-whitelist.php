<?php

class Ithemes_Sync_Verb_ITSEC_Set_Temp_Whitelist extends Ithemes_Sync_Verb {

	public static $name        = 'itsec-set-temp-whitelist';
	public static $description = 'Set temporarily whitelisted IP.';

	public $default_arguments = array(
		'direction' => 'add', //whether to "add" or "remove" whitelist
		'ip'        => '' //IP to add or remove
	);

	public function run( $arguments ) {

		global $itsec_globals;

		$direction = isset( $arguments['direction'] ) ? $arguments['direction'] : 'add';

		if ( $direction === 'add' ) {

			if ( get_site_option( 'itsec_temp_whitelist_ip' ) !== false || ! isset( $arguments['ip'] ) ) {
				return false;
			}

			$ip = sanitize_text_field( $arguments['ip'] );

			if ( ! class_exists( 'ITSEC_Lib_IP_Tools' ) ) {
				$itsec_core = ITSEC_Core::get_instance();
				require_once( dirname( $itsec_core->get_plugin_file() ) . '/core/lib/class-itsec-lib-ip-tools.php' );
			}

			if ( ITSEC_Lib_IP_Tools::validate( $ip ) ) {

				$response = array(
					'ip'  => $ip,
					'exp' => $itsec_globals['current_time'] + 86400,
				);

				add_site_option( 'itsec_temp_whitelist_ip', $response );

				return true;

			}

		} elseif ( $direction === 'remove' ) {

			delete_site_option( 'itsec_temp_whitelist_ip' );

			return true;

		}

		return false;

	}

}