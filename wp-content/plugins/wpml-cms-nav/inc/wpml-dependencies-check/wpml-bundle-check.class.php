<?php
/*
Module Name: WPML Dependency Check Module
Description: This is not a plugin! This module must be included in other plugins (WPML and add-ons) to handle compatibility checks
Author: OnTheGoSystems
Author URI: http://www.onthegosystems.com/
Version: 1.0
*/

if ( ! function_exists( 'wpml_filter_include_url' ) ) {
	function wpml_filter_include_url( $result ) {
		if ( isset( $_SERVER[ 'HTTP_HOST' ] ) ) {
			$http_host_parts = explode( ':', $_SERVER[ 'HTTP_HOST' ] );
			unset( $http_host_parts[ 1 ] );
			$http_host_without_port = implode( $http_host_parts );
			$path                   = str_replace( parse_url( $result, PHP_URL_HOST ), $http_host_without_port, $result );
		} else {
			$path = '';
		}

		return $path;
	}
}

if ( ! class_exists( 'WPML_Bundle_Check' ) ) {

	define( 'WPML_DEP_CHECK_URL', wpml_filter_include_url( untrailingslashit( plugin_dir_url( __FILE__ ) ) ) );

	class WPML_Bundle_Check {
		private static $instance;
		private        $admin_notice;
		private        $bundle;
		private        $current_product;
		private        $current_version;
		private        $expected_versions;
		private        $installed_plugins;
		private        $invalid_plugins;
		private        $valid_plugins;
		private        $validation_results;

		private function __construct() {
			if ( ! isset( $instance ) ) {
				$this->init_fields();
				$this->init_bundle();
				$this->init_hooks();
			}
		}

		public function admin_notices_action() {
			if ( is_admin() && ! $this->is_doing_ajax_cron_or_xmlrpc() ) {
				if ( $this->admin_notice ) {
					echo $this->admin_notice;
				}
			}
		}

		public function extra_plugin_headers_action( $extra_headers = array() ) {
			$new_extra_header = array(
				'PluginSlug' => 'Plugin Slug',
			);
			$all_headers      = array_merge( $new_extra_header, (array) $extra_headers );

			return $all_headers;
		}

		public function get_expected_versions() {
			return $this->expected_versions;
		}

		/**
		 * @return WPML_Bundle_Check
		 */
		public static function get_instance() {
			if ( ! isset( self::$instance ) ) {
				self::$instance = new WPML_Bundle_Check();
			}

			return self::$instance;
		}

		public function get_plugins() {
			return $this->installed_plugins;
		}

		public function get_plugins_validation() {
			if ( $this->installed_plugins ) {
				foreach ( $this->installed_plugins as $plugin => $version ) {
					$this->current_product = $plugin;
					if ( $this->is_valid_plugin() ) {
						$this->current_version               = $version;
						$validation_result                   = $this->is_plugin_version_valid();
						$this->validation_results[ $plugin ] = $validation_result;
					}
				}
			}

			return $this->validation_results;
		}

		public function has_invalid_plugins() {
			return isset( $this->invalid_plugins ) && count( $this->invalid_plugins );
		}

		public function init_plugins_action() {
			if ( is_admin() && ! $this->is_doing_ajax_cron_or_xmlrpc() ) {
				$this->init_plugins();
			}
		}

		public function is_plugin_version_valid() {
			$expected_version = $this->get_expected_product_version();

			return $expected_version ? version_compare( $this->current_version, $expected_version ) : null;
		}

		public function validate_plugins_action() {
			if ( is_admin() && ! $this->is_doing_ajax_cron_or_xmlrpc() ) {
				$this->validate_plugins();
			}
		}

		/**
		 * @param $plugin
		 */
		private function add_installed_plugin( $plugin ) {
			$data       = get_plugin_data( $plugin );
			$plugin_dir = dirname( WP_PLUGIN_DIR . DIRECTORY_SEPARATOR . $plugin );
			if ( WP_PLUGIN_DIR . DIRECTORY_SEPARATOR != $plugin_dir ) {
				$plugin_folder = str_replace( WP_PLUGIN_DIR . DIRECTORY_SEPARATOR, '', $plugin_dir );
				$plugin_slug   = $this->guess_plugin_slug( $data, $plugin_folder );
				if ( $this->is_valid_plugin( $plugin_slug ) ) {
					$this->installed_plugins[ $plugin_slug ] = $data[ 'Version' ];
				}
			}
		}

		/**
		 * @return bool
		 */
		private function doing_ajax() {
			return ( defined( 'DOING_AJAX' ) && DOING_AJAX );
		}

		/**
		 * @return bool
		 */
		private function doing_cron() {
			return ( defined( 'DOING_CRON' ) && DOING_CRON );
		}

		/**
		 * @return bool
		 */
		private function doing_xmlrpc() {
			return ( defined( 'XMLRPC_REQUEST' ) && XMLRPC_REQUEST );
		}

		private function get_expected_product_version( $product = false ) {
			$result = null;

			if ( ! $product ) {
				$product = $this->current_product;
			}
			if ( $product ) {
				$versions = $this->get_expected_versions();

				$result = isset( $versions[ $product ] ) ? $versions[ $product ] : null;
			}

			return $result;
		}

		/**
		 * @return string|void
		 */
		private function get_invalid_plugins_report_footer() {
			$wpml_org_url = '<a href="https://wpml.org/account/" title="WPML.org account">' . __( 'WPML.org account', 'sitepress' ) . '</a>';

			$notice_paragraph = __( 'Your site will not work as it should in this configuration', 'sitepress' );
			$notice_paragraph .= ' ';
			$notice_paragraph .= __( 'Please update all WPML components which you are using.', 'sitepress' );
			$notice_paragraph .= ' ';
			$notice_paragraph .= sprintf( __( 'You can receive updates from your %s or automatically, after you register WPML.', 'sitepress' ), $wpml_org_url );

			return $notice_paragraph;
		}

		/**
		 * @return string|void
		 */
		private function get_invalid_plugins_report_header() {
			if ( $this->has_valid_plugins() ) {
				if ( count( $this->valid_plugins ) == 1 ) {
					$paragraph = __( 'You are running updated %s, but the following WPML component is not updated yet:', 'sitepress' );
					$paragraph = sprintf( $paragraph, '<strong>' . $this->valid_plugins[ 0 ] . '</strong>' );

					return $paragraph;
				} else {
					$paragraph           = __( 'You are running updated %s and %s, but the following WPML components are not updated yet:', 'sitepress' );
					$first_valid_plugins = implode( ', ', array_slice( $this->valid_plugins, 0, - 1 ) );
					$last_valid_plugin   = array_slice( $this->valid_plugins, - 1 );
					$paragraph           = sprintf( $paragraph, '<strong>' . $first_valid_plugins . '</strong>', '<strong>' . $last_valid_plugin[ 0 ] . '</strong>' );

					return $paragraph;
				}
			} else {
				$paragraph = __( 'The following WPML components are not updated yet:', 'sitepress' );

				return $paragraph;
			}
		}

		/**
		 * @return string
		 */
		private function get_invalid_plugins_report_list() {
			$invalid_plugins_list = '<ul class="ul-disc">';
			foreach($this->invalid_plugins as $invalid_plugin) {
				$plugin_name_html = '<li data-installed-version="' . $this->installed_plugins[ $invalid_plugin ] . '">';
				$plugin_name_html .= $invalid_plugin;
				$plugin_name_html .= "</li>";

				$invalid_plugins_list .= $plugin_name_html;
			}
			$invalid_plugins_list .= '</ul>';

			return $invalid_plugins_list;
		}

		private function get_plugin_slug_from_name( $plugin_name ) {
			$plugin_slug = null;
			foreach ( $this->bundle as $bundle_version ) {
				if ( $bundle_version[ 'name' ] == $plugin_name ) {
					$plugin_slug = $bundle_version[ 'product' ];
					break;
				}
			}

			return $plugin_slug;
		}

		/**
		 * @param $plugin_data
		 * @param $plugin_folder
		 *
		 * @return mixed
		 */
		private function guess_plugin_slug( $plugin_data, $plugin_folder ) {
			$plugin_slug = null;
			if ( isset( $plugin_data[ 'Plugin Slug' ] ) && $plugin_data[ 'Plugin Slug' ] ) {
				$plugin_slug = $plugin_data[ 'Plugin Slug' ];
			} elseif ( isset( $plugin_data[ 'Name' ] ) && $plugin_data[ 'Name' ] ) {
				$plugin_slug = $this->get_plugin_slug_from_name( $plugin_data[ 'Name' ] );
			} else {
				$plugin_slug = $plugin_folder;
			}

			return $plugin_slug;
		}

		/**
		 * @return bool
		 */
		private function has_valid_plugins() {
			return $this->valid_plugins && count( $this->valid_plugins );
		}

		private function init_bundle() {
			$bundle_versions_json = file_get_contents( dirname( __FILE__ ) . '/bundle.json' );
			$this->bundle         = json_decode( $bundle_versions_json, true );

			foreach ( $this->bundle as $bundle_version ) {
				$this->expected_versions[ $bundle_version[ 'product' ] ] = $bundle_version[ 'version' ];
			}
		}

		private function init_fields() {
			$this->expected_versions  = array();
			$this->validation_results = array();
			$this->valid_plugins      = array();
			$this->invalid_plugins    = array();
			$this->admin_notice       = null;
		}

		private function init_hooks() {
			add_action( 'init', array( $this, 'init_plugins_action' ) );
			add_action( 'init', array( $this, 'validate_plugins_action' ) );
			add_action( 'extra_plugin_headers', array( $this, 'extra_plugin_headers_action' ) );
			add_action( 'admin_notices', array( $this, 'admin_notices_action' ) );
		}

		private function init_plugins() {
			if ( ! isset( $this->installed_plugins ) || ! $this->installed_plugins ) {
				if ( ! function_exists( 'get_plugin_data' ) ) {
					include_once ABSPATH . '/wp-admin/includes/plugin.php';
				}
				if ( function_exists( 'get_plugin_data' ) ) {
					$active_plugins = wp_get_active_and_valid_plugins();
					foreach ( $active_plugins as $plugin ) {
						$this->add_installed_plugin( $plugin );
					}
				}
			}
		}

		/**
		 * @return bool
		 */
		private function is_doing_ajax_cron_or_xmlrpc() {
			return ( $this->doing_ajax() || $this->doing_cron() || $this->doing_xmlrpc() );
		}

		private function is_valid_plugin( $product = false ) {
			$result = false;

			if ( ! $product ) {
				$product = $this->current_product;
			}
			if ( $product ) {
				$versions = $this->get_expected_versions();
				$result   = isset( $versions[ $product ] );
			}

			return $result;
		}

		private function maybe_init_admin_notice() {
			$this->admin_notice = null;
			if ( $this->has_invalid_plugins() ) {
				$notice_paragraphs = array();

				$notice_paragraphs[ ] = $this->get_invalid_plugins_report_header();
				$notice_paragraphs[ ] = $this->get_invalid_plugins_report_list();
				$notice_paragraphs[ ] = $this->get_invalid_plugins_report_footer();;

				$this->admin_notice = '<div class="error wpml-admin-notice">';
				$this->admin_notice .= '<h3>' . __( 'WPML Update is Incomplete', 'sitepress' ) . '</h3>';
				$this->admin_notice .= '<p>' . implode( '</p><p>', $notice_paragraphs ) . '</p>';
				$this->admin_notice .= '</div>';
			}
		}

		private function validate_plugins() {
			$validation_results = $this->get_plugins_validation();

			$this->valid_plugins   = array();
			$this->invalid_plugins = array();
			foreach ( $validation_results as $plugin => $validation_result ) {
				if ( $validation_result == 0 ) {
					$this->valid_plugins[ ] = $plugin;
				} else {
					$this->invalid_plugins[ ] = $plugin;
				}
			}
			$this->maybe_init_admin_notice();
		}
	}

	$WPML_Bundle_Check = WPML_Bundle_Check::get_instance();
}