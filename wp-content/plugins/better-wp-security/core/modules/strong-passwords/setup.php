<?php

if ( ! class_exists( 'ITSEC_Strong_Passwords_Setup' ) ) {

	class ITSEC_Strong_Passwords_Setup {

		private
			$defaults;

		public function __construct() {

			add_action( 'itsec_modules_do_plugin_activation',   array( $this, 'execute_activate'   )          );
			add_action( 'itsec_modules_do_plugin_deactivation', array( $this, 'execute_deactivate' )          );
			add_action( 'itsec_modules_do_plugin_uninstall',    array( $this, 'execute_uninstall'  )          );
			add_action( 'itsec_modules_do_plugin_upgrade',      array( $this, 'execute_upgrade'    ), null, 2 );

			$this->defaults = array(
				'enabled' => false,
				'roll'    => 'administrator',
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

			$options = get_site_option( 'itsec_strong_passwords' );

			if ( $options === false ) {

				add_site_option( 'itsec_strong_passwords', $this->defaults );

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

			delete_site_option( 'itsec_strong_passwords' );

		}

		/**
		 * Execute module upgrade
		 *
		 * @return void
		 */
		public function execute_upgrade( $itsec_old_version ) {

			if ( $itsec_old_version < 4000 ) {

				global $itsec_bwps_options;

				$current_options = get_site_option( 'itsec_strong_passwords' );

				if ( $current_options === false ) {
					$current_options = $this->defaults;
				}

				$current_options['enabled'] = isset( $itsec_bwps_options['st_enablepassword'] ) && $itsec_bwps_options['st_enablepassword'] == 1 ? true : false;
				$current_options['roll']    = isset( $itsec_bwps_options['st_passrole'] ) ? $itsec_bwps_options['st_passrole'] : 'administrator';

				update_site_option( 'itsec_strong_passwords', $current_options );

			}

		}

	}

}

new ITSEC_Strong_Passwords_Setup();