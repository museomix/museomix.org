<?php

/**
 * Change WordPress Salts
 *
 * Sets up all administrative functions for the Change WordPress Salts feature
 * including fields, sanitation and all other privileged functions.
 *
 * @since   4.6.0
 *
 * @package iThemes_Security
 */
class ITSEC_Salts_Admin {

	/**
	 * The module's saved options
	 *
	 * @since  4.6.0
	 * @access private
	 * @var array
	 */
	private $settings;

	/**
	 * The core plugin class utilized in order to set up admin and other screens
	 *
	 * @since  4.6.0
	 * @access private
	 * @var ITSEC_Core
	 */
	private $core;

	/**
	 * The absolute web patch to the module's files
	 *
	 * @since  4.6.0
	 * @access private
	 * @var string
	 */
	private $module_path;

	/**
	 * Setup the module's administrative functionality
	 *
	 * Loads the file change detection module's priviledged functionality including
	 * changing the folder itself.
	 *
	 * @since 4.6.0
	 *
	 * @param ITSEC_Core $core The core plugin instance
	 *
	 * @return void
	 */
	function run( $core ) {

		$this->core        = $core;
		$this->module_path = ITSEC_Lib::get_module_path( __FILE__ );
		$this->settings    = false;

		add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts' ) ); //enqueue scripts for admin page
		add_action( 'itsec_add_admin_meta_boxes', array( $this, 'itsec_add_admin_meta_boxes' ) ); //add meta boxes to admin page
		add_filter( 'itsec_add_dashboard_status', array( $this, 'itsec_add_dashboard_status' ) ); //add information for plugin status
		add_filter( 'itsec_tracking_vars', array( $this, 'itsec_tracking_vars' ) ); //Usage information tracked via Google Analytics (opt-in)

		if ( ! empty( $_POST ) ) {
			add_action( 'itsec_admin_init', array( $this, 'initialize_admin' ) ); //Process the WordPress Salts change if a form has been submitted
		}

	}

	/**
	 * Add Files Admin Javascript
	 *
	 * Enqueues files used in the admin area for the content directory module
	 *
	 * @since 4.6.0
	 *
	 * @return void
	 */
	public function admin_enqueue_scripts() {

		global $itsec_globals;

		if ( isset( get_current_screen()->id ) && false !== strpos( get_current_screen()->id, 'security_page_toplevel_page_itsec_advanced' ) ) {

			wp_register_script( 'itsec_content_directory_js', $this->module_path . 'js/itsec-salts.js', array( 'jquery' ), $itsec_globals['plugin_build'] );
			wp_enqueue_script( 'itsec_content_directory_js' );

		}

	}

	/**
	 * Generate Salts
	 *
	 * Generates a random string using alpha-numeric and special characters with a length of 64 characters.
	 *
	 * @since  4.6.0
	 *
	 * @access private
	 *
	 * @return void
	 */
	private function generate_salts() {

		$salts = '';

		for ( $i = 0; 1 > $i; $i ++ ) {

			$salts .= ITSEC_Lib::get_random( 64, false, true ) . ' ';

		}

		return $salts;
	}

	/**
	 * Build wp-config.php rules
	 *
	 * Sets the array of wp-config.php rules that will either need to be replaced later.
	 *
	 * @since  4.6.0
	 *
	 * @access private
	 *
	 * @param  array $salts options to build rules from
	 *
	 * @return array         rules to write
	 */
	private function build_salts_rules() {

		$rules_array = array();

		$rules[] = array(
			'type' => 'replace', 'search_text' => 'AUTH_KEY',
			'rule' => "define('AUTH_KEY', '" . $this->generate_salts() . "');",
		);

		$rules[] = array(
			'type' => 'replace', 'search_text' => 'SECURE_AUTH_KEY',
			'rule' => "define('SECURE_AUTH_KEY', '" . $this->generate_salts() . "');",
		);
		$rules[] = array(
			'type' => 'replace', 'search_text' => 'LOGGED_IN_KEY',
			'rule' => "define('LOGGED_IN_KEY', '" . $this->generate_salts() . "');",
		);

		$rules[] = array(
			'type' => 'replace', 'search_text' => 'NONCE_KEY',
			'rule' => "define('NONCE_KEY', '" . $this->generate_salts() . "');",
		);

		$rules[] = array(
			'type' => 'replace', 'search_text' => 'AUTH_SALT',
			'rule' => "define('AUTH_SALT', '" . $this->generate_salts() . "');",
		);

		$rules[] = array(
			'type' => 'replace', 'search_text' => 'SECURE_AUTH_SALT',
			'rule' => "define('SECURE_AUTH_SALT', '" . $this->generate_salts() . "');",
		);

		$rules[] = array(
			'type' => 'replace', 'search_text' => 'LOGGED_IN_SALT',
			'rule' => "define('LOGGED_IN_SALT', '" . $this->generate_salts() . "');",
		);

		$rules[] = array(
			'type' => 'replace', 'search_text' => 'NONCE_SALT',
			'rule' => "define('NONCE_SALT', '" . $this->generate_salts() . "');",
		);

		$rules_array[] = array( 'type' => 'wpconfig', 'name' => 'Content Directory', 'rules' => $rules, );

		return $rules_array;
	}

	/**
	 * Add meta boxes to primary options pages
	 *
	 * Adds the module's meta settings box to the advanced page.
	 *
	 * @since 4.6.0
	 *
	 * @return void
	 */
	public function itsec_add_admin_meta_boxes() {

		add_meta_box(
			'salts_options',
			__( 'WordPress Salts', 'it-l10n-better-wp-security' ),
			array( $this, 'metabox_advanced_settings' ),
			'security_page_toplevel_page_itsec_advanced',
			'advanced',
			'core'
		);

	}

	/**
	 * Sets the status in the plugin dashboard
	 *
	 * Sets a low priority item for the module's functionality in the plugin
	 * dashboard.
	 *
	 * @since 4.6.0
	 *
	 * @param array $statuses array of existing plugin dashboard statuses
	 *
	 * @return array statuses
	 */
	public function itsec_add_dashboard_status( $statuses ) {

		if ( true === $this->settings ) {

			$status_array = 'low';
			$status       = array(
				'text' => __( 'Your WordPress Salts have not been changed. You should change them now.', 'it-l10n-better-wp-security' ),
				'link' => '#itsec_enable_salts', 'advanced' => true,
			);

		} else {

			$status_array = 'safe-low';
			$status       = array(
				'text' => __( 'You have recently changed your WordPress Salts.', 'it-l10n-better-wp-security' ),
				'link' => '#itsec_enable_salts', 'advanced' => true,
			);

		}

		array_push( $statuses[ $status_array ], $status );

		return $statuses;

	}

	/**
	 * Execute admin initializations
	 *
	 * @return void
	 */
	public function initialize_admin() {

		if ( ! $this->settings === true && isset( $_POST['itsec_enable_salts'] ) && $_POST['itsec_enable_salts'] == 'true' ) {

			if ( ! wp_verify_nonce( $_POST['wp_nonce'], 'ITSEC_admin_save' ) ) {

				die( __( 'Security check', 'it-l10n-better-wp-security' ) );

			}

			$this->process_salts();

		}

	}

	/**
	 * Adds fields that will be tracked for Google Analytics
	 *
	 * Allows the tracking of when the content directory has been changed (although
	 * not the new name of the directory) via our Google Analytics tracking
	 * system.
	 *
	 * @since 4.6.0
	 *
	 * @param array $vars tracking vars
	 *
	 * @return array tracking vars
	 */
	public function itsec_tracking_vars( $vars ) {

		$vars['salts'] = array(
			'enabled' => '0:b',
		);

		return $vars;

	}

	/**
	 * Render the settings metabox
	 *
	 * Displays the contents of the module's settings metabox on the "Advanced"
	 * page with all module options.
	 *
	 * @since 4.6.0
	 *
	 * @return void
	 */
	public function metabox_advanced_settings() {

		global $itsec_globals;

		$content = '';

		if ( false === $this->settings ) {

			$content .= '<p>' . __( 'A secret key makes your site harder to hack and access by adding random elements to the password.', 'it-l10n-better-wp-security' ) . '</p>';
			$content .= '<p>' . __( 'In simple terms, a secret key is a password with elements that make it harder to generate enough options to break through your security barriers. A password like "password" or "test" is simple and easily broken. A random, unpredictable password such as "88a7da62429ba6ad3cb3c76a09641fc" takes years to come up with the right combination. A salt is used to further enhance the security of the generated result.', 'it-l10n-better-wp-security' ) . '</p>';
			$content .= '<p><strong>' . __( 'Note that enabling this feature will log you out of your WordPress site.', 'it-l10n-better-wp-security' ) . '</strong></p>';
		}

		echo $content;

		if ( isset( $itsec_globals['settings']['write_files'] ) && true === $itsec_globals['settings']['write_files'] ) {
			?>

			<form method="post" action="?page=toplevel_page_itsec_advanced&settings-updated=true" class="itsec-form">

				<?php wp_nonce_field( 'ITSEC_admin_save', 'wp_nonce' ); ?>

				<table class="form-table">
					<tr valign="top">
						<th scope="row" class="settinglabel">
							<label for="itsec_enable_salts"><?php _e( 'Change WordPress Salts', 'it-l10n-better-wp-security' ); ?></label>
						</th>
						<td class="settingfield">
							<?php //username field ?>
							<input type="checkbox" id="itsec_enable_salts" name="itsec_enable_salts" value="true"/>

							<p class="description"><?php _e( 'Check this box to change your WordPress Salts.', 'it-l10n-better-wp-security' ); ?></p>
						</td>
					</tr>
				</table>
				<p class="submit">
					<input type="submit" class="button-primary" value="<?php _e( 'Change WordPress Salts', 'it-l10n-better-wp-security' ); ?>"/>
				</p>
			</form>

		<?php

		} else {

			$content = sprintf(
				'<p>%s <a href="?page=toplevel_page_itsec_settings">%s</a> %s',
				__( 'You must allow this plugin to write to the wp-config.php file on the', 'it-l10n-better-wp-security' ),
				__( 'Settings', 'it-l10n-better-wp-security' ),
				__( 'page to use this feature.', 'it-l10n-better-wp-security' )
			);

			echo $content;
		}

	}

	/**
	 * Sanitize and validate input
	 *
	 * @since 4.6.0
	 */
	public function process_salts() {

		global $itsec_files;

		//suppress error messages due to timing
		error_reporting( 0 );
		@ini_set( 'display_errors', 0 );

		$rules = $this->build_salts_rules();

		$itsec_files->set_wpconfig( $rules );

		$configs = $itsec_files->save_wpconfig();

		if ( is_array( $configs ) ) {

			if ( $configs['success'] === false ) {

				$type    = 'error';
				$message = $configs['text'];

				add_settings_error( 'itsec', esc_attr( 'settings_updated' ), $message, $type );

			}

			if ( ! $configs ) {

				$type    = 'error';
				$message = __( 'Unable change the WordPress Salts. Operation cancelled.', 'it-l10n-better-wp-security' );

				add_settings_error( 'itsec', esc_attr( 'settings_updated' ), $message, $type );

			}

		} else {

			add_site_option( 'itsec_manual_update', true );

		}

		$this->settings = true; //this tells the form field that all went well.

		if ( is_multisite() ) {

			if ( isset( $type ) ) {

				$error_handler = new WP_Error();

				$error_handler->add( $type, $message );

				$this->core->show_network_admin_notice( $error_handler );

			} else {

				$this->core->show_network_admin_notice( false );

			}

			$this->settings = true;

		}

		if ( $this->settings === true ) {

			wp_clear_auth_cookie();
			$redirect_to = ! empty( $_REQUEST['redirect_to'] ) ? $_REQUEST['redirect_to'] : ITSEC_Lib::get_home_root() . 'wp-login.php?loggedout=true';
			wp_safe_redirect( $redirect_to );

		}

	}

}

