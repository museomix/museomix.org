<?php

class WPMDB_Utils {

	/**
	 * Test to see if executing an AJAX call specific to the WP Migrate DB family of plugins.
	 *
	 * @return bool
	 */
	public static function is_ajax() {
		// must be doing AJAX the WordPress way
		if ( ! defined( 'DOING_AJAX' ) || ! DOING_AJAX ) {
			return false;
		}

		// must be one of our actions -- e.g. core plugin (wpmdb_*), media files (wpmdbmf_*)
		if ( ! isset( $_POST['action'] ) || 0 !== strpos( $_POST['action'], 'wpmdb' ) ) {
			return false;
		}

		// must be on blog #1 (first site) if multisite
		if ( is_multisite() && 1 != get_current_site()->id ) {
			return false;
		}

		return true;
	}

	/**
	 * Checks if another version of WPMDB(Pro) is active and deactivates it.
	 * To be hooked on `activated_plugin` so other plugin is deactivated when current plugin is activated.
	 *
	 * @param string $plugin
	 *
	 */
	public static function deactivate_other_instances( $plugin ) {
		if ( ! in_array( basename( $plugin ), array( 'wp-migrate-db-pro.php', 'wp-migrate-db.php' ) ) ) {
			return;
		}

		$plugin_to_deactivate  = 'wp-migrate-db.php';
		$deactivated_notice_id = '1';
		if ( basename( $plugin ) == $plugin_to_deactivate ) {
			$plugin_to_deactivate  = 'wp-migrate-db-pro.php';
			$deactivated_notice_id = '2';
		}

		if ( is_multisite() ) {
			$active_plugins = (array) get_site_option( 'active_sitewide_plugins', array() );
			$active_plugins = array_keys( $active_plugins );
		} else {
			$active_plugins = (array) get_option( 'active_plugins', array() );
		}

		foreach ( $active_plugins as $basename ) {
			if ( false !== strpos( $basename, $plugin_to_deactivate ) ) {
				set_transient( 'wp_migrate_db_deactivated_notice_id', $deactivated_notice_id, 1 * HOUR_IN_SECONDS );
				deactivate_plugins( $basename );

				return;
			}
		}
	}

}
