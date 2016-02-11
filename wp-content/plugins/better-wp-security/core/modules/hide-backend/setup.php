<?php

if ( ! class_exists( 'ITSEC_Hide_Backend_Setup' ) ) {

	class ITSEC_Hide_Backend_Setup {

		private
			$defaults;

		public function __construct() {

			add_action( 'itsec_modules_do_plugin_activation',   array( $this, 'execute_activate'   )          );
			add_action( 'itsec_modules_do_plugin_deactivation', array( $this, 'execute_deactivate' )          );
			add_action( 'itsec_modules_do_plugin_uninstall',    array( $this, 'execute_uninstall'  )          );
			add_action( 'itsec_modules_do_plugin_upgrade',      array( $this, 'execute_upgrade'    ), null, 2 );

			$this->defaults = array(
				'enabled'           => false,
				'slug'              => 'wplogin',
				'register'          => 'wp-register.php',
				'theme_compat'      => true,
				'theme_compat_slug' => 'not_found',
				'post_logout_slug'  => '',
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

			$options = get_site_option( 'itsec_hide_backend' );

			if ( is_multisite() ) {

				switch_to_blog( 1 );

				$bwps_options = get_option( 'bit51_bwps' );

				restore_current_blog();

			} else {

				$bwps_options = get_option( 'bit51_bwps' );

			}

			if ( $bwps_options !== false && isset( $bwps_options['hb_enabled'] ) && $bwps_options['hb_enabled'] == 1 ) {

				$this->defaults['show-tooltip'] = true;
				define( 'ITSEC_SHOW_HIDE_BACKEND_TOOLTIP', true );

			} else {

				$this->defaults['show-tooltip'] = false;

			}

			if ( $options === false ) {

				add_site_option( 'itsec_hide_backend', $this->defaults );

			}

			add_site_option( 'itsec_rewrites_changed', true );

		}

		/**
		 * Execute module deactivation
		 *
		 * @return void
		 */
		public function execute_deactivate() {

			delete_site_transient( 'ITSEC_SHOW_HIDE_BACKEND_TOOLTIP' );
			delete_site_option( 'itsec_hide_backend_new_slug' );

		}

		/**
		 * Execute module uninstall
		 *
		 * @return void
		 */
		public function execute_uninstall() {

			$this->execute_deactivate();

			delete_site_option( 'itsec_hide_backend' );

		}

		/**
		 * Execute module upgrade
		 *
		 * @return void
		 */
		public function execute_upgrade( $itsec_old_version ) {

			if ( $itsec_old_version < 4000 ) {

				global $itsec_bwps_options;

				$current_options = get_site_option( 'itsec_hide_backend' );

				if ( $current_options === false ) {
					$current_options = $this->defaults;
				}

				$current_options['enabled']  = isset( $itsec_bwps_options['hb_enabled'] ) && $itsec_bwps_options['hb_enabled'] == 1 ? true : false;
				$current_options['register'] = isset( $itsec_bwps_options['hb_register'] ) ? sanitize_text_field( $itsec_bwps_options['hb_register'] ) : 'wp-register.php';

				if ( $current_options['enabled'] === true ) {

					$current_options['show-tooltip'] = true;
					set_site_transient( 'ITSEC_SHOW_HIDE_BACKEND_TOOLTIP', true, 600 );

				} else {

					$current_options['show-tooltip'] = false;

				}

				$forbidden_slugs = array( 'admin', 'login', 'wp-login.php', 'dashboard', 'wp-admin', '' );

				if ( isset( $itsec_bwps_options['hb_login'] ) && ! in_array( trim( $itsec_bwps_options['hb_login'] ), $forbidden_slugs ) ) {

					$current_options['slug'] = $itsec_bwps_options['hb_login'];
					set_site_transient( 'ITSEC_SHOW_HIDE_BACKEND_TOOLTIP', true, 600 );

				} else {

					$current_options['enabled'] = false;
					set_site_transient( 'ITSEC_SHOW_HIDE_BACKEND_TOOLTIP', true, 600 );

				}

				update_site_option( 'itsec_hide_backend', $current_options );
				add_site_option( 'itsec_rewrites_changed', true );

			}

			if ( $itsec_old_version < 4027 ) {

				$current_options = get_site_option( 'itsec_hide_backend' );

				if ( isset( $current_options['enabled'] ) && $current_options['enabled'] === true ) {

					$config_file = ITSEC_Lib::get_htaccess();

					//Make sure we can write to the file
					$perms = substr( sprintf( '%o', @fileperms( $config_file ) ), - 4 );

					@chmod( $config_file, 0664 );

					add_action( 'admin_init', array( $this, 'flush_rewrite_rules' ) );

					//reset file permissions if we changed them
					if ( $perms == '0444' ) {
						@chmod( $config_file, 0444 );
					}

					add_site_option( 'itsec_rewrites_changed', true );

				}

			}

		}

		/**
		 * Flush rewrite rules.
		 *
		 * @since 4.0.6
		 *
		 * @return void
		 */
		public function flush_rewrite_rules() {

			flush_rewrite_rules();
		}

	}

}

new ITSEC_Hide_Backend_Setup();
