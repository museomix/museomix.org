<?php

if ( ! class_exists( 'ITSEC_IPCheck_Setup' ) ) {

	class ITSEC_IPCheck_Setup {

		private
			$defaults;

		public function __construct() {

			add_action( 'itsec_modules_do_plugin_activation',   array( $this, 'execute_activate'   )          );
			add_action( 'itsec_modules_do_plugin_deactivation', array( $this, 'execute_deactivate' )          );
			add_action( 'itsec_modules_do_plugin_uninstall',    array( $this, 'execute_uninstall'  )          );
			add_action( 'itsec_modules_do_plugin_upgrade',      array( $this, 'execute_upgrade'    ), null, 2 );

			$this->defaults = array(
				'api_ban' => true,
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

			$options = get_site_option( 'itsec_ipcheck' );

			if ( $options === false ) {

				add_site_option( 'itsec_ipcheck', $this->defaults );

			}

		}

		/**
		 * Execute module deactivation
		 *
		 * @return void
		 */
		public function execute_deactivate() {

			global $wpdb;

			$wpdb->query( "DELETE FROM `" . $wpdb->base_prefix . "options` WHERE `option_name` LIKE ('%_itsec_ip_cache%')" );

		}

		/**
		 * Execute module uninstall
		 *
		 * @return void
		 */
		public function execute_uninstall() {

			$this->execute_deactivate();

			delete_site_option( 'itsec_ipcheck' );

		}

		/**
		 * Execute module upgrade
		 *
		 * @return void
		 */
		public function execute_upgrade() {
		}

	}

}

new ITSEC_IPCheck_Setup();