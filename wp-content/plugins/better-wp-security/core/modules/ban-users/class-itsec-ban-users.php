<?php

class ITSEC_Ban_Users {

	function run() {

		return null;
	}

	/**
	 * Inserts an IP address into the htaccess ban list.
	 *
	 * @since 4.0
	 *
	 * @param      $ip
	 * @param null $ban_list
	 * @param null $white_list
	 *
	 * @return void
	 */
	public static function insert_ip( $ip, $ban_list = null, $white_list = null ) {

		$settings = get_site_option( 'itsec_ban_users' );

		$host = sanitize_text_field( $ip );

		if ( $ban_list === null ) {

			$ban_list = isset( $settings['host_list'] ) ? $settings['host_list'] : array();

		}

		if ( $white_list === null ) {

			$global_settings = get_site_option( 'itsec_global' );

			$white_list = isset( $global_settings['lockout_white_list'] ) ? $global_settings['lockout_white_list'] : array();

		}

		if ( ! in_array( $host, $ban_list ) && ! ITSEC_Lib::is_ip_whitelisted( $host, $white_list ) ) {

			$ban_list[]            = $host;
			$settings['host_list'] = $ban_list;
			ITSEC_Files::quick_ban( $host );
			update_site_option( 'itsec_ban_users', $settings );
			add_site_option( 'itsec_rewrites_changed', true );

		}

	}
}