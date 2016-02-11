<?php

if ( ! class_exists( 'ITSEC_Brute_Force_Setup' ) ) {

	class ITSEC_Brute_Force_Setup {

		private
			$defaults;

		public function __construct() {

			add_action( 'itsec_modules_do_plugin_activation',   array( $this, 'execute_activate'   )          );
			add_action( 'itsec_modules_do_plugin_deactivation', array( $this, 'execute_deactivate' )          );
			add_action( 'itsec_modules_do_plugin_uninstall',    array( $this, 'execute_uninstall'  )          );
			add_action( 'itsec_modules_do_plugin_upgrade',      array( $this, 'execute_upgrade'    ), null, 2 );

			$this->defaults = array(
				'enabled'           => false,
				'max_attempts_host' => 5,
				'max_attempts_user' => 10,
				'check_period'      => 5,
				'auto_ban_admin'    => false,
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

			$options = get_site_option( 'itsec_brute_force' );

			if ( $options === false ) {

				add_site_option( 'itsec_brute_force', $this->defaults );

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

			delete_site_option( 'itsec_brute_force' );

		}

		/**
		 * Execute module upgrade
		 *
		 * @return void
		 */
		public function execute_upgrade( $itsec_old_version ) {

			if ( $itsec_old_version < 4000 ) {

				global $itsec_bwps_options;

				$current_options = get_site_option( 'itsec_brute_force' );

				if ( $current_options === false ) {
					$current_options = $this->defaults;
				}

				$current_options['enabled']           = isset( $itsec_bwps_options['ll_enabled'] ) && $itsec_bwps_options['ll_enabled'] == 1 ? true : false;
				$current_options['max_attempts_host'] = isset( $itsec_bwps_options['ll_maxattemptshost'] ) ? intval( $itsec_bwps_options['ll_maxattemptshost'] ) : 5;
				$current_options['max_attempts_user'] = isset( $itsec_bwps_options['ll_maxattemptsuser'] ) ? intval( $itsec_bwps_options['ll_maxattemptsuser'] ) : 10;
				$current_options['check_period']      = isset( $itsec_bwps_options['ll_checkinterval'] ) ? intval( $itsec_bwps_options['ll_checkinterval'] ) : 5;

				update_site_option( 'itsec_brute_force', $current_options );

			}

		}

	}

}

new ITSEC_Brute_Force_Setup();