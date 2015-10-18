<?php

class ITSEC_Hide_Backend_Admin {

	private
		$settings,
		$core,
		$module_path;

	function run( $core ) {

		$this->core        = $core;
		$this->settings    = get_site_option( 'itsec_hide_backend' );
		$this->module_path = ITSEC_Lib::get_module_path( __FILE__ );

		add_filter( 'itsec_file_modules', array( $this, 'register_file' ) ); //register tooltip action
		add_filter( 'itsec_tooltip_modules', array( $this, 'register_tooltip' ) ); //register tooltip action
		add_action( 'itsec_add_admin_meta_boxes', array( $this, 'add_admin_meta_boxes' ) ); //add meta boxes to admin page
		add_action( 'itsec_admin_init', array( $this, 'initialize_admin' ) ); //initialize admin area
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_script' ) ); //enqueue scripts for admin page
		add_filter( 'itsec_add_dashboard_status', array( $this, 'dashboard_status' ) ); //add information for plugin status
		add_filter( 'itsec_tracking_vars', array( $this, 'tracking_vars' ) );

		//manually save options on multisite
		if ( is_multisite() ) {
			add_action( 'itsec_admin_init', array( $this, 'save_network_options' ) ); //save multisite options
		}


		add_filter( 'itsec_filter_apache_server_config_modification', array( $this, 'filter_apache_server_config_modification' ) );
		add_filter( 'itsec_filter_litespeed_server_config_modification', array( $this, 'filter_apache_server_config_modification' ) );
		add_filter( 'itsec_filter_nginx_server_config_modification', array( $this, 'filter_nginx_server_config_modification' ) );
	}

	/**
	 * Add meta boxes to primary options pages
	 *
	 * @return void
	 */
	public function add_admin_meta_boxes() {

		$id    = 'hide_backend_options';
		$title = __( 'Hide Login Area', 'better-wp-security' );

		add_meta_box(
			$id,
			$title,
			array( $this, 'metabox_hide_backend_settings' ),
			'security_page_toplevel_page_itsec_settings',
			'advanced',
			'core'
		);

		$this->core->add_toc_item(
			array(
				'id'    => $id,
				'title' => $title,
			)
		);
	}

	/**
	 * Add Away mode Javascript
	 *
	 * @return void
	 */
	public function admin_script() {

		global $itsec_globals;

		if ( isset( get_current_screen()->id ) && strpos( get_current_screen()->id, 'security_page_toplevel_page_itsec_settings' ) !== false ) {

			$new_slug = get_site_option( 'itsec_hide_backend_new_slug' );

			if ( $new_slug !== false ) {

				delete_site_option( 'itsec_hide_backend_new_slug' );

				$new_slug = get_site_url() . '/' . $new_slug;

				$slug_text = sprintf(
					'%s%s%s%s%s',
					__( 'Warning: Your admin URL has changed. Use the following URL to login to your site', 'better-wp-security' ),
					PHP_EOL . PHP_EOL,
					$new_slug,
					PHP_EOL . PHP_EOL,
					__( 'Please note this may be different than what you sent as the URL was sanitized to meet various requirements. A reminder has also been sent to the notification email(s) set in this plugins global settings.', 'better-wp-security' )
				);

				$this->send_new_slug( $new_slug );

			} else {
				$slug_text = false;
			}

			sprintf(
				'%s %s %s',
				__( 'Warning: Your admin URL has changed. Use the following URL to login to your site', 'better-wp-security' ),
				get_site_url() . '/' . $new_slug,
				__( 'Please note this may be different than what you sent as the URL was sanitized to meet various requirements.', 'better-wp-security' )
			);

			wp_enqueue_script( 'itsec_hide_backend_js', $this->module_path . 'js/admin-hide-backend.js', array( 'jquery' ), $itsec_globals['plugin_build'] );
			wp_localize_script(
				'itsec_hide_backend_js',
				'itsec_hide_backend',
				array(
					'new_slug' => $slug_text,
				)
			);

		}

	}

	public function filter_apache_server_config_modification( $modification ) {
		$input = get_site_option( 'itsec_hide_backend' );
		
		if ( true != $input['enabled'] ) {
			return $modification;
		}
		
		
		$home_root = ITSEC_Lib::get_home_root();
		
		$modification .= "\n";
		$modification .= "\t# " . __( 'Enable the hide backend feature - Security > Settings > Hide Login Area > Hide Backend', 'better-wp-security' ) . "\n";
		$modification .= "\tRewriteRule ^($home_root)?{$input['slug']}/?$ {$home_root}wp-login.php [QSA,L]\n";
		
		if ( 'wp-register.php' != $input['register'] ) {
			$modification .= "\tRewriteRule ^($home_root)?{$input['register']}/?$ /wplogin?action=register [QSA,L]\n";
		}
		
		return $modification;
	}
	
	public function filter_nginx_server_config_modification( $modification ) {
		$input = get_site_option( 'itsec_hide_backend' );
		
		if ( true != $input['enabled'] ) {
			return $modification;
		}
		
		
		$home_root = ITSEC_Lib::get_home_root();
		
		$modification .= "\n";
		$modification .= "\t# " . __( 'Enable the hide backend feature - Security > Settings > Hide Login Area > Hide Backend', 'better-wp-security' ) . "\n";
		$modification .= "\trewrite ^($home_root)?{$input['slug']}/?$ {$home_root}wp-login.php?\$query_string break;\n";
		
		if ( 'wp-register.php' != $input['register'] ) {
			$modification .= "\trewrite ^($home_root)?{$input['register']}/?$ {$home_root}{$input['slug']}?action=register break;\n";
		}
		
		return $modification;
	}

	/**
	 * Sets the status in the plugin dashboard
	 *
	 * @since 4.0
	 *
	 * @return array array of statuses
	 */
	public function dashboard_status( $statuses ) {

		if ( $this->settings['enabled'] === true ) {

			$status_array = 'safe-medium';
			$status       = array(
				'text' => __( 'Your WordPress Dashboard is hidden.', 'better-wp-security' ), 'link' => '#itsec_hide_backend_enabled',
			);

		} else {

			$status_array = 'medium';
			$status       = array(
				'text' => __( 'Your WordPress Dashboard is using the default addresses. This can make a brute force attack much easier.', 'better-wp-security' ),
				'link' => '#itsec_hide_backend_enabled',
			);

		}

		array_push( $statuses[$status_array], $status );

		return $statuses;

	}

	/**
	 * echos Hide Backend  Enabled Field
	 *
	 * @since 4.0
	 *
	 * @return void
	 */
	public function hide_backend_enabled() {

		if ( ( get_option( 'permalink_structure' ) == '' || get_option( 'permalink_structure' ) == false ) && ! is_multisite() ) {

			$adminurl = is_multisite() ? admin_url() . 'network/' : admin_url();

			$content = sprintf( '<p class="noPermalinks">%s <a href="%soptions-permalink.php">%s</a> %s</p>', __( 'You must turn on', 'better-wp-security' ), $adminurl, __( 'WordPress permalinks', 'better-wp-security' ), __( 'to use this feature.', 'better-wp-security' ) );

		} else {

			if ( isset( $this->settings['enabled'] ) && $this->settings['enabled'] === true ) {
				$enabled = 1;
			} else {
				$enabled = 0;
			}

			$content = '<input type="checkbox" id="itsec_hide_backend_enabled" name="itsec_hide_backend[enabled]" value="1" ' . checked( 1, $enabled, false ) . '/>';
			$content .= '<label for="itsec_hide_backend_enabled"> ' . __( 'Enable the hide backend feature.', 'better-wp-security' ) . '</label>';

		}

		echo $content;

	}

	/**
	 * echos Hide Backend Slug  Field
	 *
	 * @since 4.0
	 *
	 * @return void
	 */
	public function hide_backend_slug() {

		if ( ( get_option( 'permalink_structure' ) == '' || get_option( 'permalink_structure' ) == false ) && ! is_multisite() ) {

			$content = '';

		} else {

			$content = '<input name="itsec_hide_backend[slug]" id="itsec_hide_backend_strong_passwords_slug" value="' . sanitize_title( $this->settings['slug'] ) . '" type="text"><br />';
			$content .= '<label for="itsec_hide_backend_strong_passwords_slug">' . __( 'Login URL:', 'better-wp-security' ) . trailingslashit( get_option( 'siteurl' ) ) . '<span style="color: #4AA02C">' . sanitize_title( $this->settings['slug'] ) . '</span></label>';
			$content .= '<p class="description">' . __( 'The login url slug cannot be "login," "admin," "dashboard," or "wp-login.php" as these are use by default in WordPress.', 'better-wp-security' ) . '</p>';
			$content .= '<p class="description"><em>' . __( 'Note: The output is limited to alphanumeric characters, underscore (_) and dash (-). Special characters such as "." and "/" are not allowed and will be converted in the same manner as a post title. Please review your selection before logging out.', 'better-wp-security' ) . '</em></p>';

		}

		echo $content;

	}

	/**
	 * echos Hide Backend Slug  Field
	 *
	 * @since 4.0.6
	 *
	 * @return void
	 */
	public function hide_backend_theme_compat_slug() {

		if ( ( get_option( 'permalink_structure' ) == '' || get_option( 'permalink_structure' ) == false ) && ! is_multisite() ) {

			$content = '';

		} else {

			$slug = sanitize_title( isset( $this->settings['theme_compat_slug'] ) ? $this->settings['theme_compat_slug'] : 'not_found' );

			$content = '<input name="itsec_hide_backend[theme_compat_slug]" id="itsec_hide_backend_strong_passwords_theme_compat_slug" value="' . $slug . '" type="text"><br />';
			$content .= '<label for="itsec_hide_backend_strong_passwords_theme_compat_slug">' . __( '404 Slug:', 'better-wp-security' ) . trailingslashit( get_option( 'siteurl' ) ) . '<span style="color: #4AA02C">' . $slug . '</span></label>';
			$content .= '<p class="description">' . __( 'The slug to redirect folks to when theme compatibility mode is enabled (just make sure it does not exist in your site).', 'better-wp-security' ) . '</p>';

		}

		echo $content;

	}

	/**
	 * echos Hide Backend Slug  Field
	 *
	 * @since 4.0.6
	 *
	 * @return void
	 */
	public function hide_backend_post_logout_slug() {

		if ( ( get_option( 'permalink_structure' ) == '' || get_option( 'permalink_structure' ) == false ) && ! is_multisite() ) {

			$content = '';

		} else {

			$slug = sanitize_title( isset( $this->settings['post_logout_slug'] ) ? $this->settings['post_logout_slug'] : '' );

			$content = '<input name="itsec_hide_backend[post_logout_slug]" id="itsec_hide_backend_strong_passwords_post_logout_slug" value="' . $slug . '" type="text"><br />';
			$content .= '<label for="itsec_hide_backend_strong_passwords_post_logout_slug">' . __( 'Custom Action:', 'better-wp-security' ) . '</label>';
			$content .= '<p class="description">' . __( 'WordPress uses the "action" variable to handle many login and logout functions. By default this plugin can handle the normal ones but some plugins and themes may utilize a custom action (such as logging out of a private post). If you need a custom action please enter it here.', 'better-wp-security' ) . '</p>';

		}

		echo $content;

	}

	/**
	 * echos Hide Backend  theme compatibility Field
	 *
	 * @since 4.0.6
	 *
	 * @return void
	 */
	public function hide_backend_theme_compat() {

		if ( ( get_option( 'permalink_structure' ) == '' || get_option( 'permalink_structure' ) == false ) && ! is_multisite() ) {

			$adminurl = is_multisite() ? admin_url() . 'network/' : admin_url();

			$content = sprintf( '<p class="noPermalinks">%s <a href="%soptions-permalink.php">%s</a> %s</p>', __( 'You must turn on', 'better-wp-security' ), $adminurl, __( 'WordPress permalinks', 'better-wp-security' ), __( 'to use this feature.', 'better-wp-security' ) );

		} else {

			if ( isset( $this->settings['theme_compat'] ) && $this->settings['theme_compat'] === true ) {
				$enabled = 1;
			} else {
				$enabled = 0;
			}

			$content = '<input type="checkbox" id="itsec_hide_backend_theme_compat" name="itsec_hide_backend[theme_compat]" value="1" ' . checked( 1, $enabled, false ) . '/>';
			$content .= '<label for="itsec_hide_backend_theme_compat"> ' . __( 'Enable theme compatibility. If  you see errors in your theme when using hide backend, in particular when going to wp-admin while not logged in, turn this on to fix them.', 'better-wp-security' ) . '</label>';

		}

		echo $content;

	}

	/**
	 * echos Register Slug  Field
	 *
	 * @since 4.0
	 *
	 * @return void
	 */
	public function hide_backend_register() {

		if ( ( get_option( 'permalink_structure' ) == '' || get_option( 'permalink_structure' ) == false ) && ! is_multisite() ) {

			$content = '';

		} else {

			$content = '<input name="itsec_hide_backend[register]" id="itsec_hide_backend_strong_passwords_register" value="' . ( $this->settings['register'] !== 'wp-register.php' ? sanitize_title( $this->settings['register'] ) : 'wp-register.php' ) . '" type="text"><br />';
			$content .= '<label for="itsec_hide_backend_strong_passwords_register">' . __( 'Registration URL:', 'better-wp-security' ) . trailingslashit( get_option( 'siteurl' ) ) . '<span style="color: #4AA02C">' . sanitize_title( $this->settings['register'] ) . '</span></label>';

		}

		echo $content;

	}

	/**
	 * Execute admin initializations
	 *
	 * @return void
	 */
	public function initialize_admin() {

		//Add Settings sections
		add_settings_section(
			'hide_backend-enabled',
			__( 'Hide Login and Admin', 'better-wp-security' ),
			'__return_empty_string',
			'security_page_toplevel_page_itsec_settings'
		);

		add_settings_section(
			'hide_backend-settings',
			__( 'Hide Login and Admin', 'better-wp-security' ),
			'__return_empty_string',
			'security_page_toplevel_page_itsec_settings'
		);

		//Hide Backend Fields
		add_settings_field(
			'itsec_hide_backend[enabled]',
			__( 'Hide Backend', 'better-wp-security' ),
			array( $this, 'hide_backend_enabled' ),
			'security_page_toplevel_page_itsec_settings',
			'hide_backend-enabled'
		);

		add_settings_field(
			'itsec_hide_backend[slug]',
			__( 'Login Slug', 'better-wp-security' ),
			array( $this, 'hide_backend_slug' ),
			'security_page_toplevel_page_itsec_settings',
			'hide_backend-settings'
		);

		if ( get_site_option( 'users_can_register' ) ) {

			add_settings_field(
				'itsec_hide_backend[register]',
				__( 'Register Slug', 'better-wp-security' ),
				array( $this, 'hide_backend_register' ),
				'security_page_toplevel_page_itsec_settings',
				'hide_backend-settings'
			);

		}

		add_settings_field(
			'itsec_hide_backend[theme_compat]',
			__( 'Enable Theme Compatibility', 'better-wp-security' ),
			array( $this, 'hide_backend_theme_compat' ),
			'security_page_toplevel_page_itsec_settings',
			'hide_backend-settings'
		);

		add_settings_field(
			'itsec_hide_backend[theme_compat_slug]',
			__( 'Theme Compatibility Slug', 'better-wp-security' ),
			array( $this, 'hide_backend_theme_compat_slug' ),
			'security_page_toplevel_page_itsec_settings',
			'hide_backend-settings'
		);

		add_settings_field(
			'itsec_hide_backend[post_logout_slug]',
			__( 'Custom Login Action', 'better-wp-security' ),
			array( $this, 'hide_backend_post_logout_slug' ),
			'security_page_toplevel_page_itsec_settings',
			'hide_backend-settings'
		);

		//Register the settings field for the entire module
		register_setting(
			'security_page_toplevel_page_itsec_settings',
			'itsec_hide_backend',
			array( $this, 'sanitize_module_input' )
		);

	}

	/**
	 * Render the settings metabox
	 *
	 * @since 4.0
	 *
	 * @return void
	 */
	public function metabox_hide_backend_settings() {

		echo '<p>' . __( 'Hides the login page (wp-login.php, wp-admin, admin and login) making it harder to find by automated attacks and making it easier for users unfamiliar with the WordPress platform.', 'better-wp-security' ) . '</p>';

		$this->core->do_settings_section( 'security_page_toplevel_page_itsec_settings', 'hide_backend-enabled', false );
		$this->core->do_settings_section( 'security_page_toplevel_page_itsec_settings', 'hide_backend-settings', false );

		echo '<p>' . PHP_EOL;

		settings_fields( 'security_page_toplevel_page_itsec_settings' );

		echo '<input class="button-primary" name="submit" type="submit" value="' . __( 'Save All Changes', 'better-wp-security' ) . '" />' . PHP_EOL;

		echo '</p>' . PHP_EOL;

	}

	/**
	 * Register ban users for file writer
	 *
	 * @param  array $file_modules array of file writer modules
	 *
	 * @return array                   array of file writer modules
	 */
	public function register_file( $file_modules ) {

		$file_modules['hide-backend'] = array(
			'rewrite' => array( $this, 'save_rewrite_rules' ),
		);

		return $file_modules;

	}

	/**
	 * Register backups for tooltips
	 *
	 * @param  array $tooltip_modules array of tooltip modules
	 *
	 * @return array                   array of tooltip modules
	 */
	public function register_tooltip( $tooltip_modules ) {

		if ( get_site_transient( 'ITSEC_SHOW_HIDE_BACKEND_TOOLTIP' ) || ( isset( $this->settings['show-tooltip'] ) && $this->settings['show-tooltip'] === true ) ) {

			$tooltip_modules['hide-backend'] = array(
				'priority'  => 0,
				'class'     => 'itsec_tooltip_hide_backend',
				'heading'   => __( 'Review Hide Backend Settings', 'better-wp-security' ),
				'text'      => __( 'The hide backend system has been rewritten. You must re-activate the feature to continue using the feature.', 'better-wp-security' ),
				'link_text' => __( 'Review Settings', 'better-wp-security' ),
				'link'      => '?page=toplevel_page_itsec_settings#itsec_hide_backend_enabled',
				'success'   => '',
				'failure'   => '',
			);

		}

		return $tooltip_modules;

	}

	/**
	 * Sanitize and validate input
	 *
	 * @param  Array $input array of input fields
	 *
	 * @return Array         Sanitized array
	 */
	public function sanitize_module_input( $input ) {

		global $itsec_globals;

		//Process hide backend settings
		$input['enabled']      = ( isset( $input['enabled'] ) && intval( $input['enabled'] == 1 ) ? true : false );
		$input['theme_compat'] = ( isset( $input['theme_compat'] ) && intval( $input['theme_compat'] == 1 ) ? true : false );
		$input['show-tooltip'] = ( isset( $this->settings['show-tooltip'] ) ? $this->settings['show-tooltip'] : false );

		if ( isset( $input['slug'] ) ) {

			$input['slug'] = sanitize_title( $input['slug'] );

		} else {

			$input['slug'] = 'wplogin';

		}

		if ( isset( $input['post_logout_slug'] ) ) {

			$input['post_logout_slug'] = sanitize_title( $input['post_logout_slug'] );

		} else {

			$input['post_logout_slug'] = '';

		}

		if ( $input['slug'] != $this->settings['slug'] && $input['enabled'] === true ) {
			add_site_option( 'itsec_hide_backend_new_slug', $input['slug'] );
		}

		if ( isset( $input['register'] ) && $input['register'] !== 'wp-register.php' ) {
			$input['register'] = sanitize_title( $input['register'] );
		} else {
			$input['register'] = 'wp-register.php';
		}

		if ( isset( $input['theme_compat_slug'] ) ) {
			$input['theme_compat_slug'] = sanitize_title( $input['theme_compat_slug'] );
		} else {
			$input['theme_compat_slug'] = 'not_found';
		}

		$forbidden_slugs = array( 'admin', 'login', 'wp-login.php', 'dashboard', 'wp-admin', '' );

		if ( in_array( trim( $input['slug'] ), $forbidden_slugs ) && $input['enabled'] === true ) {

			$invalid_login_slug = true;

			$type    = 'error';
			$message = __( 'Invalid hide login slug used. The login url slug cannot be \"login,\" \"admin,\" \"dashboard,\" or \"wp-login.php\" or \"\" (blank) as these are use by default in WordPress.', 'better-wp-security' );

			add_settings_error( 'itsec', esc_attr( 'settings_updated' ), $message, $type );

		} else {

			$invalid_login_slug = false;

		}

		if ( $invalid_login_slug === false ) {

			if (
				! isset( $type ) &&
				(
					$input['slug'] !== $this->settings['slug'] ||
					$input['register'] !== $this->settings['register'] ||
					$input['enabled'] !== $this->settings['enabled']
				) ||
				isset( $itsec_globals['settings']['write_files'] ) && $itsec_globals['settings']['write_files'] === true
			) {

				add_site_option( 'itsec_rewrites_changed', true );

			}

		}

		if ( is_multisite() ) {

			if ( isset( $type ) ) {

				$error_handler = new WP_Error();

				$error_handler->add( $type, $message );

				$this->core->show_network_admin_notice( $error_handler );

			} else {

				$this->core->show_network_admin_notice( false );

			}

			$this->settings = $input;

		}

		return $input;

	}

	/**
	 * Prepare and save options in network settings
	 *
	 * @return void
	 */
	public function save_network_options() {

		if ( isset( $_POST['itsec_hide_backend'] ) ) {

			if ( ! wp_verify_nonce( $_POST['_wpnonce'], 'security_page_toplevel_page_itsec_settings-options' ) ) {
				die( __( 'Security error!', 'better-wp-security' ) );
			}

			update_site_option( 'itsec_hide_backend', $_POST['itsec_hide_backend'] ); //we must manually save network options

		}

	}

	/**
	 * Sends an email to notify site admins of the new login url
	 *
	 * @param  string $new_slug the new login url
	 *
	 * @return void
	 */
	private function send_new_slug( $new_slug ) {

		global $itsec_globals;

		//Put the copy all together
		$body = sprintf(
			'<p>%s,</p><p>%s <a href="%s">%s</a>. %s <a href="%s">%s</a> %s.</p>',
			__( 'Dear Site Admin', 'better-wp-security' ),
			__( 'This friendly email is just a reminder that you have changed the dashboard login address on', 'better-wp-security' ),
			get_site_url(),
			get_site_url(),
			__( 'You must now use', 'better-wp-security' ),
			$new_slug,
			$new_slug,
			__( 'to login to your WordPress website', 'better-wp-security' )
		);

		//Setup the remainder of the email
		$recipients = $itsec_globals['settings']['notification_email'];
		$subject    = '[' . get_option( 'siteurl' ) . '] ' . __( 'WordPress Login Address Changed', 'better-wp-security' );
		$subject    = apply_filters( 'itsec_lockout_email_subject', $subject );
		$headers    = 'From: ' . get_bloginfo( 'name' ) . ' <' . get_option( 'admin_email' ) . '>' . "\r\n";

		//Use HTML Content type
		add_filter( 'wp_mail_content_type', array( $this, 'set_html_content_type' ) );

		//Send emails to all recipients
		foreach ( $recipients as $recipient ) {

			if ( is_email( trim( $recipient ) ) ) {

				if ( defined( 'ITSEC_DEBUG' ) && ITSEC_DEBUG === true ) {
					$body .= '<p>' . __( 'Debug info (source page): ' . esc_url( $_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"] ) ) . '</p>';
				}

				wp_mail( trim( $recipient ), $subject, '<html>' . $body . '</html>', $headers );

			}

		}

		//Remove HTML Content type
		remove_filter( 'wp_mail_content_type', array( $this, 'set_html_content_type' ) );

	}

	/**
	 * Set HTML content type for email
	 *
	 * @return string html content type
	 */
	public function set_html_content_type() {

		return 'text/html';

	}

	/**
	 * Adds fields that will be tracked for Google Analytics
	 *
	 * @since 4.0
	 *
	 * @param array $vars tracking vars
	 *
	 * @return array tracking vars
	 */
	public function tracking_vars( $vars ) {

		$vars['itsec_hide_backend'] = array(
			'enabled' => '0:b',
		);

		return $vars;

	}

}
