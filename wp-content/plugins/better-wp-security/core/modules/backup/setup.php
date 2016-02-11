<?php

if ( ! class_exists( 'ITSEC_Backup_Setup' ) ) {

	class ITSEC_Backup_Setup {

		private
			$defaults;

		public function __construct() {

			add_action( 'itsec_modules_do_plugin_activation',   array( $this, 'execute_activate'   )          );
			add_action( 'itsec_modules_do_plugin_deactivation', array( $this, 'execute_deactivate' )          );
			add_action( 'itsec_modules_do_plugin_uninstall',    array( $this, 'execute_uninstall'  )          );
			add_action( 'itsec_modules_do_plugin_upgrade',      array( $this, 'execute_upgrade'    ), null, 2 );

			global $itsec_globals;

			$this->defaults = array(
				'enabled'   => false,
				'interval'  => 3,
				'all_sites' => false,
				'method'    => 1,
				'location'  => $itsec_globals['ithemes_backup_dir'],
				'last_run'  => 0,
				'zip'       => true,
				'exclude'   => array(
					'itsec_log',
					'itsec_temp',
					'itsec_lockouts',
				),
				'retain'    => 0,
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

			$options = get_site_option( 'itsec_backup' );

			if ( $options === false ) {

				add_site_option( 'itsec_backup', $this->defaults );

			}

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

			delete_site_option( 'itsec_backup' );

		}

		/**
		 * Execute module upgrade
		 *
		 * @return void
		 */
		public function execute_upgrade( $itsec_old_version ) {

			if ( $itsec_old_version < 4000 ) {

				global $itsec_bwps_options;

				$current_options = get_site_option( 'itsec_backup' );

				if ( $current_options === false ) {
					$current_options = $this->defaults;
				}

				$current_options['enabled']  = isset( $itsec_bwps_options['backup_enabled'] ) && $itsec_bwps_options['backup_enabled'] == 1 ? true : false;
				$current_options['interval'] = isset( $itsec_bwps_options['backup_interval'] ) ? intval( $itsec_bwps_options['backup_interval'] ) : 1;

				update_site_option( 'itsec_backup', $current_options );

			}

		}

	}

}

new ITSEC_Backup_Setup();