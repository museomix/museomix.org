<?php

if ( ! class_exists( 'ITSEC_Ban_Users_Setup' ) ) {

	class ITSEC_Ban_Users_Setup {

		private
			$defaults;

		public function __construct() {
			add_action( 'itsec_modules_do_plugin_activation',   array( $this, 'execute_activate'   )          );
			add_action( 'itsec_modules_do_plugin_deactivation', array( $this, 'execute_deactivate' )          );
			add_action( 'itsec_modules_do_plugin_uninstall',    array( $this, 'execute_uninstall'  )          );
			add_action( 'itsec_modules_do_plugin_upgrade',      array( $this, 'execute_upgrade'    ), null, 2 );

			$this->defaults = array(
				'enabled'    => false,
				'default'    => false,
				'host_list'  => array(),
				'agent_list' => array(),
			);

		}

		/**
		 * Execute module activation.
		 *
		 * @since 4.0
		 *
		 * @return void
		 */
		public function execute_activate() {

			$options = get_site_option( 'itsec_ban_users' );

			if ( $options === false ) {

				add_site_option( 'itsec_ban_users', $this->defaults );

			}

			add_site_option( 'itsec_rewrites_changed', true );

		}

		/**
		 * Execute module deactivation
		 *
		 * @return void
		 */
		public function execute_deactivate() {

		}

		/**
		 * Execute module uninstall
		 *
		 * @return void
		 */
		public function execute_uninstall() {

			$this->execute_deactivate();

			delete_site_option( 'itsec_ban_users' );

		}

		/**
		 * Execute module upgrade
		 *
		 * @return void
		 */
		public function execute_upgrade( $itsec_old_version ) {

			if ( $itsec_old_version < 4000 ) {

				global $itsec_bwps_options;

				$current_options = get_site_option( 'itsec_ban_users' );

				if ( $current_options === false ) {
					$current_options = $this->defaults;
				}

				$current_options['enabled'] = isset( $itsec_bwps_options['bu_enabled'] ) && $itsec_bwps_options['bu_enabled'] == 1 ? true : false;
				$current_options['default'] = isset( $itsec_bwps_options['bu_blacklist'] ) && $itsec_bwps_options['bu_blacklist'] == 1 ? true : false;

				if ( isset( $itsec_bwps_options['bu_banlist'] ) && ! is_array( $itsec_bwps_options['bu_banlist'] ) && strlen( $itsec_bwps_options['bu_banlist'] ) > 1 ) {

					$raw_hosts = explode( PHP_EOL, $itsec_bwps_options['bu_banlist'] );

					foreach ( $raw_hosts as $host ) {

						if ( strlen( $host ) > 1 ) {
							$current_options['host_list'][] = $host;
						}

					}

				}

				if ( isset( $itsec_bwps_options['bu_banagent'] ) && ! is_array( $itsec_bwps_options['bu_banagent'] ) && strlen( $itsec_bwps_options['bu_banagent'] ) > 1 ) {

					$current_options['agent_list'] = explode( PHP_EOL, $itsec_bwps_options['bu_banagent'] );

					$raw_agents = explode( PHP_EOL, $itsec_bwps_options['bu_banagent'] );

					foreach ( $raw_agents as $agent ) {

						if ( strlen( $agent ) > 1 ) {
							$current_options['agent_list'][] = $agent;
						}

					}

				}

				update_site_option( 'itsec_ban_users', $current_options );
				add_site_option( 'itsec_rewrites_changed', true );

			}

			if ( $itsec_old_version < 4027 ) {

				add_site_option( 'itsec_rewrites_changed', true );

			}

		}

	}

}

new ITSEC_Ban_Users_Setup();