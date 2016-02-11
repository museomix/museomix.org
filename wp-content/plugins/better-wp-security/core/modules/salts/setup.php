<?php

if ( ! class_exists( 'ITSEC_Salts_Setup' ) ) {

	class ITSEC_Salts_Setup {

		public function __construct() {

			add_action( 'itsec_modules_do_plugin_activation',   array( $this, 'execute_activate'   )          );
			add_action( 'itsec_modules_do_plugin_deactivation', array( $this, 'execute_deactivate' )          );
			add_action( 'itsec_modules_do_plugin_uninstall',    array( $this, 'execute_uninstall'  )          );
			add_action( 'itsec_modules_do_plugin_upgrade',      array( $this, 'execute_upgrade'    ), null, 2 );

		}

		/**
		 * Execute module activation.
		 *
		 * @since 4.7.0
		 *
		 * @return void
		 */
		public function execute_activate() {

		}

		/**
		 * Execute module deactivation
		 *
		 * @since 4.7.0
		 *
		 * @return void
		 */
		public function execute_deactivate() {

		}

		/**
		 * Execute module uninstall
		 *
		 * @since 4.7.0
		 *
		 * @return void
		 */
		public function execute_uninstall() {

			$this->execute_deactivate();

			delete_site_option( 'itsec_salts' );

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

new ITSEC_Salts_Setup();